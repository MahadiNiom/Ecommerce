@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@section('content')
    <div class="animate-fade-in-up">
        <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">Dashboard</p>
        <h1 class="page-title">Welcome back, {{ $user->name }}</h1>
        <div class="mt-2 flex flex-wrap items-center gap-2">
            @foreach ($roles as $role)
                <span class="badge-green">{{ $role }}</span>
            @endforeach
        </div>
    </div>

    {{-- Stats --}}
    <div class="stagger mt-8 grid gap-6 sm:grid-cols-3">
        <div class="card-hover relative overflow-hidden p-6">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-emerald-100"></div>
            <div class="relative">
                <p class="text-sm text-stone-500">Products</p>
                <p class="mt-1 text-4xl font-bold text-emerald-700">{{ $productsCount }}</p>
            </div>
        </div>
        <div class="card-hover relative overflow-hidden p-6">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-teal-100"></div>
            <div class="relative">
                <p class="text-sm text-stone-500">Variants</p>
                <p class="mt-1 text-4xl font-bold text-teal-700">{{ $variantsCount }}</p>
            </div>
        </div>
        <div class="card-hover relative overflow-hidden p-6">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-amber-100"></div>
            <div class="relative">
                <p class="text-sm text-stone-500">Product variants</p>
                <p class="mt-1 text-4xl font-bold text-amber-600">{{ $productVariantsCount }}</p>
            </div>
        </div>
    </div>

    {{-- Quick links --}}
    @if (\App\Support\Permissions::canAny(auth()->user(), \App\Support\Permissions::catalog()))
        <div class="card mt-8 p-6 animate-fade-in-up [animation-delay:0.3s]">
            <h2 class="text-lg font-bold text-stone-900">Manage your store</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @if (\App\Support\Permissions::canAny(auth()->user(), \App\Support\Permissions::products()))
                    <a href="{{ route('products.index') }}" class="btn-outline justify-start">
                        <span class="text-lg">&#128230;</span> Products
                    </a>
                @endif
                @if (\App\Support\Permissions::canAny(auth()->user(), \App\Support\Permissions::categories()))
                    <a href="{{ route('categories.index') }}" class="btn-outline justify-start">
                        <span class="text-lg">&#128193;</span> Categories
                    </a>
                @endif
                @if (\App\Support\Permissions::canAny(auth()->user(), \App\Support\Permissions::brands()))
                    <a href="{{ route('brands.index') }}" class="btn-outline justify-start">
                        <span class="text-lg">&#127991;</span> Brands
                    </a>
                @endif
                @if (\App\Support\Permissions::canAny(auth()->user(), \App\Support\Permissions::tags()))
                    <a href="{{ route('tags.index') }}" class="btn-outline justify-start">
                        <span class="text-lg">&#127991;</span> Tags
                    </a>
                @endif
            </div>
        </div>
    @endif

    <div class="card mt-8 p-6 animate-fade-in-up [animation-delay:0.35s]">
        <h2 class="text-lg font-bold text-stone-900">Your activity</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('shop.index') }}" class="btn-outline justify-start">
                <span class="text-lg">&#127806;</span> Browse the shop
            </a>
            <a href="{{ route('orders.index') }}" class="btn-outline justify-start">
                <span class="text-lg">&#128230;</span> My orders
            </a>
            <a href="{{ route('wishlist.index') }}" class="btn-outline justify-start">
                <span class="text-lg">&#129505;</span> Wishlist
            </a>
            <a href="{{ route('profile.edit') }}" class="btn-outline justify-start">
                <span class="text-lg">&#128100;</span> Profile
            </a>
        </div>
    </div>
@endsection