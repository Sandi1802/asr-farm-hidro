@extends('layouts.app')

@section('content')
<style>
/* Contact Banner */
.contact-banner {
    position: relative;
    min-height: 25vh;
    display: flex;
    align-items: flex-end;
    background-image: linear-gradient(135deg, rgba(30,59,34,0.85) 0%, rgba(30,59,34,0.4) 60%), url('{{ asset('images/kebun-wide.png') }}');
    background-size: cover;
    background-position: center 30%;
    padding: 7rem 16px 1.5rem 16px;
    overflow: hidden;
}
.contact-banner-inner {
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 3rem;
}
.contact-banner-left {
    flex: 1;
    min-width: 250px;
}
.contact-banner-left h1 {
    color: var(--color-accent);
    font-size: 3rem;
    font-weight: bold;
    font-family: var(--font-serif);
    position: relative;
    padding-left: 1rem;
    border-left: 4px solid var(--color-accent);
}
.contact-banner-right {
    flex: 1.5;
    min-width: 300px;
}
.contact-banner-right p {
    color: white;
    font-size: 1.05rem;
    line-height: 1.8;
    background: rgba(0,0,0,0.3);
    padding: 2rem;
    border-radius: 12px;
    backdrop-filter: blur(5px);
}

/* Social & Contact Info Section */
.contact-info-section {
    padding: 5rem 16px;
    background: #ffffff;
}
.contact-info-grid {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
}
.social-block h2,
.kontak-block h2 {
    font-size: 2rem;
    color: var(--color-primary-dark);
    font-weight: bold;
    margin-bottom: 1rem;
}
.social-block p {
    color: #666;
    line-height: 1.8;
    margin-bottom: 1.5rem;
}
.social-icons {
    display: flex;
    gap: 1rem;
}
.social-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--color-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    text-decoration: none;
    transition: background 0.3s, transform 0.3s;
}
.social-icon:hover {
    background: var(--color-accent);
    transform: translateY(-3px);
}

.kontak-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}
.kontak-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--color-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.kontak-label {
    font-weight: bold;
    color: var(--color-primary-dark);
    font-size: 1rem;
    margin-bottom: 2px;
}
.kontak-value {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Form + Map Section */
.form-map-section {
    padding: 5rem 16px;
    background: #FAF8F5;
}
.form-map-grid {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: start;
}
.contact-form {
    background: white;
    padding: 3rem;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}
.contact-form h2 {
    font-size: 2rem;
    color: var(--color-primary-dark);
    font-weight: bold;
    margin-bottom: 0.5rem;
}
.contact-form .subtitle {
    color: #888;
    font-size: 0.95rem;
    margin-bottom: 2rem;
    line-height: 1.6;
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-group label {
    display: block;
    font-weight: 600;
    color: var(--color-primary-dark);
    margin-bottom: 6px;
    font-size: 0.95rem;
}
.form-group label span {
    color: red;
}
.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s, box-shadow 0.3s;
    background: #fafafa;
    font-family: inherit;
}
.form-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(47,88,54,0.1);
    background: white;
}
textarea.form-input {
    resize: vertical;
    min-height: 120px;
}
.btn-submit {
    background: var(--color-primary);
    color: white;
    border: 2px solid var(--color-primary);
    padding: 12px 32px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    font-family: inherit;
}
.btn-submit:hover {
    background: transparent;
    color: var(--color-primary);
}

.map-container {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    height: 100%;
    min-height: 500px;
}
.map-container iframe {
    width: 100%;
    height: 100%;
    min-height: 500px;
    border: none;
}

@media(max-width:768px) {
    .contact-info-grid,
    .form-map-grid {
        grid-template-columns: 1fr;
    }
    .contact-banner-left h1 { font-size: 2.2rem; }
    .contact-form { padding: 2rem; }
    .map-container { min-height: 350px; }
    .map-container iframe { min-height: 350px; }
}
</style>

