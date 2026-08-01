@extends('layouts.app')

@section('title', 'Notes & Lesson Learn')

@section('content')
    <div class="flex-between" style="margin-bottom: 1.5rem;">
        <div>
            <!-- DataTables will inject search and length menu here -->
        </div>
        
        <div style="display: flex; gap: 0.75rem;">
            <button class="btn btn-primary" type="button" onclick="document.getElementById('addModal').classList.add('active')">
                <i class="ph ph-plus"></i> Tambah Notes
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
                    <th>Date</th>
                    <th>Knowledge Area</th>
                    <th>Life Cycle</th>
                    <th style="width: 25%">Event Description</th>
                    <th>Event Impact</th>
                    <th style="width: 25%">Lesson Learned</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notes as $index => $note)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="color: var(--text-muted);">{{ $note->date ? \Carbon\Carbon::parse($note->date)->format('M d, Y') : '--' }}</td>
                    <td style="color: var(--text-muted);">{{ $note->knowledge_area }}</td>
                    <td style="color: var(--text-muted);">{{ $note->life_cycle }}</td>
                    <td style="color: var(--text-muted);">{{ Str::limit($note->event_description, 80) }}</td>
                    <td><span class="badge {{ $note->event_impact == 'Positive' ? 'badge-success' : 'badge-negative' }}">{{ $note->event_impact }}</span></td>
                    <td style="color: var(--text-muted);">{{ Str::limit($note->lesson_learned, 80) }}</td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="dt-action-btn dt-btn-edit" title="Edit"><i class="ph ph-pencil-simple"></i></button>
                            <button type="button" class="dt-action-btn dt-btn-detail" title="Detail"><i class="ph ph-list-bullets"></i></button>
                            <form action="{{ route('logs.notes.delete', $note->id) }}" method="POST" onsubmit="return confirm('Hapus note ini?');" style="margin: 0;">
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
                <h3>Tambah Note / Lesson Learn</h3>
                <i class="ph ph-x close-modal" onclick="document.getElementById('addModal').classList.remove('active')"></i>
            </div>
            <form action="{{ route('logs.notes.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Knowledge Area</label>
                        <input type="text" name="knowledge_area" class="form-control" required placeholder="Contoh: Integration Management">
                    </div>
                    <div class="form-group">
                        <label>Life Cycle</label>
                        <input type="text" name="life_cycle" class="form-control" required placeholder="Contoh: Planning">
                    </div>
                </div>
                <div class="form-group">
                    <label>Event Description</label>
                    <textarea name="event_description" class="form-control" rows="3" placeholder="Deskripsi kejadian..."></textarea>
                </div>
                <div class="form-group">
                    <label>Event Impact</label>
                    <select name="event_impact" class="form-control" required>
                        <option value="Negative">Negative</option>
                        <option value="Positive">Positive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lesson Learned</label>
                    <textarea name="lesson_learned" class="form-control" rows="3" placeholder="Pelajaran yang dapat diambil..."></textarea>
                </div>
                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').classList.remove('active')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
