<?php
$file = "resources/views/master-data/employees.blade.php";
$content = file_get_contents($file);
$search = "function openEditModal(btn) {";
$replace = "function openEditModal(btn) {\n    try {";
$content = str_replace($search, $replace, $content);

$search2 = "modal.classList.add('active');\n    }";
$replace2 = "modal.classList.add('active');\n    } catch (e) {\n        alert(\"Error in openEditModal: \" + e.message + \"\\nLine: \" + e.lineNumber);\n        console.error(e);\n    }\n}";
$content = str_replace($search2, $replace2, $content);
file_put_contents($file, $content);

