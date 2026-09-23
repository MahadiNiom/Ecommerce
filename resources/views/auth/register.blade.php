@extends('layouts.guest')

@section('title', 'Register - ' . config('app.name'))

@section('content')
    <div class="card overflow-hidden">
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 px-8 py-8 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-2xl backdrop-blur-sm">&#127793;</div>
            <h1 class="mt-4 font-display text-2xl font-bold text-white">Create your account</h1>
            <p class="mt-1 text-sm text-emerald-100">Join {{ config('app.name') }} in a few seconds</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5 px-8 py-8">
            @csrf

            <div class="field">
                <label for="name" class="label">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Jane Doe" class="input">
                @error('name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@example.com" class="input">
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="password" class="label">Password</label>
                <div class="relative" x-data="{ show: false }">
                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="At least 8 characters" class="input pr-12">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-stone-400 transition-colors hover:text-emerald-600" aria-label="Toggle password visibility">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation" class="label">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" class="input">
                @error('password_confirmation')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full">
                Create account
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-stone-600">
        Already have an account?
        <a href="{{ route('login') }}" class="link">Log in</a>
    </p>
@endsection