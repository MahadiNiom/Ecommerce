@extends('layouts.app')

@section('content')
    <h1>Profile</h1>

    @if (session('status') === 'profile-updated')
        <p>Your profile information has been saved.</p>
    @endif

    <h2>Profile Information</h2>
    @include('profile.partials.update-profile-information-form')

    <h2>Update Password</h2>
    @include('profile.partials.update-password-form')

    <h2>Delete Account</h2>
    @include('profile.partials.delete-user-form')
@endsection