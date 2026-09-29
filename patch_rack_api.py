import re

with open('app/Http/Controllers/Api/RackApiController.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "$racks = Rack::with(['greenhouse', 'rows.holes'])->get();",
    "$racks = Rack::with(['greenhouse', 'rows.holes'])->get()->sortBy('name', SORT_NATURAL)->values();"
)

with open('app/Http/Controllers/Api/RackApiController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched RackApiController.")
