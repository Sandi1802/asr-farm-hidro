@extends('layouts.app')

@section('title', 'Catat Panen')

@section('content')
<div class="project-hero">
    <div class="hero-content">
        <h1><i class="ph ph-basket"></i> Catat Panen</h1>
        <p>Catat hasil panen dari bedeng yang sedang aktif.</p>
    </div>
</div>

<div class="container mt-4">
    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-12 col-md-4">
            <div class="icon-box green p-3 rounded bg-white shadow-sm d-flex align-items-center">
                <i class="ph ph-plant fs-1 me-3 text-success"></i>
                <div>
                    <h6 class="mb-0 text-muted">Total Bedeng Aktif</h6>
                    <h4 class="mb-0">{{ $tanamAktif->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="icon-box orange p-3 rounded bg-white shadow-sm d-flex align-items-center">
                <i class="ph ph-basket fs-1 me-3 text-warning"></i>
                <div>
                    <h6 class="mb-0 text-muted">Siap / Mendekati Panen</h6>
                    <h4 class="mb-0">{{ $tanamAktif->whereIn('status_tampilan', ['siap_panen', 'mendekati_panen', 'terlambat'])->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tanam List -->
    <div class="row g-3">
        @forelse($tanamAktif as $tanam)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold text-primary mb-0"><i class="ph ph-squares-four"></i> {{ $tanam->bedeng->kode }}</h5>
                            @php
                                $badgeColor = match($tanam->bedeng->status_tampilan) {
                                    'tumbuh' => 'success',
                                    'mendekati_panen' => 'warning',
                                    'siap_panen' => 'orange',
                                    'terlambat' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor == 'orange' ? 'warning' : $badgeColor }} text-{{ $badgeColor == 'warning' || $badgeColor == 'orange' ? 'dark' : 'white' }}">
                                {{ str_replace('_', ' ', strtoupper($tanam->bedeng->status_tampilan)) }}
                            </span>
                        </div>
                        
                        <h6 class="text-dark">{{ $tanam->tanaman->nama }} <small class="text-muted">({{ $tanam->tanaman->varietas }})</small></h6>
                        
                        <ul class="list-unstyled small text-muted mb-3">
                            <li><i class="ph ph-calendar"></i> Tanam: {{ \Carbon\Carbon::parse($tanam->tanggal_tanam)->format('d M Y') }} (Umur: {{ $tanam->umur_hari }} hari)</li>
                            <li><i class="ph ph-clock"></i> Est. Panen: {{ \Carbon\Carbon::parse($tanam->estimasi_tanggal_panen)->format('d M Y') }}</li>
                            <li><i class="ph ph-chart-bar"></i> Est. Hasil: {{ $tanam->estimasi_hasil }} {{ $tanam->tanaman->satuan_hasil }}</li>
                            @if($tanam->total_panen > 0)
                            <li class="text-success"><i class="ph ph-check"></i> Telah dipanen: {{ $tanam->total_panen }} {{ $tanam->tanaman->satuan_hasil }}</li>
                            @endif
                        </ul>
                        
                        <button class="btn btn-outline-success w-100 btn-panen" 
                                data-id="{{ $tanam->id }}" 
                                data-bedeng="{{ $tanam->bedeng->kode }}"
                                data-tanaman="{{ $tanam->tanaman->nama }}"
                                data-satuan="{{ $tanam->tanaman->satuan_hasil }}">
                            <i class="ph ph-basket"></i> Catat Panen
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="ph ph-info me-2 fs-4"></i> Tidak ada bedeng yang sedang dalam masa tanam aktif.
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Panen -->
<div class="modal fade" id="modalPanen" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('konvensional.kebun.panen.store') }}" method="POST">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="ph ph-basket"></i> Form Panen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="tanam_id" id="panen_tanam_id">
                
                <div class="mb-3">
                    <strong>Bedeng:</strong> <span id="lbl_bedeng" class="text-primary"></span><br>
                    <strong>Tanaman:</strong> <span id="lbl_tanaman"></span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Panen</label>
                    <input type="date" class="form-control" name="tanggal_panen" value="{{ date('Y-m-d') }}" required>
                </div>
                
                <div class="row mb-3">
                    <div class="col-8">
                        <label class="form-label">Jumlah Hasil</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="jumlah_hasil" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Satuan</label>
                        <input type="text" class="form-control bg-light" id="panen_satuan" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kualitas</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kualitas" value="A" id="kualA" checked>
                            <label class="form-check-label" for="kualA">Grade A</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kualitas" value="B" id="kualB">
                            <label class="form-check-label" for="kualB">Grade B</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kualitas" value="C" id="kualC">
                            <label class="form-check-label" for="kualC">Grade C</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea class="form-control" name="catatan" rows="2"></textarea>
                </div>

                <div class="form-check bg-light p-3 rounded border">
                    <input class="form-check-input ms-1" type="checkbox" name="panen_selesai" value="1" id="chkSelesai">
                    <label class="form-check-label fw-bold ms-2" for="chkSelesai">
                        Tandai Siklus Tanam Selesai
                    </label>
                    <small class="d-block text-muted ms-4">Bedeng akan kembali berstatus kosong setelah ini disimpan.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success"><i class="ph ph-check"></i> Simpan Panen</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.btn-panen').click(function() {
        let id = $(this).data('id');
        let bedeng = $(this).data('bedeng');
        let tanaman = $(this).data('tanaman');
        let satuan = $(this).data('satuan');

        $('#panen_tanam_id').val(id);
        $('#lbl_bedeng').text(bedeng);
        $('#lbl_tanaman').text(tanaman);
        $('#panen_satuan').val(satuan);
        
        let modal = new bootstrap.Modal(document.getElementById('modalPanen'));
        modal.show();
    });
});
</script>
@endpush
@endsection
