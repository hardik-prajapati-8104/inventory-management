<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user)) {
            abort(403);
        }

        $notifications = NotificationService::items();

        return view('backend.notifications.index', compact('notifications'));
    }

    /**
     * Manual trigger for the same command the scheduler runs daily — lets an
     * admin confirm mail is configured correctly without waiting for 8am,
     * and lets them see it fire even when there's genuinely nothing pending
     * (--force sends an "all clear" email instead of silently skipping).
     */
    public function sendDigestNow()
    {
        if (is_null($this->user) || ! $this->user->isSuperAdmin()) {
            abort(403, 'Only a Super Admin can trigger the digest manually.');
        }

        Artisan::call('vsp:send-daily-digest', ['--force' => true]);

        session()->flash('success', 'Digest email triggered. '.trim(Artisan::output()));
        return back();
    }
}
