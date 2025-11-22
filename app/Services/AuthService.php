<?php

namespace App\Services;

use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{

  protected $userRepository;

  public function __construct(UserRepository $userRepository)
  {
      $this->userRepository = $userRepository;
  }

  public function user_Register(array $request)
  {
    $user = $this->userRepository->create([
      'first_name' => $request['first_name'],
      'last_name' => $request['last_name'],
      'email' => $request['email'],
      'password' => $request['password'],
      'phone_number' => $request['phone_number'],
      'location' => $request['location'],
      'role_id' => 2,
    ]);

    $this->send_email($user->email);

  }


  public function send_email(string $email)
  {
    $user = $this->userRepository->findByEmail($email);
    if (!$user) return;

    $code = rand(111111, 999999);
    $emailBody = '
      <div style="direction: rtl; text-align: right; font-family: Tahoma, Arial, sans-serif; font-size: 16px;">
          <p>مرحباً '.$user->first_name.'،</p>
        
          <p>
              شكراً لتسجيلك في <strong>نظام شكاوى المواطنين</strong>.  
              يرجى إتمام عملية التحقق من البريد الإلكتروني باستخدام رمز التحقق التالي:
          </p>
        
          <div style="font-size: 24px; font-weight: bold; margin: 20px 0; text-align: center;">
              '.$code.'
          </div>
        
          <p>صلاحية الرمز: 3 دقائق.</p>
        
          <p>
              نشكرك على ثقتك في نظام شكاوى المواطنين.<br>
              مع أطيب التحيات.
          </p>
      </div>
      ';

    Cache::put($user->id, $code, now()->addMinutes(3));

    SendEmailJob::dispatch($user->email, $emailBody);
  }

  public function verification(array $request, string $email)
  {
    $user = $this->userRepository->findByEmail($email);
    if (!$user) return false;

    $cache_value = Cache::get($user->id);

    if ($cache_value && ($request['verification_code'] == $cache_value)) {

      $first_time = false;
      if($user->email_verified_at == null){
        $first_time = true;
      }
      $user->email_verified_at = now();
      $this->userRepository->save($user);

      if($first_time){
        $user['token'] = $user->createToken('AccessToken')->plainTextToken;
        return ['token' => $user['token']];
      }

      $resetToken = Str::random(64);
      Cache::put("reset_token_".$user->id, $resetToken, now()->addMinutes(10));
      
      return ['reset_token' => $resetToken];
    
    }
    return false;
  }


  public function reset_password(array $request)
  {
    $user = $this->userRepository->findByEmail($request['email']);
    if (!$user) return false;

    $storedToken = Cache::get("reset_token_".$user->id);

    if (!$storedToken || $storedToken !== $request['reset_token']) {
        return false;
    }
    $user->password = $request['new_password'];
    $this->userRepository->save($user);

    Cache::forget("reset_token_".$user->id);
    return true;
  }


  public function login(array $request)
  {

    $user = $this->userRepository->findByEmail($request['email']);

    if ($user && Hash::check($request['password'], $user->password)) {

      if ($user['email_verified_at'] == null) {
        return 'unverified';
      }

      $user['token'] = $user->createToken('AccessToken')->plainTextToken;

      $responseData = [
        'message' => 'مرحباً بك',
        'token' => $user['token'],
        'role' => $user->role_id,
      ];

      return $responseData;
    }
    return false;
  }

  public function logout()
  {
    Auth::user()->currentAccessToken()->delete();
  }


}
