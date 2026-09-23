@php
    $cartCount = auth()->user()?->cart?->items()->count() ?? 0;
    $wishlistCount = auth()->user()?->wishlistItems()->count() ?? 0;
    $user = auth()->user();
    $canViewProducts = \App\Support\Permissions::canAny($user, \App\Support\Permissions::products());
    $canViewCategories = \App\Support\Permissions::canAny($user, \App\Support\Permissions::categories());
    $canViewBrands = \App\Support\Permissions::canAny($user, \App\Support\Permissions::brands());
    $canViewTags = \App\Support\Permissions::canAny($user, \App\Support\Permissions::tags());
    $canViewOrders = \App\Support\Permissions::canAny($user, \App\Support\Permissions::orders());
    $canViewRoles = \App\Support\Permissions::canAny($user, \App\Support\Permissions::roles());
    $canViewPermissions = \App\Support\Permissions::canAny($user, \App\Support\Permissions::permissions());
    $canViewUsers = \App\Support\Permissions::canAny($user, \App\Support\Permissions::users());
    $showManage = $canViewProducts || $canViewCategories || $canViewBrands || $canViewTags || $canViewOrders || $canViewRoles || $canViewPermissions || $canViewUsers;
@endphp

<nav x-data="{ mobileOpen: false, manageOpen: false, userOpen: false }"
     class="sticky top-0 z-40 border-b border-stone-200/70 bg-white/80 backdrop-blur-lg">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2 transition-transform hover:scale-[1.02]">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-lg shadow-soft">
                <span aria-hidden="true">&#127807;</span>
            </span>
            <span class="font-display text-lg font-semibold tracking-tight text-stone-900">
                Matir <span class="text-emerald-700">Shaad</span>
            </span>
        </a>

        {{-- Desktop links --}}
        <div class="hidden items-center gap-1 lg:flex">
            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-600 transition-colors hover:bg-stone-100 hover:text-stone-900">Home</a>
            <a href="{{ route('shop.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-600 transition-colors hover:bg-stone-100 hover:text-stone-900">Shop</a>

            @auth
                <a href="{{ route('orders.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-600 transition-colors hover:bg-stone-100 hover:text-stone-900">Orders</a>

                @if ($showManage)
                    <div class="relative">
                        <button type="button" @click="manageOpen = !manageOpen" @click.outside="manageOpen = false"
                                class="flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium text-stone-600 transition-colors hover:bg-stone-100 hover:text-stone-900">
                            Manage
                            <svg class="h-4 w-4 transition-transform" :class="manageOpen && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                        <div x-show="manageOpen" x-transition:enter="transition origin-top duration-150 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition duration-100 ease-in" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0"
                             class="absolute right-0 mt-2 w-52 rounded-2xl border border-stone-200 bg-white p-1.5 shadow-lift">
                            @if ($canViewProducts || $canViewCategories || $canViewBrands || $canViewTags)
                                @if ($canViewProducts)
                                    <a href="{{ route('products.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Products</a>
                                @endif
                                @if ($canViewCategories)
                                    <a href="{{ route('categories.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Categories</a>
                                @endif
                                @if ($canViewBrands)
                                    <a href="{{ route('brands.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Brands</a>
                                @endif
                                @if ($canViewTags)
                                    <a href="{{ route('tags.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Tags</a>
                                @endif
                            @endif
                            @if ($canViewOrders)
                                <a href="{{ route('admin.orders.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Manage Orders</a>
                            @endif
                            @if ($canViewRoles || $canViewPermissions || $canViewUsers)
                                <div class="my-1 border-t border-stone-100"></div>
                                @if ($canViewRoles)
                                    <a href="{{ route('roles.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Roles</a>
                                @endif
                                @if ($canViewPermissions)
                                    <a href="{{ route('permissions.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Permissions</a>
                                @endif
                                @if ($canViewUsers)
                                    <a href="{{ route('users.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Users</a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            @endauth
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-2">
            @auth
                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="relative hidden p-2 text-stone-600 transition-colors hover:text-emerald-700 sm:inline-flex" title="Cart">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-600 px-1 text-[11px] font-bold text-white">{{ $cartCount }}</span>
                </a>

                {{-- Wishlist --}}
                <a href="{{ route('wishlist.index') }}" class="relative hidden p-2 text-stone-600 transition-colors hover:text-red-500 sm:inline-flex" title="Wishlist">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] font-bold text-white">{{ $wishlistCount }}</span>
                </a>

                {{-- User menu --}}
                <div class="relative">
                    <button type="button" @click="userOpen = !userOpen" @click.outside="userOpen = false"
                            class="flex items-center gap-2 rounded-full border border-stone-200 bg-white py-1 pl-1 pr-3 transition-all hover:border-emerald-300 hover:shadow-soft">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-700 text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="hidden text-sm font-medium text-stone-700 sm:block">{{ auth()->user()->name }}</span>
                        <svg class="h-4 w-4 text-stone-400 transition-transform" :class="userOpen && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-show="userOpen" x-transition:enter="transition origin-top-right duration-150 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition duration-100 ease-in" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0"
                         class="absolute right-0 mt-2 w-48 rounded-2xl border border-stone-200 bg-white p-1.5 shadow-lift">
                        <a href="{{ route('dashboard') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Dashboard</a>
                        <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Profile</a>
                        <a href="{{ route('cart.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800 sm:hidden">Cart ({{ $cartCount }})</a>
                        <a href="{{ route('wishlist.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800 sm:hidden">Wishlist ({{ $wishlistCount }})</a>
                        <div class="my-1 border-t border-stone-100"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-xl px-3 py-2 text-left text-sm text-red-600 transition-colors hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-ghost hidden sm:inline-flex">Log in</a>
                <a href="{{ route('register') }}" class="btn-primary hidden sm:inline-flex">Sign up</a>
            @endauth

            {{-- Mobile hamburger --}}
            <button type="button" @click="mobileOpen = !mobileOpen"
                    class="inline-flex items-center justify-center rounded-xl border border-stone-200 bg-white p-2 text-stone-600 transition-colors hover:text-emerald-700 lg:hidden"
                    aria-label="Toggle menu">
                <svg x-show="!mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                <svg x-show="mobileOpen" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen" x-transition:enter="transition origin-top duration-200 ease-out" x-transition:enter-start="scale-y-95 opacity-0" x-transition:enter-end="scale-y-100 opacity-100" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         x-cloak class="border-t border-stone-200 bg-white px-4 py-4 lg:hidden">
        <div class="space-y-1">
            <a href="{{ route('home') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-stone-700 transition-colors hover:bg-stone-100">Home</a>
            <a href="{{ route('shop.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-stone-700 transition-colors hover:bg-stone-100">Shop</a>
            @auth
                <a href="{{ route('orders.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-stone-700 transition-colors hover:bg-stone-100">Orders</a>
                @if ($canViewProducts || $canViewCategories || $canViewBrands || $canViewTags)
                    <div class="my-1 border-t border-stone-100"></div>
                    <p class="px-3 pb-1 pt-2 text-xs font-semibold uppercase tracking-wide text-stone-400">Manage</p>
                    @if ($canViewProducts)
                        <a href="{{ route('products.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-600 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Products</a>
                    @endif
                    @if ($canViewCategories)
                        <a href="{{ route('categories.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-600 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Categories</a>
                    @endif
                    @if ($canViewBrands)
                        <a href="{{ route('brands.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-600 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Brands</a>
                    @endif
                    @if ($canViewTags)
                        <a href="{{ route('tags.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-600 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Tags</a>
                    @endif
                @endif
                @if ($canViewOrders)
                    <a href="{{ route('admin.orders.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-600 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Manage Orders</a>
                @endif
                @if ($canViewRoles || $canViewPermissions || $canViewUsers)
                    @if ($canViewRoles)
                        <a href="{{ route('roles.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-600 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Roles</a>
                    @endif
                    @if ($canViewPermissions)
                        <a href="{{ route('permissions.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-600 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Permissions</a>
                    @endif
                    @if ($canViewUsers)
                        <a href="{{ route('users.index') }}" class="block rounded-xl px-3 py-2 text-sm text-stone-600 transition-colors hover:bg-emerald-50 hover:text-emerald-800">Users</a>
                    @endif
                @endif
                <div class="my-1 border-t border-stone-100"></div>
                <a href="{{ route('dashboard') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-stone-700 transition-colors hover:bg-stone-100">Dashboard</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-stone-700 transition-colors hover:bg-stone-100">Profile</a>
                <form action="{{ route('logout') }}" method="POST" class="px-3 pt-2">
                    @csrf
                    <button type="submit" class="btn-danger btn-sm w-full">Logout</button>
                </form>
            @else
                <div class="mt-3 flex gap-3">
                    <a href="{{ route('login') }}" class="btn-outline flex-1">Log in</a>
                    <a href="{{ route('register') }}" class="btn-primary flex-1">Sign up</a>
                </div>
            @endauth
        </div>
    </div>
</nav>