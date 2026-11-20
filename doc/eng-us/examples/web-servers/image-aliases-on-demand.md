---
title: Web server recipes for image aliases made on demand
audience: admin, hosting, developer
category: examples
order: 15
summary: The rewrite rules that send a missing storage image and content/image to index_image.php, for Apache (.htaccess_root and a virtual host with AllowOverride None), nginx (also in front of Apache), LiteSpeed and OpenLiteSpeed, FrankenPHP and Caddy, Exponential Velocity and a database cluster, plus the AVIF type and the request shield's image budget.
keywords: image aliases, on demand, index_image.php, content/image, rewrite rules, try_files, Apache, AllowOverride None, docroot protect, nginx, proxy, Plesk, LiteSpeed, OpenLiteSpeed, FrankenPHP, Caddy, Caddyfile, Velocity, cluster, eZDFS, index_cluster.php, CLUSTER_IMAGE_ALIAS_ON_DEMAND, AVIF, WebP, AddType, request shield, EXP-IMAGES
type: example
---
# Web server recipes for image aliases made on demand

Exponential 6.0.15 makes image aliases when the browser asks for them
([the feature](../../features/6.0/image-aliases-on-demand.md)). Two kinds of request must reach the light entry
script `index_image.php` in the installation root, which makes the alias (or fetches it on a staging site) or
answers a placeholder, with no session and without the page kernel:

1. a **storage image that is not there** (`var/<site>/storage/images/...` or `images-versioned/...`): an alias not
   made yet. Files that exist are still served by the web server as before;
2. **`content/image/...`**, the address a template gets for an alias not made yet (with or without a URI
   siteaccess in front, `/eng/content/image/...`).

`index_image.php` reads the original `REQUEST_URI`, so a rewrite (not a redirect) is all it needs. It also answers
`/index_image.php/<storage path>` for a server without rewrite rules.

Without these rules the site still works with the default `OnDemandURL=token`: `content/image` is answered by
`index.php` (a full kernel boot per image, slower), and a missing storage path is a plain 404. Only
`OnDemandURL=storage` depends on the storage rule. Upgrade notes:
[Upgrading to 6.0.15: image aliases made on demand](../../bc/6.0/image-aliases-on-demand.md).

## Contents

