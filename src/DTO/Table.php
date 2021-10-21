<?php

namespace App\DTO;

use App\Entity\Server;

class Table
{
    private ?string $name;
    private ?string $database;
    private ?Server $server;

    /** @var Field[]|array */
    private $fields = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDatabase(): ?string
    {
        return $this->database;
    }

    public function setDatabase(?string $database): self
    {
        $this->database = $database;

        return $this;
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function setServer(?Server $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function addField(Field $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getPrimary(): array
    {
        $fields = [];
        foreach ($this->fields as $field) {
            if ('PRI' === $field->getKey()) {
                $fields[] = $field;
            }
        }

        // not found
        return $fields;
    }

    public function hasType(string $type): bool
    {
        foreach ($this->fields as $field) {
            if ($field->getType() === $type) {
                return true;
            }
        }

        return false;
    }
}
