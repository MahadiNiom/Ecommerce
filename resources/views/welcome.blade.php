@extends('layouts.guest')

@section('content')
    {{-- Hero --}}
    <section class="relative -mx-4 overflow-hidden rounded-b-3xl bg-hero px-4 pb-20 pt-14 text-center sm:-mx-6 sm:px-6 sm:pt-20 lg:-mx-8 lg:px-8">
        <span class="absolute left-6 top-10 animate-float text-3xl opacity-40" aria-hidden="true">&#127806;</span>
        <span class="absolute right-10 top-16 animate-float text-4xl opacity-40 [animation-delay:1.2s]" aria-hidden="true">&#127793;</span>
        <span class="absolute bottom-8 left-1/3 animate-float text-2xl opacity-30 [animation-delay:2s]" aria-hidden="true">&#127805;</span>

        <p class="animate-fade-in-up text-sm font-semibold uppercase tracking-widest text-emerald-700">
            Welcome to {{ config('app.name', 'Matir Shaad') }}
        </p>
        <h1 class="mx-auto mt-4 max-w-2xl font-display text-4xl font-bold leading-tight tracking-tight text-stone-900 sm:text-5xl lg:text-6xl animate-fade-in-up [animation-delay:0.1s]">
            Fresh from the earth, <span class="text-gradient">delivered</span> to your door
        </h1>
        <p class="mx-auto mt-5 max-w-xl text-base text-stone-600 sm:text-lg animate-fade-in-up [animation-delay:0.2s]">
            Discover hand-picked goods selected at their peak, so every order arrives exactly as nature intended.
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row animate-fade-in-up [animation-delay:0.3s]">
            <a href="{{ route('shop.index') }}" class="btn-primary w-full sm:w-auto">
                Explore the shop
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-outline w-full sm:w-auto">Go to dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn-outline w-full sm:w-auto">Create an account</a>
            @endauth
        </div>
    </section>

    {{-- Feature cards --}}
    <section class="stagger mt-10 grid gap-6 sm:grid-cols-3">
        <div class="card-hover p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-2xl">&#127807;</div>
            <h3 class="mt-4 text-lg font-bold text-stone-900">Grown with care</h3>
            <p class="mt-2 text-sm leading-relaxed text-stone-500">
                Small-batch goodness sourced from farmers who pour their heart into every harvest.
            </p>
        </div>
        <div class="card-hover p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-2xl">&#128666;</div>
            <h3 class="mt-4 text-lg font-bold text-stone-900">Fast &amp; fresh delivery</h3>
            <p class="mt-2 text-sm leading-relaxed text-stone-500">
                Speedy shipping that keeps your order as fresh as the day it was picked.
            </p>
        </div>
        <div class="card-hover p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-100 text-2xl">&#127793;</div>
            <h3 class="mt-4 text-lg font-bold text-stone-900">Guaranteed happiness</h3>
            <p class="mt-2 text-sm leading-relaxed text-stone-500">
                Not perfectly satisfied? We make it right — no questions asked, ever.
            </p>
        </div>
    </section>

    {{-- CTA band --}}
    <section class="mt-12 overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600 px-6 py-12 text-center shadow-lift sm:px-12">
        <h2 class="font-display text-2xl font-bold text-white sm:text-3xl">Taste the difference today</h2>
        <p class="mx-auto mt-3 max-w-lg text-sm text-emerald-50 sm:text-base">
            Browse our seasonal selection and see why customers keep coming back for more.
        </p>
        <a href="{{ route('shop.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-emerald-800 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-lift">
            Start shopping
        </a>
    </section>
@endsection