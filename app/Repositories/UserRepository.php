<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\User;

class UserRepository
{
    protected $model;
    protected $employee;

    public function __construct(User $user, Employee $employee)
    {
        $this->model = $user;
        $this->employee = $employee;
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByID($id): ?User
    {
        return $this->model->where('id', $id)
            ->select('id', 'first_name', 'last_name', 'phone_number', 'location')->first();
    }

    public function save(User $user): bool
    {
        return $user->save();
    }

    public function create_employee(array $data)
    {
        return $this->employee->create($data);
    }

    public function get_users()
    {
        return $this->model->where('role_id', 2)->select('id', 'first_name', 'last_name', 'phone_number', 'location')->get();
    }

    public function get_employees()
    {
        return $this->model->with('employee')->where('role_id', 3)
            ->select('id', 'first_name', 'last_name', 'phone_number', 'location')->get();
    }
}