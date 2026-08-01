@extends('layouts.app')

@section('title', 'Master Data — Project Scope')

@section('content')
    <div class="flex-between" style="margin-bottom: 1.5rem;">
        <div>
            <!-- DataTables will inject search and length menu here -->
        </div>
        
        <div style="display: flex; gap: 0.75rem;">
            <button class="btn btn-primary" type="button" onclick="document.getElementById('addModal').classList.add('active')">
                <i class="ph ph-plus"></i> Tambah Scope
            </button>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background: var(--asr-green); color: white; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-container">
        <table class="table datatable" style="width: 100%;">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>System Name</th>
                    <th>Category</th>
                    <th>Principal</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scopes as $index => $scope)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; border-radius: var(--radius-md); background: var(--card-bg); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                                <i class="ph ph-cube" style="color: var(--primary);"></i>
                            </div>
                            <div>
                                <div style="font-weight: 500;">{{ $scope->system_name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ Str::limit($scope->description, 50) }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color: var(--text-muted);">{{ $scope->category }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.85rem;">{{ $scope->principal }}</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $scope->principal_country_code ?: '' }}</span>
                        </div>
                    </td>
                    <td><span class="badge {{ $scope->status == 'Completed' ? 'badge-success' : ($scope->status == 'Planning' ? 'badge-danger' : 'badge-warning') }}">{{ $scope->status }}</span></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="flex: 1; height: 6px; background: #E2E8F0; border-radius: 3px; overflow: hidden;">
                                <div style="height: 100%; width: {{ $scope->progress }}%; background: {{ $scope->progress == 100 ? 'var(--success)' : ($scope->progress < 30 ? 'var(--danger)' : 'var(--warning)') }}; border-radius: 3px;"></div>
                            </div>
                            <span style="font-size: 0.8rem; font-weight: 600; color: {{ $scope->progress == 100 ? 'var(--success)' : ($scope->progress < 30 ? 'var(--danger)' : 'var(--warning)') }};">{{ $scope->progress }}%</span>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="dt-action-btn dt-btn-edit" title="Edit"><i class="ph ph-pencil-simple"></i></button>
                            <button type="button" class="dt-action-btn dt-btn-detail" title="Detail"><i class="ph ph-list-bullets"></i></button>
                            <form action="{{ route('master-data.scope.delete', $scope->id) }}" method="POST" onsubmit="return confirm('Hapus scope ini?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dt-action-btn dt-btn-delete" title="Delete"><i class="ph ph-trash"></i></button>
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
                <h3>Tambah Project Scope</h3>
                <i class="ph ph-x close-modal" onclick="document.getElementById('addModal').classList.remove('active')"></i>
            </div>
            <form action="{{ route('master-data.scope.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>System Name</label>
                    <input type="text" name="system_name" class="form-control" required placeholder="Nama Sistem">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi singkat..."></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="Platform">Platform</option>
                            <option value="Sensor">Sensor</option>
                            <option value="Weapon">Weapon</option>
                            <option value="Communication">Communication</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Principal</label>
                        <input type="text" name="principal" class="form-control" placeholder="Nama Perusahaan Principal">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Planning">Planning</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Progress (%)</label>
                        <input type="number" name="progress" class="form-control" min="0" max="100" value="0">
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
