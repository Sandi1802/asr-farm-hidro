@extends('layouts.app')

@section('content')
<style>
/* ================================================
   ABOUT PAGE — Premium Redesign
   ================================================ */

/* Banner */
.about-banner {
    position: relative;
    min-height: 28vh;
    display: flex;
    align-items: flex-end;
    background-image: linear-gradient(135deg, rgba(20,50,25,0.92) 0%, rgba(20,50,25,0.45) 70%), url('{{ asset('images/kebun-wide.png') }}');
    background-size: cover;
    background-position: center 30%;
    padding: 7rem 16px 2rem 16px;
    color: white;
    overflow: hidden;
}
.about-banner::after {
    content:'';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 60px;
    background: linear-gradient(to bottom, transparent, #ffffff);
}
.about-banner-container { max-width: 1200px; margin: 0 auto; width: 100%; }
.about-banner-title {
    color: var(--color-accent);
    font-size: 3.2rem;
    font-weight: bold;
    font-family: var(--font-serif);
    border-left: 5px solid var(--color-accent);
    padding-left: 1.2rem;
    line-height: 1.2;
}
.about-banner-sub {
    color: rgba(255,255,255,0.8);
    font-size: 1.05rem;
    margin-top: 0.6rem;
    padding-left: 1.4rem;
}

/* Section base */
.asr-section { padding: 4rem 16px; }
.asr-container { max-width: 1200px; margin: 0 auto; }
.section-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, var(--color-primary), #3d7048);
    color: white;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    padding: 6px 16px;
    border-radius: 50px;
    margin-bottom: 1.2rem;
}
.section-title {
    font-size: 2.3rem;
    font-weight: bold;
    color: var(--color-primary-dark);
    font-family: var(--font-serif);
    margin-bottom: 1.5rem;
    line-height: 1.25;
}
.section-text { color: #5a6672; line-height: 1.95; font-size: 1rem; text-align: justify; }

/* Scroll reveal */
.reveal { opacity: 0; transform: translateY(40px); transition: opacity 0.7s ease, transform 0.7s ease; }
.reveal.visible { opacity: 1; transform: translateY(0); }
.reveal-left { opacity: 0; transform: translateX(-40px); transition: opacity 0.7s ease, transform 0.7s ease; }
.reveal-left.visible { opacity: 1; transform: translateX(0); }
.reveal-right { opacity: 0; transform: translateX(40px); transition: opacity 0.7s ease, transform 0.7s ease; }
.reveal-right.visible { opacity: 1; transform: translateX(0); }
.delay-1 { transition-delay: 0.15s !important; }
.delay-2 { transition-delay: 0.3s !important; }
.delay-3 { transition-delay: 0.45s !important; }
.delay-4 { transition-delay: 0.6s !important; }

/* Two-col layout */
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }
.two-col.reverse { }

/* Image collage */
.img-collage { position: relative; height: 420px; }
.img-collage img {
    position: absolute;
    border-radius: 16px;
    object-fit: cover;
    box-shadow: 0 20px 50px rgba(0,0,0,0.18);
    transition: transform 0.4s ease;
}
.img-collage img:hover { transform: scale(1.02); }

/* =====================
   OUR FARM CARDS (SVG icons)
   ===================== */
.farm-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 1.5rem; }
.farm-card {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 16px;
    padding: 2rem 1.5rem;
    text-align: center;
    transition: background 0.3s, transform 0.3s;
}
.farm-card:hover { background: rgba(255,255,255,0.13); transform: translateY(-5px); }
.farm-card-icon {
    width: 56px; height: 56px;
    background: rgba(242,166,64,0.18);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.2rem auto;
}
.farm-card h4 { color: var(--color-accent); font-weight: 700; font-size: 0.98rem; margin-bottom: 0.6rem; }
.farm-card p { color: rgba(255,255,255,0.68); font-size: 0.88rem; line-height: 1.65; text-align: justify; }

/* =====================
   PRODUCT TAGS
   ===================== */
