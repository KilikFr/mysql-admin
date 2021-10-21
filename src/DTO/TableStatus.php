<?php

namespace App\DTO;

class TableStatus
{
    private ?string $database = null;
    private ?string $table = null;
    private ?string $lastChecksum = null;
    private ?string $previousChecksum = null;

    public function getDatabase(): ?string
    {
        return $this->database;
    }

    public function setDatabase(?string $database): self
    {
        $this->database = $database;

        return $this;
    }

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function setTable(?string $table): self
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
}
