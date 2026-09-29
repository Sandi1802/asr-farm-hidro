import re

with open('app/Http/Controllers/Api/GreenhouseApiController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_racks = "'racks' => $gh->racks->map(function($r) {"
new_racks = "'racks' => $gh->racks->sortBy('name', SORT_NATURAL)->values()->map(function($r) {"

content = content.replace(old_racks, new_racks)

with open('app/Http/Controllers/Api/GreenhouseApiController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched.")
