<?php

namespace App\Entity\Feedback;

use App\Repository\FeedbackFieldDependenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackFieldDependenceRepository::class)]
class FeedbackFieldDependence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFieldDependences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FeedbackField $sourceField = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFieldDependences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FeedbackFieldOption $sourceOption = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFieldDependences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FeedbackField $targetField = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceField(): ?FeedbackField
    {
        return $this->sourceField;
    }

    public function setSourceField(?FeedbackField $sourceField): static
    {
        $this->sourceField = $sourceField;

        return $this;
    }

    public function getSourceValue(): ?FeedbackFieldOption
    {
        return $this->sourceOption;
    }

    public function setSourceValue(?FeedbackFieldOption $sourceOption): static
    {
        $this->sourceOption = $sourceOption;

        return $this;
    }

    public function getTargetField(): ?FeedbackField
    {
        return $this->targetField;
    }

    public function setTargetField(?FeedbackField $targetField): static
    {
        $this->targetField = $targetField;

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

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
