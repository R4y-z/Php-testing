<?php

namespace App\Livewire;

use App\Models\Comanda;
use App\Models\ComandaItem;
use App\Models\Product;
use App\Models\RestaurantTable;
use Livewire\Attributes\On;
use Livewire\Component;

class ComandaManager extends Component
{
    public ?int $comandaId = null;
    public string $search = '';
    public int $selectedProductId = 0;
    public string $quantity = '1';
    public string $notes = '';
    public bool $showAddItem = false;
    public bool $showCloseModal = false;
    public string $paymentMethod = 'dinheiro';
    public string $cashReceived = '';
    public string $discount = '0';

    public function render()
    {
        $comanda = $this->comandaId ? Comanda::with(['items.product', 'table'])->find($this->comandaId) : null;

        $products = [];
        if ($this->showAddItem) {
            $products = Product::with('category')
                ->active()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        return view('livewire.comanda-manager', compact('comanda', 'products'));
    }

    public function selectComanda(int $id): void
    {
        $this->comandaId  = $id;
        $this->showAddItem = false;
    }

    public function addItem(): void
    {
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'quantity'          => 'required|numeric|min:0.001',
        ]);

        $product = Product::findOrFail($this->selectedProductId);
        $qty     = (float) $this->quantity;
        $total   = round($product->price * $qty, 2);

        ComandaItem::create([
            'comanda_id'   => $this->comandaId,
            'product_id'   => $product->id,
            'added_by'     => auth()->id(),
            'product_name' => $product->name,
            'unit_price'   => $product->price,
            'quantity'     => $qty,
            'unit'         => $product->isKg() ? 'kg' : 'un',
            'total'        => $total,
            'notes'        => $this->notes ?: null,
            'status'       => 'pendente',
        ]);

        $comanda = Comanda::find($this->comandaId);
        $comanda->calculateTotal();

        $this->reset(['selectedProductId', 'quantity', 'notes', 'search']);
        $this->showAddItem = false;
        $this->dispatch('item-added');
    }

    public function removeItem(int $itemId): void
    {
        $item = ComandaItem::find($itemId);
        if ($item && $item->comanda_id === $this->comandaId) {
            $item->update(['status' => 'cancelado']);
            Comanda::find($this->comandaId)->calculateTotal();
        }
    }

    public function getChangeAmount(): float
    {
        $total    = Comanda::find($this->comandaId)?->total ?? 0;
        $discount = (float) ($this->discount ?: 0);
        $received = (float) ($this->cashReceived ?: 0);
        return max(0, $received - ($total - $discount));
    }
}
