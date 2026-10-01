<?php
$f = 'resources/views/components/notifications.blade.php';
$c = file_get_contents($f);
$c = str_replace('@if($count > 0)', '@if($typeCount > 0)', $c);
file_put_contents($f, $c);
echo "Fixed count variable in template.\n";
