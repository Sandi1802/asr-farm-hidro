@extends('layouts.app')

@section('content')
<style>
/* Custom overrides for Cetrofarm-like design */
.text-gold { color: var(--color-accent); }
.text-green { color: var(--color-primary); }
.btn-outline-green {
    display: inline-block;
    border: 2px solid var(--color-primary);
    color: var(--color-primary);
    padding: 10px 24px;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}
.btn-outline-green:hover {
    background-color: var(--color-primary);
    color: white;
}
.btn-solid-green {
    display: inline-block;
    background-color: var(--color-primary);
    color: white;
    padding: 12px 24px;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}
.btn-solid-green:hover {
    background-color: var(--color-primary-dark);
}
.wave-bottom {
    position: absolute;
    bottom: -2px; /* Prevent tiny gaps */
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
}
.wave-bottom svg {
    display: block;
    width: calc(100% + 1.3px);
    height: 90px;
}
.hero-text-box {
    background: rgba(255, 255, 255, 0.15);
    padding: 2.5rem;
    border-radius: 12px;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.3);
}
.collage-container {
    position: relative;
    height: 500px;
    width: 100%;
}
.collage-img {
    position: absolute;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    object-fit: cover;
}
.collage-1 { width: 65%; height: 280px; top: 0; right: 0; z-index: 1; }
.collage-2 { width: 55%; height: 280px; bottom: 20px; left: 0; z-index: 2; border: 8px solid white; }
.collage-3 { width: 50%; height: 200px; bottom: 70px; right: -10px; z-index: 3; border: 8px solid white; }

