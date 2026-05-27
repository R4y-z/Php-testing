<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }

        $products   = $query->orderBy('category_id')->orderBy('sort_order')->paginate(20);
        $categories = Category::active()->orderBy('sort_order')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|string|max:150',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'type'           => 'required|in:unitario,kg',
            'sort_order'     => 'nullable|integer',
            'image'          => 'nullable|image|max:3072',
            'track_stock'    => 'nullable|boolean',
            'stock_min'      => 'nullable|numeric|min:0',
        ]);

        $validated['slug']        = Str::slug($validated['name']);
        $validated['active']      = $request->has('active');
        $validated['available']   = $request->has('available');
        $validated['track_stock'] = $request->has('track_stock');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Product $product): View
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|string|max:150',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'type'           => 'required|in:unitario,kg',
            'sort_order'     => 'nullable|integer',
            'image'          => 'nullable|image|max:3072',
            'stock_min'      => 'nullable|numeric|min:0',
        ]);

        $validated['active']      = $request->has('active');
        $validated['available']   = $request->has('available');
        $validated['track_stock'] = $request->has('track_stock');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto atualizado!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Produto excluído!');
    }

    public function toggleAvailable(Product $product): RedirectResponse
    {
        $product->update(['available' => !$product->available]);
        $status = $product->available ? 'disponível' : 'indisponível';
        return back()->with('success', "Produto marcado como {$status}!");
    }
}
