<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Models\Resource;

class ResourcesController extends Controller
{
    public function index(): Factory|View
    {
        $resources = Resource::orderBy('published_at', 'desc')->get();
        return view('resources', ['resources' => $resources]);
    }

    public function create(): Factory|View
    {
        return view('admin.resources.form', ['post' => new Resource()]);
    }

    public function edit($id): Factory|View
    {
        $post = Resource::findOrFail($id);
        return view('admin.resources.form', compact('post'));
    }

    /**
     * Validator rules specific to Resources.
     * author required, image optional (nullable).
     */
    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            // content is required unless a link is provided (useful for link-only resources)
            'content' => 'required_without:link|string',
            // optional link, must be a valid URL when present
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $post = new Resource();
        $post->title = $validated['title'];
        $post->content = $validated['content'] ?? null;
        $post->author = $validated['author'];
        $post->published_at = now();

        // store link if provided
        $post->link = $validated['link'] ?? null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('images/resources', uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $post->image = 'images/resources/' . basename($imagePath);
        } else {
            // use the new default resource image added to public/images
            $post->image = 'default-resources-image.jpg';
        }

        $post->save();
        return redirect()->route('resources.index')->with('success', 'Resource created successfully.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $post = Resource::findOrFail($id);
        $post->title = $validated['title'];
        $post->content = $validated['content'] ?? $post->content;
        $post->author = $validated['author'] ?? $post->author;

        // update link only when explicitly provided in the request (allow keeping existing link)
        if (array_key_exists('link', $validated)) {
            $post->link = $validated['link'];
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('images/resources', uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $post->image = 'images/resources/' . basename($imagePath);
        }

        $post->save();
        return redirect()->route('resources.index')->with('success', 'Resource updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $post = Resource::findOrFail($id);
        $post->delete();
        return redirect()->route('resources.index')->with('success', 'Resource deleted successfully.');
    }
}
