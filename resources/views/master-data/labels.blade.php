@extends('layouts.app')
@section('title', 'Master Data - Labels')

@section('content')
<style>
    .color-presets { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin-top: 10px; }
    .color-swatch { width: 100%; aspect-ratio: 1; border-radius: 6px; cursor: pointer; border: 2px solid transparent; transition: all 0.15s; }
    .color-swatch:hover, .color-swatch.active { border-color: var(--text-main); transform: scale(1.1); }
    .label-badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 0.82rem; font-weight: 600; }
    .status-badge-active { background: rgba(34, 197, 94, 0.15); color: #22C55E; padding: 4px 10px; border-radius: 50px; font-size: 0.78rem; font-weight: 700; }
    .status-badge-inactive { background: rgba(239, 68, 68, 0.15); color: #EF4444; padding: 4px 10px; border-radius: 50px; font-size: 0.78rem; font-weight: 700; }
</style>

<div class="content-container">
    <div class="header-action" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h2 class="page-title" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-tag" style="color: var(--asr-green);"></i> Label Management
        </h2>
        <button onclick="openAddLabelModal()" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.1rem; font-size: 0.85rem;">
            <i class="ph ph-plus"></i> ADD
        </button>
    </div>

    @if(session('success'))
    <div style="background: rgba(22, 163, 74, 0.1); color: var(--asr-green); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(22, 163, 74, 0.2); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <i class="ph ph-check-circle" style="font-size: 1.5rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table id="labelsTable" class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th>Label</th>
                        <th>Color</th>
                        <th>Parent</th>
                        <th>Description</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th style="width: 90px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labels as $index => $label)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="label-badge" style="background-color: {{ $label->color }}22; color: {{ $label->color }};">
                                {{ $label->name }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 14px; height: 14px; border-radius: 50%; background-color: {{ $label->color }}; border: 1px solid rgba(0,0,0,0.1);"></div>
                                <span style="font-size: 0.82rem; color: var(--text-muted);">{{ $label->color }}</span>
                            </div>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $label->parent ? $label->parent->name : '—' }}</td>
                        <td style="color: var(--text-muted); font-size: 0.85rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $label->description ?? '—' }}</td>
                        <td style="text-align: center;">{{ $label->sort_order }}</td>
                        <td>
                            @if($label->is_active)
                                <span class="status-badge-active">Aktif</span>
                            @else
                                <span class="status-badge-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.35rem;">
                                <button onclick="openEditLabelModal({{ $label->id }}, '{{ addslashes($label->name) }}', '{{ $label->color }}', '{{ $label->parent_id }}', `{{ addslashes($label->description ?? '') }}`, {{ $label->sort_order }})" style="background: var(--asr-green-light); color: var(--asr-green-dark); border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s;" onmouseover="this.style.background='var(--asr-green)';this.style.color='white'" onmouseout="this.style.background='var(--asr-green-light)';this.style.color='var(--asr-green-dark)'">
                                    <i class="ph ph-pencil-simple"></i>
                                </button>
                                <button onclick="deleteLabel({{ $label->id }}, '{{ addslashes($label->name) }}')" style="background: #fee2e2; color: #dc2626; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s;" onmouseover="this.style.background='#dc2626';this.style.color='white'" onmouseout="this.style.background='#fee2e2';this.style.color='#dc2626'">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add/Edit Label Modal --}}
