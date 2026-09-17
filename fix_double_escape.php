<?php
$file = "resources/views/master-data/employees.blade.php";
$content = file_get_contents($file);
$search = "data-employee=\"{{ htmlspecialchars(json_encode(\$employee), ENT_QUOTES, 'UTF-8') }}\"";
$replace = "data-employee=\"{{ json_encode(\$employee) }}\"";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);

