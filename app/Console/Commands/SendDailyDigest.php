<?php

namespace App\Console\Commands;

use App\Mail\DailyDigestMail;
use App\Models\Admin;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Section 38's "Email notification" delivery channel. Deliberately thin:
 * NotificationService::items() is the single source of truth for WHAT gets
 * reported (already used by the header bell and the /notifications page);
 * this command is just one more consumer of it, choosing to email instead
 * of render.
 */
class SendDailyDigest extends Command
{
    protected $signature = 'vsp:send-daily-digest {--force : Send even if there is nothing to report}';
    protected $description = 'Emails every admin with email notifications enabled a summary of low stock, overdue balances, and pending approvals.';

    public function handle(): int
    {
        $items = NotificationService::items();

        if (empty($items) && ! $this->option('force')) {
            $this->info('Nothing to report today — skipping (use --force to send an "all clear" email anyway).');
            return self::SUCCESS;
        }

        $recipients = Admin::where('status', 1)
            ->where('email_notifications', 1)
            ->whereNotNull('email')
            ->get();

        if ($recipients->isEmpty()) {
            $this->warn('No admins have email notifications enabled — nothing sent.');
            return self::SUCCESS;
        }

        foreach ($recipients as $admin) {
            Mail::to($admin->email)->send(new DailyDigestMail($items, $admin));
        }

        $this->info(count($items)." item(s), sent to ".$recipients->count()." recipient(s): ".$recipients->pluck('email')->implode(', '));

        return self::SUCCESS;
    }
}
