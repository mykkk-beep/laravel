@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Edit Recommendation</h1>
        </div>
        <div>
            <a href="{{ route('teacher.recommendations') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">Back</a>
        </div>
    </div>

    <form method="POST" action="{{ route('teacher.recommendations.update', $notification->id) }}">
        @csrf
        @method('PUT')

        <div class="rounded-lg border border-slate-200 bg-white p-6 space-y-4">
            <label class="block text-sm">
                <span class="text-sm font-medium">Title</span>
                <input type="text" name="title" value="{{ old('title', $notification->title) }}" class="mt-1 w-full rounded border-gray-200" required>
            </label>

            <label class="block text-sm">
                <span class="text-sm font-medium">Message</span>
                <textarea name="message" rows="8" class="mt-1 w-full rounded border-gray-200">{{ old('message', $notification->message) }}</textarea>
            </label>

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-white">Save</button>
            </div>
        </div>
    </form>
</div>
@endsection
