<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    /**
     * Get a single setting value, e.g. Setting::get('company', 'name', 'My Company').
     */
    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        $all = self::group($group);
        return $all[$key] ?? $default;
    }

    /**
     * Get every key/value pair for a group as an associative array, cached.
     */
    public static function group(string $group): array
    {
        return Cache::remember("settings.$group", 3600, function () use ($group) {
            return self::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    public static function set(string $group, string $key, mixed $value): void
    {
        self::updateOrCreate(['group' => $group, 'key' => $key], ['value' => $value]);
        Cache::forget("settings.$group");
    }
}
