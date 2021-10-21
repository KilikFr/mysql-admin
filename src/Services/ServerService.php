<?php

namespace App\Services;

use App\DTO\Field;
use App\DTO\MasterStatus;
use App\DTO\Table;
use App\Entity\Server;
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
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }

        while ($row = $stmt->fetch()) {
            $variables[$row['Variable_name']] = $row['Value'];
        }

        return $variables;
    }

    /**
     * Scan master status.
     */
    public function scan(Server $server): void
    {
        $variables = $this->showVariables($server);
        $masterStatus = $this->showMasterStatus($server);
        $slaveStatuses = $this->slaveService->scanSlaves($server);

        if (!empty($variables['server_uuid']) && $variables['server_uuid'] != $server->getUuid()) {
            $server->setUuid($variables['server_uuid']);
            $this->logService->addServerLog($server, 'uuid_changed', $server->getUuid());
        }

        if (!empty($variables['server_id']) && $variables['server_id'] != $server->getServerId()) {
            $server->setServerId($variables['server_id']);
            $this->logService->addServerLog($server, 'server_id_changed', $server->getServerId());
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

    /**
     * List server's databases.
     *
     * @throws \Exception
     */
    public function showDatabases(Server $server): array
    {
        $databases = [];

        $connection = $this->connectionService->getServerConnection($server);

        $stmt = $connection->query('SHOW DATABASES', \PDO::FETCH_ASSOC);
        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }

        while ($row = $stmt->fetch()) {
            $databases[] = $row['Database'];
        }

        return $databases;
    }

    /**
     * List server database tables.
     *
     * @throws \Exception
     */
    public function showTables(Server $server, string $database): array
    {
        $tables = [];

        $connection = $this->connectionService->getServerConnection($server);

        $stmt = $connection->query(sprintf('SHOW TABLES FROM `%s`', $database), \PDO::FETCH_NUM);
        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }

        while ($row = $stmt->fetch()) {
            $tables[] = $row[0];
        }

        return $tables;
    }

    public function tableChecksum(Server $server, string $database, string $table): string
    {
        $connection = $this->connectionService->getServerConnection($server);

        $stmt = $connection->query(sprintf('CHECKSUM TABLE `%s`.`%s`', $database, $table), \PDO::FETCH_ASSOC);
        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }

        $row = $stmt->fetch();

        return $row['Checksum'];
    }

    public function describeTable(Server $server, string $database, string $tableName): Table
    {
        $table = new Table();
        $table->setName($tableName);
        $table->setDatabase($database);
        $table->setServer($server);

        $connection = $this->connectionService->getServerConnection($server);

        $stmt = $connection->query(sprintf('DESCRIBE `%s`.`%s`', $database, $tableName), \PDO::FETCH_ASSOC);
        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }

        while ($row = $stmt->fetch()) {
            $field = new Field();
            $field->setField($row['Field']);
            $field->setType($row['Type']);
            $field->setNullable('yes' === strtolower($row['Null']));
            $field->setKey($row['Key']);
            $field->setDefault($row['Default']);
            $field->setExtra($row['Extra']);
            $table->addField($field);
        }

        return $table;
    }
}
