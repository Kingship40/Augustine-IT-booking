// Bump this whenever you change cached files so old caches get cleared out.
const CACHE_VERSION = 'v1';
const STATIC_CACHE = `itbooking-static-${CACHE_VERSION}`;
const RUNTIME_CACHE = `itbooking-runtime-${CACHE_VERSION}`;

// Files that make up the app shell — safe to cache aggressively since they
// rarely change and are needed for the app to boot and to show something
// while offline.
const PRECACHE_URLS = [
    'manifest.json',
    'offline.html',
    'assets/icon-192.png',
    'assets/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key !== STATIC_CACHE && key !== RUNTIME_CACHE)
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only handle GET requests; let POST (logins, form submits, etc.) go
    // straight to the network untouched.
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Ignore cross-origin requests entirely.
    if (url.origin !== self.location.origin) {
        return;
    }

    // Page navigations (index.php?route=...): try the network first so
    // logged-in/dashboard content stays fresh, fall back to cache, then to
    // the offline page if nothing is available.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const responseClone = response.clone();
                    caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, responseClone));
                    return response;
                })
                .catch(() =>
                    caches.match(request).then((cached) => cached || caches.match('offline.html'))
                )
        );
        return;
    }

    // Static assets (icons, manifest, css/js if you add them later):
    // cache-first for speed, refresh the cache in the background.
    const isStaticAsset = /\.(?:png|jpg|jpeg|svg|gif|webp|ico|css|js|woff2?)$/.test(url.pathname)
        || url.pathname.endsWith('manifest.json');

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cached) => {
                const networkFetch = fetch(request)
                    .then((response) => {
                        const responseClone = response.clone();
                        caches.open(STATIC_CACHE).then((cache) => cache.put(request, responseClone));
                        return response;
                    })
                    .catch(() => cached);

                return cached || networkFetch;
            })
        );
        return;
    }

    // Everything else: network-first with a runtime-cache fallback.
    event.respondWith(
        fetch(request)
            .then((response) => {
                const responseClone = response.clone();
                caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, responseClone));
                return response;
            })
            .catch(() => caches.match(request))
    );
});
