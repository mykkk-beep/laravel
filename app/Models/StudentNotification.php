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

    public function appendReply(string $side, string $reply): void
    {
        $column = $side === 'parent' ? 'parent_reply' : 'teacher_reply';
        $newReply = trim($reply);

        if ($newReply === '') {
            return;
        }

        $existingReply = trim((string) $this->getAttribute($column));
        $combinedReply = $existingReply === '' ? $newReply : $existingReply."\n\n".$newReply;

        $this->forceFill([$column => $combinedReply])->save();
    }

    protected $fillable = [
        'student_id',
        'teacher_id',
        'title',
        'message',
        'parent_reply',
        'teacher_reply',
    ];
}
