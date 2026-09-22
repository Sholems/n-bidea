<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdateRequest;
use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperAdminSettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::orderBy('key')->get();

        return view('super-admin.settings.index', compact('settings'));
    }

    public function update(SettingUpdateRequest $request): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $setting = Setting::updateOrCreate(
            ['key' => $request->input('key')],
            ['value' => $request->input('value')],
        );

        AuditService::logAction(
            action: 'setting.updated',
            description: "Setting '{$setting->key}' updated to '{$setting->value}' by super admin",
            auditable: $setting,
        );

        return back()->with('success', 'Setting updated successfully.');
    }
}
