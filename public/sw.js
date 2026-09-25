const CACHE_NAME = 'fooddash-v1';

// ============================================
// INSTALL
// ============================================
self.addEventListener('install', (e) => {
    self.skipWaiting();
});

// ============================================
// ACTIVATE
// ============================================
self.addEventListener('activate', (e) => {
    e.waitUntil(self.clients.claim());
});

// ============================================
// FETCH (pass-through)
// ============================================
self.addEventListener('fetch', (e) => {
    // Pass-through — offline support later
});

// ============================================
// PUSH EVENT (pag may bagong push notification)
// ============================================
self.addEventListener('push', (e) => {
    if (!e.data) return;

    let data = {};
    try {
        data = e.data.json();
    } catch (err) {
        data = { title: 'FoodDash', body: e.data.text() };
    }

    const options = {
        body: data.body || '',
        icon: data.icon || '/icon-192.png',
        badge: '/icon-192.png',
        vibrate: [200, 100, 200],
        tag: data.tag || 'fooddash-notification',
        data: {
            url: data.url || '/',
        },
        actions: data.actions || [],
    };

    e.waitUntil(
        self.registration.showNotification(data.title || 'FoodDash', options)
    );
});

// ============================================
// NOTIFICATION CLICK (pag clinick ang notification)
// ============================================
self.addEventListener('notificationclick', (e) => {
    e.notification.close();

    const urlToOpen = e.notification.data?.url || '/';

    e.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Kung may nakabukas na window, i-focus at i-navigate
                for (const client of clientList) {
                    if ('focus' in client) {
                        client.focus();
                        if ('navigate' in client) {
                            client.navigate(urlToOpen);
                        }
                        return;
                    }
                }
                // Kung wala, magbukas ng bagong window
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});