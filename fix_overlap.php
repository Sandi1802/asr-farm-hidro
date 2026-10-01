<?php
$f = 'resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);

$oldInnerDiv = '<div style="display: flex; align-items: center; gap: 0.5rem; position: relative;">';
$newInnerDiv = '<div style="display: flex; align-items: center; gap: 0.5rem; position: relative; flex-wrap: wrap; justify-content: flex-end;">';

$c = str_replace($oldInnerDiv, $newInnerDiv, $c);

// Reduce padding on mobile by adding it to the media query
$oldMedia = <<<'EOF'
@media (max-width: 600px) {
    .cal-event-label { display: none !important; }
EOF;
$newMedia = <<<'EOF'
@media (max-width: 600px) {
    .cal-event-label { display: none !important; }
    .cal-header-container { padding: 1rem 0.75rem !important; }
EOF;
$c = str_replace($oldMedia, $newMedia, $c);

// And we need to add the class `cal-header-container` to the header div
$oldHeader = '<div style="padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">';
$newHeader = '<div class="cal-header-container" style="padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">';
$c = str_replace($oldHeader, $newHeader, $c);

// Remove the min-width 140px to let it shrink properly
$oldNavText = '<div style="display:flex; gap:0.25rem; font-weight:700; font-size:1rem; color:var(--text-main); min-width:140px; justify-content:center; align-items:center;">';
$newNavText = '<div style="display:flex; gap:0.25rem; font-weight:700; font-size:1rem; color:var(--text-main); justify-content:center; align-items:center;">';
$c = str_replace($oldNavText, $newNavText, $c);


file_put_contents($f, $c);
echo "Fixed button overlap.\n";
