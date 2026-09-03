<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });

    // ---- Vehicle compatibility: Select2 with AJAX search (Section 7/8) ----
    function initVehicleSelect($el) {
        $el.select2({
            width: '100%',
            placeholder: 'Type make, model, or year…',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('admin.vehicles.search') }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data.results }),
            },
        });
    }
    $('.vehicle-select').each(function () { initVehicleSelect($(this)); });

    // ---- Add / remove compatibility rows ----
    $('#addCompatRow').on('click', function () {
        const template = document.getElementById('compatRowTemplate').content.cloneNode(true);
        $('#compatibilityRows').append(template);
        initVehicleSelect($('#compatibilityRows tr:last .vehicle-select'));
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });

    // ---- Duplicate detection (Section 37) ----
    $('[data-dup-field]').on('blur', function () {
        const field = $(this).data('dup-field');
        const value = $(this).val();
        const $warning = $(this).closest('.col-md-4, .col-md-3').find('.dup-warning');
        const excludeId = $('#sparePartId').val() || '';

        if (! value) { $warning.addClass('d-none'); return; }

        $.get('{{ route('admin.spare-parts.check-duplicate') }}', { field, value, exclude_id: excludeId }, function (res) {
            if (res.exists) {
                $warning.removeClass('d-none').html(
                    `⚠ Already used by <strong>${res.part.name}</strong> (SKU: ${res.part.sku}, Stock: ${res.part.current_stock}) — ` +
                    `<a href="${res.part.edit_url}" target="_blank">view/edit it</a>`
                );
            } else {
                $warning.addClass('d-none');
            }
        });
    });

    // ---- Profit margin live calculation (Section 32) ----
    function updateProfitMargin() {
        const purchase = parseFloat($('#purchase_price').val()) || 0;
        const retail = parseFloat($('#retail_price').val()) || 0;
        if (purchase <= 0) { $('#profitMarginDisplay').val('—'); return; }
        const profit = retail - purchase;
        const pct = (profit / purchase * 100).toFixed(2);
        $('#profitMarginDisplay').val(`₹${profit.toFixed(2)} (${pct}%)`);
    }
    $('#purchase_price, #retail_price').on('input', updateProfitMargin);
    updateProfitMargin();

    // ---- Warehouse -> Rack -> Shelf -> Bin cascading dropdowns ----
    function cascadeLocation(sourceId, targetId, urlBase, placeholder) {
        $(`#${sourceId}`).on('change', function () {
            const id = $(this).val();
            const $target = $(`#${targetId}`);
            $target.html('<option value="">Loading…</option>').prop('disabled', true);
            if (! id) { $target.html(`<option value="">${placeholder}</option>`); return; }

            $.get(`${urlBase}/${id}`, function (items) {
                $target.html('<option value="">Select</option>' + items.map(i => `<option value="${i.id}">${i.name}</option>`).join(''));
                $target.prop('disabled', false);
            }, 'json');
        });
    }
    cascadeLocation('warehouse_id', 'rack_id', '{{ url('admin/warehouses/ajax/racks-for') }}', 'Select Warehouse first');
    cascadeLocation('rack_id', 'shelf_id', '{{ url('admin/warehouses/ajax/shelves-for') }}', 'Select Rack first');
    cascadeLocation('shelf_id', 'bin_id', '{{ url('admin/warehouses/ajax/bins-for') }}', 'Select Shelf first');

    // ---- Quick Add Category ----
    $('#quickAddCategoryBtn').on('click', function () {
        const name = $('#quickCategoryName').val();
        if (! name) return;
        $.post('{{ route('admin.categories.store') }}', { name, _token: '{{ csrf_token() }}' }, function (res) {
            $('#category_id').append(new Option(res.category.name, res.category.id, true, true)).trigger('change');
            $('#quickAddCategory').modal('hide');
            $('#quickCategoryName').val('');
        }, 'json');
    });

    // ---- Quick Add Brand ----
    $('#quickAddBrandBtn').on('click', function () {
        const name = $('#quickBrandName').val();
        if (! name) return;
        $.post('{{ route('admin.brands.store') }}', { name, _token: '{{ csrf_token() }}' }, function (res) {
            $('#brand_id').append(new Option(res.brand.name, res.brand.id, true, true)).trigger('change');
            $('#quickAddBrand').modal('hide');
            $('#quickBrandName').val('');
        }, 'json');
    });

    // ---- Quick Add Vehicle (Make already exists -> cascades Model -> creates Variant) ----
    $('#quickVehicleMake').on('change', function () {
        const makeId = $(this).val();
        const $model = $('#quickVehicleModel');
        $model.html('<option value="">Loading…</option>').prop('disabled', true);
        if (! makeId) { $model.html('<option value="">Select Make first</option>'); return; }

        $.get(`{{ url('admin/vehicles/makes') }}/${makeId}/models`, function (models) {
            $model.html('<option value="">Select Model</option>' + models.map(m => `<option value="${m.id}">${m.name}</option>`).join(''));
            $model.prop('disabled', false);
        }, 'json');
    });

    $('#quickAddVehicleBtn').on('click', function () {
        const payload = {
            vehicle_model_id: $('#quickVehicleModel').val(),
            name: $('#quickVehicleVariantName').val(),
            start_year: $('#quickVehicleStartYear').val(),
            end_year: $('#quickVehicleEndYear').val(),
            _token: '{{ csrf_token() }}',
        };
        if (! payload.vehicle_model_id || ! payload.name) {
            alert('Please select a model and enter a variant name.');
            return;
        }
        $.post('{{ route('admin.vehicles.variants.store') }}', payload, function (res) {
            // Add a fresh compatibility row pre-selected with the new variant
            const template = document.getElementById('compatRowTemplate').content.cloneNode(true);
            $('#compatibilityRows').append(template);
            const $newSelect = $('#compatibilityRows tr:last .vehicle-select');
            initVehicleSelect($newSelect);
            $newSelect.append(new Option(res.variant.name, res.variant.id, true, true)).trigger('change');
            $('#quickAddVehicle').modal('hide');
        }, 'json');
    });
});
</script>
