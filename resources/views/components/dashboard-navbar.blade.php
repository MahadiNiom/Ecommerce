<nav>
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('shop.index') }}">Shop</a>
    @if (auth()->user()->can('manage products'))
        <a href="{{ route('products.index') }}">Products</a>
        <a href="{{ route('categories.index') }}">Categories</a>
        <a href="{{ route('brands.index') }}">Brands</a>
        <a href="{{ route('tags.index') }}">Tags</a>
    @endif
    @if (auth()->user()->can('manage orders'))
        <a href="{{ route('admin.orders.index') }}">Manage Orders</a>
    @endif
    @if (auth()->user()->can('manage roles and permissions'))
        <a href="{{ route('roles.index') }}">Roles</a>
        <a href="{{ route('permissions.index') }}">Permissions</a>
        <a href="{{ route('users.index') }}">Users</a>
    @endif
    <a href="{{ route('orders.index') }}">Orders</a>
    <a href="{{ route('cart.index') }}">Cart ({{ auth()->user()->cart?->items()->count() ?? 0 }})</a>
    <a href="{{ route('wishlist.index') }}">Wishlist ({{ auth()->user()->wishlistItems()->count() }})</a>
    <a href="{{ route('profile.edit') }}">Profile</a>
    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit">Logout</button>
    </form>
</nav>