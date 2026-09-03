<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminsController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('admin.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any admin !');
        }

        $admins = Cache::remember('admins', 10, function () {
            return Admin::with('roles')->orderByDesc('id')->get();
        });

        return view('backend.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('admin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any admin !');
        }

        $roles = Role::where('guard_name', 'admin')->get();

        return view('backend.admins.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('admin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any admin !');
        }

        $request->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'username' => 'required|max:100|unique:admins',
            'email' => 'required|max:100|email|unique:admins',
            'password' => 'required|min:6|confirmed',
            'mobile_number' => 'nullable|max:30',
            'roles' => 'required|array|min:1',
        ]);

        $admin = new Admin();
        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->mobile_number = $request->mobile_number;
        $admin->status = $request->boolean('status', true);
        $admin->login = $request->boolean('login', true);
        $admin->email_notifications = $request->boolean('email_notifications', true);
        $admin->is_super_admin = in_array('Super Admin', (array) $request->roles) ? 1 : 0;
        $admin->save();

        $admin->assignRole($request->roles);

        AuditLog::record(
            action: 'create',
            module: 'Admins',
            subject: $admin,
            description: "Created admin \"{$admin->username}\" with role(s): ".implode(', ', $request->roles),
            new: $admin->only(['username', 'email', 'status', 'login']),
        );

        Cache::forget('admins');

        session()->flash('success', $admin->username.' has been created !!');
        return redirect()->route('admin.admin.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('admin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any admin !');
        }

        $admin = Admin::with('roles')->findOrFail($id);
        $roles = Role::where('guard_name', 'admin')->get();

        return view('backend.admins.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('admin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any admin !');
        }

        $admin = Admin::findOrFail($id);
        $original = $admin->only(['first_name', 'last_name', 'username', 'email', 'status', 'login']);

        $request->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'username' => 'required|max:100|unique:admins,username,'.$id,
            'email' => 'required|max:100|email|unique:admins,email,'.$id,
            'password' => 'nullable|min:6|confirmed',
            'mobile_number' => 'nullable|max:30',
            'roles' => 'required|array|min:1',
        ]);

        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->mobile_number = $request->mobile_number;
        $admin->status = $request->boolean('status', $admin->status);
        $admin->login = $request->boolean('login', $admin->login);
        $admin->email_notifications = $request->boolean('email_notifications', $admin->email_notifications);
        $admin->is_super_admin = in_array('Super Admin', (array) $request->roles) ? 1 : 0;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();
        $admin->syncRoles($request->roles);

        AuditLog::record(
            action: 'update',
            module: 'Admins',
            subject: $admin,
            description: "Updated admin \"{$admin->username}\"",
            old: $original,
            new: $admin->only(['first_name', 'last_name', 'username', 'email', 'status', 'login']),
        );

        Cache::forget('admins');

        session()->flash('success', $admin->username.' has been updated !!');
        return redirect()->route('admin.admin.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('admin.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any admin !');
        }

        // Protect Admin #1 (the seeded Super Admin) from ever being deleted or
        // demoted by another user. Create a new admin for testing instead.
        if ($id === 1) {
            session()->flash('error', 'Sorry !! You are not authorized to delete the primary Super Admin. Please create a new admin if you need to test !');
            return back();
        }

        $admin = Admin::find($id);

        if (! is_null($admin)) {
            AuditLog::record(
                action: 'delete',
                module: 'Admins',
                subject: $admin,
                description: "Deleted admin \"{$admin->username}\"",
                old: $admin->only(['username', 'email']),
            );
            $admin->delete();
        }

        Cache::forget('admins');

        session()->flash('success', 'Admin has been deleted !!');
        return back();
    }

    /**
     * Generic AJAX status toggle (used for Active/Inactive switches across modules).
     */
    public function updateFieldStatus(Request $request, $table, $id, $status, $field = 'status')
    {
        // Whitelist tables this endpoint is allowed to touch — never trust the URL blindly.
        $allowed = ['admins', 'settings', 'menus'];
        if (! in_array($table, $allowed)) {
            abort(403);
        }

        \Illuminate\Support\Facades\DB::table($table)->where('id', $id)->update([$field => $status]);

        return response()->json([
            'success' => true,
            'data' => '',
            'message' => "$table status updated successfully.",
        ], 200);
    }

    public function changePassword()
    {
        if (is_null($this->user)) {
            abort(403, 'Sorry !! You are Unauthorized to change your password !');
        }

        return view('backend.layouts.partials.change-password');
    }
}
