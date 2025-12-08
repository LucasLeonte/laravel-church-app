<?php

namespace App\Http\Controllers;

use App\Models\FavoriteVerse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteVerseController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $favorites = Auth::id() ? FavoriteVerse::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get() : collect();
        return view('favorites.index', compact('favorites'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'translation' => 'required|string|max:32',
            'book' => 'required|string|max:255',
            'chapter' => 'required|integer|min:1',
            'verse' => 'required|integer|min:1',
        ]);

        $data = $request->only(['translation', 'book', 'chapter', 'verse']);
        $data['user_id'] = Auth::id();

        FavoriteVerse::firstOrCreate($data);

        return back()->with('success', 'Verse added to favorites');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $fav = FavoriteVerse::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$fav) {
            return back()->with('error', 'Favorite not found');
        }

        $fav->delete();

        return back()->with('success', 'Favorite removed');
    }
}
