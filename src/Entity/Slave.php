<?php

namespace App\Entity;

use App\Repository\SlaveRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=SlaveRepository::class)
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"server_id", "channel_name"}),
 * })
 */
class Slave
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * Master for this slave (where the slave is connecting to).
     *
     * @ORM\ManyToOne(targetEntity=Server::class, inversedBy="slaves")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?Server $master = null;

    /**
     * Server for this slave (where the slave run).
     *
     * @ORM\ManyToOne(targetEntity=Server::class, inversedBy="channels")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Server $server = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $channelName = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description = null;

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

    public function getChannelName(): ?string
    {
        return $this->channelName;
    }

    public function setChannelName(?string $channelName): self
    {
        $this->channelName = $channelName;

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
