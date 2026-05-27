<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart     = session('cart', []);
        $delivery = Setting::getGroup('delivery');
        return view('store.cart', compact('cart', 'delivery'));
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.001',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->active || !$product->available) {
            return response()->json(['error' => 'Produto indisponível.'], 400);
        }

        $cart = session('cart', []);
        $key  = 'product_' . $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $validated['quantity'];
        } else {
            $cart[$key] = [
                'product_id'   => $product->id,
                'name'         => $product->name,
                'price'        => $product->price,
                'quantity'     => $validated['quantity'],
                'unit'         => $product->isKg() ? 'kg' : 'un',
                'image'        => $product->image_url,
            ];
        }

        $cart[$key]['total'] = round($cart[$key]['price'] * $cart[$key]['quantity'], 2);

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'count'   => count($cart),
            'total'   => $this->cartTotal($cart),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $key  = 'product_' . $request->product_id;
        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'count'   => count($cart),
            'total'   => $this->cartTotal($cart),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'quantity'   => 'required|numeric|min:0',
        ]);

        $key  = 'product_' . $validated['product_id'];
        $cart = session('cart', []);

        if ($validated['quantity'] <= 0) {
            unset($cart[$key]);
        } elseif (isset($cart[$key])) {
            $cart[$key]['quantity'] = $validated['quantity'];
            $cart[$key]['total']    = round($cart[$key]['price'] * $validated['quantity'], 2);
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'count'   => count($cart),
            'total'   => $this->cartTotal($cart),
        ]);
    }

    public function clear(): JsonResponse
    {
        session()->forget('cart');
        return response()->json(['success' => true]);
    }

    private function cartTotal(array $cart): float
    {
        return round(array_sum(array_column($cart, 'total')), 2);
    }
}
