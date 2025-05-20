<?php

namespace App\Entity\Feedback;

use App\Repository\FeedbackFieldOptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackFieldOptionRepository::class)]
class FeedbackFieldOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFieldOptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FeedbackField $field = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column]
    private ?int $sortOrder = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, FeedbackFieldDependence>
     */
    #[ORM\OneToMany(targetEntity: FeedbackFieldDependence::class, mappedBy: 'sourceValue')]
    private Collection $feedbackFieldDependences;

    public function __construct()
    {
        $this->feedbackFieldDependences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $feedbackFieldDependence->setSourceValue($this);
        }

        return $this;
    }

    public function removeFeedbackFieldDependence(FeedbackFieldDependence $feedbackFieldDependence): static
    {
        if ($this->feedbackFieldDependences->removeElement($feedbackFieldDependence)) {
            // set the owning side to null (unless already changed)
            if ($feedbackFieldDependence->getSourceValue() === $this) {
                $feedbackFieldDependence->setSourceValue(null);
            }
        }

        return $this;
    }
}
