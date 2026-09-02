// Progressive Web App (PWA) Service Worker for Push Notifications ONLY
// Version 3 - Fixed action button handling

const SW_VERSION = 'v3';

// Install Event - Skip waiting to activate immediately
self.addEventListener('install', (event) => {
    console.log('[SW ' + SW_VERSION + '] Installing...');
    self.skipWaiting();
});

// Activate Event - Claim control immediately
self.addEventListener('activate', (event) => {
    console.log('[SW ' + SW_VERSION + '] Activated.');
    event.waitUntil(self.clients.claim());
});

// Push Event - Receive and display notification
self.addEventListener('push', (event) => {
    if (!event.data) {
        return;
    }

    try {
        const payload = event.data.json();
        const title = payload.title || 'New Order Received! 📦';

        // Build actions array - NOTE: Android shows actions left-to-right
        const notifActions = (payload.orderId && payload.orderId !== 999) ? [
            { action: 'btn_reject', title: 'Reject ❌' },
            { action: 'btn_accept', title: 'Accept ✅' }
        ] : [];

        const options = {
            body: payload.body || 'You have a new order pending on Dawalo.',
            icon: payload.icon || '/assets/icon-192.png',
            badge: payload.badge || '/assets/favicon.png',
            data: {
                url: payload.url || '/shop/orders',
                orderId: payload.orderId || null
            },
            vibrate: [500, 110, 500, 110, 450, 110, 200, 110, 170, 40],
            requireInteraction: true,
            actions: notifActions
        };

        event.waitUntil(
            self.registration.showNotification(title, options)
        );
    } catch (e) {
        // Fallback if payload is not JSON
        const text = event.data.text();
        event.waitUntil(
            self.registration.showNotification('Dawalo Notification 💊', {
                body: text,
                icon: '/assets/icon-192.png',
                badge: '/assets/favicon.png',
                data: { url: '/shop/orders' }
            })
        );
    }
});

// Notification Click Event
self.addEventListener('notificationclick', (event) => {
    const action = event.action;
    const notifData = event.notification.data || {};
    const orderId = notifData.orderId || null;

    console.log('[SW ' + SW_VERSION + '] notificationclick fired. action="' + action + '" orderId=' + orderId);

    event.notification.close();

    // ── ACCEPT button ──────────────────────────────────────────────────────
    if (action === 'btn_accept') {
        console.log('[SW] Sending ACCEPT (Accepted) for order', orderId);
        event.waitUntil(
            fetch('/api/order/status-background', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: 'Accepted' })
            })
            .then(r => r.json())
            .then(data => {
                const msg = data.success
                    ? 'Order #' + orderId + ' Accepted ✅ Successfully!'
                    : 'Accept failed: ' + (data.message || 'Unknown error');
                return self.registration.showNotification('Dawalo', {
                    body: msg, icon: '/assets/icon-192.png', badge: '/assets/favicon.png'
                });
            })
            .catch(() => self.registration.showNotification('Dawalo', {
                body: 'Network error. Could not accept order #' + orderId,
                icon: '/assets/icon-192.png', badge: '/assets/favicon.png'
            }))
        );
        return;
    }

    // ── REJECT button ──────────────────────────────────────────────────────
    if (action === 'btn_reject') {
        console.log('[SW] Sending REJECT (Cancelled) for order', orderId);
        event.waitUntil(
            fetch('/api/order/status-background', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: 'Cancelled' })
            })
            .then(r => r.json())
            .then(data => {
                const msg = data.success
                    ? 'Order #' + orderId + ' Rejected ❌ Successfully!'
                    : 'Reject failed: ' + (data.message || 'Unknown error');
                return self.registration.showNotification('Dawalo', {
                    body: msg, icon: '/assets/icon-192.png', badge: '/assets/favicon.png'
                });
            })
            .catch(() => self.registration.showNotification('Dawalo', {
                body: 'Network error. Could not reject order #' + orderId,
                icon: '/assets/icon-192.png', badge: '/assets/favicon.png'
            }))
        );
        return;
    }

    // ── BODY click (no action button) - open app ───────────────────────────
    const targetUrl = notifData.url || '/shop/orders';
    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(targetUrl);
            }
        })
    );
});
