@extends('admin.layout')

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Edit Segmen Pelanggan</h1>
        <a href="/admin/clients" style="background: #7f8c8d; color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none;">&larr; Kembali</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <form action="/admin/clients/{{ $client->id }}/edit" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Nama Pelanggan / Institusi</label>
                <input type="text" name="name" value="{{ $client->name }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Sektor</label>
                <input type="text" name="description" value="{{ $client->description }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Urutan Tampil (Order)</label>
                <input type="number" name="order" value="{{ $client->order }}" style="width: 100px; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Upload Gambar/Logo Baru (opsional)</label>
                <input type="file" name="image_file" style="width: 100%;">
                <p style="font-size: 0.85rem; color: #7f8c8d; margin-top: 0.5rem;">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                @if($client->image)
                <div style="margin-top: 0.5rem;">
                    <strong>Gambar Saat Ini:</strong><br>
                    <img src="{{ $client->image }}" style="height: 60px; margin-top: 0.5rem; border-radius: 4px;">
                </div>
                @endif
            </div>
            <button type="submit" style="background: #3498db; color: white; border: none; padding: 0.5rem 1.5rem; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 1rem;">Update Segmen</button>
        </form>
    </div>
</div>
@endsection
