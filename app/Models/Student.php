<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ENROLLED = 'enrolled';
    public const STATUS_NOT_ENROLLED = 'not_enrolled';

    protected $fillable = [
        'student_id',
        'name',
        'sex',
        'mobile',
        'email',
        'class_room_id',
        'uuid',
        'status',
        'profile_picture',
    ];

    protected static function booted()
    {
        static::creating(function (Student $student) {
            if (empty($student->uuid)) {
                $student->uuid = (string) Str::uuid();
            }
        });
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function matchingStudentIds(): Collection
    {
        if (empty($this->student_id)) {
            return collect([$this->id])->filter()->values();
        }

        return self::query()
            ->where('student_id', $this->student_id)
            ->where('name', $this->name)
            ->pluck('id')
            ->push($this->id)
            ->unique()
            ->values();
    }

    public function currentEnrollmentForClass(?int $classRoomId): ?Enrollment
    {
        if (empty($classRoomId)) {
            return null;
        }

        return $this->enrollments()->where('class_room_id', $classRoomId)->first();
    }

    public function totalClassCount(): int
    {
        $studentIds = $this->matchingStudentIds();
        $classIds = self::query()
            ->whereIn('id', $studentIds)
            ->pluck('class_room_id')
            ->merge(Enrollment::query()->whereIn('student_id', $studentIds)->pluck('class_room_id'))
            ->filter()
            ->unique()
            ->values();

        return $classIds->count();
    }

}