.product-circle {
    width: 220px;
    height: 220px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 1.5rem auto;
    display: block;
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}
.product-circle:hover {
    transform: translateY(-10px);
}
.feature-icon-box {
    width: 60px;
    height: 60px;
    background-color: #E6EFE6;
    color: var(--color-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}
.typing-cursor {
    animation: blink 1s step-end infinite;
    font-weight: 400;
    margin-left: 2px;
}
.hero-title { font-size: 4.5rem; }
.hero-subtitle { font-size: 1.5rem; }

@media (max-width: 768px) {
    .hero-section { padding: 6rem 16px 4rem 16px !important; min-height: auto !important; }
    .hero-container-flex { gap: 1rem !important; }
    .hero-title { font-size: 2.5rem !important; text-align: center; margin-bottom: 0 !important; }
    .hero-subtitle { font-size: 1rem !important; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hero-left-col { text-align: center; margin-bottom: 0; }
    .hero-text-box {
        padding: 1.25rem;
        margin: 0 auto;
    }
    .hero-text-box div {
        text-align: center !important;
        font-size: 1.05rem !important;
    }
    .hero-text-box a {
        margin-right: 0 !important;
        margin-bottom: 0.5rem;
        display: inline-block;
        width: calc(50% - 0.5rem);
        text-align: center;
        padding: 12px 0;
    }
}
</style>

<div>
    <!-- Hero Section -->
    <section class="hero-section" style="position: relative; padding: 7rem 16px 8rem 16px; background-image: url('{{ asset('images/bg-profile.jpg') }}'); background-size: cover; background-position: center 60%; min-height: 85vh; display: flex; align-items: center;">
        <!-- Dark overlay -->
      <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(30,59,34,0.85) 0%, rgba(30,59,34,0.3) 100%);"></div>
      
      <div class="container" style="position: relative; z-index: 10; width: 100%;">
          <div class="hero-container-flex" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 2rem;">
              <!-- Left Side -->
              <div class="hero-left-col" style="flex: 1; min-width: 300px;">
                  <h4 class="animate-load-up" style="color: var(--color-accent) !important; font-size: 1.2rem; margin-bottom: 0.5rem; font-weight: 600; letter-spacing: 2px;">SELAMAT DATANG DI</h4>
                  <h1 class="hero-title animate-load-up delay-100" style="color: #ffffff !important; margin-bottom: 0.5rem; font-family: var(--font-serif); font-weight: bold; text-shadow: 2px 2px 10px rgba(0,0,0,0.5);">{{ $settings['home_hero_title'] ?? 'ASR Farm' }}</h1>
                  <p class="hero-subtitle animate-load-up delay-300" style="color: #ffffff !important; letter-spacing: 1px; margin: 0; text-shadow: 1px 1px 5px rgba(0,0,0,0.5); min-height: 2.2rem; font-family: var(--font-sans); font-weight: 300; opacity: 0.9;">
                      <span id="hero-rotating-text">bringing nature inside</span><span class="typing-cursor">|</span>
                  </p>
              </div>
              
              <!-- Right Side -->
              <div style="flex: 1; min-width: 300px; max-width: 500px;">
                  <div class="hero-text-box animate-load-up delay-500">
                      <div style="color: white; font-size: 1.15rem; line-height: 1.7; margin-bottom: 2rem; text-align: left;">
                          {!! nl2br(e($settings['home_hero_text'] ?? "Hadirkan kesegaran langsung ke meja Anda.\n\nASR Farm berkomitmen menyediakan sayuran hidroponik dan konvensional premium. Dipanen setiap hari dengan standar kualitas yang tinggi untuk memastikan nutrisi dan rasa terbaik bagi bisnis dan keluarga Anda.")) !!}
                      </div>
                      <a href="/contact" class="btn-solid-green" style="margin-right: 1rem;">Hubungi Kami</a>
                      <a href="/products" class="btn-outline-green" style="color: white; border-color: white;">Lihat Produk</a>
                  </div>
              </div>
          </div>
      </div>

      <!-- Wave Bottom -->
      <div class="wave-bottom">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" style="display:block; width:100%; height:90px;">
              <path fill="#ffffff" fill-opacity="1" d="M0,160L80,170.7C160,181,320,203,480,197.3C640,192,800,160,960,149.3C1120,139,1280,149,1360,154.7L1440,160L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
          </svg>
      </div>
    </section>

    <!-- Tentang Kami Section -->
    <section id="about" style="padding: 7rem 16px; background-color: #ffffff;">
        <div class="container animate-fade-up">
            <div style="display: flex; flex-wrap: wrap; gap: 4rem; align-items: center;">
                <!-- Left text -->
                <div style="flex: 1; min-width: 300px;">
                    <h5 class="text-gold" style="font-weight: 600; letter-spacing: 1px; margin-bottom: 1rem; font-size: 1rem;">TENTANG KAMI</h5>
                    <h2 class="text-green" style="font-size: 2.2rem; line-height: 1.3; margin-bottom: 2rem; font-family: var(--font-serif); font-weight: bold;">
                        {!! nl2br(e($settings['about_quote'] ?? "\"Menanam sayuran itu seperti merawat cinta. Harus dilakukan dengan sepenuh hati atau tidak sama sekali.\" \n— ASR Farm")) !!}
                    </h2>
                    <div style="color: #666; line-height: 1.8; margin-bottom: 2.5rem; font-size: 1.05rem; text-align: justify;">
                        {!! nl2br(e($settings['about_story'] ?? "Ya, kami ingin membantu Anda menyediakan sayuran dan bahan alami yang segar untuk keluarga tercinta. Menikmati hidangan sehari-hari akan menjadi pengalaman yang sungguh menyenangkan manakala didukung oleh hasil panen yang berkualitas dan menyehatkan. Anda setuju?\n\nASR Farm adalah gagasan tentang membangun ekosistem perkebunan yang lebih baik bagi Anda, lingkungan, dan para petani lokal yang merawat sayuran-sayuran ini dengan sepenuh hati.\n\nMari mulai gaya hidup sehat dengan sayuran organik dan hidroponik terbaik untuk kebaikan keluarga kita saat ini dan masa depan.")) !!}
                    </div>
                    <a href="/about" class="btn-outline-green">Selanjutnya ➔</a>
                </div>
                
                <!-- Right images collage -->
                <div style="flex: 1; min-width: 300px;">
                    <div class="collage-container">
                        <img src="{{ asset('images/greenhouse.jpg') }}" class="collage-img collage-1" alt="Greenhouse">
                        <img src="{{ asset('images/kebun-wide.png') }}" class="collage-img collage-2" alt="Kebun">
                        <img src="{{ asset('images/bg-profile.jpg') }}" class="collage-img collage-3" alt="Gate">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Produk Kami Section -->
    <section style="padding: 7rem 16px; background-color: #FAF8F5; text-align: center;">
        <div class="container animate-fade-up delay-1">
            <h2 class="text-gold" style="font-size: 2.8rem; margin-bottom: 1.5rem; font-weight: bold;">Sayuran & Layanan Kami</h2>
            <div style="width: 50px; height: 3px; background-color: var(--color-accent); margin: 0 auto 2rem auto;"></div>
            
            <p style="max-width: 850px; margin: 0 auto 3rem auto; color: #666; line-height: 1.8; font-size: 1.1rem; text-align: justify;">
                Seperti sayuran hidroponik dan konvensional segar yang kami panen setiap hari, dedikasi kami sangatlah mendalam. Dan yang paling penting, kami selalu siap bekerja langsung bersama para petani mitra kami untuk memastikan setiap sayuran yang sampai ke meja makan Anda adalah yang berkualitas terbaik.
            </p>
            <a href="/products" class="btn-outline-green" style="margin-bottom: 4rem;">Pelajari Lebih Lanjut ➔</a>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; margin-top: 2rem;">
                <div>
                    <img src="{{ asset('images/konvensional.jpg') }}" class="product-circle" alt="Sayuran Hidroponik">
                    <h3 style="color: var(--color-primary-dark); font-size: 1.4rem; font-weight: bold;">Hidroponik</h3>
                </div>
                <div>
                    <img src="{{ asset('images/konvensional-field.jpg') }}" class="product-circle" alt="Sayuran Konvensional">
                    <h3 style="color: var(--color-primary-dark); font-size: 1.4rem; font-weight: bold;">Konvensional</h3>
                </div>
                <div>
                    <img src="{{ asset('images/distribusi.jpg') }}" class="product-circle" alt="Distribusi & Jastip">
                    <h3 style="color: var(--color-primary-dark); font-size: 1.4rem; font-weight: bold;">Distribusi (Jastip Sayur)</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimoni placeholder -->
    <section style="padding: 3.5rem 16px; background-color: #FAF8F5;">
        <div class="container animate-fade-up" style="text-align: center;">
            <h5 class="text-gold" style="font-weight: 600; letter-spacing: 1px; margin-bottom: 0.5rem; font-size: 1rem;">TESTIMONI</h5>
            <h2 class="text-green" style="font-size: 2.2rem; margin-bottom: 1.5rem; font-family: var(--font-serif); font-weight: bold;">Bagaimana Tanggapan Pelanggan Kami</h2>
            <a href="/testimonials" class="btn-outline-green">Lihat Semua ➔</a>
        </div>
    </section>

    <!-- Artikel Terbaru Section -->
    <section style="padding: 7rem 16px; background-color: #ffffff;">
        <div class="container animate-fade-up">
            <h2 class="text-gold" style="font-size: 2.8rem; text-align: center; margin-bottom: 3.5rem; font-weight: bold;">Artikel Terbaru</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                @foreach($posts as $post)
                <div style="border-radius: 12px; overflow: hidden; position: relative; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
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
                        <p style="color: #eee; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem; text-align: justify;">{{ \Illuminate\Support\Str::limit($post->content, 120) }}</p>
                        <a href="/blog/{{ $post->id }}" style="color: white; font-weight: 600; text-decoration: none; font-size: 0.9rem;">Read More »</a>
                    </div>
                </div>
                @endforeach
            </div>
            <div style="text-align: right; margin-top: 3rem;">
                <a href="/blog" class="btn-outline-green">Lihat Semua ➔</a>
            </div>
        </div>
    </section>

    <!-- Features Section (Moved Before Footer) -->
    <section style="padding: 4rem 16px; background-color: #ffffff; border-top: 1px solid #f0f0f0;">
        <div class="container animate-fade-up">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 3rem;">
                <div style="display: flex; gap: 1.2rem; align-items: center;">
                    <div style="width: 55px; height: 55px; border-radius: 50%; background-color: #E8F0E8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2F5836" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h4 style="color: var(--color-primary-dark); margin-bottom: 4px; font-size: 1.05rem; font-weight: 700; font-family: var(--font-sans);">Bebas Bahan Kimia</h4>
                        <p style="color: #888; font-size: 0.9rem; line-height: 1.5; margin: 0; font-family: var(--font-sans);">Tidak membawa bahan berbahaya/beracun</p>
                    </div>
                </div>
                <div style="display: flex; gap: 1.2rem; align-items: center;">
                    <div style="width: 55px; height: 55px; border-radius: 50%; background-color: #E8F0E8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2F5836" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8h1a4 4 0 010 8h-1"/>
                            <path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/>
                            <line x1="6" y1="1" x2="6" y2="4"/>
                            <line x1="10" y1="1" x2="10" y2="4"/>
                            <line x1="14" y1="1" x2="14" y2="4"/>
                        </svg>
                    </div>
                    <div>
                        <h4 style="color: var(--color-primary-dark); margin-bottom: 4px; font-size: 1.05rem; font-weight: 700; font-family: var(--font-sans);">Segar & Sehat</h4>
                        <p style="color: #888; font-size: 0.9rem; line-height: 1.5; margin: 0; font-family: var(--font-sans);">Nyaman dan aman untuk dikonsumsi sesuai selera anda</p>
                    </div>
                </div>
                <div style="display: flex; gap: 1.2rem; align-items: center;">
                    <div style="width: 55px; height: 55px; border-radius: 50%; background-color: #E8F0E8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2F5836" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 20l4-16m2 16l4-16M3 8h18M3 16h18"/>
                            <circle cx="12" cy="12" r="3" fill="#2F5836" stroke="none"/>
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83"/>
                        </svg>
                    </div>
                    <div>
                        <h4 style="color: var(--color-primary-dark); margin-bottom: 4px; font-size: 1.05rem; font-weight: 700; font-family: var(--font-sans);">100% Organik</h4>
                        <p style="color: #888; font-size: 0.9rem; line-height: 1.5; margin: 0; font-family: var(--font-sans);">Dikelola secara hati-hati & sudah bersertifikat organik</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const texts = [
            "Bringing Nature Inside",
            "Sayuran Segar Berkualitas",
            "Dari Kebun ke Meja Anda",
            "Pilihan Sehat Keluarga"
        ];
        
        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        const rotatingText = document.getElementById("hero-rotating-text");
        
        function type() {
            if (!rotatingText) return;
            
            const currentText = texts[textIndex];
            
            if (isDeleting) {
                rotatingText.textContent = currentText.substring(0, charIndex - 1);
                charIndex--;
            } else {
                rotatingText.textContent = currentText.substring(0, charIndex + 1);
                charIndex++;
            }
            
            let typeSpeed = isDeleting ? 30 : 60;
            
            if (!isDeleting && charIndex === currentText.length) {
                typeSpeed = 2000; // Pause at end of sentence
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                textIndex = (textIndex + 1) % texts.length;
                typeSpeed = 500; // Pause before typing new sentence
            }
            
            setTimeout(type, typeSpeed);
        }
        
        // Start typing effect slightly after load
        setTimeout(type, 1000);
    });
    </script>
</div>
@endsection
