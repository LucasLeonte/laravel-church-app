<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Models\Post;

class PostController extends Controller
{
    protected $type;

    public function index(): Factory|View
    {
        $posts = Post::where('type', $this->type)->get();
        return view($this->type, ['posts' => $posts]);
    }

    public function create(): Factory|View
    {
        return view('admin.' . $this->type . '.form', ['post' => new Post()]);
    }

    public function edit($id): Factory|View
    {
        $post = Post::findOrFail($id);
        return view('admin.' . $this->type . '.form', compact('post'));
    }

    /**
     * Centralized validator rules depending on context.
     *
     * @param Request $request
     * @param bool $isUpdate
     * @return array
     */
    protected function rules(Request $request, bool $isUpdate = false): array
    {
        // author required only for resources; optional for news and other types
        $authorRule = $this->type === 'resources' ? 'required|string|max:255' : 'nullable|string|max:255';

        // image required for news on create, optional (nullable) on update and for resources
        $imageRulePrefix = ($this->type === 'news' && !$isUpdate) ? 'required' : 'nullable';

        return [
            'title' => 'required|string|max:255',
            'author' => $authorRule,
            'content' => 'required|string',
            'image' => $imageRulePrefix . '|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate($this->rules($request, false));

        $post = new Post();
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->author = $validated['author'] ?? null;
        $post->published_at = now();
        $post->type = $this->type;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('images/' . $this->type, uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            // store path relative to storage/app/public so asset('storage/' . $post->image) works
            $post->image = 'images/' . $this->type . '/' . basename($imagePath);
        } else {
            // If this is a resources post and no image was uploaded, use the factory/default image value
            if ($this->type === 'resources') {
                $post->image = 'default-news-image.jpg';
            }
        }

        $post->save();
        return redirect()->route($this->type . '.index')->with('success', ucfirst($this->type) . ' post created successfully.');
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate($this->rules($request, true));
        $post = Post::findOrFail($id);
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->author = $validated['author'] ?? $post->author;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('images/' . $this->type, uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $post->image = 'images/' . $this->type . '/' . basename($imagePath);
        }

        $post->save();
        return redirect()->route($this->type . '.index')->with('success', ucfirst($this->type) . ' post updated successfully.');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect()->route($this->type . '.index')->with('success', ucfirst($this->type) . ' post deleted successfully.');
    }
}
