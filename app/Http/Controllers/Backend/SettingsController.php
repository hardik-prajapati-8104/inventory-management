<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('settings.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Settings !');
        }

        $company = Setting::group('company');
        $invoice = Setting::group('invoice');
        $inventory = Setting::group('inventory');
        $general = Setting::group('general');

        return view('backend.settings.index', compact('company', 'invoice', 'inventory', 'general'));
    }

    public function update(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('settings.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Settings !');
        }

        $request->validate([
            'group' => 'required|in:company,invoice,inventory,general',
            'fields' => 'required|array',
        ]);

        foreach ($request->fields as $key => $value) {
            Setting::set($request->group, $key, $value);
        }

        AuditLog::record(
            action: 'update',
            module: 'Settings',
            description: "Updated \"{$request->group}\" settings",
            new: $request->fields,
        );

        session()->flash('success', ucfirst($request->group).' settings updated successfully !!');
        return back();
    }
}
