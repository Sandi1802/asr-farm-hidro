@extends('layouts.app')

@section('title', 'Daily Hidroponik')

@section('content')
<div class="container-fluid" style="padding: 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1 style="margin:0; font-weight:700; color:var(--text-main);">Daily Hidroponik</h1>
        <div style="display:flex; gap:1rem;">
            <input type="date" id="taskDate" class="form-control" value="{{ $date }}" onchange="loadTasks()" style="border-radius:8px; padding:0.5rem 1rem;">
            <button onclick="showAddPRModal()" class="btn btn-primary" style="border-radius:8px; display:flex; align-items:center; gap:0.5rem; background:var(--asr-green); border:none;">
                <i class="ph ph-plus-circle"></i> Tambah PR
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1.5rem; margin-bottom: 2rem;">
        <div style="background:white; padding:1.5rem; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05); display:flex; flex-direction:column; align-items:center;">
            <div style="font-size:3rem; font-weight:800; color:#ca8a04;" id="statOpening">0%</div>
            <div style="color:var(--text-muted); font-weight:600;">Opening Shift</div>
        </div>
        <div style="background:white; padding:1.5rem; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05); display:flex; flex-direction:column; align-items:center;">
            <div style="font-size:3rem; font-weight:800; color:#0ea5e9;" id="statSiang">0%</div>
            <div style="color:var(--text-muted); font-weight:600;">Siang Shift</div>
        </div>
        <div style="background:white; padding:1.5rem; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05); display:flex; flex-direction:column; align-items:center;">
            <div style="font-size:3rem; font-weight:800; color:#10b981;" id="statClosing">0%</div>
            <div style="color:var(--text-muted); font-weight:600;">Closing Shift</div>
        </div>
        <div style="background:white; padding:1.5rem; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05); display:flex; flex-direction:column; align-items:center;">
            <div style="font-size:3rem; font-weight:800; color:#f43f5e;" id="statPR">0</div>
            <div style="color:var(--text-muted); font-weight:600;"> Task Kepala Produksi</div>
        </div>
    </div>

    <!-- Task Lists -->
    <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1.5rem;">
        <!-- Opening -->
        <div class="task-column">
            <h3 style="border-bottom:3px solid #ca8a04; padding-bottom:0.5rem;"><i class="ph ph-sun" style="color:#ca8a04;"></i> Opening</h3>
            <div class="table-responsive">
                <table id="dt-opening" class="table" style="width:100%">
                    <thead><tr><th style="width:30px;">Aksi</th><th>Pekerjaan</th><th style="width:50px;">Notes</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <!-- Siang -->
        <div class="task-column">
            <h3 style="border-bottom:3px solid #0ea5e9; padding-bottom:0.5rem;"><i class="ph ph-cloud-sun" style="color:#0ea5e9;"></i> Operasional Siang</h3>
            <div class="table-responsive">
                <table id="dt-siang" class="table" style="width:100%">
                    <thead><tr><th style="width:30px;">Aksi</th><th>Pekerjaan</th><th style="width:50px;">Notes</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <!-- Closing -->
        <div class="task-column">
            <h3 style="border-bottom:3px solid #10b981; padding-bottom:0.5rem;"><i class="ph ph-moon" style="color:#10b981;"></i> Closing</h3>
            <div class="table-responsive">
                <table id="dt-closing" class="table" style="width:100%">
                    <thead><tr><th style="width:30px;">Aksi</th><th>Pekerjaan</th><th style="width:50px;">Notes</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <!-- PR -->
        <div class="task-column">
            <h3 style="border-bottom:3px solid #f43f5e; padding-bottom:0.5rem;"><i class="ph ph-push-pin" style="color:#f43f5e;"></i> Tugas Khusus Dede</h3>
            <div class="table-responsive">
                <table id="dt-pr" class="table" style="width:100%">
                    <thead><tr><th style="width:30px;">Aksi</th><th>Pekerjaan</th><th style="width:50px;">Notes</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Laporan Pekerjaan Lainnya -->
    <div style="background:white; padding:1.5rem; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05); margin-top:2rem;">
        <h3 style="border-bottom:3px solid #6366f1; padding-bottom:0.5rem; margin-top:0;">
            <i class="ph ph-notebook" style="color:#6366f1;"></i> Laporan Pekerjaan Lainnya
        </h3>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1rem;">
            Tuliskan pekerjaan-pekerjaan tambahan (di luar rutinitas di atas) yang dikerjakan hari ini. Laporan akan tersimpan otomatis saat Anda selesai mengetik (klik di luar kotak).
        </p>
        <textarea id="catatanHarian" rows="5" onblur="saveCatatanHarian()" placeholder="Contoh: Memperbaiki atap bocor di GH 1..." style="width:100%; border:1px solid #ccc; border-radius:8px; padding:1rem; font-family:inherit; resize:vertical;"></textarea>
        <div id="catatanStatus" style="font-size:0.8rem; color:#10b981; margin-top:0.5rem; text-align:right; min-height:1rem;"></div>
    </div>