<div class="modal-overlay" id="labelModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
    <div style="background: var(--card-bg); border-radius: 14px; padding: 1.5rem; width: 100%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 id="modalTitle" style="color: var(--asr-green); font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                <i class="ph ph-tag"></i> <span id="modalTitleText">Tambah Label Baru</span>
            </h3>
            <button onclick="closeModal()" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--text-muted);line-height:1;">&times;</button>
        </div>
        <form id="labelForm" method="POST" action="{{ route('master-data.labels.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div style="margin-bottom: 0.9rem;">
                <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem; color: var(--text-main);">Label Name</label>
                <input type="text" id="labelName" name="name" required placeholder="e.g. Rutinitas, Darurat, Pemeliharaan" oninput="updateBadgePreview()" style="width:100%; padding:0.55rem 0.8rem; border:1.5px solid var(--border-color); border-radius:8px; box-sizing:border-box; background: var(--bg-main); color: var(--text-main); font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: auto 1fr; gap: 1rem; margin-bottom: 0.9rem;">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem; color: var(--text-main);">Color</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div id="colorPreviewCircle" style="width: 36px; height: 36px; border-radius: 50%; background-color: #3B82F6; border: 2px solid var(--border-color); cursor: pointer;"></div>
                        <input type="text" id="colorInput" name="color" value="#3B82F6" oninput="handleColorInput()" style="width: 90px; padding:0.55rem 0.6rem; border:1.5px solid var(--border-color); border-radius:8px; box-sizing:border-box; background: var(--bg-main); color: var(--text-main); font-family: monospace; font-weight: 600;">
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem; color: var(--text-main);">Parent Label</label>
                    <select id="parentId" name="parent_id" style="width:100%; padding:0.55rem 0.8rem; border:1.5px solid var(--border-color); border-radius:8px; box-sizing:border-box; background: var(--bg-main); color: var(--text-main); font-family: inherit;">
                        <option value="">Tidak Ada (Top Level)</option>
                        @foreach($labels as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 0.9rem;">
                <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem; color: var(--text-muted);">PRESETS</label>
                <div class="color-presets" id="colorPresetsGrid"></div>
            </div>

            <div style="margin-bottom: 0.9rem;">
                <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem; color: var(--text-main);">Description <span style="font-weight:normal; color:var(--text-muted);">(Optional)</span></label>
                <textarea id="labelDesc" name="description" rows="2" placeholder="Optional description..." style="width:100%; padding:0.55rem 0.8rem; border:1.5px solid var(--border-color); border-radius:8px; box-sizing:border-box; resize:vertical; background: var(--bg-main); color: var(--text-main); font-family: inherit;"></textarea>
            </div>

            <div style="margin-bottom: 0.9rem;">
                <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem; color: var(--text-main);">Sort Order</label>
                <input type="number" id="sortOrder" name="sort_order" value="0" style="width: 80px; padding:0.55rem 0.8rem; border:1.5px solid var(--border-color); border-radius:8px; box-sizing:border-box; background: var(--bg-main); color: var(--text-main); font-family: inherit;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem; color: var(--text-main);">Preview</label>
                <div style="padding: 0.75rem; border: 1px dashed var(--border-color); border-radius: 8px; text-align: center;">
                    <span id="badgePreview" class="label-badge" style="background-color: #3B82F622; color: #3B82F6; font-size: 0.9rem; padding: 6px 16px;">Label Preview</span>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="closeModal()" style="padding:0.55rem 1.1rem; border:1.5px solid var(--border-color); border-radius:8px; background: transparent; color: var(--text-main); cursor:pointer; font-weight:600; font-family: inherit;">Cancel</button>
                <button type="submit" id="btnSave" style="padding:0.55rem 1.1rem; border:none; border-radius:8px; background: linear-gradient(135deg, #16a34a, #15803d); color:white; cursor:pointer; font-weight:600; font-family: inherit;">
                    <i class="ph ph-plus"></i> Add
                </button>
            </div>
        </form>
    </div>
</div>
<style> .modal-overlay.open { display:flex !important; } </style>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
const colorPresets = ['#EF4444','#F97316','#F59E0B','#EAB308','#84CC16','#22C55E','#10B981','#14B8A6','#06B6D4','#0EA5E9','#3B82F6','#6366F1','#8B5CF6','#A855F7','#D946EF','#EC4899','#F43F5E','#64748B'];

document.addEventListener('DOMContentLoaded', function() {
    // Render preset swatches
    const grid = document.getElementById('colorPresetsGrid');
    colorPresets.forEach(c => {
        const d = document.createElement('div');
        d.className = 'color-swatch';
        d.style.backgroundColor = c;
        d.onclick = () => setColor(c);
        grid.appendChild(d);
    });

    // Init DataTable
    if ($.fn.DataTable) {
        $('#labelsTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: '<i class="ph ph-magnifying-glass"></i>',
                searchPlaceholder: 'Search by Name...',
                lengthMenu: 'Rows per page _MENU_',
                info: 'Total _TOTAL_ data',
                paginate: { previous: '‹ Previous', next: 'Next ›' }
            }
        });
    }
});

function setColor(c) {
    document.getElementById('colorInput').value = c;
    updateColorPreview();
}

function handleColorInput() { updateColorPreview(); }

function updateColorPreview() {
    const c = document.getElementById('colorInput').value || '#3B82F6';
    document.getElementById('colorPreviewCircle').style.backgroundColor = c;
    updateBadgePreview();
    document.querySelectorAll('.color-swatch').forEach(s => {
        s.classList.toggle('active', rgbToHex(s.style.backgroundColor).toLowerCase() === c.toLowerCase());
    });
}

function updateBadgePreview() {
    const name = document.getElementById('labelName').value || 'Label Preview';
    const c = document.getElementById('colorInput').value || '#3B82F6';
    const badge = document.getElementById('badgePreview');
    badge.innerText = name;
    badge.style.color = c;
    badge.style.backgroundColor = c + '22';
}

function rgbToHex(rgb) {
    if (/^#/i.test(rgb)) return rgb;
    const m = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if (!m) return '';
    return '#' + [m[1],m[2],m[3]].map(x => parseInt(x).toString(16).padStart(2,'0')).join('');
}

function openAddLabelModal() {
    document.getElementById('modalTitleText').innerText = 'Tambah Label Baru';
    document.getElementById('btnSave').innerHTML = '<i class="ph ph-plus"></i> Add';
    document.getElementById('labelForm').action = "{{ route('master-data.labels.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('labelName').value = '';
    document.getElementById('parentId').value = '';
    document.getElementById('labelDesc').value = '';
    document.getElementById('sortOrder').value = '0';
    setColor('#3B82F6');
    document.getElementById('labelModal').classList.add('open');
}

function openEditLabelModal(id, name, color, parentId, desc, sortOrder) {
    document.getElementById('modalTitleText').innerText = 'Edit Label';
    document.getElementById('btnSave').innerHTML = '<i class="ph ph-floppy-disk"></i> Update';
    document.getElementById('labelForm').action = `/hydroponics/master-data/labels/${id}`;
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('labelName').value = name;
    document.getElementById('parentId').value = parentId || '';
    document.getElementById('labelDesc').value = desc;
    document.getElementById('sortOrder').value = sortOrder;
    setColor(color);
    document.getElementById('labelModal').classList.add('open');
}

function closeModal() {
    document.getElementById('labelModal').classList.remove('open');
}

function deleteLabel(id, name) {
    Swal.fire({
        title: 'Hapus Label?',
        html: `Label <b>"${name}"</b> akan dihapus secara permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/hydroponics/master-data/labels/${id}`;
            form.submit();
        }
    });
}

// Close modal on overlay click
document.getElementById('labelModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
