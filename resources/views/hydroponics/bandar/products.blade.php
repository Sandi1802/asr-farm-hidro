@extends('layouts.app')

@section('content')
<div class="content-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>Data Produk / Komoditas Bandar</h2>
        <p>Kelola daftar barang yang Anda perdagangkan.</p>
    </div>
    <a href="{{ route('hydroponics.bandar') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>

<div class="card p-4 mb-4">
    <h3>Tambah Komoditas Baru</h3>
    <form action="/hydroponics/bandar/products" method="POST" style="display: flex; gap: 1rem; align-items: end;">
        @csrf
        <div style="flex: 1;">
            <label>Nama Komoditas</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: Cabai Merah" required>
        </div>
        <div style="flex: 1;">
            <label>Satuan</label>
            <input type="text" name="unit" class="form-control" placeholder="Contoh: Kg, Ikat" required>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="background: var(--asr-green); border:none;">Simpan Komoditas</button>
        </div>
    </form>
</div>

<div class="card p-4">
    <h3>Daftar Komoditas</h3>
    <table class="table datatable">
        <thead>
            <tr>
                <th>Nama Komoditas</th>
                <th>Satuan</th>
                <th>Sisa Stok Gudang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->unit }}</td>
                <td><span class="badge bg-success p-2 rounded" style="background: var(--asr-green); color: white;">{{ $p->stock }} {{ $p->unit }}</span></td>
                <td>
                    <form action="/hydroponics/bandar/products/{{ $p->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus komoditas ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="ph ph-trash"></i> Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Belum ada data komoditas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
