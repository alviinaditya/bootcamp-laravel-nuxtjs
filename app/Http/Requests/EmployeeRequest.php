<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'gender' => 'required|string|in:MALE,FEMALE',
            'age' => 'required|integer',
            'phone' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,gif,svg|max:255',
            'team_id' => 'required|integer|exists:teams,id',
            'role_id' => 'required|integer|exists:roles,id',
        ];
    }
}
