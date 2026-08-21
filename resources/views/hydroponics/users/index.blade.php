@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')

<style>
    .form-group { margin-bottom: 1.25rem; }
    .form-group label {
        display: block; font-size: 0.875rem; font-weight: 500;
        color: var(--text-main); margin-bottom: 0.5rem;
    }
    .form-group label span { color: #dc2626; }
    .form-control, .form-select {
        width: 100%; padding: 0.625rem 0.75rem;
        border: 1px solid var(--border-color); border-radius: 6px;
        font-size: 0.875rem; color: var(--text-main);
        background: var(--card-bg); transition: all 0.2s;
        box-sizing: border-box;
    }
    .form-control:focus, .form-select:focus { outline: none; border-color: var(--asr-green); box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1); }
    .btn-primary-form {
        width: 100%; padding: 0.75rem;
        background: var(--asr-green);
        color: white; border: none; border-radius: 6px; font-size: 0.95rem;
        font-weight: 600; cursor: pointer; display: flex; align-items: center;
        justify-content: center; gap: 0.5rem; transition: background 0.2s;
    }
    .btn-primary-form:hover { background: var(--asr-green-dark); }
</style>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">Manajemen Pengguna</h1>
        <p style="color:var(--text-muted);font-size:0.9rem;margin:0.25rem 0 0;">Kelola akun pengguna, hak akses, dan kata sandi.</p>
    </div>

    @if(session('success'))
    <div style="padding: 1rem 1.25rem; background: rgba(22, 163, 74, 0.1); color: var(--asr-green); border-radius: 10px; border-left: 4px solid var(--asr-green); font-weight: 500;">
        <i class="ph ph-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="padding: 1rem 1.25rem; background: rgba(220, 38, 38, 0.1); color: #dc2626; border-radius: 10px; border-left: 4px solid #dc2626; font-weight: 500;">
        <i class="ph ph-warning-circle"></i> {{ session('error') }}
    </div>
    @endif

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.1rem; font-weight: 600; color: var(--text-main); margin: 0;">Daftar Pengguna</h2>
            <button class="btn btn-primary" onclick="openModal('addModal')" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph ph-plus"></i> Tambah Pengguna
            </button>
        </div>

        <div class="table-responsive">
            <table class="table datatable" id="usersTable">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Peran (Role)</th>
                        <th>Dibuat Pada</th>
                        <th style="text-align: right; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td style="font-weight: 500; color: var(--text-main);">{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; 
                                background: {{ $user->roleBadgeColor() }}1A; 
                                color: {{ $user->roleBadgeColor() }};">
                                {{ $user->roleLabel() }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td style="text-align: right;">
                            <button class="btn btn-sm btn-light" title="Edit" 
                                onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->username ?? '') }}', '{{ addslashes($user->email) }}', '{{ $user->role_agri }}')"
                                style="width: 32px; height: 32px; padding: 0; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; margin-right: 0.25rem;">
                                <i class="ph ph-pencil-simple" style="font-size: 1.1rem; color: #0ea5e9;"></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('hydroponics.users.destroy', $user->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light" title="Hapus" 
                                    style="width: 32px; height: 32px; padding: 0; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="ph ph-trash" style="font-size: 1.1rem; color: #ef4444;"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal" style="display:none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Tambah Pengguna</h3>
            <button class="close-modal" onclick="closeModal('addModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('hydroponics.users.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Lengkap <span>*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
                </div>
                <div class="form-group">
                    <label>Username (Login) <span>*</span></label>
                    <input type="text" name="username" class="form-control" required placeholder="Contoh: budi.s">
                </div>
                <div class="form-group">
                    <label>Email <span>*</span></label>
                    <input type="email" name="email" class="form-control" required placeholder="Contoh: budi@asrfarm.com">
                </div>
                <div class="form-group">
                    <label>Kata Sandi <span>*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
                </div>
                <div class="form-group">
                    <label>Peran Divisi (Role) <span>*</span></label>
                    <select name="role_agri" class="form-select" required>
                        <option value="admin">Tim IT / Super Admin</option>
                        <option value="atasan">Atasan / Manajer</option>
                        <option value="kepala_produksi">Kepala Produksi</option>
                        <option value="kepala_greenhouse">Kepala Greenhouse</option>
                        <option value="kepala_konven">Kepala Konven</option>
                        <option value="staff">Staff Umum</option>
                        <option value="keuangan">Tim Keuangan</option>
                        <option value="pemasaran">Tim Pemasaran</option>
                        <option value="packing">Tim Packing</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary-form" style="margin-top: 1.5rem;">
                    <i class="ph ph-floppy-disk"></i> Simpan Pengguna
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal" style="display:none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Edit Pengguna</h3>
            <button class="close-modal" onclick="closeModal('editModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Lengkap <span>*</span></label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Username (Login) <span>*</span></label>
                    <input type="text" name="username" id="editUsername" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email <span>*</span></label>
                    <input type="email" name="email" id="editEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Kata Sandi (Opsional)</label>
                    <input type="password" name="password" class="form-control" minlength="6" placeholder="Kosongkan jika tidak ingin mengubah sandi">
                </div>
                <div class="form-group">
                    <label>Peran Divisi (Role) <span>*</span></label>
                    <select name="role_agri" id="editRole" class="form-select" required>
                        <option value="admin">Tim IT / Super Admin</option>
                        <option value="atasan">Atasan / Manajer</option>
                        <option value="kepala_produksi">Kepala Produksi</option>
                        <option value="kepala_greenhouse">Kepala Greenhouse</option>
                        <option value="kepala_konven">Kepala Konven</option>
                        <option value="staff">Staff Umum</option>
                        <option value="keuangan">Tim Keuangan</option>
                        <option value="pemasaran">Tim Pemasaran</option>
                        <option value="packing">Tim Packing</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary-form" style="margin-top: 1.5rem;">
                    <i class="ph ph-floppy-disk"></i> Update Pengguna
                </button>
            </form>
        </div>
    </div>
</div>

<script>    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function openEditModal(id, name, username, email, role) {
        document.getElementById('editForm').action = '/hydroponics/master-data/users/' + id;
        document.getElementById('editName').value = name;
        document.getElementById('editUsername').value = username;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role;
        openModal('editModal');
    }
</script>
@endsection
