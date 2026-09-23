<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div>
        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name', 'updateProfileInformation')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email', 'updateProfileInformation')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">Save</button>
</form>