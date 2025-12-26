<?php

namespace App\Repositories;

use App\Models\Operation;

class OperationRepository
{
    protected $operation;

    public function __construct(Operation $operation)
    {
        $this->operation = $operation;
    }

    public function createOperation(array $data)
    {
        return $this->operation->create($data);
    }

    public function get_operations_by_date($start_date, $end_date)
    {
        return $this->operation->whereDate('operation_date', '>=', $start_date)
        ->whereDate('operation_date', '<=', $end_date)
        ->select('complaint_id', 'details', 'operation_date')->get();
    }

}