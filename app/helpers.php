<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        static $settings = null;

        $settings ??= Setting::pluck('value', 'key');

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('tel')) {
    function tel(string $key): string
    {
        $phone = (string) setting($key, '');

        return 'tel:'.preg_replace('/[^\d+]/', '', $phone);
    }
if (! function_exists('friendly_date')) {
    function friendly_date($date): string
    {
        if (! $date) {
            return '';
        }

        $date = \Illuminate\Support\Carbon::parse($date);

        if ($date->isToday()) {
            return 'Today, ' . $date->format('h:i A');
        }

        if ($date->isYesterday()) {
            return 'Yesterday, ' . $date->format('h:i A');
        }

        return $date->format('d M Y');
    }
}

    }
