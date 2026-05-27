<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('store.cart')->with('error', 'Seu carrinho está vazio.');
        }

        $delivery = Setting::getGroup('delivery');
        return view('store.checkout', compact('cart', 'delivery'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('store.index')->with('error', 'Carrinho vazio.');
        }

        $validated = $request->validate([
            'customer_name'  => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'order_type'     => 'required|in:delivery,retirada',
            'payment_method' => 'required|in:dinheiro,pix,cartao_credito,cartao_debito',
            'address_street' => 'required_if:order_type,delivery|nullable|string',
            'address_number' => 'required_if:order_type,delivery|nullable|string',
            'address_neighborhood' => 'required_if:order_type,delivery|nullable|string',
            'address_city'   => 'nullable|string',
            'notes'          => 'nullable|string|max:500',
        ]);

        $subtotal    = array_sum(array_column($cart, 'total'));
        $deliveryFee = $validated['order_type'] === 'delivery'
            ? (float) Setting::get('delivery_fee', 5)
            : 0;
        $total = $subtotal + $deliveryFee;

        $minOrder = (float) Setting::get('min_order', 0);
        if ($total < $minOrder) {
            return back()->with('error', 'Pedido mínimo: R$ ' . number_format($minOrder, 2, ',', '.'));
        }

        $order = DB::transaction(function () use ($validated, $cart, $subtotal, $deliveryFee, $total) {
            $customer = Customer::firstOrCreate(
                ['phone' => $validated['customer_phone']],
                ['name' => $validated['customer_name'], 'phone' => $validated['customer_phone']]
            );

            $deliveryAddress = null;
            if ($validated['order_type'] === 'delivery') {
                $deliveryAddress = implode(', ', array_filter([
                    $validated['address_street'] ?? '',
                    $validated['address_number'] ?? '',
                    $validated['address_neighborhood'] ?? '',
                    $validated['address_city'] ?? 'Canindé de São Francisco',
                ]));
            }

            $order = Order::create([
                'customer_id'    => $customer->id,
                'type'           => $validated['order_type'],
                'status'         => 'recebido',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pendente',
                'subtotal'       => $subtotal,
                'delivery_fee'   => $deliveryFee,
                'total'          => $total,
                'notes'          => $validated['notes'] ?? null,
                'delivery_address' => $deliveryAddress,
                'customer_name'  => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
            ]);

            foreach ($cart as $item) {
                $product = Product::find($item['product_id']);
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'unit_price'   => $item['price'],
                    'quantity'     => $item['quantity'],
                    'unit'         => $item['unit'],
                    'total'        => $item['total'],
                ]);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('store.tracking', $order->code)
            ->with('success', 'Pedido realizado! Código: ' . $order->code);
    }
}
