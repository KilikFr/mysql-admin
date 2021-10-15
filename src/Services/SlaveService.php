<?php

namespace App\Services;

use App\DTO\SlaveStatus;
use App\Entity\Server;

class SlaveService
{
    private ConnectionService $connectionService;

    public function __construct(ConnectionService $connectionService)
    {
        $this->connectionService = $connectionService;
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
}