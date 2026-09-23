@extends('layouts.guest')

@section('content')
    <h1>{{ config('app.name', 'Laravel') }}</h1>

    <p>Welcome to {{ config('app.name', 'Laravel') }}.</p>

    @auth
        <p><a href="{{ route('dashboard') }}">Go to dashboard</a></p>
    @else
        <p><a href="{{ route('login') }}">Log in</a> or <a href="{{ route('register') }}">create an account</a>.</p>
    @endauth
@endsection