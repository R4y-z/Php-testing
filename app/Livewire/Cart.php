<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Setting;
use Livewire\Component;

class Cart extends Component
{
    public array $cart = [];
    public bool $showCart = false;

    public function mount(): void
    {
        $this->cart = session('cart', []);
    }

    public function render()
    {
        $deliveryFee = (float) Setting::get('delivery_fee', 5);
        $subtotal    = array_sum(array_column($this->cart, 'total'));
        $total       = $subtotal + $deliveryFee;

        return view('livewire.cart', compact('deliveryFee', 'subtotal', 'total'));
    }

    public function addProduct(int $productId, float $quantity = 1): void
    {
        $product = Product::find($productId);
        if (!$product || !$product->active || !$product->available) {
            return;
        }

        $key = 'product_' . $productId;

        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity'] += $quantity;
        } else {
            $this->cart[$key] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => (float) $product->price,
                'quantity'   => $quantity,
                'unit'       => $product->isKg() ? 'kg' : 'un',
                'image'      => $product->image_url,
            ];
        }

        $this->cart[$key]['total'] = round($this->cart[$key]['price'] * $this->cart[$key]['quantity'], 2);

        session(['cart' => $this->cart]);
        $this->dispatch('cart-updated', count: count($this->cart));
    }

    public function removeItem(string $key): void
    {
        unset($this->cart[$key]);
        session(['cart' => $this->cart]);
        $this->dispatch('cart-updated', count: count($this->cart));
    }

    public function updateQuantity(string $key, float $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($key);
            return;
        }

        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity'] = $quantity;
            $this->cart[$key]['total']    = round($this->cart[$key]['price'] * $quantity, 2);
            session(['cart' => $this->cart]);
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
        session()->forget('cart');
        $this->dispatch('cart-updated', count: 0);
    }

    public function getCountProperty(): int
    {
        return count($this->cart);
    }

    public function getSubtotalProperty(): float
    {
        return array_sum(array_column($this->cart, 'total'));
    }
}
