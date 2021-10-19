<?php

namespace App\Services;

use App\DTO\MasterStatus;
use App\DTO\SlaveStatus;
use App\Entity\Server;
use App\Entity\Slave;
use App\Repository\SlaveRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class ServerService
{
    private ConnectionService $connectionService;
    private EntityManager $entityManager;
    private SlaveService $slaveService;
    private LogService $logService;

    public function __construct(
        ConnectionService $connectionService,
        EntityManagerInterface $entityManager,
        SlaveService $slaveService,
        LogService $logService
    ) {
        $this->connectionService = $connectionService;
        $this->entityManager = $entityManager;
        $this->slaveService = $slaveService;
        $this->logService = $logService;
    }

    /**
     * @throws \Exception
     */
    public function showMasterStatus(Server $server): MasterStatus
    {
        $connection = $this->connectionService->getServerConnection($server);

        $stmt = $connection->query('SHOW MASTER STATUS', \PDO::FETCH_ASSOC);
        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }
        $row = $stmt->fetch();

        return MasterStatus::createFromRow($row);
    }

    /**
     * @throws \Exception
     */
    public function showMasterStatusFromCache(Server $server): MasterStatus
    {
        // @todo
        return $this->showMasterStatus($server);
    }

    public function showVariables(Server $server): array
    {
        $variables = [];

        $connection = $this->connectionService->getServerConnection($server);

        $stmt = $connection->query('SHOW VARIABLES', \PDO::FETCH_ASSOC);
        if (false === $stmt) {
            $message = sprintf  ('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }

        while ($row = $stmt->fetch()) {
            $variables[$row['Variable_name']] = $row['Value'];
        }

        return $variables;
    }

    /**
     * Scan master status
     */
    public function scan(Server $server): void
    {
        $variables = $this->showVariables($server);
        $masterStatus = $this->showMasterStatus($server);
        $slaveStatuses = $this->slaveService->scanSlaves($server);

        if (!empty($variables['server_uuid']) && $variables['server_uuid'] != $server->getUuid()) {
            $server->setUuid($variables['server_uuid']);
            $this->logService->addServerLog($server,'uuid_changed', $server->getUuid());
        }

        if (!empty($variables['server_id']) && $variables['server_id'] != $server->getServerId()) {
            $server->setServerId($variables['server_id']);
            $this->logService->addServerLog($server,'server_id_changed', $server->getServerId());
        }

        $this->save($server, true);
    }

    public function save(Server $server, bool $flush = false)
    {
        $server->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($server);

        if ($flush) {
            $this->entityManager->flush();
        }

    }
}