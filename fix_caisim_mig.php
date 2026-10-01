<?php
$f = 'database/migrations/2026_10_01_081947_update_caisim_sawi_to_caisim.php';
$c = file_get_contents($f);

$old = <<<'EOF'
        DB::table('maintenance_logs')
            ->where('details', 'LIKE', '%"Caisim/Sawi"%')
            ->update([
                'details' => DB::raw("REPLACE(details, '\"Caisim/Sawi\"', '\"Caisim\"')")
            ]);
EOF;
$new = <<<'EOF'
        DB::table('maintenance_logs')
            ->whereRaw("details::text LIKE '%\"Caisim/Sawi\"%'")
            ->update([
                'details' => DB::raw("REPLACE(details::text, '\"Caisim/Sawi\"', '\"Caisim\"')::json")
            ]);
EOF;
$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "Fixed.\n";
