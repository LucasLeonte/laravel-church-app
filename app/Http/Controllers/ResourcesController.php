<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Redirect;
use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Support\Facades\Storage;

class ResourcesController extends Controller
{
    public function index(Request $request): Factory|View
    {
        // base query
        $query = Resource::query();

        // search in title or content
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('content', 'like', "%$search%");
            });
        }

        // filter by category id
        if ($request->filled('category')) {
            $query->where('resource_category_id', $request->input('category'));
        }

        // filter by author name
        if ($request->filled('author')) {
            $query->where('author', $request->input('author'));
        }

        // always sort newest first
        $resources = $query->orderBy('published_at', 'desc')->paginate(10)->withQueryString();

        // load categories and authors for filter controls
        $categories = ResourceCategory::orderBy('name')->get();
        $authors = Resource::whereNotNull('author')->select('author')->distinct()->orderBy('author')->pluck('author');

        return view('resources.index', compact('resources', 'categories', 'authors'));
    }

    // Show a single resource (full view)
    public function show($id): Factory|View
    {
        $resource = Resource::findOrFail($id);
        return view('resources.show', compact('resource'));
    }

    public function create(): Factory|View
    {
        $categories = ResourceCategory::orderBy('name')->get();
        return view('admin.resources.form', ['post' => new Resource(), 'categories' => $categories]);
    }

    public function edit($id): Factory|View
    {
        $post = Resource::findOrFail($id);
        $categories = ResourceCategory::orderBy('name')->get();
        return view('admin.resources.form', compact('post', 'categories'));
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
            'resource_category_id' => 'nullable|exists:resource_categories,id',
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
        $post->resource_category_id = $validated['resource_category_id'] ?? null;

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

        if (array_key_exists('resource_category_id', $validated)) {
            $post->resource_category_id = $validated['resource_category_id'];
        }

        if ($request->hasFile('image')) {
            // delete old image if present and not one of the shared defaults
            if (!empty($post->image) && $post->image !== 'default-resources-image.jpg' && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }

            $imagePath = $request->file('image')->storeAs('images/resources', uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $post->image = 'images/resources/' . basename($imagePath);
        }

        $post->save();
        return redirect()->route('resources.index')->with('success', 'Resource updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $post = Resource::findOrFail($id);

        // delete image from storage if it exists and isn't a shared default in public/images
        if (!empty($post->image) && $post->image !== 'default-resources-image.jpg' && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();
        return redirect()->route('resources.index')->with('success', 'Resource deleted successfully.');
    }

    // Admin: category CRUD
    public function categories(): Factory|View
    {
        $categories = ResourceCategory::orderBy('name')->get();
        return view('admin.resources.categories', compact('categories'));
    }

    public function createCategory(): Factory|View
    {
        return view('admin.resources.category-form', ['category' => new ResourceCategory()]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        ResourceCategory::create($data);
        return Redirect::route('resources.categories.index')->with('success', 'Category created');
    }

    public function editCategory($id): Factory|View
    {
        $category = ResourceCategory::findOrFail($id);
        return view('admin.resources.category-form', compact('category'));
    }

    public function updateCategory(Request $request, $id): RedirectResponse
    {
        $category = ResourceCategory::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $category->update($data);
        return Redirect::route('resources.categories.index')->with('success', 'Category updated');
    }

    public function destroyCategory($id): RedirectResponse
    {
        $category = ResourceCategory::findOrFail($id);
        $category->delete();
        return Redirect::route('resources.categories.index')->with('success', 'Category deleted');
    }

    // Show paginated manage view for resources
    public function manage(): Factory|View
    {
        $resources = Resource::orderBy('published_at', 'desc')->paginate(10);
        return view('admin.resources.resources', compact('resources'));
    }
}
