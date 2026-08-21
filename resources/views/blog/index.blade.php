@extends('layouts.app')

@section('content')
<style>
.article-banner {
    position: relative;
    min-height: 25vh;
    display: flex;
    align-items: flex-end;
    background-image: linear-gradient(135deg, rgba(30,59,34,0.85) 0%, rgba(30,59,34,0.4) 60%), url('{{ asset('images/greenhouse.jpg') }}');
    background-size: cover;
    background-position: center 30%;
    padding: 7rem 16px 1.5rem 16px;
}
.article-banner-inner {
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 3rem;
}
.article-banner-left h1 {
    color: var(--color-accent);
    font-size: 3rem;
    font-weight: bold;
    font-family: var(--font-serif);
    padding-left: 1rem;
    border-left: 4px solid var(--color-accent);
}
.article-banner-right p {
    color: white;
    font-size: 1.05rem;
    line-height: 1.8;
    background: rgba(0,0,0,0.3);
    padding: 2rem;
    border-radius: 12px;
    backdrop-filter: blur(5px);
}
.article-card-overlay {
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}
.article-card-overlay:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}
@media(max-width:768px) {
    .article-banner-left h1 { font-size: 2.2rem; }
}
</style>

<div>
    <!-- Banner -->
    <div class="article-banner animate-fade-up">
        <div class="article-banner-inner">
            <div class="article-banner-left" style="flex: 1; min-width: 250px;">
                <h1>Article</h1>
            </div>
            <div class="article-banner-right" style="flex: 1.5; min-width: 300px;">
                <p>Kabar terbaru, tips pertanian, dan inspirasi alam. Temukan berbagai informasi menarik seputar pertanian organik, kesehatan, dan gaya hidup sehat bersama ASR Farm.</p>
            </div>
        </div>
    </div>

    <section style="padding: 4rem 16px; background-color: #FAF8F5;">
        <div class="container animate-fade-up delay-2">
            <h2 style="color: var(--color-primary-dark); font-size: 2.2rem; text-align: center; margin-bottom: 2.5rem; font-weight: bold;">Semua Artikel</h2>
            
            @if(count($posts) > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                @foreach($posts as $post)
                <div class="article-card-overlay">
                    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.1)); z-index: 1;"></div>
                    @if($post->image)
                        <img src="{{ $post->image }}" alt="{{ $post->title }}" style="width: 100%; height: 350px; object-fit: cover; display: block;">
                    @else
                        <div style="width: 100%; height: 350px; background: linear-gradient(135deg, #2F5836, #1a3a20); display: flex; align-items: center; justify-content: center;">
                            <span style="color: rgba(255,255,255,0.3); font-size: 4rem;">📰</span>
                        </div>
                    @endif
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 2rem; z-index: 2; color: white;">
                        <h3 style="font-size: 1.3rem; margin-bottom: 0.5rem; font-weight: bold; color: white;">{{ $post->title }}</h3>
                        <p style="color: #ccc; font-size: 0.9rem; margin-bottom: 1rem;">{{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y') }} · ASR Farm</p>
                        <p style="color: #eee; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">{{ \Illuminate\Support\Str::limit($post->content, 120) }}</p>
                        <a href="/blog/{{ $post->id }}" style="color: white; font-weight: 600; text-decoration: none; font-size: 0.9rem;">Read More »</a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p style="text-align: center; color: #888; font-size: 1.1rem;">Belum ada artikel yang dipublikasikan.</p>
            @endif
        </div>
    </section>
</div>
@endsection