.product-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 0.8rem; margin-top: 2rem; }
.product-tag {
    display: flex; align-items: center; gap: 10px;
    background: #f0f7f1;
    border: 1px solid #c8e6ca;
    border-radius: 10px;
    padding: 10px 14px;
    font-weight: 600;
    color: var(--color-primary-dark);
    font-size: 0.9rem;
    transition: background 0.25s, transform 0.25s;
}
.product-tag:hover { background: #dff0e1; transform: translateY(-2px); }
.product-tag svg { flex-shrink: 0; }

/* =====================
   WHY CHOOSE US
   ===================== */
.why-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
.why-card {
    background: white;
    border-radius: 16px;
    padding: 1.8rem;
    box-shadow: 0 4px 24px rgba(47,88,54,0.08);
    border-left: 4px solid var(--color-primary);
    display: flex; gap: 1rem; align-items: flex-start;
    transition: box-shadow 0.3s, transform 0.3s;
}
.why-card:hover { box-shadow: 0 8px 32px rgba(47,88,54,0.14); transform: translateY(-3px); }
.why-card-icon {
    width: 46px; height: 46px; flex-shrink: 0;
    background: #eef7ef;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
}
.why-card-body h4 { font-size: 0.98rem; font-weight: 700; color: var(--color-primary-dark); margin-bottom: 0.3rem; }
.why-card-body p { font-size: 0.87rem; color: #6b7a80; margin: 0; line-height: 1.65; }

/* =====================
   HOW WE WORK
   ===================== */
.how-steps { display: grid; grid-template-columns: repeat(4,1fr); gap: 0; position: relative; margin-top: 3rem; }
.how-steps::before {
    content: '';
    position: absolute;
    top: 28px; left: 12%; right: 12%;
    height: 2px;
    background: linear-gradient(to right, var(--color-primary), var(--color-accent));
    z-index: 0;
}
.how-step { text-align: center; padding: 0 1rem 2rem; position: relative; z-index: 1; }
.how-step-num {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--color-primary), #3d7048);
    color: white;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    font-weight: bold;
    margin: 0 auto 1.5rem auto;
    box-shadow: 0 6px 20px rgba(47,88,54,0.3);
    border: 4px solid white;
}
.how-step h4 { font-weight: 700; color: var(--color-primary-dark); font-size: 1rem; margin-bottom: 0.5rem; }
.how-step p { font-size: 0.85rem; color: #777; line-height: 1.65; }
.how-step-icon {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--color-primary), #3d7048);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.5rem auto;
    box-shadow: 0 6px 20px rgba(47,88,54,0.3);
    border: 4px solid white;
}

/* =====================
   CLIENT MARQUEE TICKER
   ===================== */
.marquee-box {
    background: transparent;
    padding: 1rem 0 3.5rem 0;
    overflow: hidden;
}
.marquee-label {
    text-align: center;
    margin-bottom: 2.5rem;
    color: var(--color-primary);
    font-size: 0.95rem;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-weight: 800;
}
.marquee-track-wrap {
    overflow: hidden;
    position: relative;
    mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
    -webkit-mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
}
.marquee-track {
    display: flex;
    gap: 0;
    animation: marquee-scroll 70s linear infinite;
    width: max-content;
    will-change: transform;
}
.marquee-track:hover { animation-play-state: paused; }
@keyframes marquee-scroll {
    0%   { transform: translate3d(0, 0, 0); }
    100% { transform: translate3d(-50%, 0, 0); }
}
.marquee-item {
    display: flex;
    align-items: center;
    gap: 16px;
    background: white;
    border: 1px solid rgba(0,0,0,0.03);
    border-radius: 16px;
    padding: 16px 28px;
    margin: 0 16px;
    white-space: nowrap;
    transition: transform 0.3s, box-shadow 0.3s;
    box-shadow: 0 10px 25px rgba(0,0,0,0.04);
    cursor: default;
}
.marquee-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
}
.marquee-item-icon {
    width: 48px; height: 48px;
    background: rgba(39, 174, 96, 0.08);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.marquee-item-text strong {
    display: block;
    color: var(--color-primary-dark);
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 2px;
}
.marquee-item-text span {
    color: #666;
    font-size: 0.85rem;
}

/* =====================
   TARGET MARKET CARDS
   ===================== */
.market-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem; }
.market-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 8px 36px rgba(47,88,54,0.09);
    transition: transform 0.3s, box-shadow 0.3s;
}
.market-card:hover { transform: translateY(-5px); box-shadow: 0 16px 48px rgba(47,88,54,0.14); }
.market-card-header {
    display: flex; align-items: center; gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.2rem;
    border-bottom: 2px solid #e8f5e9;
}
.market-card-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, var(--color-primary), #3d7048);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.market-card-header h3 { font-size: 1.3rem; font-weight: 700; color: var(--color-primary-dark); margin: 0; }
.market-card-header p { font-size: 0.82rem; color: #8a9ba8; margin: 0; }
.market-list { list-style: none; padding: 0; margin: 0; }
.market-list li {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f4f7f4;
    color: #4a5568;
    font-size: 0.93rem;
    font-weight: 500;
    transition: color 0.2s;
}
.market-list li:last-child { border: none; }
.market-list li:hover { color: var(--color-primary); }
.market-list-icon {
    width: 32px; height: 32px;
    background: #eef7ef;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

@media(max-width: 768px) {
    .asr-section { padding: 2.5rem 16px !important; }
    .pt-mobile-0 { padding-top: 0 !important; }
    .about-banner { padding: 6rem 16px 2rem 16px !important; }
    .two-col { grid-template-columns: 1fr; gap: 2rem; }
    .farm-cards { grid-template-columns: repeat(2,1fr); }
    .why-grid, .market-grid { grid-template-columns: 1fr; }
    .how-steps { grid-template-columns: repeat(2,1fr); }
    .how-steps::before { display: none; }
    .product-grid { grid-template-columns: repeat(2,1fr); }
    .about-banner-title { font-size: 2.2rem; }
    .img-collage { height: 300px; }
    .section-title { font-size: 1.85rem; }
}
</style>

<div>
    {{-- ===== BANNER ===== --}}
    <div class="about-banner">
        <div class="about-banner-container">
            <h1 class="about-banner-title">Tentang ASR Farm</h1>
            <p class="about-banner-sub">Pertanian modern, sayuran segar, untuk hidup yang lebih sehat.</p>
        </div>
    </div>

    {{-- ===== 1. ABOUT OUR LOCATION ===== --}}
    <section class="asr-section pt-mobile-0" style="background:#fff;">
        <div class="asr-container">
            <div class="reveal-left" style="max-width: 800px; margin: 0 auto; text-align: center;">
                <span class="section-label">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                    About Our
                </span>
                <h2 class="section-title">Lokasi Strategis di Kawasan Lembang, Bandung Barat</h2>
                <p class="section-text" style="text-align: justify;">Lahan pertanian ASR Farm berlokasi di kawasan Lembang, Bandung, Jawa Barat — wilayah yang dikenal memiliki kondisi geografis dan iklim yang sangat mendukung untuk kegiatan budidaya hortikultura, khususnya sayur-sayuran.</p>
                <p class="section-text" style="margin-top:1rem; text-align: justify;">Dengan suhu udara yang relatif sejuk, kualitas tanah yang subur, serta ketersediaan air yang baik, Lembang menjadi lokasi strategis untuk menghasilkan produk pertanian berkualitas tinggi yang segar dan terjaga mutunya.</p>
                <div style="display:flex;align-items:center;justify-content:center;text-align:left;gap:10px;margin-top:1.5rem;padding:14px 18px;background:#f0f7f1;border-radius:12px;border-left:4px solid var(--color-primary);">
                    <svg width="20" height="20" fill="var(--color-primary)" viewBox="0 0 24 24" style="flex-shrink:0;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    <span style="color:var(--color-primary-dark);font-size:0.9rem;font-weight:600;">Jl. Cimerta Tengah, Tugumukti, Kec. Cisarua, Kabupaten Bandung Barat, Jawa Barat 40551</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== 2. PRODUCTION METHOD ===== --}}
    <section class="asr-section" style="background:#FAF8F5;">
        <div class="asr-container">
            <div class="two-col">
                <div class="reveal-left">
                    <div class="img-collage">
                        <img src="{{ asset('images/greenhouse.jpg') }}" style="width:62%;height:270px;top:0;left:0;z-index:1;" alt="Metode Hidroponik">
                        <img src="{{ asset('images/kebun-wide.png') }}" style="width:62%;height:240px;bottom:0;right:0;z-index:2;border:7px solid white;" alt="Kebun Konvensional">
                    </div>
                </div>
                <div class="reveal-right">
                    <span class="section-label">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 008 20c9 0 10-16 10-16C17.56 5.45 14 7 12 7c2-4 5-4 5-4z"/></svg>
                        Metode Produksi
                    </span>
                    <h2 class="section-title">Kombinasi Hidroponik & Pertanian Konvensional</h2>
                    <p class="section-text">ASR Farm mengombinasikan dua metode pertanian — hidroponik dan konvensional. Sistem hidroponik memungkinkan budidaya tanpa tanah menggunakan larutan nutrisi terkontrol, menghasilkan sayuran yang lebih higienis dan efisien dalam penggunaan air.</p>
                    <p class="section-text" style="margin-top:1rem;">Sementara itu, metode konvensional tetap diterapkan untuk jenis tanaman yang lebih optimal di media tanah. Kombinasi keduanya memastikan keberagaman produk dengan kualitas konsisten sepanjang tahun.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== 3. OUR FOCUS ===== --}}
    <section class="asr-section" style="background:#fff;">
        <div class="asr-container">
            <div class="two-col">
                <div class="reveal-left">
                    <span class="section-label">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        Our Focus
                    </span>
                    <h2 class="section-title">Sayuran Segar, Sehat & Berkualitas Tinggi</h2>
                    <p class="section-text">Fokus utama ASR Farm adalah menghadirkan hasil pertanian yang fresh, sehat, dan berkualitas tinggi bagi konsumen. Kualitas sayuran tidak hanya ditentukan oleh proses budidaya, tetapi juga oleh sistem pengelolaan pascapanen yang baik.</p>
                    <p class="section-text" style="margin-top:1rem;">Setiap tahapan — dari penanaman, perawatan, hingga panen — dilakukan dengan standar operasional ketat untuk memastikan produk tetap segar, bersih, dan aman dikonsumsi. Komitmen ini mendukung gaya hidup sehat masyarakat secara nyata.</p>
                </div>
                <div class="reveal-right">
                    <div class="img-collage">
                        <img src="{{ asset('images/greenhouse.jpg') }}" style="width:100%;height:400px;top:0;left:0;border-radius:20px;" alt="Fokus ASR Farm">
                    </div>
                </div>
            </div>
        </div>
    </section>



    {{-- ===== 6. WHY CHOOSE US ===== --}}
    <section class="asr-section" style="background:#FAF8F5;">
        <div class="asr-container">
            <div class="reveal" style="text-align:center;margin-bottom:3rem;">
                <span class="section-label">Why Choose Us</span>
                <h2 class="section-title" style="text-align:center;">Mengapa Memilih ASR Farm?</h2>
            </div>
            <div class="why-grid">
                <div class="why-card reveal delay-1">
                    <div class="why-card-icon">
                        <svg width="22" height="22" fill="var(--color-primary)" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    </div>
                    <div class="why-card-body">
                        <h4>Consistent Quality</h4>
                        <p>Ukuran & kesegaran terjaga setiap kali panen — produk selalu memenuhi standar kualitas yang ketat.</p>
                    </div>
                </div>
                <div class="why-card reveal delay-2">
                    <div class="why-card-icon">
                        <svg width="22" height="22" fill="var(--color-primary)" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                    </div>
                    <div class="why-card-body">
                        <h4>Hygienic Process</h4>
                        <p>Minim kontaminasi — dari penanaman hingga pengemasan dilakukan dengan standar kebersihan tinggi.</p>
                    </div>
                </div>
                <div class="why-card reveal delay-3">
                    <div class="why-card-icon">
                        <svg width="22" height="22" fill="var(--color-primary)" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 008 20c9 0 10-16 10-16C17.56 5.45 14 7 12 7c2-4 5-4 5-4z"/></svg>
                    </div>
                    <div class="why-card-body">
                        <h4>Controlled Farming</h4>
                        <p>Nutrisi & lingkungan terukur — teknologi modern memastikan pertumbuhan tanaman yang optimal.</p>
                    </div>
                </div>
                <div class="why-card reveal delay-4">
                    <div class="why-card-icon">
                        <svg width="22" height="22" fill="var(--color-primary)" viewBox="0 0 24 24"><path d="M20 6h-2.18c.07-.44.18-.88.18-1.35C18 2.53 15.7 1 13.5 1c-1.32 0-2.46.52-3.28 1.38L9 3.56 7.78 2.38C6.96 1.52 5.82 1 4.5 1 2.3 1 0 2.53 0 4.65c0 .47.11.91.18 1.35H0v14h20V6zm-8.5-3c.83 0 1.5.67 1.5 1.5S12.33 6 11.5 6s-1.5-.67-1.5-1.5S10.67 3 11.5 3zM4.5 3C5.33 3 6 3.67 6 4.5S5.33 6 4.5 6 3 5.33 3 4.5 3.67 3 4.5 3zM18 18H2V8h7v2H7v2h2v-2h2v2h2v-2h2v2h2v-2h1V8h1v10z"/></svg>
                    </div>
                    <div class="why-card-body">
                        <h4>Scalable Supply</h4>
                        <p>Siap untuk kebutuhan bisnis — kapasitas produksi dapat disesuaikan dengan kebutuhan mitra.</p>
                    </div>
                </div>
                <div class="why-card reveal" style="grid-column:1/-1;">
                    <div class="why-card-icon">
                        <svg width="22" height="22" fill="var(--color-primary)" viewBox="0 0 24 24"><path d="M20 8H4V6h16v2zm-2-6H6v2h12V2zm4 10v8l-6 2-4-2-4 2-6-2v-8l6 2 4-2 4 2 6-2zm-2 1.92L18 13v6.08l2 .67V11.92zM12 11l-4 2v6.08l4-1.75 4 1.75V13l-4-2zM2 11.92v5.83l2-.67V11l-2 .92z"/></svg>
                    </div>
                    <div class="why-card-body">
                        <h4>Fresh Harvest — Distribusi Langsung Farm ke Market</h4>
                        <p>Sayuran sampai ke tangan konsumen dalam kondisi paling segar. Rantai distribusi singkat memastikan kualitas terjaga dari kebun ke meja makan Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== 7. HOW WE WORK ===== --}}
    <section class="asr-section" style="background:#fff;">
        <div class="asr-container">
            <div class="reveal" style="text-align:center;margin-bottom:1rem;">
                <span class="section-label">How We Work</span>
                <h2 class="section-title" style="text-align:center;">Proses Kerja Kami</h2>
                <p style="color:#777;max-width:520px;margin:0 auto;font-size:0.95rem;line-height:1.8;">Dari penanaman hingga distribusi, setiap langkah dirancang untuk menjaga kualitas dan kesegaran produk.</p>
            </div>
            <div class="how-steps">
                <div class="how-step reveal delay-1">
                    <div class="how-step-icon">
                        <svg width="26" height="26" fill="white" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                    </div>
                    <h4>Scheduled Planting</h4>
                    <p>Penanaman terjadwal untuk memastikan ketersediaan produk yang konsisten sepanjang waktu.</p>
                </div>
                <div class="how-step reveal delay-2">
                    <div class="how-step-icon">
                        <svg width="26" height="26" fill="white" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 008 20c9 0 10-16 10-16C17.56 5.45 14 7 12 7c2-4 5-4 5-4z"/></svg>
                    </div>
                    <h4>Controlled Growing</h4>
                    <p>Pertumbuhan dipantau ketat dengan kontrol nutrisi, suhu, dan kelembapan yang terukur.</p>
                </div>
                <div class="how-step reveal delay-3">
                    <div class="how-step-icon">
                        <svg width="26" height="26" fill="white" viewBox="0 0 24 24"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
                    </div>
                    <h4>Harvest Planning</h4>
                    <p>Panen direncanakan sesuai permintaan pasar untuk menjaga kesegaran dan efisiensi produk.</p>
                </div>
                <div class="how-step reveal delay-4">
                    <div class="how-step-icon">
                        <svg width="26" height="26" fill="white" viewBox="0 0 24 24"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                    </div>
                    <h4>Fast Distribution</h4>
                    <p>Distribusi cepat langsung dari farm agar produk tiba ke konsumen dalam kondisi paling segar.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== 8. CLIENT MARQUEE (Target Market) ===== --}}
    <section class="asr-section" style="background:#FAF8F5; padding-bottom: 0;">
        <div class="asr-container">
            <div class="marquee-box reveal delay-1">
                <p class="marquee-label">Melayani Berbagai Segmen Pelanggan</p>
                <div class="marquee-track-wrap">
                    <div class="marquee-track">
                        @php
                        $dbClients = \App\Models\Client::orderBy('order')->get();
                        // For a smooth infinite marquee, duplicate the items if there are too few
                        $clientsToRender = $dbClients->concat($dbClients)->concat($dbClients);
                        @endphp
                        @foreach($clientsToRender as $client)
                        <div class="marquee-item">
                            <div class="marquee-item-icon" style="padding: 4px;">
                                @if($client->image)
                                    <img src="{{ $client->image }}" alt="{{ $client->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                @else
                                    <svg width="20" height="20" fill="var(--color-accent)" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
                                @endif
                            </div>
                            <div class="marquee-item-text">
                                <strong>{{ $client->name }}</strong>
                                <span>{{ $client->description }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>


</div>

<script>
// Scroll reveal observer
const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
        }
    });
}, { threshold: 0.12 });
revealEls.forEach(el => obs.observe(el));
</script>
@endsection
