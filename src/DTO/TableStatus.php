<?php

namespace App\DTO;

class TableStatus
{
    private ?Table $table = null;
    private ?string $lastChecksum = null;
    private ?string $previousChecksum = null;

    public function getTable(): ?Table
    {
        return $this->table;
    }

    public function setTable(?Table $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function getLastChecksum(): ?string
    {
        return $this->lastChecksum;
    }

    public function setLastChecksum(?string $lastChecksum): self
    {
        $this->setPreviousChecksum($this->lastChecksum);
        $this->lastChecksum = $lastChecksum;

        return $this;
    }

    public function getPreviousChecksum(): ?string
    {
        return $this->previousChecksum;
    }

    public function setPreviousChecksum(?string $previousChecksum): self
    {
        $this->previousChecksum = $previousChecksum;

        return $this;
    }

    public function __clone()
    {
        $this->table = clone $this->table;
    }
}
