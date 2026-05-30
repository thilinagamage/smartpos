<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'shop_name' => Setting::get('shop_name', 'SmartPOS'),
            'shop_email' => Setting::get('shop_email', ''),
            'shop_phone' => Setting::get('shop_phone', ''),
            'shop_address' => Setting::get('shop_address', ''),
            'tax_percentage' => Setting::get('tax_percentage', 0),
            'currency' => Setting::get('currency', 'USD'),
            'receipt_footer' => Setting::get('receipt_footer', 'Thank you for your purchase!'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_email' => 'nullable|email',
            'shop_phone' => 'nullable|string|max:20',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|string|max:10',
        ]);

        $settings = [
            'shop_name',
            'shop_email',
            'shop_phone',
            'shop_address',
            'tax_percentage',
            'currency',
            'receipt_footer',
        ];

        foreach ($settings as $key) {
            Setting::set($key, $request->get($key));
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
