<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#0d9488">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>Diyabet Takip</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body>

    {{-- LOGIN --}}
    <div id="login-screen" class="screen-login">
        <div class="login-bg"></div>
        <div class="login-content">
            <div class="login-brand">
                <div class="login-logo"><i data-lucide="heart-pulse"></i></div>
                <h1>Diyabet Takip</h1>
                <p>Profesyonel sağlık yönetim platformu</p>
            </div>
            <div class="login-card">
                <div id="login-error" class="alert-error hidden"></div>
                <label class="field-label">E-posta adresi</label>
                <input type="email" id="email" class="field-input" placeholder="ornek@email.com" value="demo@diyabet.app" autocomplete="email">
                <label class="field-label">Şifre</label>
                <input type="password" id="password" class="field-input" placeholder="••••••••" value="password" autocomplete="current-password">
                <button class="btn-main" onclick="login()">
                    <i data-lucide="log-in"></i> Giriş Yap
                </button>
            </div>
            <p class="login-footer">Demo: demo@diyabet.app / password</p>
        </div>
    </div>

    {{-- APP --}}
    <div id="app-screen" class="hidden">
        <header class="app-header">
            <div class="header-row">
                <div>
                    <p class="header-greet">Hoş geldin</p>
                    <h1 id="user-name">Kullanıcı</h1>
                </div>
                <button class="avatar-btn" id="user-avatar" onclick="logout()" title="Çıkış">D</button>
            </div>

            <div class="glucose-hero" id="glucose-hero">
                <div class="glucose-hero-left">
                    <span class="glucose-label">Son glukoz ölçümü</span>
                    <div class="glucose-value-wrap">
                        <span class="glucose-value" id="last-value">—</span>
                        <span class="glucose-unit">mg/dL</span>
                    </div>
                    <span class="status-pill" id="last-status">Yükleniyor</span>
                </div>
                <div class="glucose-ring" id="glucose-ring">
                    <svg viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="42" class="ring-bg"/>
                        <circle cx="50" cy="50" r="42" class="ring-fill" id="ring-fill" stroke-dasharray="264" stroke-dashoffset="264"/>
                    </svg>
                    <span class="ring-text" id="ring-percent">—</span>
                </div>
            </div>

            <div class="mini-stats" id="mini-stats">
                <div class="mini-stat"><span class="ms-val" id="stat-avg">—</span><span class="ms-lbl">7g ort.</span></div>
                <div class="mini-stat"><span class="ms-val" id="stat-range">—</span><span class="ms-lbl">Hedefte</span></div>
                <div class="mini-stat"><span class="ms-val" id="stat-count">—</span><span class="ms-lbl">Ölçüm</span></div>
            </div>
        </header>

        <main class="app-body">
            <div id="tab-home">
                <section class="section">
                    <div class="section-head">
                        <h2>Haftalık Trend</h2>
                        <span class="section-badge" id="chart-badge">7 gün</span>
                    </div>
                    <div class="chart-card">
                        <canvas id="trendChart" height="160"></canvas>
                    </div>
                </section>

                <section class="section">
                    <div class="section-head"><h2>Randevular</h2></div>
                    <div class="list-card" id="appt-data">
                        <div class="skeleton-wrap">@for($i=0;$i<2;$i++)<div class="skeleton-row"></div>@endfor</div>
                    </div>
                </section>

                <section class="section">
                    <div class="section-head"><h2>İlaç Programı</h2></div>
                    <div class="list-card" id="meds-preview-data">
                        <div class="skeleton-wrap">@for($i=0;$i<2;$i++)<div class="skeleton-row"></div>@endfor</div>
                    </div>
                </section>
            </div>

            <div id="tab-sugar" class="hidden">
                <section class="section">
                    <div class="section-head"><h2>Yeni Ölçüm Kaydı</h2></div>
                    <div class="form-card">
                        <label class="field-label">Glukoz değeri (mg/dL)</label>
                        <input type="number" id="sugar-value" class="field-input field-lg" placeholder="ör. 110" inputmode="decimal">
                        <label class="field-label">Ölçüm tipi</label>
                        <div class="chip-group" id="context-chips">
                            <button type="button" class="chip active" data-value="fasting">Açlık</button>
                            <button type="button" class="chip" data-value="before_meal">Öncesi</button>
                            <button type="button" class="chip" data-value="after_meal">Sonrası</button>
                            <button type="button" class="chip" data-value="bedtime">Gece</button>
                        </div>
                        <input type="hidden" id="sugar-context" value="fasting">
                        <button class="btn-main" onclick="addSugar()"><i data-lucide="save"></i> Kaydet</button>
                    </div>
                </section>
                <section class="section">
                    <div class="section-head"><h2>Ölçüm Geçmişi</h2></div>
                    <div class="list-card" id="sugar-data"></div>
                </section>
            </div>

            <div id="tab-meal" class="hidden">
                <section class="section">
                    <div class="section-head"><h2>Günlük Beslenme</h2></div>
                    <div class="meal-card-pro" id="meal-card">
                        <div class="skeleton-wrap"><div class="skeleton-row" style="height:80px"></div></div>
                    </div>
                </section>
            </div>

            <div id="tab-meds" class="hidden">
                <section class="section">
                    <div class="section-head"><h2>Aktif İlaçlar</h2></div>
                    <div class="list-card" id="meds-card">
                        <div class="skeleton-wrap">@for($i=0;$i<3;$i++)<div class="skeleton-row"></div>@endfor</div>
                    </div>
                </section>
            </div>
        </main>

        <button class="fab" id="fab" onclick="openQuickAdd()"><i data-lucide="plus"></i></button>

        <nav class="bottom-nav">
            <button class="nav-btn active" data-tab="home" onclick="showTab('home')">
                <i data-lucide="layout-dashboard"></i><span>Ana Sayfa</span>
            </button>
            <button class="nav-btn" data-tab="sugar" onclick="showTab('sugar')">
                <i data-lucide="activity"></i><span>Glukoz</span>
            </button>
            <button class="nav-btn" data-tab="meal" onclick="showTab('meal')">
                <i data-lucide="utensils"></i><span>Diyet</span>
            </button>
            <button class="nav-btn" data-tab="meds" onclick="showTab('meds')">
                <i data-lucide="pill"></i><span>İlaç</span>
            </button>
        </nav>
    </div>

    <div class="toast" id="toast"></div>

    <script src="{{ asset('js/mobile-app.js') }}"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
