<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - ASR FARM</title>
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Apply saved theme IMMEDIATELY before render to avoid flash -->
    <script>
        (function() {
            var t = localStorage.getItem('theme');
            if (t) document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <style>
        .global-marquee-wrapper { overflow: hidden; white-space: nowrap; box-sizing: border-box; width: 100%; }
        .global-marquee-content { display: inline-flex; align-items: center; white-space: nowrap; padding-left: 100%; animation: global-marquee-anim 25s linear infinite; }
        .global-marquee-content:hover { animation-play-state: paused; }
        @keyframes global-marquee-anim { 0% { transform: translate(0, 0); } 100% { transform: translate(-100%, 0); } }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-header" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; border-bottom: 1px solid var(--border-color);">
            {{-- Logo --}}
            <img src="{{ asset('images/logo-asr.png') }}"
                 alt="ASR Farm Logo"
                 class="sidebar-logo"
                 style="width: 48px; height: 48px; object-fit: contain; display: block;"
                 onerror="this.style.display='none'; document.getElementById('logoFallback').style.display='flex';">

            {{-- Fallback jika logo belum diupload --}}
            <div id="logoFallback" style="display: none; width: 48px; height: 48px; border-radius: 50%; background: var(--asr-green-light); align-items: center; justify-content: center;">
                <i class="ph ph-plant" style="font-size: 1.6rem; color: var(--asr-green);"></i>
            </div>
            
            {{-- Toggle button kanan --}}
            <i class="ph ph-list" id="sidebarToggle" onclick="toggleSidebar()"
               style="font-size: 1.3rem; color: var(--text-muted); cursor: pointer; flex-shrink: 0;"></i>
        </div>
        <div class="sidebar-nav-container">
        <nav class="sidebar-nav">
            <a href="/hydroponics/dashboard" class="nav-item {{ request()->is('hydroponics/dashboard') ? 'active' : '' }}">
                <i class="ph ph-squares-four"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            
            <a href="/hydroponics/greenhouses" class="nav-item {{ request()->is('hydroponics/greenhouses*') ? 'active' : '' }}">
                <i class="ph ph-house-line"></i>
                <span class="nav-text">Green House</span>
            </a>

            <a href="/hydroponics/semai" class="nav-item {{ request()->is('hydroponics/semai*') ? 'active' : '' }}">
                <i class="ph ph-plant"></i>
                <span class="nav-text">Semai</span>
                @php $semaiCount = \App\Models\Semai::where('status','aktif')->count(); @endphp
                @if($semaiCount > 0)
                <span style="margin-left:auto; background:#16a34a; color:white; font-size:0.65rem; font-weight:800; padding:0.1rem 0.45rem; border-radius:50px; min-width:18px; text-align:center;">{{ $semaiCount }}</span>
                @endif
            </a>

            <div class="nav-item nav-dropdown {{ request()->is('hydroponics/inventory*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <div style="display: flex; align-items: center;">
                    <i class="ph ph-package"></i>
                    <span class="nav-text">Inventaris</span>
                </div>
                <i class="ph ph-caret-down" style="margin-right: 0; font-size: 0.9rem;"></i>
            </div>
            
            <div class="nav-submenu {{ request()->is('hydroponics/inventory*') ? 'open' : '' }}">
                <a href="/hydroponics/inventory?cat=bibit" class="submenu-item {{ request('cat') == 'bibit' ? 'active' : '' }}">Stok Bibit</a>
                <a href="/hydroponics/inventory?cat=media_tanam" class="submenu-item {{ request('cat') == 'media_tanam' ? 'active' : '' }}">Media Tanam</a>
                <a href="/hydroponics/inventory?cat=nutrisi" class="submenu-item {{ request('cat') == 'nutrisi' ? 'active' : '' }}">Nutrisi Tanaman</a>
                <a href="/hydroponics/inventory?cat=obat" class="submenu-item {{ request('cat') == 'obat' ? 'active' : '' }}">Obat & Pestisida</a>
                <a href="/hydroponics/inventory?cat=peralatan" class="submenu-item {{ request('cat') == 'peralatan' ? 'active' : '' }}">Peralatan</a>
                <a href="/hydroponics/inventory?cat=perlengkapan" class="submenu-item {{ request('cat') == 'perlengkapan' ? 'active' : '' }}">Perlengkapan</a>
            </div>

            <a href="/hydroponics/damage-notes" class="nav-item {{ request()->is('hydroponics/damage-notes*') ? 'active' : '' }}">
                <i class="ph ph-warning-octagon"></i>
                <span class="nav-text">Catatan Kerusakan</span>
            </a>

            <div class="nav-item nav-dropdown {{ request()->is('hydroponics/bandar*') ? 'active' : '' }}" onclick="toggleDropdown(this)" style="margin-top: 1rem;">
                <div style="display: flex; align-items: center;">
                    <i class="ph ph-storefront"></i>
                    <span class="nav-text">Pusat Distribusi</span>
                </div>
                <i class="ph ph-caret-down"></i>
            </div>
            <div class="nav-submenu {{ request()->is('hydroponics/bandar*') ? 'open' : '' }}">
                <a href="/hydroponics/bandar" class="submenu-item {{ request()->is('hydroponics/bandar') ? 'active' : '' }}">Dashboard Distribusi</a>
                <a href="/hydroponics/bandar/products" class="submenu-item {{ request()->is('hydroponics/bandar/products') ? 'active' : '' }}">Data Produk (Stok)</a>
                <a href="/hydroponics/bandar/partners" class="submenu-item {{ request()->is('hydroponics/bandar/partners') ? 'active' : '' }}">Data Mitra</a>
                <a href="/hydroponics/bandar/transactions" class="submenu-item {{ request()->is('hydroponics/bandar/transactions') ? 'active' : '' }}">Riwayat Transaksi</a>
            </div>

            @if(Auth::user()?->role === 'super_admin')
            <div class="nav-item nav-dropdown {{ request()->is('hydroponics/master-data/*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <div style="display: flex; align-items: center;">
                    <i class="ph ph-database"></i>
                    <span class="nav-text">Master Data</span>
                </div>
                <i class="ph ph-caret-down" style="margin-right: 0; font-size: 0.9rem;"></i>
            </div>
            
            <div class="nav-submenu {{ request()->is('hydroponics/master-data/*') ? 'open' : '' }}">
                <a href="/hydroponics/master-data/plants" class="submenu-item {{ request()->is('hydroponics/master-data/plants') ? 'active' : '' }}">Jenis Tanaman</a>
                <a href="/hydroponics/master-data/users" class="submenu-item {{ request()->is('hydroponics/master-data/users') ? 'active' : '' }}">Pengguna</a>
            </div>
            @endif

        </nav>
        </div>
    </aside>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Top Header -->
        <header class="top-header">
            <div class="page-title" style="display: flex; align-items: center; flex: 1; min-width: 0; margin-right: 1rem;">
                <button class="menu-toggle-btn" id="headerToggleBtn" onclick="toggleSidebar()" style="flex-shrink: 0; margin-right: 0.75rem;">
                    <i class="ph ph-list"></i>
                </button>

                <div style="flex: 1; overflow: hidden; display: flex; align-items: center; background: transparent; padding: 0.2rem 0;">
                    <div class="global-marquee-wrapper">
                        <div class="global-marquee-content">
                            <img src="{{ asset('images/logo-asr.png') }}" alt="Logo" style="height: 24px; width: 24px; object-fit: cover; margin-right: 10px; border-radius: 50%;">
                            <span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">Selamat Datang, {{ Auth::user()->name ?? 'Super Admin' }}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="header-actions" style="flex-shrink: 0;">
                <button class="icon-btn" id="themeToggle"><i class="ph ph-sun" id="themeIcon"></i></button>
                <button class="icon-btn"><i class="ph ph-bell"></i></button>
                
                <div class="user-profile" style="position: relative; cursor: pointer; flex-shrink: 0;" onclick="this.querySelector('.user-dropdown').classList.toggle('open')">
                    <div class="user-info" style="text-align: right;">
                        <span class="user-name">{{ Auth::user()->name ?? 'Super Admin' }}</span>
                        <span class="user-role" style="color: {{ Auth::user()?->roleBadgeColor() ?? '#16a34a' }};">
                            {{ Auth::user()?->roleLabel() ?? 'Super Admin' }}
                        </span>
                    </div>
                    <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'SA', 0, 2)) }}</div>
                    <div class="user-dropdown" style="display:none; position: absolute; top: 110%; right: 0; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-md); min-width: 180px; box-shadow: var(--shadow-md); z-index: 100; overflow: hidden;">
                        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color);">
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Login sebagai</div>
                            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-main);">{{ Auth::user()->name ?? '-' }}</div>
                            <span style="display:inline-block; margin-top:0.25rem; padding: 0.15rem 0.6rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600; background: {{ Auth::user()?->roleBadgeColor() ?? '#16a34a' }}22; color: {{ Auth::user()?->roleBadgeColor() ?? '#16a34a' }};">
                                {{ Auth::user()?->roleLabel() ?? 'Super Admin' }}
                            </span>
                        </div>
                        <form action="{{ route('auth.logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" style="width:100%; padding: 0.75rem 1rem; background:none; border:none; text-align:left; cursor:pointer; color: var(--text-main); font-size:0.875rem; display:flex; align-items:center; gap:0.5rem; font-family: inherit;">
                                <i class="ph ph-sign-out" style="color: var(--asr-green);"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Container -->
        <div class="page-container">
            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            if (window.innerWidth <= 992) {
                document.getElementById('mainSidebar').classList.toggle('open');
                document.querySelector('.sidebar-overlay').classList.toggle('open');
            } else {
                document.getElementById('mainSidebar').classList.toggle('collapsed');
                document.getElementById('mainContent').classList.toggle('collapsed');
            }
        }

        function toggleDropdown(element) {
            const submenu = element.nextElementSibling;
            submenu.classList.toggle('open');
            
            const caret = element.querySelector('.ph-caret-down') || element.querySelector('.ph-caret-up');
            if (caret) {
                if (submenu.classList.contains('open')) {
                    caret.classList.replace('ph-caret-down', 'ph-caret-up');
                } else {
                    caret.classList.replace('ph-caret-up', 'ph-caret-down');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Close user dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const profile = document.querySelector('.user-profile');
                const dropdown = document.querySelector('.user-dropdown');
                if (profile && dropdown && !profile.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
            document.querySelector('.user-profile')?.addEventListener('click', function() {
                const dropdown = this.querySelector('.user-dropdown');
                if (dropdown) {
                    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
                }
            });

            // Theme Toggle (Dark Mode)
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            
            // Check local storage
            const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;
            if (currentTheme) {
                document.documentElement.setAttribute('data-theme', currentTheme);
                if (currentTheme === 'dark') {
                    themeIcon.classList.replace('ph-sun', 'ph-moon');
                }
            }

            themeToggle.addEventListener('click', function() {
                let theme = document.documentElement.getAttribute('data-theme');
                if (theme === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'light');
                    localStorage.setItem('theme', 'light');
                    themeIcon.classList.replace('ph-moon', 'ph-sun');
                } else {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                    themeIcon.classList.replace('ph-sun', 'ph-moon');
                }
            });

            // Initialize DataTables
            if ($('.datatable').length) {
                $('.datatable').each(function() {
                    var lastCol = $(this).find('thead th').length - 1;
                    $(this).DataTable({
                        dom: '<"dt-row-buttons"B><"dt-row-controls"lf>rt<"dt-bottom"ip><"clear">',
                        buttons: [
                            { extend: 'copy', text: '<i class="ph ph-copy"></i> Copy', className: 'dt-btn dt-btn-copy' },
                            { extend: 'excel', text: '<i class="ph ph-file-xls"></i> Excel', className: 'dt-btn dt-btn-excel' },
                            { extend: 'csv', text: '<i class="ph ph-file-csv"></i> CSV', className: 'dt-btn dt-btn-csv' },
                            { extend: 'pdf', text: '<i class="ph ph-file-pdf"></i> PDF', className: 'dt-btn dt-btn-pdf' }
                        ],
                        columnDefs: [
                            { orderable: false, targets: lastCol }
                        ],
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                            infoEmpty: "Menampilkan 0 data",
                            emptyTable: "Belum ada data tersedia.",
                            zeroRecords: "Tidak ditemukan data yang cocok.",
                            paginate: {
                                first: "«",
                                last: "»",
                                next: "›",
                                previous: "‹"
                            }
                        }
                    });
                });
            }
        });

        // Global Helper for SweetAlert2 Confirmation
        function confirmAction(title, text, actionUrl, formMethod = 'POST') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="ph ph-check"></i> Ya, Lanjutkan',
                cancelButtonText: '<i class="ph ph-x"></i> Batal',
                customClass: {
                    popup: 'asr-swal-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = actionUrl;
                    form.method = 'POST';
                    form.innerHTML = `
                        @csrf
                        ${formMethod !== 'POST' ? '<input type="hidden" name="_method" value="'+formMethod+'">' : ''}
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        // ----------------------------------------------------
        // Chart.js Custom Export Menu (Highcharts Style)
        // ----------------------------------------------------
        function toggleChartMenu(btn) {
            const dropdown = btn.nextElementSibling;
            // Close all others first
            document.querySelectorAll('.chart-export-dropdown').forEach(el => {
                if(el !== dropdown) el.classList.remove('open');
            });
            dropdown.classList.toggle('open');
            btn.classList.toggle('active', dropdown.classList.contains('open'));
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.chart-export-wrapper')) {
                document.querySelectorAll('.chart-export-dropdown').forEach(el => {
                    el.classList.remove('open');
                    el.previousElementSibling.classList.remove('active');
                });
            }
        });

        function exportChart(canvasId, format) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const chart = Chart.getChart(canvas);
            
            if (format === 'fullscreen') {
                const card = canvas.closest('.chart-card') || canvas.closest('.card');
                if (card) {
                    if (!document.fullscreenElement) {
                        card.requestFullscreen().catch(err => {
                            console.error(`Error attempting to enable fullscreen mode: ${err.message} (${err.name})`);
                        });
                    } else {
                        document.exitFullscreen();
                    }
                }
            } else if (format === 'svg') {
                alert('Pustaka Chart.js menggunakan elemen Canvas yang tidak mendukung ekspor SVG secara native. Silakan gunakan PNG atau JPEG.');
                return;
            } else if (format === 'csv' || format === 'xls') {
                if (!chart) return;
                let csvContent = "data:text/csv;charset=utf-8,";
                const labels = chart.data.labels || [];
                const datasets = chart.data.datasets || [];
                csvContent += "Category," + datasets.map(ds => `"${ds.label || 'Value'}"`).join(",") + "\r\n";
                for (let i = 0; i < labels.length; i++) {
                    let row = [`"${labels[i]}"`];
                    datasets.forEach(ds => {
                        let val = ds.data[i] !== undefined ? ds.data[i] : 0;
                        row.push(val);
                    });
                    csvContent += row.join(",") + "\r\n";
                }
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", `chart_data.${format}`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else if (format === 'table') {
                if (!chart) return;
                const card = canvas.closest('.chart-card') || canvas.closest('.card');
                let tableContainer = card.querySelector('.chart-data-table');
                if (!tableContainer) {
                    tableContainer = document.createElement('div');
                    tableContainer.className = 'chart-data-table';
                    tableContainer.style.marginTop = '1rem';
                    tableContainer.style.maxHeight = '250px';
                    tableContainer.style.overflowY = 'auto';
                    tableContainer.style.border = '1px solid var(--border-color)';
                    tableContainer.style.borderRadius = '5px';
                    
                    const labels = chart.data.labels || [];
                    const datasets = chart.data.datasets || [];
                    
                    let tableHTML = '<table class="table" style="width:100%;font-size:0.8rem;border-collapse:collapse;margin:0;">';
                    tableHTML += '<thead style="background:var(--card-bg);"><tr><th style="padding:0.5rem;border-bottom:2px solid var(--border-color);text-align:left;">Category</th>';
                    datasets.forEach(ds => {
                        tableHTML += `<th style="padding:0.5rem;border-bottom:2px solid var(--border-color);text-align:right;">${ds.label || 'Value'}</th>`;
                    });
                    tableHTML += '</tr></thead><tbody>';
                    for (let i = 0; i < labels.length; i++) {
                        tableHTML += `<tr><td style="padding:0.5rem;border-bottom:1px solid var(--border-color);">${labels[i]}</td>`;
                        datasets.forEach(ds => {
                            let val = ds.data[i] !== undefined ? ds.data[i] : 0;
                            tableHTML += `<td style="padding:0.5rem;border-bottom:1px solid var(--border-color);text-align:right;">${val}</td>`;
                        });
                        tableHTML += '</tr>';
                    }
                    tableHTML += '</tbody></table>';
                    tableContainer.innerHTML = tableHTML;
                    card.appendChild(tableContainer);
                } else {
                    tableContainer.style.display = tableContainer.style.display === 'none' ? 'block' : 'none';
                }
            } else if (format === 'print' || format === 'png' || format === 'jpeg') {
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = canvas.width;
                tempCanvas.height = canvas.height;
                const ctx = tempCanvas.getContext('2d');
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
                ctx.drawImage(canvas, 0, 0);

                if (format === 'print') {
                    const dataUrl = tempCanvas.toDataURL('image/png');
                    const printWin = window.open('', '_blank');
                    printWin.document.write(`
                        <html><head><title>Print Chart</title></head>
                        <body style="margin:0;display:flex;justify-content:center;align-items:center;height:100vh;">
                            <img src="${dataUrl}" style="max-width:100%;max-height:100%;" onload="window.print();window.close();" />
                        </body></html>
                    `);
                    printWin.document.close();
                } else {
                    const dataUrl = format === 'jpeg' ? tempCanvas.toDataURL('image/jpeg', 1.0) : canvas.toDataURL('image/png');
                    const link = document.createElement('a');
                    link.download = `chart_export.${format}`;
                    link.href = dataUrl;
                    link.click();
                }
            }
            
            // Close the menu
            document.querySelectorAll('.chart-export-dropdown').forEach(el => {
                el.classList.remove('open');
                el.previousElementSibling.classList.remove('active');
            });
        }

        // Fix: Resize charts when exiting fullscreen so they return to original dimensions
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                // Exited fullscreen - resize all charts after a short delay
                setTimeout(function() {
                    document.querySelectorAll('canvas').forEach(function(canvas) {
                        try {
                            const chart = Chart.getChart(canvas);
                            if (chart) chart.resize();
                        } catch(e) { /* ignore non-chart canvases */ }
                    });
                }, 150);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
