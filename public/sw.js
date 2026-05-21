'use strict';

var CACHE_NAME = 'siprt-v4';
var OFFLINE_URL = '/offline';

// App shell resources pre-cached on install
var APP_SHELL = [
    OFFLINE_URL,
    '/css/public.css',
    '/css/public.js',
    '/favicon/android-chrome-192x192.png',
    '/favicon/android-chrome-512x512.png',
    '/favicon/favicon-32x32.png',
    'https://fonts.bunny.net/css?family=Nunito:400,500,600,700',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css',
    'https://code.jquery.com/jquery-3.7.1.min.js',
];

// ── Install: pre-cache app shell ──
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) { return cache.addAll(APP_SHELL); })
            .then(function() { return self.skipWaiting(); })
            .catch(function(err) { console.warn('[SW] Pre-cache failed:', err); })
    );
});

// ── Activate: prune old caches ──
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys()
            .then(function(keys) {
                return Promise.all(
                    keys.filter(function(k) { return k !== CACHE_NAME; })
                        .map(function(k) { return caches.delete(k); })
                );
            })
            .then(function() { return self.clients.claim(); })
    );
});

// ── Fetch ──
self.addEventListener('fetch', function(event) {
    var req = event.request;

    // Only handle GET
    if (req.method !== 'GET') return;

    // Pass through Ajax/DataTables requests
    if (req.headers.get('X-Requested-With') === 'XMLHttpRequest') return;

    // Pass through auth endpoints
    var url = new URL(req.url);
    if (url.pathname === '/login' || url.pathname === '/logout' ||
        url.pathname === '/broadcasting/auth') return;

    // Static assets (CSS, JS, images, fonts) — cache-first, then network
    if (isStaticAsset(url)) {
        event.respondWith(cacheFirst(req));
        return;
    }

    // HTML pages — network-first with offline fallback
    if (req.mode === 'navigate') {
        event.respondWith(networkFirstWithFallback(req));
        return;
    }
});

function isStaticAsset(url) {
    return url.hostname !== location.hostname ||
        /\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)(\?.*)?$/.test(url.pathname);
}

async function cacheFirst(request) {
    var cached = await caches.match(request);
    if (cached) return cached;
    try {
        var response = await fetch(request);
        if (response.ok) {
            var cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch (err) {
        return new Response('', { status: 503 });
    }
}

async function networkFirstWithFallback(request) {
    try {
        var response = await fetch(request);
        if (response.ok) {
            var cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch (err) {
        var cached = await caches.match(request);
        if (cached) return cached;
        var offline = await caches.match(OFFLINE_URL);
        return offline || new Response('<h1>Offline</h1>', { headers: { 'Content-Type': 'text/html' } });
    }
}

// ── Background sync: laporan queue ──
self.addEventListener('sync', function(event) {
    if (event.tag === 'laporan-sync') {
        event.waitUntil(syncPendingLaporan());
    }
});

async function syncPendingLaporan() {
    var db = await openDB();
    var items = await getAllItems(db, 'laporan-queue');
    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        try {
            var fd = new FormData();
            fd.append('_token', item.csrf_token);
            fd.append('task_id', item.task_id);
            fd.append('description', item.description);
            if (item.signature_tech) fd.append('signature_tech', item.signature_tech);
            if (item.signature_cust) fd.append('signature_cust', item.signature_cust);
            if (item.photo_blob) fd.append('photo', item.photo_blob, item.photo_name || 'photo.jpg');

            var resp = await fetch('/laporan', {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
            });

            if (resp.ok || resp.redirected) {
                await deleteItem(db, 'laporan-queue', item.id);
                self.registration.showNotification('SIPRT', {
                    body: 'Laporan offline berhasil dikirim.',
                    icon: '/favicon/android-chrome-192x192.png',
                });
            }
        } catch (err) {
            // Will retry on next sync event
        }
    }
}

// ── IndexedDB helpers ──
function openDB() {
    return new Promise(function(resolve, reject) {
        var req = indexedDB.open('siprt-offline', 1);
        req.onupgradeneeded = function(e) {
            var db = e.target.result;
            if (!db.objectStoreNames.contains('laporan-queue')) {
                db.createObjectStore('laporan-queue', { keyPath: 'id', autoIncrement: true });
            }
        };
        req.onsuccess = function(e) { resolve(e.target.result); };
        req.onerror   = function(e) { reject(e.target.error); };
    });
}

function getAllItems(db, store) {
    return new Promise(function(resolve, reject) {
        var tx = db.transaction(store, 'readonly');
        var req = tx.objectStore(store).getAll();
        req.onsuccess = function(e) { resolve(e.target.result); };
        req.onerror   = function(e) { reject(e.target.error); };
    });
}

function deleteItem(db, store, id) {
    return new Promise(function(resolve, reject) {
        var tx = db.transaction(store, 'readwrite');
        var req = tx.objectStore(store).delete(id);
        req.onsuccess = function() { resolve(); };
        req.onerror   = function(e) { reject(e.target.error); };
    });
}
