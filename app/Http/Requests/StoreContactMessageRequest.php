<?php

namespace App\Http\Requests;

use App\Models\ContactMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
{
    // On validation errors, return to the form itself
    protected $redirect = '/contact#contact-form';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Ignore spaces, dashes, dots and brackets in the phone number
        $this->merge([
            'phone' => preg_replace('/[\s\-().]/', '', (string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'phone'   => ['nullable', 'regex:/^(?:\+?254|0)[17]\d{8}$/'],
            'subject' => ['required', Rule::in(ContactMessage::SUBJECTS)],
            'message' => ['required', 'string', 'max:3000'],
            'consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex'      => 'Enter a valid Kenyan mobile number, for example 0700 000 000, or leave it empty.',
            'subject.in'       => 'Please choose an inquiry topic.',
            'consent.accepted' => 'Please agree to be contacted so we can reply to your message.',
        ];
    }
}