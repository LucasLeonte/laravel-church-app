<?php

namespace App\Http\Controllers;

use App\Models\FavoriteVerse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteVerseController extends Controller
{
    public function index(Request $request): Factory|View|JsonResponse
    {
        $favorites = Auth::id() ? FavoriteVerse::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get() : collect();

        if ($request->expectsJson()) {
            return response()->json($favorites);
        }

        return view('bible.favorites', compact('favorites'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'translation' => 'required|string|max:32',
            'book' => 'required|string|max:255',
            'chapter' => 'required|integer|min:1',
            'verse' => 'required|integer|min:1',
        ]);

        $data = $request->only(['translation', 'book', 'chapter', 'verse']);
        $data['user_id'] = Auth::id();

        $favorite = FavoriteVerse::firstOrCreate($data);

        if ($request->expectsJson()) {
            return response()->json(['favorite' => $favorite], 201);
        }

        return back()->with('success', 'Verse added to favorites');
    }

    public function destroy(Request $request, $id): JsonResponse|RedirectResponse
    {
        $fav = FavoriteVerse::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$fav) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Favorite not found'], 404);
            }
            return back()->with('error', 'Favorite not found');
        }

        $fav->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Favorite removed');
    }
}
