@extends('layouts.app')

@section('content')
<style>
/* Styling khusus untuk DataTables menyesuaikan tema */
.dt-buttons .dt-button {
    background: white;
    border: 1px solid var(--border-color);
    color: var(--text-main);
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.85rem;
    margin-right: 0.25rem;
    cursor: pointer;
}
.dt-buttons .dt-button:hover {
    background: var(--bg-main);
}
.dataTables_wrapper .dataTables_length select {
    padding: 0.3rem 0.5rem;
    border-radius: 4px;
    border: 1px solid var(--border-color);
}
.dataTables_wrapper .dataTables_filter input {
    padding: 0.3rem 0.5rem;
    border-radius: 4px;
    border: 1px solid var(--border-color);
    margin-left: 0.5rem;
}
.dt-buttons-wrapper {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 0.5rem;
}
.dt-controls-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.dt-bottom-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}
.dataTables_wrapper .dataTables_paginate {
    display: flex; border-radius: 4px; overflow: hidden; border: 1px solid var(--border-color);
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.375rem 0.75rem; border: none; border-right: 1px solid var(--border-color);
    cursor: pointer; background: white; color: var(--text-main) !important; text-decoration: none; margin: 0 !important; border-radius: 0 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:last-child {
    border-right: none;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #0d6efd; color: white !important; font-weight: bold;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled) {
    background: #f8f9fa;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background: #f3f4f6; color: #9ca3af !important; cursor: not-allowed;
}
</style>

<div class="dashboard-header">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 1.5rem; color: var(--text-main); font-weight: 600; margin-bottom: 0.5rem;">Detail {{ $greenhouse->name }}</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Manajemen 1.000 titik tanam di dalam greenhouse.</p>
        </div>
        <a href="/paprika/greenhouses" class="btn btn-secondary" style="background: white; border: 1px solid var(--border-color); color: var(--text-main); padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card" style="background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border-color); padding: 1.5rem; margin-top: 1.5rem;">
    <form action="/paprika/plants/bulk-update" method="POST" id="bulkUpdateForm">
        @csrf
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; background: var(--bg-main); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
            <div style="font-weight: 600; color: var(--text-main);">
                Aksi Massal (Bulk Update)
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <select name="new_status" class="form-control" style="padding: 0.5rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-main); min-width: 150px;">
                    <option value="">-- Ubah Status --</option>
                    <option value="kosong">Kosong</option>
                    <option value="ditanam">Ditanam</option>
                    <option value="proses">Proses</option>
                    <option value="panen">Panen</option>
                    <option value="gagal">Gagal</option>
                </select>
                <button type="submit" class="btn btn-primary" style="background: var(--asr-green); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    Update Terpilih
                </button>
            </div>
        </div>

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table" id="plantsTable" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" style="cursor: pointer;">
                        </th>
                        <th style="width: 50px;">NO</th>
                        <th>KODE TANAMAN</th>
                        <th>STATUS</th>
                        <th>TANGGAL TANAM</th>
                        <th>TANGGAL PANEN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($greenhouse->plants as $index => $plant)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 1rem;">
                            <input type="checkbox" name="plant_ids[]" value="{{ $plant->id }}" class="plant-checkbox" style="cursor: pointer;">
                        </td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-muted);">
                            {{ $index + 1 }}
                        </td>
                        <td style="padding: 1rem; color: var(--text-main); font-weight: 500;">{{ $plant->code }}</td>
                        <td style="padding: 1rem;">
                            @if($plant->status == 'kosong')
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(107, 114, 128, 0.1); color: #6b7280; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">Kosong</span>
                            @elseif($plant->status == 'ditanam')
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">Ditanam</span>
                            @elseif($plant->status == 'proses')
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(234, 179, 8, 0.1); color: #eab308; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">Proses</span>
                            @elseif($plant->status == 'panen')
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(34, 197, 94, 0.1); color: #22c55e; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">Panen</span>
                            @else
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">Gagal</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; color: var(--text-muted);">{{ $plant->planted_at ? \Carbon\Carbon::parse($plant->planted_at)->format('d M Y') : '-' }}</td>
                        <td style="padding: 1rem; color: var(--text-muted);">{{ $plant->harvested_at ? \Carbon\Carbon::parse($plant->harvested_at)->format('d M Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with export buttons and dynamic numbering
        var t = $('#plantsTable').DataTable({
            "dom": "<'dt-buttons-wrapper'B><'dt-controls-wrapper'lf>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'dt-bottom-container'<'dt-info'i><'dt-pagination'p>>",
            "buttons": [
                { extend: 'copy', text: '<i class="ph ph-copy"></i> Copy', className: 'dt-button' },
                { extend: 'excel', text: '<i class="ph ph-file-xls"></i> Excel', className: 'dt-button' },
                { extend: 'csv', text: '<i class="ph ph-file-csv"></i> CSV', className: 'dt-button' },
                { extend: 'pdf', text: '<i class="ph ph-file-pdf"></i> PDF', className: 'dt-button' }
            ],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "Semua"]],
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data",
                "search": "Cari:",
                "info": "Menampilkan _START_–_END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "emptyTable": "Belum ada data",
                "zeroRecords": "Belum ada data",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "»",
                    "previous": "«"
                }
            },
            "columnDefs": [
                { "searchable": false, "orderable": false, "targets": [0, 1] }
            ],
            "order": [[ 2, 'asc' ]]
        });

        // Dynamic numbering for the 'No' column
        t.on('order.dt search.dt', function () {
            let i = 1;
            t.cells(null, 1, {search: 'applied', order: 'applied'}).every(function (cell) {
                this.data(i++);
            });
        }).draw();

        // Select All Checkbox
        $('#selectAll').on('change', function() {
            // Get all rows with search applied
            var rows = t.rows({ 'search': 'applied' }).nodes();
            // Check/uncheck checkboxes for all rows in the table
            $('input[type="checkbox"].plant-checkbox', rows).prop('checked', this.checked);
        });
    });
</script>
@endsection
