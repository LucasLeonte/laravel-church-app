<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Contracts\View\Factory;
use \Illuminate\Contracts\View\View;

class NewsController extends Controller
{
    public function index(): Factory|View
    {
        $newsPosts = \App\Models\Post::where('type', 'news')->get();
        return view('news.news', compact('newsPosts'));
    }

    public function create(): Factory|View
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('news.form', ['post' => new \App\Models\Post()]);
    }

    public function edit($id): Factory|View
    {
        $post = \App\Models\Post::findOrFail($id);
        return view('news.form', compact('post'));
    }

    //Create new news post
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
        ]);

        $post = new \App\Models\Post();
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->published_at = now();
        $post->type = 'news';

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('images/news', uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $post->image = basename($imagePath);
        }

        $post->save();

        return redirect()->route('news.index')->with('success', 'News post created successfully.');
    }

    //Modify existing news post
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
            'published_at' => 'required|date',
        ]);

        $post = \App\Models\Post::findOrFail($id);
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->published_at = $validated['published_at'];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('images/news', uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $post->image = basename($imagePath);
        }

        $post->save();

        return redirect()->route('news.index')->with('success', 'News post updated successfully.');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $post = \App\Models\Post::findOrFail($id);
        $post->delete();

        return redirect()->route('news.index')->with('success', 'News post deleted successfully.');
    }
}
