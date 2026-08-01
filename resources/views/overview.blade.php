@extends('layouts.app')

@section('title', 'Project Overview')

@section('content')
    <div class="project-hero">
        <div>
            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">Project Profile</div>
            <h1>Refurbishment KRI Tipe FPB - TRAK/505/PLN/IX/2022/AL</h1>
        </div>
        <div>
            <button class="btn btn-outline" onclick="document.getElementById('consortiumModal').classList.add('active')"
                style="color: white; border-color: rgba(255,255,255,0.4); display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="ph ph-share-network"></i> Structure Consortium
            </button>
        </div>
    </div>

    <div class="overview-grid">
        <!-- Project Manager -->
        <div class="col-span-12 card" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; align-items: center;">
            <div class="flex-start">
                <img src="https://ui-avatars.com/api/?name=Reindy+Dwien&background=E31837&color=fff" alt="PM" style="width: 50px; height: 50px; border-radius: 50%;">
                <div>
                    <div class="stat-label">Project Manager</div>
                    <div style="font-weight: 600; font-size: 1.1rem;">Reindy Dwien Suchendar, S.T., PMP®</div>
                </div>
            </div>

            <div class="insight-card card" style="padding: 1rem; border-radius: var(--radius-md); box-shadow: none; display: flex; align-items: center; gap: 1rem;">
                <div class="icon-box white">
                    <i class="ph ph-money"></i>
                </div>
                <div>
                    <div class="stat-label light">Project Value <i class="ph ph-eye-slash"></i></div>
                    <div class="stat-value" style="font-size: 1.25rem;">••••••••</div>
                </div>
            </div>

            <div class="card" style="padding: 1rem; border-radius: var(--radius-md); box-shadow: none; background: var(--bg-color); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1rem;">
                <div class="progress-circle">
                    <div class="progress-text">42%</div>
                </div>
                <div>
                    <div class="stat-label">Project Progress</div>
                    <div class="stat-value" style="font-size: 1.25rem; margin:0;">42.97%</div>
                    <div style="font-size: 0.75rem; color: var(--success);">Completed</div>
                </div>
            </div>
        </div>

        <!-- Project Scope -->
        <div class="col-span-6 card">
            <div class="flex-start" style="margin-bottom: 1rem;">
                <div class="icon-box blue" style="width: 40px; height: 40px; font-size: 1.25rem;">
                    <i class="ph ph-scan"></i>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600;">Project Scope</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <ul class="issue-list" style="margin: 0; padding: 0;">
                    <li>CMS</li>
                    <li>NDDU</li>
                </ul>
                <ul class="issue-list" style="margin: 0; padding: 0;">
                    <li>Tactical Data Link</li>
                    <li>Intercom</li>
                </ul>
                <ul class="issue-list" style="margin: 0; padding: 0;">
                    <li>EO FCS</li>
                    <li>EO Sensor</li>
                    <li>Gyro INS</li>
                </ul>
            </div>
        </div>

        <!-- Vessels -->
        <div class="col-span-6 card">
            <div class="flex-start" style="margin-bottom: 1rem;">
                <div class="icon-box blue" style="width: 40px; height: 40px; font-size: 1.25rem;">
                    <i class="ph ph-boat"></i>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600;">Vessels (4 Kapal)</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <ul class="vessel-list" style="margin: 0; padding: 0;">
                    <li><i class="ph-fill ph-boat text-blue"></i> Hiu-Todak</li>
                    <li><i class="ph-fill ph-boat text-blue"></i> Lemadang-Layang</li>
                </ul>
                <ul class="vessel-list" style="margin: 0; padding: 0;">
                    <li><i class="ph-fill ph-boat text-green"></i> Sura-Pandrong</li>
                    <li><i class="ph-fill ph-boat text-green"></i> Ajak-Singa</li>
                </ul>
                <ul class="vessel-list" style="margin: 0; padding: 0;">
                    <li><i class="ph-fill ph-boat text-red"></i> Kakap-Tongkol</li>
                    <li><i class="ph-fill ph-boat text-red"></i> Kerapu-Barakuda</li>
                </ul>
            </div>
        </div>


        <!-- High Level Issue -->
        <div class="col-span-12 card">
            <div class="flex-between" style="margin-bottom: 1rem;">
                <div class="flex-start">
                    <div class="icon-box red" style="width: 40px; height: 40px; font-size: 1.25rem;">
                        <i class="ph ph-warning-circle"></i>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 600;">High Level Issue</h3>
                </div>
                <a href="/logs/issue" style="font-size: 0.8rem; color: var(--text-muted); text-decoration: none;">View all <i class="ph ph-arrow-up-right"></i></a>
            </div>

            <ul class="mini-sticky-grid">
                <li class="mini-sticky red-sticky pinned" style="transform: rotate(-1deg);">
                    <div class="mini-sticky-header">
                        <i class="ph ph-warning-circle" style="color: var(--asr-green);"></i> Critical
                    </div>
                    Keterlambatan Material Pengembangan CMS Phase 1 Eltran
                </li>
                <li class="mini-sticky red-sticky pinned" style="transform: rotate(1.5deg);">
                    <div class="mini-sticky-header">
                        <i class="ph ph-warning-circle" style="color: var(--asr-green);"></i> Critical
                    </div>
                    Kesepahaman Dokumen Kontrak
                </li>
                <li class="mini-sticky red-sticky pinned" style="transform: rotate(-0.5deg);">
                    <div class="mini-sticky-header">
                        <i class="ph ph-warning-circle" style="color: var(--asr-green);"></i> Critical
                    </div>
                    Manajemen Proyek PT PAL
                </li>
                <li class="mini-sticky yellow-sticky pinned" style="transform: rotate(1deg);">
                    <div class="mini-sticky-header">
                        <i class="ph ph-warning" style="color: #D97706;"></i> Warning
                    </div>
                    Ketersediaan Dokumen
                </li>
                <li class="mini-sticky yellow-sticky pinned" style="transform: rotate(-1.5deg);">
                    <div class="mini-sticky-header">
                        <i class="ph ph-warning" style="color: #D97706;"></i> Warning
                    </div>
                    Dashboard Project Konsorsium
                </li>
            </ul>
        </div>


        <!-- AI Analysis Section -->
        <div class="col-span-12 card executive-card" style="border-left: 4px solid var(--asr-green);">
            <div class="flex-start" style="margin-bottom: 1rem;">
                <div class="icon-box red" style="width: 40px; height: 40px; font-size: 1.25rem;">
                    <i class="ph ph-brain"></i>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 600;">Executive Analysis Insight</h3>
            </div>
            <div style="font-size: 0.95rem; line-height: 1.6; color: var(--text-main);">
                <p style="margin-bottom: 0.75rem;">Berdasarkan data *Project Overview* saat ini, proyek Refurbishment KRI berada pada tingkat penyelesaian <strong>42.97%</strong>. Meskipun progres berjalan cukup baik, terdapat beberapa indikator risiko (High Level Issues) yang membutuhkan perhatian segera dari manajemen eksekutif:</p>
                <ol style="margin-left: 1.5rem; margin-bottom: 0.75rem; color: var(--text-muted);">
                    <li style="margin-bottom: 0.5rem;"><strong>Keterlambatan Material (CMS Phase 1 Eltran):</strong> Keterlambatan pasokan komponen kritis dapat memberikan efek domino pada fase instalasi dan integrasi (dijadwalkan Nov '27 - Mar '28). Diperlukan eskalasi kepada pihak *Principal* untuk mempercepat *delivery*.</li>
                    <li style="margin-bottom: 0.5rem;"><strong>Manajemen Proyek Konsorsium (PT PAL):</strong> Terdapat tantangan sinkronisasi dengan PT PAL (Lead Konsorsium). Disarankan mengadakan *alignment meeting* level direksi untuk mempertegas kesepahaman dokumen kontrak dan *Scope of Work* (SoW).</li>
                </ol>
                <p><strong>Rekomendasi Strategis:</strong> Fokuskan sumber daya pada penyelesaian dokumen kontrak yang tertunda. Pastikan seluruh 4 kapal (Hiu-Todak, dll.) mendapat prioritas penjadwalan *dry-dock* yang tersinkronisasi agar masa garansi 1 tahun pasca *SAT* (Agustus 2028) dapat dimanfaatkan dengan maksimal tanpa ada *downtime* tambahan.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- Consortium Modal -->
