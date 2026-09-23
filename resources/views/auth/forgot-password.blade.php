@extends('layouts.guest')

@section('content')
    <h1>Forgot your password?</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Email Password Reset Link</button>
    </form>
@endsection