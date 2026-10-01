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
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTanaman" onclick="resetForm()">
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
                                <button class="btn btn-sm btn-outline-primary btn-edit" 
                                    data-id="{{ $t->id }}"
                                    data-nama="{{ $t->nama }}"
                                    data-varietas="{{ $t->varietas }}"
                                    data-lama="{{ $t->lama_hari_ke_panen }}"
                                    data-hasil="{{ $t->rata2_hasil_per_tanaman }}"
                                    data-satuan="{{ $t->satuan_hasil }}"
                                    data-catatan="{{ $t->catatan }}">
                                    <i class="ph ph-pencil-simple"></i>
                                </button>
                                <form action="{{ route('konvensional.kebun.tanaman.destroy', $t->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
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

<!-- Modal Tanaman -->
<div class="modal fade" id="modalTanaman" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formTanaman" action="{{ route('konvensional.kebun.tanaman.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodSpoof" value="POST">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle"><i class="ph ph-plant"></i> Tambah Tanaman</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
                          <div class="modal-body p-4">
                  @if($errors->any())
                      <div class="alert alert-danger">
                          <ul class="mb-0">
                              @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                  @endif
                <div class="mb-3">
                    <label class="form-label">Nama Tanaman <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" id="t_nama" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Varietas</label>
                    <input type="text" class="form-control" name="varietas" id="t_varietas">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Lama ke Panen (Hari) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="lama_hari_ke_panen" id="t_lama" required min="1">
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
                    <input type="number" step="0.01" class="form-control" name="rata2_hasil_per_tanaman" id="t_hasil" required min="0.01">
                    <small class="text-muted">Gunakan titik untuk desimal. Contoh: 0.25 (untuk 250 gram jika satuan kg)</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea class="form-control" name="catatan" id="t_catatan" rows="2"></textarea>
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
    function resetForm() {
        $('#formTanaman')[0].reset();
        $('#formTanaman').attr('action', '{{ route('konvensional.kebun.tanaman.store') }}');
        $('#methodSpoof').val('POST');
        $('#modalTitle').html('<i class="ph ph-plant"></i> Tambah Tanaman');
    }

          $(document).ready(function() {
          @if($errors->any())
              var myModal = new bootstrap.Modal(document.getElementById('modalTanaman'));
              myModal.show();
          @endif
        // Initialize DataTables
        if ($.fn.DataTable) {
            $('.datatable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
            });
        }

        // Edit button click
        $('.btn-edit').click(function() {
            let id = $(this).data('id');
            $('#t_nama').val($(this).data('nama'));
            $('#t_varietas').val($(this).data('varietas'));
            $('#t_lama').val($(this).data('lama'));
            $('#t_hasil').val($(this).data('hasil'));
            $('#t_satuan').val($(this).data('satuan'));
            $('#t_catatan').val($(this).data('catatan'));
            
            $('#formTanaman').attr('action', `/konvensional/kebun/master-tanaman/${id}`);
            $('#methodSpoof').val('PUT');
            $('#modalTitle').html('<i class="ph ph-pencil-simple"></i> Edit Tanaman');
            
            let modal = new bootstrap.Modal(document.getElementById('modalTanaman'));
            modal.show();
        });

        // Delete confirmation
        $('.btn-delete').click(function() {
            let form = $(this).closest('.delete-form');
            if(confirm('Apakah Anda yakin ingin menghapus tanaman ini? Data yang terhubung mungkin akan ikut terhapus.')) {
                form.submit();
            }
        });
    });
</script>
@endpush
@endsection
