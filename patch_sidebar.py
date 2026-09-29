import sys

with open('resources/views/layouts/sidebar.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old = """                <a href="{{ route('konvensional.bibit') }}" class="submenu-item {{ request()->is('konvensional/bibit*') ? 'active' : '' }}">
                    <i class="ph ph-leaf" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Bibit Konvensional
                </a>
            </div>"""

new = """                <a href="/konvensional/kebun" class="submenu-item {{ request()->is('konvensional/kebun') ? 'active' : '' }}">
                    <i class="ph ph-squares-four" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Peta Bedeng
                </a>
                <a href="/konvensional/kebun/tanam" class="submenu-item {{ request()->is('konvensional/kebun/tanam*') ? 'active' : '' }}">
                    <i class="ph ph-plant" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Tanam Baru
                </a>
                <a href="/konvensional/kebun/panen" class="submenu-item {{ request()->is('konvensional/kebun/panen*') ? 'active' : '' }}">
                    <i class="ph ph-basket" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Catat Panen
                </a>
                <a href="/konvensional/kebun/master-tanaman" class="submenu-item {{ request()->is('konvensional/kebun/master-tanaman*') ? 'active' : '' }}">
                    <i class="ph ph-list-bullets" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Master Tanaman
                </a>
            </div>"""

if old in content:
    content = content.replace(old, new)
    with open('resources/views/layouts/sidebar.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print('Sidebar updated')
else:
    print('Target not found')
