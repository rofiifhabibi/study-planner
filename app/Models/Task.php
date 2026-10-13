<?php

namespace App\Models;

use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(TaskObserver::class)]
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'due_time',
        'status',
        'category',
        'priority',
        'pomodoro_duration',
        'break_duration',
        'google_task_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'pomodoro_duration' => 'integer',
        'break_duration' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(TaskStep::class)->orderBy('id');
    }

    public function studySessions(): HasMany
    {
        return $this->hasMany(StudySession::class);
    }
}
