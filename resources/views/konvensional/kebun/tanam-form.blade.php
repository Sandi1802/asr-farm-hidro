@extends('layouts.app')

@section('title', 'Tanam Baru')

@section('content')
<div class="project-hero">
    <div class="hero-content">
        <h1><i class="ph ph-plant"></i> Tanam Baru</h1>
        <p>Catat penanaman baru secara kolektif per lahan dan zona.</p>
    </div>
</div>

<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-body p-4">
            <form id="tanamForm" action="{{ route('konvensional.kebun.tanam.store') }}" method="POST">
                @csrf
                
                <!-- Step 1: Lokasi -->
                <h5 class="mb-3"><i class="ph ph-map-pin"></i> Step 1: Pilih Lokasi</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Lahan</label>
                        <select class="form-control" id="lahan_id" name="lahan_id" required>
                            <option value="">-- Pilih Lahan --</option>
                            @foreach($lahanList as $lahan)
                                <option value="{{ $lahan->id }}">{{ $lahan->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Zona</label>
                        <select class="form-control" id="zona_id" name="zona_id" required disabled>
                            <option value="">-- Pilih Zona --</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Pola</label>
                        <select class="form-control" id="pola_id" name="pola_id" required disabled>
                            <option value="">-- Pilih Pola --</option>
                        </select>
                    </div>
                </div>

                <!-- Step 2: Bedeng -->
                <h5 class="mb-3"><i class="ph ph-squares-four"></i> Step 2: Pilih Bedeng</h5>
                <div id="bedengContainer" class="mb-4 d-none">
                    <p class="text-muted small mb-2">Pilih bedeng kosong yang akan ditanami. Bedeng yang sudah terisi tidak dapat dipilih.</p>
                    <div class="d-flex flex-wrap gap-2" id="bedengGrid">
                        <!-- Bedeng checkboxes populated by AJAX -->
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBedeng">Pilih Semua</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBedeng">Batal Pilih Semua</button>
                    </div>
                </div>

                <!-- Step 3 & 4: Tanaman & Detail -->
                <div id="detailContainer" class="d-none">
                    <h5 class="mb-3"><i class="ph ph-leaf"></i> Step 3: Detail Tanaman</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Tanaman</label>
                            <select class="form-control" id="tanaman_id" name="tanaman_id" required>
                                <option value="">-- Pilih Tanaman --</option>
                                @foreach($tanamanList as $tanaman)
                                    <option value="{{ $tanaman->id }}" data-panen="{{ $tanaman->lama_hari_ke_panen }}" data-hasil="{{ $tanaman->rata2_hasil_per_tanaman }}" data-satuan="{{ $tanaman->satuan_hasil }}">
                                        {{ $tanaman->nama }} ({{ $tanaman->varietas }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Tanggal Tanam</label>
                            <input type="date" class="form-control" id="tanggal_tanam" name="tanggal_tanam" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Metode Input Jumlah</label>
                            <div class="d-flex gap-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="input_mode" id="mode_sama" value="sama" checked>
                                    <label class="form-check-label" for="mode_sama">Sama untuk semua bedeng</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="input_mode" id="mode_beda" value="beda">
                                    <label class="form-check-label" for="mode_beda">Per bedeng</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6" id="jumlahSamaContainer">
                            <label class="form-label">Jumlah Tanam per Bedeng</label>
                            <input type="number" class="form-control" id="jumlah_tanam_sama" min="1" placeholder="Masukkan jumlah">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Sumber Benih / Catatan</label>
                            <textarea class="form-control" name="catatan" rows="2" placeholder="Informasi asal benih atau catatan tambahan"></textarea>
                        </div>
                    </div>

                    <!-- Step 5: Preview -->
                    <h5 class="mb-3"><i class="ph ph-table"></i> Step 4: Preview</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered" id="previewTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Bedeng</th>
                                    <th>Tanaman</th>
                                    <th>Tgl Tanam</th>
                                    <th>Jml Tanam</th>
                                    <th>Est. Panen</th>
                                    <th>Est. Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="emptyPreview"><td colspan="6" class="text-center text-muted">Lengkapi form di atas untuk melihat preview.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit" disabled>
                            <i class="ph ph-check-circle"></i> Simpan Penanaman
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const lahanData = @json($lahanList);
    const $lahan = $('#lahan_id');
    const $zona = $('#zona_id');
    const $pola = $('#pola_id');
    const $bedengGrid = $('#bedengGrid');
    const $bedengContainer = $('#bedengContainer');
    const $detailContainer = $('#detailContainer');
    const $tanaman = $('#tanaman_id');
    const $tglTanam = $('#tanggal_tanam');
    const $jmlSama = $('#jumlah_tanam_sama');
    const $modeSama = $('#mode_sama');
    const $modeBeda = $('#mode_beda');
    
    // Cascading Lahan -> Zona
    $lahan.change(function() {
        let lahanId = $(this).val();
        $zona.html('<option value="">-- Pilih Zona --</option>').prop('disabled', !lahanId);
        $pola.html('<option value="">-- Pilih Pola --</option>').prop('disabled', true);
        $bedengContainer.addClass('d-none');
        $detailContainer.addClass('d-none');
        if(lahanId) {
            let selectedLahan = lahanData.find(l => l.id == lahanId);
            if(selectedLahan && selectedLahan.zona) {
                selectedLahan.zona.forEach(item => {
                    $zona.append(`<option value="${item.id}">${item.nama}</option>`);
                });
            }
        }
    });

    // Cascading Zona -> Pola
    $zona.change(function() {
        let zonaId = $(this).val();
        $pola.html('<option value="">-- Pilih Pola --</option>').prop('disabled', !zonaId);
        $bedengContainer.addClass('d-none');
        $detailContainer.addClass('d-none');
        if(zonaId) {
            let lahanId = $lahan.val();
            let selectedLahan = lahanData.find(l => l.id == lahanId);
            let selectedZona = selectedLahan ? selectedLahan.zona.find(z => z.id == zonaId) : null;
            if(selectedZona && selectedZona.pola) {
                selectedZona.pola.forEach(item => {
                    $pola.append(`<option value="${item.id}">${item.nama}</option>`);
                });
            }
        }
    });

    // Pola -> Load Bedeng
    $pola.change(function() {
        let polaId = $(this).val();
        if(polaId) {
            $.get(`/konvensional/kebun/tanam/get-bedeng?pola_id=${polaId}`, function(data) {
                $bedengGrid.empty();
                if(data.length === 0) {
                    $bedengGrid.html('<p class="text-muted">Tidak ada bedeng di pola ini.</p>');
                } else {
                    data.forEach(b => {
                        let isOccupied = b.tanam_aktif !== null;
                        let disabled = isOccupied ? 'disabled' : '';
                        let checked = !isOccupied ? 'checked' : '';
                        let badge = isOccupied ? `<span class="badge bg-danger small ms-1">${b.tanam_aktif.tanaman.nama}</span>` : `<span class="badge bg-success small ms-1">Kosong</span>`;
                        
                        let html = `
                        <div class="form-check card p-2" style="width:140px; ${isOccupied ? 'background:#f8d7da; opacity:0.7;' : 'background:#d1e7dd;'}">
                            <input class="form-check-input ms-1 bedeng-checkbox" type="checkbox" name="bedeng_ids[]" value="${b.id}" id="bdg_${b.id}" data-kode="${b.kode}" ${disabled} ${checked}>
                            <label class="form-check-label d-block text-center mt-1" for="bdg_${b.id}">
                                <strong>${b.kode}</strong><br>
                                ${badge}
                            </label>
                        </div>
                        `;
                        $bedengGrid.append(html);
                    });
                }
                $bedengContainer.removeClass('d-none');
                $detailContainer.removeClass('d-none');
                updatePreview();
            });
        } else {
            $bedengContainer.addClass('d-none');
            $detailContainer.addClass('d-none');
        }
    });

    $('#selectAllBedeng').click(function() {
        $('.bedeng-checkbox:not(:disabled)').prop('checked', true);
        updatePreview();
    });

    $('#deselectAllBedeng').click(function() {
        $('.bedeng-checkbox').prop('checked', false);
        updatePreview();
    });

    $('input[name="input_mode"]').change(function() {
        if($modeSama.is(':checked')) {
            $('#jumlahSamaContainer').removeClass('d-none');
        } else {
            $('#jumlahSamaContainer').addClass('d-none');
        }
        updatePreview();
    });

    // Listen to changes for preview
    $(document).on('change', '.bedeng-checkbox, #tanaman_id, #tanggal_tanam, #jumlah_tanam_sama', updatePreview);
    $(document).on('input', '.jml-per-bedeng', function() {
        updateSingleEst($(this));
    });

    function updatePreview() {
        let tanamanSelected = $tanaman.find('option:selected');
        let tName = tanamanSelected.text() || '-';
        let hariPanen = parseInt(tanamanSelected.data('panen')) || 0;
        let rataHasil = parseFloat(tanamanSelected.data('hasil')) || 0;
        let satuan = tanamanSelected.data('satuan') || '';
        let tglTanam = $tglTanam.val();
        
        let estPanen = '-';
        if(tglTanam && hariPanen > 0) {
            let d = new Date(tglTanam);
            d.setDate(d.getDate() + hariPanen);
            estPanen = d.toISOString().split('T')[0];
        }

        let isSama = $modeSama.is(':checked');
        let jmlSama = parseInt($jmlSama.val()) || 0;

        let tbody = $('#previewTable tbody');
        tbody.empty();

        let count = 0;
        $('.bedeng-checkbox:checked').each(function() {
            let id = $(this).val();
            let kode = $(this).data('kode');
            count++;

            let inputJml = isSama 
                ? `<input type="hidden" name="jumlah_per_bedeng[${id}]" value="${jmlSama}"><span class="badge bg-secondary">${jmlSama}</span>`
                : `<input type="number" class="form-control form-control-sm jml-per-bedeng" name="jumlah_per_bedeng[${id}]" data-hasil="${rataHasil}" data-satuan="${satuan}" min="1" required>`;

            let estHasilTxt = isSama && jmlSama > 0 
                ? (jmlSama * rataHasil).toFixed(1) + ' ' + satuan 
                : '-';

            tbody.append(`
                <tr>
                    <td><strong>${kode}</strong></td>
                    <td>${tName}</td>
                    <td>${tglTanam}</td>
                    <td>${inputJml}</td>
                    <td>${estPanen}</td>
                    <td class="est-hasil">${estHasilTxt}</td>
                </tr>
            `);
        });

        if(count === 0) {
            tbody.append('<tr id="emptyPreview"><td colspan="6" class="text-center text-muted">Belum ada bedeng yang dipilih.</td></tr>');
            $('#btnSubmit').prop('disabled', true);
        } else {
            if($tanaman.val() && $tglTanam.val() && (!isSama || jmlSama > 0)) {
                $('#btnSubmit').prop('disabled', false);
            } else {
                $('#btnSubmit').prop('disabled', true);
            }
        }
    }

    function updateSingleEst($input) {
        let val = parseInt($input.val()) || 0;
        let rata = parseFloat($input.data('hasil')) || 0;
        let satuan = $input.data('satuan');
        let total = (val * rata).toFixed(1);
        $input.closest('tr').find('.est-hasil').text(total + ' ' + satuan);
        
        let allValid = true;
        $('.jml-per-bedeng').each(function() {
            if(!$(this).val() || parseInt($(this).val()) <= 0) allValid = false;
        });
        if(allValid && $tanaman.val() && $tglTanam.val()) {
            $('#btnSubmit').prop('disabled', false);
        } else {
            $('#btnSubmit').prop('disabled', true);
        }
    }
});
</script>
@endpush
@endsection
