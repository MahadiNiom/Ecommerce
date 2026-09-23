@extends('layouts.app')

@section('title', 'Profile - ' . config('app.name'))

@section('content')
    <div class="animate-fade-in-up">
        <h1 class="page-title">Profile</h1>
        <p class="page-subtitle">Manage your account information, password, and preferences.</p>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="card mt-6 flex items-center gap-2 border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 animate-fade-in-up">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white">&#10003;</span>
            Your profile information has been saved.
        </div>
    @endif

    <div class="mt-8 grid gap-8 lg:grid-cols-3">
        <div class="card p-6 animate-fade-in-up sm:p-8 lg:col-span-2">
            <h2 class="flex items-center gap-2 text-lg font-bold text-stone-900">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </span>
                Profile information
            </h2>
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card p-6 animate-fade-in-up [animation-delay:0.1s] sm:p-8 lg:col-span-2">
            <h2 class="flex items-center gap-2 text-lg font-bold text-stone-900">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </span>
                Update password
            </h2>
            @include('profile.partials.update-password-form')
        </div>

        <div class="lg:row-span-2">
            <div class="card border-red-200 p-6 animate-fade-in-up [animation-delay:0.2s] sm:p-8">
                <h2 class="flex items-center gap-2 text-lg font-bold text-red-700">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-red-100 text-red-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </span>
                    Delete account
                </h2>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection