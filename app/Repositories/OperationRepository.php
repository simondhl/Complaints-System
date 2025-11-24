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

}