@extends('layouts.app')

@section('title', 'Welcome')
@section('hide_title', true)
@section('hide_navbar', true)
@section('hide_footer', true)

@section('content')
    {{-- Hero Section --}}
    <section style="text-align: center; padding: 4rem 1rem 6rem; background: radial-gradient(circle at center, var(--bg-surface), var(--bg-body)); border-bottom: 1px solid var(--border-color); margin-bottom: 3rem; border-radius: var(--radius-lg);">
        <h1 style="font-size: 3.5rem; color: var(--primary-dark); margin-bottom: 1rem; letter-spacing: -1px; line-height: 1.1;">
            Welcome to <span style="color: var(--primary);">Elim Church</span>
        </h1>
        <p style="font-size: 1.25rem; color: var(--text-muted); max-width: 600px; margin: 0 auto 2.5rem; line-height: 1.6;">
            A space to connect, grow, and access spiritual resources. Join our community and explore the Bible, sermons, and more.
        </p>

        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            @auth
                <a href="{{ route('home') }}" class="btn" style="background: var(--primary); color: white; padding: 0.75rem 2rem; font-size: 1.1rem; border-radius: 999px;">Go to Dashboard &rarr;</a>
            @else
                <a href="{{ route('login') }}" class="btn" style="background: var(--primary); color: white; padding: 0.75rem 2rem; font-size: 1.1rem; border-radius: 999px;">Log In</a>
                <a href="{{ route('register') }}" class="btn" style="background: var(--bg-surface); color: var(--primary-dark); border: 1px solid var(--border-color); padding: 0.75rem 2rem; font-size: 1.1rem; border-radius: 999px;">Register</a>
                <a href="{{ route('home') }}" style="display: block; width: 100%; margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem; text-decoration: underline;">Continue as Guest</a>
            @endauth
        </div>
    </section>

    {{-- Features Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 4rem;">
        {{-- Feature 1 --}}
        <div class="dashboard-section" style="text-align: center; padding: 2rem;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">&#128214;</div>
            <h3 style="margin-bottom: 0.75rem; color: var(--primary-dark);">Bible & Resources</h3>
            <p style="color: var(--text-muted);">Access the WEB Bible translation and a library of spiritual resources and sermons.</p>
        </div>

        {{-- Feature 2 --}}
        <div class="dashboard-section" style="text-align: center; padding: 2rem;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">&#128101;</div>
            <h3 style="margin-bottom: 0.75rem; color: var(--primary-dark);">Community Connect</h3>
            <p style="color: var(--text-muted);">Find and connect with other members of the church. Send requests and build friendships.</p>
        </div>

        {{-- Feature 3 --}}
        <div class="dashboard-section" style="text-align: center; padding: 2rem;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">&#128197;</div>
            <h3 style="margin-bottom: 0.75rem; color: var(--primary-dark);">Weekly Program</h3>
            <p style="color: var(--text-muted);">Stay up to date with our weekly schedule, events, and special services.</p>
        </div>
    </div>
@endsection
