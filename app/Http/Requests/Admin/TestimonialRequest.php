<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // the role:super_admin,admin middleware already guards these routes
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_published' => $this->boolean('is_published')]);
    }

    public function rules(): array
    {
        return [
            'client_name'  => ['required', 'string', 'max:100'],
            'message'      => ['required', 'string', 'max:1000'],
            'rating'       => ['required', 'integer', 'between:1,5'],
            'is_published' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['client_name' => 'client name'];
    }
}