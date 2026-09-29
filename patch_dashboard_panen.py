import re

with open('resources/views/hydroponics/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_blade_list = """                        @foreach($harvestedByPlant as $plant => $qty)
                            <li style="padding: 1rem; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <div style="font-weight:600; color:var(--text-main);">{{ $plant }}</div>
                                </div>
                                <div style="background:var(--bg-hover); padding: 0.3rem 0.8rem; border-radius: 20px; font-weight:700; font-size:0.9rem; color:#0f766e;">
                                    {{ $qty }} Lubang
                                </div>
                            </li>
                        @endforeach"""

new_blade_list = """                        @foreach($harvestedByPlant as $plant => $data)
                            <li style="padding: 1rem; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <div style="font-weight:600; color:var(--text-main);">{{ $plant }}</div>
                                </div>
                                <div style="text-align:right; font-size:0.85rem; color:var(--text-muted);">
                                    <div><span style="display:inline-block; width:60px; text-align:left;">Hari ini</span>: <strong style="color:var(--text-main);">{{ $data['today'] }}</strong></div>
                                    <div><span style="display:inline-block; width:60px; text-align:left;">Kemarin</span>: <strong style="color:var(--text-main);">{{ $data['yesterday'] }}</strong></div>
                                    <div style="margin-top:4px; font-weight:bold; color:#0f766e;"><span style="display:inline-block; width:60px; text-align:left;">Bulan ini</span>: {{ $data['month'] }} Lubang</div>
                                </div>
                            </li>
                        @endforeach"""


old_js_list = """                        for (const [plant, qty] of Object.entries(data.sudah_panen_detail)) {
                            listSudah.innerHTML += '<li style="padding: 1rem; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">' +
                                '<div><div style="font-weight:600; color:var(--text-main);">' + plant + '</div></div>' +
                                '<div style="background:var(--bg-hover); padding: 0.3rem 0.8rem; border-radius: 20px; font-weight:700; font-size:0.9rem; color:#0f766e;">' + qty + ' Lubang</div>' +
                                '</li>';
                        }"""

new_js_list = """                        for (const [plant, pData] of Object.entries(data.sudah_panen_detail)) {
                            listSudah.innerHTML += '<li style="padding: 1rem; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">' +
                                '<div><div style="font-weight:600; color:var(--text-main);">' + plant + '</div></div>' +
                                '<div style="text-align:right; font-size:0.85rem; color:var(--text-muted);">' +
                                    '<div><span style="display:inline-block; width:60px; text-align:left;">Hari ini</span>: <strong style="color:var(--text-main);">' + pData.today + '</strong></div>' +
                                    '<div><span style="display:inline-block; width:60px; text-align:left;">Kemarin</span>: <strong style="color:var(--text-main);">' + pData.yesterday + '</strong></div>' +
                                    '<div style="margin-top:4px; font-weight:bold; color:#0f766e;"><span style="display:inline-block; width:60px; text-align:left;">Bulan ini</span>: ' + pData.month + ' Lubang</div>' +
                                '</div>' +
                                '</li>';
                        }"""


content = content.replace(old_blade_list, new_blade_list)
content = content.replace(old_js_list, new_js_list)

with open('resources/views/hydroponics/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Dashboard blade patched.")
