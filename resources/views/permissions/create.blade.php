@extends('base')

@section('title', 'Create Permission - ' . config('app.name'))

@section('content')
    <a href="{{ route('permissions.index') }}" class="link inline-flex items-center gap-1 text-sm animate-fade-in-up">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to permissions
    </a>

    <div class="mt-6 max-w-xl animate-fade-in-up">
        <h1 class="page-title">Create permission</h1>
        <div class="card mt-6 p-6 sm:p-8">
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf
                <div class="field">
                    <label for="name" class="label">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required class="input" placeholder="manage reports">
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('permissions.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Create permission</button>
                </div>
            </form>
        </div>
    </div>
@endsection