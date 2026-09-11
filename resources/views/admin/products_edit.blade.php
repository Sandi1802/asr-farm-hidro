@extends('admin.layout')

@section('content')
<div>
    <h1>Edit Produk: {{ $product->name }}</h1>
    
    <div class="card" style="padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <form method="POST" action="/admin/products/{{ $product->id }}/update" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Nama Produk</label>
                <input type="text" name="name" value="{{ $product->name }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Kategori</label>
                <select name="category" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="konvensional" {{ $product->category == 'konvensional' ? 'selected' : '' }}>Konvensional</option>
                    <option value="hidroponik" {{ $product->category == 'hidroponik' ? 'selected' : '' }}>Hidroponik</option>
                </select>
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Tags (pisahkan dengan koma)</label>
                <input type="text" name="tags" value="{{ $product->tags }}" placeholder="Sayuran, Segar, Sehat" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Harga</label>
                <input type="text" name="price" value="{{ $product->price }}" placeholder="Rp 15.000 / 250g" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Upload Gambar (Atau isi URL di bawahnya)</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 0.5rem;">
                <input type="text" name="image" value="{{ $product->image }}" placeholder="URL Gambar (Opsional jika sudah upload)" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Deskripsi</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">{{ $product->description }}</textarea>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; font-weight: bold; cursor: pointer;">
                    <input type="checkbox" name="sale" value="1" {{ $product->sale ? 'checked' : '' }} style="margin-right: 0.5rem;">
                    Beri Badge Promo / Best Seller
                </label>
            </div>

            <button type="submit" style="background: var(--color-primary); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 4px; font-weight: bold; cursor: pointer;">Update Produk</button>
            <a href="/admin/products" style="margin-left: 1rem; color: #666; text-decoration: none;">Batal</a>
        </form>
    </div>
</div>
@endsection
