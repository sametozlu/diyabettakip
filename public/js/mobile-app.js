const API = '/api';
let token = localStorage.getItem('diyabet_token');
let trendChart = null;
let targetMin = 70;
let targetMax = 140;

async function api(path, options = {}) {
    const headers = { Accept: 'application/json', 'Content-Type': 'application/json', ...(options.headers || {}) };
    if (token) headers.Authorization = 'Bearer ' + token;
    const res = await fetch(API + path, { ...options, headers });
    if (res.status === 401) { logout(); throw new Error('Oturum sona erdi'); }
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || 'Bir hata oluştu');
    return data;
}

function toast(msg, icon = 'check') {
    const el = document.getElementById('toast');
    el.innerHTML = `<i data-lucide="${icon}"></i> ${msg}`;
    lucide.createIcons();
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 2800);
}

function glucoseStatus(v) {
    if (!v && v !== 0) return { text: 'Veri yok', cls: 'normal', pct: 0 };
    const mid = (targetMin + targetMax) / 2;
    const range = targetMax - targetMin;
    let cls = 'normal', text = 'Hedef aralıkta';
    if (v < targetMin) { cls = 'low'; text = 'Düşük'; }
    else if (v > targetMax) { cls = 'high'; text = 'Yüksek'; }
    const pct = Math.min(100, Math.max(0, 100 - Math.abs(v - mid) / range * 100));
    return { text, cls, pct: Math.round(pct) };
}

function updateRing(pct) {
    const circle = document.getElementById('ring-fill');
    const text = document.getElementById('ring-percent');
    const circumference = 264;
    circle.style.strokeDashoffset = circumference - (pct / 100) * circumference;
    text.textContent = pct + '%';
}

async function login() {
    try {
        document.getElementById('login-error').classList.add('hidden');
        const data = await api('/login', {
            method: 'POST',
            body: JSON.stringify({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                device_name: 'mobil-web',
            }),
        });
        token = data.token;
        localStorage.setItem('diyabet_token', token);
        document.getElementById('user-name').textContent = data.user.name.split(' ')[0];
        document.getElementById('user-avatar').textContent = data.user.name.charAt(0).toUpperCase();
        showApp();
    } catch (e) {
        const el = document.getElementById('login-error');
        el.textContent = e.message;
        el.classList.remove('hidden');
    }
}

function logout() {
    token = null;
    localStorage.removeItem('diyabet_token');
    document.getElementById('login-screen').classList.remove('hidden');
    document.getElementById('app-screen').classList.add('hidden');
}

function showApp() {
    document.getElementById('login-screen').classList.add('hidden');
    document.getElementById('app-screen').classList.remove('hidden');
    document.getElementById('fab').classList.add('show');
    loadAll();
}

function showTab(name) {
    ['home', 'sugar', 'meal', 'meds'].forEach(t => {
        document.getElementById('tab-' + t).classList.toggle('hidden', t !== name);
    });
    document.querySelectorAll('.nav-btn').forEach(el => {
        el.classList.toggle('active', el.dataset.tab === name);
    });
    document.getElementById('fab').classList.toggle('show', name === 'home' || name === 'sugar');
}

function openQuickAdd() {
    showTab('sugar');
    setTimeout(() => document.getElementById('sugar-value')?.focus(), 300);
}

document.querySelectorAll('#context-chips .chip').forEach(chip => {
    chip.addEventListener('click', () => {
        document.querySelectorAll('#context-chips .chip').forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        document.getElementById('sugar-context').value = chip.dataset.value;
    });
});

function renderChart(chartData) {
    const ctx = document.getElementById('trendChart');
    if (!ctx) return;
    const labels = chartData.map(d => d.label);
    const values = chartData.map(d => d.average);

    if (trendChart) trendChart.destroy();

    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: values,
                borderColor: '#0d9488',
                backgroundColor: 'rgba(13,148,136,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#0d9488',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                spanGaps: true,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: {
                backgroundColor: '#0f172a',
                padding: 10,
                cornerRadius: 8,
                callbacks: { label: c => c.parsed.y + ' mg/dL' },
            }},
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10, weight: '600' }, color: '#94a3b8' } },
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8' }, suggestedMin: 60, suggestedMax: 180 },
            },
        },
    });
}

function renderSugars(data) {
    const list = Array.isArray(data) ? data : (data.data || []);
    const ctxLabels = { fasting: 'Açlık', before_meal: 'Öncesi', after_meal: 'Sonrası', bedtime: 'Gece', other: 'Diğer' };

    if (list.length) {
        const last = list[0];
        document.getElementById('last-value').textContent = last.value;
        const st = glucoseStatus(last.value);
        const statusEl = document.getElementById('last-status');
        statusEl.textContent = st.text;
        statusEl.className = 'status-pill ' + st.cls;
        updateRing(st.pct);
    }

    const inRange = list.filter(s => s.value >= targetMin && s.value <= targetMax).length;
    const avg = list.length ? Math.round(list.reduce((a, b) => a + b.value, 0) / list.length) : '—';
    document.getElementById('stat-avg').textContent = avg;
    document.getElementById('stat-range').textContent = list.length ? Math.round(inRange / list.length * 100) + '%' : '—';
    document.getElementById('stat-count').textContent = list.length;

    document.getElementById('sugar-data').innerHTML = list.length
        ? list.slice(0, 12).map(s => {
            const st = glucoseStatus(s.value);
            return `<div class="list-item">
                <div class="list-icon ${st.cls === 'normal' ? 'teal' : st.cls === 'high' ? 'amber' : 'indigo'}">
                    <i data-lucide="activity"></i>
                </div>
                <div class="list-body">
                    <div class="title">${ctxLabels[s.context] || s.context}</div>
                    <div class="sub">${new Date(s.measured_at).toLocaleString('tr-TR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })}</div>
                </div>
                <div class="list-value">
                    <strong>${s.value}</strong>
                    <span class="badge-sm ${st.cls}">${st.text}</span>
                </div>
            </div>`;
        }).join('')
        : '<div class="empty"><i data-lucide="inbox"></i><p>Henüz ölçüm yok</p></div>';
    lucide.createIcons();
}

