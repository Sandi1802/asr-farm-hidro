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

        @if($countReady > 0)
        <div style="background: linear-gradient(135deg, #15803d, #16a34a); color: white; padding: 1rem 1.25rem; border-radius: 14px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; font-weight: 700;">Status Panen</div>
                <div style="font-size: 1.25rem; font-weight: 800; margin-top: 2px;">🌾 {{ $countReady }} Tanaman Siap Panen!</div>
                <div style="font-size: 0.8rem; opacity: 0.95; margin-top: 2px;">Terdapat kloter yang telah mencapai usia panen di rak ini.</div>
            </div>
            <span style="background: rgba(255,255,255,0.25); padding: 0.4rem 0.8rem; border-radius: 20px; font-weight: 800; font-size: 0.85rem;">Siap Panen</span>
        </div>
        @elseif($totalDitanam > 0)
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 0.85rem 1rem; border-radius: 12px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="ph ph-plant" style="font-size: 1.5rem; color: #16a34a;"></i>
            <div>
                <div style="font-weight: 700; font-size: 0.9rem;">Masa Pertumbuhan Aktif</div>
                <div style="font-size: 0.8rem; color: #15803d;">Tanaman sedang dalam masa pemeliharaan, belum ada yang siap panen hari ini.</div>
            </div>
        </div>
        @endif

        <div class="header-card" style="padding-top: 1rem;">
            <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.5rem;">Statistik Lubang Tanam</div>
            <div class="stats-grid">
                <div class="stat-mini">
                    <div class="val" style="color: #16a34a;">{{ $totalDitanam }}</div>
                    <div class="lbl">Ditanam</div>
                </div>
                <div class="stat-mini">
                    <div class="val" style="color: #ea580c;">{{ $countReady }}</div>
                    <div class="lbl">Siap Panen</div>
                </div>
                <div class="stat-mini">
                    <div class="val" style="color: #6b7280;">{{ $countKosong }}</div>
                    <div class="lbl">Kosong</div>
                </div>
                <div class="stat-mini">
                    <div class="val" style="color: #2563eb;">{{ $totalHoles }}</div>
                    <div class="lbl">Kapasitas</div>
                </div>
            </div>

            @if(isset($uniquePlantTypes) && $uniquePlantTypes->count() > 0)
            <div style="margin-top: 0.75rem; padding: 0.6rem 0.8rem; background: #f9fafb; border-radius: 8px; border: 1px solid var(--border); font-size: 0.85rem;">
                <span style="color: var(--text-muted); font-weight: 600;">Jenis Tanaman:</span> 
                <strong>{{ $uniquePlantTypes->implode(', ') }}</strong>
            </div>
            @endif
            
            <a href="{{ route('hydroponics.racks.show', $rack->id) }}" class="action-btn" style="background: #2563eb; margin-top:1.25rem;">
                <i class="ph ph-table"></i> Manajemen Lubang Detail (Desktop View)
            </a>
        </div>

        <h3 class="section-title"><i class="ph ph-calendar-check" style="color:#16a34a;"></i> Kloter Tanam (HST & Tanggal)</h3>
        
        @if(isset($batches) && $batches->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @foreach($batches as $idx => $batch)
            <div style="background: white; border-radius: 12px; border: 1px solid {{ $batch['is_ready'] ? '#86efac' : 'var(--border)' }}; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04); {{ $batch['is_ready'] ? 'border-left: 5px solid #16a34a;' : '' }}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-weight: 700; font-size: 1rem; color: #111827; display: flex; align-items: center; gap: 0.4rem;">
                            🌱 {{ $batch['plant_name'] }} 
                            <span style="font-size: 0.75rem; font-weight: 600; color: #6b7280;">(Kloter {{ $idx + 1 }})</span>
                        </div>
                        <div style="font-size: 0.8rem; color: #4b5563; margin-top: 3px;">
                            📅 Tanam: <strong>{{ $batch['planted_at_formatted'] }}</strong>
                        </div>
                    </div>
                    @if($batch['is_ready'])
                        <span class="badge badge-ready" style="font-size: 0.75rem;">🌾 Siap Panen</span>
                    @else
                        <span class="badge badge-ditanam" style="font-size: 0.75rem;">🌱 {{ $batch['hst'] }} HST</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed #e5e7eb; font-size: 0.8rem;">
                    <div>
                        <span style="color: #6b7280;">Jumlah Tanaman:</span><br>
                        <strong style="color: #111827;">{{ $batch['count'] }} Lubang</strong>
                    </div>
                    <div>
                        <span style="color: #6b7280;">Umur Tanaman (HST):</span><br>
                        <strong style="color: #15803d;">{{ $batch['hst'] }} Hari</strong> 
                        <span style="color: #9ca3af; font-size: 0.75rem;">/ {{ $batch['growth_days'] }} hr</span>
                    </div>
                </div>

                <div style="margin-top: 0.6rem; background: #e5e7eb; border-radius: 999px; height: 6px; overflow: hidden;">
                    <div style="background: {{ $batch['is_ready'] ? '#16a34a' : '#2563eb' }}; width: {{ $batch['progress_percent'] }}%; height: 100%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #6b7280; margin-top: 3px;">
                    <span>Target: {{ $batch['growth_days'] }} Hari</span>
                    @if($batch['is_ready'])
                        <span style="color: #15803d; font-weight: 700;">Tercapai (Siap Panen)</span>
                    @else
                        <span>Sisa {{ $batch['days_remaining'] }} hari lagi</span>
                    @endif
                </div>

                @if($batch['estimated_harvest_at'])
                <div style="font-size: 0.75rem; color: #4b5563; margin-top: 0.5rem; background: #f9fafb; padding: 0.4rem 0.6rem; border-radius: 6px;">
                    🎯 Estimasi Panen: <strong>{{ $batch['estimated_harvest_at'] }}</strong>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center; padding: 1.5rem; background:white; border-radius:12px; border:1px dashed #d1d5db; color: #9ca3af; font-size:0.85rem;">
            Rak ini masih kosong (belum ada tanaman yang ditanam).
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
