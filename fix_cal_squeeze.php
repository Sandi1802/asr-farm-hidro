<?php
$f = 'resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);

// 1. Remove min-width: 500px and overflow-x: auto
$c = str_replace('<div class="cal-scroll-wrapper" style="overflow-x: auto; padding-bottom: 0.5rem; width: 100%;">', '<div class="cal-scroll-wrapper" style="width: 100%;">', $c);
$c = str_replace('<div style="min-width: 500px;">', '<div style="width: 100%;">', $c);

// 2. Fix Header wrapping
$c = str_replace('<div style="padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">', '<div style="padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">', $c);

// 3. Update media query for better mobile squeezing
$oldMedia = <<<'EOF'
@media (max-width: 600px) {
    .cal-event-label { display: none !important; }
    .cal-event-pill { justify-content: center !important; text-align: center; }
}
EOF;
$newMedia = <<<'EOF'
@media (max-width: 600px) {
    .cal-event-label { display: none !important; }
    .cal-event-pill { justify-content: center !important; text-align: center; padding: 2px 0 !important; font-size: 0.55rem !important; margin-bottom: 1px !important; border-left-width: 1px !important; }
    .cal-day { padding: 4px 2px !important; min-height: 55px !important; }
    .cal-day-num { font-size: 0.7rem !important; margin-bottom: 2px !important; }
    .cal-day-header { font-size: 0.6rem !important; padding: 0.2rem 0 !important; }
    .cal-grid { gap: 3px !important; }
}
EOF;
$c = str_replace($oldMedia, $newMedia, $c);

file_put_contents($f, $c);
echo "Squeezed calendar for mobile.\n";
