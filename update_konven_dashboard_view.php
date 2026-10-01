<?php
$f = 'resources/views/konvensional/dashboard.blade.php';
$c = file_get_contents($f);

$newGraphsHtml = <<<'EOF'
    {{-- GRAFIK TOP TANAMAN --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem; margin-bottom:1rem;">
        
        {{-- Tanaman Paling Sering Ditanam --}}
        <div class="chart-card" style="position: relative;">
            <div class="chart-export-wrapper">
                <button class="chart-export-btn" onclick="toggleChartMenu(this)"><i class="ph ph-list"></i></button>
                <div class="chart-export-dropdown">
                    <a class="chart-export-item" onclick="exportChart('topTanamChart', 'fullscreen')">Lihat layar penuh</a>
                    <a class="chart-export-item" onclick="exportChart('topTanamChart', 'print')">Cetak grafik</a>
                </div>
            </div>
            <h3 class="chart-card-title" style="padding-right: 30px;"><i class="ph ph-plant" style="color:var(--asr-green);font-size:1.2rem;"></i> Tanaman Paling Sering Ditanam <span style="font-size:0.7rem; background:#dcfce7; color:#166534; padding:2px 6px; border-radius:4px; margin-left:8px;">Top 8</span></h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="topTanamChart"></canvas>
            </div>
        </div>

        {{-- Tanaman Paling Sering Dipanen --}}
        <div class="chart-card" style="position: relative;">
            <div class="chart-export-wrapper">
                <button class="chart-export-btn" onclick="toggleChartMenu(this)"><i class="ph ph-list"></i></button>
                <div class="chart-export-dropdown">
                    <a class="chart-export-item" onclick="exportChart('topPanenChart', 'fullscreen')">Lihat layar penuh</a>
                    <a class="chart-export-item" onclick="exportChart('topPanenChart', 'print')">Cetak grafik</a>
                </div>
            </div>
            <h3 class="chart-card-title" style="padding-right: 30px;"><i class="ph ph-basket" style="color:#f59e0b;font-size:1.2rem;"></i> Tanaman Paling Sering Dipanen <span style="font-size:0.7rem; background:#fef3c7; color:#b45309; padding:2px 6px; border-radius:4px; margin-left:8px;">Top 8</span></h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="topPanenChart"></canvas>
            </div>
        </div>

    </div>

    {{-- GRAFIK SECTION (2 KOLOM) --}}
EOF;

$c = str_replace('{{-- GRAFIK SECTION (2 KOLOM) --}}', $newGraphsHtml, $c);

// Add Chart JS code
$newJs = <<<'EOF'
    // Chart Top Tanam
    const ctxTopTanam = document.getElementById('topTanamChart').getContext('2d');
    const topTanamData = @json($chartTopTanam);
    new Chart(ctxTopTanam, {
        type: 'bar',
        data: {
            labels: topTanamData.labels,
            datasets: [{
                label: 'Total Pohon Ditanam',
                data: topTanamData.data,
                backgroundColor: '#16a34a', // ASR Green
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y', // Horizontal bar chart
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { borderDash: [2,4] } }, y: { grid: { display: false } } }
        }
    });

    // Chart Top Panen
    const ctxTopPanen = document.getElementById('topPanenChart').getContext('2d');
    const topPanenData = @json($chartTopPanen);
    new Chart(ctxTopPanen, {
        type: 'bar',
        data: {
            labels: topPanenData.labels,
            datasets: [{
                label: 'Total Pohon Dipanen',
                data: topPanenData.data,
                backgroundColor: '#f59e0b', // Amber
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y', // Horizontal bar chart
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { borderDash: [2,4] } }, y: { grid: { display: false } } }
        }
    });

    const ctxKeterisian = document.getElementById('keterisianChart').getContext('2d');
EOF;

$c = str_replace("const ctxKeterisian = document.getElementById('keterisianChart').getContext('2d');", $newJs, $c);

file_put_contents($f, $c);
echo "View updated with charts.\n";
