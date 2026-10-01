@extends('layouts.app')

@section('title', 'Master Tanaman')

@section('content')
<div class="project-hero">
    <div class="hero-content">
        <h1><i class="ph ph-plant"></i> Master Tanaman</h1>
        <p>Kelola data referensi tanaman konvensional.</p>
    </div>
</div>

<div class="container mt-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ph ph-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="ph ph-warning-circle"></i> Terdapat kesalahan input:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="ph ph-plus-circle"></i> Tambah Tanaman
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4 table-responsive">
            <table class="table table-hover datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Tanaman</th>
                        <th>Varietas</th>
                        <th>Lama Panen (Hari)</th>
                        <th>Rata-rata Hasil / Tanaman</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tanamanList as $idx => $t)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="fw-bold">{{ $t->nama }}</td>
                            <td>{{ $t->varietas ?? '-' }}</td>
                            <td>{{ $t->lama_hari_ke_panen }}</td>
                            <td>{{ $t->rata2_hasil_per_tanaman }} {{ $t->satuan_hasil }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="openEditModal({{ $t->id }}, '{{ addslashes($t->nama) }}', '{{ addslashes($t->varietas ?? '') }}', {{ $t->lama_hari_ke_panen }}, {{ $t->rata2_hasil_per_tanaman }}, '{{ $t->satuan_hasil }}', '{{ addslashes($t->catatan ?? '') }}')">
                                    <i class="ph ph-pencil-simple"></i>
                                </button>
                                <form action="{{ route('konvensional.kebun.tanaman.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tanaman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="modalTanaman" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formTanaman" method="POST" action="{{ route('konvensional.kebun.tanaman.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodSpoof" value="POST">
            <div class="modal-header" style="background:var(--asr-green); color:white;">
                <h5 class="modal-title" id="modalTitle"><i class="ph ph-plant"></i> <span id="modalTitleText">Tambah Tanaman</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label">Nama Tanaman <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" id="t_nama" required placeholder="Contoh: Selada, Pakcoy, Bayam">
                </div>
                <div class="mb-3">
                    <label class="form-label">Varietas <span class="text-muted small">(opsional)</span></label>
                    <input type="text" class="form-control" name="varietas" id="t_varietas" placeholder="Contoh: Keriting, Romaine">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Lama ke Panen (Hari) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="lama_hari_ke_panen" id="t_lama" required min="1" placeholder="Contoh: 30">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Satuan Hasil <span class="text-danger">*</span></label>
                        <select class="form-control" name="satuan_hasil" id="t_satuan" required>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="ikat">Ikat</option>
                            <option value="buah">Buah</option>
                            <option value="gram">Gram (g)</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rata-rata Hasil per Tanaman <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" name="rata2_hasil_per_tanaman" id="t_hasil" required min="0.01" placeholder="Contoh: 0.25">
                    <small class="text-muted">Gunakan titik untuk desimal. Contoh: 0.25 = 250 gram jika satuan kg.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea class="form-control" name="catatan" id="t_catatan" rows="2" placeholder="Catatan khusus (opsional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="ph ph-check"></i> Simpan Data</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Buka modal Tambah
    function openAddModal() {
        document.getElementById('formTanaman').reset();
        document.getElementById('formTanaman').action = '{{ route('konvensional.kebun.tanaman.store') }}';
        document.getElementById('methodSpoof').value = 'POST';
        document.getElementById('modalTitleText').innerText = 'Tambah Tanaman';
        new bootstrap.Modal(document.getElementById('modalTanaman')).show();
    }

    // Buka modal Edit
    function openEditModal(id, nama, varietas, lama, hasil, satuan, catatan) {
        document.getElementById('t_nama').value    = nama;
        document.getElementById('t_varietas').value = varietas;
        document.getElementById('t_lama').value    = lama;
        document.getElementById('t_hasil').value   = hasil;
        document.getElementById('t_satuan').value  = satuan;
        document.getElementById('t_catatan').value = catatan;

        document.getElementById('formTanaman').action = '/konvensional/kebun/master-tanaman/' + id;
        document.getElementById('methodSpoof').value  = 'PUT';
        document.getElementById('modalTitleText').innerText = 'Edit Tanaman';
        new bootstrap.Modal(document.getElementById('modalTanaman')).show();
    }

    $(document).ready(function () {
        // Auto-buka modal jika ada error validasi (user tadi kirim form)
        @if($errors->any())
            new bootstrap.Modal(document.getElementById('modalTanaman')).show();
        @endif

        // DataTables
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
            });
        }
    });
</script>
@endpush
@endsection
