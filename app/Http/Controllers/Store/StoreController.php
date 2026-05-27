<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(): View
    {
        $categories = Category::with(['activeProducts' => fn($q) => $q->orderBy('sort_order')])
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->filter(fn($cat) => $cat->activeProducts->isNotEmpty());

        $settings = Setting::getGroup('general');
        $delivery = Setting::getGroup('delivery');

        return view('store.home', compact('categories', 'settings', 'delivery'));
    }

    public function product(Product $product): View
    {
        abort_unless($product->active, 404);
        return view('store.product', compact('product'));
    }
}
