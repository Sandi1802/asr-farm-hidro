@extends('admin.layout')

@section('content')
<div>
    <h1>Pengaturan Teks Website</h1>
    <p>Ubah teks di berbagai halaman website di sini.</p>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <form action="/admin/settings" method="POST">
            @csrf
            
            <h3 style="border-bottom: 2px solid var(--color-primary); padding-bottom: 0.5rem; margin-bottom: 1rem;">Halaman Home</h3>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Judul Hero Utama (contoh: ASR Farm)</label>
                <input type="text" name="home_hero_title" value="{{ $settings['home_hero_title'] ?? '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Teks Penjelasan Hero (Kanan)</label>
                <textarea name="home_hero_text" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" rows="4">{{ $settings['home_hero_text'] ?? '' }}</textarea>
            </div>

            <h3 style="border-bottom: 2px solid var(--color-primary); padding-bottom: 0.5rem; margin-bottom: 1rem; margin-top: 2rem;">Halaman About</h3>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Kutipan (Quote) Tentang Kami</label>
                <textarea name="about_quote" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" rows="3">{{ $settings['about_quote'] ?? '' }}</textarea>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Judul Banner About</label>
                <input type="text" name="about_banner_title" value="{{ $settings['about_banner_title'] ?? '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Teks Singkat Banner About</label>
                <textarea name="about_banner_text" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" rows="3">{{ $settings['about_banner_text'] ?? '' }}</textarea>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Isi Cerita Kami (Panjang)</label>
                <textarea name="about_story" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" rows="6">{{ $settings['about_story'] ?? '' }}</textarea>
            </div>

            <h3 style="border-bottom: 2px solid var(--color-primary); padding-bottom: 0.5rem; margin-bottom: 1rem; margin-top: 2rem;">Informasi Kontak & Footer</h3>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Alamat Lengkap</label>
                <textarea name="contact_address" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" rows="2">{{ $settings['contact_address'] ?? '' }}</textarea>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Email</label>
                <input type="text" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Nomor Telepon / WA</label>
                <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Teks Singkat Footer</label>
                <textarea name="footer_text" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" rows="2">{{ $settings['footer_text'] ?? '' }}</textarea>
            </div>

            <h3 style="border-bottom: 2px solid var(--color-primary); padding-bottom: 0.5rem; margin-bottom: 1rem; margin-top: 2rem;">Media Sosial (Link)</h3>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Link Facebook</label>
                <input type="text" name="social_fb" value="{{ $settings['social_fb'] ?? '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Link Instagram</label>
                <input type="text" name="social_ig" value="{{ $settings['social_ig'] ?? '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Link TikTok</label>
                <input type="text" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Link LinkedIn</label>
                <input type="text" name="social_linkedin" value="{{ $settings['social_linkedin'] ?? '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <button type="submit" style="background: var(--color-primary); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 1rem;">Simpan Pengaturan</button>
        </form>
    </div>
</div>
@endsection
