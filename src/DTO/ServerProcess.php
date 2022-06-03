<?php

namespace App\DTO;

class ServerProcess
{
    public const KILL_OPTION_CONNECTION = 'CONNECTION';
    public const KILL_OPTION_QUERY = 'QUERY';

    public const KILL_OPTIONS = [
        self::KILL_OPTION_CONNECTION,
        self::KILL_OPTION_QUERY,
    ];

    private ?int $id;
    private ?string $user;
    private ?string $host;
    private ?string $db;
    private ?string $command;
    private ?int $time;
    private ?string $state;
    private ?string $info;

    /**
     * @param   int|null  $id
     *
     * @return ServerProcess
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param   string|null  $user
     *
     * @return ServerProcess
     */
    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @param   string|null  $host
     *
     * @return ServerProcess
     */
    public function setHost(?string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param   string|null  $db
     *
     * @return ServerProcess
     */
    public function setDb(?string $db): self
    {
        $this->db = $db;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDb(): ?string
    {
        return $this->db;
    }

    /**
     * @param   string|null  $command
     *
     * @return ServerProcess
     */
    public function setCommand(?string $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * @param   int|null  $time
     *
     * @return ServerProcess
     */
    public function setTime(?int $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->time;
    }

    /**
     * @param   string|null  $state
     *
     * @return ServerProcess
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param   string|null  $info
     *
     * @return ServerProcess
     */
    public function setInfo(?string $info): self
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setFromRow(array $row): void
    {
        $fields = [
            'Id' => 'setId',
            'User' => 'setUser',
            'Host' => 'setHost',
            'db' => 'setDb',
            'Command' => 'setCommand',
            'Time' => 'setTime',
            'State' => 'setState',
            'Info' => 'setInfo',
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
        $process = new self();
        $process->setFromRow($row);

        return $process;
    }
}
