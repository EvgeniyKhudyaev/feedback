<?php

namespace App\Entity\Sync;

use App\Enum\Shared\StatusEnum;
use App\Repository\ServiceTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceTypeRepository::class)]
class ServiceType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, nullable: false)]
    private ?string $uuid;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private ?string $name;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $description;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: StatusEnum::class)]
    private ?StatusEnum $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private \DateTimeInterface $updatedAt;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'serviceType', orphanRemoval: true)]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    public function setStatus(StatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setServiceType($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getServiceType() === $this) {
                $service->setServiceType(null);
            }
        }

        return $this;
    }
}
