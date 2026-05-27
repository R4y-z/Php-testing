<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comanda;
use App\Models\ComandaItem;
use App\Models\Product;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComandaController extends Controller
{
    public function index(): View
    {
        $comandas = Comanda::with(['table', 'openedBy'])
            ->whereIn('status', ['aberta', 'fechamento'])
            ->latest()
            ->get();

        $tables = RestaurantTable::active()->orderBy('number')->get();

        return view('admin.comandas.index', compact('comandas', 'tables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_id'      => 'nullable|exists:restaurant_tables,id',
            'customer_name' => 'nullable|string|max:100',
        ]);

        $comanda = Comanda::create([
            'table_id'      => $validated['table_id'] ?? null,
            'customer_name' => $validated['customer_name'] ?? null,
            'opened_by'     => auth()->id(),
            'status'        => 'aberta',
        ]);

        if ($validated['table_id']) {
            RestaurantTable::find($validated['table_id'])->update(['status' => 'ocupada']);
        }

        return redirect()->route('admin.comandas.show', $comanda)
            ->with('success', 'Comanda ' . $comanda->number . ' aberta!');
    }

    public function show(Comanda $comanda): View
    {
        $comanda->load(['items.product', 'table', 'openedBy']);
        $products = Product::with('category')
            ->active()
            ->orderBy('category_id')
            ->orderBy('name')
            ->get()
            ->groupBy('category.name');

        return view('admin.comandas.show', compact('comanda', 'products'));
    }

    public function addItem(Request $request, Comanda $comanda): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.001',
            'notes'      => 'nullable|string|max:200',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $total = round($product->price * $validated['quantity'], 2);

        ComandaItem::create([
            'comanda_id'   => $comanda->id,
            'product_id'   => $product->id,
            'added_by'     => auth()->id(),
            'product_name' => $product->name,
            'unit_price'   => $product->price,
            'quantity'     => $validated['quantity'],
            'unit'         => $product->isKg() ? 'kg' : 'un',
            'total'        => $total,
            'notes'        => $validated['notes'] ?? null,
            'status'       => 'pendente',
        ]);

        $comanda->calculateTotal();

        return back()->with('success', 'Item adicionado!');
    }

    public function removeItem(Comanda $comanda, ComandaItem $item): RedirectResponse
    {
        $item->update(['status' => 'cancelado']);
        $comanda->calculateTotal();
        return back()->with('success', 'Item removido!');
    }

    public function close(Request $request, Comanda $comanda): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:dinheiro,pix,cartao_credito,cartao_debito',
            'cash_received'  => 'nullable|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        DB::transaction(function () use ($comanda, $validated) {
            if (isset($validated['discount'])) {
                $comanda->update(['discount' => $validated['discount']]);
                $comanda->calculateTotal();
            }

            $changeAmount = 0;
            if ($validated['payment_method'] === 'dinheiro' && isset($validated['cash_received'])) {
                $changeAmount = max(0, $validated['cash_received'] - $comanda->total);
            }

            $comanda->payments()->create([
                'method'        => $validated['payment_method'],
                'status'        => 'aprovado',
                'amount'        => $comanda->total,
                'cash_received' => $validated['cash_received'] ?? 0,
                'change_amount' => $changeAmount,
                'processed_by'  => auth()->id(),
                'paid_at'       => now(),
            ]);

            $comanda->update([
                'status'    => 'finalizada',
                'closed_at' => now(),
                'closed_by' => auth()->id(),
            ]);

            if ($comanda->table_id) {
                RestaurantTable::find($comanda->table_id)->update(['status' => 'disponivel']);
            }
        });

        return redirect()->route('admin.comandas.index')
            ->with('success', 'Comanda ' . $comanda->number . ' finalizada!');
    }

    public function cancel(Comanda $comanda): RedirectResponse
    {
        if (!$comanda->isOpen()) {
            return back()->with('error', 'Somente comandas abertas podem ser canceladas.');
        }

        $comanda->update(['status' => 'cancelada', 'closed_at' => now(), 'closed_by' => auth()->id()]);

        if ($comanda->table_id) {
            RestaurantTable::find($comanda->table_id)->update(['status' => 'disponivel']);
        }

        return redirect()->route('admin.comandas.index')
            ->with('success', 'Comanda cancelada.');
    }
}
