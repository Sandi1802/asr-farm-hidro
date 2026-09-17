<?php
$file = "resources/views/master-data/employees.blade.php";
$content = file_get_contents($file);
$search = "onclick=\"openEditModal({{ htmlspecialchars(json_encode(\$employee)) }})\">";
$replace = "onclick=\"openEditModal(this)\" data-employee=\"{{ htmlspecialchars(json_encode(\$employee), ENT_QUOTES, 'UTF-8') }}\">";
$content = str_replace($search, $replace, $content);

$searchJs = "function openEditModal(employee) {";
$replaceJs = "function openEditModal(btn) {\n        let employee = JSON.parse(btn.getAttribute('data-employee'));";
$content = str_replace($searchJs, $replaceJs, $content);

file_put_contents($file, $content);

