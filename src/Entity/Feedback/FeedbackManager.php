<?php

namespace App\Entity\Feedback;

use App\Entity\User;
use App\Repository\FeedbackEditorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackEditorRepository::class)]
#[ORM\Table(name: 'feedback_manager', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_feedback_editor', columns: ['feedback_id', 'editor_id'])
])]
class FeedbackManager
{
    public const STATUS_ACTIVE = true;
    public const STATUS_INACTIVE = false;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Feedback::class, inversedBy: 'feedbackEditors')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Feedback $feedback = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'feedbackEditors')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $editor = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $isActive = true;

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

    public function getEditor(): ?User
    {
        return $this->editor;
    }

    public function setEditor(?User $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
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

    public static function create(User $user, Feedback $feedback): static
    {
        $feedbackManager = new static();
        $feedbackManager->setEditor($user);
        $feedbackManager->setFeedback($feedback);
        $feedbackManager->setIsActive(static::STATUS_ACTIVE);

        return $feedbackManager;
    }
}
