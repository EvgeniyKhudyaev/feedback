<?php

namespace App\Entity;

use App\Enum\Status;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Guid\Guid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, nullable: false)]
    private ?string $uuid = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 512, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $telegram = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $password = null;

//    #[ORM\Column(type: 'string', length: 50, enumType: UserRole::class)]
//    private ?UserRole $role;

    #[ORM\Column(type: 'string', length: 50, enumType: UserRole::class)]
    private ?string $role;

    #[ORM\Column(type: 'string', length: 20, enumType: Status::class)]
    private ?string $status;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, ServiceHistory>
     */
    #[ORM\OneToMany(targetEntity: ServiceHistory::class, mappedBy: 'creator')]
    private Collection $serviceHistories;

    public function __construct()
    {
        $this->uuid = Guid::uuid4()->toString();
        $this->serviceHistories = new ArrayCollection();
        $this->status = Status::Active->value;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getTelegram(): ?string
    {
        return $this->telegram;
    }

    public function setTelegram(?string $telegram): static
    {
        $this->telegram = $telegram;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
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

    public function getRoles(): array
    {
        return [$this->role ?? UserRole::Member];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? $this->uuid;
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
            $serviceHistory->setCreator($this);
        }

        return $this;
    }

    public function removeServiceHistory(ServiceHistory $serviceHistory): static
    {
        if ($this->serviceHistories->removeElement($serviceHistory)) {
            // set the owning side to null (unless already changed)
            if ($serviceHistory->getCreator() === $this) {
                $serviceHistory->setCreator(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
