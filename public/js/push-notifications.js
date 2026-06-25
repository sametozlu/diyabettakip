async function subscribePush() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        alert('Bu tarayıcı push bildirimlerini desteklemiyor.');
        return;
    }
    try {
        const reg = await navigator.serviceWorker.ready;
        const res = await fetch('/push/vapid-key', { headers: { Accept: 'application/json' } });
        const { publicKey } = await res.json();
        if (!publicKey) {
            alert('Push bildirimleri henüz yapılandırılmadı. İlaç hatırlatmaları log olarak kaydedilir.');
            return;
        }
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return;
        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(publicKey),
        });
        await fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                Accept: 'application/json',
            },
            body: JSON.stringify(sub.toJSON()),
        });
        alert('Bildirimler aktif!');
    } catch (e) {
        console.error(e);
        alert('Bildirim aboneliği başarısız.');
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
}
