<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockItem::with('product')->active();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->get('low_stock')) {
            $query->lowStock();
        }

        $items    = $query->orderBy('name')->paginate(25);
        $lowCount = StockItem::active()->lowStock()->count();
        $products = Product::active()->orderBy('name')->get();

        return view('admin.stock.index', compact('items', 'lowCount', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'product_id'   => 'nullable|exists:products,id',
            'unit'         => 'required|string|max:10',
            'quantity'     => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'cost_price'   => 'nullable|numeric|min:0',
            'supplier'     => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated) {
            $item = StockItem::create($validated);

            if ($validated['quantity'] > 0) {
                StockMovement::create([
                    'stock_item_id'   => $item->id,
                    'user_id'         => auth()->id(),
                    'type'            => 'entrada',
                    'quantity'        => $validated['quantity'],
                    'quantity_before' => 0,
                    'quantity_after'  => $validated['quantity'],
                    'reason'          => 'Estoque inicial',
                ]);
            }
        });

        return back()->with('success', 'Item criado no estoque!');
    }

    public function movement(Request $request, StockItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'type'       => 'required|in:entrada,saida,ajuste',
            'quantity'   => 'required|numeric|min:0.001',
            'reason'     => 'nullable|string|max:200',
            'unit_cost'  => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($item, $validated) {
            $before = $item->quantity;
            $after  = match($validated['type']) {
                'entrada' => $before + $validated['quantity'],
                'saida'   => max(0, $before - $validated['quantity']),
                'ajuste'  => $validated['quantity'],
            };

            $item->update(['quantity' => $after]);

            StockMovement::create([
                'stock_item_id'   => $item->id,
                'user_id'         => auth()->id(),
                'type'            => $validated['type'],
                'quantity'        => $validated['quantity'],
                'quantity_before' => $before,
                'quantity_after'  => $after,
                'unit_cost'       => $validated['unit_cost'] ?? null,
                'reason'          => $validated['reason'] ?? null,
            ]);
        });

        return back()->with('success', 'Movimentação registrada!');
    }
}
