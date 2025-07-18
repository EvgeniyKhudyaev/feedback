<?php

namespace App\Entity\Feedback;

use App\Enum\Feedback\FeedbackFieldTypeEnum;
use App\Enum\Shared\StatusEnum;
use App\Repository\FeedbackFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackFieldRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FeedbackField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, nullable: false)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(targetEntity: Feedback::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Feedback $feedback = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $code = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Вопрос не должен быть пустым.')]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: FeedbackFieldTypeEnum::class)]
    #[Assert\NotBlank(message: 'Тип вопроса не должен быть пустым.')]
    private ?FeedbackFieldTypeEnum $type = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $required = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $sortOrder = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: StatusEnum::class)]
    private ?StatusEnum $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, FeedbackFieldAnswer>
     */
    #[ORM\OneToMany(targetEntity: FeedbackFieldAnswer::class, mappedBy: 'field')]
    private Collection $feedbackFieldValues;

    /**
     * @var Collection<int, FeedbackFieldOption>
     */
    #[ORM\OneToMany(
        targetEntity: FeedbackFieldOption::class,
        mappedBy: 'field',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $options;

    public function __construct()
    {
        $this->status = StatusEnum::ACTIVE;
        $this->feedbackFieldValues = new ArrayCollection();
        $this->options = new ArrayCollection();
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

    public function getFeedback(): ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(?Feedback $feedback): static
    {
        $this->feedback = $feedback;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getType(): ?FeedbackFieldTypeEnum
    {
        return $this->type;
    }

    public function setType(FeedbackFieldTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): static
    {
        $this->required = $required;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;

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
            $feedbackFieldValue->setField($this);
        }

        return $this;
    }

    public function removeFeedbackFieldValue(FeedbackFieldAnswer $feedbackFieldValue): static
    {
        if ($this->feedbackFieldValues->removeElement($feedbackFieldValue)) {
            // set the owning side to null (unless already changed)
            if ($feedbackFieldValue->getField() === $this) {
                $feedbackFieldValue->setField(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FeedbackFieldOption>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(FeedbackFieldOption $feedbackFieldOption): static
    {
        if (!$this->options->contains($feedbackFieldOption)) {
            $this->options->add($feedbackFieldOption);
            $feedbackFieldOption->setField($this);
        }

        return $this;
    }

    public function removeOption(FeedbackFieldOption $feedbackFieldOption): static
    {
        if ($this->options->removeElement($feedbackFieldOption)) {
            // set the owning side to null (unless already changed)
            if ($feedbackFieldOption->getField() === $this) {
                $feedbackFieldOption->setField(null);
            }
        }

        return $this;
    }

    public function hasOptions(): bool
    {
        return !$this->options->isEmpty();
    }

    #[ORM\PrePersist]
    public function generateCode(): void
    {
        if (empty($this->code)) {
            $this->code = substr(hash('sha256', $this->label), 0, 8) . "_$this->sortOrder";
        }
    }

    public function normalizeOptionsSortOrder(): void
    {
        foreach ($this->getOptions() as $index => $option) {
            $option->setSortOrder($index + 1);
        }
    }
}
