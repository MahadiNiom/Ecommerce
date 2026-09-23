@extends('layouts.guest')

@section('content')
    <h1>Login</h1>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="remember_me">
                <input id="remember_me" type="checkbox" name="remember"> Remember me
            </label>
        </div>

        <div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot your password?</a>
            @endif

            <button type="submit">Log in</button>
        </div>
    </form>

    <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
@endsection