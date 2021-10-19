<?php

namespace App\Services;

use App\DTO\MasterStatus;
use App\DTO\SlaveStatus;
use App\Entity\Log;
use App\Entity\Server;
use App\Entity\Slave;
use App\Repository\SlaveRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class LogService
{
    const LOG_INFO = 0;
    const LOG_WARNING = 1;
    const LOG_ERROR = 2;

    private EntityManager $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Log $log, ?string $code, ?string $message, ?int $level = self::LOG_INFO, ?bool $flush = true)
    {
        $log->setDatetime(new \DateTime());
        $log->setCode($code);
        $log->setMessage($message);
        $log->setLevel($level);

        $this->entityManager->persist($log);

        if ($flush) {
            $this->entityManager->flush($log);
        }
    }

    public function addServerLog(
        Server $server,
        ?string $code,
        ?string $message,
        ?int $level = self::LOG_INFO,
        ?bool $flush = true
    ) {
        $log = new Log();
        $log->setServer($server);
        $this->add($log, $code, $message, $level, $flush);
    }

    public function addSlaveLog(
        Slave $slave,
        ?string $code,
        ?string $message,
        ?int $level = self::LOG_INFO,
        ?bool $flush = true
    ) {
        $log = new Log();
        $log->setServer($slave->getServer());
        $log->setSlave($slave);
        $this->add($log, $code, $message, $level, $flush);
    }

}