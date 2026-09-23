<form method="POST" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div>
        <label for="update_password_current_password">Current Password</label>
        <input id="update_password_current_password" type="password" name="current_password" required autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="update_password_password">New Password</label>
        <input id="update_password_password" type="password" name="password" required autocomplete="new-password">
        @error('password', 'updatePassword')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="update_password_password_confirmation">Confirm Password</label>
        <input id="update_password_password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">Save</button>
</form>