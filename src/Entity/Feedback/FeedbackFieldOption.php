<?php

namespace App\Entity\Feedback;

use App\Enum\Shared\StatusEnum;
use App\Repository\FeedbackFieldOptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackFieldOptionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FeedbackFieldOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, nullable: false)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFieldOptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FeedbackField $field = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $value = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $sortOrder = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: StatusEnum::class)]
    private ?StatusEnum $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;


    public function __construct()
    {
        $this->status = StatusEnum::ACTIVE;
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

    public function getField(): ?FeedbackField
    {
        return $this->field;
    }

    public function setField(?FeedbackField $field): static
    {
        $this->field = $field;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

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

    #[ORM\PrePersist]
    public function generateCode(): void
    {
        if (empty($this->value)) {
            $this->value = $this->label;
        }
    }

}
