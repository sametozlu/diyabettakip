<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-red?style=for-the-badge&logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.3-blue?style=for-the-badge&logo=php" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="MIT">
</p>

# Diyabet Takip

Profesyonel diyabet ve sağlık yönetim platformu. Kan şekeri takibi, diyet planı, ilaç programı, HbA1c analizi, egzersiz/su takibi ve doktor raporları — web paneli, mobil PWA ve REST API ile.

**Repository:** [github.com/sametozlu/diyabettakip](https://github.com/sametozlu/diyabettakip)

---

## Özellikler

### Temel Sağlık Takibi
- **Kan şekeri (glukoz)** — ölçüm kaydı, haftalık trend grafiği, hedef aralık analizi
- **HbA1c** — laboratuvar sonuçları, trend grafiği, tahmini HbA1c (90 günlük ortalama)
- **Diyet planı** — günlük menü, ye/azalt/kaçın önerileri
- **İlaç takibi** — doz, sıklık, günlük saatler
- **Randevular** — doktor kontrolleri, e-posta hatırlatma

### Gelişmiş Takip
- **Sağlık profili** — kilo, boy, BMI, diyabet tipi, glukoz hedefleri
- **İnsülin / karbonhidrat** — öğün bazlı kayıt
- **Egzersiz & adım** — aktivite ve günlük adım hedefi
- **Su takibi** — hızlı ekleme, günlük hedef
- **Uyku & stres** — glukoz kayıtlarına ruh hali, uyku, stres seviyesi

### Analiz & Raporlama
- **Günlük özet** — tek ekranda bugünün tüm verileri
- **Akıllı uyarılar** — akşam yüksek glukoz, hedef dışı ölçüm, düşük glukoz riski
- **PDF rapor** — son 30 gün doktor raporu
- **Doktor paylaşımı** — salt okunur güvenli link
- **Veri dışa aktarma** — JSON ve CSV

### Platform
- **Landing page** — tanıtım sayfası, özellik kartları ve demo giriş
- **Onboarding** — yeni kullanıcılar için 3 adımlı tanıtım slaytı
- **Görsel zenginlik** — hero banner, yemek galerisi, diyet planı fotoğrafları, rozetler
- **İlerleme takibi** — kilo/HbA1c öncesi-sonrası fotoğraflı kayıtlar
- **Haftalık paylaşım kartı** — story formatında özet (`/weekly-story`)
- **Web paneli** — modern dashboard, karanlık mod, TR/EN dil desteği
- **Mobil PWA** — `/mobile` — Chart.js, offline cache, ana ekrana ekleme
- **REST API** — Laravel Sanctum token auth, Flutter/React Native hazır
- **E-posta bildirimleri** — randevu hatırlatma (`appointments:remind`)

---

## Kurulum

### Gereksinimler
- PHP 8.3+
- Composer
- SQLite (varsayılan) veya MySQL

### Adımlar

```bash
git clone https://github.com/sametozlu/diyabettakip.git
cd diyabettakip

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite

php artisan migrate --seed
php artisan serve
```

### Demo Giriş

| Alan | Değer |
|------|-------|
| E-posta | `demo@diyabet.app` |
| Şifre | `password` |

### URL'ler

| Adres | Açıklama |
|-------|----------|
| http://localhost:8000 | Landing + web panel |
| http://localhost:8000/login | Giriş (hero tasarım) |
| http://localhost:8000/weekly-story | Haftalık paylaşım kartı (giriş gerekli) |
| http://localhost:8000/mobile | Mobil PWA |
| http://localhost:8000/api | REST API |

---

## API Kullanımı

### Giriş

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@diyabet.app","password":"password","device_name":"test"}'
```

### Örnek İstekler

```bash
curl -H "Authorization: Bearer TOKEN" http://localhost:8000/api/daily-summary
curl -H "Authorization: Bearer TOKEN" http://localhost:8000/api/alerts
```

### API Endpoint Listesi

| Method | Endpoint | Açıklama |
|--------|----------|----------|
| POST | `/api/login` | Token al |
| GET | `/api/me` | Kullanıcı profili |
| GET | `/api/daily-summary` | Günlük özet |
| GET | `/api/alerts` | Uyarılar + tahmini HbA1c |
| GET/PUT | `/api/health-profile` | Sağlık profili |
| CRUD | `/api/blood-sugar` | Glukoz |
| CRUD | `/api/insulin` | İnsülin/karb |
| CRUD | `/api/exercise` | Egzersiz |
| CRUD | `/api/water` | Su |
| GET | `/api/export/json` | JSON export |
| GET | `/api/export/csv` | CSV export |

---

## Zamanlanmış Görevler

```bash
php artisan appointments:remind
```

### E-posta Ayarı (.env)

```env
MAIL_MAILER=log
MAIL_MAILER=smtp
```

---

## Proje Yapısı

```
app/Http/Controllers/   Web + API
app/Models/             Eloquent modeller
app/Services/           HealthAnalyticsService
resources/views/        Blade şablonları
public/css mobile.js    Stil ve PWA
mobile_flutter/         Flutter API rehberi
```

---

## Karanlık Mod & Dil

- Karanlık mod: sidebar ay ikonu
- Dil: TR / EN — `/locale/tr`, `/locale/en`

---

## Flutter Mobil

Detaylar: [`mobile_flutter/README.md`](mobile_flutter/README.md)

---

## Lisans

MIT — [sametozlu/diyabettakip](https://github.com/sametozlu/diyabettakip)
