<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{

  protected $userRepository;

  public function __construct(UserRepository $userRepository)
  {
      $this->userRepository = $userRepository;
  }

  public function create_employee(array $request)
  {
    $user = $this->userRepository->create([
      'first_name' => $request['first_name'],
      'last_name' => $request['last_name'],
      'email' => $request['email'],
      'password' => $request['password'],
      'phone_number' => $request['phone_number'],
      'location' => $request['location'],
      'role_id' => 3,
    ]);

    $employee = $this->userRepository->create_employee([
      'user_id' => $user->id,
      'government_sector_id' => $request['government_sector_id'],
      'employee_number' => $request['employee_number'],
    ]);

  }

  public function get_all_users()
  {
    return $this->userRepository->get_users();
  }

  public function get_all_employees()
  {
    return $this->userRepository->get_employees();
  }


}
