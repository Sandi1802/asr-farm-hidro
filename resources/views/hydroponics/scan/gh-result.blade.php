<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Greenhouse: {{ $greenhouse->name }}</title>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #16a34a;
            --primary-dark: #15803d;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-color); color: var(--text-main); line-height: 1.5; padding-bottom: 2rem; -webkit-tap-highlight-color: transparent; }
        
        /* App Bar */
        .app-bar {
            background: var(--primary);
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .app-bar h1 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-left: 0.75rem;
            flex: 1;
        }
        .back-btn { color: white; text-decoration: none; display: flex; align-items: center; font-size: 1.25rem; }

        .container { padding: 1rem; }

        /* GH Header Card */
        .gh-header {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
            text-align: center;
        }
        .gh-icon {
            width: 64px; height: 64px;
            background: #dcfce7;
            color: var(--primary);
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; margin-bottom: 0.75rem;
        }
        .gh-title { font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; }
        .gh-desc { font-size: 0.875rem; color: var(--text-muted); }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1rem;
            display: flex; flex-direction: column;
            border: 1px solid var(--border);
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .stat-val { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.25rem; }
        .stat-label { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        
        .c-ditanam { color: #15803d; }
        .c-ready { color: #ea580c; }
        .c-kosong { color: #475569; }
        .c-rusak { color: #dc2626; }

        /* Plants List */
        .section-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .plant-list {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .plant-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .plant-item:last-child { border-bottom: none; }
        .plant-name { font-weight: 600; font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem; }
        .plant-count { 
            background: #f3f4f6; color: #374151; 
            padding: 0.25rem 0.75rem; border-radius: 50px; 
            font-size: 0.8rem; font-weight: 700; 
        }

        .alert-box {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 12px;
            padding: 1rem;
            display: flex; gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .alert-icon { color: #ea580c; font-size: 1.5rem; }
        .alert-title { font-size: 0.9rem; font-weight: 700; color: #9a3412; }
        .alert-desc { font-size: 0.8rem; color: #c2410c; margin-top: 0.2rem; }
    </style>
</head>
<body>

    <div class="app-bar">
        <a href="javascript:history.back()" class="back-btn"><i class="ph ph-arrow-left"></i></a>
        <h1>Detail Greenhouse</h1>
        <i class="ph ph-house-line" style="font-size:1.25rem;"></i>
    </div>

    <div class="container">
        
        <div class="gh-header">
            <div class="gh-icon">
                <i class="ph ph-plant"></i>
            </div>
            <div class="gh-title">{{ $greenhouse->name }}</div>
            <div class="gh-desc">{{ $greenhouse->racks->count() }} Rak &bull; {{ $totalHoles }} Total Lubang</div>
        </div>

        @if($readyToHarvestCount > 0)
        <div class="alert-box">
            <div class="alert-icon"><i class="ph ph-warning-circle"></i></div>
            <div>
                <div class="alert-title">Perhatian Panen!</div>
                <div class="alert-desc">Ada <b>{{ $readyToHarvestCount }}</b> tanaman di GH ini yang sudah mencapai atau melewati usia panen (≥30 Hari).</div>
            </div>
        </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card" style="border-left: 4px solid #16a34a;">
                <div class="stat-val c-ditanam">{{ $plantedHoles }}</div>
                <div class="stat-label">Terisi Tanam</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #ea580c;">
                <div class="stat-val c-ready">{{ $readyToHarvestCount }}</div>
                <div class="stat-label">Siap Panen</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #94a3b8;">
                <div class="stat-val c-kosong">{{ $emptyHoles }}</div>
                <div class="stat-label">Kosong</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #dc2626;">
                <div class="stat-val c-rusak">{{ $damagedHoles }}</div>
                <div class="stat-label">Rusak</div>
            </div>
        </div>

        <h3 class="section-title"><i class="ph ph-list-bullets" style="color:var(--primary);"></i> Daftar Tanaman Saat Ini</h3>
        
        @if($plantsPlanted->count() > 0)
        <div class="plant-list">
            @foreach($plantsPlanted as $name => $count)
            <div class="plant-item">
                <div class="plant-name">🌱 {{ $name }}</div>
                <div class="plant-count">{{ $count }} lubang</div>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center; padding: 2rem; background:white; border-radius:12px; border:1px dashed #d1d5db; color: #9ca3af; font-size:0.9rem;">
            Belum ada tanaman yang sedang ditanam di Greenhouse ini.
        </div>
        @endif

    </div>

</body>
</html>
