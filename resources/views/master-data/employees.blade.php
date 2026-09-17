@extends('layouts.app')

@section('title', 'Master Data – Pegawai')

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

    @if(session('error'))
        <div style="padding: 1rem; background: #dc2626; color: white; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="padding: 1rem; background: #dc2626; color: white; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive">
        <table id="employeesTable" class="data-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Username</th>
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
                                <img src="{{ url('storage/' . $employee->avatar) }}" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
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
                    <td>
                        @if($employee->user && $employee->user->username)
                            <span style="font-family: monospace; font-size: 0.85rem; background: var(--asr-green-light); color: var(--asr-green); padding: 0.15rem 0.5rem; border-radius: 4px;">{{ $employee->user->username }}</span>
                        @else
                            <span style="color: var(--text-muted);">--</span>
                        @endif
                    </td>
                    <td>{{ $employee->email ?? '--' }}</td>
                    <td>{{ $employee->phone ?? '--' }}</td>
                    <td>
                        <span class="badge {{ $employee->status === 'Active' ? 'badge-success' : 'badge-negative' }}">
                            {{ $employee->status }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="dt-action-btn dt-btn-edit" title="Edit" onclick="openEditModal(this)" data-employee="{{ json_encode($employee) }}">
                                <i class="ph ph-pencil-simple"></i>
                            </button>
                            <button type="button" class="dt-action-btn dt-btn-delete" title="Delete" onclick="confirmAction('Hapus Pegawai', 'Apakah Anda yakin ingin menghapus pegawai ini? Data yang dihapus tidak dapat dikembalikan.', '{{ route('master-data.employees.delete', $employee->id) }}', 'DELETE')">
                                <i class="ph ph-trash"></i>
                            </button>
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
                            <option value="it_admin">Tim IT / Super Admin</option>
                            <option value="atasan">Atasan / Manajer</option>
                            <option value="produksi">Kepala Produksi (Global)</option>
                            <option value="produksi_gh">Kepala Produksi GH</option>
                            <option value="produksi_konvensional">Kepala Produksi Konvensional</option>
                            <option value="produksi_paprika">Kepala Produksi Paprika</option>
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

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Pegawai</h3>
                <i class="ph ph-x close-modal" onclick="document.getElementById('editModal').classList.remove('active')"></i>
            </div>
            <form action="" id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" name="nip" id="edit_nip" class="form-control" required placeholder="Contoh: 202401001">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control" required>
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
                    <input type="text" name="name" id="edit_name" class="form-control" required placeholder="Nama pegawai">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="position" id="edit_position" class="form-control" required placeholder="Contoh: Project Manager">
                    </div>
                    <div class="form-group">
                        <label>Departemen</label>
                        <input type="text" name="department" id="edit_department" class="form-control" required placeholder="Contoh: Engineering">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Username <span style="color:var(--text-muted); font-weight:400;">(Wajib untuk login)</span></label>
                        <input type="text" name="username" id="edit_username" class="form-control" required placeholder="Contoh: sandi.p">
                    </div>
                    <div class="form-group">
                        <label>No. Telepon <span style="color:var(--text-muted); font-weight:400;">(Opsional)</span></label>
                        <input type="text" name="phone" id="edit_phone" class="form-control" placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Role Akun</label>
                        <select name="role_agri" id="edit_role_agri" class="form-control" required>
                            <option value="it_admin">Tim IT / Super Admin</option>
                            <option value="atasan">Atasan / Manajer</option>
                            <option value="produksi">Kepala Produksi (Global)</option>
                            <option value="produksi_gh">Kepala Produksi GH</option>
                            <option value="produksi_konvensional">Kepala Produksi Konvensional</option>
                            <option value="produksi_paprika">Kepala Produksi Paprika</option>
                            <option value="keuangan">Tim Keuangan</option>
                            <option value="pemasaran">Tim Pemasaran</option>
                            <option value="packing">Tim Packing</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password Akun <span style="color:var(--text-muted); font-weight:400;">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" id="edit_password" class="form-control" placeholder="Minimal 6 karakter">
                    </div>
                </div>
                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('editModal').classList.remove('active')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#employeesTable').DataTable({
            responsive: true,
            pageLength: 10,
            dom: "<'flex-between' l <'dt-search' f>>rt<'flex-between' ip>",
            language: {
                search: '',
                searchPlaceholder: 'Cari...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                paginate: { previous: '‹', next: '›' },
                zeroRecords: 'Tidak ada data ditemukan'
            }
        });
    }
});

function openEditModal(btn) {
    var employee = JSON.parse(btn.getAttribute('data-employee'));
    var modal = document.getElementById('editModal');
    var form = document.getElementById('editForm');

    form.action = '/hydroponics/master-data/employees/' + employee.id;

    document.getElementById('edit_nip').value = employee.nip || '';
    document.getElementById('edit_name').value = employee.name || '';
    document.getElementById('edit_position').value = employee.position || '';
    document.getElementById('edit_department').value = employee.department || '';
    document.getElementById('edit_phone').value = employee.phone || '';
    document.getElementById('edit_status').value = employee.status || 'Active';
    document.getElementById('edit_password').value = '';

    if (employee.user) {
        document.getElementById('edit_username').value = employee.user.username || '';
        document.getElementById('edit_role_agri').value = employee.user.role_agri || '';
    } else {
        document.getElementById('edit_username').value = '';
        document.getElementById('edit_role_agri').value = '';
    }

    modal.classList.add('active');
}
</script>
@endsection

@endsection
