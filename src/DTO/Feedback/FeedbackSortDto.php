<?php

namespace App\DTO\Feedback;

class FeedbackSortDto
{
    public string $field;
    public string $direction;
    private array $allowedFields = ['f.id', 'f.name', 'f.type', 'f.scope', 'f.status', 'f.createdAt', 'f.updatedAt'];

    public function __construct(array $data = [])
    {
        $this->field = $data['sort'] ?? 'f.id';
        $this->direction = strtoupper($data['direction'] ?? 'ASC');

        if (!in_array($this->direction, ['ASC', 'DESC'], true)) {
            $this->direction = 'ASC';
        }

        if (!in_array($this->field, $this->allowedFields, true)) {
            $this->field = 'f.id';
        }
    }
}