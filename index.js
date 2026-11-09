/*
 * =============================================================================
 *  Exponential Service Workers Index in JavaScript
 * =============================================================================
 *
 *  This is the Exponential Service Workers Index: index.js, the one service
 *  worker script of an Exponential site. Every service worker feature of the
 *  site lives in this file (or is loaded from it with importScripts), so there
 *  is one place to read, change or switch off what runs in a visitor's browser
 *  on the site's behalf.
 *
 *  Where it comes from and who uses it
 *  -----------------------------------
 *  - Registered with scope '/' by the site design after page load
 *    (extension/sevenx_themes_media: pagelayout/script_bottom.tpl).
 *  - Served from the document root as a static file, always revalidated:
 *    nginx/Apache (the vhost's location for /index.js, .htaccess_root,
 *    .htaccess_root_static) and Velocity (expVelocity::STATIC_PATHS and its
 *    revalidate list).
 *  - /sw.js was this file's name before. It stays as a one-line script that
 *    loads this one, so a browser registered under the old name runs the same
 *    code until the site registers /index.js over it.
 *
 *  What it contains
 *  ----------------
 *  1. The navigation cache (below): repeat page visits answered from the
 *     browser, refreshed in the background.
 *  2. The packed-asset fallback (before the fetch handler): the last good copy
 *     of each packed stylesheet and script stands in when a cache clear has
 *     deleted the file a stored page names, so no page shows without styles.
 *
 * =============================================================================
 */

/* Exponential — navigation cache.
 *
 * Why this exists
 * ---------------
 * A conditional request for a page this browser already holds is answered in
 * about 0.36 ms of server time. The visitor still waits ~12 ms, because that
 * is the round trip between their machine and the server, and a revalidation
 * is one round trip by definition. No amount of server work reduces it.
 *
 * So the page is served from here instead, and the network is asked afterwards
 * rather than first. A repeat navigation becomes a read out of the Cache API —
 * no connection, no round trip — and the copy is refreshed in the background
 * for next time.
 *
 * This is the same bargain the server makes with stale-while-revalidate, moved
 * to the one place where the round trip can be skipped rather than shortened.
 *
 * What it deliberately does NOT do
 * --------------------------------
 * - It does not touch anything but same-origin GET navigations. Assets already
 *   carry a year-long max-age and are answered from the HTTP cache at 0 ms;
 *   putting them here as well would be a second cache to keep honest for no
 *   gain.
 * - It never serves a response to a request carrying a session cookie. A
 *   signed-in page is that person's, and a shared cache is exactly how one
 *   visitor is shown another's page.
 * - It never caches anything but a 200. An error answered from cache for the
 *   next hour is worse than an error.
 * - It never keeps a copy the server no longer gives out. When the background
 *   refresh gets a 3xx or 4xx (the page was removed, moved or closed to the
 *   public, like ezinfo/about since it answers 404), or a 200 marked private or
 *   no-store, the stored copy is deleted. Before v4 it stood, and the old page
 *   was replayed on every normal navigation while only a hard reload, which
 *   skips the worker, showed the real answer.
 * - It never shows a stored page while the site is being set up or is down
 *   for maintenance. The server then answers every page with a 503 that
 *   carries X-Exp-Maintenance (and Retry-After). The first time the worker
 *   sees one, it deletes every page it holds and takes the browser tab it
 *   had just shown a stored page in to the real answer; until the server
 *   answers with a page again, every navigation goes to the network. Before
 *   v5 a 503 counted as a server fault, the stored copy stood, and the site
 *   was shown on every reload throughout an installation.
 * - It asks the server, not the browser's HTTP cache, when it goes to the
 *   network: a page sent with a max-age was otherwise answered by that cache
 *   on the server's behalf, and never showed that the server had changed its
 *   answer (a 503, a 404).
 * - It stays out of the admin entirely.
 *
 * Turning it off
 * --------------
 * Raise VERSION to invalidate every stored page. To remove it altogether,
 * serve this file with the single line `self.registration.unregister()` — every
 * browser that has it will drop it on its next update check.
 */

// v6: drops every page stored by v5, many of which named packed stylesheets
// that a cache clear had since deleted (pages shown without styles, mostly on
// phones), and adds the packed-asset fallback below.
// v5: drops every page stored by v4, which kept showing the site while it
// answered the maintenance page (503) during an installation.
// v4 dropped every page stored by v3, which never let go of a page that later
// answered 404 (ezinfo/about kept showing to anyone who had seen it before).
const VERSION = 'exp-nav-v6';
const CACHE = VERSION;
// The last good copy of each packed stylesheet and script (see packedAsset()).
// Not versioned with the pages: a copy only ever stands in for a deleted file.
const ASSETS = 'exp-packed-last-good';

