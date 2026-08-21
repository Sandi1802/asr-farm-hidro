    <!-- Sidebar -->
    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-header" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; border-bottom: 1px solid var(--border-color);">
            {{-- Logo --}}
            <img src="{{ asset('images/logo-asr.png') }}"
                 alt="ASR Farm Logo"
                 class="sidebar-logo"
                 style="width: 48px; height: 48px; object-fit: contain; display: block; background-color: #ffffff; border-radius: 50%; padding: 2px;"
                 onerror="this.style.display='none'; document.getElementById('logoFallback').style.display='flex';">

            {{-- Fallback jika logo belum diupload --}}
            <div id="logoFallback" style="display: none; width: 48px; height: 48px; border-radius: 50%; background: var(--asr-green-light); align-items: center; justify-content: center;">
                <i class="ph ph-plant" style="font-size: 1.6rem; color: var(--asr-green);"></i>
            </div>
            
            {{-- Toggle button kanan --}}
            <i class="ph ph-list" id="sidebarToggle" onclick="toggleSidebar()"
               style="font-size: 1.3rem; color: var(--text-muted); cursor: pointer; flex-shrink: 0;"></i>
        </div>
        <nav class="sidebar-nav">
            @php 
                $isDashboardActive = request()->is('hydroponics/dashboard') || request()->is('konvensional/dashboard'); 
            @endphp
            <a href="/hydroponics/dashboard" class="nav-item {{ $isDashboardActive ? 'active' : '' }}" style="margin-bottom: 0; display: flex; align-items: center; text-decoration: none;">
                <i class="ph ph-house"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <div class="nav-submenu open" style="display: block; margin-top: 0.3rem; margin-bottom: 1rem;">
                <a href="/hydroponics/dashboard" class="submenu-item {{ request()->is('hydroponics/dashboard') ? 'active' : '' }}">
                    <i class="ph ph-chart-line-up" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Hidroponik
                </a>
                <a href="/konvensional/dashboard" class="submenu-item {{ request()->is('konvensional/dashboard') ? 'active' : '' }}">
                    <i class="ph ph-chart-pie-slice" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Konvensional
                </a>
            </div>
            
            @php 
                $hidroponikActive = request()->is('hydroponics/greenhouses*') || request()->is('hydroponics/semai*') || request()->is('hydroponics/damage-notes*'); 
            @endphp
            {{-- Hidroponik Dropdown --}}
            @if(in_array(Auth::user()?->role_agri, ['it_admin', 'atasan', 'produksi', 'produksi_gh', 'packing']))
            <div class="nav-item nav-dropdown {{ $hidroponikActive ? 'active' : '' }}" onclick="toggleDropdown(this)" style="margin-top: 1rem;">
                <div style="display: flex; align-items: center;">
                    <i class="ph ph-drop"></i>
                    <span class="nav-text">Hidroponik</span>
                </div>
                <i class="{{ $hidroponikActive ? 'ph ph-caret-down' : 'ph ph-caret-left' }}" style="margin-right: 0; font-size: 0.9rem; transition: transform 0.2s;"></i>
            </div>
            
            <div class="nav-submenu {{ $hidroponikActive ? 'open' : '' }}">
                <a href="/hydroponics/greenhouses" class="submenu-item {{ request()->is('hydroponics/greenhouses*') ? 'active' : '' }}">
                    <i class="ph ph-house-line" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Green House
                </a>
                
                <a href="/hydroponics/semai" class="submenu-item {{ request()->is('hydroponics/semai*') ? 'active' : '' }}">
                    <i class="ph ph-plant" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Semai
                    @php $semaiCount = \App\Models\Semai::where('status','aktif')->count(); @endphp
                    @if($semaiCount > 0)
                    <span style="margin-left:auto; background:#16a34a; color:white; font-size:0.65rem; font-weight:800; padding:0.1rem 0.45rem; border-radius:50px; min-width:18px; text-align:center;">{{ $semaiCount }}</span>
                    @endif
                </a>
                <a href="{{ route('hydroponics.maintenance-logs') }}" class="submenu-item {{ request()->is('maintenance-logs*') ? 'active' : '' }}">
                    <i class="ph ph-clipboard-text" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Laporan Pemeliharaan
                </a>
                
                <a href="/hydroponics/damage-notes" class="submenu-item {{ request()->is('hydroponics/damage-notes*') ? 'active' : '' }}">
                    <i class="ph ph-warning-octagon" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Catatan Kerusakan
                </a>
            </div>
            @endif

            @php 
                $konvensionalActive = request()->is('konvensional*');
            @endphp
            {{-- Konvensional Dropdown --}}
            @if(in_array(Auth::user()?->role_agri, ['it_admin', 'atasan', 'produksi', 'produksi_konven', 'packing']))
            <div class="nav-item nav-dropdown {{ $konvensionalActive ? 'active' : '' }}" onclick="toggleDropdown(this)" style="margin-top: 1rem;">
                <div style="display: flex; align-items: center;">
                    <i class="ph ph-tree"></i>
                    <span class="nav-text">Konvensional</span>
                </div>
                <i class="{{ $konvensionalActive ? 'ph ph-caret-down' : 'ph ph-caret-left' }}" style="margin-right: 0; font-size: 0.9rem; transition: transform 0.2s;"></i>
            </div>
            
            <div class="nav-submenu {{ $konvensionalActive ? 'open' : '' }}">
                <a href="{{ route('konvensional.lahan') }}" class="submenu-item {{ request()->is('konvensional/lahan*') || request()->is('konvensional/bedengan*') || request()->is('konvensional/titik-tanam*') ? 'active' : '' }}">
                    <i class="ph ph-map-trifold" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Manajemen Lahan
                </a>
                <a href="{{ route('konvensional.pemupukan') }}" class="submenu-item {{ request()->is('konvensional/pemupukan*') ? 'active' : '' }}">
                    <i class="ph ph-flask" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Jadwal Pemupukan
                </a>
                <a href="{{ route('konvensional.penyemprotan') }}" class="submenu-item {{ request()->is('konvensional/penyemprotan*') ? 'active' : '' }}">
                    <i class="ph ph-drop" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Jadwal Penyemprotan
                </a>
                <a href="{{ route('konvensional.bibit') }}" class="submenu-item {{ request()->is('konvensional/bibit*') ? 'active' : '' }}">
                    <i class="ph ph-leaf" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Bibit Konvensional
                </a>
            </div>
            @endif

            @php
                $distribusiActive = request()->is('hydroponics/bandar*');
            @endphp
            {{-- Pusat Distribusi Dropdown --}}
            @if(in_array(Auth::user()?->role_agri, ['it_admin', 'atasan', 'keuangan', 'pemasaran', 'packing']))
            <div class="nav-item nav-dropdown {{ $distribusiActive ? 'active' : '' }}" onclick="toggleDropdown(this)" style="margin-top: 1rem;">
                <div style="display: flex; align-items: center;">
                    <i class="ph ph-storefront"></i>
                    <span class="nav-text">Warehouse</span>
                </div>
                <i class="{{ $distribusiActive ? 'ph ph-caret-down' : 'ph ph-caret-left' }}" style="margin-right: 0; font-size: 0.9rem; transition: transform 0.2s;"></i>
            </div>
            <div class="nav-submenu {{ $distribusiActive ? 'open' : '' }}">
                <a href="/hydroponics/bandar" class="submenu-item {{ request()->is('hydroponics/bandar') ? 'active' : '' }}">
                    <i class="ph ph-chart-bar" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Dashboard Distribusi
                </a>
                <a href="/hydroponics/bandar/products" class="submenu-item {{ request()->is('hydroponics/bandar/products') ? 'active' : '' }}">
                    <i class="ph ph-package" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Data Produk (Stok)
                </a>
                <a href="/hydroponics/bandar/partners" class="submenu-item {{ request()->is('hydroponics/bandar/partners') ? 'active' : '' }}">
                    <i class="ph ph-users" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Data Mitra
                </a>
                <a href="/hydroponics/bandar/transactions" class="submenu-item {{ request()->is('hydroponics/bandar/transactions') ? 'active' : '' }}">
                    <i class="ph ph-receipt" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Riwayat Transaksi
                </a>
            </div>
            @endif

            @php
                $inventarisActive = request()->is('hydroponics/inventory*');
            @endphp
            {{-- Inventaris Dropdown --}}
            @if(in_array(Auth::user()?->role_agri, ['it_admin', 'atasan', 'produksi', 'produksi_gh', 'produksi_konven', 'keuangan', 'pemasaran', 'packing']))
            <div class="nav-item nav-dropdown {{ $inventarisActive ? 'active' : '' }}" onclick="toggleDropdown(this)" style="margin-top: 1rem;">
                <div style="display: flex; align-items: center;">
                    <i class="ph ph-package"></i>
                    <span class="nav-text">Inventaris</span>
                </div>
                <i class="{{ $inventarisActive ? 'ph ph-caret-down' : 'ph ph-caret-left' }}" style="margin-right: 0; font-size: 0.9rem; transition: transform 0.2s;"></i>
            </div>
            
            <div class="nav-submenu {{ $inventarisActive ? 'open' : '' }}">
                <a href="/hydroponics/inventory?cat=bibit" class="submenu-item {{ request('cat') == 'bibit' ? 'active' : '' }}">
                    <i class="ph ph-leaf" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Stok Bibit
                </a>
                <a href="/hydroponics/inventory?cat=media_tanam" class="submenu-item {{ request('cat') == 'media_tanam' ? 'active' : '' }}">
                    <i class="ph ph-mountains" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Media Tanam
                </a>
                <a href="/hydroponics/inventory?cat=nutrisi" class="submenu-item {{ request('cat') == 'nutrisi' ? 'active' : '' }}">
                    <i class="ph ph-flask" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Nutrisi Tanaman
                </a>
                <a href="/hydroponics/inventory?cat=obat" class="submenu-item {{ request('cat') == 'obat' ? 'active' : '' }}">
                    <i class="ph ph-prescription" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Obat & Pestisida
                </a>
                <a href="/hydroponics/inventory?cat=peralatan" class="submenu-item {{ request('cat') == 'peralatan' ? 'active' : '' }}">
                    <i class="ph ph-wrench" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Peralatan
                </a>
                <a href="/hydroponics/inventory?cat=perlengkapan" class="submenu-item {{ request('cat') == 'perlengkapan' ? 'active' : '' }}">
                    <i class="ph ph-toolbox" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Perlengkapan
                </a>
            </div>
            @endif

            @php
                $masterDataActive = request()->is('hydroponics/master-data/*');
            @endphp
            {{-- Master Data Dropdown --}}
            @if(Auth::user()?->role_agri === 'it_admin')
            <div class="nav-item nav-dropdown {{ $masterDataActive ? 'active' : '' }}" onclick="toggleDropdown(this)" style="margin-top: 1rem;">
                <div style="display: flex; align-items: center;">
                    <i class="ph ph-database"></i>
                    <span class="nav-text">Master Data</span>
                </div>
                <i class="{{ $masterDataActive ? 'ph ph-caret-down' : 'ph ph-caret-left' }}" style="margin-right: 0; font-size: 0.9rem; transition: transform 0.2s;"></i>
            </div>
            
            <div class="nav-submenu {{ $masterDataActive ? 'open' : '' }}">
                <a href="/hydroponics/master-data/labels" class="submenu-item {{ request()->is('hydroponics/master-data/labels') ? 'active' : '' }}">
                    <i class="ph ph-tag" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Labels
                </a>
                <a href="/hydroponics/master-data/plants" class="submenu-item {{ request()->is('hydroponics/master-data/plants') ? 'active' : '' }}">
                    <i class="ph ph-list-bullets" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Jenis Tanaman
                </a>
                <a href="/hydroponics/master-data/users" class="submenu-item {{ request()->is('hydroponics/master-data/users') ? 'active' : '' }}">
                    <i class="ph ph-users-three" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Pengguna
                </a>
                <a href="/hydroponics/master-data/employees" class="submenu-item {{ request()->is('hydroponics/master-data/employees') ? 'active' : '' }}">
                    <i class="ph ph-identification-card" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Karyawan
                </a>
            </div>

            {{-- Diari IT (Hanya IT Admin) --}}
            <a href="{{ route('it.diary') }}" class="nav-item {{ request()->routeIs('it.diary') ? 'active' : '' }}" style="margin-top: 1rem;">
                <div style="display: flex; align-items: center;">
                    <i class="ph ph-book-open-text"></i>
                    <span class="nav-text">Diari IT</span>
                </div>
            </a>
            @endif

        </nav>
    </aside>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