</div>

<!-- Modal Notes -->
<div id="noteModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:1.5rem; width:100%; max-width:400px;">
        <h4 id="noteTitle" style="margin-top:0;">Catatan Tugas</h4>
        <input type="hidden" id="noteTaskId">
        <textarea id="noteContent" rows="4" style="width:100%; border:1px solid #ccc; border-radius:8px; padding:0.5rem; margin-bottom:1rem;"></textarea>
        <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
            <button onclick="closeNoteModal()" class="btn btn-secondary">Batal</button>
            <button onclick="saveNote()" class="btn btn-primary" style="background:var(--asr-green); border:none;">Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Tambah PR -->
<div id="prModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:1.5rem; width:100%; max-width:400px;">
        <h4 style="margin-top:0;">Tambah Tugas Untuk Kepala GreenHouse</h4>
        <div style="margin-bottom:1rem;">
            <label style="font-weight:600; display:block; margin-bottom:0.3rem;">Nama Pekerjaan</label>
            <input type="text" id="prTitle" style="width:100%; border:1px solid #ccc; border-radius:8px; padding:0.5rem;">
        </div>
        <div style="margin-bottom:1rem;">
            <label style="font-weight:600; display:block; margin-bottom:0.3rem;">Keterangan/Catatan</label>
            <textarea id="prNotes" rows="3" style="width:100%; border:1px solid #ccc; border-radius:8px; padding:0.5rem;"></textarea>
        </div>
        <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
            <button onclick="closePRModal()" class="btn btn-secondary">Batal</button>
            <button onclick="savePR()" class="btn btn-primary" style="background:var(--asr-green); border:none;">Simpan</button>
        </div>
    </div>
</div>

<style>
.task-column {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.task-list {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}
.task-item {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    padding: 0.8rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    transition: all 0.2s;
}
.task-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.task-item.completed {
    background: #f8fafc;
    border-color: #e2e8f0;
}
.task-item.completed .task-title {
    text-decoration: line-through;
    color: var(--text-muted);
}
.task-check {
    width: 24px;
    height: 24px;
    cursor: pointer;
    flex-shrink: 0;
    margin-top: 2px;
}
.task-content {
    flex-grow: 1;
}
.task-title {
    font-weight: 600;
    color: var(--text-main);
    margin-bottom: 0.2rem;
    font-size: 0.95rem;
}
.task-meta {
    font-size: 0.75rem;
    color: var(--text-muted);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.task-note-text {
    font-size: 0.8rem;
    background: #fef9c3;
    color: #854d0e;
    padding: 0.3rem 0.5rem;
    border-radius: 4px;
    margin-top: 0.4rem;
    display: inline-block;
}
.btn-note {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 2px;
}
.btn-note:hover {
    color: var(--asr-green);
}
.modal-overlay.open {
    display: flex !important;
}
</style>

<script>
let allTasks = [];
let dtTables = {};

document.addEventListener("DOMContentLoaded", function() {
    ["opening", "siang", "closing", "pr"].forEach(type => {
        dtTables[type] = $("#dt-" + type).DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            language: { search: "", searchPlaceholder: "Cari..." },
            ordering: false,
            dom: "<'flex-between' l <'dt-search' f>>rt<'flex-between' ip>"
        });
    });
    loadTasks();
});

function loadTasks() {
    const date = document.getElementById('taskDate').value;
    fetch(`/hydroponics/daily-tasks-api?date=${date}`)
        .then(res => res.json())
        .then(data => {
            allTasks = data;
            renderTasks();
        });
}

