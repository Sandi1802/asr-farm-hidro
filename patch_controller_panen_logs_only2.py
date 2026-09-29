import re

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content = f.read()

pattern = re.compile(r"^[ \t]*\$holeHarvested = \\App\\Models\\Hole::whereNotNull\('plant_name'\).*?\} elseif \(\$date === \$yesterday\) \{\s*\$harvestedTotals\[\$pName\]\['yesterday'\]\+\+;\s*\}\s*\}", re.MULTILINE | re.DOTALL)

content = re.sub(pattern, "", content)

with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("All Hole aggregations removed.")
