@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 1.5rem; color: var(--text-main); font-weight: 600; margin-bottom: 0.5rem;">Daftar Baris di GH: {{ $gh->nama_gh }}</h1>
            <a href="{{ route('paprika.v2.index') }}" style="color: #007bff; text-decoration: none;">&larr; Kembali ke Daftar GH</a>
        </div>
        <button onclick="document.getElementById('addBarisModal').showModal()" class="btn-primary" style="padding: 0.5rem 1rem; border-radius: 8px; background: var(--primary-color); color: white; border: none; cursor: pointer;">
            <i class="fas fa-plus"></i> Tambah Baris & Pot
        </button>
    </div>
</div>

<div class="content-body" style="padding: 20px; background: white; border-radius: 10px; margin-top: 20px;">
    @if(session('success'))
        <div style="padding: 10px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;">{{ session('success') }}</div>
    @endif
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #eee;">
                <th style="padding: 10px; text-align: left;">ID</th>
                <th style="padding: 10px; text-align: left;">Nama Baris</th>
                <th style="padding: 10px; text-align: left;">Jumlah Pot</th>
                <th style="padding: 10px; text-align: left;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($baris as $b)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 10px;">{{ $b->id }}</td>
                <td style="padding: 10px;">{{ $b->nama_baris }}</td>
                <td style="padding: 10px;">{{ $b->pots_count }}</td>
                <td style="padding: 10px;">
                    <a href="{{ route('paprika.v2.pot', $b->id) }}" style="padding: 5px 10px; background: #007bff; color: white; border-radius: 5px; text-decoration: none; margin-right:5px;">Masuk ke Pot</a>
                    <form action="{{ route('paprika.v2.baris.destroy', $b->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus Baris ini beserta Pot-nya?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<dialog id="addBarisModal" style="border: none; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 400px; margin: auto;">
    <h3 style="margin-top:0;">Tambah Baris</h3>
    <form action="{{ route('paprika.v2.baris.store', $gh->id) }}" method="POST">
        @csrf
        <div style="margin-bottom: 15px;">
            <label>Nama Baris</label>
            <input type="text" name="nama_baris" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; margin-top: 5px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label>Jumlah Pot (Otomatis dibuat)</label>
            <input type="number" name="jumlah_pot" min="1" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; margin-top: 5px;">
        </div>
        <div style="text-align: right;">
            <button type="button" onclick="document.getElementById('addBarisModal').close()" style="padding: 8px 15px; border: none; background: #ccc; border-radius: 5px; cursor: pointer;">Batal</button>
            <button type="submit" style="padding: 8px 15px; border: none; background: #28a745; color: white; border-radius: 5px; cursor: pointer; margin-left: 10px;">Simpan</button>
        </div>
    </form>
</dialog>
@endsection
