/*
 * The Exponential Service Workers Index is /index.js. This is its old name.
 *
 * Browsers that registered the site's service worker before the rename still
 * hold /sw.js and check this address for updates. It loads /index.js, so they
 * run exactly the same code, until the site's page registers /index.js over it
 * (same scope '/', so the registration moves to the new script). Keep this file
 * until no browser can still hold the old registration; removing it early would
 * leave those browsers on their last copy of the old worker for good, because a
 * failed update check keeps the installed worker running.
 */
importScripts('/index.js');
