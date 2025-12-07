const CACHE_NAME = 'rms-v1';
const urlsToCache = [
    '/',
    '/dashboard',
    '/offline',
    '/css/app.css',
    '/js/app.js',
    '/images/school-logo.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }
                return fetch(event.request).catch(() => {
                    // Start of fallback logic
                    if (event.request.mode === 'navigate') {
                        return caches.match('/dashboard') || caches.match('/');
                    }
                });
            })
    );
});
