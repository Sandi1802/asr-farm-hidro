@extends('admin.layout')

@section('content')
<div>
    <h1>Kelola Testimoni</h1>
    <p>Tambah, edit, dan hapus testimoni pengunjung.</p>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 2rem;">
        <h3>Tambah Testimoni Baru</h3>
        <form action="/admin/testimonials" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Nama Pengunjung</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">URL Foto Profil (opsional)</label>
                <input type="url" name="image" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Isi Testimoni</label>
                <textarea name="content" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" rows="4"></textarea>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Rating (1-5)</label>
                <input type="number" name="rating" min="1" max="5" value="5" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <button type="submit" style="background: var(--color-primary); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 4px; font-weight: bold; cursor: pointer;">Tambah Testimoni</button>
        </form>
    </div>

    <div class="card" style="padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h3>Daftar Testimoni</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #dee2e6;">Nama</th>
                    <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #dee2e6;">Testimoni</th>
                    <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #dee2e6;">Rating</th>
                    <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #dee2e6;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($testimonials as $testi)
                <tr>
                    <td style="padding: 1rem; border-bottom: 1px solid #dee2e6;">{{ $testi->name }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #dee2e6;">{{ Str::limit($testi->content, 50) }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #dee2e6;">{{ $testi->rating }}/5</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #dee2e6;">
                        <form action="/admin/testimonials/{{ $testi->id }}/delete" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                            @csrf
                            <button type="submit" style="background: #dc3545; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer;">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
