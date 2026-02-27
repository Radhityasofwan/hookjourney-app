const CACHE_NAME = 'hookjourney-v1';
const ASSETS = [
    './',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
    'https://unpkg.com/@phosphor-icons/web',
    'https://unpkg.com/htmx.org@1.9.10'
];

// Install & Cache Core Assets
self.addEventListener('install', (e) => {
    e.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[ServiceWorker] Caching App Shell');
            return cache.addAll(ASSETS);
        })
    );
});

// Activate & Cleanup Old Caches
self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(keyList.map((key) => {
                if (key !== CACHE_NAME) {
                    console.log('[ServiceWorker] Removing old cache', key);
                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});

// Stale-while-revalidate Strategy
self.addEventListener('fetch', (e) => {
    // Only cache GET requests
    if (e.request.method !== 'GET') return;
    
    // Ignore chrome-extension and other non-http schemes
    if (!e.request.url.startsWith('http')) return;

    e.respondWith(
        caches.match(e.request).then((cachedResponse) => {
            const fetchPromise = fetch(e.request).then((networkResponse) => {
                // Return original response immediately if not OK
                if(!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                    if (e.request.url.includes('unpkg.com') || e.request.url.includes('cdn.jsdelivr.net')) {
                        // external resources might not have type='basic'
                        caches.open(CACHE_NAME).then((cache) => cache.put(e.request, networkResponse.clone()));
                        return networkResponse;
                    }
                    return networkResponse;
                }
                
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(e.request, networkResponse.clone());
                });
                return networkResponse;
            }).catch(() => {
                // If offline, returning the cached response, or fallback if available
                return cachedResponse;
            });

            return cachedResponse || fetchPromise;
        })
    );
});
