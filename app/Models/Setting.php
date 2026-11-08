<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['group', 'key', 'value'])]
class Setting extends Model
{
    /**
     * Get the stored value of a setting, falling back to the config default.
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        $aliases = [
            'app_name' => 'site_title',
            'site_title' => 'app_name',
            'app_year' => 'year',
            'year' => 'app_year',
            'app_city' => 'city',
            'city' => 'app_city',
            'app_country' => 'country',
            'country' => 'app_country',
        ];

        $keys = [$key];
        if (isset($aliases[$key])) {
            $keys[] = $aliases[$key];
        }

        $value = static::query()->whereIn('key', $keys)->value('value');

        return $value ?? $default;
    }

    /**
     * Get all stored values of a group keyed by setting key.
     *
     * @return array<string, string|null>
     */
    public static function valuesForGroup(string $group): array
    {
        return static::query()->where('group', $group)->pluck('value', 'key')->all();
    }

    /**
     * Store a setting value.
     */
    public static function setValue(string $group, string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['group' => $group, 'value' => $value]
        );
    }
}