function renderTasks() {
    const lists = { opening: [], siang: [], closing: [], pr: [] };
    const stats = { opening: { total:0, done:0 }, siang: { total:0, done:0 }, closing: { total:0, done:0 }, pr: { pending:0 } };

    allTasks.forEach(t => {
        const type = t.is_pr ? 'pr' : t.shift;
        if(lists[type]) {
            lists[type].push(t);
            if(type !== 'pr') {
                stats[type].total++;
                if(t.status === 'completed') stats[type].done++;
            } else {
                if(t.status === 'pending') stats.pr.pending++;
            }
        }
    });

    ['opening', 'siang', 'closing', 'pr'].forEach(type => {
        if(!dtTables[type]) return;
        const table = dtTables[type];
        table.clear();
        
        lists[type].forEach(t => {
            const isCompleted = t.status === 'completed';
            
            let noteHtml = '';
            if(t.notes) {
                noteHtml = `<div class="task-note-text" style="font-size:0.85rem; color:#666; margin-top:4px;">${t.notes}</div>`;
            }
            
            let metaHtml = '';
            if(isCompleted && t.completer) {
                const time = t.completed_at ? new Date(t.completed_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
                metaHtml = `
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.3rem;">
                        Oleh ${t.completer.name} ${time}
                    </div>
                `;
            }

            const checkboxHtml = `<div style="display:flex; align-items:center; justify-content:center;">
                <input type="checkbox" class="task-check" style="width:20px; height:20px; cursor:pointer;" ${isCompleted ? 'checked' : ''} onchange="toggleTask(${t.id})">
            </div>`;
            
            const taskContent = `
                <div class="task-title" style="font-weight:600; color:var(--text-main); font-size:0.95rem; ${isCompleted ? 'text-decoration:line-through; color:#999;' : ''}">
                    ${t.task_name}
                </div>
                ${noteHtml}
                ${metaHtml}
            `;

            let n = t.notes || '';
            n = n.replace(/'/g, "\\'");
            const btnHtml = `<button onclick="openNoteModal(${t.id}, '${n}')" class="btn-icon" title="Catatan" style="background:transparent; border:none; color:var(--text-muted); cursor:pointer; padding:0.3rem;">
                                <i class="ph ph-chat-text" style="font-size:1.2rem;"></i>
                            </button>`;

            table.row.add([checkboxHtml, taskContent, btnHtml]);
        });
        table.draw(false);
    });

    // Update Stats
    document.getElementById('statOpening').innerText = stats.opening.total > 0 ? Math.round((stats.opening.done / stats.opening.total) * 100) + '%' : '0%';
    document.getElementById('statSiang').innerText = stats.siang.total > 0 ? Math.round((stats.siang.done / stats.siang.total) * 100) + '%' : '0%';
    document.getElementById('statClosing').innerText = stats.closing.total > 0 ? Math.round((stats.closing.done / stats.closing.total) * 100) + '%' : '0%';
    document.getElementById('statPR').innerText = stats.pr.pending + ' Pending';

    const catatan = allTasks.find(t => t.shift === 'catatan');
    if (catatan) {
        document.getElementById('catatanHarian').value = catatan.notes || '';
    } else {
        document.getElementById('catatanHarian').value = '';
    }

}

function toggleTask(id) {
    fetch(`/hydroponics/daily-tasks-api/${id}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const idx = allTasks.findIndex(t => t.id === id);
            if(idx > -1) allTasks[idx] = data.task;
            renderTasks();
        }
    });
}

function openNoteModal(id, currentNote) {
    document.getElementById('noteTaskId').value = id;
    document.getElementById('noteContent').value = currentNote !== 'null' ? currentNote : '';
    document.getElementById('noteModal').classList.add('open');
}

function closeNoteModal() {
    document.getElementById('noteModal').classList.remove('open');
}

function saveNote() {
    const id = document.getElementById('noteTaskId').value;
    const notes = document.getElementById('noteContent').value;
    
    fetch(`/hydroponics/daily-tasks-api/${id}/note`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const idx = allTasks.findIndex(t => t.id == id);
            if(idx > -1) allTasks[idx].notes = notes;
            renderTasks();
            closeNoteModal();
        }
    });
}

function showAddPRModal() {
    document.getElementById('prTitle').value = '';
    document.getElementById('prNotes').value = '';
    document.getElementById('prModal').style.display = 'flex';
}

function closePRModal() {
    document.getElementById('prModal').style.display = 'none';
}

function savePR() {
    const title = document.getElementById('prTitle').value;
    const notes = document.getElementById('prNotes').value;
    const date = document.getElementById('taskDate').value;
    
    if(!title) return alert('Nama pekerjaan harus diisi');
    
    fetch(`/hydroponics/daily-tasks-api`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            task_name: title,
            notes: notes,
            date: date,
            shift: 'pr',
            is_pr: true
        })
    })
    .then(async res => {
        if(!res.ok) {
            const errData = await res.json().catch(() => ({}));
            throw new Error(errData.message || 'Gagal menyimpan data (HTTP ' + res.status + ')');
        }
        return res.json();
    })
    .then(data => {
        if(data.success) {
            allTasks.push(data.task);
            renderTasks();
            closePRModal();
        } else {
            alert('Gagal: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan: ' + err.message);
    });
}

function saveCatatanHarian() {
    const text = document.getElementById("catatanHarian").value;
    const date = document.getElementById("taskDate").value;
    const statusEl = document.getElementById("catatanStatus");
    
    statusEl.innerText = "Menyimpan...";
    
    fetch("/hydroponics/daily-tasks-api/catatan", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]') .getAttribute("content")
        },
        body: JSON.stringify({ date: date, notes: text })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            statusEl.innerText = "Tersimpan pada " + new Date().toLocaleTimeString();
            setTimeout(() => { if(statusEl.innerText.startsWith("Tersimpan")) statusEl.innerText=""; }, 3000);
        } else {
            statusEl.innerText = "Gagal menyimpan";
        }
    }).catch(err => {
        statusEl.innerText = "Gagal menyimpan: " + err.message;
    });
}
</script>
@endsection
