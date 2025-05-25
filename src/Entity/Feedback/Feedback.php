<?php

namespace App\Entity\Feedback;

use App\Enum\Feedback\FeedbackScopeEnum;
use App\Enum\Feedback\FeedbackTypeEnum;
use App\Enum\Shared\StatusEnum;
use App\Repository\FeedbackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Guid\Guid;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ORM\Table(name: '`feedback`')]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, nullable: false)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: FeedbackTypeEnum::class)]
    private ?FeedbackTypeEnum $type = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: FeedbackScopeEnum::class)]
    private ?FeedbackScopeEnum $scope = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: StatusEnum::class)]
    private ?StatusEnum $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, FeedbackField>
     */
    #[ORM\OneToMany(targetEntity: FeedbackField::class, mappedBy: 'feedback', orphanRemoval: true)]
    private Collection $fields;

    /**
     * @var Collection<int, FeedbackManager>
     */
    #[ORM\OneToMany(targetEntity: FeedbackManager::class, mappedBy: 'feedback')]
    private Collection $feedbackEditors;

    /**
     * @var Collection<int, FeedbackTarget>
     */
    #[ORM\OneToMany(targetEntity: FeedbackTarget::class, mappedBy: 'feedback')]
    private Collection $feedbackTargets;

    public function __construct()
    {
        $this->uuid = Guid::uuid4()->toString();
        $this->type = FeedbackTypeEnum::SURVEY;
        $this->scope = FeedbackScopeEnum::GLOBAL;
        $this->status = StatusEnum::ACTIVE;
        $this->fields = new ArrayCollection();
        $this->feedbackEditors = new ArrayCollection();
        $this->feedbackTargets = new ArrayCollection();
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

    public function getType(): ?FeedbackTypeEnum
    {
        return $this->type;
    }

    public function setType(FeedbackTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getScope(): ?FeedbackScopeEnum
    {
        return $this->scope;
    }

    public function setScope(FeedbackScopeEnum $scope): static
    {
        $this->scope = $scope;

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

    /**
     * @return Collection<int, FeedbackField>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

//    public function setFields(Collection $fields): static
//    {
//        $this->fields = $fields;
//
//        foreach ($fields as $field) {
//            $field->setFeedback($this);
//        }
//
//        return $this;
//    }

    public function addField(FeedbackField $feedbackField): static
    {
        if (!$this->fields->contains($feedbackField)) {
            $this->fields->add($feedbackField);
            $feedbackField->setFeedback($this);
        }

        return $this;
    }

    public function removeField(FeedbackField $feedbackField): static
    {
        if ($this->fields->removeElement($feedbackField)) {
            // set the owning side to null (unless already changed)
            if ($feedbackField->getFeedback() === $this) {
                $feedbackField->setFeedback(null);
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
            $feedbackEditor->setFeedback($this);
        }

        return $this;
    }

    public function removeFeedbackEditor(FeedbackManager $feedbackEditor): static
    {
        if ($this->feedbackEditors->removeElement($feedbackEditor)) {
            // set the owning side to null (unless already changed)
            if ($feedbackEditor->getFeedback() === $this) {
                $feedbackEditor->setFeedback(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FeedbackTarget>
     */
    public function getFeedbackTargets(): Collection
    {
        return $this->feedbackTargets;
    }

    public function addFeedbackTarget(FeedbackTarget $feedbackTarget): static
    {
        if (!$this->feedbackTargets->contains($feedbackTarget)) {
            $this->feedbackTargets->add($feedbackTarget);
            $feedbackTarget->setFeedback($this);
        }

        return $this;
    }

    public function removeFeedbackTarget(FeedbackTarget $feedbackTarget): static
    {
        if ($this->feedbackTargets->removeElement($feedbackTarget)) {
            // set the owning side to null (unless already changed)
            if ($feedbackTarget->getFeedback() === $this) {
                $feedbackTarget->setFeedback(null);
            }
        }

        return $this;
    }
}
