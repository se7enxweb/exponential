---
title: External resources and CDNs
audience: designer
category: theme-design
order: 630
summary: When a design loads anything from another host (fonts, scripts, maps, videos, analytics): the costs in speed, privacy and security policy, the self-hosted alternatives the shipped themes use, consent before third-party embeds, and the settings involved.
keywords: cdn, external resources, third party, google fonts, jquery cdn, LoadFromCDN, privacy, consent, youtube embed, maps, analytics, subresource integrity, csp directives, self-hosting
type: help
---
# External resources and CDNs

This page is for designers who are tempted to add a `<link>` or `<script>` from another host. Each third-party origin
costs speed, privacy and a change to the site's security policy. The page answers: when it is worth it and how the
shipped themes avoid or contain it.

## Contents

1. [The costs](#the-costs)
2. [Self-hosting first](#self-hosting-first)
3. [When a third party is unavoidable](#when-a-third-party-is-unavoidable)
4. [The security policy](#the-security-policy)

## The costs

- **Speed**: every new host means a DNS lookup, a TCP connection and a TLS handshake before the first byte. The media
  design's stylesheet comment records this as the reason it moved its font from a font host to its own `fonts/`.
- **Privacy**: the visitor's address and the page they read go to the third party. In many countries that needs
  consent before it happens.
- **Availability**: if the third party is down or blocked, your design breaks.
- **Security policy**: each origin must be added to the site's `[ContentSecurityPolicy] Directives[]`.

## Self-hosting first

| Instead of | The shipped way |
|---|---|
| Google Fonts link | font files in the design's `fonts/`, `@font-face` packed with the CSS ([Fonts in a design](fonts-in-a-design.md)) |
| jQuery from a CDN | `ezjsc::jquery` with `ezjscore.ini [eZJSCore] LoadFromCDN=disabled` (default): the local jQuery 4 |
| map tiles straight from the tile server | the starter theme's `osm_proxy` module caches map tiles so visitors never contact the tile server (by its README) |
| icon fonts from a CDN | SVG icons in the design or `share/icons/` |

## When a third party is unavoidable

Videos, maps with live data, payment widgets, analytics:

- **Ask first.** The media design draws a placeholder for a YouTube video and loads the player only after the visitor
  agrees (`exp-youtube-consent.js` and `.css`, texts passed as a JSON block in `pagelayout/head/script.tpl`), and has a
  cookie consent script (`exp-cookie-consent.js`).
- **Load late.** Use `loading="lazy"` on iframes, `defer` on scripts.
- **Pin and verify.** For a script from a CDN, a fixed version URL and a Subresource Integrity hash (`integrity=""`);
  the comment above `ExternalScripts[]` in `ezjscore.ini` recommends https and integrity for external scripts.

## The security policy

Each origin is added to the directive it needs, per siteaccess, for example:

```ini
# settings/siteaccess/<site>/site.ini.append.php
[ContentSecurityPolicy]
Directives[frame-src]='self' https://www.youtube-nocookie.com
Directives[img-src]='self' data: blob: https://i.ytimg.com
```

Keep the list short and documented in your theme's README, so administrators know what to allow. See
[CSP-safe design](csp-safe-design.md).

## Related pages

- [Fonts in a design](fonts-in-a-design.md)
- [jQuery in designs](jquery-in-designs.md)
- [Theme performance](theme-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
