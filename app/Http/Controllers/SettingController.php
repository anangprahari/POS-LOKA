<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('settings.edit');
    }

    /**
     * Store the settings in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Basic validation for settings
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'required|string',
            'currency_symbol' => 'required|string|max:10',
            'warning_quantity' => 'required|numeric|min:1',
        ]);

        // Original functionality - save all settings
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            $setting = Setting::firstOrCreate(['key' => $key]);
            $setting->value = $value;
            $setting->save();
        }

        // Redirect with success message
        return redirect()->route('settings.index')
            ->with('success', __('Settings updated successfully'));
    }
}
