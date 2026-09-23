@extends('layouts.guest')

@section('content')
    <h1>Verify your email</h1>

    @if (session('status') === 'verification-link-sent')
        <p>A new verification link has been sent to the email address you provided during registration.</p>
    @endif

    <p>Before proceeding, please check your email for a verification link. If you did not receive the email, you can request another.</p>

    <form action="{{ route('verification.send') }}" method="POST">
        @csrf
        <button type="submit">Resend Verification Email</button>
    </form>
@endsection