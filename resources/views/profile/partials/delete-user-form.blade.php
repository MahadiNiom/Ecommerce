<form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account?');" class="mt-6">
    @csrf
    @method('delete')

    <p class="text-sm leading-relaxed text-stone-500">
        Once your account is deleted, all of its resources and data will be permanently deleted.
    </p>

    <div class="field mt-4">
        <label for="password" class="label">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Confirm your password" class="input">
        @error('password', 'userDeletion')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn-danger w-full">Delete account</button>
</form>