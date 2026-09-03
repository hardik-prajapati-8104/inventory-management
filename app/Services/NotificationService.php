<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\SparePart;
use App\Models\StockTransfer;
use App\Models\StockAdjustment;

/**
 * Section 38: dashboard/header notifications. Deliberately read-only and
 * computed on demand rather than stored — every one of these is already a
 * query away (low stock, pending approvals, etc.), so there's no separate
 * notifications table to keep in sync. If volume ever makes that too slow,
 * this is the one place to add caching without touching any caller.
 *
 * This is also the single source of truth every low-stock surface reads
 * from — the header bell, the full /notifications page, the dashboard
 * banner, and the daily digest email (SendDailyDigest) all call
 * items() rather than each re-deciding "what counts as low stock" on their
 * own, so Settings → Inventory → Low Stock Notification only has to be
 * checked in one place to turn all four off at once.
 */
class NotificationService
{
    public static function items(): array
    {
        $items = [];

        // Settings → Inventory → "Low Stock Notification". Defaults to
        // enabled (matches the toggle's own default) so a fresh install
        // behaves the same whether or not Settings has been touched yet.
        $lowStockNotificationsEnabled = Setting::get('inventory', 'low_stock_notification', '1') == '1';

        if ($lowStockNotificationsEnabled) {
            $lowStock = SparePart::lowStock()->where('current_stock', '>', 0)->count();
            if ($lowStock > 0) {
                $items[] = self::make('warning', 'exclamation-triangle', self::pluralize($lowStock, 'Product Is', 'Products Are').' Low in Stock', route('admin.stock.low'));
            }

            $outOfStock = SparePart::outOfStock()->count();
            if ($outOfStock > 0) {
                $items[] = self::make('danger', 'x-octagon', self::pluralize($outOfStock, 'Product Is', 'Products Are').' Out of Stock', route('admin.stock.out'));
            }
        }

        $overdueSupplier = Purchase::where('due_amount', '>', 0)->whereNotNull('due_date')->where('due_date', '<', now())->count();
        if ($overdueSupplier > 0) {
            $items[] = self::make('danger', 'cash-coin', "{$overdueSupplier} overdue supplier payment(s)", route('admin.reports.outstanding-suppliers'));
        }

        $customerBalance = Sale::where('due_amount', '>', 0)->count();
        if ($customerBalance > 0) {
            $items[] = self::make('warning', 'wallet2', "{$customerBalance} customer invoice(s) with a balance due", route('admin.reports.outstanding-customers'));
        }

        $pendingPOs = PurchaseOrder::where('status', 'pending')->count();
        if ($pendingPOs > 0) {
            $items[] = self::make('info', 'file-earmark-text', "{$pendingPOs} purchase order(s) awaiting approval", route('admin.purchase-orders.index'));
        }

        $pendingTransfers = StockTransfer::where('status', 'pending')->count();
        if ($pendingTransfers > 0) {
            $items[] = self::make('info', 'truck', "{$pendingTransfers} stock transfer(s) awaiting approval", route('admin.stock-transfers.index'));
        }

        $pendingAdjustments = StockAdjustment::where('status', 'pending')->count();
        if ($pendingAdjustments > 0) {
            $items[] = self::make('info', 'sliders', "{$pendingAdjustments} stock adjustment(s) awaiting approval", route('admin.stock-adjustments.index'));
        }

        $discontinued = SparePart::where('status', 'discontinued')->count();
        if ($discontinued > 0) {
            $items[] = self::make('secondary', 'archive', "{$discontinued} discontinued part(s) still in the catalogue", route('admin.spare-parts.index'));
        }

        return $items;
    }

    /**
     * True when the header bell/notifications-page/dashboard-banner/daily
     * digest should show anything low-stock related at all. Exposed
     * separately from items() so the dashboard banner can render its own
     * "N Products Are Low in Stock" markup (styled differently from a
     * regular notification row) while still respecting the same setting
     * and the same count.
     */
    public static function lowStockNotificationsEnabled(): bool
    {
        return Setting::get('inventory', 'low_stock_notification', '1') == '1';
    }

    private static function pluralize(int $count, string $singular, string $plural): string
    {
        return $count.' '.($count === 1 ? $singular : $plural);
    }

    private static function make(string $severity, string $icon, string $message, string $url): array
    {
        return compact('severity', 'icon', 'message', 'url');
    }
}
