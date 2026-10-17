<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private function sections(): array
    {
        $phone = 'regex:/^\+254[17]\d{8}$/';

        return [
            [
                'title' => 'Company', 'subtitle' => 'The business name shown on the website',
                'icon' => 'bi-building',
                'fields' => [
                    ['key' => 'company_name', 'label' => 'Company name', 'type' => 'text', 'icon' => 'bi-building',
                     'placeholder' => 'EMAB Electrical', 'wide' => true,
                     'help' => 'Shown in the footer and the browser title.',
                     'rules' => ['required', 'string', 'max:150']],
                ],
            ],
            [
                'title' => 'Phone and WhatsApp', 'subtitle' => 'How customers call or message you',
                'icon' => 'bi-telephone',
                'fields' => [
                    ['key' => 'main_phone', 'label' => 'Main phone', 'type' => 'tel', 'icon' => 'bi-telephone',
                     'placeholder' => '0712 345 678', 'phone' => true,
                     'help' => 'Kenyan number, for example 0712 345 678.',
                     'rules' => ['required', 'string', $phone]],
                    ['key' => 'secondary_phone', 'label' => 'Second phone (optional)', 'type' => 'tel', 'icon' => 'bi-phone',
                     'placeholder' => '0722 345 678', 'phone' => true,
                     'help' => 'Leave empty if you only have one number.',
                     'rules' => ['nullable', 'string', $phone]],
                    ['key' => 'whatsapp_number', 'label' => 'WhatsApp number', 'type' => 'tel', 'icon' => 'bi-whatsapp',
                     'placeholder' => '0712 345 678', 'phone' => true, 'whatsapp' => true,
                     'help' => 'Used for the WhatsApp button. Saved without the + sign, as WhatsApp links need.',
                     'rules' => ['nullable', 'string', $phone]],
                ],
            ],
            [
                'title' => 'Email', 'subtitle' => 'Where customers can write to you',
                'icon' => 'bi-envelope',
                'fields' => [
                    ['key' => 'general_email', 'label' => 'General email', 'type' => 'email', 'icon' => 'bi-at',
                     'placeholder' => 'info@example.co.ke',
                     'help' => 'Shown on the contact page and footer.',
                     'rules' => ['required', 'email', 'max:150']],
                    ['key' => 'quotes_email', 'label' => 'Quotes email (optional)', 'type' => 'email', 'icon' => 'bi-receipt',
                     'placeholder' => 'quotes@example.co.ke',
                     'help' => 'Leave empty to use the general email.',
                     'rules' => ['nullable', 'email', 'max:150']],
                ],
            ],
            [
                'title' => 'Location and hours', 'subtitle' => 'Where you are and when you work',
                'icon' => 'bi-geo-alt',
                'fields' => [
                    ['key' => 'physical_address', 'label' => 'Physical address', 'type' => 'textarea', 'icon' => 'bi-geo-alt',
                     'placeholder' => 'Street or building, town, Bungoma County',
                     'rules' => ['nullable', 'string', 'max:255']],
                    ['key' => 'po_box', 'label' => 'Postal address (P.O. Box)', 'type' => 'textarea', 'icon' => 'bi-mailbox',
                     'placeholder' => 'P.O. Box 123, Bungoma',
                     'rules' => ['nullable', 'string', 'max:255']],
                    ['key' => 'working_hours', 'label' => 'Working hours', 'type' => 'text', 'icon' => 'bi-clock', 'wide' => true,
                     'placeholder' => 'Mon - Fri: 8:00 AM - 5:00 PM',
                     'rules' => ['nullable', 'string', 'max:255']],
                    ['key' => 'maps_embed_url', 'label' => 'Google Maps embed link', 'type' => 'url', 'icon' => 'bi-map', 'wide' => true,
                     'placeholder' => 'https://www.google.com/maps/embed?pb=...',
                     'help' => 'In Google Maps choose Share > Embed a map, then paste only the address inside src="...".',
                     'rules' => ['nullable', 'url:https', 'max:1000', 'starts_with:https://www.google.com/maps/embed']],
                ],
            ],
            [
                'title' => 'Social media', 'subtitle' => 'Links to your pages (leave empty to hide)',
                'icon' => 'bi-globe',
                'fields' => [
                    ['key' => 'social_facebook', 'label' => 'Facebook page', 'type' => 'url', 'icon' => 'bi-facebook',
                     'placeholder' => 'https://facebook.com/...', 'rules' => ['nullable', 'url:https,http', 'max:255']],
                    ['key' => 'social_instagram', 'label' => 'Instagram page', 'type' => 'url', 'icon' => 'bi-instagram',
                     'placeholder' => 'https://instagram.com/...', 'rules' => ['nullable', 'url:https,http', 'max:255']],
                    ['key' => 'social_x', 'label' => 'X page', 'type' => 'url', 'icon' => 'bi-twitter-x',
                     'placeholder' => 'https://x.com/...', 'rules' => ['nullable', 'url:https,http', 'max:255']],
                    ['key' => 'social_linkedin', 'label' => 'LinkedIn page', 'type' => 'url', 'icon' => 'bi-linkedin',
                     'placeholder' => 'https://linkedin.com/company/...', 'rules' => ['nullable', 'url:https,http', 'max:255']],
                ],
            ],
        ];
    }

    private function fields()
    {
        return collect($this->sections())->flatMap(fn ($s) => $s['fields'])->keyBy('key');
    }

    /** 0712 345 678, 254712345678, +254 712-345-678 -> +254712345678. Anything else is returned unchanged so validation fails. */
    private function normalisePhone(?string $value): ?string
    {
        $digits = preg_replace('/[\s\-()]/', '', (string) $value);

        if ($digits === '') {
            return null;
        }

        if (preg_match('/^(?:\+?254|0)([17]\d{8})$/', $digits, $m)) {
            return '+254' . $m[1];
        }

        return $value;
    }

    public function edit()
    {
        $values = $this->fields()->map(function ($field, $key) {
            $value = (string) setting($key);

            return !empty($field['whatsapp']) && $value !== '' ? '+' . ltrim($value, '+') : $value;
        });

        return view('admin.settings.edit', [
            'sections' => $this->sections(),
            'values'   => $values,
        ]);
    }

    public function update(Request $request)
    {
        $fields = $this->fields();

        $request->merge(
            $fields->filter(fn ($f) => !empty($f['phone']))
                ->mapWithKeys(fn ($f, $key) => [$key => $this->normalisePhone($request->input($key))])
                ->filter(fn ($v) => $v !== null)
                ->all()
        );

        $data = $request->validate($fields->map(fn ($f) => $f['rules'])->all());

        $changed = [];

        foreach ($fields as $key => $field) {
            $new = (string) ($data[$key] ?? '');

            if (!empty($field['whatsapp'])) {
                $new = ltrim($new, '+');
            }

            if ((string) setting($key) !== $new) {
                $changed[] = $field['label'];
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $new]);
        }

        if ($changed) {
            ActivityLog::record('settings_changed', 'Updated settings: ' . implode(', ', $changed));
        }

        return redirect()->route('admin.settings.edit')->with('status', 'Settings saved.');
    }
}