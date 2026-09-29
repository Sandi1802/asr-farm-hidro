import re

with open('resources/views/hydroponics/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_html_list = """                <ul id="sudahPanenModalList" style="list-style: none; padding: 0; margin: 0;">
                    @if(empty($harvestedByPlant))
                        <div style="text-align:center; padding:2rem; color:var(--text-muted);">
                            Belum ada data panen bulan ini.
                        </div>
                    @else
                        @foreach($harvestedByPlant as $plant => $data)
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
                        @endforeach
                    @endif
                </ul>"""

new_html_table = """                <div id="sudahPanenModalContainer">
                    @if(empty($harvestedByPlant))
                        <div style="text-align:center; padding:2rem; color:var(--text-muted);">
                            Belum ada data panen bulan ini.
                        </div>
                    @else
                        <div style="border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">
                            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                                <thead>
                                    <tr style="background:var(--bg-light); border-bottom:1px solid var(--border-color); color:var(--text-muted);">
                                        <th style="padding:0.75rem 1rem; text-align:left; font-weight:600;">Nama Tanaman</th>
                                        <th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Hari Ini</th>
                                        <th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Kemarin</th>
                                        <th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Total Bulan Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($harvestedByPlant as $plant => $data)
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td style="padding:0.75rem 1rem; font-weight:600; color:var(--text-main);">{{ $plant }}</td>
                                            <td style="padding:0.75rem 1rem; text-align:center; color:var(--text-main);">{{ $data['today'] }}</td>
                                            <td style="padding:0.75rem 1rem; text-align:center; color:var(--text-main);">{{ $data['yesterday'] }}</td>
                                            <td style="padding:0.75rem 1rem; text-align:center; font-weight:700; color:#0f766e;">{{ $data['month'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>"""

old_js_list = """                let listSudah = document.getElementById('sudahPanenModalList');
                if (listSudah && data.sudah_panen_detail) {
                    listSudah.innerHTML = '';
                    if (Object.keys(data.sudah_panen_detail).length === 0) {
                        listSudah.innerHTML = '<div style="text-align:center; padding:2rem; color:var(--text-muted);">Belum ada data panen.</div>';
                    } else {
                        for (const [plant, pData] of Object.entries(data.sudah_panen_detail)) {
                            listSudah.innerHTML += '<li style="padding: 1rem; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">' +
                                '<div><div style="font-weight:600; color:var(--text-main);">' + plant + '</div></div>' +
                                '<div style="text-align:right; font-size:0.85rem; color:var(--text-muted);">' +
                                    '<div><span style="display:inline-block; width:60px; text-align:left;">Hari ini</span>: <strong style="color:var(--text-main);">' + pData.today + '</strong></div>' +
                                    '<div><span style="display:inline-block; width:60px; text-align:left;">Kemarin</span>: <strong style="color:var(--text-main);">' + pData.yesterday + '</strong></div>' +
                                    '<div style="margin-top:4px; font-weight:bold; color:#0f766e;"><span style="display:inline-block; width:60px; text-align:left;">Bulan ini</span>: ' + pData.month + ' Lubang</div>' +
                                '</div>' +
                                '</li>';
                        }
                    }
                }"""

new_js_table = """                let listSudah = document.getElementById('sudahPanenModalContainer');
                if (listSudah && data.sudah_panen_detail) {
                    listSudah.innerHTML = '';
                    if (Object.keys(data.sudah_panen_detail).length === 0) {
                        listSudah.innerHTML = '<div style="text-align:center; padding:2rem; color:var(--text-muted);">Belum ada data panen.</div>';
                    } else {
                        let tableHtml = '<div style="border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">' +
                            '<table style="width:100%; border-collapse:collapse; font-size:0.9rem;">' +
                            '<thead><tr style="background:var(--bg-light); border-bottom:1px solid var(--border-color); color:var(--text-muted);">' +
                            '<th style="padding:0.75rem 1rem; text-align:left; font-weight:600;">Nama Tanaman</th>' +
                            '<th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Hari Ini</th>' +
                            '<th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Kemarin</th>' +
                            '<th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Total Bulan Ini</th>' +
                            '</tr></thead><tbody>';
                        for (const [plant, pData] of Object.entries(data.sudah_panen_detail)) {
                            tableHtml += '<tr style="border-bottom: 1px solid var(--border-color);">' +
                                '<td style="padding:0.75rem 1rem; font-weight:600; color:var(--text-main);">' + plant + '</td>' +
                                '<td style="padding:0.75rem 1rem; text-align:center; color:var(--text-main);">' + pData.today + '</td>' +
                                '<td style="padding:0.75rem 1rem; text-align:center; color:var(--text-main);">' + pData.yesterday + '</td>' +
                                '<td style="padding:0.75rem 1rem; text-align:center; font-weight:700; color:#0f766e;">' + pData.month + '</td>' +
                                '</tr>';
                        }
                        tableHtml += '</tbody></table></div>';
                        listSudah.innerHTML = tableHtml;
                    }
                }"""

content = content.replace(old_html_list, new_html_table)
content = content.replace(old_js_list, new_js_table)

with open('resources/views/hydroponics/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Dashboard blade patched to use table format.")
