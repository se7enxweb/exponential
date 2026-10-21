---
title: Web apps, CORS and CSP
audience: mobile-app-developer, app-creator, developer, hosting
category: mobile-apps
order: 640
summary: Build a browser app (PWA, single-page app) on Exponential - same origin versus another origin, the absence of CORS headers and the options around it, the site's Content Security Policy and connect-src, sessions and form tokens in the browser, and how the site's service worker relates to an app's own.
keywords: web app, PWA, single page application, CORS, Access-Control-Allow-Origin, same origin, proxy, Content Security Policy, CSP, connect-src, service worker, index.js, sw.js, session cookie, form token, X-CSRF-Token, browser
type: help
---
# Web apps, CORS and CSP

This page is for developers who build an app that runs in a browser: a progressive web app, a single-page app, or a
JavaScript front end on its own domain. Browsers enforce rules that native apps do not meet, and this page explains
how they apply to Exponential.

## Contents

1. [Same origin or another origin](#same-origin-or-another-origin)
2. [CORS](#cors)
3. [Content Security Policy](#content-security-policy)
4. [Signing in from a browser](#signing-in-from-a-browser)
5. [Service workers](#service-workers)

## Same origin or another origin

| Setup | What the browser allows |
|---|---|
| The app is served from the same scheme, host and port as the Exponential site (for example a design or siteaccess of the site, like the expservices sample portals) | every API call, with the site's session cookie |
| The app is on another host (`app.example.com` calling `www.example.com`) | only what CORS headers from the server allow |

The sample portals of `expservices` (`/portaljq/`, `/portalreact/` and others) are browser front ends built only on
the remote services and served by the site itself, so they need no CORS
([Remote services and apps, section 7](../../../guides/remote-services-and-apps.md#7-open-a-portal-built-on-the-services-3-minutes)).

## CORS

Neither the REST framework (`kernel/private/rest/`), ezjscore nor expservices sends `Access-Control-Allow-*` headers,
and no setting in the shipped INI files adds them. A browser app on another origin therefore cannot call the APIs
directly. The options:

1. **Serve the app from the same origin**, as a design of the site or under a path the web server maps to the app's
   files.
2. **Put a back end on the app's origin** that calls Exponential server-to-server
   ([A back end for your app](../auth/a-backend-for-your-app.md)). This also keeps tokens out of the browser.
3. **Add CORS headers at the web server or a proxy**, for the API paths only, with an explicit list of allowed origins
   (never `*` together with credentials), and answer the `OPTIONS` preflight there. This is hosting work; review it as a
   security change ([Security hardening](../../security-hardening.md)).

Native apps (iOS, Android, desktop) and React Native's networking are not subject to CORS.

## Content Security Policy

Exponential sends a Content Security Policy from `site.ini [ContentSecurityPolicy]`; by default it is report-only on
the administration and off on the sites, and its shipped directives include `connect-src 'self'`
([Content Security Policy](../../../features/6.0/content-security-policy.md)). If your app is served by the site and
the site enforces the policy, calls to other hosts (your back end on another domain, an analytics service) need
`connect-src` entries, and inline scripts are not allowed: load data the way
[Content Security Policy for your code](../../creators/app-creators/content-security-policy.md) describes.

## Signing in from a browser

- **Same origin:** sign in through the site and use the session cookie. Writes to the remote services then need the
  form token: get it from `expsession::token` and send it as `X-CSRF-Token` and as the field `ezxform_token`
  ([Writing through expservices](../json-endpoints/writing-through-expservices.md#rules-for-every-write)).
- **Another origin:** let the back end on the app's origin hold the credentials; do not keep tokens in `localStorage`
  ([Storing secrets on devices](../auth/storing-secrets-on-devices.md#browsers)).

## Service workers

The site's own service worker, `/index.js` (with `/sw.js` as its old name), is a navigation cache for site pages: it
keeps only same-origin `GET` navigations made without a session cookie, and only successful pages
([Offline and repeat visits](../../businesses/mobile-and-headless/offline-and-repeat-visits.md#what-it-never-stores)).
It does not cache API answers for your app. A PWA that needs offline data registers its own worker with its own
scope and its own cache rules, and must not take over the site's scope; agree on scopes with the site team.

## Related pages

- [Headless Exponential](../../creators/app-creators/headless-exponential.md)
- [The response cache, and getting out of the network's way](../../../bc/6.0/response-cache-and-navigation.md)
- [Security for applications](../../creators/app-creators/security-for-applications.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
