<?php

namespace App\Entity\Sync;

use App\Enum\Shared\Status;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, nullable: false)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ServiceType $serviceType = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: Status::class)]
    private ?Status $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, ServiceHistory>
     */
    #[ORM\OneToMany(targetEntity: ServiceHistory::class, mappedBy: 'service')]
    private Collection $serviceHistories;

    public function __construct()
    {
        $this->serviceHistories = new ArrayCollection();
    }


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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getServiceType(): ?ServiceType
    {
        return $this->serviceType;
    }

    public function setServiceType(ServiceType $serviceType): static
    {
        $this->serviceType = $serviceType;

        return $this;
    }

    public function getStatus(): ?Status
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

    /**
     * @return Collection<int, ServiceHistory>
     */
    public function getServiceHistories(): Collection
    {
        return $this->serviceHistories;
    }

    public function addServiceHistory(ServiceHistory $serviceHistory): static
    {
        if (!$this->serviceHistories->contains($serviceHistory)) {
            $this->serviceHistories->add($serviceHistory);
            $serviceHistory->setService($this);
        }

        return $this;
    }

    public function removeServiceHistory(ServiceHistory $serviceHistory): static
    {
        if ($this->serviceHistories->removeElement($serviceHistory)) {
            // set the owning side to null (unless already changed)
            if ($serviceHistory->getService() === $this) {
                $serviceHistory->setService(null);
            }
        }

        return $this;
    }




}
