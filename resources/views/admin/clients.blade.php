@extends('admin.layout')

@section('content')
<div>
    <h1>Kelola Segmen Pelanggan</h1>
    <p>Tambah, edit, dan hapus logo/segmen pasar.</p>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 2rem;">
        <h3>Tambah Segmen Pelanggan Baru</h3>
        <form action="/admin/clients" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Nama Pelanggan / Institusi</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Sektor (mis. Industri F&B)</label>
                <input type="text" name="description" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Urutan Tampil (Order)</label>
                <input type="number" name="order" value="0" style="width: 100px; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Upload Gambar/Logo (opsional)</label>
                <input type="file" name="image_file" style="width: 100%;">
            </div>
            <button type="submit" style="background: #27ae60; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-weight: bold;">Simpan</button>
        </form>
    </div>

    <div class="card" style="padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h3>Daftar Segmen Pelanggan</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; text-align: left;">
                    <th style="padding: 0.5rem;">Logo</th>
                    <th style="padding: 0.5rem;">Nama</th>
                    <th style="padding: 0.5rem;">Sektor</th>
                    <th style="padding: 0.5rem;">Urutan</th>
                    <th style="padding: 0.5rem;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 0.5rem;">
                        @if($client->image)
                        <img src="{{ $client->image }}" style="height: 40px; border-radius: 4px;">
                        @else
                        -
                        @endif
                    </td>
                    <td style="padding: 0.5rem;">{{ $client->name }}</td>
                    <td style="padding: 0.5rem;">{{ $client->description }}</td>
                    <td style="padding: 0.5rem;">{{ $client->order }}</td>
                    <td style="padding: 0.5rem;">
                        <a href="/admin/clients/{{ $client->id }}/edit" style="color: #2980b9; margin-right: 1rem;">Edit</a>
                        <form action="/admin/clients/{{ $client->id }}/delete" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="color: #e74c3c; background: none; border: none; cursor: pointer;" onclick="return confirm('Hapus segmen ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
