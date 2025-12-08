<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Faq;
use App\Models\FaqCategory;

class FaqController extends Controller
{
    // Public listing grouped by category
    public function index(Request $request): Factory|View
    {
        $q = $request->input('q');

        if (!$q) {
            $categories = FaqCategory::with(['faqs' => function($q) {
                $q->orderBy('id');
            }])->orderBy('name')->get();

            return view('faq', compact('categories'));
        }

        // When searching:
        // 1) categories where the category name matches -> include all faqs
        // 2) categories that have faqs matching question/answer -> include only the matching faqs

        $qStr = "%$q%";

        // Categories where category name matches
        $categoriesByName = FaqCategory::where('name', 'like', $qStr)
            ->with(['faqs' => function($q) { $q->orderBy('id'); }])
            ->orderBy('name')
            ->get();

        // Categories that have matching faqs (question or answer)
        $categoriesWithMatchingFaqs = FaqCategory::whereHas('faqs', function($faqQ) use ($qStr) {
            $faqQ->where('question', 'like', $qStr)
                  ->orWhere('answer', 'like', $qStr);
        })->with(['faqs' => function($faqQ) use ($qStr) {
            $faqQ->where('question', 'like', $qStr)
                  ->orWhere('answer', 'like', $qStr)
                  ->orderBy('id');
        }])->orderBy('name')->get();

        // Merge categories, prefer the full category when name matched
        $merged = $categoriesByName->keyBy('id');
        foreach ($categoriesWithMatchingFaqs as $cat) {
            if ($merged->has($cat->id)) {
                // already present (category name matched) - leave as is (full faqs)
                continue;
            }
            $merged->put($cat->id, $cat);
        }

        $categories = $merged->values();

        return view('faq', compact('categories'));
    }

    // Admin: category CRUD
    public function categories(): Factory|View
    {
        $categories = FaqCategory::orderBy('name')->get();
        return view('admin.faq.categories', compact('categories'));
    }

    public function createCategory(): Factory|View
    {
        return view('admin.faq.category-form', ['category' => new FaqCategory()]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        FaqCategory::create($data);
        return Redirect::route('faq.categories.index')->with('success', 'Category created');
    }

    public function editCategory($id): Factory|View
    {
        $category = FaqCategory::findOrFail($id);
        return view('admin.faq.category-form', compact('category'));
    }

    public function updateCategory(Request $request, $id): RedirectResponse
    {
        $category = FaqCategory::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $category->update($data);
        return Redirect::route('faq.categories.index')->with('success', 'Category updated');
    }

    public function destroyCategory($id): RedirectResponse
    {
        $category = FaqCategory::findOrFail($id);
        $category->delete();
        return Redirect::route('faq.categories.index')->with('success', 'Category deleted');
    }

    // Admin: faq CRUD
    public function faqs(Request $request): Factory|View
    {
        $q = $request->input('q');
        $faqs = Faq::with('category')
            ->when($q, function($query, $q) {
                $query->where(function($sub) use ($q) {
                    $sub->where('question', 'like', "%$q%")
                        ->orWhere('answer', 'like', "%$q%")
                        ->orWhereHas('category', function($cat) use ($q) {
                            $cat->where('name', 'like', "%$q%");
                        });
                });
            })
            ->orderBy('faq_category_id')
            ->paginate(15);

        return view('admin.faq.faqs', compact('faqs'));
    }

    public function createFaq(): Factory|View
    {
        $categories = FaqCategory::orderBy('name')->get();
        return view('admin.faq.faq-form', ['faq' => new Faq(), 'categories' => $categories]);
    }

    public function storeFaq(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);
        Faq::create($data);
        return Redirect::route('faq.faqs.index')->with('success', 'FAQ created');
    }

    public function editFaq($id): Factory|View
    {
        $faq = Faq::findOrFail($id);
        $categories = FaqCategory::orderBy('name')->get();
        return view('admin.faq.faq-form', compact('faq', 'categories'));
    }

    public function updateFaq(Request $request, $id): RedirectResponse
    {
        $faq = Faq::findOrFail($id);
        $data = $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);
        $faq->update($data);
        return Redirect::route('faq.faqs.index')->with('success', 'FAQ updated');
    }

    public function destroyFaq($id): RedirectResponse
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();
        return Redirect::route('faq.faqs.index')->with('success', 'FAQ deleted');
    }
}
