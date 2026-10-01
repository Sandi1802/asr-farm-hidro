<?php
$f = 'resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);
$c = str_replace('</style>', ".cal-scroll-wrapper::-webkit-scrollbar { height: 6px; }\n.cal-scroll-wrapper::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }\n</style>", $c);
file_put_contents($f, $c);
echo "Added CSS.\n";
