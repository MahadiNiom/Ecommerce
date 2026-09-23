@extends('layouts.guest')

@section('title', 'Verify email - ' . config('app.name'))

@section('content')
    <div class="card overflow-hidden">
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 px-8 py-8 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-2xl backdrop-blur-sm">&#128231;</div>
            <h1 class="mt-4 font-display text-2xl font-bold text-white">Verify your email</h1>
        </div>

        <div class="px-8 py-8">
            @if (session('status') === 'verification-link-sent')
                <div class="mb-5 flex items-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white">&#10003;</span>
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <p class="text-sm leading-relaxed text-stone-600">
                Before proceeding, please check your email for a verification link. If you did not receive the email, you can request another below.
            </p>

            <form action="{{ route('verification.send') }}" method="POST" class="mt-6">
                @csrf
                <button type="submit" class="btn-primary w-full">Resend verification email</button>
            </form>
        </div>
    </div>
@endsection