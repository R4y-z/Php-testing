<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComandaItem;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['items.product', 'table'])
            ->whereIn('status', ['confirmado', 'preparando'])
            ->latest()
            ->get();

        $comandaItems = ComandaItem::with(['comanda.table', 'product'])
            ->where('status', 'pendente')
            ->latest()
            ->get();

        return view('admin.kitchen.index', compact('orders', 'comandaItems'));
    }

    public function updateOrderStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate(['status' => 'required|in:preparando,pronto']);
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'status'  => $order->status,
            'label'   => $order->status_label,
        ]);
    }

    public function updateItemStatus(Request $request, ComandaItem $item): JsonResponse
    {
        $request->validate(['status' => 'required|in:preparando,pronto']);
        $item->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }
}
