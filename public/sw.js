const CACHE = 'diyabet-v2';
const ASSETS = ['/mobile', '/css/mobile.css', '/js/mobile-app.js', '/dashboard'];

self.addEventListener('install', e => {
  e.waitUntil(caches.open(CACHE).then(c => c.addAll(ASSETS)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', e => {
  e.waitUntil(self.clients.claim());
});

self.addEventListener('push', e => {
  let data = { title: 'Diyabet Takip', body: 'Yeni bildirim' };
  try { data = e.data.json(); } catch (_) {}
  e.waitUntil(self.registration.showNotification(data.title, {
    body: data.body,
    icon: '/favicon.ico',
    badge: '/favicon.ico',
    data: data.data || {},
  }));
});

self.addEventListener('notificationclick', e => {
  e.notification.close();
  e.waitUntil(clients.openWindow('/dashboard'));
});

self.addEventListener('fetch', e => {
  if (e.request.method !== 'GET') return;
  e.respondWith(
    caches.match(e.request).then(cached => cached || fetch(e.request).catch(() => caches.match('/mobile')))
  );
});
