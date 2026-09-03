document.addEventListener('DOMContentLoaded', function () {
    var nestedSortables = [].slice.call(document.querySelectorAll('.menu-manage-tree'));

    nestedSortables.forEach(function (el) {
        new Sortable(el, {
            group: 'menu-nesting',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            handle: '.drag-handle',
            onEnd: persistOrder,
        });
    });

    document.querySelectorAll('.toggle-status-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            fetch(btn.getAttribute('data-url'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.MENU_CSRF_TOKEN,
                    'Accept': 'application/json',
                },
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        var icon = btn.querySelector('i');
                        icon.classList.toggle('bi-toggle-on', data.is_active);
                        icon.classList.toggle('bi-toggle-off', !data.is_active);
                    }
                });
        });
    });

    function persistOrder() {
        var items = [];

        document.querySelectorAll('.menu-manage-tree').forEach(function (list) {
            var parentId = list.getAttribute('data-parent-id') || null;
            var children = [].slice.call(list.children).filter(function (li) {
                return li.classList.contains('menu-manage-item');
            });

            children.forEach(function (li, index) {
                items.push({
                    id: li.getAttribute('data-id'),
                    parent_id: parentId,
                    sort_order: index,
                });
            });
        });

        fetch(window.MENU_REORDER_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.MENU_CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ items: items }),
        });
    }
});
