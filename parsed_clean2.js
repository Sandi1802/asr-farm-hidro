
// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â
//  DATA FROM BLADE
// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â
const calendarData  = [];
const rotationData  = [];
const plantStageData= [];

let currentYear, currentMonth;
const monthsStr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

window.calFilters = {
    semai: true, tanam: true, remaja: true, harvest: true, custom: true
};
window.currentActiveDate = null;

function toggleCalFilter(type, el) {
    window.calFilters[type] = !window.calFilters[type];
    if (!window.calFilters[type]) {
        el.style.textDecoration = 'line-through';
        el.style.opacity = '0.5';
    } else {
        el.style.textDecoration = 'none';
        el.style.opacity = '1';
    }
    renderCalendar();
    if (window.currentActiveDate) {
        renderDailySchedule(window.currentActiveDate);
    }
}

// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â
//  CALENDAR
// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â
function initCalendar() {
    const t = new Date();
    currentYear  = t.getFullYear();
    currentMonth = t.getMonth();
    renderCalendar();
    const todayStr = currentYear + '-' + pad(currentMonth+1) + '-' + pad(t.getDate());
    renderDailySchedule(todayStr);
    buildMonthDropdown();
    buildYearDropdown();
}

function buildMonthDropdown() {
    let html = '';
    monthsStr.forEach((m, i) => {
        const btnClass = i === currentMonth ? 'cal-btn active' : 'cal-btn';
        html += `<button type="button" class="${btnClass}" onclick="selectMonth(${i})">${m.substring(0,3)}</button>`;
    });
    document.getElementById('monthGrid').innerHTML = html;
}
function buildYearDropdown() {
    // Set the input value to current year
    const inp = document.getElementById('yearInput');
    if (inp) inp.value = currentYear;
}
function nudgeYear(d) {
    const inp = document.getElementById('yearInput');
    const newY = parseInt(inp.value) + d;
    if (newY >= 1990 && newY <= 2099) inp.value = newY;
}
function selectYearFromInput(val) {
    const y = parseInt(val);
    if (y >= 1990 && y <= 2099) { currentYear = y; renderCalendar(); }
}
function confirmYear() {
    const inp = document.getElementById('yearInput');
    const y = parseInt(inp.value);
    if (y >= 1990 && y <= 2099) { currentYear = y; }
    document.getElementById('yearDropdown').style.display = 'none';
    renderCalendar();
}
function toggleMonthSelect() {
    document.getElementById('monthDropdown').style.display = document.getElementById('monthDropdown').style.display === 'none' ? 'block' : 'none';
    document.getElementById('yearDropdown').style.display  = 'none';
}
function toggleYearSelect() {
    const dd = document.getElementById('yearDropdown');
    const isOpen = dd.style.display !== 'none';
    dd.style.display = isOpen ? 'none' : 'block';
    document.getElementById('monthDropdown').style.display = 'none';
    if (!isOpen) {
        buildYearDropdown();
        // Briefly highlight the input
        setTimeout(() => { const inp=document.getElementById('yearInput'); if(inp){inp.select();} }, 50);
    }
}
function selectMonth(m) { currentMonth = m; document.getElementById('monthDropdown').style.display = 'none'; renderCalendar(); }
function selectYear(y)  { currentYear  = y; document.getElementById('yearDropdown').style.display  = 'none'; renderCalendar(); }
function changeMonth(d) {
    currentMonth += d;
    if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
    renderCalendar();
}
function pad(n) { return String(n).padStart(2,'0'); }

document.addEventListener('click', function(e) {
    if (!e.target.closest('.cal-month-selector') && !e.target.closest('#monthDropdown'))
        document.getElementById('monthDropdown').style.display = 'none';
    if (!e.target.closest('.cal-year-selector') && !e.target.closest('#yearDropdown'))
        document.getElementById('yearDropdown').style.display  = 'none';
});

