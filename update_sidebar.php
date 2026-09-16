<?php
$file = "resources/views/layouts/sidebar.blade.php";
$content = file_get_contents($file);
$search = "<a href=\"{{ route('hydroponics.maintenance-logs') }}\"";
$replace = "<a href=\"{{ route('hydroponics.daily-tasks') }}\" class=\"submenu-item {{ request()->is('hydroponics/daily-tasks*') ? 'active' : '' }}\">\n                    <i class=\"ph ph-calendar-check\" style=\"margin-right: 0.5rem; font-size: 1.1rem;\"></i> Daily Hidroponik\n                </a>\n                <a href=\"{{ route('hydroponics.maintenance-logs') }}\"";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);

