<?php

namespace App\Entity\Feedback;

use App\Entity\MessageLog;
use App\Enum\Feedback\FeedbackScopeEnum;
use App\Enum\Feedback\FeedbackTypeEnum;
use App\Enum\Shared\StatusEnum;
use App\Repository\FeedbackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ORM\Table(name: '`feedback`')]
#[UniqueEntity(
    fields: ['name'],
    message: 'Опрос с таким названием уже существует.'
)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, nullable: false)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    #[Assert\NotBlank(message: 'Название опроса не должно быть пустым.')]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: FeedbackTypeEnum::class)]
    private ?FeedbackTypeEnum $type = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: FeedbackScopeEnum::class)]
    #[Assert\NotBlank(message: 'Область опроса не должна быть пустой.')]
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
    #[ORM\OneToMany(targetEntity: FeedbackField::class, mappedBy: 'feedback', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Count(min: 1, minMessage: "Должен быть указан хотя бы один вопрос.")]
    private Collection $fields;

    /**
     * @var Collection<int, FeedbackManager>
     */
    #[ORM\OneToMany(targetEntity: FeedbackManager::class, mappedBy: 'feedback')]
    private Collection $editors;

    /**
     * @var Collection<int, FeedbackTarget>
     */
    #[ORM\OneToMany(targetEntity: FeedbackTarget::class, mappedBy: 'feedback')]
    private Collection $targets;

    /**
     * @var Collection<int, MessageLog>
     */
    #[ORM\OneToMany(targetEntity: MessageLog::class, mappedBy: 'feedback')]
    private Collection $messageLogs;

    public function __construct()
    {
        $this->type = FeedbackTypeEnum::SURVEY;
        $this->status = StatusEnum::ACTIVE;
        $this->fields = new ArrayCollection();
        $this->editors = new ArrayCollection();
        $this->targets = new ArrayCollection();
        $this->messageLogs = new ArrayCollection();
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
    public function getEditors(): Collection
    {
        return $this->editors;
    }

    public function getActiveEditors(): Collection
    {
        return $this->editors->filter(fn(FeedbackManager $editor) => $editor->getIsActive());
    }

    public function addEditor(FeedbackManager $feedbackEditor): static
    {
        if (!$this->editors->contains($feedbackEditor)) {
            $this->editors->add($feedbackEditor);
            $feedbackEditor->setFeedback($this);
        }

        return $this;
    }

    public function removeEditor(FeedbackManager $feedbackEditor): static
    {
        if ($this->editors->removeElement($feedbackEditor)) {
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
    public function getTargets(): Collection
    {
        return $this->targets;
    }

    public function addTarget(FeedbackTarget $feedbackTarget): static
    {
        if (!$this->targets->contains($feedbackTarget)) {
            $this->targets->add($feedbackTarget);
            $feedbackTarget->setFeedback($this);
        }

        return $this;
    }

    public function removeTarget(FeedbackTarget $feedbackTarget): static
    {
        if ($this->targets->removeElement($feedbackTarget)) {
            // set the owning side to null (unless already changed)
            if ($feedbackTarget->getFeedback() === $this) {
                $feedbackTarget->setFeedback(null);
            }
        }

        return $this;
    }

    public function getActiveTargets(): Collection
    {
        return $this->targets->filter(fn(FeedbackTarget $target) => $target->getIsActive());
    }

    public function hasEditor(UserInterface $user): bool
    {
        return $this->getActiveEditors()->exists(
            fn($key, $fm) => $fm->getEditor() === $user
        );
    }

    /**
     * @return Collection<int, MessageLog>
     */
    public function getMessageLogs(): Collection
    {
        return $this->messageLogs;
    }

    public function addMessageLog(MessageLog $messageLog): static
    {
        if (!$this->messageLogs->contains($messageLog)) {
            $this->messageLogs->add($messageLog);
            $messageLog->setFeedback($this);
        }

        return $this;
    }

    public function removeMessageLog(MessageLog $messageLog): static
    {
        if ($this->messageLogs->removeElement($messageLog)) {
            // set the owning side to null (unless already changed)
            if ($messageLog->getFeedback() === $this) {
                $messageLog->setFeedback(null);
            }
        }

        return $this;
    }

    public function normalizeFieldsSortOrder(): void
    {
        foreach ($this->getFields() as $index => $field) {
            $field->setSortOrder($index + 1);
            $field->normalizeOptionsSortOrder();
        }
    }
}
