<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(StoreContactMessageRequest $request)
    {
        // Honeypot: bots fill the hidden field. Pretend it worked, save nothing.
        if ($request->filled('website')) {
            return redirect(route('contact') . '#contact-form')->with('contact_success', true);
        }

        $data = $request->validated();

        // Save phones as +254XXXXXXXXX when one was given
        $phone = null;
        if (! empty($data['phone'])) {
            $digits = preg_replace('/\D/', '', $data['phone']);
            $digits = str_starts_with($digits, '254') ? substr($digits, 3) : ltrim($digits, '0');
            $phone = '+254' . $digits;
        }

        ContactMessage::create([
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $phone,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'is_read' => false,
        ]);

        return redirect(route('contact') . '#contact-form')->with('contact_success', true);
    }
}