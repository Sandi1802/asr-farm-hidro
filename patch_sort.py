import re

with open('resources/views/hydroponics/greenhouses.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace arsort($rackGrouped); with uksort($rackGrouped, 'strnatcmp');
content = content.replace("arsort($rackGrouped);", "uksort($rackGrouped, 'strnatcmp');")

with open('resources/views/hydroponics/greenhouses.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched greenhouses.blade.php")

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content2 = f.read()

# Patch 1: emptyHolesGrouped
old_empty_sort = """            // Sort by quantity descending
            uasort($emptyHolesGrouped, function($a, $b) {
                return $b['qty'] <=> $a['qty'];
            });"""
new_empty_sort = """            // Sort by GH and Rack name naturally
            uksort($emptyHolesGrouped, 'strnatcmp');"""
content2 = content2.replace(old_empty_sort, new_empty_sort)

# Patch 2: locations in buildSiapPanenHtml
old_locations_build = """                    if (!isset($locations[$locKey])) {
                        $locations[$locKey] = ['count' => 0, 'ages' => [], 'catatan' => $catatan];
                    }
                    $locations[$locKey]['count']++;
                    $locations[$locKey]['ages'][] = $age;
                }
                
                $siapPanenHtml .= '<div style="margin-bottom:1.5rem; border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">';"""

new_locations_build = """                    if (!isset($locations[$locKey])) {
                        $locations[$locKey] = ['count' => 0, 'ages' => [], 'catatan' => $catatan];
                    }
                    $locations[$locKey]['count']++;
                    $locations[$locKey]['ages'][] = $age;
                }
                
                // Sort locations naturally
                uksort($locations, 'strnatcmp');
                
                $siapPanenHtml .= '<div style="margin-bottom:1.5rem; border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">';"""

content2 = content2.replace(old_locations_build, new_locations_build)

with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
    f.write(content2)

print("Patched HydroponicController.php")