<div>
    <!-- Banner -->
    <div class="contact-banner animate-fade-up">
        <div class="contact-banner-inner">
            <div class="contact-banner-left">
                <h1>Hubungi Kami</h1>
            </div>
            <div class="contact-banner-right">
                <p>Seperti sayuran yang kita produksi, domba yang kami pelihara, sapi yang menghasilkan susu dan telor dari ayam; kami sangatlah beragam. Dan yang paling penting kami pribadi yang siap berkotor-kotor bersama petani dampingan kami untuk menyiapkan bahan makanan untuk anda.</p>
            </div>
        </div>
    </div>

    <!-- Social Media & Kontak Section -->
    <section class="contact-info-section">
        <div class="contact-info-grid animate-fade-up delay-1">
            <!-- Social Media -->
            <div class="social-block">
                <h2>Sosial Media</h2>
                <p>Ayo ikuti kami kemudian dapatkan informasi menarik dan terbaru lainnya melalui sosial media yang kami punya di sini!</p>
                <div class="social-icons">
                    <a href="{{ $settings['social_ig'] ?? '#' }}" class="social-icon" title="Instagram">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="{{ $settings['social_tiktok'] ?? '#' }}" class="social-icon" title="TikTok">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 2.26-1.05 4.41-2.74 5.92-1.67 1.49-3.9 2.26-6.16 2.05-2.25-.19-4.36-1.25-5.83-2.93-1.47-1.68-2.24-3.9-2.09-6.15.15-2.25 1.25-4.36 2.93-5.83 1.68-1.47 3.9-2.24 6.15-2.09.43.03.86.1 1.28.2v4.06c-.45-.09-.92-.12-1.39-.08-1.15.06-2.22.61-2.95 1.51-.73.9-1.03 2.09-.84 3.23.19 1.14.88 2.14 1.87 2.7.99.56 2.21.65 3.28.25 1.07-.4 1.9-1.32 2.22-2.46.2-.73.23-1.5.07-2.24V.02z"/></svg>
                    </a>
                    <a href="{{ $settings['social_fb'] ?? '#' }}" class="social-icon" title="Facebook">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                    </a>
                    <a href="{{ $settings['social_linkedin'] ?? '#' }}" class="social-icon" title="LinkedIn">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                    </a>
                </div>
            </div>
            
            <!-- Kontak Info -->
            <div class="kontak-block">
                <h2>Kontak</h2>
                
                <div class="kontak-item">
                    <div class="kontak-icon">📍</div>
                    <div>
                        <div class="kontak-label">Alamat</div>
                        <div class="kontak-value">{{ $settings['contact_address'] ?? 'Jl. Setro Raya, Desa Gondoriyo, Kecamatan Bergas, Kabupaten Semarang' }}</div>
                    </div>
                </div>
                
                <div class="kontak-item">
                    <div class="kontak-icon">📞</div>
                    <div>
                        <div class="kontak-label">Telepon</div>
                        <div class="kontak-value">{{ $settings['contact_phone'] ?? '+62 812 3456 7890' }}</div>
                    </div>
                </div>
                
                <div class="kontak-item">
                    <div class="kontak-icon">✉️</div>
                    <div>
                        <div class="kontak-label">Email</div>
                        <div class="kontak-value">customerrelation@asrfarm.com</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Form & Map Section -->
    <section class="form-map-section" id="contact">
        <div class="form-map-grid animate-fade-up delay-2">
            <!-- Contact Form -->
            <div class="contact-form">
                <h2>Kirim Kami Pesan</h2>
                <p class="subtitle">Hubungi kami, lebih lanjut untuk mengetahui seputar produk dan kerjasama.</p>
                
                <form action="#" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Name <span>*</span></label>
                        <input type="text" class="form-input" placeholder="Masukkan nama Anda" required>
                    </div>
                    <div class="form-group">
                        <label>Email <span>*</span></label>
                        <input type="email" class="form-input" placeholder="Masukkan email Anda" required>
                    </div>
                    <div class="form-group">
                        <label>Subject <span>*</span></label>
                        <input type="text" class="form-input" placeholder="Subjek pesan" required>
                    </div>
                    <div class="form-group">
                        <label>Message <span>*</span></label>
                        <textarea class="form-input" placeholder="Tulis pesan Anda di sini..." required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Submit</button>
                </form>
            </div>
            
            <!-- Google Map -->
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.5!2d110.4!3d-7.2!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMTInMDAuMCJTIDExMMKwMjQnMDAuMCJF!5e0!3m2!1sen!2sid!4v1"
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>
</div>
@endsection
