<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\Comanda;
use App\Models\Order;
use App\Models\Product;
use App\Models\RestaurantTable;
use App\Models\StockItem;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = today();

        $stats = [
            'orders_today'     => Order::whereDate('created_at', $today)->count(),
            'revenue_today'    => Order::whereDate('created_at', $today)->where('payment_status', 'pago')->sum('total'),
            'pending_orders'   => Order::whereIn('status', ['recebido', 'confirmado', 'preparando'])->count(),
            'open_tables'      => RestaurantTable::where('status', 'ocupada')->count(),
            'open_comandas'    => Comanda::whereIn('status', ['aberta', 'fechamento'])->count(),
            'low_stock'        => StockItem::active()->lowStock()->count(),
            'cash_session'     => CashSession::getCurrent(),
            'available_tables' => RestaurantTable::where('status', 'disponivel')->active()->count(),
        ];

        $recent_orders = Order::with(['customer', 'items'])
            ->latest()
            ->limit(10)
            ->get();

        $top_products = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->limit(5)
            ->get();

        $orders_by_status = Order::whereDate('created_at', $today)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.dashboard.index', compact(
            'stats', 'recent_orders', 'top_products', 'orders_by_status'
        ));
    }
}
