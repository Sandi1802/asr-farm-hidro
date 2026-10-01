<?php
$f = 'resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);

if (strpos($c, 'cal-scroll-wrapper" style="overflow-x') === false) {
    $searchGrid = '<div class="cal-grid" style="margin-bottom:6px;">' . "\n" .
                  '                    @foreach([\'Min\',\'Sen\',\'Sel\',\'Rab\',\'Kam\',\'Jum\',\'Sab\'] as $d)' . "\n" .
                  '                    <div class="cal-day-header">{{ $d }}</div>' . "\n" .
                  '                    @endforeach' . "\n" .
                  '                </div>' . "\n" .
                  '                <div class="cal-grid" id="calBody" style="flex: 1;"></div>';
                  
    $replaceGrid = '<div class="cal-scroll-wrapper" style="overflow-x: auto; padding-bottom: 0.5rem; width: 100%;">' . "\n" .
                   '                    <div style="min-width: 500px;">' . "\n" .
                   '                        <div class="cal-grid" style="margin-bottom:6px;">' . "\n" .
                   '                            @foreach([\'Min\',\'Sen\',\'Sel\',\'Rab\',\'Kam\',\'Jum\',\'Sab\'] as $d)' . "\n" .
                   '                            <div class="cal-day-header">{{ $d }}</div>' . "\n" .
                   '                            @endforeach' . "\n" .
                   '                        </div>' . "\n" .
                   '                        <div class="cal-grid" id="calBody" style="flex: 1;"></div>' . "\n" .
                   '                    </div>' . "\n" .
                   '                </div>';
                   
    $c = str_replace($searchGrid, $replaceGrid, $c);
    
    // In case the spacing is slightly different, let's also try regex
    if (strpos($c, 'cal-scroll-wrapper" style="overflow-x') === false) {
        $c = preg_replace('/<div class="cal-grid" style="margin-bottom:6px;">.*?id="calBody" style="flex: 1;"><\/div>/s', $replaceGrid, $c);
    }
}

file_put_contents($f, $c);
echo "Wrapper added.\n";
