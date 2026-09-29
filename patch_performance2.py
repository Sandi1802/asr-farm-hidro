import re

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace plantedHolesGroup
old_planted = """            $plantedHolesGroup = \App\Models\Hole::where('status', 'ditanam')->whereNotNull('plant_name')->get()->groupBy('plant_name');
            $plantedHoles = \App\Models\Hole::where('status', 'ditanam')->count();
            $plantedTypesCount = $plantedHolesGroup->count();"""

new_planted = """            $plantedHoles = \App\Models\Hole::where('status', 'ditanam')->count();
            $plantedTypesCount = \App\Models\Hole::where('status', 'ditanam')->whereNotNull('plant_name')->distinct('plant_name')->count('plant_name');"""

content = content.replace(old_planted, new_planted)

with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Added fix for plantedTypesCount")
