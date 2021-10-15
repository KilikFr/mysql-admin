<?php

namespace App\Entity;

use App\Repository\SlaveRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=SlaveRepository::class)
 */
class Slave
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * Master for this slave (where the slave is connecting to)
     *
     * @ORM\ManyToOne(targetEntity=Server::class, inversedBy="slaves")
     */
    private Server $master;

    /**
     * Server for this slave (where the slave run)
     *
     * @ORM\ManyToOne(targetEntity=Server::class, inversedBy="channels")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Server $server;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $channelNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaster(): ?Server
    {
        return $this->master;
    }

    public function setMaster(?Server $master): self
    {
        $this->master = $master;

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

    public function getChannelNumber(): ?int
    {
        return $this->channelNumber;
    }

    public function setChannelNumber(?int $channelNumber): self
    {
        $this->channelNumber = $channelNumber;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
