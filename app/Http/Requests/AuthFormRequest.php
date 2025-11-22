<?php

namespace App\Http\Requests;

use App\Rules\NoHtml;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AuthFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      return match($this->route()->getActionMethod()) {
        'user_Register' => [
            'first_name' => ['required', 'string', 'max:255', new NoHtml],
            'last_name' => ['required', 'string', 'max:255', new NoHtml],
            'email' => 'required|email|unique:users',
            'phone_number' => ['required', 'unique:users', 'string', new NoHtml],
            'location' => ['required', 'string', 'max:255', new NoHtml],
            'password' => [
                'required',
                'string',
                 Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ],
        'login' => [
            'email' => 'required|email',
            'password' => 'required|string',
        ],
        'verification' => [
            'verification_code' => 'required|digits:6',
        ],
        'reset_password' => [
            'email' => 'required|email',
            'reset_token' => 'required|string',
            'new_password' => [
                'required',
                'string',
                 Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ],

        default => [],
      };
    }
}
