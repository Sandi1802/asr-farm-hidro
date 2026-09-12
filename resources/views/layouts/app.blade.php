<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASR Farm</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('scripts')
</head>
<body>
    <nav class="navbar">
      <div class="nav-container" style="max-width: 1400px; margin: 0 auto; padding: 0 2rem; width: 100%;">
        <a href="/" class="nav-logo" style="gap: 10px; font-size: 1.6rem;">
          <img src="{{ asset('images/logo.jpg') }}" alt="ASR Farm Logo" style="width: 45px; height: 45px;">
          ASR <span style="color: var(--color-accent);">FARM</span>
        </a>
        
        <button class="mobile-toggle" id="menu-toggle" onclick="toggleMenu()">
          <span class="hamburger-icon">☰</span>
        </button>

        <div class="nav-links" id="nav-menu" style="gap: 2rem;">
          <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
          <a href="/about" class="nav-link {{ request()->is('about') ? 'active' : '' }}">About</a>
          <a href="/blog" class="nav-link {{ request()->is('blog') ? 'active' : '' }}">Article</a>
          <a href="/testimonials" class="nav-link {{ request()->is('testimonials') ? 'active' : '' }}">Testimonials</a>
          <a href="/contact" class="nav-link {{ request()->is('contact') ? 'active' : '' }}">Contact Us</a>
          
          <a href="/products" style="background-color: #27ae60; color: white; border-radius: 8px; padding: 10px 24px; font-weight: 600; font-size: 0.95rem; text-decoration: none; margin-left: 1rem; transition: background 0.3s;">Our Product</a>
        </div>
      </div>
    </nav>

    <main style="min-height: 80vh;">
        @yield('content')
    </main>

    <a 
      href="https://wa.me/6281234567890" 
      target="_blank" 
      rel="noopener noreferrer"
      style="position: fixed; bottom: 30px; right: 30px; background-color: #25D366; color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 1000; text-decoration: none; font-size: 30px;"
    >
      <svg viewBox="0 0 32 32" width="35" height="35" fill="currentColor">
        <path d="M16 2.3A13.7 13.7 0 002.3 16c0 2.4.6 4.7 1.8 6.6L2 29.5l7-1.8a13.6 13.6 0 006.9 1.9 13.7 13.7 0 0013.7-13.6A13.7 13.7 0 0016 2.3zm0 23.3c-2 0-3.9-.5-5.5-1.5l-.4-.2-4.1 1.1 1.1-4-.2-.4a11.3 11.3 0 01-1.7-6 11.4 11.4 0 0111.4-11.4 11.4 11.4 0 0111.4 11.4A11.4 11.4 0 0116 25.6zm6.3-8.6c-.3-.2-2-.9-2.3-1-.3-.2-.6-.2-.7 0s-1 1-1.2 1.3c-.2.2-.4.3-.8.1-.3-.2-1.4-.5-2.7-1.7-1-1-1.7-2-2-2.3-.2-.4 0-.5.2-.7l.5-.6c.2-.2.2-.4.3-.7.1-.2 0-.4 0-.6s-1-2.4-1.3-3.3c-.3-.8-.7-.7-1-.7h-.8c-.3 0-.8.1-1.2.6-.4.5-1.6 1.6-1.6 3.8s1.6 4.3 1.8 4.6c.2.3 3.1 4.7 7.5 6.6 1 .4 1.9.7 2.6.9 1 .3 2 .3 2.8.2.9-.1 2-.8 2.3-1.6.3-.8.3-1.4.2-1.6-.2-.2-.5-.3-.9-.5z"/>
      </svg>
    </a>

    <footer style="background-color: var(--color-primary-dark); color: white; padding: 4rem 16px 2rem 16px; margin-top: 4rem; font-family: var(--font-sans);">
      <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; margin-bottom: 3rem;">
            
            <!-- Column 1: Logo & Contact -->
            <div>
                <a href="/" style="display: flex; align-items: center; gap: 10px; color: white; text-decoration: none; margin-bottom: 1.5rem; font-size: 2rem; font-weight: bold;">
                    <img src="{{ asset('images/logo.jpg') }}" alt="ASR Farm" style="height: 45px; border-radius: 5px;">
                    ASR <span style="color: var(--color-accent);">Farm</span>
                </a>
                <ul style="list-style: none; padding: 0; margin: 0; color: #ddd; font-size: 0.95rem; line-height: 1.8;">
                    <li style="display: flex; gap: 12px; margin-bottom: 12px; align-items: flex-start;">
                        <span style="flex-shrink: 0; margin-top: 2px;">
                            <svg width="20" height="20" fill="none" stroke="var(--color-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </span>
                        <span>{{ $settings['contact_address'] ?? 'Cimerta Tengah, Tugumukti, Kec. Cisarua, Kabupaten Bandung Barat, Jawa Barat 40551' }}</span>
                    </li>
                    <li style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center;">
                        <span style="flex-shrink: 0;">
                            <svg width="20" height="20" fill="none" stroke="var(--color-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </span>
                        <span>{{ $settings['contact_phone'] ?? '+62 821-2958-9232' }}</span>
                    </li>
                    <li style="display: flex; gap: 12px; align-items: center;">
                        <span style="flex-shrink: 0;">
                            <svg width="20" height="20" fill="none" stroke="var(--color-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </span>
                        <span>{{ $settings['contact_email'] ?? 'asrfarm.id@gmail.com' }}</span>
                    </li>
                </ul>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h4 style="color: white; font-size: 1.2rem; margin-bottom: 1.5rem; letter-spacing: 1px;">Menu Pintas</h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.95rem; line-height: 2;">
                    <li><a href="/" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Beranda</a></li>
                    <li><a href="/about" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Tentang Kami</a></li>
                    <li><a href="/blog" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Artikel</a></li>
                    <li><a href="/testimonials" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Testimoni</a></li>
                    <li><a href="/contact" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Kontak</a></li>
                </ul>
            </div>

            <!-- Column 3: Social Media -->
            <div>
                <h4 style="color: white; font-size: 1.2rem; margin-bottom: 1.5rem; letter-spacing: 1px;">Ikuti Kami</h4>
                <div class="social-icons" style="gap: 12px;">
                    <a href="{{ $settings['social_ig'] ?? '#' }}" class="social-icon footer-social" title="Instagram">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.07M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="{{ $settings['social_tiktok'] ?? '#' }}" class="social-icon footer-social" title="TikTok">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.53 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    </a>
                    <a href="{{ $settings['social_fb'] ?? '#' }}" class="social-icon footer-social" title="Facebook">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                    </a>
                    <a href="{{ $settings['social_linkedin'] ?? '#' }}" class="social-icon footer-social" title="LinkedIn">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                    </a>
                    <a href="{{ $settings['social_youtube'] ?? 'https://youtube.com/@asrfarm' }}" class="social-icon footer-social" title="YouTube" target="_blank" rel="noopener noreferrer">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.377.55a3.016 3.016 0 0 0-2.122 2.136C0 8.086 0 12 0 12s0 3.914.501 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.55 9.377.55 9.377.55s7.505 0 9.377-.55a3.016 3.016 0 0 0 2.122-2.136C24 15.914 24 12 24 12s0-3.914-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>

        </div>

        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 1rem; color: #aaa; font-size: 0.85rem;">
            <div>Copyright © {{ date('Y') }} ASR Farm</div>
            <div>Powered by ASR Group</div>
        </div>
      </div>
    </footer>
    <style>
      footer a:not(.footer-social):hover { color: var(--color-accent) !important; }
    </style>

    <script>
      // Hamburger menu toggle
      function toggleMenu() {
          const menu = document.getElementById('nav-menu');
          const icon = document.querySelector('.hamburger-icon');
          menu.classList.toggle('show');
          if (menu.classList.contains('show')) {
              icon.textContent = '✕';
              icon.style.transform = 'rotate(90deg)';
          } else {
              icon.textContent = '☰';
              icon.style.transform = 'rotate(0deg)';
          }
      }

      // Scroll animations
      document.addEventListener("DOMContentLoaded", function() {
          const observerOptions = {
              root: null,
              rootMargin: '0px',
              threshold: 0.15
          };

          const observer = new IntersectionObserver((entries, observer) => {
              entries.forEach(entry => {
                  if (entry.isIntersecting) {
                      entry.target.classList.add('visible');
                      observer.unobserve(entry.target); // Optional: only animate once
                  }
              });
          }, observerOptions);

          document.querySelectorAll('.animate-fade-up, .animate-fade-in, .animate-fade-left, .animate-fade-right').forEach(el => {
              observer.observe(el);
          });
      });
    </script>
    {{-- Floating WhatsApp Button --}}
    @php $waNumber = $settings['whatsapp_number'] ?? '6282129589232'; @endphp
    <a href="https://wa.me/{{ $waNumber }}?text=Halo%20ASR%20Farm%2C%0A%0APerkenalkan%2C%20saya%20%5BNama%20Anda%5D%20dari%20%5BPerusahaan%2FPersonal%5D.%0A%0ASaya%20ingin%20menanyakan%20informasi%20mengenai%3A%0A%E2%80%A2%20%5BTulis%20keperluan%20Anda%2C%20misal%3A%20produk%2C%20harga%2C%20kerjasama%2C%20dll%5D%0A%0ATerima%20kasih."
       target="_blank"
       rel="noopener"
       title="Chat via WhatsApp"
       style="
           position: fixed;
           bottom: 28px;
           right: 28px;
           z-index: 9999;
           background: #25D366;
           color: white;
           width: 60px;
           height: 60px;
           border-radius: 50%;
           display: flex;
           align-items: center;
           justify-content: center;
           box-shadow: 0 4px 20px rgba(37,211,102,0.5);
           text-decoration: none;
           transition: transform 0.3s, box-shadow 0.3s;
           animation: wa-pulse 2.5s infinite;
       "
       onmouseover="this.style.transform='scale(1.12)'; this.style.boxShadow='0 6px 28px rgba(37,211,102,0.7)';"
       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 20px rgba(37,211,102,0.5)';">
        <svg width="30" height="30" fill="white" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
    <style>
        @keyframes wa-pulse {
            0% { box-shadow: 0 4px 20px rgba(37,211,102,0.5); }
            50% { box-shadow: 0 4px 35px rgba(37,211,102,0.85); }
            100% { box-shadow: 0 4px 20px rgba(37,211,102,0.5); }
        }
    </style>
</body>
</html>
