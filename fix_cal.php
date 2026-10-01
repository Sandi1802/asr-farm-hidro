<?php
$f = 'resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);

// 1. Wrap calendar grid
$oldGrid = <<<'EOF'
                <div class="cal-grid" style="margin-bottom:6px;">
                    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
                    <div class="cal-day-header">{{ $d }}</div>
                    @endforeach
                </div>
                <div class="cal-grid" id="calBody" style="flex: 1;"></div>
EOF;
$newGrid = <<<'EOF'
                <div class="cal-scroll-wrapper" style="overflow-x: auto; padding-bottom: 0.5rem; width: 100%;">
                    <div style="min-width: 650px;">
                        <div class="cal-grid" style="margin-bottom:6px;">
                            @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
                            <div class="cal-day-header">{{ $d }}</div>
                            @endforeach
                        </div>
                        <div class="cal-grid" id="calBody" style="flex: 1;"></div>
                    </div>
                </div>
EOF;
$c = str_replace($oldGrid, $newGrid, $c);

// 2. Fix Javascript pills
$oldJs = <<<'EOF'
            dots += `<div style="font-size: 0.65rem; background: ${color}15; border-left: 2px solid ${color}; color: ${color}; padding: 2px 4px; border-radius: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; justify-content: space-between; margin-bottom: 2px; font-weight: 700; line-height:1;">
                <span>${label}</span>
                <span style="opacity:0.8;">${count > 1 ? count : ''}</span>
            </div>`;
EOF;
$newJs = <<<'EOF'
            dots += `<div style="font-size: 0.65rem; background: ${color}15; border-left: 2px solid ${color}; color: ${color}; padding: 2px 4px; border-radius: 3px; display: flex; justify-content: space-between; gap: 4px; margin-bottom: 2px; font-weight: 700; line-height:1;">
                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; text-align: left;">${label}</span>
                <span style="opacity:0.8; flex-shrink: 0;">${count > 1 ? count : ''}</span>
            </div>`;
EOF;
$c = str_replace($oldJs, $newJs, $c);

file_put_contents($f, $c);
echo "Calendar view fixed.\n";
