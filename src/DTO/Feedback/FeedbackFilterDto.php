<?php

namespace App\DTO\Feedback;

class FeedbackFilterDto
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $type = null;
    public ?string $scope = null;
    public ?string $status = null;
    public ?\DateTimeInterface $createdFrom = null;
    public ?\DateTimeInterface $createdTo = null;
    public ?\DateTimeInterface $updatedFrom = null;
    public ?\DateTimeInterface $updatedTo = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['filter_id']) && is_numeric($data['filter_id'])
            ? (int)$data['filter_id']
            : null;
        $this->name = $data['filter_name'] ?? null;
        $this->type = $data['filter_type'] ?? null;
        $this->scope = $data['filter_scope'] ?? null;
        $this->status = $data['filter_status'] ?? null;
        $this->createdFrom = !empty($data['filter_created_from']) ? new \DateTime($data['filter_created_from']) : null;
        $this->createdTo = !empty($data['filter_created_to']) ? new \DateTime($data['filter_created_to']) : null;
        $this->updatedFrom = !empty($data['filter_updated_from']) ? new \DateTime($data['filter_updated_from']) : null;
        $this->updatedTo = !empty($data['filter_updated_to']) ? new \DateTime($data['filter_updated_to']) : null;
    }
}