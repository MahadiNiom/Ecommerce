<footer class="mt-auto border-t border-stone-200 bg-white">
    <div class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-lg shadow-soft">
                        <span aria-hidden="true">&#127807;</span>
                    </span>
                    <span class="font-display text-lg font-semibold tracking-tight text-stone-900">
                        Matir <span class="text-emerald-700">Shaad</span>
                    </span>
                </a>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-stone-500">
                    Fresh from the earth to your doorstep. Hand-picked goods grown with care and delivered with a smile.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-stone-400">Shop</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('shop.index') }}" class="link">All products</a></li>
                    <li><a href="{{ route('cart.index') }}" class="link">Your cart</a></li>
                    <li><a href="{{ route('wishlist.index') }}" class="link">Wishlist</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-stone-400">Account</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @auth
                        <li><a href="{{ route('orders.index') }}" class="link">My orders</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="link">Profile</a></li>
                        <li><a href="{{ route('dashboard') }}" class="link">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="link">Log in</a></li>
                        <li><a href="{{ route('register') }}" class="link">Create account</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-stone-400">About</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @if (\App\Support\Permissions::canAny(auth()->user(), \App\Support\Permissions::products()))
                        <li><a href="{{ route('products.index') }}" class="link">Manage products</a></li>
                    @endif
                    @if (\App\Support\Permissions::canAny(auth()->user(), \App\Support\Permissions::orders()))
                        <li><a href="{{ route('admin.orders.index') }}" class="link">Manage orders</a></li>
                    @endif
                    @if (\App\Support\Permissions::canAny(auth()->user(), \App\Support\Permissions::users()))
                        <li><a href="{{ route('users.index') }}" class="link">Manage users</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-stone-100 pt-6 text-xs text-stone-400 sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Matir Shaad') }}. All rights reserved.</p>
            <p>Grown with care &#127793;</p>
        </div>
    </div>
</footer>