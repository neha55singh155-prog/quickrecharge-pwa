/**
 * Service Worker — Premium PWA
 * Offline support, caching, background sync
 */

var CACHE_VERSION = 'v3';
var STATIC_CACHE = 'recharge-static-' + CACHE_VERSION;
var DYNAMIC_CACHE = 'recharge-dynamic-' + CACHE_VERSION;
var API_CACHE = 'recharge-api-' + CACHE_VERSION;

var BASE = '/PWE/recharge-app/';

var STATIC_ASSETS = [
    BASE,
    BASE + 'index.php',
    BASE + 'assets/css/base.css',
    BASE + 'assets/css/home.css',
    BASE + 'assets/css/verify.css',
    BASE + 'assets/css/plans.css',
    BASE + 'assets/css/checkout.css',
    BASE + 'assets/css/payment.css',
    BASE + 'assets/css/success.css',
    BASE + 'assets/css/failed.css',
    BASE + 'assets/css/splash.css',
    BASE + 'assets/js/app.js',
    BASE + 'assets/js/router.js',
    BASE + 'pwa/manifest.json',
    BASE + 'assets/icons/icon-192.png',
    BASE + 'assets/icons/icon-512.png',
];

var PAGES = [
    'pages/home.php',
    'pages/verify.php',
    'pages/plans.php',
    'pages/checkout.php',
    'pages/payment.php',
    'pages/success.php',
    'pages/failed.php',
    'pages/splash.php',
];

// Install
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(function(cache) {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(function() {
                return caches.open(DYNAMIC_CACHE);
            })
            .then(function(cache) {
                return cache.addAll(PAGES.map(function(p) { return BASE + p; }));
            })
            .then(function() {
                return self.skipWaiting();
            })
    );
});

// Activate
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys()
            .then(function(keys) {
                return Promise.all(
                    keys.filter(function(key) {
                        return key !== STATIC_CACHE && key !== DYNAMIC_CACHE && key !== API_CACHE;
                    }).map(function(key) {
                        console.log('[SW] Deleting old cache:', key);
                        return caches.delete(key);
                    })
                );
            })
            .then(function() {
                return self.clients.claim();
            })
    );
});

// Fetch
self.addEventListener('fetch', function(event) {
    var url = new URL(event.request.url);

    // Skip non-GET
    if (event.request.method !== 'GET') return;

    // Skip chrome-extension
    if (!url.protocol.startsWith('http')) return;

    // API requests — Network first, cache fallback
    if (url.pathname.indexOf('/api/') !== -1) {
        event.respondWith(
            fetch(event.request)
                .then(function(response) {
                    if (response && response.status === 200) {
                        var clone = response.clone();
                        caches.open(API_CACHE).then(function(cache) {
                            cache.put(event.request, clone);
                        });
                    }
                    return response;
                })
                .catch(function() {
                    return caches.match(event.request);
                })
        );
        return;
    }

    // Static assets — Cache first, network fallback
    event.respondWith(
        caches.match(event.request)
            .then(function(cached) {
                if (cached) return cached;

                return fetch(event.request)
                    .then(function(response) {
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        var clone = response.clone();
                        var cacheName = url.pathname.match(/\.(css|js|png|jpg|svg|ico|json)$/)
                            ? STATIC_CACHE : DYNAMIC_CACHE;

                        caches.open(cacheName).then(function(cache) {
                            cache.put(event.request, clone);
                        });

                        return response;
                    })
                    .catch(function() {
                        // Offline fallback for navigation
                        if (event.request.mode === 'navigate') {
                            return caches.match(BASE);
                        }
                        return new Response('Offline', {
                            status: 503,
                            headers: { 'Content-Type': 'text/plain' }
                        });
                    });
            })
    );
});

// Push notifications
self.addEventListener('push', function(event) {
    var data = event.data ? event.data.json() : {};
    var options = {
        body: data.body || 'Your recharge was successful!',
        icon: BASE + 'assets/icons/icon-192.png',
        badge: BASE + 'assets/icons/icon-72.png',
        vibrate: [100, 50, 100],
        tag: data.tag || 'recharge-notification',
        data: { url: BASE, ...data },
        actions: [
            { action: 'open', title: 'Open App' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'QuickRecharge', options)
    );
});

// Notification click
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(function(clientList) {
            for (var i = 0; i < clientList.length; i++) {
                if (clientList[i].url.indexOf(BASE) !== -1 && 'focus' in clientList[i]) {
                    return clientList[i].focus();
                }
            }
            return clients.openWindow(BASE);
        })
    );
});

// Background sync
self.addEventListener('sync', function(event) {
    if (event.tag === 'sync-recharge') {
        event.waitUntil(syncPendingRecharges());
    }
});

function syncPendingRecharges() {
    return caches.open('pending-recharges').then(function(cache) {
        return cache.keys().then(function(requests) {
            return Promise.all(requests.map(function(request) {
                return cache.match(request).then(function(response) {
                    return response.json();
                }).then(function(data) {
                    return fetch(BASE + 'api/create-order', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    }).then(function() {
                        return cache.delete(request);
                    });
                });
            }));
        });
    });
}
