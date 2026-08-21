@extends('layouts.app')

@section('content')
<style>
/* Header banner */
.about-banner {
    position: relative;
    min-height: 25vh;
    display: flex;
    align-items: flex-end;
    background-image: linear-gradient(135deg, rgba(30,59,34,0.85) 0%, rgba(30,59,34,0.4) 60%), url('{{ asset('images/kebun-wide.png') }}');
    background-size: cover;
    background-position: center 30%;
    padding: 7rem 16px 1.5rem 16px;
    color: white;
}
.about-banner-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    justify-content: space-between;
    align-items: center;
}
.about-banner-title {
    color: var(--color-accent);
    font-size: 3rem;
    font-weight: bold;
    font-family: var(--font-serif);
    margin: 0;
    position: relative;
}
.about-banner-title::before {
    content: "";
    position: absolute;
    top: -15px;
    left: 0;
    width: 60px;
    height: 3px;
    background-color: var(--color-accent);
}
.about-banner-text {
    flex: 1;
    min-width: 300px;
    max-width: 600px;
    font-size: 1.1rem;
    line-height: 1.8;
}

/* Team section */
.team-section {
    padding: 5rem 16px;
    background-color: #fff;
    max-width: 1200px;
    margin: 0 auto;
}
.main-team-photo {
    width: 100%;
    border-radius: 12px;
    margin-bottom: 4rem;
}
.team-title {
    text-align: center;
    font-size: 2.5rem;
    color: var(--color-primary-dark);
    margin-bottom: 3rem;
    font-weight: bold;
}

/* Profiles */
.profile-img {
    border-radius: 50%;
    object-fit: cover;
}
.profile-img-large {
    width: 250px;
    height: 250px;
    margin: 0 auto 1.5rem auto;
    display: block;
}
.profile-img-small {
    width: 150px;
    height: 150px;
    flex-shrink: 0;
}
.ceo-profile {
    text-align: center;
    margin-bottom: 6rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}
