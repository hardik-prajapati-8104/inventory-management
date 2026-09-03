<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>Part</th>
                <th>Category</th>
                <th>Current Stock</th>
                @if ($mode === 'low' || $mode === 'out')
                    <th>Minimum Stock</th>
                    <th>Status</th>
                @elseif ($mode === 'damaged')
                    <th>Damaged Stock</th>
                @endif
                <th width="10%">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($spareParts as $part)
            <tr>
                <td>
                    <div class="fw-medium">{{ $part->name }}</div>
                    <div class="small text-muted">{{ $part->part_number }}</div>
                </td>
                <td>{{ $part->category->name ?? '-' }}</td>
                <td>
                    @if ($mode === 'out')
                        <span class="badge bg-danger">{{ $part->current_stock }}</span>
                    @else
                        <span class="badge" style="background:var(--vsp-warning)">{{ $part->current_stock }} {{ $part->unit->short_code ?? '' }}</span>
                    @endif
                </td>
                @if ($mode === 'low' || $mode === 'out')
                    <td>{{ $part->minimum_stock }}</td>
                    <td>
                        @if ($mode === 'out')
                            <span class="badge bg-danger">⚠ OUT OF STOCK</span>
                        @else
                            <span class="badge" style="background:var(--vsp-warning)">⚠ LOW STOCK</span>
                        @endif
                    </td>
                @elseif ($mode === 'damaged')
                    <td>{{ $part->damaged_stock }}</td>
                @endif
                <td>
                    @can('spare-part.edit')
                    <a href="{{ route('admin.spare-parts.edit', $part->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    @endcan
                    @can('stock-adjustment.create')
                    <a href="{{ route('admin.stock-adjustments.create') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-sliders"></i></a>
                    @endcan
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">{{ $emptyMessage ?? 'Nothing to show.' }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $spareParts->links() }}
