@extends('layouts.app')

@section('content')
<div class="content-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>Data Mitra & Petani</h2>
        <p>Kelola data orang/petani yang bekerja sama dengan Anda.</p>
    </div>
    <a href="{{ route('hydroponics.bandar') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>

<div class="card p-4 mb-4">
    <h3>Tambah Mitra Baru</h3>
    <form action="/hydroponics/bandar/partners" method="POST" style="display: flex; gap: 1rem; align-items: end;">
        @csrf
        <div style="flex: 1;">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div>
            <label>Tipe Mitra</label>
            <select name="type" class="form-control" required>
                <option value="supplier">Petani Pemasok (Sumber Barang)</option>
                <option value="buyer">Pembeli / Agen (Tujuan Distribusi)</option>
            </select>
        </div>
        <div style="flex: 1;">
            <label>No HP / Telepon</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div style="flex: 1;">
            <label>Alamat (Opsional)</label>
            <input type="text" name="address" class="form-control">
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="background: var(--asr-green); border:none;">Simpan Mitra</button>
        </div>
    </form>
</div>

<div class="card p-4">
    <h3>Daftar Mitra</h3>
    <table class="table datatable">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tipe Mitra</th>
                <th>No HP</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($partners as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>
                    @if($p->type == 'supplier')
                        <span class="badge bg-secondary">Petani Pemasok</span>
                    @else
                        <span class="badge bg-info text-dark">Pembeli / Agen</span>
                    @endif
                </td>
                <td>{{ $p->phone ?? '-' }}</td>
                <td>{{ $p->address ?? '-' }}</td>
                <td>
                    <form action="/hydroponics/bandar/partners/{{ $p->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus mitra ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="ph ph-trash"></i> Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Belum ada data mitra.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
