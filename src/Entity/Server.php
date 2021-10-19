<?php

namespace App\Entity;

use Ambta\DoctrineEncryptBundle\Configuration\Encrypted;
use App\Repository\ServerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ServerRepository::class)
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"name"}),
 *     @ORM\UniqueConstraint(columns={"uuid"}),
 * })
 * @UniqueEntity("name")
 * @UniqueEntity("uuid")
 */
class Server
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cluster::class, inversedBy="servers")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Cluster $cluster;

    /**
     * MySQL Server ID
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $serverId = null;

    /**
     * @ORM\Column(type="guid", nullable=true)
     */
    private ?string $uuid = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $host;

    /**
     * @ORM\Column(type="integer")
     */
    private int $port;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Encrypted
     */
    private ?string $password;

    /**
     * Server's Slaves (master side)
     *
     * @ORM\OneToMany(targetEntity=Slave::class, mappedBy="master")
     *
     * @var Slave[]|ArrayCollection
     */
    private $slaves;

    /**
     * Server's Slaves (slave side)
     *
     * @ORM\OneToMany(targetEntity=Slave::class, mappedBy="server")
     *
     * @var Slave[]|ArrayCollection
     */
    private $channels;

    public function __construct()
    {
        $this->slaves = new ArrayCollection();
        $this->channels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCluster(): ?Cluster
    {
        return $this->cluster;
    }

    public function setCluster(?Cluster $cluster): self
    {
        $this->cluster = $cluster;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getServerId(): ?int
    {
        return $this->serverId;
    }

    public function setServerId(?int $serverId): self
    {
        $this->serverId = $serverId;

        return $this;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Slave[]
     */
    public function getSlaves(): Collection
    {
        return $this->slaves;
    }

    public function addSlave(Slave $slave): self
    {
        if (!$this->slaves->contains($slave)) {
            $this->slaves[] = $slave;
            $slave->setMaster($this);
        }

        return $this;
    }

    public function removeSlave(Slave $slave): self
    {
        if ($this->slaves->removeElement($slave)) {
            // set the owning side to null (unless already changed)
            if ($slave->getMaster() === $this) {
                $slave->setMaster(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Slave[]
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(Slave $channel): self
    {
        if (!$this->channels->contains($channel)) {
            $this->channels[] = $channel;
            $channel->setServer($this);
        }

        return $this;
    }

    public function removeChannel(Slave $channel): self
    {
        if ($this->channels->removeElement($channel)) {
            // set the owning side to null (unless already changed)
            if ($channel->getServer() === $this) {
                $channel->setServer(null);
            }
        }

        return $this;
    }
}
