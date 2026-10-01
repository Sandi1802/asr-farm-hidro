<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// Replace CSS
$c = preg_replace('/\.global-marquee-wrapper\s*\{.*?\}/s', '.global-marquee-wrapper { overflow: hidden; display: flex; width: 100%; }', $c);
$c = preg_replace('/\.global-marquee-content\s*\{.*?\}/s', '.global-marquee-content { display: flex; flex-shrink: 0; align-items: center; white-space: nowrap; animation: global-marquee-anim 25s linear infinite; }', $c);
$c = preg_replace('/@keyframes\s+global-marquee-anim\s*\{.*?\}/s', '.global-marquee-item { display: flex; align-items: center; padding-right: 3rem; } @keyframes global-marquee-anim { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }', $c);

// Replace HTML
$htmlRegex = '/<div class="global-marquee-wrapper">.*?<div class="global-marquee-content">.*?<img.*?<span.*?>(.*?)<\/span>.*?<\/div>.*?<\/div>/s';

$newHtml = '<div class="global-marquee-wrapper">
                    <div class="global-marquee-content">
                        <div class="global-marquee-item">
                            <img src="{{ asset(\'images/logo-asr.png\') }}" alt="Logo" style="height: 24px; width: 24px; object-fit: cover; margin-right: 10px; background-color: #ffffff; border-radius: 50%; padding: 1px;">
                            <span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">$1</span>
                        </div>
                        <div class="global-marquee-item">
                            <img src="{{ asset(\'images/logo-asr.png\') }}" alt="Logo" style="height: 24px; width: 24px; object-fit: cover; margin-right: 10px; background-color: #ffffff; border-radius: 50%; padding: 1px;">
                            <span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">$1</span>
                        </div>
                    </div>
                </div>';

$c = preg_replace($htmlRegex, $newHtml, $c);

file_put_contents($f, $c);
echo "Marquee updated via regex.\n";
