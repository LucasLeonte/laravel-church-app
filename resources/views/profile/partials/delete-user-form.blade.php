<section class="form-card" style="margin: 0; max-width: 100%; border: 1px solid #fecaca;">
    <header style="margin-bottom: 2rem;">
        <h2 class="text-lg font-medium text-gray-900" style="color: #b91c1c; font-size: 1.5rem; margin-bottom: 0.5rem; border-bottom: none;">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600" style="color: var(--text-muted);">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6" style="padding: 2rem;">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900" style="color: var(--primary-dark); font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600" style="color: var(--text-muted); margin-bottom: 1.5rem;">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6" style="margin-top: 1.5rem;">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                    style="width: 100%;"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" style="color: #ef4444; margin-top: 0.5rem;" />
            </div>

            <div class="mt-6 flex justify-end" style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
