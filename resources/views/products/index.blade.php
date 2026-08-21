@extends('layouts.app')

@section('content')
<style>
.product-banner {
    position: relative;
    min-height: 25vh;
    display: flex;
    align-items: flex-end;
    background-image: linear-gradient(135deg, rgba(30,59,34,0.85) 0%, rgba(30,59,34,0.4) 60%), url('{{ asset('images/hidroponik.jpg') }}');
    background-size: cover;
    background-position: center 30%;
    padding: 7rem 16px 1.5rem 16px;
}
.product-banner-inner {
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 3rem;
}
.product-banner-left h1 {
    color: var(--color-accent);
    font-size: 3rem;
    font-weight: bold;
    font-family: var(--font-serif);
    padding-left: 1rem;
    border-left: 4px solid var(--color-accent);
}
.product-banner-right p {
    color: white;
    font-size: 1.05rem;
    line-height: 1.8;
    background: rgba(0,0,0,0.3);
    padding: 2rem;
    border-radius: 12px;
    backdrop-filter: blur(5px);
}
.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #f0f0f0;
    text-align: center;
    padding-bottom: 1.5rem;
}
.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}
.badge-sale {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--color-accent);
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
    z-index: 2;
}
.badge-category {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--color-primary);
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
    z-index: 2;
}
@media(max-width:768px) {
    .product-banner-left h1 { font-size: 2.2rem; }
}
</style>

<div>
    <!-- Banner -->
    <div class="product-banner animate-fade-up">
        <div class="product-banner-inner">
            <div class="product-banner-left" style="flex: 1; min-width: 250px;">
                <h1>Our Product</h1>
            </div>
            <div class="product-banner-right" style="flex: 1.5; min-width: 300px;">
                <p>Hasil bumi terbaik dari kebun hidroponik dan ladang organik kami. Dipanen dengan penuh cinta dan dikirim langsung dalam keadaan segar untuk keluarga Anda.</p>
            </div>
        </div>
    </div>

    <div class="container" style="padding: 4rem 16px;">
        <div class="page-layout">
            
            <!-- Sidebar -->
            <div class="sidebar">
                <h3 style="border-bottom: 2px solid var(--color-accent); padding-bottom: 0.5rem; margin-bottom: 1rem; color: var(--color-primary-dark);">Kategori Produk</h3>
                <ul style="list-style: none; margin-bottom: 2rem; padding: 0;">
                    <li style="margin-bottom: 0.8rem;">
                        <a href="#" style="color: #555; text-decoration: none; font-weight: 500;">Sayuran Hidroponik</a>
                    </li>
                    <li style="margin-bottom: 0.8rem;">
                        <a href="#" style="color: #555; text-decoration: none; font-weight: 500;">Sayuran Organik</a>
                    </li>
                    <li style="margin-bottom: 0.8rem;">
                        <a href="#" style="color: #555; text-decoration: none; font-weight: 500;">Buah-buahan</a>
                    </li>
                    <li style="margin-bottom: 0.8rem;">
                        <a href="#" style="color: #555; text-decoration: none; font-weight: 500;">Bumbu Dapur</a>
                    </li>
                </ul>

                <h3 style="border-bottom: 2px solid var(--color-accent); padding-bottom: 0.5rem; margin-bottom: 1rem; color: var(--color-primary-dark);">Tags</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <span style="background: #E8F0E8; color: var(--color-primary); padding: 5px 12px; border-radius: 20px; font-size: 0.85rem;">Hidroponik</span>
                    <span style="background: #E8F0E8; color: var(--color-primary); padding: 5px 12px; border-radius: 20px; font-size: 0.85rem;">Segar</span>
                    <span style="background: #E8F0E8; color: var(--color-primary); padding: 5px 12px; border-radius: 20px; font-size: 0.85rem;">Organik</span>
                    <span style="background: #E8F0E8; color: var(--color-primary); padding: 5px 12px; border-radius: 20px; font-size: 0.85rem;">Sayur Daun</span>
                </div>
            </div>

            <!-- Main Content -->
            <div class="content-main">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                    <h2 style="color: var(--color-primary-dark); font-size: 1.8rem; font-family: var(--font-serif); margin: 0;">Katalog Produk</h2>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <select style="padding: 0.6rem 1rem; border: 1px solid #ddd; border-radius: 8px; font-family: var(--font-sans); color: #555; outline: none;">
                            <option>Urutkan: Terbaru</option>
                            <option>Urutkan: Termurah</option>
                            <option>Urutkan: Termahal</option>
                        </select>
                    </div>
                </div>

                <style>
                    .product-grid {
                        display: grid;
                        grid-template-columns: repeat(2, 1fr);
                        gap: 1.5rem;
                    }
                    @media(min-width: 768px) {
                        .product-grid {
                            grid-template-columns: repeat(3, 1fr);
                        }
                    }
                    @media(min-width: 1024px) {
                        .product-grid {
                            grid-template-columns: repeat(4, 1fr);
                        }
                    }
                </style>
                <div class="product-grid">
                    


                    <!-- DB Products -->
                    @foreach($products as $product)
                    <div class="product-card animate-fade-up">
                        <div style="position: relative; height: 220px; background-color: #f9f9f9; background-image: {{ $product->image ? "url('{$product->image}')" : 'none' }}; background-size: cover; background-position: center;">
                            @if($product->category)
                                <div class="badge-category">{{ $product->category }}</div>
                            @endif
                            @if($product->sale)
                                <div class="badge-sale">Promo</div>
                            @endif
                            @if(!$product->image)
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc;">No Image</div>
                            @endif
                        </div>
                        <div style="padding: 1.5rem 1rem 0 1rem;">
                            <h3 style="margin: 0; font-size: 1.15rem; color: var(--color-primary-dark); font-weight: 700;">{{ $product->name }}</h3>
                            <div style="color: #888; font-size: 0.9rem; margin-top: 0.3rem;">Produk ASR Farm</div>
                            <div style="font-weight: bold; color: var(--color-accent); font-size: 1.2rem; margin-top: 0.8rem; letter-spacing: 2px;">
                                ★★★★★
                            </div>
                            <button style="width: 100%; margin-top: 1rem; padding: 0.6rem; background: var(--color-primary); color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s;">Pesan via WA</button>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
