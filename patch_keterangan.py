import re

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("Konfirmasi Lapangan</th>", "Kondisi di lapangan</th>")

with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Replaced.")
