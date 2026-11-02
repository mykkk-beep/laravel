<?php

namespace App\Providers;

use App\Models\AttendanceRecord;
use App\Models\StudentNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (! Auth::check()) {
                return;
            }

            $user = Auth::user();
            if ($user->role === User::ROLE_SUPERADMIN) {
                $pendingTeacherCount = User::where('role', User::ROLE_TEACHER)
                    ->where('status', User::STATUS_PENDING)
                    ->count();

                $view->with('pendingTeacherCount', $pendingTeacherCount);

                return;
            }

            if ($user->role !== User::ROLE_TEACHER) {
                return;
            }

            $absentNotificationCount = AttendanceRecord::whereHas('classRoom', function ($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->where('status', 'absent')
                ->whereDate('date', '>=', now()->subDays(7)->toDateString())
                ->distinct('student_id')
                ->count('student_id');

            $pendingParentMessageCount = StudentNotification::where('teacher_id', $user->id)
                ->whereHas('latestReply', function ($query) {
                    $query->where('sender', 'parent');
                })
                ->count();

            $pendingRecommendationReplyCount = StudentNotification::where('teacher_id', $user->id)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('recipient', 'guardian')
                            ->where('title', 'like', 'Recommendation for %');
                    })->orWhere(function ($query) {
                        $query->where('recipient', 'student')
                            ->where('title', 'Student Recommendation');
                    });
                })
                ->whereHas('latestReply', function ($query) {
                    $query->where('sender', 'parent');
                })
                ->count();

            $view->with('absentNotificationCount', $absentNotificationCount)
                ->with('pendingParentMessageCount', $pendingParentMessageCount)
                ->with('pendingRecommendationReplyCount', $pendingRecommendationReplyCount);
        });
    }
}
