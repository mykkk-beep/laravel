<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-[55px] w-[55px] items-center justify-center rounded-xl bg-green-600">
            <svg width="38" height="38" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path fill="white" d="M4 4h14v14H4zM30 4h14v14H30zM4 30h14v14H4z"/>
                <path fill="#16a34a" d="M7 7h8v8H7zM33 7h8v8h-8zM7 33h8v8H7z"/>
                <path fill="white" d="M9 9h4v4H9zM35 9h4v4h-4zM9 35h4v4H9z"/>
                <path fill="white" d="M21 4h5v5h-5zM21 12h5v5h-5zM30 21h5v5h-5zM38 21h6v5h-6zM21 30h5v5h-5zM30 38h5v6h-5zM38 30h6v5h-6zM21 39h5v5h-5z"/>
                <circle cx="25" cy="25" r="8" fill="#15803d" stroke="white" stroke-width="2"/>
                <path d="m21.5 25 2.3 2.3 4.7-5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('Create your account') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('Join the QR-based attendance system') }}</p>
    </div>

    <a href="{{ url('/') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 rounded-md">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.56l3.22 3.22a.75.75 0 11-1.06 1.06l-4.5-4.5a.75.75 0 010-1.06l4.5-4.5a.75.75 0 111.06 1.06l-3.22 3.22h10.69A.75.75 0 0117 10z" clip-rule="evenodd" />
        </svg>
        {{ __('Back') }}
    </a>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="mt-1 flex w-full items-stretch overflow-hidden rounded-2xl border border-slate-300 bg-white/95 transition focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-100/80" x-data="{ visible: false }">
                <x-text-input id="password" class="min-w-0 flex-1 rounded-none border-0 bg-transparent shadow-none focus:border-0 focus:ring-0"
                                x-bind:type="visible ? 'text' : 'password'"
                                name="password"
                                required autocomplete="new-password" />
                <button type="button" class="inline-flex shrink-0 items-center justify-center border-l border-slate-200 px-3 text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-500"
                        x-on:click="visible = !visible"
                        x-bind:aria-label="visible ? 'Hide password' : 'Show password'"
                        x-bind:title="visible ? 'Hide password' : 'Show password'">
                    <svg x-show="!visible" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6.25 9.75-6.25S21.75 12 21.75 12 18.25 18.25 12 18.25 2.25 12 2.25 12Z" />
                        <circle cx="12" cy="12" r="2.5" />
                    </svg>
                    <svg x-show="visible" x-cloak class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8m2.1 2.1A9.8 9.8 0 0 1 12 16.5c-6.25 0-9.75-4.5-9.75-4.5a15.4 15.4 0 0 1 4.1-3.9m3.05-1.25A9.8 9.8 0 0 1 12 6.5c6.25 0 9.75 5.5 9.75 5.5a15.4 15.4 0 0 1-3.2 3.4" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="mt-1 flex w-full items-stretch overflow-hidden rounded-2xl border border-slate-300 bg-white/95 transition focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-100/80" x-data="{ visible: false }">
                <x-text-input id="password_confirmation" class="min-w-0 flex-1 rounded-none border-0 bg-transparent shadow-none focus:border-0 focus:ring-0"
                                x-bind:type="visible ? 'text' : 'password'"
                                name="password_confirmation" required autocomplete="new-password" />
                <button type="button" class="inline-flex shrink-0 items-center justify-center border-l border-slate-200 px-3 text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-500"
                        x-on:click="visible = !visible"
                        x-bind:aria-label="visible ? 'Hide password' : 'Show password'"
                        x-bind:title="visible ? 'Hide password' : 'Show password'">
                    <svg x-show="!visible" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6.25 9.75-6.25S21.75 12 21.75 12 18.25 18.25 12 18.25 2.25 12 2.25 12Z" />
                        <circle cx="12" cy="12" r="2.5" />
                    </svg>
                    <svg x-show="visible" x-cloak class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8m2.1 2.1A9.8 9.8 0 0 1 12 16.5c-6.25 0-9.75-4.5-9.75-4.5a15.4 15.4 0 0 1 4.1-3.9m3.05-1.25A9.8 9.8 0 0 1 12 6.5c6.25 0 9.75 5.5 9.75 5.5a15.4 15.4 0 0 1-3.2 3.4" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
