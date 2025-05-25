<?php

namespace App\Entity\User;

use App\Entity\Feedback\FeedbackFieldAnswer;
use App\Entity\Feedback\FeedbackManager;
use App\Entity\Shared\File;
use App\Enum\Shared\StatusEnum;
use App\Enum\UserRoleEnum;
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

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?File $avatar = null;
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $telegram = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $password = null;

//    #[ORM\Column(type: 'string', length: 50, enumType: UserRole::class)]
//    private ?UserRole $role;

    #[ORM\Column(type: 'string', length: 50, enumType: UserRoleEnum::class)]
    private ?UserRoleEnum $role;

    #[ORM\Column(type: 'string', length: 20, enumType: StatusEnum::class)]
    private ?StatusEnum $status;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'owner')]
    private Collection $files;

    /**
     * @var Collection<int, FeedbackFieldAnswer>
     */
    #[ORM\OneToMany(targetEntity: FeedbackFieldAnswer::class, mappedBy: 'responder')]
    private Collection $feedbackFieldValues;

    /**
     * @var Collection<int, FeedbackManager>
     */
    #[ORM\OneToMany(targetEntity: FeedbackManager::class, mappedBy: 'editor')]
    private Collection $feedbackEditors;

    public function __construct()
    {
        $this->uuid = Guid::uuid4()->toString();
        $this->status = StatusEnum::ACTIVE;
        $this->files = new ArrayCollection();
        $this->feedbackFieldValues = new ArrayCollection();
        $this->feedbackEditors = new ArrayCollection();
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

    public function getAvatar(): ?File
    {
        return $this->avatar;
    }

    public function setAvatar(File $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getTelegram(): ?string
    {
        return $this->telegram;
    }

    public function setTelegram(string $telegram): static
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

    public function getRole(): ?UserRoleEnum
    {
        return $this->role;
    }

    public function setRole(UserRoleEnum $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getStatus(): ?StatusEnum
    {
        return $this->status;
    }

    public function setStatus(StatusEnum $status): static
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
        return [$this->role->value ?? UserRoleEnum::MEMBER->value];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? $this->uuid;
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

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setOwner($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getOwner() === $this) {
                $file->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FeedbackFieldAnswer>
     */
    public function getFeedbackFieldValues(): Collection
    {
        return $this->feedbackFieldValues;
    }

    public function addFeedbackFieldValue(FeedbackFieldAnswer $feedbackFieldValue): static
    {
        if (!$this->feedbackFieldValues->contains($feedbackFieldValue)) {
            $this->feedbackFieldValues->add($feedbackFieldValue);
            $feedbackFieldValue->setResponder($this);
        }

        return $this;
    }

    public function removeFeedbackFieldValue(FeedbackFieldAnswer $feedbackFieldValue): static
    {
        if ($this->feedbackFieldValues->removeElement($feedbackFieldValue)) {
            // set the owning side to null (unless already changed)
            if ($feedbackFieldValue->getResponder() === $this) {
                $feedbackFieldValue->setResponder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FeedbackManager>
     */
    public function getFeedbackEditors(): Collection
    {
        return $this->feedbackEditors;
    }

    public function addFeedbackEditor(FeedbackManager $feedbackEditor): static
    {
        if (!$this->feedbackEditors->contains($feedbackEditor)) {
            $this->feedbackEditors->add($feedbackEditor);
            $feedbackEditor->setEditor($this);
        }

        return $this;
    }

    public function removeFeedbackEditor(FeedbackManager $feedbackEditor): static
    {
        if ($this->feedbackEditors->removeElement($feedbackEditor)) {
            // set the owning side to null (unless already changed)
            if ($feedbackEditor->getEditor() === $this) {
                $feedbackEditor->setEditor(null);
            }
        }

        return $this;
    }
}
