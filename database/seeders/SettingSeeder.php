<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'company_name' => 'EMAB Electrical',
            'phone' => '+254 715 813 119',
            'secondary_phone' => '+254 715 813 119',
            'emergency_phone' => '+254 715 813 119',
            'whatsapp' => '254715813119',
            'email' => 'mabukeraz@gmail.com',
            'quotes_email' => 'emab_electrical@gmail.com',
            'address' => config('emab.base'),
            'po_box' => 'P.O. Box 000-00000',
            'working_hours' => 'We are on 24/7 service, feel free to contact us anytime.',
            'facebook' => 'emab_electrical',
            'instagram' => 'emab_electrical',
            'x' => 'emab_electrical',
            'linkedin' => '#',
            'map_embed_url' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
