@extends('layouts.app')
@section('title', 'Manajemen Inventaris')
@section('content')

@php
$categoryConfig = [
    'bibit'       => ['label' => 'Stok Bibit',        'icon' => 'ph-seed',          'color' => '#16a34a', 'bg' => '#dcfce7', 'desc' => 'Bibit & benih tanaman'],
    'media_tanam' => ['label' => 'Media Tanam',        'icon' => 'ph-cube',          'color' => '#0369a1', 'bg' => '#e0f2fe', 'desc' => 'Rockwool, Net pot, dll'],
    'nutrisi'     => ['label' => 'Nutrisi Tanaman',    'icon' => 'ph-flask',         'color' => '#d97706', 'bg' => '#fef3c7', 'desc' => 'AB Mix, pH Up/Down, dll'],
    'obat'        => ['label' => 'Obat & Pestisida',   'icon' => 'ph-first-aid-kit', 'color' => '#dc2626', 'bg' => '#fee2e2', 'desc' => 'Pestisida, fungisida, dll'],
    'peralatan'   => ['label' => 'Peralatan',          'icon' => 'ph-wrench',        'color' => '#7c3aed', 'bg' => '#ede9fe', 'desc' => 'TDS meter, pH meter, pompa'],
    'perlengkapan'=> ['label' => 'Perlengkapan',       'icon' => 'ph-toolbox',       'color' => '#0891b2', 'bg' => '#cffafe', 'desc' => 'Selang, wadah, atribut kebun'],
    'lainnya'     => ['label' => 'Lainnya',            'icon' => 'ph-package',       'color' => '#64748b', 'bg' => '#f1f5f9', 'desc' => 'Barang lain yang tidak terkategori'],
];
$activeCat = isset($categoryConfig[$currentCat]) ? $categoryConfig[$currentCat] : null;
$pageTitle = $activeCat ? $activeCat['label'] : 'Semua Inventaris';
@endphp

