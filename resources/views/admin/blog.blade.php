@extends('admin.layout')

@section('content')
<div>
    <h1>Kelola Blog</h1>
    
    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1rem;">Tulis Artikel Baru</h2>
        <form method="POST" action="/admin/blog" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <input type="text" name="title" placeholder="Judul Artikel" required style="padding: 0.5rem; width: 100%; border: 1px solid #ccc; border-radius: 4px;" />
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem; font-size: 0.9rem;">Upload Gambar Header (Atau isi URL di bawahnya)</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 0.5rem;">
                <input type="text" name="image" placeholder="URL Gambar Header (Opsional jika sudah upload)" style="padding: 0.5rem; width: 100%; border: 1px solid #ccc; border-radius: 4px;" />
            </div>
            <textarea name="content" placeholder="Isi Artikel..." required style="padding: 0.5rem; min-height: 200px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></textarea>
            <button type="submit" class="btn btn-primary" style="align-self: flex-start;">Publikasikan Artikel</button>
        </form>
    </div>

    <div class="card" style="padding: 2rem;">
        <h2 style="margin-bottom: 1rem;">Daftar Artikel</h2>
        @if(count($posts) === 0)
            <p>Belum ada artikel.</p>
        @else
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee;">
                        <th style="padding: 1rem;">Gambar</th>
                        <th style="padding: 1rem;">Judul</th>
                        <th style="padding: 1rem;">Tanggal</th>
                        <th style="padding: 1rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 1rem;">
                            @if($post->image)
                                <img src="{{ $post->image }}" alt="{{ $post->title }}" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;" />
                            @else
                                <div style="width: 80px; height: 50px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #aaa;">No Img</div>
                            @endif
                        </td>
                        <td style="padding: 1rem;"><b>{{ $post->title }}</b></td>
                        <td style="padding: 1rem;">{{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y') }}</td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="/admin/blog/{{ $post->id }}/edit" style="background: var(--color-accent); color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Edit</a>
                                <form method="POST" action="/admin/blog/{{ $post->id }}/delete" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="background: #ff5252; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
