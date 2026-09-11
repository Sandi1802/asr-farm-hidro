@extends('layouts.app')

@section('content')
<style>
/* Testimonials Banner */
.testi-banner {
    position: relative;
    min-height: 25vh;
    display: flex;
    align-items: flex-end;
    background-image: linear-gradient(135deg, rgba(30,59,34,0.85) 0%, rgba(30,59,34,0.4) 60%), url('{{ asset('images/greenhouse.jpg') }}');
    background-size: cover;
    background-position: center 30%;
    padding: 7rem 16px 1.5rem 16px;
    overflow: hidden;
}
.testi-banner-inner {
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 3rem;
}
.testi-banner-left {
    flex: 1;
    min-width: 250px;
}
.testi-banner-left h1 {
    color: var(--color-accent);
    font-size: 3rem;
    font-weight: bold;
    font-family: var(--font-serif);
    position: relative;
    padding-left: 1rem;
    border-left: 4px solid var(--color-accent);
}
.testi-banner-right {
    flex: 1.5;
    min-width: 300px;
}
.testi-banner-right p {
    color: white;
    font-size: 1.05rem;
    line-height: 1.8;
    background: rgba(0,0,0,0.3);
    padding: 2rem;
    border-radius: 12px;
    backdrop-filter: blur(5px);
}

/* Testimonial cards */
.testi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2.5rem;
    max-width: 1200px;
    margin: 0 auto;
}
.testi-card {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
}
.testi-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
}
.testi-card::before {
    content: '"';
    position: absolute;
    top: 15px;
    right: 25px;
    font-size: 5rem;
    color: rgba(47,88,54,0.1);
    font-family: var(--font-serif);
    line-height: 1;
}
.testi-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.testi-avatar {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--color-primary);
}
.testi-name {
    font-weight: bold;
    color: var(--color-primary-dark);
    font-size: 1.1rem;
}
.testi-role {
    font-size: 0.85rem;
    color: #999;
}
.testi-text {
    color: #555;
    line-height: 1.8;
    font-size: 0.95rem;
    font-style: italic;
}
.testi-stars {
    margin-top: 1rem;
    color: #f5a623;
    font-size: 1.1rem;
    letter-spacing: 3px;
}
@media(max-width:768px) {
    .testi-grid { grid-template-columns: 1fr; }
    .testi-banner-left h1 { font-size: 2.2rem; }
}
</style>

<div>
    <!-- Banner -->
    <div class="testi-banner animate-fade-up">
        <div class="testi-banner-inner">
            <div class="testi-banner-left">
                <h1>Testimoni</h1>
            </div>
            <div class="testi-banner-right">
                <p>{{ $settings['about_banner_text'] ?? 'Seperti sayuran hidroponik dan konvensional segar yang kami panen setiap hari, dedikasi kami sangatlah mendalam. Dan yang paling penting, kami selalu siap bekerja langsung bersama para petani mitra kami untuk memastikan setiap sayuran yang sampai ke meja makan Anda adalah yang berkualitas terbaik.' }}</p>
            </div>
        </div>
    </div>

    <!-- Testimonials Content -->
    <section style="padding: 5rem 16px; background-color: #FAF8F5;">
        <div class="animate-fade-up delay-1">
            <h2 style="text-align: center; font-size: 2.5rem; color: var(--color-primary-dark); margin-bottom: 1rem; font-weight: bold;">Apa Kata Pelanggan Kami?</h2>
            <div style="width: 60px; height: 3px; background: var(--color-accent); margin: 0 auto 3.5rem auto;"></div>

            <div class="testi-grid">
                @foreach($testimonials as $testi)
                <div class="testi-card">
                    <div class="testi-card-header">
                        @if($testi->image)
                            <img src="{{ $testi->image }}" alt="Avatar" class="testi-avatar">
                        @else
                            <div class="testi-avatar" style="background: #ccc; display: flex; align-items: center; justify-content: center;"></div>
                        @endif
                        <div>
                            <div class="testi-name">{{ $testi->name }}</div>
                            <div class="testi-role">Pelanggan ASR Farm</div>
                        </div>
                    </div>
                    <p class="testi-text">"{{ $testi->content }}"</p>
                    <div class="testi-stars">
                        @for($i=0; $i<$testi->rating; $i++)
                            ★
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
