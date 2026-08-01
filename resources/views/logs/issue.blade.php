@extends('layouts.app')

@section('title', 'Issue Log')

@section('content')
    <div class="flex-between" style="margin-bottom: 1.5rem;">
        <div>
            <!-- DataTables will inject search and length menu here -->
        </div>
        
        <div style="display: flex; gap: 0.75rem;">
            <button class="btn btn-primary" type="button" onclick="document.getElementById('addModal').classList.add('active')">
                <i class="ph ph-plus"></i> Tambah Issue
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
                    <th>Issue</th>
                    <th style="width: 30%">Description</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Resolution</th>
                    <th>PIC</th>
                    <th>Date Reported</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($issues as $index => $issue)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: 500;">{{ $issue->issue }}</td>
                    <td style="color: var(--text-muted);">{{ Str::limit($issue->description, 100) }} <br><a href="#" style="color: var(--primary); text-decoration: none; font-size: 0.8rem;">show more</a></td>
                    <td><span class="badge {{ $issue->status == 'Completed' ? 'badge-success' : 'badge-warning' }}">{{ $issue->status }}</span></td>
                    <td><span class="badge {{ $issue->priority == 'High' ? 'badge-negative' : ($issue->priority == 'Medium' ? 'badge-warning' : 'badge-success') }}">{{ $issue->priority }}</span></td>
                    <td style="color: var(--text-muted);">{{ Str::limit($issue->resolution, 100) }} <br><a href="#" style="color: var(--primary); text-decoration: none; font-size: 0.8rem;">show more</a></td>
                    <td style="color: var(--text-muted);">{{ $issue->pic ?: '--' }}</td>
                    <td style="color: var(--text-muted);">{{ $issue->date_reported ? \Carbon\Carbon::parse($issue->date_reported)->format('M d, Y') : '--' }}</td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="dt-action-btn dt-btn-edit" title="Edit"><i class="ph ph-pencil-simple"></i></button>
                            <button type="button" class="dt-action-btn dt-btn-detail" title="Detail"><i class="ph ph-list-bullets"></i></button>
                            <form action="{{ route('logs.issues.delete', $issue->id) }}" method="POST" onsubmit="return confirm('Hapus issue ini?');" style="margin: 0;">
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
                <h3>Tambah Issue Log</h3>
                <i class="ph ph-x close-modal" onclick="document.getElementById('addModal').classList.remove('active')"></i>
            </div>
            <form action="{{ route('logs.issues.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Issue</label>
                    <input type="text" name="issue" class="form-control" required placeholder="Judul Issue">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi detail issue..."></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Open">Open</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" class="form-control" required>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Resolution / Mitigation</label>
                    <textarea name="resolution" class="form-control" rows="2" placeholder="Langkah penyelesaian..."></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>PIC (Person In Charge)</label>
                        <input type="text" name="pic" class="form-control" placeholder="Nama PIC">
                    </div>
                    <div class="form-group">
                        <label>Date Reported</label>
                        <input type="date" name="date_reported" class="form-control">
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
