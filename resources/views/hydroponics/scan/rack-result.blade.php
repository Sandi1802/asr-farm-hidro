<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rak: {{ $rack->name }}</title>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f59e0b; /* Using amber/yellow for rack to differentiate from GH green */
            --primary-dark: #d97706;
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
            background: #111827;
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

        /* Header Card */
        .header-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
        }
        .rack-title { font-size: 1.5rem; font-weight: 800; color: var(--text-main); }
        .rack-subtitle { font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem; }

        /* Sensor Grid */
        .sensor-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        .sensor-box {
            border-radius: 12px;
            padding: 0.75rem;
            text-align: center;
        }
        .sensor-box.ppm { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .sensor-box.ph { background: #eff6ff; border: 1px solid #bfdbfe; }
        
        .sensor-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .sensor-box.ppm .sensor-label { color: #16a34a; }
        .sensor-box.ph .sensor-label { color: #2563eb; }
        
        .sensor-val { font-size: 1.5rem; font-weight: 800; line-height: 1.2; margin-top: 0.2rem; }
        .sensor-box.ppm .sensor-val { color: #15803d; }
        .sensor-box.ph .sensor-val { color: #1d4ed8; }

        .action-btn {
            display: block; width: 100%; text-align: center;
            background: #111827; color: white;
            padding: 0.75rem; border-radius: 10px;
            font-weight: 600; font-size: 0.9rem; text-decoration: none;
            margin-top: 1rem; border: none; cursor: pointer;
        }
        .action-btn.secondary {
            background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;
        }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-top: 1rem; }
        .stat-mini { text-align: center; background: #f9fafb; padding: 0.5rem; border-radius: 8px; border: 1px solid var(--border); }
        .stat-mini .val { font-size: 1.1rem; font-weight: 800; }
        .stat-mini .lbl { font-size: 0.65rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }

        /* Plants List */
        .section-title { font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem; margin-top: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .plant-list { background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        .plant-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid var(--border); }
        .plant-item:last-child { border-bottom: none; }
        .plant-name { font-weight: 600; font-size: 0.9rem; }
        
        .badge { padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .badge-ready { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }
        .badge-ditanam { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }

        /* Modals */
        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); z-index: 100;
            display: none; align-items: center; justify-content: center;
            padding: 1rem;
        }
        .modal-content {
            background: white; width: 100%; border-radius: 16px; padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .modal-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151; }
        .form-input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; font-weight: 600; }
        .btn-flex { display: flex; gap: 0.75rem; margin-top: 1.5rem; }
        .btn-flex button { flex: 1; padding: 0.75rem; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; }
        .btn-cancel { background: #f3f4f6; color: #374151; }
        .btn-save { background: var(--primary); color: white; }

        /* Alert message */
        .alert-success {
            background: #dcfce7; color: #15803d; border-left: 4px solid #16a34a;
            padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 500;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <div class="app-bar">
        <a href="javascript:history.back()" class="back-btn"><i class="ph ph-arrow-left"></i></a>
        <h1>Monitoring Rak</h1>
        <i class="ph ph-squares-four" style="font-size:1.25rem;"></i>
    </div>

    <div class="container">
        
        @if(session('success'))
        <div class="alert-success"><i class="ph ph-check-circle"></i> {{ session('success') }}</div>
        @endif

        <div class="header-card">
            <div class="rack-title">{{ $rack->name }}</div>
            <div class="rack-subtitle">{{ $rack->greenhouse->name ?? 'GH' }}</div>
            
            <div class="sensor-grid">
                <div class="sensor-box ppm">
                    <div class="sensor-label">PPM Level</div>
                    <div class="sensor-val">{{ $rack->ppm_level ?? '—' }}</div>
                </div>
                <div class="sensor-box ph">
                    <div class="sensor-label">pH Level</div>
                    <div class="sensor-val">{{ $rack->ph_level ?? '—' }}</div>
                </div>
            </div>
            <div style="font-size: 0.7rem; color: #9ca3af; text-align: center; margin-top: 0.5rem;">
                Update: {{ $rack->ppm_ph_updated_at ? \Carbon\Carbon::parse($rack->ppm_ph_updated_at)->diffForHumans() : 'Belum ada' }}
            </div>

            <button class="action-btn" onclick="document.getElementById('sensorModal').style.display='flex'">
                <i class="ph ph-pencil-simple"></i> Update PPM & pH
            </button>
            <form action="{{ route('hydroponics.racks.drain', $rack->id) }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="action-btn secondary" onclick="return confirm('Kuras air rak ini?')">
                    <i class="ph ph-drop"></i> Kuras Air Rak
                </button>
            </form>
        </div>

        <div class="header-card" style="padding-top: 1rem;">
            <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.5rem;">Statistik Lubang Tanam</div>
            <div class="stats-grid">
                <div class="stat-mini">
                    <div class="val" style="color: #16a34a;">{{ $totalDitanam }}</div>
                    <div class="lbl">Tanam</div>
                </div>
                <div class="stat-mini">
                    <div class="val" style="color: #ea580c;">{{ $countReady }}</div>
                    <div class="lbl">Siap</div>
                </div>
                <div class="stat-mini">
                    <div class="val" style="color: #2563eb;">{{ $countPanen }}</div>
                    <div class="lbl">Panen</div>
                </div>
                <div class="stat-mini">
                    <div class="val" style="color: #dc2626;">{{ $countRusak }}</div>
                    <div class="lbl">Rusak</div>
                </div>
            </div>
            
            <a href="{{ route('hydroponics.racks.show', $rack->id) }}" class="action-btn" style="background: #2563eb; margin-top:1.25rem;">
                <i class="ph ph-table"></i> Manajemen Lubang Detail (Desktop View)
            </a>
        </div>

        <h3 class="section-title"><i class="ph ph-plant" style="color:#16a34a;"></i> Tanaman di Rak Ini</h3>
        
        @if($plantsPlanted->count() > 0)
        <div class="plant-list">
            @foreach($plantsPlanted as $name => $count)
            @php 
                $ready = $plantsReadyToHarvest[$name] ?? 0;
            @endphp
            <div class="plant-item">
                <div class="plant-name">🌱 {{ $name }}</div>
                <div style="display:flex; gap:0.4rem;">
                    @if($ready > 0)
                        <span class="badge badge-ready">{{ $ready }} Siap Panen</span>
                    @endif
                    <span class="badge badge-ditanam">{{ $count }} Total</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center; padding: 1.5rem; background:white; border-radius:12px; border:1px dashed #d1d5db; color: #9ca3af; font-size:0.85rem;">
            Rak ini masih kosong.
        </div>
        @endif

    </div>

    <!-- Modal Update Sensor -->
    <div id="sensorModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="modal-title">Update Sensor Rak</h3>
            <form action="{{ route('hydroponics.racks.updatePpmPh', $rack->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">PPM Level (800 - 2000)</label>
                    <input type="number" step="1" name="ppm_level" value="{{ $rack->ppm_level }}" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">pH Level (5.5 - 6.5)</label>
                    <input type="number" step="0.1" name="ph_level" value="{{ $rack->ph_level }}" class="form-input" required>
                </div>
                <div class="btn-flex">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('sensorModal').style.display='none'">Batal</button>
                    <button type="submit" class="btn-save">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