function renderCalendar() {
    const today    = new Date();
    const todayStr = today.getFullYear() + '-' + pad(today.getMonth()+1) + '-' + pad(today.getDate());
    const first    = new Date(currentYear, currentMonth, 1);
    const last     = new Date(currentYear, currentMonth + 1, 0);

    document.getElementById('calMonthText').textContent = monthsStr[currentMonth];
    document.getElementById('calYearText').textContent  = currentYear;
    
    // Update dashboard stats automatically
    if (typeof fetchProduksiStats === 'function') {
        fetchProduksiStats(currentMonth + 1, currentYear);
    }

    document.querySelectorAll('#monthGrid .cal-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i === currentMonth);
    });
    document.querySelectorAll('#yearScroller .cal-btn').forEach(btn => {
        btn.classList.toggle('active', parseInt(btn.textContent) === currentYear);
    });

    const typeColors = {
        semai: '#16a34a', tanam: '#2563eb', remaja: '#d97706',
        harvest: '#e11d48', planting: '#16a34a', custom: '#0891b2'
    };

    const firstDayIndex = first.getDay();
    const prevLast = new Date(currentYear, currentMonth, 0);
    const prevDaysCount = prevLast.getDate();

    let html = '';
    // Previous month padding days
    for (let i = firstDayIndex - 1; i >= 0; i--) {
        const num = prevDaysCount - i;
        html += `<div class="cal-day other-month"><div class="cal-day-num">${num}</div></div>`;
    }

    // Current month days
    for (let d = 1; d <= last.getDate(); d++) {
        const dateStr = currentYear + '-' + pad(currentMonth+1) + '-' + pad(d);
        const isToday = dateStr === todayStr;
        let evList  = calendarData[dateStr] || [];
        evList = evList.filter(ev => {
            let type = ev.type;
            if (type === 'planting') type = 'tanam';
            return window.calFilters[type] !== false;
        });
        const evCount = evList.length;

        let dots = '';
        evList.slice(0,4).forEach(ev => {
            const color = typeColors[ev.type] || '#9ca3af';
            dots += `<span class="cal-dot" style="background:${color};"></span>`;
        });
        if (evCount > 4) dots += `<span style="font-size:0.6rem;color:var(--text-muted);font-weight:700;line-height:1;">+${evCount-4}</span>`;

        html += `<div class="cal-day${isToday?' today':''}${evCount>0?' has-event':''}"
                     data-date="${dateStr}"
                     onclick="renderDailySchedule('${dateStr}')">
                    <div class="cal-day-num">${d}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:2px;align-items:center;margin-top:2px;">${dots}</div>
                 </div>`;
    }

    // Next month padding days to complete row grid
    const totalCells = firstDayIndex + last.getDate();
    const nextDaysNeeded = (totalCells % 7 === 0) ? 0 : 7 - (totalCells % 7);
    for (let n = 1; n <= nextDaysNeeded; n++) {
        html += `<div class="cal-day other-month"><div class="cal-day-num">${n}</div></div>`;
    }

    document.getElementById('calBody').innerHTML = html;
}

