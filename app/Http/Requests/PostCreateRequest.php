<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PostCreateRequest extends FormRequest
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
            'title' => ['required', 'max:255', Rule::unique('posts', 'title')->whereNull('deleted_at')],
            'description' => ['required'],
            'flag' => ['required', 'boolean'],
            'created_user_id' => ['required', Rule::exists(User::class, 'id')->whereNull('deleted_at')],
            'updated_user_id' => ['required', Rule::exists(User::class, 'id')->whereNull('deleted_at')],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => "User name is required",
            'created_user_id' => "Created user name is required",
            'updated_user_id' => "Updated user name is required"
        ];
    }
}
