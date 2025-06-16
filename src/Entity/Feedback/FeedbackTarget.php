<?php

namespace App\Entity\Feedback;

use App\Entity\Sync\Service;
use App\Enum\Feedback\FeedbackTargetTypeEnum;
use App\Repository\FeedbackTargetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackTargetRepository::class)]
class FeedbackTarget
{
    public const STATUS_ACTIVE = true;
    public const STATUS_INACTIVE = false;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackTargets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Feedback $feedback = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: FeedbackTargetTypeEnum::class)]
    private ?FeedbackTargetTypeEnum $targetType = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $target = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
    private ?bool $isActive = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTargetType(): ?FeedbackTargetTypeEnum
    {
        return $this->targetType;
    }

    public function setTargetType(FeedbackTargetTypeEnum $targetType): static
    {
        $this->targetType = $targetType;

        return $this;
    }

    public function getTarget(): ?int
    {
        return $this->target;
    }

    public function setTarget(int $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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

    public static function create(int $targetId, Feedback $feedback): static
    {
        $feedbackManager = new static();
        $feedbackManager->setTarget($targetId);
        $feedbackManager->setFeedback($feedback);
        $feedbackManager->setIsActive(static::STATUS_ACTIVE);
        $feedbackManager->setTargetType(FeedbackTargetTypeEnum::SERVICE);

        return $feedbackManager;
    }
}
