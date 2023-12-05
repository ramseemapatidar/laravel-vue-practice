<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

function setting($key)
{
    $settings = Cache::rememberForever('settings', function () {
        return Setting::pluck('value', 'key')->all();
    });

    if (! $settings) {
        $settings = config('constants.defualt_setting');

    }

    return $settings[$key] ?? false;
}
