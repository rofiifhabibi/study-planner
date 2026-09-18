<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'session_key',
        'is_project',
        'parent_id',
    ];

    protected $casts = [
        'is_project' => 'boolean',
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_session_id');
    }

    public function parent()
    {
        return $this->belongsTo(ChatSession::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChatSession::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