.ceo-name {
    font-size: 1.8rem;
    color: var(--color-primary-dark);
    font-weight: bold;
    margin-bottom: 0.2rem;
}
.ceo-title {
    color: #666;
    font-size: 1rem;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.ceo-desc {
    color: #555;
    line-height: 1.8;
    font-size: 1.05rem;
}

.team-member {
    display: flex;
    align-items: center;
    gap: 3rem;
    margin-bottom: 4rem;
}
.team-member.reverse {
    flex-direction: row-reverse;
}
.member-info {
    flex: 1;
}
.member-name {
    font-size: 1.5rem;
    color: var(--color-primary-dark);
    font-weight: bold;
    margin-bottom: 0.2rem;
}
.member-title {
    color: #666;
    font-size: 0.95rem;
    margin-bottom: 1rem;
}
.member-desc {
    color: #555;
    line-height: 1.8;
    font-size: 0.95rem;
}

@media(max-width: 768px) {
    .team-member, .team-member.reverse {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    .about-banner-title {
        font-size: 2.2rem;
    }
}
.text-gold { color: var(--color-accent); }
.text-green { color: var(--color-primary); }
</style>

<div>
    <!-- Banner -->
    <div class="about-banner animate-fade-up">
        <div class="about-banner-container">
            <div>
                <h1 class="about-banner-title">{{ $settings['about_banner_title'] ?? 'Tentang Kami' }}</h1>
            </div>
            <div class="about-banner-text">
                {{ $settings['about_banner_text'] ?? 'Seperti sayuran yang kita produksi, domba yang kami pelihara, sapi yang menghasilkan susu dan telor dari ayam; kami sangatlah beragam. Dan yang paling penting kami pribadi yang siap berkotor-kotor bersama petani dampingan kami untuk menyiapkan bahan makanan untuk anda.' }}
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="team-section">
        
        <!-- About Text and Collage (Copied from Home) -->
        <div style="display: flex; flex-wrap: wrap; gap: 4rem; align-items: center; margin-bottom: 6rem;">
            <!-- Left text -->
            <div style="flex: 1; min-width: 300px;">
                <h5 class="text-gold" style="font-weight: 600; letter-spacing: 1px; margin-bottom: 1rem; font-size: 1rem;">TENTANG KAMI</h5>
                <h2 class="text-green" style="font-size: 2.2rem; line-height: 1.3; margin-bottom: 2rem; font-family: var(--font-serif); font-weight: bold;">
                    {!! nl2br(e($settings['about_quote'] ?? "\"Menanam sayuran itu seperti merawat cinta. Harus dilakukan dengan sepenuh hati atau tidak sama sekali.\" \n— ASR Farm")) !!}
                </h2>
                <div style="color: #666; line-height: 1.8; margin-bottom: 2.5rem; font-size: 1.05rem;">
                    {!! nl2br(e($settings['about_story'] ?? "Ya, kami ingin membantu Anda menyediakan sayuran dan bahan alami yang segar untuk keluarga tercinta. Menikmati hidangan sehari-hari akan menjadi pengalaman yang sungguh menyenangkan manakala didukung oleh hasil panen yang berkualitas dan menyehatkan. Anda setuju?\n\nASR Farm adalah gagasan tentang membangun ekosistem perkebunan yang lebih baik bagi Anda, lingkungan, dan para petani lokal yang merawat sayuran-sayuran ini dengan sepenuh hati.\n\nMari mulai gaya hidup sehat dengan sayuran organik dan hidroponik terbaik untuk kebaikan keluarga kita saat ini dan masa depan.")) !!}
                </div>
            </div>
            
            <!-- Right images collage -->
            <div style="flex: 1; min-width: 300px;">
                <div style="position: relative; height: 500px; width: 100%;">
                    <img src="{{ asset('images/greenhouse.jpg') }}" style="position: absolute; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); object-fit: cover; width: 65%; height: 280px; top: 0; right: 0; z-index: 1;" alt="Greenhouse">
                    <img src="{{ asset('images/kebun-wide.png') }}" style="position: absolute; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); object-fit: cover; width: 55%; height: 280px; bottom: 20px; left: 0; z-index: 2; border: 8px solid white;" alt="Kebun">
                    <img src="{{ asset('images/bg-profile.jpg') }}" style="position: absolute; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); object-fit: cover; width: 50%; height: 200px; bottom: 70px; right: -10px; z-index: 3; border: 8px solid white;" alt="Gate">
                </div>
            </div>
        </div>
        
        <h2 class="team-title">Tim Kami</h2>
        
        <!-- CEO -->
        <div class="ceo-profile">
            <!-- Using placeholder images until you provide actual team member photos -->
            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=600&auto=format&fit=crop" alt="Devi Silvia" class="profile-img profile-img-large">
            <h3 class="ceo-name">Devi Silvia</h3>
            <p class="ceo-title">CEO</p>
            <div class="ceo-desc">
                <p>Ada pepatah India yang menyatakan bahwa orang sehat memiliki harapan, dan orang yang memiliki harapan memiliki segalanya.</p>
                <p>Bagaimana menjaga kesehatan Anda dan keluarga Anda sangatlah penting. Penting karena dari makanan yang kita konsumsi masuk ke dalam tubuh dan menjadi sumber vitamin, mineral dan serat. Maka tak heran jika selalu ada yang mengatakan bahwa kesehatan dimulai dari piring makan Anda.</p>
            </div>
        </div>

        <!-- Team Members -->
        <div class="team-member">
            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=600&auto=format&fit=crop" alt="Nasrudin" class="profile-img profile-img-small">
            <div class="member-info">
                <h3 class="member-name">Nasrudin</h3>
                <p class="member-title">Sourcing Manager</p>
                <div class="member-desc">
                    <p>Saya berlatar belakang pendidikan industri, kami memiliki beberapa tugas khusus, karena hal ini sangat penting dalam menjaga konsistensi produk pertanian di ASR Farm. Di samping itu saya mengkoordinir teman-teman di lapangan untuk memastikan standar kualitas berbagai produk yang dihasilkan.</p>
                </div>
            </div>
        </div>

        <div class="team-member reverse">
            <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=600&auto=format&fit=crop" alt="Ragil Subarkah" class="profile-img profile-img-small">
            <div class="member-info" style="text-align: right;">
                <h3 class="member-name">Ragil Subarkah</h3>
                <p class="member-title">Farm Manager</p>
                <div class="member-desc">
                    <p>Saya adalah seorang Farm Manager. Berdiri di atas hamparan, meneliti tanaman, dan mengelola lahan bersama tim. Hal yang paling membanggakan adalah saat melihat apa yang kami tanam tumbuh sehat dan memberikan manfaat. Dengan latar belakang saya dari fakultas Pertanian Universitas ternama di Indonesia, saya selalu berusaha menerapkan ilmu terbaik.</p>
                </div>
            </div>
        </div>

        <div class="team-member">
            <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=600&auto=format&fit=crop" alt="Sasa" class="profile-img profile-img-small">
            <div class="member-info">
                <h3 class="member-name">Sasa</h3>
                <p class="member-title">Logistic Head</p>
                <div class="member-desc">
                    <p>Memastikan hasil panen didistribusikan secara efisien dengan kualitas terjaga sampai ke tangan konsumen. Koordinasi yang baik dari lapangan ke tempat penyimpanan merupakan kunci utama dalam menjaga rantai pasok sayuran agar tetap segar.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