<div id="consortiumModal" class="modal-overlay" style="display:none; align-items:flex-start; padding:1.5rem; overflow-y:auto; background:rgba(0,0,0,0.78); backdrop-filter:blur(8px);">
    <div style="background:var(--card-bg); border-radius:1.25rem; width:100%; max-width:1280px; margin:auto; box-shadow:0 30px 70px rgba(0,0,0,0.5); overflow:hidden; border:1px solid var(--border-color);">

        <!-- Header -->
        <div style="background:linear-gradient(135deg,#8B0000,#C80A22,#16a34a); padding:1.5rem 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div style="color:rgba(255,255,255,0.65); font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.12em; margin-bottom:0.3rem;"><i class="ph ph-share-network"></i>&nbsp; Organizational Structure</div>
                <h2 style="color:#fff; font-size:1.4rem; font-weight:700; margin:0; letter-spacing:-0.01em;">Consortium Structure — KRI FPB Refurbishment</h2>
                <div style="color:rgba(255,255,255,0.6); font-size:0.82rem; margin-top:0.3rem;">KSO Non-Administratif &middot; PT LEN Industri (Persero) + PT PAL Indonesia (Persero)</div>
            </div>
            <button id="closeConsortiumBtn"
                style="background:rgba(255,255,255,0.12); border:1.5px solid rgba(255,255,255,0.25); color:#fff; width:40px; height:40px; border-radius:50%; cursor:pointer; font-size:1.1rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:all .2s;">
                <i class="ph ph-x"></i>
            </button>
        </div>

        <!-- Chart Container -->
        <div style="padding:2rem 2rem 2.5rem; overflow-x:auto; background:var(--card-bg);">

