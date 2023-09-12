<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        $id = $this->route('user')->id;
        return [
            'name' => ['required'],
            'email' => ['required', Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($id)],
            'password' => ['required', 'min:6', 'confirmed'],
            'profile' => ['required', 'mimes:png,jpg'],
            'type' => ['required', Rule::in([0,1])],
            'phone' => ['nullable'],
            'address' => ['nullable'],
            'dob' => ['nullable'],
            'created_user_id' => ['required', Rule::exists(User::class, 'id')->whereNull('deleted_at')],
            'updated_user_id' => ['required', Rule::exists(User::class, 'id')->whereNull('deleted_at')],
            'deleted_user_id' => ['nullable', Rule::exists(User::class, 'id')->whereNull('deleted_at')]
        ];
    }
}
