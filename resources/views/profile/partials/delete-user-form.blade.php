<form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account?');">
    @csrf
    @method('delete')

    <p>Once your account is deleted, all of its resources and data will be permanently deleted.</p>

    <div>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Confirm your password">
        @error('password', 'userDeletion')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">Delete Account</button>
</form>