<style>
.inv-photo { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid #e5e7eb; cursor: pointer; transition: transform 0.2s; }
.inv-photo:hover { transform: scale(1.1); }
.inv-icon-box { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

/* Stock History Modal */
#stockHistoryModal .modal-content { max-width: 640px; }
.log-timeline { display: flex; flex-direction: column; gap: 0; margin-top: 1rem; }
.log-entry { display: flex; gap: 1rem; align-items: flex-start; padding: 0.875rem 0; border-bottom: 1px solid #f1f5f9; }
.log-entry:last-child { border-bottom: none; }
.log-dot-wrap { display: flex; flex-direction: column; align-items: center; gap: 0; }
.log-dot { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0; }
.log-dot.in  { background: #dcfce7; color: #16a34a; }
.log-dot.out { background: #fee2e2; color: #dc2626; }
.log-dot.adjustment { background: #fef3c7; color: #d97706; }
.log-body { flex: 1; }
.log-qty { font-weight: 700; font-size: 0.95rem; }
.log-qty.in  { color: #16a34a; }
.log-qty.out { color: #dc2626; }
.log-qty.adjustment { color: #d97706; }
.log-desc { font-size: 0.85rem; color: #6b7280; margin-top: 0.15rem; }
.log-meta { font-size: 0.75rem; color: #9ca3af; margin-top: 0.2rem; }
.log-empty { text-align: center; padding: 2rem; color: #9ca3af; }
.log-empty i { font-size: 2.5rem; display: block; margin-bottom: 0.5rem; }
#logLoadingSpinner { text-align: center; padding: 2rem; color: #6b7280; }

/* Image preview */
.img-preview-wrap { margin-top: 0.5rem; }
.img-preview { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb; }
</style>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- HEADER --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $pageTitle }}</h1>
            <p style="color: #6b7280; font-size: 0.9rem; margin-top: 0.25rem;">Kelola stok barang pertanian — klik nama barang untuk melihat riwayat stok</p>
        </div>
        @if(Auth::check() && Auth::user()->isAgriAdmin())
        <button onclick="openAddModal('{{ $currentCat != 'all' ? $currentCat : '' }}')" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: #16a34a; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem;">
            <i class="ph ph-plus"></i> Tambah Barang
        </button>
        @endif
    </div>

    @if(session('success'))
    <div style="padding: 1rem 1.25rem; background: #dcfce7; color: #15803d; border-radius: 10px; border-left: 4px solid #16a34a; font-weight: 500;">
        <i class="ph ph-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Main Card with DataTable --}}
    <div class="card" style="padding: 0;">
        <div style="padding: 1.5rem;">
            <table class="table datatable" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 50px;">NO</th>
                        <th style="width: 55px;">FOTO</th>
                        <th>KATEGORI</th>
                        <th>NAMA BARANG</th>
                        <th style="text-align: right;">STOK</th>
                        <th style="text-align: center;">SATUAN</th>
                        <th>KETERANGAN</th>
                        <th style="width: 80px; text-align: center;">RIWAYAT</th>
                        @if(Auth::check() && Auth::user()->isAgriAdmin())
                        <th style="width: 100px; text-align: right;">AKSI</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventories as $index => $inv)
                    @php $cfg = $categoryConfig[$inv->type] ?? $categoryConfig['lainnya']; @endphp
                    <tr>
                        <td style="color: #6b7280; text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            @if($inv->image)
                                <img src="{{ Storage::url($inv->image) }}" class="inv-photo"
                                     alt="{{ $inv->name }}"
                                     onclick="openImagePreview('{{ Storage::url($inv->image) }}', '{{ addslashes($inv->name) }}')" />
                            @else
                                <div class="inv-icon-box" style="background: {{ $cfg['bg'] }};">
                                    <i class="ph {{ $cfg['icon'] }}" style="color: {{ $cfg['color'] }}; font-size: 1.1rem;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 500; font-size: 0.85rem; color: #374151;">{{ $cfg['label'] }}</span>
                        </td>
                        <td>
                            <button type="button"
                                onclick="openStockHistory({{ $inv->id }}, '{{ addslashes($inv->name) }}')"
                                style="background: none; border: none; cursor: pointer; font-weight: 700; color: #1d4ed8; font-size: 0.95rem; padding: 0; text-decoration: underline; text-decoration-style: dotted; text-underline-offset: 3px;">
                                {{ $inv->name }}
                            </button>
                        </td>
                        <td style="text-align: right;">
                            <span style="font-weight: 700; font-size: 1rem; color: {{ $inv->quantity < 10 ? '#dc2626' : '#111827' }};">
                                {{ number_format($inv->quantity, 0, ',', '.') }}
                            </span>
                            @if($inv->quantity < 10)
                            <br><span style="background: #fee2e2; color: #dc2626; font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 4px;">MENIPIS</span>
                            @endif
                        </td>
                        <td style="text-align: center; color: #6b7280;">{{ $inv->unit }}</td>
                        <td style="color: #6b7280; font-size: 0.85rem; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $inv->description ?: '-' }}
                        </td>
                        <td style="text-align: center;">
                            <button type="button"
                                onclick="openStockHistory({{ $inv->id }}, '{{ addslashes($inv->name) }}')"
                                style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; border-radius: 6px; padding: 0.3rem 0.6rem; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                <i class="ph ph-clock-counter-clockwise"></i>
                            </button>
                        </td>
                        @if(Auth::check() && Auth::user()->isAgriAdmin())
                        <td>
                            <div style="display: flex; gap: 4px; justify-content: flex-end;">
                                <button type="button" class="dt-action-btn dt-btn-edit" title="Edit"
                                    onclick="openEditModal({{ $inv->id }}, '{{ addslashes($inv->name) }}', '{{ $inv->type }}', {{ $inv->quantity }}, '{{ $inv->unit }}', '{{ addslashes($inv->description) }}', '{{ $inv->image ? Storage::url($inv->image) : '' }}')">
                                    <i class="ph ph-pencil-simple"></i>
                                </button>
                                <button type="button" class="dt-action-btn dt-btn-delete" title="Hapus"
                                    onclick="confirmAction('Hapus Data?', 'Yakin ingin menghapus {{ addslashes($inv->name) }} dari inventaris?', '{{ route('hydroponics.inventory.destroy', $inv->id) }}', 'DELETE')">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL: Tambah Barang --}}
<div id="addInventoryModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Barang Inventaris</h3>
            <button onclick="closeModal('addInventoryModal')" class="close-modal">&times;</button>
        </div>
        <form action="{{ route('hydroponics.inventory.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Kategori</label>
                <select name="type" id="addType" onchange="updateUnit(this.value)" class="form-control">
                    <option value="bibit">🌱 Stok Bibit</option>
                    <option value="media_tanam">🧱 Media Tanam</option>
                    <option value="nutrisi">🧪 Nutrisi Tanaman</option>
                    <option value="obat">💊 Obat & Pestisida</option>
                    <option value="peralatan">🔧 Peralatan</option>
                    <option value="perlengkapan">🧰 Perlengkapan</option>
                    <option value="lainnya">📦 Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nama Barang</label>

                {{-- Dropdown benih (tampil saat kategori = bibit) --}}
                <select name="name" id="addNameSelect" class="form-control" style="display:none;">
                    <option value="">-- Pilih Jenis Benih --</option>
                    @foreach($plantTypes as $pt)
                    <option value="{{ $pt->name }}">{{ $pt->name }}</option>
                    @endforeach
                    <option value="__lainnya__">+ Ketik manual...</option>
                </select>

                {{-- Input teks bebas (default / untuk kategori selain bibit) --}}
                <input type="text" name="name" id="addNameText" required placeholder="Contoh: Rockwool, Net Pot, AB Mix A" class="form-control">

                <p id="addNameHint" style="font-size:0.78rem; color:#6b7280; margin-top:0.35rem; display:none;">
                    <i class="ph ph-info"></i> Data benih diambil dari Master Data Jenis Tanaman.
                </p>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="quantity" step="0.01" required placeholder="0" class="form-control">
                </div>
                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" name="unit" id="addUnit" required placeholder="kg, pcs, lembar" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan (Opsional)</label>
                <input type="text" name="description" placeholder="Deskripsi singkat..." class="form-control">
            </div>
            <div class="form-group">
                <label>Foto Barang (Opsional)</label>
                <input type="file" name="image" accept="image/*" class="form-control" onchange="previewAddImage(this)">
                <div class="img-preview-wrap" id="addImagePreview" style="display:none;">
                    <img id="addImgPreviewImg" class="img-preview" src="" alt="Preview">
                </div>
            </div>

            <div style="display:flex; gap:0.75rem; margin-top:1.5rem; justify-content:flex-end;">
                <button type="button" onclick="closeModal('addInventoryModal')" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Edit Barang --}}
<div id="editInventoryModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Barang</h3>
            <button onclick="closeModal('editInventoryModal')" class="close-modal">&times;</button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Kategori</label>
                <select name="type" id="editType" class="form-control">
                    <option value="bibit">🌱 Stok Bibit</option>
                    <option value="media_tanam">🧱 Media Tanam</option>
                    <option value="nutrisi">🧪 Nutrisi Tanaman</option>
                    <option value="obat">💊 Obat & Pestisida</option>
                    <option value="peralatan">🔧 Peralatan</option>
                    <option value="perlengkapan">🧰 Perlengkapan</option>
                    <option value="lainnya">📦 Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="name" id="editName" required class="form-control">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="quantity" id="editQty" step="0.01" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" name="unit" id="editUnit" required class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <input type="text" name="description" id="editDesc" class="form-control">
            </div>
            <div class="form-group">
                <label>Alasan Perubahan Jumlah (jika stok berubah)</label>
                <input type="text" name="log_description" placeholder="Contoh: Panen, Dipakai di GH-2, Pembelian..." class="form-control">
            </div>
            <div class="form-group">
                <label>Ganti Foto Barang (Opsional)</label>
                <div class="img-preview-wrap" id="editCurrentPhoto" style="display:none; margin-bottom: 0.5rem;">
                    <p style="font-size:0.8rem; color:#6b7280; margin-bottom:0.35rem;">Foto saat ini:</p>
                    <img id="editCurrentPhotoImg" class="img-preview" src="" alt="Current">
                </div>
                <input type="file" name="image" accept="image/*" class="form-control" onchange="previewEditImage(this)">
                <div class="img-preview-wrap" id="editNewImagePreview" style="display:none; margin-top:0.5rem;">
                    <p style="font-size:0.8rem; color:#6b7280; margin-bottom:0.35rem;">Foto baru:</p>
                    <img id="editNewImgPreviewImg" class="img-preview" src="" alt="Preview">
                </div>
            </div>

            <div style="display:flex; gap:0.75rem; margin-top:1.5rem; justify-content:flex-end;">
                <button type="button" onclick="closeModal('editInventoryModal')" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Riwayat Stok --}}
<div id="stockHistoryModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 640px;">
        <div class="modal-header">
            <div>
                <h3 id="historyModalTitle">Riwayat Stok</h3>
                <p id="historyModalSubtitle" style="font-size: 0.85rem; color: #6b7280; margin-top: 0.2rem;"></p>
            </div>
            <button onclick="closeModal('stockHistoryModal')" class="close-modal">&times;</button>
        </div>

        <div id="logLoadingSpinner">
            <i class="ph ph-spinner" style="font-size: 1.5rem; display:block; margin-bottom:0.5rem; animation: spin 1s linear infinite;"></i>
            Memuat riwayat...
        </div>

        <div id="logContent" style="display:none; max-height: 420px; overflow-y: auto;">
            <div class="log-timeline" id="logTimeline"></div>
        </div>
    </div>
</div>

{{-- MODAL: Zoom Foto --}}
<div id="imagePreviewModal" class="modal-overlay" onclick="if(event.target===this) closeModal('imagePreviewModal')">
    <div style="background: white; border-radius: 14px; padding: 1rem; max-width: 90vw; max-height: 90vh; overflow: hidden; position: relative;">
        <button onclick="closeModal('imagePreviewModal')" class="close-modal" style="position: absolute; top: 0.5rem; right: 0.75rem; font-size: 1.5rem; background:none; border:none; cursor:pointer;">&times;</button>
        <p id="imgPreviewName" style="font-weight: 700; margin-bottom: 0.75rem; color: #111827; padding-right: 2rem;"></p>
        <img id="imgPreviewSrc" src="" alt="" style="max-width: 75vw; max-height: 75vh; border-radius: 8px; display: block; object-fit: contain;">
    </div>
</div>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script>
const unitHints = {
    bibit: 'gram', media_tanam: 'pcs', nutrisi: 'kg', obat: 'liter', peralatan: 'unit', perlengkapan: 'pcs', lainnya: 'pcs'
};

function updateUnit(type) {
    const unit        = document.getElementById('addUnit');
    const nameSelect  = document.getElementById('addNameSelect');
    const nameText    = document.getElementById('addNameText');
    const nameHint    = document.getElementById('addNameHint');

    if (unit && unitHints[type]) unit.placeholder = unitHints[type];

    if (type === 'bibit') {
        // Tampilkan dropdown, sembunyikan text input
        nameSelect.style.display = 'block';
        nameSelect.required = true;
        nameText.style.display = 'none';
        nameText.required = false;
        nameText.value = '';
        nameHint.style.display = 'block';
    } else {
        // Tampilkan text input, sembunyikan dropdown
        nameSelect.style.display = 'none';
        nameSelect.required = false;
        nameText.style.display = 'block';
        nameText.required = true;
        nameHint.style.display = 'none';
    }
}

// Handle 'Ketik manual...' option on select
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('addNameSelect');
    if (!sel) return;
    sel.addEventListener('change', function() {
        const nameText = document.getElementById('addNameText');
        if (this.value === '__lainnya__') {
            // Switch ke text input
            this.style.display = 'none';
            this.required = false;
            nameText.style.display = 'block';
            nameText.required = true;
            nameText.value = '';
            nameText.focus();
            document.getElementById('addNameHint').style.display = 'none';
        }
    });
});

function openAddModal(presetType) {
    const modal = document.getElementById('addInventoryModal');
    const type  = presetType || document.getElementById('addType').value || 'bibit';
    if (presetType) {
        document.getElementById('addType').value = presetType;
    }
    updateUnit(type);
    modal.classList.add('active');
}

function openEditModal(id, name, type, qty, unit, desc, imageUrl) {
    document.getElementById('editForm').action = '/hydroponics/inventory/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editType').value = type;
    document.getElementById('editQty').value = qty;
    document.getElementById('editUnit').value = unit;
    document.getElementById('editDesc').value = desc;

    const photoWrap = document.getElementById('editCurrentPhoto');
    const photoImg  = document.getElementById('editCurrentPhotoImg');
    if (imageUrl) {
        photoImg.src = imageUrl;
        photoWrap.style.display = 'block';
    } else {
        photoWrap.style.display = 'none';
    }
    document.getElementById('editNewImagePreview').style.display = 'none';

    document.getElementById('editInventoryModal').classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

function previewAddImage(input) {
    const wrap = document.getElementById('addImagePreview');
    const img  = document.getElementById('addImgPreviewImg');
    if (input.files && input.files[0]) {
        img.src = URL.createObjectURL(input.files[0]);
        wrap.style.display = 'block';
    }
}

function previewEditImage(input) {
    const wrap = document.getElementById('editNewImagePreview');
    const img  = document.getElementById('editNewImgPreviewImg');
    if (input.files && input.files[0]) {
        img.src = URL.createObjectURL(input.files[0]);
        wrap.style.display = 'block';
    }
}

function openImagePreview(url, name) {
    document.getElementById('imgPreviewSrc').src = url;
    document.getElementById('imgPreviewName').textContent = name;
    document.getElementById('imagePreviewModal').classList.add('active');
}

async function openStockHistory(inventoryId, name) {
    const modal = document.getElementById('stockHistoryModal');
    document.getElementById('historyModalTitle').textContent = 'Riwayat Stok — ' + name;
    document.getElementById('historyModalSubtitle').textContent = 'Seluruh mutasi keluar masuk barang ini';
    document.getElementById('logLoadingSpinner').style.display = 'block';
    document.getElementById('logContent').style.display = 'none';
    modal.classList.add('active');

    try {
        const response = await fetch('/hydroponics/inventory/' + inventoryId + '/logs');
        const data = await response.json();

        const timeline = document.getElementById('logTimeline');
        timeline.innerHTML = '';

        if (data.logs.length === 0) {
            timeline.innerHTML = `<div class="log-empty"><i class="ph ph-clipboard-text"></i>Belum ada riwayat untuk barang ini.</div>`;
        } else {
            data.logs.forEach(log => {
                const sign   = log.type === 'in' ? '+' : (log.type === 'out' ? '-' : '±');
                const icon   = log.type === 'in' ? 'ph-arrow-circle-down' : (log.type === 'out' ? 'ph-arrow-circle-up' : 'ph-arrows-clockwise');
                const label  = log.type === 'in' ? 'Barang Masuk' : (log.type === 'out' ? 'Barang Keluar' : 'Penyesuaian');

                timeline.innerHTML += `
                <div class="log-entry">
                    <div class="log-dot-wrap">
                        <div class="log-dot ${log.type}">
                            <i class="ph ${icon}"></i>
                        </div>
                    </div>
                    <div class="log-body">
                        <div class="log-qty ${log.type}">${sign}${parseFloat(log.quantity).toLocaleString('id-ID')} ${data.unit}</div>
                        <div class="log-desc">${log.description || label}</div>
                        <div class="log-meta">
                            <i class="ph ph-clock"></i> ${log.date}
                            &nbsp;·&nbsp;
                            <i class="ph ph-user"></i> ${log.user}
                        </div>
                    </div>
                    <div style="font-size:0.75rem; color: #9ca3af; white-space: nowrap; padding-top: 0.1rem;">${label}</div>
                </div>`;
            });
        }

        document.getElementById('logLoadingSpinner').style.display = 'none';
        document.getElementById('logContent').style.display = 'block';

    } catch (err) {
        document.getElementById('logLoadingSpinner').innerHTML = '<span style="color:#dc2626;">Gagal memuat riwayat.</span>';
    }
}

// Close modal on backdrop click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>

@endsection
