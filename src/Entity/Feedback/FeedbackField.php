<?php

namespace App\Entity\Feedback;

use App\Enum\Feedback\FeedbackFieldType;
use App\Enum\Shared\Status;
use App\Repository\FeedbackFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackFieldRepository::class)]
class FeedbackField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Feedback $feedback = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $code = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: FeedbackFieldType::class)]
    private ?FeedbackFieldType $type = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $required = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $sortOrder = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $isHiddenByDefault = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: Status::class)]
    private ?Status $status;

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
    #[ORM\OneToMany(targetEntity: FeedbackFieldOption::class, mappedBy: 'field')]
    private Collection $feedbackFieldOptions;

    /**
     * @var Collection<int, FeedbackFieldDependence>
     */
    #[ORM\OneToMany(targetEntity: FeedbackFieldDependence::class, mappedBy: 'sourceField')]
    private Collection $feedbackFieldDependences;

    public function __construct()
    {
        $this->feedbackFieldValues = new ArrayCollection();
        $this->feedbackFieldOptions = new ArrayCollection();
        $this->feedbackFieldDependences = new ArrayCollection();
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

    public function getType(): ?FeedbackFieldType
    {
        return $this->type;
    }

    public function setType(FeedbackFieldType $type): static
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

    public function getIsHiddenByDefault(): ?bool
    {
        return $this->isHiddenByDefault;
    }

    public function setIsHiddenByDefault(bool $isHiddenByDefault): static
    {
        $this->isHiddenByDefault = $isHiddenByDefault;

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
    public function getFeedbackFieldOptions(): Collection
    {
        return $this->feedbackFieldOptions;
    }

    public function addFeedbackFieldOption(FeedbackFieldOption $feedbackFieldOption): static
    {
        if (!$this->feedbackFieldOptions->contains($feedbackFieldOption)) {
            $this->feedbackFieldOptions->add($feedbackFieldOption);
            $feedbackFieldOption->setField($this);
        }

        return $this;
    }

    public function removeFeedbackFieldOption(FeedbackFieldOption $feedbackFieldOption): static
    {
        if ($this->feedbackFieldOptions->removeElement($feedbackFieldOption)) {
            // set the owning side to null (unless already changed)
            if ($feedbackFieldOption->getField() === $this) {
                $feedbackFieldOption->setField(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FeedbackFieldDependence>
     */
    public function getFeedbackFieldDependences(): Collection
    {
        return $this->feedbackFieldDependences;
    }

    public function addFeedbackFieldDependence(FeedbackFieldDependence $feedbackFieldDependence): static
    {
        if (!$this->feedbackFieldDependences->contains($feedbackFieldDependence)) {
            $this->feedbackFieldDependences->add($feedbackFieldDependence);
            $feedbackFieldDependence->setSourceField($this);
        }

        return $this;
    }

    public function removeFeedbackFieldDependence(FeedbackFieldDependence $feedbackFieldDependence): static
    {
        if ($this->feedbackFieldDependences->removeElement($feedbackFieldDependence)) {
            // set the owning side to null (unless already changed)
            if ($feedbackFieldDependence->getSourceField() === $this) {
                $feedbackFieldDependence->setSourceField(null);
            }
        }

        return $this;
    }
}
