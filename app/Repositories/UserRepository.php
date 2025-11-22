<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
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
}