function renderDailySchedule(dateStr) {
    const [y,m,d] = dateStr.split('-');
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('scheduleDateLabel').textContent = `${parseInt(d)} ${months[parseInt(m)-1]} ${y}`;

    // Active day selection highlight
    document.querySelectorAll('.cal-day').forEach(el => el.classList.remove('selected-day'));
    const clickedDay = document.querySelector(`.cal-day[data-date="${dateStr}"]`);
    if (clickedDay) clickedDay.classList.add('selected-day');

    window.currentActiveDate = dateStr;
    let evList  = calendarData[dateStr] || [];
    evList = evList.filter(ev => {
        let type = ev.type;
        if (type === 'planting') type = 'tanam';
        return window.calFilters[type] !== false;
    });
    const container = document.getElementById('dailyScheduleList');

    if (evList.length === 0) {
        container.innerHTML = `<div style="padding:3.5rem 1.5rem;text-align:center;color:var(--text-muted);display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;">
            <i class="ph ph-calendar-blank" style="font-size:2.5rem;margin-bottom:0.75rem;opacity:0.6;"></i>
            <div style="font-weight:600;font-size:0.9rem;margin-bottom:0.25rem;">Tidak Ada Kegiatan</div>
            <div style="font-size:0.78rem;opacity:0.8;">Tidak ada jadwal tercatat pada tanggal ini.</div>
        </div>`;
        return;
    }

    // Stage config
    const stageMap = {
        semai:   { icon:'ph-seedling',  color:'#16a34a', bg:'#dcfce7', label:'Fase Penyemaian',  emoji:'Ã°Å¸Å’Â±' },
        tanam:   { icon:'ph-plant',     color:'#2563eb', bg:'#dbeafe', label:'Fase Penanaman',   emoji:'Ã°Å¸ÂªÂ´' },
        remaja:  { icon:'ph-leaf',      color:'#9333ea', bg:'#f3e8ff', label:'Fase Remaja',       emoji:'Ã°Å¸Å’Â¿' },
        harvest: { icon:'ph-basket',    color:'#e11d48', bg:'#ffe4e6', label:'Jadwal Panen',      emoji:'Ã°Å¸Å’Â¾' },
        planting:{ icon:'ph-plant',     color:'#16a34a', bg:'#dcfce7', label:'Penanaman',         emoji:'Ã°Å¸Å’Â±' },
        custom:  { icon:'ph-bookmark',  color:'#0891b2', bg:'#ecfeff', label:'Kegiatan',          emoji:'Ã°Å¸â€œÅ’' },
    };

    let html = '';
    evList.forEach(ev => {
        const cfg = stageMap[ev.type] || stageMap.custom;
        let title, subtitle, badge = '';

        if (ev.type === 'harvest') {
            title    = `<span style="font-weight:700;">${cfg.emoji} Panen ${ev.plant_name}</span>`;
            subtitle = `${ev.location} Ã‚Â· Umur: ${ev.days_old} hari`;
            if (ev.is_ready)      badge = `<span style="color:#dc2626;background:#fee2e2;padding:2px 7px;border-radius:4px;font-size:0.65rem;font-weight:700;">SIAP!</span>`;
            if (ev.harvested_by)  subtitle += ` Ã‚Â· <i class="ph ph-user" style="font-size:0.7rem;"></i> ${ev.harvested_by}`;
        } else if (ev.type === 'semai') {
            title    = `<span style="font-weight:700;">${cfg.emoji} Penyemaian ${ev.plant_name}</span>`;
            subtitle = `${ev.location} Ã‚Â· Hari ke-${ev.stage_day}`;
            badge    = `<span style="color:#15803d;font-size:0.7rem;font-weight:600;">${ev.time || ''}</span>`;
        } else if (ev.type === 'tanam') {
            title    = `<span style="font-weight:700;">${cfg.emoji} Fase Tanam ${ev.plant_name}</span>`;
            subtitle = `${ev.location} Ã‚Â· Hari ke-${ev.stage_day}`;
        } else if (ev.type === 'remaja') {
            title    = `<span style="font-weight:700;">${cfg.emoji} Fase Remaja ${ev.plant_name}</span>`;
            subtitle = `${ev.location} Ã‚Â· Hari ke-${ev.stage_day}`;
        } else if (ev.type === 'custom') {
            title    = `<span style="font-weight:700;">${cfg.emoji} ${ev.title}</span>`;
            subtitle = ev.description || 'Kegiatan Kustom';
            badge    = ev.time ? `<span style="color:#3b82f6;font-size:0.7rem;font-weight:600;">${ev.time}</span>` : '';
        } else {
            title    = `<span style="font-weight:700;">${cfg.emoji} ${ev.plant_name}</span>`;
            subtitle = ev.location || '';
        }

        if (ev.hole_count && ev.hole_count > 1) {
            title += ` <span style="font-size:0.75rem;font-weight:normal;color:var(--text-muted);">(${ev.hole_count} lubang)</span>`;
        }


        html += `
        <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1.25rem;border-bottom:1px solid var(--border-color);">
            <div style="width:36px;height:36px;border-radius:50%;background:${cfg.bg};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ph ${cfg.icon}" style="color:${cfg.color};font-size:1.1rem;"></i>
            </div>
            <div style="flex:1;min-width:0;line-height:1.4;">
                <div style="font-size:0.875rem;color:var(--text-main);">${title}</div>
                <div style="font-size:0.72rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${subtitle}</div>
            </div>
            <div>${badge}</div>
        </div>`;
    });
    container.innerHTML = html;
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});

// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â
//  CHARTS
// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â
function initCharts() {
    const isDark     = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
    const textColor  = isDark ? '#94A3B8' : '#64748B';
    const cardBg     = isDark ? '#1E293B' : '#ffffff';

    const greenPalette  = ['#16a34a','#22c55e','#4ade80','#86efac','#bbf7d0','#dcfce7','#f0fdf4','#15803d'];
    const orangePalette = ['#ea580c','#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5','#fff7ed','#c2410c'];
    const fontOpts = { family: 'Inter', size: 12 };

    // Ã¢â€â‚¬Ã¢â€â‚¬ 1. Tanaman Paling Sering Ditanam Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
    const mostPlantedCtx = document.getElementById('chartMostPlanted');
    if (mostPlantedCtx) {
        new Chart(mostPlantedCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Jumlah Lubang Ditanam',
                    data:   [],
                    backgroundColor: greenPalette,
                    borderRadius: 7,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: fontOpts } },
                    y: { grid: { display: false }, ticks: { color: textColor, font: fontOpts } }
                }
            }
        });
    }

    // Ã¢â€â‚¬Ã¢â€â‚¬ 2. Tanaman Paling Sering Dipanen Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
    const mostHarvestedCtx = document.getElementById('chartMostHarvested');
    if (mostHarvestedCtx) {
        new Chart(mostHarvestedCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Jumlah Panen',
                    data:   [],
                    backgroundColor: orangePalette,
                    borderRadius: 7,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: fontOpts } },
                    y: { grid: { display: false }, ticks: { color: textColor, font: fontOpts } }
                }
            }
        });
    }

    // Ã¢â€â‚¬Ã¢â€â‚¬ 3. Perputaran / Occupancy per Greenhouse Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
    const rotCtx = document.getElementById('chartRotation');
    if (rotCtx && rotationData.length > 0) {
        new Chart(rotCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: rotationData.map(r => r.name),
                datasets: [
                    {
                        label: 'Ditanam',
                        data:  rotationData.map(r => r.planted),
                        backgroundColor: 'rgba(22,163,74,0.8)',
                        borderRadius: 6, stack: 'stack',
                    },
                    {
                        label: 'Siap Panen',
                        data:  rotationData.map(r => r.ready),
                        backgroundColor: 'rgba(234,88,12,0.8)',
                        borderRadius: 6, stack: 'stack',
                    },
                    {
                        label: 'Sudah Panen',
                        data:  rotationData.map(r => r.harvested),
                        backgroundColor: 'rgba(124,58,237,0.7)',
                        borderRadius: 6, stack: 'stack',
                    },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, font: fontOpts, usePointStyle: true, boxWidth: 10 } },
                    tooltip: {
                        callbacks: {
                            afterBody: (items) => {
                                const rd = rotationData[items[0].dataIndex];
                                return [`Occupancy: ${rd.rate}%`];
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor, font: fontOpts } },
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: fontOpts } }
                }
            }
        });
    }

    // Ã¢â€â‚¬Ã¢â€â‚¬ 3B. Distribusi Green House (Pie) Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
    const ghCtx = document.getElementById('chartGHDistribution');
    if (ghCtx) {
        const ghDistData = [];
        if (ghDistData && ghDistData.length > 0) {
            const labels = ghDistData.map(item => item.name);
            const data = ghDistData.map(item => item.racks);
            const bgColors = ['#10b981', '#f59e0b', '#06b6d4', '#ec4899', '#8b5cf6', '#3b82f6', '#ef4444', '#14b8a6', '#f97316'];
            
            new Chart(ghCtx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: bgColors.slice(0, labels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: 10 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed;
                                    const total = context.chart._metasets[context.datasetIndex].total;
                                    const percent = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${percent}% (${val} Rak)`;
                                }
                            }
                        }
                    },
                    onClick: (e, activeElements) => {
                        if (activeElements.length > 0) {
                            const dataIndex = activeElements[0].index;
                            const ghInfo = ghDistData[dataIndex];
                            
                            document.getElementById('ghPlantsModalTitle').innerText = 'Tanaman di ' + ghInfo.name;
                            const listEl = document.getElementById('ghPlantsModalList');
                            listEl.innerHTML = '';
                            
                            if (ghInfo.plants && ghInfo.plants.length > 0) {
                                ghInfo.plants.forEach((p, idx) => {
                                    let li = document.createElement('li');
                                    li.innerText = `${idx + 1}. ${p}`;
                                    li.style.padding = '0.75rem 1rem';
                                    li.style.borderBottom = idx < ghInfo.plants.length - 1 ? '1px solid var(--border-color)' : 'none';
                                    li.style.fontSize = '0.9rem';
                                    li.style.color = 'var(--text-main)';
                                    listEl.appendChild(li);
                                });
                            } else {
                                let li = document.createElement('li');
                                li.innerText = 'Tidak ada tanaman saat ini.';
                                li.style.padding = '1rem';
                                li.style.color = 'var(--text-muted)';
                                li.style.textAlign = 'center';
                                listEl.appendChild(li);
                            }
                            
                            document.getElementById('ghPlantsModal').style.display = 'block';
                        }
                    }
                }
            });

            // Build Custom HTML Legend
            const legendContainer = document.getElementById('ghLegendContainer');
            if (legendContainer) {
                let legendHtml = `<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-top: 1rem;">`;
                ghDistData.forEach((item, idx) => {
                    const color = bgColors[idx % bgColors.length];
                    legendHtml += `
                        <div style="display: flex; align-items: center; gap: 0.5rem; background: var(--bg-color); padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                            <div style="width: 14px; height: 14px; border-radius: 4px; background-color: ${color}; flex-shrink: 0;"></div>
                            <div style="line-height: 1.2;">
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-main);">${item.name}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">${item.racks} Rak</div>
                            </div>
                        </div>
                    `;
                });
                legendHtml += `</div>`;
                legendContainer.innerHTML = legendHtml;
            }
        }
    }

    // Ã¢â€â‚¬Ã¢â€â‚¬ 3C. Tren Produksi (4 Minggu Terakhir)
    const trendCtx = document.getElementById('chartWeeklyTrend');
    if (trendCtx) {
        const trendData = [];
        if (trendData && trendData.labels) {
            window.trendChartInstance = new Chart(trendCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: trendData.labels,
                    datasets: [
                        {
                            label: 'Semai',
                            data: trendData.semai,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Tanam',
                            data: trendData.tanam,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Panen',
                            data: trendData.panen,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: {size: 11} } }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [4,4] } },
                        x: { grid: { display: false } }
                    },
                    interaction: { mode: 'index', intersect: false }
                }
            });
        }
    }
}

    function fetchProduksiStats(month, year) {
    let totalJenisSemai = new Set();
    let totalSemai = 0;
    let totalTanam = 0;
    let totalPanen = 0;

    // We calculate from calendarData for the requested month/year
    const daysInMonth = new Date(year, month, 0).getDate();
    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = year + '-' + pad(month) + '-' + pad(d);
        let evList = calendarData[dateStr] || [];
        
        // Filter by calFilters to make it truly dynamic
        evList = evList.filter(ev => {
            let type = ev.type;
            if (type === 'planting') type = 'tanam';
            return window.calFilters[type] !== false;
        });

        evList.forEach(ev => {
            let type = ev.type;
            if (type === 'planting') type = 'tanam';
            
            // Determine quantity. Default to hole_count, or meta_qty, or 1.
            const qty = ev.hole_count || ev.meta_qty || 1;
            
            if (type === 'semai') {
                if (ev.plant_name) totalJenisSemai.add(ev.plant_name);
                totalSemai += qty;
            } else if (type === 'tanam') {
                totalTanam += qty;
            } else if (type === 'harvest') {
                totalPanen += qty;
            }
        });
    }

    const monthName = monthsStr[month - 1];

    if (document.getElementById('produksiTitleText')) {
        document.getElementById('produksiTitleText').textContent = `Produksi ${monthName} ${year}`;
    }
    if (document.getElementById('val-jenis-semai')) {
        document.getElementById('val-jenis-semai').textContent = `${totalJenisSemai.size} Jenis`;
    }
    if (document.getElementById('val-total-semai')) {
        const fmt = new Intl.NumberFormat('id-ID').format(totalSemai);
        document.getElementById('val-total-semai').textContent = `${fmt} Benih`;
    }
    if (document.getElementById('val-total-tanam')) {
        const fmt = new Intl.NumberFormat('id-ID').format(totalTanam);
        document.getElementById('val-total-tanam').textContent = `${fmt} Lubang`;
    }
    if (document.getElementById('val-total-panen')) {
        const fmt = new Intl.NumberFormat('id-ID').format(totalPanen);
        document.getElementById('val-total-panen').textContent = `${fmt} Lubang`;
    }
}


