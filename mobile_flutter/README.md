# Flutter Mobil Uygulama (API Entegrasyonu)

Bu klasör, **Diyabet Takip** REST API'si ile çalışacak Flutter mobil uygulaması için başlangıç rehberidir.

## API Base URL

```
http://localhost:8000/api
```

## Kimlik Doğrulama (Sanctum)

```dart
// POST /api/login
final response = await http.post(
  Uri.parse('$baseUrl/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'demo@diyabet.app',
    'password': 'password',
    'device_name': 'flutter',
  }),
);
final token = jsonDecode(response.body)['token'];
```

Sonraki isteklerde: `Authorization: Bearer $token`

## Temel Endpoint'ler

| Endpoint | Açıklama |
|----------|----------|
| `GET /api/me` | Kullanıcı + sağlık profili |
| `GET /api/daily-summary` | Günlük özet |
| `GET /api/alerts` | Glukoz uyarıları + tahmini HbA1c |
| `GET /api/blood-sugar` | Glukoz kayıtları |
| `POST /api/blood-sugar` | Yeni ölçüm |
| `GET /api/water/today` | Su takibi |
| `GET /api/exercise/today` | Egzersiz özeti |
| `GET /api/export/json` | Veri dışa aktarma |

## Flutter Projesi Oluşturma

```bash
flutter create diyabet_takip
cd diyabet_takip
flutter pub add http shared_preferences fl_chart
```

## PWA Alternatifi

Hazır mobil arayüz için: `http://localhost:8000/mobile` (PWA + Service Worker)
