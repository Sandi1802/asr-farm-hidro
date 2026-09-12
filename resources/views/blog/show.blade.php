@extends('layouts.app')

@section('content')
<style>
.article-banner {
    background: linear-gradient(to right, var(--color-primary-dark), var(--color-primary));
    padding: 6rem 16px 4rem 16px;
    color: white;
    text-align: center;
}
.article-content {
    max-width: 800px;
    margin: -3rem auto 5rem auto;
    background: white;
    padding: 3rem;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    position: relative;
    z-index: 10;
}
.article-title {
    font-size: 2.5rem;
    color: var(--color-primary-dark);
    font-family: var(--font-serif);
    margin-bottom: 1rem;
    line-height: 1.3;
}
.article-meta {
    color: #888;
    font-size: 0.95rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}
.article-body {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #444;
}
.article-body p {
    margin-bottom: 1.5rem;
}
.article-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 2rem;
}
</style>

<div>
    <div class="article-banner animate-fade-up">
        <div class="container">
            <h1 style="font-size: 2rem; font-family: var(--font-serif); margin-bottom: 0.5rem;">Artikel ASR Farm</h1>
            <p style="opacity: 0.8;">Edukasi & Informasi</p>
        </div>
    </div>

    <div class="container">
        <div class="article-content animate-fade-up delay-1">
            <h2 class="article-title">{{ $post->title }}</h2>
            <div class="article-meta">
                Ditulis pada {{ \Carbon\Carbon::parse($post->created_at)->format('d F Y') }}
            </div>
            
            @if($post->image)
                <img src="{{ $post->image }}" alt="{{ $post->title }}" class="article-image">
            @endif

            <div class="article-body">
                {!! $post->content !!}
            </div>
            
            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #eee; text-align: center;">
                <a href="/blog" class="btn-outline-green">← Kembali ke Artikel Lainnya</a>
            </div>
        </div>
    </div>
</div>
@endsection
