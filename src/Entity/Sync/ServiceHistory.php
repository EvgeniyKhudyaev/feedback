<?php

namespace App\Entity\Sync;

use App\Enum\Shared\Status;
use App\Repository\ServiceHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceHistoryRepository::class)]
class ServiceHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, nullable: false)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'serviceHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'serviceHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClientUser $creator = null;

    #[ORM\ManyToOne(inversedBy: 'serviceHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ServiceState $state = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $note = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: Status::class)]
    private ?Status $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getCreator(): ?ClientUser
    {
        return $this->creator;
    }

    public function setCreator(ClientUser $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function getState(): ?ServiceState
    {
        return $this->state;
    }

    public function setState(ServiceState $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
