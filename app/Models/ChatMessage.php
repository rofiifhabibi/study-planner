<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    // Properti yang diizinkan untuk diisi massal
    protected $fillable = [
        'chat_session_id',
        'role',
        'content',
        'file_url',
    ];

    // Relasi ke ChatSession
    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }
}
