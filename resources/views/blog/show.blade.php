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

            {{-- SOCIAL INTERACTIONS --}}
            <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; gap: 1.5rem; color: #555; font-size: 1.1rem; align-items: center;">
                    <span title="Dilihat"><i class="fas fa-eye"></i> {{ $post->views }}x</span>
                    <button id="likeBtn" onclick="likePost({{ $post->id }})" style="background: none; border: none; cursor: pointer; color: #e74c3c; font-size: 1.1rem; display: flex; gap: 0.5rem; align-items: center; transition: transform 0.2s;">
                        <i class="fas fa-heart"></i> <span id="likeCount">{{ $post->likes }}</span> Suka
                    </button>
                    <span><i class="fas fa-comment"></i> {{ $post->comments->count() }} Komentar</span>
                </div>
                
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <span style="color: #777; font-size: 0.9rem;">Bagikan:</span>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . url('/blog/' . $post->id)) }}" target="_blank" onclick="sharePost({{ $post->id }})" style="color: white; background: #25D366; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none;"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/blog/' . $post->id)) }}" target="_blank" onclick="sharePost({{ $post->id }})" style="color: white; background: #1877F2; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none;"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url('/blog/' . $post->id)) }}&text={{ urlencode($post->title) }}" target="_blank" onclick="sharePost({{ $post->id }})" style="color: white; background: #1DA1F2; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none;"><i class="fab fa-twitter"></i></a>
                </div>
            </div>

            {{-- COMMENTS SECTION --}}
            <div style="margin-top: 3rem; background: #f9f9f9; padding: 2rem; border-radius: 12px;">
                <h3 style="font-family: var(--font-serif); color: var(--color-primary-dark); margin-bottom: 1.5rem;">Komentar ({{ $post->comments->count() }})</h3>
                
                @if(session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="/blog/{{ $post->id }}/comment" method="POST" style="margin-bottom: 2rem; display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    <input type="text" name="name" placeholder="Nama Anda" required style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-family: var(--font-sans);">
                    <textarea name="content" placeholder="Tulis komentar Anda di sini..." rows="3" required style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-family: var(--font-sans);"></textarea>
                    <button type="submit" class="btn-solid-green" style="align-self: flex-start; border: none; cursor: pointer;">Kirim Komentar</button>
                </form>

                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @forelse($post->comments()->latest()->get() as $comment)
                        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <strong style="color: var(--color-primary-dark);">{{ $comment->name }}</strong>
                                <small style="color: #999;">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p style="color: #555; margin: 0; line-height: 1.6;">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <p style="color: #888; text-align: center; font-style: italic;">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    @endforelse
                </div>
            </div>
            
            <div style="margin-top: 3rem; text-align: center;">
                <a href="/blog" class="btn-outline-green">← Kembali ke Artikel Lainnya</a>
            </div>
        </div>
    </div>
</div>

<script>
function likePost(id) {
    let btn = document.getElementById('likeBtn');
    btn.style.transform = 'scale(1.2)';
    setTimeout(() => btn.style.transform = 'scale(1)', 200);
    
    fetch(`/blog/${id}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById('likeCount').innerText = data.likes;
            btn.style.color = '#c0392b';
        }
    });
}

function sharePost(id) {
    fetch(`/blog/${id}/share`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    });
}
</script>
@endsection
