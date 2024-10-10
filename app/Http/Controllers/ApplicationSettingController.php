<?php

namespace App\Http\Controllers;

use App\Models\ApplicationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationSettingController extends Controller
{
    public function index(Request $request)
    {

        return view('app_setting.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'app_address' => 'nullable|string|max:255',
            'japati_token' => 'nullable|string|max:255',
            'japati_gateway' => 'nullable|string|max:255',
            'japati_url' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('app_logo') && $request->file('app_logo')->isValid()) {
            $path = $request->file('app_logo')->store('logos', 'public');
        } else {
            return redirect()->back()->withErrors(['app_logo' => 'Invalid logo file.']);
        }

        ApplicationSetting::create([
            'app_name' => $request->app_name,
            'app_logo' => $path,
            'app_address' => $request->app_address,
            'japati_token' => $request->japati_token,
            'japati_gateway' => $request->japati_gateway,
            'japati_url' => $request->japati_url,
        ]);
        return redirect()->route('application-setting.index')->with('success', 'Application setting created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'app_address' => 'nullable|string|max:255',
            'japati_token' => 'nullable|string|max:255',
            'japati_gateway' => 'nullable|string|max:255',
            'japati_url' => 'nullable|string|max:255',
        ]);

        $setting = ApplicationSetting::first();
        if ($request->hasFile('app_logo')) {
            $path = $request->file('app_logo')->store('logos', 'public');
            $setting->update([
                'app_name' => $request->app_name,
                'app_logo' => $path,
                'app_address' => $request->app_address,
                'japati_token' => $request->japati_token,
                'japati_gateway' => $request->japati_gateway,
                'japati_url' => $request->japati_url,
            ]);
        } else {
            $setting->update($request->only(['app_name', 'app_address', 'japati_token', 'japati_gateway', 'japati_url']));
        }

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
