<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolTimetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day_of_week',
        'subject',
        'start_time',
        'end_time',
        'google_event_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