// Ã¢â€â‚¬Ã¢â€â‚¬ Chart UI interactions & API Updates Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function toggleChartMenu(btn) {
    document.querySelectorAll('.chart-export-dropdown.show').forEach(menu => {
        if (menu !== btn.nextElementSibling) menu.classList.remove('show');
    });
    btn.nextElementSibling.classList.toggle('show');
}
document.addEventListener('click', function(e) {
    if(!e.target.closest('.chart-export-wrapper')) {
        document.querySelectorAll('.chart-export-dropdown.show').forEach(menu => menu.classList.remove('show'));
    }
});

function exportChart(chartId, action) {
    const canvas = document.getElementById(chartId);
    if (!canvas) return;
    if (action === 'fullscreen') {
        const wrapper = canvas.closest('.chart-card');
        if (wrapper.requestFullscreen) { wrapper.requestFullscreen(); }
        else if (wrapper.webkitRequestFullscreen) { wrapper.webkitRequestFullscreen(); }
    } else if (action === 'png') {
        const link = document.createElement('a');
        link.download = chartId + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    } else if (action === 'jpeg') {
        const link = document.createElement('a');
        link.download = chartId + '.jpeg';
        link.href = canvas.toDataURL('image/jpeg', 1.0);
        link.click();
    } else if (action === 'print') {
        const win = window.open();
        win.document.write('<img src="' + canvas.toDataURL() + '" onload="window.print();window.close();" />');
    } else {
        alert("Fitur " + action + " sedang dalam pengembangan.");
    }
}

function updateTrendChart(period) {
    fetch('/hydroponics/dashboard/trend-chart?period=' + period)
        .then(res => res.json())
        .then(data => {
            if(window.trendChartInstance) {
                window.trendChartInstance.data.labels = data.labels;
                window.trendChartInstance.data.datasets[0].data = data.semai;
                window.trendChartInstance.data.datasets[1].data = data.tanam;
                window.trendChartInstance.data.datasets[2].data = data.panen;
                window.trendChartInstance.update();
                
                let title = 'Tren Produksi ';
                if(period === 'mingguan') title += '(4 Minggu Terakhir)';
                else if(period === 'bulanan') title += '(Tahun Ini)';
                else title += '(5 Tahun Terakhir)';
                document.getElementById('trendChartTitleText').textContent = title;
            }
        });
}
document.addEventListener('DOMContentLoaded', function() {
    if (typeof initCalendar === 'function') initCalendar();
    if (typeof initCharts === 'function') initCharts();
});

