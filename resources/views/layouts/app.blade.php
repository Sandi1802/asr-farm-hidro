<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASR Farm</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
          
          <div class="lang-selector" style="cursor: pointer; background: transparent; border: none; font-weight: 500; display: flex; align-items: center; gap: 8px; padding: 0; margin-left: 1rem;">
              <img src="https://flagcdn.com/w20/us.png" alt="English" style="width: 20px; border-radius: 2px;"> EN
          </div>
          
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
                    <li style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <span>📍</span>
                        {{ $settings['contact_address'] ?? 'Jl. Setro Raya, Desa Gondoriyo, Kecamatan Bergas, Kabupaten Semarang' }}
                    </li>
                    <li style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <span>📞</span>
                        {{ $settings['contact_phone'] ?? '024 69335130' }}
                    </li>
                    <li style="display: flex; gap: 10px;">
                        <span>✉️</span>
                        {{ $settings['contact_email'] ?? 'hello@asrfarm.com' }}
                    </li>
                </ul>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h4 style="color: white; font-size: 1.2rem; margin-bottom: 1.5rem; letter-spacing: 1px;">Quick link</h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.95rem; line-height: 2;">
                    <li><a href="/" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Home</a></li>
                    <li><a href="/about" style="color: #ccc; text-decoration: none; transition: color 0.3s;">About</a></li>
                    <li><a href="/blog" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Article</a></li>
                    <li><a href="/testimonials" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Testimonials</a></li>
                    <li><a href="/contact" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Contact Us</a></li>
                </ul>
            </div>

            <!-- Column 3: Important Links -->
            <div>
                <h4 style="color: white; font-size: 1.2rem; margin-bottom: 1.5rem; letter-spacing: 1px;">Important links</h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.95rem; line-height: 2;">
                    <li><a href="/products" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Shop</a></li>
                    <li><a href="#" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Sitemap</a></li>
                    <li><a href="#" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Privacy Policy</a></li>
                </ul>
            </div>

        </div>

        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 1rem; color: #aaa; font-size: 0.85rem;">
            <div>Copyright © {{ date('Y') }} ASR Farm</div>
            <div>Powered by ASR Group</div>
        </div>
      </div>
    </footer>
    <style>
      footer a:hover { color: var(--color-accent) !important; }
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

          document.querySelectorAll('.animate-fade-up, .animate-fade-in').forEach(el => {
              observer.observe(el);
          });
      });
    </script>
</body>
</html>
