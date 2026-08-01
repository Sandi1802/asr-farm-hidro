@extends('layouts.app')

@section('content')
<div class="content-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>Riwayat Transaksi Bandar</h2>
        <p>Catat barang masuk (pembelian dari petani) dan barang keluar (penjualan).</p>
    </div>
    <a href="{{ route('hydroponics.bandar') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>

<div class="card p-4 mb-4">
    <h3>Tambah Transaksi Baru</h3>
    <form action="/hydroponics/bandar/transactions" method="POST" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; align-items: end;">
        @csrf
        <div>
            <label>Jenis Transaksi</label>
            <select name="type" class="form-control" required>
                <option value="in">Barang Masuk (Beli dari Petani)</option>
                <option value="out">Barang Keluar (Jual/Distribusi)</option>
                <option value="wasted">Barang Terbuang (Busuk/Rusak)</option>
            </select>
        </div>
        <div>
            <label>Komoditas / Produk</label>
            <select name="product_id" class="form-control" required>
                @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->name }} (Stok: {{ $p->stock }} {{ $p->unit }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Mitra (Opsional)</label>
            <select name="partner_id" class="form-control">
                <option value="">-- Pilih Mitra --</option>
                @foreach($partners as $partner)
                <option value="{{ $partner->id }}">{{ $partner->name }} ({{ ucfirst($partner->type) }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Jumlah (Qty)</label>
            <input type="number" step="0.01" name="quantity" class="form-control" required>
        </div>
        <div>
            <label>Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        <div style="grid-column: span 3;">
            <label>Keterangan</label>
            <input type="text" name="notes" class="form-control" placeholder="Contoh: Panen cabai dari Pak Budi / Busuk karena hama">
        </div>
        <div style="grid-column: span 4; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="background: var(--asr-green); border:none;">Simpan Transaksi</button>
        </div>
    </form>
</div>

<div class="card p-4">
    <h3>Data Transaksi</h3>
    <table class="table datatable">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Komoditas</th>
                <th>Mitra</th>
                <th>Jumlah (Qty)</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>{{ $t->date }}</td>
                <td>
                    @if($t->type == 'in')
                    <span class="badge bg-success text-white p-1 rounded" style="background:#16a34a;"><i class="ph ph-arrow-down-left"></i> Masuk</span>
                    @elseif($t->type == 'out')
                    <span class="badge bg-primary text-white p-1 rounded" style="background:#2563eb;"><i class="ph ph-arrow-up-right"></i> Keluar</span>
                    @else
                    <span class="badge bg-danger text-white p-1 rounded" style="background:#dc2626;"><i class="ph ph-trash"></i> Terbuang</span>
                    @endif
                </td>
                <td>{{ $t->product->name ?? '-' }}</td>
                <td>{{ $t->partner->name ?? '-' }}</td>
                <td>{{ $t->quantity }} {{ $t->product->unit ?? '' }}</td>
                <td>{{ $t->notes }}</td>
                <td>
                    <form action="/hydroponics/bandar/transactions/{{ $t->id }}" method="POST" onsubmit="return confirm('Batalkan transaksi ini? Stok akan dikembalikan otomatis.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="ph ph-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
