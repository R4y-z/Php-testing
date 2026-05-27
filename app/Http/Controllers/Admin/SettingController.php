<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        // Sincronizar checkboxes (se não veio no request, está desmarcado)
        $booleanKeys = [
            'delivery_enabled', 'payment_dinheiro', 'payment_pix',
            'payment_credito', 'payment_debito', 'kg_enabled',
        ];

        foreach ($booleanKeys as $key) {
            if (!array_key_exists($key, $data)) {
                Setting::set($key, '0');
            }
        }

        return back()->with('success', 'Configurações salvas!');
    }
}
