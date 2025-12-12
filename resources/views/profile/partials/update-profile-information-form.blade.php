@php use Illuminate\Contracts\Auth\MustVerifyEmail; @endphp
<section class="form-card" style="margin: 0; max-width: 100%;">
    <header style="margin-bottom: 2rem;">
        <h2 class="text-lg font-medium text-gray-900" style="color: var(--primary-dark); font-size: 1.5rem; margin-bottom: 0.5rem; border-bottom: none;">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600" style="color: var(--text-muted);">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="form-group">
            <x-input-label for="name" :value="__('Name')"/>
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" style="margin-bottom: 0.5rem;"/>
            <x-input-error :messages="$errors->get('name')" style="color: #ef4444; font-size: 0.875rem;"/>
        </div>

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" style="margin-bottom: 0.5rem;"/>
            <x-input-error :messages="$errors->get('email')" style="color: #ef4444; font-size: 0.875rem;"/>

            @if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top: 1rem;">
                    <p style="font-size: 0.875rem; color: var(--text-muted);">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" style="background: none; border: none; padding: 0; color: var(--primary); text-decoration: underline; cursor: pointer; font-size: inherit;">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p style="margin-top: 0.5rem; font-size: 0.875rem; color: #16a34a;">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-group">
            <x-input-label for="bio" :value="__('Bio')"/>
            <textarea id="bio" name="bio">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error :messages="$errors->get('bio')" style="color: #ef4444; font-size: 0.875rem;"/>
        </div>

        <div class="form-group">
            <x-input-label for="birthdate" :value="__('Birthdate')"/>
            <x-text-input id="birthdate" name="birthdate" type="date" :value="old('birthdate', $user->birthdate)" style="margin-bottom: 0.5rem;"/>
            <x-input-error :messages="$errors->get('birthdate')" style="color: #ef4444; font-size: 0.875rem;"/>
        </div>

        <div class="form-group">
            <x-input-label for="avatar" :value="__('Avatar')"/>
            <input id="avatar" name="avatar" type="file" accept="image/*" style="width: 100%; border: 1px solid var(--border-color); padding: 0.5rem; border-radius: var(--radius-sm);"/>
            <x-input-error :messages="$errors->get('avatar')" style="color: #ef4444; font-size: 0.875rem;"/>
        </div>

        <div class="flex items-center gap-4" style="display: flex; align-items: center; gap: 1rem; margin-top: 2rem;">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
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
