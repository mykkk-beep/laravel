<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationReply extends Model
{
    use HasFactory;

    public const SENDER_TEACHER = 'teacher';
    public const SENDER_PARENT = 'parent';

    protected $fillable = [
        'notification_id',
        'sender',
        'message',
    ];

    public function notification()
    {
        return $this->belongsTo(StudentNotification::class, 'notification_id');
    }
}