1. [Apache with .htaccess](#apache-with-htaccess)
2. [Apache virtual host with AllowOverride None](#apache-virtual-host-with-allowoverride-none)
3. [Allow-lists of root scripts](#allow-lists-of-root-scripts)
4. [nginx with PHP-FPM](#nginx-with-php-fpm)
5. [nginx in front of Apache](#nginx-in-front-of-apache)
6. [LiteSpeed and OpenLiteSpeed](#litespeed-and-openlitespeed)
7. [FrankenPHP and Caddy](#frankenphp-and-caddy)
8. [Exponential Velocity](#exponential-velocity)
9. [The cluster gateway](#the-cluster-gateway)
10. [AVIF and WebP types](#avif-and-webp-types)
11. [The request shield's image budget](#the-request-shields-image-budget)
12. [Check it](#check-it)

## Apache with .htaccess

`.htaccess_root` and `.htaccess_root_static` carry the rules, active, right before the storage passthrough. A site
that copied `.htaccess_root` to `.htaccess` before 6.0.15 copies it again (`cp .htaccess_root .htaccess`, after
saving any lines of its own):

```apache
# An image of the storage that is not there (an image alias not made yet, see
# settings/image.ini [AliasSettings]) goes to index_image.php, which makes the
# alias or answers a placeholder; the files that exist are served as they are.
# content/image, the address of an image alias made on demand, is answered by
# the same script, without the page kernel.
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_image.php [L]
RewriteRule ^([^/]+/)?content/image/ index_image.php [L]
RewriteRule ^index_image\.php - [L]
RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* - [L]
```

The `RewriteCond` belongs to the first rule only; keep the four lines together and in this order, before
`RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* - [L]`. The whole file, rule by rule:
[Apache .htaccess](apache-htaccess.md).

## Apache virtual host with AllowOverride None

A virtual host that reads no `.htaccess` (`AllowOverride None`, faster, the recommended setup) needs the rules in its
own configuration.

- **In a `<Directory>` block** for the document root, the same lines as `.htaccess` work unchanged. This is how
  alpha.se7enx.com runs (Plesk, `vhost_ssl.conf` and `vhost.conf`, after the tree menu rule).
- **In server context** (directly in `<VirtualHost>`), paths start with a slash and `%{REQUEST_FILENAME}` is not
  resolved to a file yet, so the condition names the file itself:

```apache
        # Image aliases made on demand
        RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f
        RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_image.php [L]
        RewriteRule ^/([^/]+/)?content/image/ /index_image.php [L]
        RewriteRule ^/index_image\.php - [L]
        RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* - [L]
```

The complete virtual host: [Apache virtual host](apache-virtual-host.md). Check with `apachectl configtest`, then
reload Apache.

## Allow-lists of root scripts

A configuration that lists the scripts allowed to run at the root must name `index_image.php`, or the rewritten
request answers 404 there. On alpha.se7enx.com that is the shared `/etc/httpd/conf/exponential-docroot-protect.conf`:

```apache
RewriteCond %{REQUEST_URI} !^/(index(_rest|_treemenu|_cluster|_webdav|_soap|_image)?\.php|soap\.php|webdav\.php|robots\.txt|favicon\.ico|sw\.js)$
```

The same goes for an IIS `web.config`, a hosting panel's list of PHP entry points, or a WAF rule that allows only
`index.php`.

## nginx with PHP-FPM

In the server block of [nginx.conf](nginx.conf): the storage location falls back to `index_image.php` instead of
`=404`, and two locations send `content/image` and the entry script to PHP. Checked with `nginx -t` and a real nginx
1.28 in front of PHP-FPM.

```nginx
# content/image, the address of an image alias made on demand, is answered by
# index_image.php, without the page kernel (REQUEST_URI stays the original).
location ~ ^/([^/]+/)?content/image/ {
    rewrite ^ /index_image.php last;
}
location = /index_image.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index_image.php;
    fastcgi_param SCRIPT_NAME /index_image.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}

# A storage image that is not there (an image alias not made yet) goes to
# index_image.php, which makes it or answers a placeholder.
location ~ ^/var/([^/]+/)?storage/images(-versioned)?/ {
    try_files $uri /index_image.php$is_args$args;
}
```

`fastcgi_params` passes `REQUEST_URI` as `$request_uri`, the original address, which `index_image.php` reads. The SVG
location that sets the sandbox headers comes before it and may keep `try_files $uri =404;` (SVG images have no
aliases). With a database cluster, the storage location goes to `index_cluster.php` instead (see
[The cluster gateway](#the-cluster-gateway)).

## nginx in front of Apache

Plesk and many hosting panels put nginx in front of Apache and let nginx serve static files itself. Its storage
location then answers a missing image with 404 before Apache sees it. Let it fall back to Apache, whose rules hand the
image to `index_image.php`. On alpha.se7enx.com, in Plesk's "Additional nginx directives" (`vhost_nginx.conf`),
before the shared static location (the first regex location that matches wins):

```nginx
location ~ ^/var/([^/]+/)?storage/images(-versioned)?/(?!.*/\.).+\.(png|jpe?g|gif|webp|avif)$ {
    expires 1y;
    access_log off;
    try_files $uri @exp_image_alias;
}
location @exp_image_alias {
    proxy_read_timeout 120;
    proxy_pass https://plesk_apache_ssl;
    proxy_hide_header upgrade;
    proxy_ssl_server_name on;
    proxy_ssl_name $host;
    proxy_ssl_session_reuse on;
    proxy_set_header Host             $host;
    proxy_set_header X-Real-IP        $remote_addr;
    proxy_set_header X-Forwarded-For  $proxy_add_x_forwarded_for;
    proxy_set_header X-Accel-Internal /internal-nginx-static-location;
    access_log off;
}
```

`plesk_apache_ssl` is the upstream name Plesk generates in the domain's `nginx.conf`; copy the upstream name and the
proxy headers from there for another panel. Repeat any security headers the static location sets
(`add_header ... always;`), since a location with `add_header` of its own inherits none. `content/image` needs nothing
on the nginx side: nginx passes it to Apache like any page. Check with `nginx -t`, reload nginx, and make sure the
static location's extension list includes `avif`, or the AVIF variants are served by Apache with the wrong type
(see [AVIF and WebP types](#avif-and-webp-types)).

## LiteSpeed and OpenLiteSpeed

- **LiteSpeed Web Server (Enterprise)** reads `.htaccess` like Apache: the rules of `.htaccess_root` work as they are.
- **OpenLiteSpeed** reads no `.htaccess`; the rules go into the virtual host's rewrite block, in the `^/?` style of
  [openlitespeed-vhost.conf](openlitespeed-vhost.conf), before the storage passthrough:

```apache
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f
RewriteRule ^/?var/([^/]+/)?storage/images(-versioned)?/.* /index_image.php [L]
RewriteRule ^/?([^/]+/)?content/image/ /index_image.php [L]
RewriteRule ^/?index_image\.php - [L]
RewriteRule ^/?var/([^/]+/)?storage/images(-versioned)?/.* - [L]
```

Add `index_image.php` wherever the virtual host lists the runnable root scripts, and `image/avif avif` to the MIME
types (Server Configuration > General > MIME Settings) if it is missing. These rules were not run on a LiteSpeed
server (none was available); they are the Apache rules tested under Apache. More: [LiteSpeed](litespeed.md).

## FrankenPHP and Caddy

Through Velocity's frankenphp engine the generated Caddyfile (`var/vc/frankenphp/run/Caddyfile`) has the block
already; restart Velocity after the upgrade so it is written again. A Caddyfile of your own needs it before
`file_server @static`, and `index_image.php` in the `@front` exclusion:

```caddyfile
		@image {
			path_regexp image ^/var/([^/]+/)?storage/images(-versioned)?/
			not file
		}
		rewrite @image /index_image.php
		@imageview path_regexp imageview ^/([^/]+/)?content/image/
		rewrite @imageview /index_image.php
		...
		@front not path /index_rest.php /index_treemenu.php /index_image.php
		rewrite @front /index.php
```

Caddy passes the original request URI to PHP after a `rewrite`. Checked with FrankenPHP 1.12.7 (`frankenphp validate`
and a running server); the same block works in a Caddy that passes PHP to PHP-FPM. The complete files:
[Caddyfile.example](Caddyfile.example), [frankenphp.Caddyfile.example](frankenphp.Caddyfile.example). FrankenPHP's
worker mode was not tested.

## Exponential Velocity

Nothing to configure. The kernel's front-controller routes (`expVelocity::FRONT_CONTROLLERS`) send a storage image
the engine did not find as a static file and every `content/image` request to `index_image.php`, which runs in the
persistent worker (request scope reset, the siteaccess matched on the host without the port, the request shield run
as `index.php` runs it). Velocity writes the routes into its server configuration when it starts, so restart it once
after the upgrade:

```bash
./console exp:velocity deploy --kernel
```

Velocity's own php engine (the router `bin/php/velocity-router.php`) routes them the same way. Velocity's response
cache ignores `Vary`, so a negotiated WebP/AVIF answer that is streamed is sent `private`; redirects and plain aliases
are cached normally. After a purge of aliases the response cache is cleared.

## The cluster gateway

A database cluster (eZDFS or eZDB, `file.ini [ClusteringSettings]`) keeps the storage files and the public cache
(packed scripts and stylesheets) elsewhere, and both are answered by `index_cluster.php`. Uncomment the three cluster
lines in `.htaccess_root`; they must stand **before** the
`index_image.php` rule:

```apache
RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_cluster.php [L]
RewriteRule ^var/([^/]+/)?cache/(texttoimage|public)/.* index_cluster.php [L]
RewriteRule ^index_cluster\.php - [L]
```

For nginx: `location ~ ^/var/([^/]+/)?(storage/images(-versioned)?|cache/(texttoimage|public))/ { rewrite ^ /index_cluster.php last; }` plus a
`location = /index_cluster.php` FastCGI block like the one for `index_image.php`.

When the gateway has no metadata for a storage image (an alias not made yet), it closes its connection and runs
`index_image.php` in the same request, which makes the alias in the cluster. Any other missing file stays a 404. To
switch the hand-over off, in `config.cluster.php`:

```php
define( 'CLUSTER_IMAGE_ALIAS_ON_DEMAND', false );
```

On a cluster, `Delivery=auto` streams the file instead of redirecting, since the web server cannot serve the storage
itself; the per-alias lock is the handler's `.generating` entry in the shared database. The hand-over was tested with
a gateway without metadata on eZFS storage, not on a real cluster.

## AVIF and WebP types

The WebP and AVIF variants of an alias are named after it: `foo_large.jpg.webp`, `foo_large.jpg.avif`. A server that
does not know `.avif` types such a file after its inner `.jpg` and sends `image/jpeg` (RHEL 9's `/etc/mime.types` has
no `avif`, for example). `.htaccess_root` carries:

```apache
<IfModule mod_mime.c>
    AddType image/webp .webp
    AddType image/avif .avif
</IfModule>
```

A virtual host with `AllowOverride None` needs `AddType image/avif .avif` in its own configuration (alpha.se7enx.com:
`vhost_ssl.conf` and `vhost.conf`). nginx 1.28's `mime.types` has both; older versions may lack `avif`
(`image/avif avif;` in the `types` block of `mime.types`). In nginx in front of Apache, add `avif` to the extension
list of the static location, or nginx hands those files to Apache.

## The request shield's image budget

A page with hundreds of new images asks `content/image` (or `index_image.php`) once per image, all at once. The
request shield's default pace counts every request through PHP as a page view and asks for the browser check after
120 in a minute, so such a page gets a few hundred answers 429 and never completes. The default rules of
`exp_request_shield` after 0.0.1.3 (`rules/exponential.rules`, version 2026-11-14.1) leave the images out of the pace
and give them a budget of their own:

```
no-limit    requests
[EXP-PACE]      limit pages 300/min challenge-at 120 on-exceeded challenge at regex ^(?!(/[a-z0-9_]+)?/content/image/|/index_image\.php|/var/([^/]+/)?storage/images(-versioned)?/)
[EXP-IMAGES]    limit images 3000/min on-exceeded challenge at regex ^((/[a-z0-9_]+)?/content/image/|/index_image\.php|/var/([^/]+/)?storage/images(-versioned)?/)
expect GET /content/image/abc/x.jpg  times 600     answered
```

A site with an older copy of the rules adds the same lines to its own rule file (with IDs of its own, for example
`SITE-PAGES` and `SITE-IMAGES`), then checks and tests it:

```bash
php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
```

A site rule file that restated the pace this way and then takes the new copy of `exponential.rules` must drop its own
lines: two budgets named `pages` are refused by `check`. Guide: [Setting up the request shield](../../guides/request-shield.md).

## Check it

With an image whose alias is not made yet (Setup > Image aliases > Test tools > One image shows which, and the
address a template gets):

```bash
# the storage path of a missing alias: made, 200 with the image type (or 200 SVG placeholder while it is being made)
curl -sI https://www.example.org/var/site/storage/images/.../1234-1-eng-GB/photo_large.jpg
# again: now a static file, without X-Image-Placeholder
curl -sI https://www.example.org/var/site/storage/images/.../1234-1-eng-GB/photo_large.jpg
# an address that names nothing: 404 with the SVG placeholder and X-Image-Placeholder: 1
curl -sI https://www.example.org/var/site/storage/images/nothing/here_large.jpg
# an AVIF variant: Content-Type image/avif
curl -sI https://www.example.org/var/site/storage/images/.../photo_large.jpg.avif
```

A 404 HTML page from the site (not the SVG placeholder) for the first request means the rule is missing or comes
after the storage passthrough; a 403 or 404 from the server means an allow-list lacks `index_image.php`. `content/image`
addresses never answer with `Set-Cookie`. `./console exp:image:alias status` lists the checks that need attention.
