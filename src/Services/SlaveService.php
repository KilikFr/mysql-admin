<?php

namespace App\Services;

use App\DTO\SlaveStatus;
use App\Entity\Server;
use App\Entity\Slave;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class SlaveService
{
    private ConnectionService $connectionService;
    private EntityManager $entityManager;
    private LogService $logService;

    public function __construct(
        ConnectionService $connectionService,
        EntityManagerInterface $entityManager,
        LogService $logService
    ) {
        $this->connectionService = $connectionService;
        $this->entityManager = $entityManager;
        $this->logService = $logService;
    }

    /**
     * @return SlaveStatus[]|array
     *
     * @throws \Exception
     */
    public function showSlaveStatus(Server $server): array
    {
        $slaves = [];

        $connection = $this->connectionService->getServerConnection($server);

        $stmt = $connection->query('SHOW SLAVE STATUS', \PDO::FETCH_ASSOC);
        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }
        while ($row = $stmt->fetch()) {
            $slaves[] = SlaveStatus::createFromRow($row);
        }

        return $slaves;
    }

    /**
     * @return SlaveStatus[]|array
     *
     * @throws \Exception
     */
    public function showSlaveStatusFromCache(Server $server): array
    {
        // @todo get value from cache
        return $this->showSlaveStatus($server);
    }

    /**
     * Scan slaves, and add news to slave table.
     *
     * @return SlaveStatus[]|array
     */
    public function scanSlaves(Server $server, bool $flush = false): array
    {
        $slaveStatuses = $this->showSlaveStatus($server);

        foreach ($slaveStatuses as $slaveStatus) {
            $slave = $this->entityManager->getRepository(Slave::class)->findOneBy(
                ['server' => $server, 'channelName' => $slaveStatus->getChannelName()]
            );

            // new channel ?
            if (null === $slave) {
                $slave = new Slave();
                $slave->setServer($server);
                $slave->setChannelName($slaveStatus->getChannelName());
                $slave->setDescription(
                    sprintf('auto generated from scan on %s', (new \DateTime())->format('d/m/Y H:i:s'))
                );
                $slave->setCreatedAt(new \DateTime());
                $this->logService->addSlaveLog(
                    $slave,
                    'create',
                    'channel_name '.$slave->getChannelName(),
                    LogService::LOG_INFO,
                    false
                );
            }

            $this->autolink($slave, $slaveStatus);

            $this->save($slave);
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        return $slaveStatuses;
    }

    public function autolink(Slave $slave, SlaveStatus $slaveStatus)
    {
        // check master (autolink)
        if ($slaveStatus->getMasterUuid()) {
            $master = $this->entityManager->getRepository(Server::class)->findOneByUuid($slaveStatus->getMasterUuid());

            if (null !== $master) {
                if (null === $slave->getMaster() || $slave->getMaster()->getId() !== $master->getId()) {
                    $slave->setMaster($master);
                    $this->logService->addSlaveLog(
                        $slave,
                        'master_link',
                        sprintf('link to master %s (%d)', $master->getName(), $master->getId()),
                        LogService::LOG_INFO,
                        false
                    );
                }
            }
        }
    }

    public function startSlave(Slave $slave)
    {
        $connection = $this->connectionService->getServerConnection($slave->getServer());

        $stmt = $connection->query(
            sprintf("START SLAVE FOR CHANNEL '%s'", $slave->getChannelName()),
            \PDO::FETCH_ASSOC
        );

        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }
    }

    public function stopSlave(Slave $slave)
    {
        $connection = $this->connectionService->getServerConnection($slave->getServer());

        $stmt = $connection->query(
            sprintf("STOP SLAVE FOR CHANNEL '%s'", $slave->getChannelName()),
            \PDO::FETCH_ASSOC
        );

        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }
    }

    public function save(Slave $slave, bool $flush = false)
    {
        $slave->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($slave);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function switchToNextMasterLogFile($slave, int $channel, string $nextMasterLogFile)
    {
        $connection = $this->connectionService->getServerConnection($slave->getServer());

        $stmt = $connection->exec(
            sprintf("STOP SLAVE FOR CHANNEL '%s'; CHANGE MASTER TO MASTER_LOG_FILE='%s', MASTER_LOG_POS=0 FOR CHANNEL '%s'; START SLAVE FOR CHANNEL '%s';",
                $channel,
                $nextMasterLogFile,
                $channel,
                $channel
            )
        );

        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }
    }
}
