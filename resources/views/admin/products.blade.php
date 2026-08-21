@extends('admin.layout')

@section('content')
<div>
    <h1>Kelola Produk</h1>
    
    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1rem;">Tambah Produk Baru</h2>
        <form method="POST" action="/admin/products" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <input type="text" name="name" placeholder="Nama Produk" required style="padding: 0.5rem; width: 100%; border: 1px solid #ccc; border-radius: 4px;" />
            <input type="text" name="category" placeholder="Kategori (Misal: Organic Food, Vegetables)" style="padding: 0.5rem; width: 100%; border: 1px solid #ccc; border-radius: 4px;" />
            <input type="text" name="tags" placeholder="Tags (Pisahkan dengan koma, misal: Citrus, Green)" style="padding: 0.5rem; width: 100%; border: 1px solid #ccc; border-radius: 4px;" />
            <input type="text" name="price" placeholder="Harga (Kosongkan jika tidak ada)" style="padding: 0.5rem; width: 100%; border: 1px solid #ccc; border-radius: 4px;" />
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem; font-size: 0.9rem;">Upload Gambar (Atau isi URL di bawahnya)</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 0.5rem;">
                <input type="text" name="image" placeholder="URL Gambar (Opsional jika sudah upload)" style="padding: 0.5rem; width: 100%; border: 1px solid #ccc; border-radius: 4px;" />
            </div>
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="sale" /> Produk Sedang Diobral (Obral!)
            </label>
            <textarea name="description" placeholder="Deskripsi Singkat" required style="padding: 0.5rem; min-height: 100px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></textarea>
            <button type="submit" class="btn btn-primary" style="align-self: flex-start;">Simpan Produk</button>
        </form>
    </div>

    <div class="card" style="padding: 2rem;">
        <h2 style="margin-bottom: 1rem;">Daftar Produk</h2>
        @if(count($products) === 0)
            <p>Belum ada produk.</p>
        @else
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee;">
                        <th style="padding: 1rem;">Gambar</th>
                        <th style="padding: 1rem;">Nama</th>
                        <th style="padding: 1rem;">Kategori / Tags</th>
                        <th style="padding: 1rem;">Harga</th>
                        <th style="padding: 1rem;">Obral</th>
                        <th style="padding: 1rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 1rem;">
                            @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" />
                            @else
                                <div style="width: 50px; height: 50px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #aaa;">No Img</div>
                            @endif
                        </td>
                        <td style="padding: 1rem;"><b>{{ $product->name }}</b></td>
                        <td style="padding: 1rem;">
                            <div>{{ $product->category }}</div>
                            <div style="font-size: 0.8rem; color: #666;">{{ $product->tags }}</div>
                        </td>
                        <td style="padding: 1rem;">{{ $product->price }}</td>
                        <td style="padding: 1rem;">
                            @if($product->sale)
                                <span style="background: var(--color-primary); color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">Ya</span>
                            @else
                                <span style="background: #ccc; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">Tidak</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="/admin/products/{{ $product->id }}/edit" style="background: var(--color-accent); color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Edit</a>
                                <form method="POST" action="/admin/products/{{ $product->id }}/delete" style="margin: 0;">
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
