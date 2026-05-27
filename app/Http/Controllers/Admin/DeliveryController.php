<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['customer', 'items'])
            ->where('type', 'delivery')
            ->whereIn('status', ['pronto', 'saiu_entrega', 'confirmado', 'preparando'])
            ->latest()
            ->get();

        $history = Order::with(['customer'])
            ->where('type', 'delivery')
            ->whereIn('status', ['finalizado', 'cancelado'])
            ->whereDate('created_at', today())
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.delivery.index', compact('orders', 'history'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:saiu_entrega,finalizado,cancelado',
        ]);

        $data = ['status' => $request->status];
        if ($request->status === 'saiu_entrega') {
            $data['delivered_at'] = now();
        } elseif ($request->status === 'finalizado') {
            $data['finished_at'] = now();
        }

        $order->update($data);
        return back()->with('success', 'Status atualizado!');
    }
}
