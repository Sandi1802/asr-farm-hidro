@extends('admin.layout')

@section('content')
<div>
    <h1>Dashboard</h1>
    <p>Selamat datang di Panel Admin ASR Farm.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
        <div class="card" style="padding: 2rem; text-align: center; border-top: 4px solid var(--color-primary);">
            <h3 style="color: #666; margin-bottom: 0.5rem;">Total Produk</h3>
            <p style="font-size: 3rem; font-weight: bold; color: var(--color-primary-dark); margin: 0;">{{ $productCount }}</p>
        </div>
        <div class="card" style="padding: 2rem; text-align: center; border-top: 4px solid var(--color-accent);">
            <h3 style="color: #666; margin-bottom: 0.5rem;">Total Artikel Blog</h3>
            <p style="font-size: 3rem; font-weight: bold; color: var(--color-primary-dark); margin: 0;">{{ $postCount }}</p>
        </div>
    </div>
</div>
@endsection
