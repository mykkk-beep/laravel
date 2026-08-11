<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentNotification extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function replies()
    {
        return $this->hasMany(NotificationReply::class, 'notification_id')->orderBy('created_at');
    }

    public function latestReply()
    {
        return $this->hasOne(NotificationReply::class, 'notification_id')->latestOfMany();
    }

    public function addReply(string $sender, string $message): NotificationReply
    {
        $message = trim($message);

        if ($message === '') {
            throw new \InvalidArgumentException('Reply message cannot be empty.');
        }

        return $this->replies()->create([
            'sender' => $sender,
            'message' => $message,
        ]);
    }

    protected $fillable = [
        'student_id',
        'teacher_id',
        'title',
        'message',
        'recipient',
    ];
}
