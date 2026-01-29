<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Display a listing of the settings.
     */
    public function index(): Response
    {
        $settings = Setting::all()->groupBy('group');

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update the specified settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->storeOnCloudinary('shopwave/settings')->getSecurePath();
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $path, 'type' => 'image', 'group' => 'brand']
                );
            } else {
                // Determine group based on key if creating new
                $setting = Setting::where('key', $key)->first();
                
                // If existing setting is an image and value is null (no new file uploaded), skip update to preserve existing URL
                if ($setting && $setting->type === 'image' && is_null($value)) {
                    continue;
                }

                $group = $setting ? $setting->group : 'general';

                if (!$setting) {
                    if (str_contains($key, 'logo') || str_contains($key, 'favicon')) {
                        $group = 'brand';
                    } elseif (in_array($key, ['currency', 'timezone', 'locale'])) {
                        $group = 'regional';
                    } elseif (in_array($key, ['site_name', 'contact_email', 'seo_description'])) {
                        $group = 'general';
                    } elseif (str_starts_with($key, 'mail_')) {
                        $group = 'email';
                    }
                }

                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => $group]
                );
            }
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
