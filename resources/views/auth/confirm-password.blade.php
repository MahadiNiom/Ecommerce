@extends('layouts.guest')

@section('content')
    <h1>Confirm password</h1>

    <p>This is a secure area of the application. Please confirm your password before continuing.</p>

    <form action="{{ route('password.confirm') }}" method="POST">
        @csrf

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">
        @error('password')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Confirm</button>
    </form>
@endsection