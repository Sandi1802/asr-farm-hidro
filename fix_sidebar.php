<?php
$file = "resources/views/layouts/sidebar.blade.php";
$content = file_get_contents($file);
$search = "\$hidroponikActive = request()->is('hydroponics/greenhouses*') || request()->is('hydroponics/semai*') || request()->is('hydroponics/maintenance-logs*') || request()->is('hydroponics/damage-notes*');";
$replace = "\$hidroponikActive = request()->is('hydroponics/greenhouses*') || request()->is('hydroponics/semai*') || request()->is('hydroponics/daily-tasks*') || request()->is('hydroponics/maintenance-logs*') || request()->is('hydroponics/damage-notes*');";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);

