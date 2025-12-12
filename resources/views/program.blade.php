@extends('layouts.app')

@section('title', 'Weekly Program')

@section('content')
    <div style="max-width: var(--container-width); margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            {{-- Title/Intro if needed, or just the button --}}
            <div>
                {{-- Could add a subtitle or filter here later --}}
            </div>

            @can('admin')
                <a href="{{ route('program.manage') }}" class="btn" style="background: var(--secondary); color: var(--primary-dark); display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 999px; padding: 0.5rem 1.25rem; font-weight: 600;">
                    <span style="font-size: 1.2rem;">&#9881;</span> Manage Program
                </a>
            @endcan
        </div>

        <section class="dashboard-section">
            @if($programs->isEmpty())
                <div style="text-align: center; padding: 3rem; background: var(--bg-surface); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
                    <p style="font-size: 1.1rem; color: var(--text-muted);">No program items scheduled at the moment.</p>
                </div>
            @else
                {{-- Using ul/li to leverage existing .dashboard-section ul grid styles in app.css --}}
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($programs as $item)
                        <li style="position: relative; overflow: hidden; display: flex; flex-direction: column;">
                            {{-- Day Badge --}}
                            @if($item->day_of_week)
                                <div style="position: absolute; top: 0; right: 0; background: var(--secondary); color: var(--primary-dark); padding: 0.25rem 0.75rem; border-bottom-left-radius: var(--radius-sm); font-weight: 700; font-size: 0.85rem;">
                                    {{ $item->day_of_week }}
                                </div>
                            @endif

                            <h2 style="font-size: 1.25rem; margin-bottom: 0.5rem; margin-top: 0.5rem; padding-right: 3rem; border: none; color: var(--text-main);">
                                {{ $item->title }}
                            </h2>

                            {{-- Time Badge --}}
                            <div style="margin-bottom: 1rem;">
                                <span style="display: inline-block; background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">
                                    @if($item->start_time)
                                        {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                    @endif
                                    @if($item->end_time)
                                        - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                    @endif
                                </span>
                            </div>

                            @if($item->description)
                                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5; flex-grow: 1;">
                                    {{ $item->description }}
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
