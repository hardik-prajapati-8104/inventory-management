<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menu Management: the sidebar (`sidebar.blade.php`) no longer hard-codes its
 * links — it's rendered from this table instead, so menus can be created,
 * reordered, nested, permission-gated, and enabled/disabled from the admin
 * panel without touching a Blade file.
 *
 * Two `type`s share one self-referencing tree:
 *   - 'heading' — a non-clickable section label (what used to be the plain
 *     `<li class="vsp-sidebar__heading">Master Data</li>` lines). Always
 *     top-level (no parent, no children rendered as a dropdown under it —
 *     its "children" are simply the items that follow it in sort order).
 *   - 'link'    — an actual clickable nav item. Top-level links render
 *     directly in the sidebar; links with a parent_id pointing at another
 *     'link' render as a collapsible dropdown under that parent (arbitrary
 *     depth is supported by the schema/model, the UI styles two levels).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->cascadeOnDelete();

            $table->enum('type', ['heading', 'link'])->default('link');
            $table->string('name', 100);
            $table->string('icon', 50)->nullable(); // bootstrap-icon class, e.g. "bi-boxes"

            // Exactly one of these two drives where a link goes. route_name
            // takes priority when both are set (resolved with route(), and
            // silently falls back to '#' if the named route doesn't exist —
            // see Menu::resolveUrl()).
            $table->string('url', 255)->nullable();
            $table->string('route_name', 150)->nullable();

            // Wildcard route pattern (e.g. "admin.spare-parts.*") used to
            // decide "is this menu item active" and, for a child link, to
            // auto-expand its parent's dropdown. Auto-derived from
            // route_name at save time when left blank — see Menu::booted().
            $table->string('active_pattern', 150)->nullable();

            // Spatie permission name (guard 'admin'), e.g. "spare-part.view".
            // Null means "visible to any authenticated admin" — same
            // open-by-default rule the rest of the app uses for ungated
            // pages like Dashboard.
            $table->string('permission', 100)->nullable();

            $table->integer('sort_order')->default(0);
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->boolean('status')->default(1);

            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
