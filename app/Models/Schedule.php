<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'subject',
        'date',
        'start_time',
        'end_time',
        'status',
        'color',
        'google_event_id',
        'recurrence_frequency',
        'recurrence_interval',
        'recurrence_days',
        'recurrence_until',
        'recurrence_count',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'recurrence_interval' => 'integer',
        'recurrence_until' => 'date',
        'recurrence_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
