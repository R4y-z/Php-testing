<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function show(string $code): View
    {
        $order = Order::with(['items.product'])
            ->where('code', $code)
            ->firstOrFail();

        return view('store.tracking', compact('order'));
    }
}