function renderAppts(appts) {
    document.getElementById('appt-data').innerHTML = appts?.length
        ? appts.map(a => `<div class="list-item">
            <div class="list-icon indigo"><i data-lucide="stethoscope"></i></div>
            <div class="list-body">
                <div class="title">${a.doctor_name}</div>
                <div class="sub">${a.specialty || 'Randevu'} · ${a.location || 'Konum belirtilmedi'}</div>
            </div>
            <div class="list-value">
                <span>${new Date(a.scheduled_at).toLocaleString('tr-TR', { day: 'numeric', month: 'short' })}</span>
                <strong style="font-size:0.82rem;">${new Date(a.scheduled_at).toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })}</strong>
            </div>
        </div>`).join('')
        : '<div class="empty"><i data-lucide="calendar-x"></i><p>Yaklaşan randevu yok</p></div>';
}

function renderMeds(meds) {
    const list = Array.isArray(meds) ? meds : [];
    const html = list.length
        ? list.map(m => `<div class="list-item">
            <div class="list-icon teal"><i data-lucide="pill"></i></div>
            <div class="list-body">
                <div class="title">${m.name}</div>
                <div class="sub">${[m.dosage, m.frequency].filter(Boolean).join(' · ') || 'Doz belirtilmedi'}</div>
            </div>
            <div class="list-value">
                <span>${(m.times || []).join(', ') || '—'}</span>
            </div>
        </div>`).join('')
        : '<div class="empty"><i data-lucide="pill-bottle"></i><p>Aktif ilaç yok</p></div>';

    document.getElementById('meds-preview-data').innerHTML = html;
    document.getElementById('meds-card').innerHTML = html;
}

function renderMeal(meal) {
    if (!meal?.menu_items) {
        document.getElementById('meal-card').innerHTML = '<div class="empty"><i data-lucide="utensils-crossed"></i><p>Bugün için plan yok</p></div>';
        return;
    }
    document.getElementById('meal-card').innerHTML = `
        <div class="meal-title">${meal.day_name}</div>
        <div class="meal-date">${new Date(meal.plan_date).toLocaleDateString('tr-TR', { weekday: 'long', day: 'numeric', month: 'long' })}</div>
        <div class="meal-menu">${meal.menu_items}</div>
        <div class="tag-list">
            ${(meal.eat_items || []).map(i => `<span class="tag eat">✓ ${i}</span>`).join('')}
            ${(meal.reduce_items || []).map(i => `<span class="tag reduce">! ${i}</span>`).join('')}
            ${(meal.skip_items || []).map(i => `<span class="tag skip">✕ ${i}</span>`).join('')}
        </div>`;
}

async function loadAll() {
    try {
        const [chart, appts, sugars, meal, meds, profile] = await Promise.all([
            api('/blood-sugar/chart/weekly'),
            api('/appointments/upcoming'),
            api('/blood-sugar'),
            api('/meals/today'),
            api('/medications/today'),
            api('/me'),
        ]);

        if (profile.health_profile) {
            targetMin = profile.health_profile.target_min || 70;
            targetMax = profile.health_profile.target_max || 140;
        }

        renderChart(chart.chart || []);
        renderSugars(sugars.data || sugars);
        renderAppts(appts.upcoming);
        renderMeds(meds);
        renderMeal(meal.menu_items ? meal : null);
        lucide.createIcons();
    } catch (e) { console.error(e); }
}

async function addSugar() {
    const value = document.getElementById('sugar-value').value;
    if (!value) { toast('Lütfen değer girin', 'alert-circle'); return; }
    try {
        await api('/blood-sugar', {
            method: 'POST',
            body: JSON.stringify({
                value: parseFloat(value),
                context: document.getElementById('sugar-context').value,
                measured_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
            }),
        });
        document.getElementById('sugar-value').value = '';
        toast('Ölçüm kaydedildi');
        const [sugars, chart] = await Promise.all([api('/blood-sugar'), api('/blood-sugar/chart/weekly')]);
        renderSugars(sugars.data || sugars);
        renderChart(chart.chart || []);
    } catch (e) { toast(e.message, 'alert-circle'); }
}

if (token) {
    api('/me').then(u => {
        document.getElementById('user-name').textContent = u.name.split(' ')[0];
        document.getElementById('user-avatar').textContent = u.name.charAt(0).toUpperCase();
        showApp();
    }).catch(() => logout());
}
