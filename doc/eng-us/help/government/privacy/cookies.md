---
title: Cookies set by Exponential
audience: government, admin, developer, project-manager
category: public-sector
order: 190
summary: For public bodies writing their cookie notice - the cookies Exponential's kernel and administration designs set (session, is_logged_in, setup wizard, mobile detection, administration UI state), their purpose and when, the browser storage admin4 uses, and how to check what your own site sets.
keywords: cookies, session cookie, eZSESSID, is_logged_in, exp_setup_wizard, eZMobileDeviceDetect, adminNavigationTab, localStorage, functional cookies, strictly necessary, ePrivacy article 5(3), cookie notice, public sector
type: reference
---
# Cookies set by Exponential

This page is for data protection officers, administrators and developers who write a cookie notice or check a site
against the ePrivacy rules. The kernel sets only functional cookies; none is used for tracking
([Privacy standards: ePrivacy Directive](../../../standards/privacy.md#eprivacy-directive-cookies)). What your design and
your extensions add is yours to list.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The kernel's cookies](#the-kernels-cookies)
2. [Browser storage](#browser-storage)
3. [What else your site may set](#what-else-your-site-may-set)
4. [Checking your site](#checking-your-site)
5. [Related pages](#related-pages)

## The kernel's cookies

| Cookie | Set by | Purpose | When |
|---|---|---|---|
| Session cookie (`[Session] SessionNamePrefix=eZSESSID`, plus a suffix) | `lib/ezsession/classes/ezsession.php` | sign-in and form state; `HttpOnly`, `SameSite=Lax`, `Secure` over HTTPS | when a session starts |
| `is_logged_in` | `kernel/private/classes/ezpkernelweb.php` | lets HTTP caches skip signed-in users | while signed in |
| `exp_setup_wizard` | `kernel/classes/expmaintenance.php` | the setup wizard's own run | during installation only |
| `eZMobileDeviceDetect` | `kernel/private/classes/ezpmobiledeviceregexpfilter.php` | remembers mobile detection | only with `[SiteAccessSettings] DetectMobileDevice=enabled` (default `disabled`) |
| `adminNavigationTab` and other UI state cookies | admin4 scripts (`node_tabs.js`, `contentstructuremenu_dynamic.js`, `ezajaxsubitems_expdatatable.js`) | open tab, tree state, table columns | administration only |

Anonymous visitors of a public page usually get no session cookie until something needs a session (a form, a sign-in);
`[Session] ForceStart=enabled` would open one for every visitor ([Sessions](../../../guides/sessions.md)).

## Browser storage

admin4 stores display choices (color mode, side columns, menu groups) as user preferences for a signed-in user, and in
the browser's `localStorage` on pages without a signed-in user (`design/admin4/javascript/a4_head.js`). This concerns
the administration, not the public site.

## What else your site may set

- Your design's scripts (a language choice, a font size switch).
- Extensions: analytics (`bcwebsitestatistics`), CAPTCHAs (`recaptcha`, `hcaptcha`), maps, embedded video, social login.
  These may set third-party cookies; see "Third-party extensions".
- A service worker or cache in your design.

## Checking your site

1. Open a fresh private browser window and visit a few public pages without signing in.
2. In the developer tools (Application or Storage), list cookies and local storage per host.
3. Submit a form, sign in, sign out; list again.
4. Repeat on each siteaccess; record the result with the date in your cookie documentation.

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Cookie banners and consent](cookie-banners-and-consent.md)
- [Sessions and cookies](../security/sessions-and-cookies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
