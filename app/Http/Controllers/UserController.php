<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function get_all_users()
    {
      $users = $this->userService->get_all_users();
      return response()->json([
        'users' => $users
      ]);
    }

    public function get_all_employees()
    {
      $employees = $this->userService->get_all_employees();
      return response()->json([
        'employees' => $employees
      ]);
    }

    public function create_employee(UserFormRequest $request)
    {
      $result = $this->userService->create_employee($request->validated());
      return response()->json([
          'message' => 'تم إنشاء موظف بنجاح',
      ], 200);
    }
}
