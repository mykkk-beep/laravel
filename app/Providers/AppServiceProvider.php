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
            if (! Auth::check() || Auth::user()->role !== User::ROLE_TEACHER) {
                return;
            }

            $teacher = Auth::user();
            $absentNotificationCount = AttendanceRecord::whereHas('classRoom', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->where('status', 'absent')
                ->whereDate('date', '>=', now()->subDays(7)->toDateString())
                ->distinct('student_id')
                ->count('student_id');

            $pendingParentMessageCount = StudentNotification::where('teacher_id', $teacher->id)
                ->whereHas('latestReply', function ($query) {
                    $query->where('sender', 'parent');
                })
                ->count();

            $view->with('absentNotificationCount', $absentNotificationCount)
                ->with('pendingParentMessageCount', $pendingParentMessageCount);
        });
    }
}
