<?php
$file = "routes/web.php";
$content = file_get_contents($file);

// Extract paprika routes
preg_match("/\/\/ Paprika Routes.*?}\);/s", $content, $matches);
$paprika_routes = $matches[0];

// Remove paprika routes from its current place
$content = str_replace($paprika_routes, "", $content);

// Find the closing brace of `auth` middleware or `hydroponics` prefix.
// The structure is:
// Route::prefix('hydroponics')->group(function () {
//    ...
// });
// Route::prefix('konvensional')->group(function () {
//    ...
// });
// 
// So let's put paprika after `konvensional` or at the end before `auth` middleware closes.
$search = "}); // end auth middleware";
if (strpos($content, $search) !== false) {
    // If we have a clear comment
    $content = str_replace($search, $paprika_routes . "\n" . $search, $content);
} else {
    // Just append before the last }); in the file
    $content = preg_replace("/\}\);\s*$/", "\n    " . $paprika_routes . "\n});\n", $content);
}

file_put_contents($file, $content);

