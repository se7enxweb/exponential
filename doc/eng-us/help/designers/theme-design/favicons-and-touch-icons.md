---
title: Favicons and touch icons
audience: designer
category: theme-design
order: 320
summary: Shipping a favicon, touch icons and a web app manifest with a design: where the files go, the link tags with ezimage, the /favicon.ico rewrite rule, and a minimal modern set.
keywords: favicon, favicon.ico, touch icon, apple-touch-icon, web app manifest, site icon, browser tab icon, icon sizes, svg favicon
type: help
---
# Favicons and touch icons

This page is for designers who give a site its tab icon and home screen icon. It answers: which files are needed, where
they go in a design, and how to link them.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../hosting/panels/plesk.md), [cPanel](../../hosting/panels/cpanel.md), [DirectAdmin](../../hosting/panels/directadmin.md), [ISPConfig](../../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

## Contents

1. [A minimal modern set](#a-minimal-modern-set)
2. [Where the files go](#where-the-files-go)
3. [The link tags](#the-link-tags)
4. [The /favicon.ico request](#the-faviconico-request)
5. [Checks](#checks)

## A minimal modern set

| File | Purpose |
|---|---|
| `favicon.svg` | the tab icon in current browsers (scales, can adapt to dark mode with a media query inside the SVG) |
| `favicon.ico` (32 x 32) | older browsers and tools that ask for `/favicon.ico` |
| `apple-touch-icon.png` (180 x 180) | home screen icon on Apple devices |
| `icon-192.png`, `icon-512.png` | Android home screen and install, named in a web app manifest |

The starter design ships a larger set of PNG sizes (16 to 152 pixels) in `images/favicon/`; both approaches work.

## Where the files go

In your design's `images/favicon/`, so they are found with `ezimage` and served as static files:

```
extension/mytheme/design/mytheme/images/favicon/favicon.svg
extension/mytheme/design/mytheme/images/favicon/favicon.ico
extension/mytheme/design/mytheme/images/favicon/apple-touch-icon.png
```

## The link tags

In a head part of your pagelayout (for example `pagelayout/head/link.tpl`, as the starter design does):

```tpl
<link rel="icon" href={'favicon/favicon.svg'|ezimage} type="image/svg+xml">
<link rel="icon" href={'favicon/favicon.ico'|ezimage} sizes="32x32">
<link rel="apple-touch-icon" href={'favicon/apple-touch-icon.png'|ezimage}>
```

`ezimage` returns the URL in quotes; do not add your own.

## The /favicon.ico request

Browsers ask for `/favicon.ico` at the site root even when the page links another icon. The shipped `.htaccess_root`
serves `design/standard/images/favicon.ico` and has a commented rule to map `/favicon.ico` to it:

```apache
#RewriteRule ^favicon\.ico /design/standard/images/favicon.ico [L]
```

An administrator can point such a rule at your design's file instead. Without a rule, `/favicon.ico` goes to
`index.php` and costs a page request with a 404 answer.

## Checks

- [ ] Tab icon visible in light and dark browser themes.
- [ ] No 404 for `/favicon.ico` in the server log or the network panel.
- [ ] Home screen icon tested on a phone.

## Related pages

- [Images in a design](images-in-a-design.md)
- [SVG images](svg-images.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
