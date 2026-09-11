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
                        <a href="{{ route('products.index', ['kategori' => 'konvensional']) }}" style="color: {{ request('kategori') == 'konvensional' ? 'var(--color-primary)' : '#555' }}; text-decoration: none; font-weight: {{ request('kategori') == 'konvensional' ? '700' : '500' }};">Konvensional</a>
                    </li>
                    <li style="margin-bottom: 0.8rem;">
                        <a href="{{ route('products.index', ['kategori' => 'hidroponik']) }}" style="color: {{ request('kategori') == 'hidroponik' ? 'var(--color-primary)' : '#555' }}; text-decoration: none; font-weight: {{ request('kategori') == 'hidroponik' ? '700' : '500' }};">Hidroponik</a>
                    </li>
                    <li style="margin-bottom: 0.8rem;">
                        <a href="{{ route('products.index') }}" style="color: {{ !request('kategori') ? 'var(--color-primary)' : '#555' }}; text-decoration: none; font-weight: {{ !request('kategori') ? '700' : '500' }};">Semua Produk</a>
                    </li>
                </ul>


            </div>

            <!-- Main Content -->
            <div class="content-main">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h2 style="color: var(--color-primary-dark); font-size: 1.8rem; font-family: var(--font-serif); margin: 0;">Katalog Produk</h2>
                        @if(request('kategori'))
                            <p style="color: #888; font-size: 0.88rem; margin: 4px 0 0 0;">
                                Menampilkan kategori: <strong style="color: var(--color-primary); text-transform: capitalize;">{{ request('kategori') }}</strong>
                                — <a href="{{ route('products.index') }}" style="color: #aaa; font-size: 0.85rem;">Tampilkan semua</a>
                            </p>
                        @endif
                    </div>
                    <a href="https://wa.me/6282129589232?text=Halo%20ASR%20Farm%2C%0A%0APerkenalkan%2C%20saya%20%5BNama%20Anda%5D%20dari%20%5BPerusahaan%2FPersonal%5D.%0A%0ASaya%20ingin%20menanyakan%20informasi%20mengenai%3A%0A%E2%80%A2%20%5BTulis%20keperluan%20Anda%2C%20misal%3A%20produk%2C%20harga%2C%20kerjasama%2C%20dll%5D%0A%0ATerima%20kasih."
                       target="_blank" rel="noopener"
                       style="display: inline-flex; align-items: center; gap: 8px; background: #25D366; color: white; padding: 10px 20px; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 14px rgba(37,211,102,0.3); transition: all 0.3s;">
                        <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Tanya via WhatsApp
                    </a>
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

                    /* Custom Pagination */
                    .custom-pagination {
                        display: flex;
                        justify-content: center;
                        gap: 8px;
                        margin-top: 3rem;
                    }
                    .page-btn {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 40px;
                        height: 40px;
                        border-radius: 8px;
                        background: white;
                        color: var(--color-primary-dark);
                        font-weight: bold;
                        text-decoration: none;
                        border: 1px solid #e0e0e0;
                        transition: all 0.2s;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                    }
                    .page-btn:hover:not(.disabled):not(.active) {
                        background: #f0f7f1;
                        border-color: var(--color-primary);
                        color: var(--color-primary);
                    }
                    .page-btn.active {
                        background: var(--color-primary);
                        color: white;
                        border-color: var(--color-primary);
                    }
                    .page-btn.disabled {
                        color: #ccc;
                        background: #f9f9f9;
                        cursor: not-allowed;
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
                        <div style="padding: 1.5rem 1rem 1rem 1rem; text-align: left;">
                            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-primary-dark); font-weight: 700;">{{ $product->name }}</h3>
                            
                            @if($product->description)
                            <p style="color: #666; font-size: 0.85rem; margin-top: 0.5rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-align: justify;">
                                {{ $product->description }}
                            </p>
                            @endif

                            <div style="display: flex; justify-content: center; align-items: center; margin-top: 0.5rem; margin-bottom: 0.5rem;">
                                <div style="color: #f2a640; font-size: 1.3rem; letter-spacing: 3px;">
                                    ★★★★★
                                </div>
                            </div>

                            @php
                                $waText = urlencode("Halo ASR Farm, saya tertarik untuk memesan produk " . $product->name . ".");
                            @endphp
                            <a href="https://wa.me/6282129589232?text={{ $waText }}" target="_blank" rel="noopener" style="display: block; text-align: center; width: 100%; margin-top: 1.2rem; padding: 0.6rem; background: var(--color-primary); color: white; text-decoration: none; border-radius: 6px; font-weight: bold; transition: background 0.3s;">
                                Pesan via WA
                            </a>
                        </div>
                    </div>
                    @endforeach

                </div>

                <!-- Custom Pagination Links -->
                {{ $products->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
