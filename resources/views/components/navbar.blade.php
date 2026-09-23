<nav>
    <a href="{{ route('shop.index') }}">Shop</a>
    @auth
        <a href="{{ route('dashboard') }}">Dashboard</a>
        @if (auth()->user()->can('manage products'))
            <a href="{{ route('products.index') }}">Products</a>
            <a href="{{ route('categories.index') }}">Categories</a>
            <a href="{{ route('brands.index') }}">Brands</a>
            <a href="{{ route('tags.index') }}">Tags</a>
        @endif
        <a href="{{ route('cart.index') }}">Cart ({{ auth()->user()->cart?->items()->count() ?? 0 }})</a>
        <a href="{{ route('wishlist.index') }}">Wishlist ({{ auth()->user()->wishlistItems()->count() }})</a>
        <a href="{{ route('orders.index') }}">Orders</a>
        @if (auth()->user()->can('manage orders'))
            <a href="{{ route('admin.orders.index') }}">Manage Orders</a>
        @endif
        <a href="{{ route('profile.edit') }}">Profile</a>
        @if (auth()->user()->can('manage roles and permissions'))
            <a href="{{ route('roles.index') }}">Roles</a>
            <a href="{{ route('permissions.index') }}">Permissions</a>
            <a href="{{ route('users.index') }}">Users</a>
        @endif
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @else
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Register</a>
    @endauth
</nav>