// Paths this must never touch.
const OFF_LIMITS = [
	/^\/admin(\/|$)/,
	// the editor siteaccess: the admin for content editing
	/^\/editor(\/|$)/,
	/^\/user(\/|$)/,
	/^\/explayouts_ui/,
	/^\/var\/site\/cache\//,
	/\/api(\/|$)/,
	// The web server's own views and API (dashboard, control panel, shell):
	// signed-in, live, and never the site's to cache or replay.
	/^\/Q\//
];

// Cookies that mean the response is personal. Kept in step with the server's
// own skip list; a page fetched with one of these is never stored or served.
const PRIVATE_COOKIES = ['eZSESSID', 'is_logged_in'];

self.addEventListener('install', (event) => {
	// Take over as soon as this version is ready rather than waiting for every
	// tab to close, so a fix reaches people on their next navigation.
	self.skipWaiting();
});

// The page tells the worker whether this visitor is signed in, because the
// worker cannot read the cookie itself.
self.addEventListener('message', (event) => {
	if (event.data && typeof event.data === 'object'
		&& event.data.type === 'session') {
		self.__sessionHint = !!event.data.signedIn;
	}
});

self.addEventListener('activate', (event) => {
	event.waitUntil((async () => {
		const names = await caches.keys();
		await Promise.all(names.filter((n) => n !== CACHE && n !== ASSETS).map((n) => caches.delete(n)));
		await self.clients.claim();
	})());
});

function hasSessionCookie(request) {
	// Service workers cannot read request.headers.cookie -- it is a forbidden
	// header name -- so this asks the Cookie Store where it is available and
	// falls back to what the page could see otherwise. Either way the server
	// remains the authority: it marks personal responses private or no-store,
	// and mayStore() honours that.
	try {
		if (self.__sessionHint === true) return true;
	} catch (e) {}
	return false;
}

function isPrivate() {
	// The service worker cannot read HttpOnly cookies, so this only catches the
	// readable ones. The server remains the authority: it sets Cache-Control
	// private/no-store on anything personal, and the check below honours that.
	try {
		const jar = self.document ? self.document.cookie : '';
		return PRIVATE_COOKIES.some((c) => jar.indexOf(c) !== -1);
	} catch (e) {
		return false;
	}
}

function mayStore(response) {
	if (!response || response.status !== 200 || response.type === 'opaque') return false;
	const cc = response.headers.get('Cache-Control') || '';
	if (/no-store|private/i.test(cc)) return false;
	return true;
}

// Opened once, reused for every navigation this worker handles.
const cacheHandle = caches.open(CACHE);

// The site answers with its maintenance page (expMaintenance): set up or down
// for maintenance. A 503 without the header is a fault and is treated as one.
function isMaintenance(response) {
	return !!response && response.status === 503
		&& (response.headers.has('X-Exp-Maintenance') || response.headers.has('Retry-After'));
}

// Set while the site answers with its maintenance page: navigations go to the
// network and nothing stored is shown. Cleared by the first page that is not
// the maintenance page. A worker started afresh does not know it, and needs
// not: the pages were deleted when it was set.
let inMaintenance = false;

async function enterMaintenance(cache) {
	inMaintenance = true;
	try {
		const keys = await cache.keys();
		await Promise.all(keys.map((k) => cache.delete(k)));
	} catch (e) {}
}

// To the server, revalidating whatever the browser's HTTP cache holds: a page
// sent with a max-age is otherwise answered from there for that long.
function fromNetwork(request) {
	return fetch(request, { cache: 'no-cache' });
}

async function refresh(cache, request, clientId) {
	try {
		const response = await fromNetwork(request);
		if (isMaintenance(response)) {
			// The page just shown from here is not what the site answers now:
			// drop every stored page, and take the tab to the real answer.
			await enterMaintenance(cache);
			try {
				const client = clientId ? await self.clients.get(clientId) : null;
				if (client && typeof client.navigate === 'function') await client.navigate(client.url);
			} catch (e) {}
			return;
		}
		if (mayStore(response)) {
			await cache.put(request, response.clone());
		} else if (response && response.status < 500) {
			// The page is gone, moved, closed or now personal (a 3xx or 4xx, or a
			// 200 marked private/no-store): the stored copy must go with it, or
			// it is replayed on every normal navigation for good. Only this once
			// more was it shown. A server fault (5xx) keeps the copy.
			await cache.delete(request);
		}
	} catch (e) {
		// Offline: the stored copy stands.
	}
}

