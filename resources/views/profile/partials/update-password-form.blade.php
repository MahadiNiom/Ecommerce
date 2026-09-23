<form method="POST" action="{{ route('password.update') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
    @csrf
    @method('put')

    <div class="field sm:col-span-2">
        <label for="update_password_current_password" class="label">Current password</label>
        <input id="update_password_current_password" type="password" name="current_password" required autocomplete="current-password" class="input">
        @error('current_password', 'updatePassword')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field">
        <label for="update_password_password" class="label">New password</label>
        <input id="update_password_password" type="password" name="password" required autocomplete="new-password" class="input">
        @error('password', 'updatePassword')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field">
        <label for="update_password_password_confirmation" class="label">Confirm new password</label>
        <input id="update_password_password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input">
        @error('password_confirmation', 'updatePassword')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="sm:col-span-2 sm:text-right">
        <button type="submit" class="btn-primary">Save password</button>
    </div>
</form>