<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Program;
use App\Models\FriendRequest;
use App\Models\Resource;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Show the application home page with a daily bible verse and sections.
     *
     * @return View
     */
    public function index(): View
    {
        // A small rotating list of popular verses (reference => text).
        $verses = [
            ['reference' => 'John 3:16', 'text' => 'For God so loved the world that he gave his one and only Son, that whoever believes in him shall not perish but have eternal life.'],
            ['reference' => 'Psalm 23:1', 'text' => 'The Lord is my shepherd; I shall not want.'],
            ['reference' => 'Philippians 4:13', 'text' => 'I can do all this through him who gives me strength.'],
            ['reference' => 'Romans 8:28', 'text' => 'And we know that in all things God works for the good of those who love him...'],
            ['reference' => 'Proverbs 3:5-6', 'text' => 'Trust in the Lord with all your heart and lean not on your own understanding...'],
            ['reference' => 'Matthew 5:16', 'text' => 'Let your light shine before others, that they may see your good deeds and glorify your Father in heaven.'],
            ['reference' => 'Isaiah 41:10', 'text' => 'So do not fear, for I am with you; do not be dismayed, for I am your God. I will strengthen you and help you...'],
            ['reference' => 'Psalm 46:1', 'text' => 'God is our refuge and strength, an ever-present help in trouble.'],
            ['reference' => 'Hebrews 11:1', 'text' => 'Now faith is confidence in what we hope for and assurance about what we do not see.'],
            ['reference' => 'Galatians 5:22-23', 'text' => 'But the fruit of the Spirit is love, joy, peace, forbearance, kindness, goodness, faithfulness, gentleness and self-control.']
        ];

        $daySeed = (int) now()->format('z'); // day of year (0-365)
        $index = $daySeed % count($verses);
        $dailyVerse = $verses[$index];

        // 3 newest news posts (published_at <= now)
        $news = News::where('published_at', '<=', now())->orderByDesc('published_at')->take(3)->get();

        // Upcoming programs: show next 3 events from today onwards (by weekday and start_time)
        $today = Carbon::now();
        $todayIndex = $today->dayOfWeek; // 0 (Sun) - 6 (Sat)

        // Fetch all published programs and compute their day offset from today
        $allPrograms = Program::query()->where('published', true)->get();

        $upcomingPrograms = $allPrograms->map(function ($p) use ($todayIndex) {
            // Map weekday names to numeric indexes (0 = Sunday ... 6 = Saturday)
            $weekdayOrder = ['Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6];
            $dayName = $p->day_of_week;
            // If program has an unexpected day value, skip it by returning null
            if (! isset($weekdayOrder[$dayName])) {
                return null;
            }
            // Compute the offset in days from today (0 = today, 1 = tomorrow, ..., 6 = next week)
            $dayIndex = $weekdayOrder[$dayName];
            $p->day_offset = ($dayIndex - $todayIndex + 7) % 7;
            return $p;
        })->filter() // remove any null entries (invalid day_of_week)
        ->sort(function ($a, $b) {
            // Primary sort: by day offset (so earlier upcoming days first)
            if ($a->day_offset === $b->day_offset) {
                // Secondary sort: by start_time for events on the same day
                return strcmp($a->start_time ?? '', $b->start_time ?? '');
            }
            return $a->day_offset <=> $b->day_offset;
        })->values() // reindex collection after sorting
        ->take(3); // keep only the next 3 upcoming events

        // Expose as $programs for the view (upcoming next 3 events)
        $programs = $upcomingPrograms;

        // pending friend requests for authenticated user (received)
        $pendingRequests = collect();
        if (Auth::check()) {
            $authId = Auth::id();
            $pendingRequests = FriendRequest::query()
                ->where('receiver_id', $authId)
                ->where('status', 'pending')
                ->with('sender')
                ->orderByDesc('created_at')
                ->get();
        }

        // 3 latest resources
        $latestResources = Resource::where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('home', compact('dailyVerse', 'news', 'programs', 'pendingRequests', 'latestResources'));
    }
}
