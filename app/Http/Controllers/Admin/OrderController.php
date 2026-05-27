<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'items', 'table']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            $query->whereDate('created_at', today());
        }

        $orders = $query->latest()->paginate(30);

        $counts = [
            'recebido'   => Order::whereDate('created_at', today())->where('status', 'recebido')->count(),
            'confirmado' => Order::whereDate('created_at', today())->where('status', 'confirmado')->count(),
            'preparando' => Order::whereDate('created_at', today())->where('status', 'preparando')->count(),
            'pronto'     => Order::whereDate('created_at', today())->where('status', 'pronto')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'customer', 'table', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:recebido,confirmado,preparando,pronto,saiu_entrega,finalizado,cancelado',
        ]);

        $timestamps = [
            'confirmado'   => 'confirmed_at',
            'preparando'   => 'prepared_at',
            'pronto'       => 'ready_at',
            'saiu_entrega' => 'delivered_at',
            'finalizado'   => 'finished_at',
        ];

        $data = ['status' => $validated['status']];
        if (isset($timestamps[$validated['status']])) {
            $data[$timestamps[$validated['status']]] = now();
        }

        $order->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $order->status_label]);
        }

        return back()->with('success', 'Status do pedido atualizado!');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Este pedido não pode ser cancelado.');
        }

        $order->update(['status' => 'cancelado']);
        return back()->with('success', 'Pedido cancelado.');
    }
}
