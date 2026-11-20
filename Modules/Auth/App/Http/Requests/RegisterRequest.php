<?php

namespace Modules\Auth\App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|min:2|max:120',
            'email' => 'required|email|max:254|unique:users',
            'password' => 'required|string|min:8|max:128|confirmed',
            'agree' => 'accepted',
        ];
    }

    public function attributes(): array
    {
        return [
            'agree' => __('common::attributes.agree'),
        ];
    }
}
