<?php

namespace App\DTO\Report;

class ReportSortDto
{
    public string $field = 'date'; // например, дата
    public string $direction = 'asc';
    public function __construct(array $data)
    {
        $this->field = $data['sortField'] ?? 'date';
        $this->direction = $data['sortDirection'] ?? 'asc';
    }
}