<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Models\News;

class NewsController extends Controller
{
    public function index(): Factory|View
    {
        $news = News::orderBy('published_at', 'desc')->get();
        return view('news', ['news' => $news]);
    }

    public function create(): Factory|View
    {
        return view('admin.news.form', ['post' => new News()]);
    }

    public function edit($id): Factory|View
    {
        $post = News::findOrFail($id);
        return view('admin.news.form', compact('post'));
    }

    /**
     * Validator rules specific to News.
     * image required on create, nullable on update.
     * author is optional for news.
     */
    protected function rules(bool $isUpdate = false): array
    {
        $imageRulePrefix = (!$isUpdate) ? 'required' : 'nullable';

        return [
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'content' => 'required|string',
            'image' => $imageRulePrefix . '|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $post = new News();
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->author = $validated['author'] ?? null;
        $post->published_at = now();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('images/news', uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $post->image = 'images/news/' . basename($imagePath);
        }

        $post->save();
        return redirect()->route('news.index')->with('success', 'News post created successfully.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate($this->rules(true));
        $post = News::findOrFail($id);
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->author = $validated['author'] ?? $post->author;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('images/news', uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $post->image = 'images/news/' . basename($imagePath);
        }

        $post->save();
        return redirect()->route('news.index')->with('success', 'News post updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $post = News::findOrFail($id);
        $post->delete();
        return redirect()->route('news.index')->with('success', 'News post deleted successfully.');
    }
}
