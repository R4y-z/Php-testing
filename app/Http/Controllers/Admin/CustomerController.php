<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('orders');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->orderBy('name')->paginate(25);
        return view('admin.clients.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load(['orders' => fn($q) => $q->latest()->limit(20), 'addresses']);
        return view('admin.clients.show', compact('customer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'cpf'   => 'nullable|string|max:14',
        ]);

        Customer::create($validated);
        return back()->with('success', 'Cliente cadastrado!');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
        ]);

        $customer->update($validated);
        return back()->with('success', 'Cliente atualizado!');
    }
}
