<?php

$file = 'app/Http/Controllers/KonvensionalController.php';
$content = file_get_contents($file);

// Replace the parsing of $plants to use array_unique
$target = <<<'PHP'
        foreach ($allTanam as $row) {
            $plants = explode(', ', $row->plant_name);
            foreach ($plants as $p) {
                $p = trim($p);
                if (empty($p)) continue;

                if (!isset($tanamCounts[$p])) $tanamCounts[$p] = 0;
                $tanamCounts[$p]++;

                if ($row->status === 'panen') {
                    if (!isset($panenCounts[$p])) $panenCounts[$p] = 0;
                    $panenCounts[$p]++;
                }
            }
        }
PHP;

$replacement = <<<'PHP'
        foreach ($allTanam as $row) {
            // Fix issue where duplicate plants in same hole were counted twice
            $plants = array_unique(array_filter(array_map('trim', explode(',', $row->plant_name))));
            foreach ($plants as $p) {
                if (!isset($tanamCounts[$p])) $tanamCounts[$p] = 0;
                $tanamCounts[$p]++;

                if ($row->status === 'panen') {
                    if (!isset($panenCounts[$p])) $panenCounts[$p] = 0;
                    $panenCounts[$p]++;
                }
            }
        }
PHP;

$content = str_replace(str_replace("\r\n", "\n", $target), str_replace("\r\n", "\n", $replacement), str_replace("\r\n", "\n", $content));
file_put_contents($file, $content);

echo "Fixed KonvensionalController.php\n";
