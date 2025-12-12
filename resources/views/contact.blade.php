@extends('layouts.app')

@section('title', 'Contact')

@section('content')
    <section class="dashboard-section" style="max-width: 700px; margin: 0 auto; padding: 1rem;">
        <div style="text-align: center; margin-bottom: 3rem;">
            <p style="font-size: 1.1rem; color: var(--text-muted);">Ask us a question or become a member at our church!</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success" style="margin-bottom: 2rem; padding: 1rem; border-radius: var(--radius-sm); background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; text-align: center;">
                {{ session('status') }}
            </div>
        @endif

        <form id="contactForm" action="{{ route('contact.send') }}" method="POST" novalidate class="form-card" style="margin-top: 0;">
            @csrf

            <div class="form-group">
                <label for="name">Full Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" placeholder="Your full name">
                @error('name') <div class="text-danger" style="color: #E63946; font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" placeholder="name@example.com">
                @error('email') <div class="text-danger" style="color: #E63946; font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" required maxlength="2000" rows="5" placeholder="How can we help you?">{{ old('message') }}</textarea>
                @error('message') <div class="text-danger" style="color: #E63946; font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn" style="width: 100%; justify-content: center; padding: 1rem; font-size: 1.1rem;">Send Message</button>
        </form>
    </section>

    <script>
        // Basic client-side validation and trimming to complement server-side rules.
        (function () {
            const form = document.getElementById('contactForm');
            if (!form) return;

            function showError(el, msg) {
                let next = el.nextElementSibling;
                if (!next || !next.classList.contains('client-error')) {
                    next = document.createElement('div');
                    next.className = 'client-error text-danger';
                    el.parentNode.insertBefore(next, el.nextSibling);
                }
                next.textContent = msg;
            }

            function clearError(el) {
                const next = el.nextElementSibling;
                if (next && next.classList.contains('client-error')) next.remove();
            }

            form.addEventListener('submit', function (e) {
                let valid = true;

                // trim inputs
                ['name','email','message'].forEach(function (id) {
                    const el = document.getElementById(id);
                    if (el && el.value) el.value = el.value.trim();
                });

                const name = document.getElementById('name');
                if (!name.value) { showError(name, 'Please enter your name'); valid = false; } else { clearError(name); }

                const email = document.getElementById('email');
                if (!email.value) { showError(email, 'Please enter your email'); valid = false; } else if (!/^\S+@\S+\.\S+$/.test(email.value)) { showError(email, 'Please enter a valid email'); valid = false; } else { clearError(email); }

                const message = document.getElementById('message');
                if (!message.value) { showError(message, 'Please enter a message'); valid = false; } else if (message.value.length > 2000) { showError(message, 'Message too long'); valid = false; } else { clearError(message); }

                if (!valid) {
                    e.preventDefault();
                    return false;
                }
            });
        })();
    </script>
@endsection
