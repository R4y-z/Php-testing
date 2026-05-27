<?php

namespace App\Livewire;

use App\Models\ComandaItem;
use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class KitchenBoard extends Component
{
    public bool $autoRefresh = true;
    public int $refreshInterval = 30;

    public function render()
    {
        $orders = Order::with(['items.product', 'table'])
            ->whereIn('status', ['confirmado', 'preparando'])
            ->latest()
            ->get();

        $comandaItems = ComandaItem::with(['comanda.table', 'product'])
            ->where('status', 'pendente')
            ->latest()
            ->limit(50)
            ->get();

        return view('livewire.kitchen-board', compact('orders', 'comandaItems'));
    }

    public function markPreparing(int $orderId): void
    {
        $order = Order::find($orderId);
        if ($order && $order->status === 'confirmado') {
            $order->update(['status' => 'preparando', 'prepared_at' => now()]);
            $this->dispatch('order-updated');
        }
    }

    public function markReady(int $orderId): void
    {
        $order = Order::find($orderId);
        if ($order && $order->status === 'preparando') {
            $order->update(['status' => 'pronto', 'ready_at' => now()]);
            $this->dispatch('order-updated');
        }
    }

    public function markItemPreparing(int $itemId): void
    {
        ComandaItem::find($itemId)?->update(['status' => 'preparando']);
    }

    public function markItemReady(int $itemId): void
    {
        ComandaItem::find($itemId)?->update(['status' => 'pronto']);
    }
}
