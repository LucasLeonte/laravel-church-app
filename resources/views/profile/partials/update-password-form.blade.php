<section class="form-card" style="margin: 0; max-width: 100%;">
    <header style="margin-bottom: 2rem;">
        <h2 class="text-lg font-medium text-gray-900" style="color: var(--primary-dark); font-size: 1.5rem; margin-bottom: 0.5rem; border-bottom: none;">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600" style="color: var(--text-muted);">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="form-group">
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" style="margin-bottom: 0.5rem;" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" style="color: #ef4444; font-size: 0.875rem;" />
        </div>

        <div class="form-group">
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" style="margin-bottom: 0.5rem;" />
            <x-input-error :messages="$errors->updatePassword->get('password')" style="color: #ef4444; font-size: 0.875rem;" />
        </div>

        <div class="form-group">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" style="margin-bottom: 0.5rem;" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" style="color: #ef4444; font-size: 0.875rem;" />
        </div>

        <div class="flex items-center gap-4" style="display: flex; align-items: center; gap: 1rem; margin-top: 2rem;">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    style="font-size: 0.875rem; color: var(--text-muted);"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
