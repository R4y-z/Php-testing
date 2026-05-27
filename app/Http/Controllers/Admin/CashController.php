<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\Comanda;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashController extends Controller
{
    public function index(): View
    {
        $currentSession = CashSession::getCurrent();

        $openComandas = Comanda::with(['table', 'openedBy'])
            ->whereIn('status', ['aberta', 'fechamento'])
            ->orderBy('opened_at')
            ->get();

        $todayPayments = [];
        if ($currentSession) {
            $todayPayments = $currentSession->payments()
                ->where('status', 'aprovado')
                ->selectRaw('method, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('method')
                ->get();
        }

        $recentComandas = Comanda::with(['table'])
            ->where('status', 'finalizada')
            ->whereDate('created_at', today())
            ->latest('closed_at')
            ->limit(10)
            ->get();

        return view('admin.cash.index', compact(
            'currentSession', 'openComandas', 'todayPayments', 'recentComandas'
        ));
    }

    public function openSession(Request $request): RedirectResponse
    {
        if (CashSession::getCurrent()) {
            return back()->with('error', 'Já existe um caixa aberto.');
        }

        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        CashSession::create([
            'opened_by'       => auth()->id(),
            'opening_balance' => $validated['opening_balance'],
            'notes'           => $validated['notes'] ?? null,
            'status'          => 'aberto',
            'opened_at'       => now(),
        ]);

        return back()->with('success', 'Caixa aberto com sucesso!');
    }

    public function closeSession(Request $request): RedirectResponse
    {
        $session = CashSession::getCurrent();
        if (!$session) {
            return back()->with('error', 'Nenhum caixa aberto.');
        }

        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $totalPayments   = $session->payments()->where('status', 'aprovado')->sum('amount');
        $expectedBalance = $session->opening_balance + $totalPayments;
        $difference      = $validated['closing_balance'] - $expectedBalance;

        $session->update([
            'closing_balance'  => $validated['closing_balance'],
            'expected_balance' => $expectedBalance,
            'difference'       => $difference,
            'status'           => 'fechado',
            'closed_by'        => auth()->id(),
            'closed_at'        => now(),
            'notes'            => $validated['notes'] ?? $session->notes,
        ]);

        return back()->with('success', 'Caixa fechado. Diferença: R$ ' . number_format($difference, 2, ',', '.'));
    }
}
