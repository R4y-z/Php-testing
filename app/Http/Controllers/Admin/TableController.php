<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = RestaurantTable::with('activeComanda')->active()->orderBy('number')->get();
        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number'   => 'required|string|max:10|unique:restaurant_tables,number',
            'name'     => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:100',
        ]);

        $validated['active'] = true;
        $validated['status'] = 'disponivel';

        RestaurantTable::create($validated);

        return back()->with('success', 'Mesa criada!');
    }

    public function update(Request $request, RestaurantTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'number'   => 'required|string|max:10|unique:restaurant_tables,number,' . $table->id,
            'name'     => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:100',
            'status'   => 'required|in:disponivel,ocupada,reservada,manutencao',
        ]);

        $table->update($validated);
        return back()->with('success', 'Mesa atualizada!');
    }

    public function updateStatus(Request $request, RestaurantTable $table): RedirectResponse
    {
        $request->validate(['status' => 'required|in:disponivel,ocupada,reservada,manutencao']);
        $table->update(['status' => $request->status]);
        return back()->with('success', 'Status da mesa atualizado!');
    }

    public function destroy(RestaurantTable $table): RedirectResponse
    {
        if ($table->activeComanda()->exists()) {
            return back()->with('error', 'Mesa possui comanda aberta. Feche antes de excluir.');
        }
        $table->delete();
        return back()->with('success', 'Mesa excluída!');
    }
}
