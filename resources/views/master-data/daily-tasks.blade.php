@extends("layouts.app")
@section("title", "Master Data - Daily Tasks")

@section("content")
<style>
    .shift-badge { padding: 4px 12px; border-radius: 999px; font-size: 0.82rem; font-weight: 600; text-transform: uppercase; }
    .shift-opening { background: rgba(234, 179, 8, 0.15); color: #EAB308; }
    .shift-siang { background: rgba(56, 189, 248, 0.15); color: #38BDF8; }
    .shift-closing { background: rgba(34, 197, 94, 0.15); color: #22C55E; }
</style>

<div class="content-container">
    <div class="header-action" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h2 class="page-title" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-check-square" style="color: var(--asr-green);"></i> Template Daily Task
        </h2>
        <button onclick="openAddModal()" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.1rem; font-size: 0.85rem;">
            <i class="ph ph-plus"></i> Tambah Tugas
        </button>
    </div>

    @if(session("success"))
    <div style="background: rgba(22, 163, 74, 0.1); color: var(--asr-green); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(22, 163, 74, 0.2); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <i class="ph ph-check-circle" style="font-size: 1.5rem;"></i>
        <span>{{ session("success") }}</span>
    </div>
    @endif
    @if($errors->any())
    <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(239, 68, 68, 0.2); margin-bottom: 1.5rem;">
        <ul style="margin:0; padding-left:1.5rem;">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card" style="padding: 1.5rem; overflow: hidden; background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border-color);">
        <div class="table-responsive">
            <table class="table datatable" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="dt-no" style="width: 50px;">NO</th>
                        <th>Shift</th>
                        <th>Nama Tugas</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $index => $t)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            <span class="shift-badge shift-{{ strtolower($t->shift) }}">
                                {{ $t->shift }}
                            </span>
                        </td>
                        <td>{{ $t->task_name }}</td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                <button onclick="openEditModal({{ $t->id }}, '{{ $t->shift }}', '{{ addslashes($t->task_name) }}')" class="btn-icon" title="Edit" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: none; padding: 0.4rem; border-radius: 6px; cursor: pointer;">
                                    <i class="ph ph-pencil-simple"></i>
                                </button>
                                <form action="{{ route('master-data.daily-tasks.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?');" style="display:inline;">
                                    @csrf
                                    @method("DELETE")
                                    <button type="submit" class="btn-icon" title="Delete" style="background: rgba(239,68,68,0.1); color: #ef4444; border: none; padding: 0.4rem; border-radius: 6px; cursor: pointer;">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<div id="taskModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: var(--card-bg); padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px; position: relative;">
        <h3 id="modalTitle" style="margin-top: 0; margin-bottom: 1.5rem; color: var(--text-main);">Tambah Tugas</h3>
        
        <form id="taskForm" action="{{ route('master-data.daily-tasks.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 500;">Shift</label>
                <select name="shift" id="shiftInput" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    <option value="opening">Opening</option>
                    <option value="siang">Siang</option>
                    <option value="closing">Closing</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 500;">Nama Tugas</label>
                <input type="text" name="task_name" id="taskNameInput" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeModal()" class="btn-secondary" style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid var(--border-color); background: transparent; cursor: pointer; color: var(--text-main);">Batal</button>
                <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; border-radius: 6px; background: var(--asr-green); color: white; border: none; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if($.fn.DataTable) {
            $(".datatable").DataTable({
                pageLength: 25,
                language: { search: "", searchPlaceholder: "Cari..." }
            });
        }
    });

    function openAddModal() {
        document.getElementById("modalTitle").innerText = "Tambah Tugas";
        const form = document.getElementById("taskForm");
        form.action = "{{ route('master-data.daily-tasks.store') }}";
        document.getElementById("formMethod").value = "POST";
        
        document.getElementById("shiftInput").value = "opening";
        document.getElementById("taskNameInput").value = "";
        
        document.getElementById("taskModal").style.display = "flex";
    }

    function openEditModal(id, shift, taskName) {
        document.getElementById("modalTitle").innerText = "Edit Tugas";
        const form = document.getElementById("taskForm");
        form.action = `/hydroponics/master-data/daily-tasks/${id}`;
        document.getElementById("formMethod").value = "PUT";
        
        document.getElementById("shiftInput").value = shift;
        document.getElementById("taskNameInput").value = taskName;
        
        document.getElementById("taskModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("taskModal").style.display = "none";
    }
</script>
@endsection

