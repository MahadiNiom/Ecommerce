<form method="POST" action="{{ route('profile.update') }}" class="mt-6">
    @csrf
    @method('patch')

    <div class="field">
        <label for="name" class="label">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="input">
        @error('name', 'updateProfileInformation')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field">
        <label for="email" class="label">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="input">
        @error('email', 'updateProfileInformation')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex items-center justify-end gap-3">
        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
            <p class="text-xs text-stone-500">Your email address is unverified.</p>
        @endif
        <button type="submit" class="btn-primary">Save changes</button>
    </div>
</form>