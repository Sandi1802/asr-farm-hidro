@extends('layouts.app')
@section('title', 'Daftar Pengguna & Role')
@section('content')

<style>
    .form-select {
        padding: 0.35rem; border-radius: 6px; border: 1px solid var(--border-color); font-size: 0.85rem;
    }
    .form-select:focus { outline: none; border-color: var(--asr-green); }
</style>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">Daftar Pengguna & Role</h1>
        <p style="color:var(--text-muted);font-size:0.9rem;margin:0.25rem 0 0;">Ubah role pengguna secara langsung. (Akun pengguna otomatis terbuat saat Karyawan ditambahkan)</p>
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

    <div class="card border-0 shadow-sm" style="background: var(--bg-card); border-radius: var(--radius-lg); padding: 1.5rem;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table datatable" id="usersTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="dt-no" style="width: 50px;">NO</th>
                            <th>NAMA ANGGOTA</th>
                            <th>USERNAME</th>
                            <th>ROLE SAAT INI</th>
                            <th style="width: 250px;">UBAH ROLE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td style="font-weight: 500; color: var(--text-main);">{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                <span style="padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; 
                                    background: {{ $user->roleBadgeColor() }}1A; 
                                    color: {{ $user->roleBadgeColor() }};">
                                    {{ $user->roleLabel() }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('hydroponics.users.update', $user->id) }}" method="POST" style="display: flex; gap: 0.5rem; align-items: center; margin: 0;">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ $user->name }}">
                                    <input type="hidden" name="username" value="{{ $user->username }}">
                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                    
                                    <select name="role_agri" class="form-select" style="min-width: 150px;" required>
                                        <option value="it_admin" {{ $user->role_agri == 'it_admin' ? 'selected' : '' }}>Tim IT / Super Admin</option>
                                        <option value="atasan" {{ $user->role_agri == 'atasan' ? 'selected' : '' }}>Atasan / Manajer</option>
                                        <option value="produksi" {{ $user->role_agri == 'produksi' ? 'selected' : '' }}>Kepala Produksi (Global)</option>
                                        <option value="produksi_gh" {{ $user->role_agri == 'produksi_gh' ? 'selected' : '' }}>Kepala Produksi GH</option>
                                        <option value="produksi_konven" {{ $user->role_agri == 'produksi_konven' ? 'selected' : '' }}>Kepala Produksi Konvensional</option>
                                        <option value="produksi_paprika" {{ $user->role_agri == 'produksi_paprika' ? 'selected' : '' }}>Kepala Produksi Paprika</option>
                                        <option value="keuangan" {{ $user->role_agri == 'keuangan' ? 'selected' : '' }}>Tim Keuangan</option>
                                        <option value="pemasaran" {{ $user->role_agri == 'pemasaran' ? 'selected' : '' }}>Tim Pemasaran</option>
                                        <option value="packing" {{ $user->role_agri == 'packing' ? 'selected' : '' }}>Tim Packing</option>
                                    </select>
                                    
                                    <button type="submit" class="btn btn-sm" style="background: var(--asr-green); color: white; border: none; padding: 0.35rem 0.6rem; border-radius: 6px;" title="Simpan Role">
                                        <i class="ph ph-floppy-disk" style="font-size: 1.1rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
