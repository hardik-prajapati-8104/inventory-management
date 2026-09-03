<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('audit-log.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Audit Logs !');
        }

        $logs = AuditLog::with('admin')->latest()->paginate(15);

        return view('backend.audit-logs.index', compact('logs'));
    }
}
