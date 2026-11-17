<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'uri',
        'query_string',
        'method',
        'request_data',
        'ip',
        'user_agent',
        'user_id',
        'roles',
        'browser',
        'platform',
        'device',
        'is_ajax',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_data' => 'array',
            'roles' => 'array',
            'is_ajax' => 'boolean',
            'user_id' => 'integer',
        ];
    }

    /**
     * Associated user if authenticated.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
