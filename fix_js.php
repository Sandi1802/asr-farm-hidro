<?php
$file = "resources/views/master-data/employees.blade.php";
$content = file_get_contents($file);
$content = preg_replace("/.*document\.getElementById\('edit_email'\)\.value.*/", "", $content);
file_put_contents($file, $content);

