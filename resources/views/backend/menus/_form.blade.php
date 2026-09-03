@php
    $menu = $menu ?? null;
    $old = fn($field, $default = null) => old($field, $menu->$field ?? $default);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label>Type<span class="text-error">*</span></label>
        <select name="type" id="menuType" class="form-select" required>
            <option value="link" {{ $old('type', 'link') === 'link' ? 'selected' : '' }}>Link (clickable nav item)
            </option>
            <option value="heading" {{ $old('type') === 'heading' ? 'selected' : '' }}>Heading (section label, e.g.
                "Inventory")</option>
        </select>
        <div class="form-text"> A heading is a non-clickable section label. It can contain menu items below it.</div>
    </div>

    <div class="col-md-6">
        <label>Name<span class="text-error">*</span></label>
        <input type="text" name="name" required maxlength="100" class="form-control" value="{{ $old('name') }}"
            placeholder="e.g. Spare Parts">
        @error('name')
            <div class="text-error small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6" id="parentField">

        <label for="parent_id">
            Parent Menu
        </label>

        <select name="parent_id" id="parent_id" class="form-select">

            <option value="">None (top level)</option>

            @foreach ($parents as $p)
                <option value="{{ $p->id }}"
                    @selected((string) $old('parent_id') === (string) $p->id)>
                    {{ $p->name }}
                </option>
            @endforeach

        </select>

        <div class="form-text">
             Select a top-level menu or section heading to place this item inside its menu group.
            Leave as <strong>None</strong> to create a top-level menu.
        </div>

        @error('parent_id')
            <div class="text-error small">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="col-md-6">
        <label>Icon (Bootstrap Icon class)</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi" id="iconPreview">{{ '' }}</i></span>
            <input type="text" name="icon" id="iconInput" maxlength="50" class="form-control"
                value="{{ $old('icon') }}" placeholder="e.g. bi-gear-wide-connected">
        </div>
        <div class="form-text">Browse classes at <a href="https://icons.getbootstrap.com" target="_blank"
                rel="noopener">icons.getbootstrap.com</a> — paste the "bi-..." class shown there.</div>
    </div>

    <div class="col-12">
        <hr class="my-1">
    </div>

    <div class="col-md-6" id="routeNameField">
        <label>Route Name</label>
        <input type="text" name="route_name" id="routeNameInput" maxlength="150" class="form-control"
            value="{{ $old('route_name') }}" placeholder="e.g. admin.spare-parts.index">
        <div class="form-text" id="routeNameStatus">Preferred over URL below — resolved with Laravel's
            <code>route()</code> helper, so it survives URL prefix changes. Leave URL blank if you set this.</div>
        @error('route_name')
            <div class="text-error small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6" id="urlField">
        <label>URL</label>
        <input type="text" name="url" maxlength="255" class="form-control" value="{{ $old('url') }}"
            placeholder="e.g. https://example.com or /admin/custom-page">
        <div class="form-text">Only used when Route Name above is blank or invalid.</div>
        @error('url')
            <div class="text-error small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6" id="activePatternField">
        <label>Active-State Pattern <span class="text-muted small">(optional)</span></label>
        <input type="text" name="active_pattern" maxlength="150" class="form-control"
            value="{{ $old('active_pattern') }}" placeholder="e.g. admin.spare-parts.*">
        <div class="form-text">Wildcard route pattern that keeps this item highlighted on sub-pages. Auto-filled from
            Route Name if left blank.</div>
    </div>

    <div class="col-md-6">
        <label>Required Permission</label>
        <select name="permission" class="form-select">
            <option value="">None — visible to any logged-in admin</option>
            @foreach ($permissions as $perm)
                <option value="{{ $perm }}" {{ $old('permission') === $perm ? 'selected' : '' }}>
                    {{ $perm }}</option>
            @endforeach
        </select>
        <div class="form-text">Only admins who have this permission (or Super Admins) will see this item in the sidebar.
        </div>
        @error('permission')
            <div class="text-error small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <hr class="my-1">
    </div>

    <div class="col-md-4" id="targetField">
        <label>Open In</label>
        <select name="target" class="form-select">
            <option value="_self" {{ $old('target', '_self') === '_self' ? 'selected' : '' }}>Same tab</option>
            <option value="_blank" {{ $old('target') === '_blank' ? 'selected' : '' }}>New tab</option>
        </select>
    </div>

    <div class="col-md-4">
        <label>Sort Order</label>
        <input type="number" name="sort_order" min="0" class="form-control"
            value="{{ $old('sort_order', 0) }}">
        <div class="form-text">Lower numbers appear first. You can also drag items into order from the list.</div>
    </div>

    <div class="col-md-4">
        <label class="d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" role="switch" name="status" value="1"
                id="statusSwitch" {{ $old('status', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="statusSwitch">Active (shown in sidebar)</label>
        </div>
    </div>
</div>

<script>
    (function() {
        const typeSelect = document.getElementById('menuType');
        const parentField = document.getElementById('parentField');
        const routeField = document.getElementById('routeNameField');
        const urlField = document.getElementById('urlField');
        const activeField = document.getElementById('activePatternField');
        const targetField = document.getElementById('targetField');

        function toggleLinkOnlyFields() {
            const isHeading = typeSelect.value === 'heading';
            [parentField, routeField, urlField, activeField, targetField].forEach(function(el) {
                if (!el) return;
                el.style.display = isHeading ? 'none' : '';
            });
        }
        typeSelect?.addEventListener('change', toggleLinkOnlyFields);
        toggleLinkOnlyFields();

        const iconInput = document.getElementById('iconInput');
        const iconPreview = document.getElementById('iconPreview');

        function updateIconPreview() {
            iconPreview.className = 'bi ' + (iconInput.value.trim() || 'bi-question-circle');
        }
        iconInput?.addEventListener('input', updateIconPreview);
        updateIconPreview();

        const routeInput = document.getElementById('routeNameInput');
        const routeStatus = document.getElementById('routeNameStatus');
        const checkRouteUrl = @json(route('admin.menus.check-route'));
        let routeCheckTimer = null;
        routeInput?.addEventListener('input', function() {
            clearTimeout(routeCheckTimer);
            const name = routeInput.value.trim();
            if (!name) {
                routeStatus.innerHTML =
                    'Preferred over URL below — resolved with Laravel\'s <code>route()</code> helper, so it survives URL prefix changes. Leave URL blank if you set this.';
                routeStatus.classList.remove('text-success', 'text-error');
                return;
            }
            routeCheckTimer = setTimeout(function() {
                fetch(checkRouteUrl + '?route_name=' + encodeURIComponent(name))
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(data) {
                        routeStatus.classList.remove('text-success', 'text-error');
                        if (data.exists) {
                            routeStatus.classList.add('text-success');
                            routeStatus.textContent = '✓ Route found (resolves to ' + data.url +
                                ')';
                        } else {
                            routeStatus.classList.add('text-error');
                            routeStatus.textContent = '✗ No route named "' + name +
                                '" exists. Double check spelling, or use the URL field instead.';
                        }
                    })
                    .catch(function() {
                        /* non-fatal — validation still happens on submit */ });
            }, 400);
        });
    })();
</script>
