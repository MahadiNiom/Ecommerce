<nav>
    @auth
        <a href="{{ route('dashboard') }}">Dashboard</a>
        @if (auth()->user()->can('manage products'))
            <a href="{{ route('products.index') }}">Products</a>
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