/* Packed stylesheets and scripts (ezjscore's packer, var/<site>/cache/public).
 *
 * Their names carry the newest source file's time
 * ([Packer] AppendLastModifiedTime), so a changed stylesheet gets a new name
 * and browsers fetch it. Clearing the packer cache deletes every packed file,
 * though, and a page stored above (or in any other cache) still names the old
 * one: the browser gets a 404 and shows the page without styles. So every
 * packed file that loads is also kept here under its name without the time
 * (md5 of the file list + suffix), and when a packed file answers 404 the
 * last good copy of that same list stands in, and the stored pages are
 * dropped so the next navigation gets the page that names the new file.
 * A copy is written only when the name changed (once per new version), never
 * on the hot path of an asset the browser's HTTP cache already answers.
 */
const PACKED = /^\/var\/[^/]+\/cache\/public\/(stylesheets|javascript)\/([0-9a-f]{32})_\d+(_all)?\.(css|js)$/;
const assetHandle = caches.open(ASSETS);
const assetSeen = {};

function packedKey(url) {
	const m = PACKED.exec(url.pathname);
	return m ? url.origin + '/__packed/' + m[1] + '/' + m[2] + (m[3] || '') + '.' + m[4] : null;
}

async function packedAsset(request, key) {
	const store = await assetHandle;
	let response;
	try {
		response = await fromNetwork(request);
	} catch (e) {
		response = null;
	}
	if (response && response.status === 200) {
		if (assetSeen[key] !== request.url) {
			assetSeen[key] = request.url;
			const copy = response.clone();
			store.put(key, copy).catch(() => {});
		}
		return response;
	}
	const lastGood = await store.match(key);
	if (lastGood) {
		// The page that asked for this file is out of date: let the next
		// navigation fetch it again instead of replaying it.
		try {
			const pages = await cacheHandle;
			const keys = await pages.keys();
			await Promise.all(keys.map((k) => pages.delete(k)));
		} catch (e) {}
		return lastGood;
	}
	return response || new Response('', { status: 504 });
}

self.addEventListener('fetch', (event) => {
	const request = event.request;

	if (request.method !== 'GET') return;
	if (request.mode !== 'navigate') {
		const asset = new URL(request.url);
		const key = asset.origin === self.location.origin ? packedKey(asset) : null;
		if (key) event.respondWith(packedAsset(request, key));
		return;
	}

	// A signed-in visitor is served from the network, every time.
	//
	// The request carries the cookies, including the HttpOnly ones the page
	// cannot see, so this is the right place to make the decision -- and it is
	// a decision to step aside, not to be removed. Unregistering on sign-in
	// meant an editor lost this permanently and had to be re-installed on
	// sign-out; stepping aside costs nothing and reverses the moment the
	// session ends.
	if (hasSessionCookie(request)) return;

	const url = new URL(request.url);
	if (url.origin !== self.location.origin) return;
	if (OFF_LIMITS.some((re) => re.test(url.pathname))) return;

	event.respondWith((async () => {
		// The cache handle is opened once for the worker's lifetime, not once
		// per navigation. caches.open() is asynchronous and was being awaited
		// on the hot path before anything could even be looked up -- two
		// sequential trips into the Cache API where one will do. Measured over
		// ten navigations, the handler was taking 12.4ms; almost none of that
		// was reading the page.
		const cache = await cacheHandle;

		// match() without ignoreVary, and without a second await for the
		// network. The stored response is returned the moment it is found;
		// the refresh is handed to waitUntil, which keeps the worker alive to
		// finish it without the navigation waiting on it. Not while the site
		// answers with its maintenance page: then only the network answers.
		const stored = inMaintenance ? null : await cache.match(request);

		if (stored) {
			event.waitUntil(refresh(cache, request, event.resultingClientId || event.clientId));
			return stored;               // answered without a round trip
		}

		try {
			const response = await fromNetwork(request);
			if (isMaintenance(response)) {
				event.waitUntil(enterMaintenance(cache));
				return response;
			}
			inMaintenance = false;
			if (mayStore(response)) {
				event.waitUntil(cache.put(request, response.clone()).catch(() => {}));
			}
			return response;
		} catch (e) {
			return new Response('Offline', { status: 503 });
		}
	})());
});
