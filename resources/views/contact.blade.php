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
                <p>Seperti sayuran hidroponik dan konvensional segar yang kami panen setiap hari, dedikasi kami sangatlah mendalam. Dan yang paling penting, kami selalu siap bekerja langsung bersama para petani mitra kami untuk memastikan setiap sayuran yang sampai ke meja makan Anda adalah yang berkualitas terbaik.</p>
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
                    <a href="{{ $settings['social_youtube'] ?? 'https://youtube.com/@asrfarm' }}" class="social-icon" title="YouTube" target="_blank" rel="noopener noreferrer">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.377.55a3.016 3.016 0 0 0-2.122 2.136C0 8.086 0 12 0 12s0 3.914.501 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.55 9.377.55 9.377.55s7.505 0 9.377-.55a3.016 3.016 0 0 0 2.122-2.136C24 15.914 24 12 24 12s0-3.914-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>
            
            <!-- Kontak Info -->
            <div class="kontak-block">
                <h2>Kontak</h2>
                
                <div class="kontak-item">
                    <div class="kontak-icon">
                        <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <div>
                        <div class="kontak-label">Alamat</div>
                        <div class="kontak-value">Cimerta Tengah, Tugumukti, Kec. Cisarua, Kabupaten Bandung Barat, Jawa Barat 40551</div>
                    </div>
                </div>
                
                <div class="kontak-item">
                    <div class="kontak-icon">
                        <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div>
                        <div class="kontak-label">Telepon / WhatsApp</div>
                        <div class="kontak-value">+62 821-2958-9232</div>
                    </div>
                </div>
                
                <div class="kontak-item">
                    <div class="kontak-icon">
                        <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <div>
                        <div class="kontak-label">Email</div>
                        <div class="kontak-value">{{ $settings['contact_email'] ?? 'asrfarm.id@gmail.com' }}</div>
                    </div>
                </div>

                {{-- WhatsApp Button --}}
                <div style="margin-top: 1.5rem;">
                    <a href="https://wa.me/6282129589232?text=Halo%20ASR%20Farm%2C%0A%0APerkenalkan%2C%20saya%20%5BNama%20Anda%5D%20dari%20%5BPerusahaan%2FPersonal%5D.%0A%0ASaya%20ingin%20menanyakan%20informasi%20mengenai%3A%0A%E2%80%A2%20%5BTulis%20keperluan%20Anda%2C%20misal%3A%20produk%2C%20harga%2C%20kerjasama%2C%20dll%5D%0A%0ATerima%20kasih."
                       target="_blank"
                       rel="noopener"
                       style="display: inline-flex; align-items: center; gap: 10px; background: #25D366; color: white; padding: 14px 28px; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 15px rgba(37,211,102,0.35); transition: all 0.3s; font-family: inherit;">
                        <svg width="22" height="22" fill="white" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Chat via WhatsApp
                    </a>
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

                @if(session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <ul style="margin: 0; padding-left: 1.2rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/contact" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Name <span>*</span></label>
                        <input type="text" name="name" class="form-input" placeholder="Masukkan nama Anda" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email <span>*</span></label>
                        <input type="email" name="email" class="form-input" placeholder="Masukkan email Anda" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Subject <span>*</span></label>
                        <input type="text" name="subject" class="form-input" placeholder="Subjek pesan" value="{{ old('subject') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Message <span>*</span></label>
                        <textarea name="message" class="form-input" placeholder="Tulis pesan Anda di sini..." required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn-submit">Kirim Pesan</button>
                </form>
            </div>
            
            <!-- Google Map -->
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps?q=ASR+Farm+Hidroponik+Cimerta+Tengah+Tugumukti+Cisarua+Kabupaten+Bandung+Barat+Jawa+Barat+40551&output=embed"
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>
</div>
@endsection
