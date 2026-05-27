<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', today()->format('Y-m-d'));
        $endDate   = $request->get('end_date', today()->format('Y-m-d'));

        $revenue = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('payment_status', 'pago')
            ->sum('total');

        $ordersCount = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', '!=', 'cancelado')
            ->count();

        $avgTicket = $ordersCount > 0 ? $revenue / $ordersCount : 0;

        $byStatus = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('status')
            ->get();

        $topProducts = OrderItem::with('product')
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']))
            ->selectRaw('product_id, product_name, SUM(quantity) as total_qty, SUM(total) as total_revenue, COUNT(*) as orders')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $byPayment = Payment::where('status', 'aprovado')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('method')
            ->get();

        $kgSold = OrderItem::where('unit', 'kg')
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']))
            ->sum('quantity');

        $dailyRevenue = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('payment_status', 'pago')
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.index', compact(
            'revenue', 'ordersCount', 'avgTicket', 'byStatus', 'topProducts',
            'byPayment', 'kgSold', 'dailyRevenue', 'startDate', 'endDate'
        ));
    }
}
