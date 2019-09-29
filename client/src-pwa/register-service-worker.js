import { register } from 'register-service-worker';

// The ready(), registered(), cached(), updatefound() and updated()
// events passes a ServiceWorkerRegistration instance in their arguments.
// ServiceWorkerRegistration: https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration

register(process.env.SERVICE_WORKER_FILE, {
    // The registrationOptions object will be passed as the second argument
    // to ServiceWorkerContainer.register()
    // https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerContainer/register#Parameter

    // registrationOptions: { scope: './' },

    ready() {
        devlog.log('App is being served from cache by a service worker.');
    },

    registered(registration) {
        devlog.log('Service worker has been registered.', registration);
    },

    cached(registration) {
        devlog.log('Content has been cached for offline use.', registration);
    },

    updatefound(registration) {
        devlog.log('New content is downloading.', registration);
    },

    updated(registration) {
        devlog.log('New content is available; please refresh.', registration);
    },

    offline() {
        devlog.log('No internet connection found. App is running in offline mode.');
    },

    error(err) {
        devlog.error('Error during service worker registration:', err);
    },
});
