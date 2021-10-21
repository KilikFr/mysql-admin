<?php

namespace App\DTO;

use Gedmo\Timestampable\Traits\Timestampable;

class ServerStatus
{
    use Timestampable;

    public const STATUS_UNKNOWN = null;
    public const STATUS_ERROR = 0;
    public const STATUS_OK = 1;

    public const STATUSES = [
        self::STATUS_UNKNOWN,
        self::STATUS_ERROR,
        self::STATUS_OK,
    ];

    private ?int $status = self::STATUS_UNKNOWN;

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
