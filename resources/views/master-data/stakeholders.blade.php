@extends('layouts.app')

@section('title', 'Master Data — Stakeholders')

@section('content')
    <div class="flex-between" style="margin-bottom: 1.5rem;">
        <div>
            <!-- DataTables will inject search and length menu here -->
        </div>
        
        <div style="display: flex; gap: 0.75rem;">
            <button class="btn btn-primary" type="button" onclick="document.getElementById('addModal').classList.add('active')">
                <i class="ph ph-plus"></i> Tambah Stakeholder
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
                    <th>Name</th>
                    <th>Role</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stakeholders as $index => $stakeholder)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: 500;">{{ $stakeholder->name }}</td>
                    <td>{{ $stakeholder->role }}</td>
                    <td style="color: var(--text-muted);">{{ $stakeholder->contact_person }}</td>
                    <td style="color: var(--text-muted);">{{ $stakeholder->email }}</td>
                    <td style="color: var(--text-muted);">{{ $stakeholder->phone }}</td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="dt-action-btn dt-btn-edit" title="Edit"><i class="ph ph-pencil-simple"></i></button>
                            <button type="button" class="dt-action-btn dt-btn-detail" title="Detail"><i class="ph ph-list-bullets"></i></button>
                            <form action="{{ route('master-data.stakeholders.delete', $stakeholder->id) }}" method="POST" onsubmit="return confirm('Hapus stakeholder ini?');" style="margin: 0;">
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
                <h3>Tambah Stakeholder</h3>
                <i class="ph ph-x close-modal" onclick="document.getElementById('addModal').classList.remove('active')"></i>
            </div>
            <form action="{{ route('master-data.stakeholders.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Ministry of Defence">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category" class="form-control" required>
                            <option value="Customer">Customer</option>
                            <option value="Principal">Principal</option>
                            <option value="Konsorsium">Konsorsium</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Peran</label>
                        <input type="text" name="role" class="form-control" required placeholder="End User / CMS Provider">
                    </div>
                </div>
                <div class="form-group">
                    <label>Kontak Person</label>
                    <input type="text" name="contact_person" class="form-control" required placeholder="Nama Kontak">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Negara</label>
                        <input type="text" name="country" class="form-control" required placeholder="Indonesia">
                    </div>
                    <div class="form-group">
                        <label>Kode Emoji (Opsional)</label>
                        <input type="text" name="country_code" class="form-control" placeholder="🇮🇩">
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
