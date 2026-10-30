---
title: nginx with PHP-FPM
audience: hosting, admin
category: hosting
order: 40
summary: How a host serves Exponential with nginx and PHP-FPM: the server block derived from .htaccess_root rule for rule, why its order matters, HTTPS, uploads, and the checks that prove nothing leaks.
keywords: nginx, PHP-FPM, server block, location, try_files, fastcgi_pass, fastcgi_params, client_max_body_size, http2, htaccess_root, rewrite, derived configuration
type: help
---
# nginx with PHP-FPM

This page is for system administrators at hosting companies who run nginx and want to serve Exponential with it. It
answers: what configuration exists for nginx, how the server block is built, what to watch after an upgrade, and how
to prove the routing is right. The source is
[Chapter 8, nginx with PHP-FPM](../../../install/08-serving-the-site.md#86-nginx-with-php-fpm).

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
> panel: [Plesk](../panels/plesk.md), [cPanel](../panels/cpanel.md), [DirectAdmin](../panels/directadmin.md), [ISPConfig](../panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

## Contents

1. [What the project ships for nginx](#what-the-project-ships-for-nginx)
2. [The server block](#the-server-block)
3. [Why it is ordered this way](#why-it-is-ordered-this-way)
4. [HTTPS and HTTP/2](#https-and-http2)
5. [The PHP-FPM side](#the-php-fpm-side)
6. [Checks before go-live](#checks-before-go-live)
7. [Checklist](#checklist)

## What the project ships for nginx

**Nothing shipped; one derived block documented.** Exponential ships no nginx configuration in its repository. The
server block in chapter 8 is a translation of `.htaccess_root` into nginx directives, rule for rule. Chapter 8 says
it plainly: "Test it on your site before you rely on it, and compare it with `.htaccess_root` after every upgrade."
[Operating system support](../../os-support.md#web-servers) lists nginx with PHP-FPM as tier 2, known to work.

> **Warning**
> Treat the block below as a starting point you own. When a new release changes `.htaccess_root`, your nginx block
> does not change with it. Put "compare nginx rules with `.htaccess_root`" on the upgrade checklist of every nginx plan.

nginx never reads `.htaccess`, so `./console exp:setup:htaccess --apply` protects nothing here. It still helps with the
comparison: keep a `.htaccess` copied by it, and after an upgrade `./console exp:setup:htaccess` prints the diff
between that copy and the new `.htaccess_root` (exit 1 while they differ), which is the list of rules to carry into the
server block. `config.php` is set up the same way on every server: `./console exp:setup:config --apply` and
`./console exp:config:set` ([Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)).

## The server block

Adjust the root and the PHP-FPM socket (one socket per customer pool on a shared machine). This is chapter 8's block:

```nginx
server {
    listen 80;
    server_name example.com www.example.com;

    # The installation root, where index.php is
    root /var/www/example.com;
    index index.php;

    # Uploads: nginx's default limit is 1 MB
    client_max_body_size 64m;

    # 1. No dot file or dot directory, anywhere; .well-known stays reachable
    location ~ (^|/)\.(?!well-known(/|$)) {
        return 404;
    }

    # 2. No script below the root runs because its path was asked for
    location ~ ^/.+/[^/]+\.(php[0-9]?|phtml|phar)$ {
        return 404;
    }

    # 3. Front controllers
    location ^~ /api/ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    location = /index_rest.php {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    location ~ ^/([^/]+/)?content/treemenu {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_treemenu.php;
        fastcgi_param SCRIPT_NAME /index_treemenu.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }

    # 4. Files served as they are; a listed path that does not exist is a 404
    location ~ ^/var/([^/]+/)?storage/images(-versioned)?/                                    { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$             { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$                      { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?cache/(texttoimage|public)/                                       { try_files $uri =404; }
    location ~ ^/design/[^/]+/(stylesheets|images|javascript|fonts)/                            { try_files $uri =404; }
    location ~ ^/share/icons/                                                                   { try_files $uri =404; }
    location ~ ^/extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$                          { try_files $uri =404; }
    location ~ ^/extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/ { try_files $uri =404; }
    location ~ ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/                      { try_files $uri =404; }
    location ~ ^/packages/styles/.+/thumbnail/                                                  { try_files $uri =404; }
    location ~ ^/var/storage/packages/.+\.(png|jpe?g|gif|webp)$                                 { try_files $uri =404; }
    location = /favicon.ico  { try_files $uri =404; }
    location = /robots.txt   { try_files $uri =404; }
    location = /index.js     { try_files $uri =404; }
    location = /sw.js        { try_files $uri =404; }
    location = /w3c/p3p.xml  { try_files $uri =404; }

    # 5. Everything else goes to index.php, with the original REQUEST_URI
    location / {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_param SCRIPT_NAME /index.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
}
```

For a multi-site installation with `site.ini [FileSettings] CacheVarDir=var_cache`, chapter 8 adds a commented
location for `^/var_cache/([^/]+/)?cache/(texttoimage|public)/`; see
[Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md).

## Why it is ordered this way

From chapter 8:

- nginx chooses a location by its own precedence, not by file order: exact matches (`=`) first, then the longest `^~`
  prefix, then **regular expressions in the order they appear**, then the longest plain prefix. The dot-file and
  script rules are therefore the first regular expressions, as they are the first rules of `.htaccess_root`.
- The front controller is named in `SCRIPT_FILENAME` and the request is not rewritten, so `REQUEST_URI` still carries
  the address the visitor asked for, exactly as with Apache.
- A root-level `.php` other than the front controllers (for example `/config.php`) reaches `location /` and is
  answered by `index.php`, never executed.
- The static locations have no `fastcgi_pass`, so no PHP runs there; the script rule before them makes sure a `.php`
  below an asset directory is not sent as text either.
- `.htaccess_root` answers 404 for a script below the root only when the file exists; this block answers 404 always.
- `fastcgi_params` of the nginx packages passes `HTTPS` when the connection is TLS, which is what Exponential checks
  first.

## HTTPS and HTTP/2

Add `listen 443 ssl;` (and `http2 on;` on nginx 1.25.1 or later), `ssl_certificate` and `ssl_certificate_key`, and a
separate `server` on port 80 that returns `301 https://$host$request_uri`. Check with `nginx -t` and reload with
`systemctl reload nginx`. Security headers for static files: `add_header ... always;`
([Chapter 13](../../../install/13-security-hardening.md)). The kernel sends its own security headers on pages
(`site.ini [HTTPHeaderSettings] SecurityHeaders[]`).

Certificates for many customer domains: see [TLS and HTTPS](../security/tls-and-https.md).

## The PHP-FPM side

Everything on the PHP-FPM side is the same as under Apache: one pool per site as the site's user, OPcache on with
timestamp checks, uploads sized to the customer's needs (and `client_max_body_size` in nginx to match), and a reload
of **the** FPM master that serves the site after a PHP change. `exp:velocity deploy --no-velocity` finds that master
and clears the caches in the right order. See [Apache with PHP-FPM](apache-with-php-fpm.md#reloading-the-right-php-fpm)
and [PHP-FPM tuning](../performance/php-fpm-tuning.md).

## Checks before go-live

Replace the address with your own. These are chapter 8's checks:

```bash
nginx -t
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/.git/config                      # 404
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/kernel/classes/ezcontentobject.php  # 404
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/robots.txt                        # 200 when the file exists
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'                # 0
```

What can go wrong:

| Symptom | Cause | Fix |
|---|---|---|
| a 200 for the `.git` or kernel request | the block is not this one, or another `location` wins first | put the dot-file and script rules first among the regular expressions |
| 404 for every asset under `design/` | `root` is not the installation root | point `root` at the directory with `index.php` |
| uploads larger than 1 MB fail | `client_max_body_size` left at nginx's default | raise it with `upload_max_filesize` and `post_max_size` |
| links and redirects go to `http://` behind a TLS terminator | the terminator is not trusted, or sends no `X-Forwarded-Proto` | [Varnish and reverse proxies](../performance/varnish-and-reverse-proxies.md) |

## Checklist

- [ ] Server block derived from the current `.htaccess_root`, tested on staging.
- [ ] Dot-file and script rules are the first regular-expression locations.
- [ ] `client_max_body_size` matches the PHP upload limits.
- [ ] HTTPS on, port 80 redirected, `nginx -t` clean.
- [ ] The checks above pass.
- [ ] The upgrade checklist includes comparing the block with `.htaccess_root`.

**Demo mode with nginx.** nginx passes nothing of its own environment to PHP: for a public demo add `fastcgi_param EXP_DEMO_MODE 1;` where its server block hands requests to PHP-FPM, or `env[EXP_DEMO_MODE] = 1` to its pool. [`nginx-fastcgi-demo.conf`](../../../examples/demo-mode/nginx-fastcgi-demo.conf), [Demo mode for hosts](../security/demo-mode-for-hosts.md).

## Related pages

- [Apache with PHP-FPM](apache-with-php-fpm.md)
- [Velocity beside Apache or nginx](velocity-beside-apache-or-nginx.md)
- [Choosing a web server](choosing-a-web-server.md)
- [Chapter 8: Serving the site](../../../install/08-serving-the-site.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
