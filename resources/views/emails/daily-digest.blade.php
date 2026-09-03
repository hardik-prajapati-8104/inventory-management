<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Summary</title>
</head>
<body style="margin:0; padding:0; background:#f7f5f0; font-family: Arial, Helvetica, sans-serif; color:#2a2620;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f7f5f0; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="580" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #e7e1d4;">

                    <tr>
                        <td style="background: linear-gradient(135deg, #6b4d1f, #aa8038); padding: 24px 28px;">
                            <span style="color:#fff; font-size: 1.1rem; font-weight: 700;">Vehicle Spare Parts Inventory</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 24px 28px 8px;">
                            <p style="margin:0 0 4px; font-size: .9rem; color:#6b6459;">Hi {{ $recipientName }},</p>
                            <h2 style="margin: 4px 0 16px; font-size: 1.2rem;">
                                @if (count($items))
                                    Here's what needs your attention today
                                @else
                                    All caught up — nothing needs attention today
                                @endif
                            </h2>
                        </td>
                    </tr>

                    @forelse ($items as $item)
                    <tr>
                        <td style="padding: 0 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 1px solid #f0ece0;">
                                <tr>
                                    <td style="padding: 12px 0; font-size: .9rem;">
                                        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:
                                            {{ match($item['severity']) { 'danger' => '#c0392b', 'warning' => '#d4a017', 'info' => '#3f7d9e', default => '#8c8272' } }};
                                            margin-right:8px;"></span>
                                        {{ $item['message'] }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td style="padding: 8px 28px 24px; font-size: .9rem; color:#6b6459;">
                            No low stock, overdue balances, or pending approvals right now.
                        </td>
                    </tr>
                    @endforelse

                    <tr>
                        <td style="padding: 24px 28px;">
                            <a href="{{ route('admin.dashboard') }}" style="display:inline-block; background:#aa8038; color:#fff; text-decoration:none; padding: 10px 20px; border-radius:8px; font-size:.85rem; font-weight:600;">
                                Open Dashboard
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 16px 28px; background:#faf5ea; font-size:.75rem; color:#8c8272;">
                            You're receiving this because email notifications are enabled for your account. A Super Admin can turn this off from Users &amp; Permissions → your admin profile.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
