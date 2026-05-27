<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'active'      => 'nullable|boolean',
            'image'       => 'nullable|image|max:2048',
        ]);

        $validated['slug']   = Str::slug($validated['name']);
        $validated['active'] = $request->has('active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'active'      => 'nullable|boolean',
            'image'       => 'nullable|image|max:2048',
        ]);

        $validated['slug']   = Str::slug($validated['name']);
        $validated['active'] = $request->has('active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria atualizada!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Não é possível excluir uma categoria com produtos.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria excluída!');
    }
}
