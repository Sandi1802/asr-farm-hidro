with open('resources/views/hydroponics/greenhouse-detail.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_nutrisi = """                        <div style="display: flex; gap: 0.5rem; font-size: 0.85rem;">
                            <span style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;"><strong style="color: #475569;">PPM:</strong> <span style="color: #15803d; font-weight: 700;">{{ $rack->ppm_level ?? '-' }}</span></span>
                            <span style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;"><strong style="color: #475569;">pH:</strong> <span style="color: #1d4ed8; font-weight: 700;">{{ $rack->ph_level ?? '-' }}</span></span>
                        </div>"""

new_nutrisi = """                        <div style="display: flex; gap: 0.5rem; font-size: 0.85rem; flex-wrap: wrap;">
                            <span style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;"><strong style="color: #475569;">PPM:</strong> <span style="color: #15803d; font-weight: 700;">{{ $rack->ppm_level ?? '-' }}</span></span>
                            <span style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;"><strong style="color: #475569;">pH:</strong> <span style="color: #1d4ed8; font-weight: 700;">{{ $rack->ph_level ?? '-' }}</span></span>
                            <span style="background: #fff7ed; padding: 0.2rem 0.5rem; border-radius: 4px;"><strong style="color: #92400e;">🌡️</strong> <span style="color: #b45309; font-weight: 700;">{{ $rack->suhu ? $rack->suhu . '°C' : '-' }}</span></span>
                        </div>"""

content = content.replace(old_nutrisi, new_nutrisi)

with open('resources/views/hydroponics/greenhouse-detail.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched.")
