@extends('layouts.app')

@section('title', 'Master Data — Pegawai')

@section('content')
    <div class="flex-between" style="margin-bottom: 1.5rem;">
        <div>
            <!-- DataTables will inject search and length menu here -->
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button class="btn btn-primary" type="button" onclick="document.getElementById('addModal').classList.add('active')">
                <i class="ph ph-plus"></i> Tambah Pegawai
            </button>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background: var(--asr-green); color: white; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="padding: 1rem; background: #ef4444; color: white; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-container">
        <table class="table datatable" style="width: 100%;">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $index => $employee)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-size: 0.85rem;">{{ $employee->nip }}</td>
                    <td style="font-weight: 500;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            @if($employee->avatar)
                                <img src="{{ asset('storage/' . $employee->avatar) }}" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-main); color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; border: 1px solid var(--border-color);">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </div>
                            @endif
                            {{ $employee->name }}
                        </div>
                    </td>
                    <td>{{ $employee->position }}</td>
                    <td>{{ $employee->department }}</td>
                    <td>{{ $employee->email ?? '--' }}</td>
                    <td>{{ $employee->phone ?? '--' }}</td>
                    <td>
                        <span class="badge {{ $employee->status === 'Active' ? 'badge-success' : 'badge-negative' }}">
                            {{ $employee->status }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="dt-action-btn dt-btn-edit" title="Edit">
                                <i class="ph ph-pencil-simple"></i>
                            </button>
                            <form action="{{ route('master-data.employees.delete', $employee->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus pegawai ini?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dt-action-btn dt-btn-delete" title="Delete">
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

    <!-- Add Modal -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambah Pegawai</h3>
                <i class="ph ph-x close-modal" onclick="document.getElementById('addModal').classList.remove('active')"></i>
            </div>
            <form action="{{ route('master-data.employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" name="nip" class="form-control" required placeholder="Contoh: 202401001">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label>Foto Profil / Avatar <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">(Opsional, Maks 2MB)</span></label>
                    <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/jpg" style="padding: 0.5rem; height: auto;">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required placeholder="Nama pegawai">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="position" class="form-control" required placeholder="Contoh: Project Manager">
                    </div>
                    <div class="form-group">
                        <label>Departemen</label>
                        <input type="text" name="department" class="form-control" required placeholder="Contoh: Engineering">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Username <span style="color:var(--text-muted); font-weight:400;">(Wajib untuk login)</span></label>
                        <input type="text" name="username" class="form-control" required placeholder="Contoh: sandi.p">
                    </div>
                    <div class="form-group">
                        <label>No. Telepon <span style="color:var(--text-muted); font-weight:400;">(Opsional)</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Role Akun</label>
                        <select name="role_agri" class="form-control" required>
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
                    <div class="form-group">
                        <label>Password Akun</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                    </div>
                </div>
                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').classList.remove('active')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
