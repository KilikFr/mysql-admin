<?php

namespace App\Services;

use App\Entity\Server;
use PDO;

class ConnectionService
{
    /**
     * @var PDO[]|array
     */
    private array $connectionPool = [];

    /**
     * Get a Server PDO connection.
     *
     * @throws \Exception
     */
    public function getServerConnection(Server $server): PDO
    {
        try {
            if (!isset($this->connectionPool[$server->getId()])) {
                $pdo = new \PDO(
                    sprintf('mysql:host=%s;port=%d', $server->getHost(), $server->getPort()),
                    $server->getUser(),
                    $server->getPassword()
                );
                $this->connectionPool[$server->getId()] = $pdo;
            }

            return $this->connectionPool[$server->getId()];
        } catch (\PDOException $e) {
            throw new \Exception(sprintf('connection error to server %s: %s', $server->getName(), $e->getMessage()), $e->getCode());
        }
    }
}
