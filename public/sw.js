const CACHE_NAME = 'sirukti-pwa-v1';
const STATIC_ASSETS = [
    '/manifest.webmanifest',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/icon.svg'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Hanya tangani request GET; biarkan POST/PUT/DELETE (ber-CSRF) langsung ke jaringan
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);

    // Hanya tangani origin yang sama
    if (url.origin !== self.location.origin) {
        return;
    }

    // Strategi Network-First agar data status surat dan token CSRF selalu segar
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});
