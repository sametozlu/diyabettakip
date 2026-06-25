<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0d9488">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>@yield('title', __('Panel')) — {{ __('Diyabet Takip') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/health.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <div class="brand-mark"><i data-lucide="heart-pulse"></i></div>
                <div>
                    <h1>{{ __('Diyabet Takip') }}</h1>
                    <span>Health Platform</span>
                </div>
            </div>

            <div class="nav-section">{{ __('Ana Menü') }}</div>
            <nav class="nav-links">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i> {{ __('Panel') }}
                </a>
                <a href="{{ route('daily-summary') }}" class="{{ request()->routeIs('daily-summary') ? 'active' : '' }}">
                    <i data-lucide="sun"></i> {{ __('Günlük Özet') }}
                </a>
                <a href="{{ route('blood-sugar.index') }}" class="{{ request()->routeIs('blood-sugar.*') ? 'active' : '' }}">
                    <i data-lucide="activity"></i> {{ __('Kan Şekeri') }}
                </a>
                <a href="{{ route('meals.index') }}" class="{{ request()->routeIs('meals.*') ? 'active' : '' }}">
                    <i data-lucide="utensils"></i> {{ __('Diyet Planı') }}
                </a>
                <a href="{{ route('appointments.index') }}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                    <i data-lucide="calendar-clock"></i> {{ __('Randevular') }}
                </a>
            </nav>

            <div class="nav-section">{{ __('Takip') }}</div>
            <nav class="nav-links">
                <a href="{{ route('insulin.index') }}" class="{{ request()->routeIs('insulin.*') ? 'active' : '' }}">
                    <i data-lucide="syringe"></i> {{ __('İnsülin / Karb') }}
                </a>
                <a href="{{ route('food.index') }}" class="{{ request()->routeIs('food.*') ? 'active' : '' }}">
                    <i data-lucide="camera"></i> {{ __('Yemek Kaydı') }}
                </a>
                <a href="{{ route('exercise.index') }}" class="{{ request()->routeIs('exercise.*') ? 'active' : '' }}">
                    <i data-lucide="footprints"></i> {{ __('Egzersiz') }}
                </a>
                <a href="{{ route('water.index') }}" class="{{ request()->routeIs('water.*') ? 'active' : '' }}">
                    <i data-lucide="droplets"></i> {{ __('Su Takibi') }}
                </a>
                <a href="{{ route('medications.index') }}" class="{{ request()->routeIs('medications.*') ? 'active' : '' }}">
                    <i data-lucide="pill"></i> {{ __('İlaçlar') }}
                </a>
                <a href="{{ route('hba1c.index') }}" class="{{ request()->routeIs('hba1c.*') ? 'active' : '' }}">
                    <i data-lucide="trending-up"></i> HbA1c
                </a>
            </nav>

            <div class="nav-section">{{ __('Araçlar') }}</div>
            <nav class="nav-links">
                <a href="{{ route('health-profile.edit') }}" class="{{ request()->routeIs('health-profile.*') ? 'active' : '' }}">
                    <i data-lucide="user-cog"></i> {{ __('Sağlık Profili') }}
                </a>
                <a href="{{ route('report.pdf') }}">
                    <i data-lucide="file-text"></i> {{ __('PDF Rapor') }}
                </a>
                <a href="{{ route('share.index') }}" class="{{ request()->routeIs('share.*') ? 'active' : '' }}">
                    <i data-lucide="share-2"></i> {{ __('Doktor Paylaşımı') }}
                </a>
                <a href="{{ route('export.json') }}">
                    <i data-lucide="download"></i> JSON
                </a>
                <a href="{{ route('export.csv') }}">
                    <i data-lucide="table"></i> CSV
                </a>
                <a href="{{ route('mobile') }}" target="_blank">
                    <i data-lucide="smartphone"></i> {{ __('Mobil Uygulama') }}
                </a>
            </nav>

            <div class="sidebar-tools">
                <div class="tool-row">
                    <a href="{{ route('locale.switch', 'tr') }}" class="locale-btn {{ app()->getLocale() === 'tr' ? 'active' : '' }}">TR</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="locale-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    <button type="button" class="theme-toggle" onclick="toggleTheme()" title="{{ __('Tema') }}">
                        <i data-lucide="moon"></i>
                    </button>
                    <button type="button" class="theme-toggle" onclick="subscribePush()" title="{{ __('Bildirimler') }}">
                        <i data-lucide="bell"></i>
                    </button>
                </div>
            </div>

            <div class="sidebar-footer">
                <div class="user-chip">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div>
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="email">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-block">
                        <i data-lucide="log-out"></i> {{ __('Çıkış') }}
                    </button>
                </form>
            </div>
        </aside>

        <div class="main-wrap">
            <header class="page-header">
                <div class="page-header-top">
                    <div>
                        <h2>@yield('hero_title', __('Sağlık Paneli'))</h2>
                        <p>@yield('hero_subtitle', __('Günlük sağlık verileriniz ve analizler'))</p>
                    </div>
                    <div class="page-meta">
                        <i data-lucide="calendar" style="width:14px;height:14px;"></i>
                        {{ now()->locale(app()->getLocale())->isoFormat('D MMMM YYYY, dddd') }}
                    </div>
                </div>
                @yield('hero_stats')
            </header>

            <main>
                @if (session('success'))
                    <div class="alert alert-success">
                        <i data-lucide="check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-error">
                        <i data-lucide="alert-circle"></i>
                        <div>@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')" aria-label="Menü">
        <i data-lucide="menu"></i>
    </button>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            lucide.createIcons();
        }
        const saved = localStorage.getItem('theme');
        if (saved) document.documentElement.setAttribute('data-theme', saved);
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
        lucide.createIcons();
    </script>
    <script src="{{ asset('js/push-notifications.js') }}"></script>
    @stack('scripts')
</body>
</html>
