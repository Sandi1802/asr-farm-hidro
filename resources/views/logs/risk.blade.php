@extends('layouts.app')

@section('title', 'Risk Register')

@section('content')
    <div class="flex-between" style="margin-bottom: 1.5rem;">
        <div>
            <!-- DataTables will inject search and length menu here -->
        </div>
        
        <div style="display: flex; gap: 0.75rem;">
            <button class="btn btn-primary" type="button" onclick="document.getElementById('addModal').classList.add('active')">
                <i class="ph ph-plus"></i> Tambah Risk
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
                    <th>Date Raised</th>
                    <th>Risk</th>
                    <th style="width: 20%">Risk Description</th>
                    <th>Likelihood</th>
                    <th>Impact</th>
                    <th style="width: 20%">Impact Description</th>
                    <th>Risk Score</th>
                    <th>Mitigation</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($risks as $index => $risk)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="color: var(--text-muted);">{{ $risk->date_raised ? \Carbon\Carbon::parse($risk->date_raised)->format('M d, Y') : '--' }}</td>
                    <td style="font-weight: 500;">{{ $risk->risk }}</td>
                    <td style="color: var(--text-muted);">{{ Str::limit($risk->risk_description, 50) }}</td>
                    <td style="color: var(--text-muted);">{{ $risk->likelihood }}</td>
                    <td style="color: var(--text-muted);">{{ $risk->impact }}</td>
                    <td style="color: var(--text-muted);">{{ Str::limit($risk->impact_description, 50) }}</td>
                    <td>
                        <span class="badge {{ str_contains(strtolower($risk->risk_score), 'high') ? 'badge-negative' : (str_contains(strtolower($risk->risk_score), 'low') ? 'badge-success' : 'badge-warning') }}" style="{{ str_contains(strtolower($risk->risk_score), 'medium') ? 'background-color: #F59E0B; color: white;' : '' }}">
                            {{ $risk->risk_score }}
                        </span>
                    </td>
                    <td style="color: var(--text-muted);">{{ Str::limit($risk->mitigation, 50) }}</td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="dt-action-btn dt-btn-edit" title="Edit"><i class="ph ph-pencil-simple"></i></button>
                            <button type="button" class="dt-action-btn dt-btn-detail" title="Detail"><i class="ph ph-list-bullets"></i></button>
                            <form action="{{ route('logs.risks.delete', $risk->id) }}" method="POST" onsubmit="return confirm('Hapus risk ini?');" style="margin: 0;">
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
                <h3>Tambah Risk Register</h3>
                <i class="ph ph-x close-modal" onclick="document.getElementById('addModal').classList.remove('active')"></i>
            </div>
            <form action="{{ route('logs.risks.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Date Raised</label>
                    <input type="date" name="date_raised" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Risk Title</label>
                    <input type="text" name="risk" class="form-control" required placeholder="Judul Risiko">
                </div>
                <div class="form-group">
                    <label>Risk Description</label>
                    <textarea name="risk_description" class="form-control" rows="2" placeholder="Deskripsi risiko..."></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Likelihood</label>
                        <select name="likelihood" class="form-control" required>
                            <option value="Rare">Rare</option>
                            <option value="Unlikely">Unlikely</option>
                            <option value="Possible">Possible</option>
                            <option value="Likely">Likely</option>
                            <option value="Almost Certain">Almost Certain</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Impact</label>
                        <select name="impact" class="form-control" required>
                            <option value="Insignificant">Insignificant</option>
                            <option value="Minor">Minor</option>
                            <option value="Moderate">Moderate</option>
                            <option value="Major">Major</option>
                            <option value="Catastrophic">Catastrophic</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Impact Description</label>
                    <textarea name="impact_description" class="form-control" rows="2" placeholder="Deskripsi dampak..."></textarea>
                </div>
                <div class="form-group">
                    <label>Risk Score (Text)</label>
                    <input type="text" name="risk_score" class="form-control" placeholder="Contoh: 12 - Medium">
                </div>
                <div class="form-group">
                    <label>Mitigation Plan</label>
                    <textarea name="mitigation" class="form-control" rows="2" placeholder="Rencana mitigasi..."></textarea>
                </div>
                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').classList.remove('active')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
