<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password' => ['required', 'min:6', 'confirmed'],
            'profile' => ['required', 'mimes:png,jpg'],
            'type' => ['required', 'integer', Rule::in([0,1])],
            'phone' => ['nullable'],
            'address' => ['nullable'],
            'dob' => ['nullable'],
            'created_user_id' => ['required', Rule::exists(User::class, 'id')->whereNull('deleted_at')],
            'updated_user_id' => ['required', Rule::exists(User::class, 'id')->whereNull('deleted_at')],
            'deleted_user_id' => ['nullable', Rule::exists(User::class, 'id')->whereNull('deleted_at')]
        ];
    }

    public function messages()
    {
        return [
            'created_user_id' => "Created user name is required",
            'updated_user_id' => "Updated user name is required"
        ];
    }
}
