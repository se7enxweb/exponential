---
title: File uploads and SVG
audience: government, admin, developer
category: public-sector
order: 220
summary: For public bodies whose staff upload images and documents - how Exponential stops uploaded files from running code (no execution below the root, type read from content, SVG sanitizer, downloads through content/download), and what the operator keeps in place.
keywords: file uploads, upload security, SVG, expSvgSanitizer, SanitizeSVG, expImageUploadValidator, AllowedMIMETypes, fileinfo, content/download, rewrite rules, TemporaryDir, packages, ManagedByDeployment, public sector
type: help
---
# File uploads and SVG

This page is for administrators and developers. Uploads are a classic way into a web server: a script disguised as an
image, an SVG or HTML file that runs script in the browser, or a huge file that exhausts the server. Exponential has
defenses at each step ([Security hardening: File upload abuse](../../security-hardening.md#file-upload-abuse)).

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

1. [The defenses](#the-defenses)
2. [SVG images](#svg-images)
3. [Keep them in place](#keep-them-in-place)
4. [Checking](#checking)
5. [Related pages](#related-pages)

## The defenses

| Step | Defense | Where |
|---|---|---|
| Accepting an image | type read from the file content with PHP's fileinfo, compared with an allow-list | `expImageUploadValidator`, `image.ini [ImageUploadSettings] AllowedMIMETypes[]` |
| Storing an SVG | sanitized; only what is known to be safe is kept | `expSvgSanitizer`, `image.ini [ImageUploadSettings] SanitizeSVG=enabled` |
| Serving | nothing below the document root executed (`.php`, `.phtml`, `.phar` answer 404); only image files served directly from the storage | `.htaccess_root` |
| Serving other uploads | only through `content/download`, which checks read permission | kernel |
| Serving SVG from the storage | a sandboxing policy header (`sandbox; default-src 'none'`) | `.htaccess_root`, needs `mod_headers` |
| Packages | uploads need `package` policies; `ManagedByDeployment` blocks them | [Packages](../../../guides/packages.md) |

The allowed image types as shipped are JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP and SVG
([Security and audit, 1](../../../guides/security-and-audit.md#1-check-the-hardening-that-is-already-on-5-minutes)).

## SVG images

Since 15 October 2026 the image datatype accepts SVG (`.svg`, `.svgz`) on every site. An SVG is a document a browser
can run script from, so it is never stored as uploaded: the stored file is the sanitizer's output. Every path that
stores an image sanitizes (edit form, upload module, multi-upload, editor upload dialog, AJAX uploader, WebDAV, package
import). A file that claims to be SVG but is not recognized as one is not stored
([SVG images](../../../bc/6.0/svg-images.md)).

## Keep them in place

- Keep the rewrite rules current after every upgrade (`.htaccess_root`, or the derived nginx block); never serve the
  installation with a "serve the file if it exists" configuration.
- Keep `SanitizeSVG=enabled`.
- Check that fileinfo is loaded in the web server's PHP: `php -m | grep fileinfo` on the command line, and the same
  for PHP-FPM. Without it an older check applies and the log says so.
- Give upload rights only to roles that need them.
- Keep `TemporaryDir` private to the site, for example below `var/` (the shipped `/tmp/` is shared with every user of
  the machine).
- Documents and other files are not scanned for malware by Exponential. If your policy requires scanning, do it at the
  server or storage level.

## Checking

```bash
./console exp:ini get 'image.ini/ImageUploadSettings/AllowedMIMETypes' --allow-root-user
for p in .env .git/config extension/expservices/ezinfo.php; do
  curl -s -o /dev/null -w "$p %{http_code}\n" https://www.example.gov/$p
done
```

Expected: the allow-list as you intend it, and `404` three times.

## Related pages

- [SVG images](../../../bc/6.0/svg-images.md)
- [13.2 What the web server must never hand out](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)
- [SVG and image formats](../../creators/website-creators/svg-and-image-formats.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
