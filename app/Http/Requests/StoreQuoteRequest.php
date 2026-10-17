<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Strip spaces, dashes, dots and brackets so "0700 000 000" passes
    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/[\s\-().]/', '', (string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            // Kenyan mobile: 07xx / 01xx, or +2547xx / +2541xx / 2547xx
            'phone' => ['required', 'regex:/^(?:\+254|254|0)[17]\d{8}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'service_id' => ['nullable', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
            'location' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            // Checked on the server: real file type (not just the extension) and size in KB
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'privacy_consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid Kenyan phone number, e.g. 0700 000 000.',
            'photo.mimes' => 'The file must be a JPG, PNG or PDF.',
            'photo.max' => 'The file must not be larger than 5 MB.',
            'privacy_consent.accepted' => 'Please agree to be contacted so we can respond to your request.',
        ];
    }

    // Always store phones as +2547XXXXXXXX
    public function normalizedPhone(): string
    {
        $phone = ltrim((string) $this->input('phone'), '+');

        if (str_starts_with($phone, '0')) {
            $phone = '254'.substr($phone, 1);
        }

        return '+'.$phone;
    }
}
