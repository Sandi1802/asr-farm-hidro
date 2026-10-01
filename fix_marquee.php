<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// 1. Replace the CSS
$oldCss = <<<'EOF'
        .global-marquee-wrapper { overflow: hidden; white-space: nowrap; box-sizing: border-box; width: 100%; }
        .global-marquee-content { display: inline-flex; align-items: center; white-space: nowrap; padding-left: 100%; animation: global-marquee-anim 25s linear infinite; }
        .global-marquee-content:hover { animation-play-state: paused; }
        @keyframes global-marquee-anim { 0% { transform: translate(0, 0); } 100% { transform: translate(-100%, 0); } }
EOF;

$newCss = <<<'EOF'
        .global-marquee-wrapper { overflow: hidden; display: flex; width: 100%; }
        .global-marquee-content { display: flex; flex-shrink: 0; align-items: center; white-space: nowrap; animation: global-marquee-anim 25s linear infinite; }
        .global-marquee-content:hover { animation-play-state: paused; }
        .global-marquee-item { display: flex; align-items: center; padding-right: 3rem; }
        @keyframes global-marquee-anim { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
EOF;

$c = str_replace($oldCss, $newCss, $c);

// 2. Replace the HTML structure
$oldHtml = <<<'EOF'
                    <div class="global-marquee-wrapper">
                        <div class="global-marquee-content">
                            <img src="{{ asset('images/logo-asr.png') }}" alt="Logo" style="height: 24px; width: 24px; object-fit: cover; margin-right: 10px; background-color: #ffffff; border-radius: 50%; padding: 1px;">
                            <span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">Selamat Datang, {{ Auth::user()->name ?? 'Super Admin' }}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.</span>
                        </div>
                    </div>
EOF;

$newHtml = <<<'EOF'
                    <div class="global-marquee-wrapper">
                        <div class="global-marquee-content">
                            <!-- Item 1 -->
                            <div class="global-marquee-item">
                                <img src="{{ asset('images/logo-asr.png') }}" alt="Logo" style="height: 24px; width: 24px; object-fit: cover; margin-right: 10px; background-color: #ffffff; border-radius: 50%; padding: 1px;">
                                <span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">Selamat Datang, {{ Auth::user()->name ?? 'Super Admin' }}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.</span>
                            </div>
                            <!-- Item 2 (Duplicate for seamless loop) -->
                            <div class="global-marquee-item">
                                <img src="{{ asset('images/logo-asr.png') }}" alt="Logo" style="height: 24px; width: 24px; object-fit: cover; margin-right: 10px; background-color: #ffffff; border-radius: 50%; padding: 1px;">
                                <span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">Selamat Datang, {{ Auth::user()->name ?? 'Super Admin' }}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.</span>
                            </div>
                        </div>
                    </div>
EOF;

$c = str_replace($oldHtml, $newHtml, $c);
file_put_contents($f, $c);
echo "Marquee updated.\n";
