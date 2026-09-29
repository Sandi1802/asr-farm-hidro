import re

with open('resources/views/hydroponics/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add " LT" suffix to Sudah Panen, Siap Panen, Gagal Panen, Lubang Kosong cards

# Sudah Panen
content = content.replace(
    "'value' => number_format($harvestedHoles,0,',','.'),         'icon' => 'ph-basket'",
    "'value' => number_format($harvestedHoles,0,',','.') . ' LT', 'icon' => 'ph-basket'"
)

# Siap Panen
content = content.replace(
    "'value' => number_format($readyToHarvestCount,0,',','.'),    'icon' => 'ph-trophy'",
    "'value' => number_format($readyToHarvestCount,0,',','.') . ' LT', 'icon' => 'ph-trophy'"
)

# Gagal Panen
content = content.replace(
    "'value' => number_format($damagedHoles,0,',','.'),           'icon' => 'ph-warning'",
    "'value' => number_format($damagedHoles,0,',','.') . ' LT',  'icon' => 'ph-warning'"
)

# Lubang Kosong
content = content.replace(
    "'value' => number_format($emptyHolesCount,0,',','.'),        'icon' => 'ph-circle-dashed'",
    "'value' => number_format($emptyHolesCount,0,',','.') . ' LT', 'icon' => 'ph-circle-dashed'"
)

with open('resources/views/hydroponics/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched dashboard cards with LT suffix.")
