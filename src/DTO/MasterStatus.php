<?php

namespace App\DTO;

use Gedmo\Timestampable\Traits\Timestampable;

class MasterStatus
{
    use Timestampable;

    const STATUS_UNKNOWN = null;
    const STATUS_ERROR = 0;
    const STATUS_OK = 1;

    const STATUSES = [
        self::STATUS_UNKNOWN,
        self::STATUS_ERROR,
        self::STATUS_OK,
    ];

    private ?int $status = self::STATUS_UNKNOWN;

    private ?string $file;
    private ?int $position;
    private ?string $binlogDoDb;
    private ?string $binlogIgnoreDb;
    private ?string $executedGtidSet;

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getBinlogDoDb(): ?string
    {
        return $this->binlogDoDb;
    }

    public function setBinlogDoDb(?string $binlogDoDb): self
    {
        $this->binlogDoDb = $binlogDoDb;

        return $this;
    }

    public function getBinlogIgnoreDb(): ?string
    {
        return $this->binlogIgnoreDb;
    }

    public function setBinlogIgnoreDb(?string $binlogIgnoreDb): self
    {
        $this->binlogIgnoreDb = $binlogIgnoreDb;

        return $this;
    }

    public function getExecutedGtidSet(): ?string
    {
        return $this->executedGtidSet;
    }

    public function setExecutedGtidSet(?string $executedGtidSet): self
    {
        $this->executedGtidSet = $executedGtidSet;

        return $this;
    }

    public function setFromRow(array $row)
    {
        $fields = [
            'File' => 'setFile',
            'Position' => 'setPosition',
            'Binlog_Do_DB' => 'setBinlogDoDb',
            'Binlog_Ignore_DB' => 'setBinlogIgnoreDb',
            'Executed_Gtid_Set' => 'setExecutedGtidSet',
        ];

        foreach ($row as $key => $value) {
            if (isset($fields[$key])) {
                $setter = $fields[$key];
                $this->$setter($value);
            }
        }
    }

    public static function createFromRow(array $row): self
    {
        $status = new self();
        $status->setFromRow($row);

        return $status;
    }

}