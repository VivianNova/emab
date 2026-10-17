<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // the role:super_admin middleware already guards these routes
    }

    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/[\s\-()]/', '', (string) $this->input('phone'));

        if (preg_match('/^(?:\+?254|0)([17]\d{8})$/', $phone, $m)) {
            $phone = '+254' . $m[1];
        }

        $this->merge([
            'phone'     => $phone === '' ? null : $phone,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $user = $this->route('user'); // null when creating

        return [
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'phone'     => ['nullable', 'regex:/^\+254[17]\d{8}$/'],
            'role'      => ['required', Rule::in(['super_admin', 'admin', 'staff'])],
            'is_active' => ['boolean'],
            'password'  => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Enter a Kenyan number, for example 0712 345 678.'];
    }
}