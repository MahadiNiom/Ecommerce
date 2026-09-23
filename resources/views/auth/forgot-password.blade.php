@extends('layouts.guest')

@section('title', 'Forgot password - ' . config('app.name'))

@section('content')
    <div class="card overflow-hidden">
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 px-8 py-8 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-2xl backdrop-blur-sm">&#128274;</div>
            <h1 class="mt-4 font-display text-2xl font-bold text-white">Forgot your password?</h1>
            <p class="mt-1 text-sm text-amber-100">No worries — we'll email you a reset link</p>
        </div>

        @if (session('status'))
            <div class="mx-8 mt-6 flex items-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white">&#10003;</span>
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5 px-8 py-8">
            @csrf

            <div class="field">
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com" class="input">
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full">Email password reset link</button>
        </form>
    </div>

    <p class="mt-6 text-center">
        <a href="{{ route('login') }}" class="link inline-flex items-center gap-1 text-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to login
        </a>
    </p>
@endsection