<style>
/* ═══════════════════════════════════════════
   Consortium Org Chart — CSS Tree System
   ═══════════════════════════════════════════ */

/* Base card */
.oc-card {
    border-radius: 14px;
    padding: 0.8rem 1rem;
    text-align: center;
    border: 2px solid var(--border-color);
    background: var(--card-bg);
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
    cursor: default;
    min-width: 115px;
    white-space: nowrap;
}
.oc-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(22,163,74,0.18); }

/* Card variants */
.oc-card.gov   { border-color: #C09060; background: #FFF8F0; }
.oc-card.kso   { border: 2.5px dashed var(--asr-green); background: rgba(22,163,74,0.05); }
.oc-card.main  { border-color: var(--asr-green); border-width: 2.5px; background: rgba(22,163,74,0.06); }
.oc-card.pal   { border-color: #2563EB; background: rgba(37,99,235,0.05); }
.oc-card.sub1  { background: var(--card-bg); }
.oc-card.sub2  { background: var(--card-bg); }

/* Dark mode */
:root[data-theme="dark"] .oc-card.gov  { background: rgba(160,82,45,0.12); }
:root[data-theme="dark"] .oc-card.kso  { background: rgba(22,163,74,0.08); }
:root[data-theme="dark"] .oc-card.main { background: rgba(22,163,74,0.10); }
:root[data-theme="dark"] .oc-card.pal  { background: rgba(37,99,235,0.12); }

.oc-title { font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin: 0.25rem 0 0.15rem; line-height: 1.3; }
.oc-hint  { font-size: 0.65rem; color: var(--text-muted); }
.oc-badge {
    display: inline-block;
    font-size: 0.58rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.06em; padding: 0.18rem 0.55rem;
    border-radius: 20px; margin: 0.2rem 0 0;
}
.oc-badge.gov-b  { background: #FEF3C7; color: #92400E; }
:root[data-theme="dark"] .oc-badge.gov-b { background:rgba(245,158,11,0.18); color:#FCD34D; }
.oc-badge.main-b { background: var(--asr-green); color: #fff; }
.oc-badge.pal-b  { background: rgba(37,99,235,0.14); color: #2563EB; }
:root[data-theme="dark"] .oc-badge.pal-b { background:rgba(96,165,250,0.18); color:#93C5FD; }

/* Emblem/avatar circle */
.oc-emblem {
    width: 52px; height: 52px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; margin: 0 auto 0.35rem; flex-shrink: 0;
}

/* ────────────────────────────
   TREE CONNECTOR SYSTEM
   ────────────────────────────
   The technique:
   - A .oc-group wraps all siblings at same level
   - .oc-group::before draws the horizontal bar
   - Each .oc-branch::before draws the vertical drop
   - Parent node connects down via .oc-vdown
*/

.oc-group {
    display: flex;
    justify-content: center;
    position: relative;
    padding-top: 32px;  /* space for vertical drops */
}

/* Horizontal connecting bar — spans center-of-first to center-of-last child */
.oc-group::before {
    content: '';
    position: absolute;
    top: 0;
    left: var(--grp-l, 10%);
    right: var(--grp-r, 10%);
    height: 2px;
    background: var(--grp-color, var(--asr-green));
}

/* Vertical drop from horizontal bar to each sibling card */
.oc-branch {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.oc-branch::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: 32px;
    background: var(--grp-color, var(--asr-green));
}

/* Vertical line going down from a parent to child group */
.oc-vdown {
    width: 2px;
    background: var(--vc-color, var(--asr-green));
    margin: 0 auto;
    flex-shrink: 0;
}

/* Single child → no horizontal bar needed */
.oc-group.single::before { display: none; }
.oc-group.single .oc-branch { flex: none; }

/* Sub-node icon circle */
.oc-icon {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; font-weight: 800;
    margin: 0 auto 0.3rem; flex-shrink: 0;
}
</style>

<!-- ═══════════════════════════════════════════
     CHART: 1100px fixed canvas, centered
     ═══════════════════════════════════════════ -->
<div style="width:1100px; margin:0 auto; display:flex; flex-direction:column; align-items:center;">

    <!-- ┌──────────────────────────────────────────┐
         │  LEVEL 1: Ministry ─── dashed ──► Navy   │
         └──────────────────────────────────────────┘ -->
    <div style="display:flex; width:100%; justify-content:center; align-items:flex-start; gap:280px; position:relative;">
        <!-- Ministry of Defence -->
        <div style="display:flex; flex-direction:column; align-items:center;">
            <div class="oc-card gov" style="min-width:160px;">
                <div class="oc-emblem" style="background:linear-gradient(135deg,#D4A460,#A0522D); color:#fff;">
                    <i class="ph ph-shield-star" style="font-size:1.5rem;"></i>
                </div>
                <div class="oc-title">Kementerian Pertahanan</div>
                <div class="oc-hint">Republik Indonesia</div>
                <span class="oc-badge gov-b">Ministry of Defence</span>
            </div>
        </div>

        <!-- Dashed arrow: Ministry → Navy (absolute, stretched between them) -->
        <div style="position:absolute; top:50px; left:calc(50% - 60px); right:0; height:2px; display:flex; align-items:center;">
            <!-- dashed line from Ministry right-edge to Navy left-edge -->
            <div style="flex:1; height:2px; background:repeating-linear-gradient(to right, #A0522D 0, #A0522D 7px, transparent 7px, transparent 14px);"></div>
            <div style="color:#A0522D; font-size:0.9rem; margin-left:2px;">▶</div>
        </div>

        <!-- Indonesian Navy -->
        <div style="display:flex; flex-direction:column; align-items:center;">
            <div class="oc-card gov" style="min-width:160px;">
                <div class="oc-emblem" style="background:linear-gradient(135deg,#1E40AF,#1E3A8A); color:#fff;">
                    <i class="ph ph-anchor-simple" style="font-size:1.5rem;"></i>
                </div>
                <div class="oc-title">Tentara Nasional Indonesia</div>
                <div class="oc-hint">Angkatan Laut</div>
                <span class="oc-badge gov-b">Indonesian Navy</span>
            </div>
        </div>
    </div>

    <!-- Vertical line: Ministry center → KSO (left of true center to align with Ministry) -->
    <div class="oc-vdown" style="height:32px; --vc-color:var(--border-color); margin-right:280px;"></div>

    <!-- ┌──────────────────────────────┐
         │  LEVEL 2: KSO Box            │
         └──────────────────────────────┘ -->
    <div style="margin-right:280px; display:flex; flex-direction:column; align-items:center;">
        <div class="oc-card kso" style="min-width:260px; padding:1rem 1.5rem;">
            <div style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--asr-green); margin-bottom:0.6rem;">KSO Non-Administratif</div>
            <div style="display:flex; align-items:center; justify-content:center; gap:0.7rem; margin-bottom:0.5rem;">
                <!-- LEN placeholder logo text -->
                <span style="font-size:1.1rem; font-weight:900; color:var(--asr-green); letter-spacing:-1px;">LEN</span>
                <span style="color:var(--asr-green); font-size:1.2rem; font-weight:700;">+</span>
                <span style="font-size:1.1rem; font-weight:900; color:#1D4ED8; letter-spacing:-1px;">PAL</span>
            </div>
            <div style="font-size:0.68rem; color:var(--text-muted);">PT LEN Industri &nbsp;+&nbsp; PT PAL Indonesia</div>
        </div>
        <div class="oc-vdown" style="height:30px; --vc-color:var(--asr-green);"></div>
    </div>

    <!-- ┌────────────────────────────────────────────┐
         │  LEVEL 3: LEN (Main) ←fork→ PAL Partner    │
         └────────────────────────────────────────────┘
         2 branches; horizontal bar: left=25%, right=25%
         The group is aligned to be under Ministry/KSO on the left
    -->
    <!-- We use a 700px wide group so LEN sits under Ministry, PAL on the right -->
    <div style="width:700px; margin-right:380px; position:relative; display:flex; flex-direction:column; align-items:center;">
        <!-- manual fork: horizontal bar -->
        <div style="position:relative; width:100%; height:30px;">
            <!-- bar from LEN center (17.5%) to PAL center (82.5%) -->
            <div style="position:absolute; top:0; left:17.5%; right:17.5%; height:2px; background:var(--asr-green);"></div>
            <!-- drop to LEN -->
            <div style="position:absolute; top:0; left:calc(17.5% - 1px); width:2px; height:30px; background:var(--asr-green);"></div>
            <!-- drop to PAL -->
            <div style="position:absolute; top:0; right:calc(17.5% - 1px); width:2px; height:30px; background:#2563EB;"></div>
        </div>

        <div style="display:flex; width:100%; justify-content:space-between; align-items:flex-start;">
            <!-- LEN Main Contractor -->
            <div style="display:flex; flex-direction:column; align-items:center;">
                <div class="oc-card main" style="min-width:155px;">
                    <div class="oc-emblem" style="background:rgba(22,163,74,0.12); color:var(--asr-green);">
                        <span style="font-size:1rem; font-weight:900;">LEN</span>
                    </div>
                    <div class="oc-title">PT LEN Industri</div>
                    <span class="oc-badge main-b">Main Contractor</span>
                    <div class="oc-hint" style="margin-top:0.25rem;">Mission System Integrator</div>
                </div>
                <div class="oc-vdown" style="height:28px; --vc-color:var(--asr-green);"></div>
            </div>

            <!-- PAL Indonesia -->
            <div style="display:flex; flex-direction:column; align-items:center;">
                <div class="oc-card pal" style="min-width:155px;">
                    <div class="oc-emblem" style="background:rgba(37,99,235,0.12); color:#2563EB;">
                        <span style="font-size:1rem; font-weight:900;">PAL</span>
                    </div>
                    <div class="oc-title">PT PAL Indonesia</div>
                    <span class="oc-badge pal-b">Consortium Partner</span>
                    <div class="oc-hint" style="margin-top:0.25rem;">Shipyard Services</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ┌──────────────────────────────────────────────────────────────┐
         │  LEVEL 4: 5 Sub-Contractors under LEN (full width)          │
         └──────────────────────────────────────────────────────────────┘ -->
    <!-- Group spans full 1100px; 5 items → bar: left=10%, right=10% -->
    <div class="oc-group" style="width:100%; --grp-l:10%; --grp-r:10%; --grp-color:var(--asr-green);">

        @php
        $sub1list = [
            ['name'=>'THALES',             'role'=>'Combat System',             'abbr'=>'THL', 'bg'=>'#DC2626', 'sub'=>true],
            ['name'=>'Nevesbu',            'role'=>'Platform System Integration','abbr'=>'NVB', 'bg'=>'#2563EB', 'sub'=>true],
            ['name'=>'PT PAL',             'role'=>'Shipyard Services',         'abbr'=>'PAL', 'bg'=>'#1D4ED8', 'sub'=>false],
            ['name'=>'CATERPILLAR',        'role'=>'Diesel Generator',          'abbr'=>'CAT', 'bg'=>'#D97706', 'sub'=>true],
            ['name'=>'MAN SEMT Pielstick', 'role'=>'Repowering Engine',         'abbr'=>'MAN', 'bg'=>'#059669', 'sub'=>true],
        ];
        @endphp

        @foreach($sub1list as $s)
        <div class="oc-branch" style="--grp-color:var(--asr-green);">
            <div style="display:flex; flex-direction:column; align-items:center;">
                <div class="oc-card sub1" style="border-color:{{ $s['bg'] }}; min-width:120px;">
                    <div class="oc-icon" style="background:{{ $s['bg'] }}18; color:{{ $s['bg'] }};">{{ $s['abbr'] }}</div>
                    <div class="oc-title" style="font-size:0.76rem;">{{ $s['name'] }}</div>
                    <span class="oc-badge" style="background:{{ $s['bg'] }}15; color:{{ $s['bg'] }};">Sub Contractor</span>
                    <div class="oc-hint" style="margin-top:0.2rem; font-size:0.62rem; white-space:normal; max-width:110px;">{{ $s['role'] }}</div>
                </div>
                @if($s['sub'])
                <div class="oc-vdown" style="height:24px; --vc-color:{{ $s['bg'] }};"></div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- ┌──────────────────────────────────────────────────────────────┐
         │  LEVEL 5: Sub-Sub-Contractors (aligned under parents)       │
         └──────────────────────────────────────────────────────────────┘
         Only 4 of the 5 subs have a sub-sub (PAL has none).
         We place a spacer for PAL's position.
    -->
    @php
    $sub2list = [
        ['name'=>'MBDA',           'role'=>'SAM & SSM Supplier',    'bg'=>'#7C3AED', 'has'=>true],
        ['name'=>'Anschütz',       'role'=>'Navigation System',     'bg'=>'#2563EB', 'has'=>true],
        ['name'=>'',               'role'=>'',                      'bg'=>'',        'has'=>false], /* PAL spacer */
        ['name'=>'Gun Systems',    'role'=>'Gun System',            'bg'=>'#D97706', 'has'=>true],
        ['name'=>'Rohde & Schwarz','role'=>'Integrated Comms System','bg'=>'#059669', 'has'=>true],
    ];
    @endphp

    <div style="display:flex; width:100%; justify-content:space-around; align-items:flex-start; padding-top:0;">
        @foreach($sub2list as $s)
        <div style="flex:1; display:flex; flex-direction:column; align-items:center;">
            @if($s['has'])
            <div class="oc-card sub2" style="border-color:{{ $s['bg'] }}77; min-width:115px;">
                <div class="oc-icon" style="background:{{ $s['bg'] }}12; color:{{ $s['bg'] }}; border-radius:10px; width:32px; height:32px; font-size:0.85rem;">
                    <i class="ph ph-arrow-bend-right-down"></i>
                </div>
                <div class="oc-title" style="font-size:0.72rem;">{{ $s['name'] }}</div>
                <span class="oc-badge" style="background:{{ $s['bg'] }}12; color:{{ $s['bg'] }};">Sub Contractor</span>
                <div class="oc-hint" style="margin-top:0.18rem; font-size:0.6rem; white-space:normal; max-width:105px;">{{ $s['role'] }}</div>
            </div>
            @else
            <!-- Spacer for PAL (no sub-sub) -->
            <div style="min-width:115px; height:10px;"></div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Legend -->
    <div style="margin-top:2rem; padding-top:1.25rem; border-top:1px solid var(--border-color); width:100%; display:flex; flex-wrap:wrap; gap:1.25rem; align-items:center;">
        <span style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.07em;">Legend</span>
        <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.75rem; color:var(--text-main);">
            <div style="width:13px; height:13px; border-radius:4px; background:#FFF8F0; border:1.5px solid #C09060;"></div> Government Entity
        </div>
        <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.75rem; color:var(--text-main);">
            <div style="width:13px; height:13px; border-radius:4px; background:rgba(22,163,74,0.06); border:1.5px dashed var(--asr-green);"></div> KSO Non-Administratif
        </div>
        <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.75rem; color:var(--text-main);">
            <div style="width:13px; height:13px; border-radius:4px; background:rgba(22,163,74,0.08); border:2px solid var(--asr-green);"></div> Main Contractor — PT LEN
        </div>
        <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.75rem; color:var(--text-main);">
            <div style="width:13px; height:13px; border-radius:4px; background:rgba(37,99,235,0.06); border:1.5px solid #2563EB;"></div> Consortium Partner — PT PAL
        </div>
        <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.75rem; color:var(--text-main);">
            <div style="width:13px; height:13px; border-radius:4px; border:1.5px solid #888;"></div> Sub Contractor Tier 1 & 2
        </div>
        <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.75rem; color:var(--text-main);">
            <div style="width:22px; height:2px; background:repeating-linear-gradient(to right,#A0522D 0,#A0522D 5px,transparent 5px,transparent 10px);"></div> Coordination Line
        </div>
    </div>

</div><!-- end 1100px canvas -->
        </div><!-- end chart container -->
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('consortiumModal');
    const closeBtn = document.getElementById('closeConsortiumBtn');
    function closeModal() {
        modal.classList.remove('active');
        modal.style.display = 'none';
    }
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (modal) {
        modal.addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    }
    // Ensure .active shows as flex
    const s = document.createElement('style');
    s.textContent = '#consortiumModal{display:none!important}#consortiumModal.active{display:flex!important}';
    document.head.appendChild(s);
})();
</script>
@endpush
