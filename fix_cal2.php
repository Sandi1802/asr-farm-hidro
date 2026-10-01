<?php
$f = 'resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);

// 1. Let's fix the JS part using preg_replace
$searchJs = '/dots \+= `<div style="font-size: 0\.65rem; background: \$\{color\}15; border-left: 2px solid \$\{color\}; color: \$\{color\}; padding: 2px 4px; border-radius: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; justify-content: space-between; margin-bottom: 2px; font-weight: 700; line-height:1;">\s*<span>\$\{label\}<\/span>\s*<span style="opacity:0\.8;">\$\{count > 1 \? count : \'\'\}<\/span>\s*<\/div>`;/s';

$replaceJs = 'dots += `<div class="cal-event-pill" style="font-size: 0.65rem; background: ${color}15; border-left: 2px solid ${color}; color: ${color}; padding: 2px 4px; border-radius: 3px; display: flex; justify-content: space-between; align-items:center; gap: 4px; margin-bottom: 2px; font-weight: 700; line-height:1; min-width:0;">
                <span class="cal-event-label" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; text-align: left;">${label}</span>
                <span style="opacity:0.9; flex-shrink: 0;">${count > 1 ? count : \'\'}</span>
            </div>`;';

$c = preg_replace($searchJs, $replaceJs, $c);

// 2. Add media query to CSS
$css = <<<'EOF'
@media (max-width: 600px) {
    .cal-event-label { display: none !important; }
    .cal-event-pill { justify-content: center !important; text-align: center; }
}
EOF;
$c = str_replace('</style>', $css . "\n</style>", $c);

// 3. Wait, I also previously added `<div class="cal-scroll-wrapper"` which didn't apply properly?
// Let's check if cal-scroll-wrapper is there.
if (strpos($c, 'cal-scroll-wrapper') === false) {
    $searchGrid = '/<div class="cal-grid" style="margin-bottom:6px;">.*?<div class="cal-day-header">\{\{ \$d \}\}<\/div>\s*@endforeach\s*<\/div>\s*<div class="cal-grid" id="calBody" style="flex: 1;"><\/div>/s';
    $replaceGrid = <<<'EOF'
<div class="cal-scroll-wrapper" style="overflow-x: auto; padding-bottom: 0.5rem; width: 100%;">
                    <div style="min-width: 500px;">
                        <div class="cal-grid" style="margin-bottom:6px;">
                            @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
                            <div class="cal-day-header">{{ $d }}</div>
                            @endforeach
                        </div>
                        <div class="cal-grid" id="calBody" style="flex: 1;"></div>
                    </div>
                </div>
EOF;
    $c = preg_replace($searchGrid, $replaceGrid, $c);
} else {
    // If it's already there but min-width is 650px, let's change it to 500px so it's not TOO wide on mobile.
    $c = str_replace('min-width: 650px;', 'min-width: 500px;', $c);
}

file_put_contents($f, $c);
echo "JS replaced.\n";
