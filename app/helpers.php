<?php
if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        static $cache = [];
        if (!isset($cache[$key])) {
            $s = \App\Models\Setting::where('key', $key)->first();
            $cache[$key] = $s ? $s->value : $default;
        }
        return $cache[$key];
    }
}
