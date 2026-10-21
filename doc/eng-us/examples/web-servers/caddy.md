---
title: Caddy
audience: admin, developer
category: examples
order: 50
summary: A Caddyfile for Exponential 6 with Caddy and PHP-FPM, automatic HTTPS and the routing of .htaccess_root, built like the Caddyfile Velocity generates for FrankenPHP; why php_fastcgi must not be used.
keywords: Caddy, Caddyfile, reverse_proxy, fastcgi, PHP-FPM, automatic HTTPS, Let's Encrypt, HTTP/3, path_regexp, file_server, php_fastcgi, php_server, rewrite, example
type: example
---
# Caddy

[Caddy](https://caddyserver.com/) obtains and renews certificates by itself, redirects plain HTTP to HTTPS and speaks
HTTP/2 and HTTP/3. [`Caddyfile.example`](Caddyfile.example) serves Exponential with Caddy and a PHP-FPM pool. It is not
shipped by the kernel; its routing is the one of the Caddyfile that `exp:velocity` generates for its FrankenPHP engine
(`kernel/classes/expvelocityfrankenphp.php`, `caddyfileText()`), with PHP handed to PHP-FPM instead of FrankenPHP's
built-in PHP, plus the first two rules of `.htaccess_root`.

> **Warning:** Do not use Caddy's `php_fastcgi` (or FrankenPHP's `php_server`) for Exponential. Both serve every file
> that exists before they fall back to `index.php`: `settings/site.ini`, `settings/override/*.ini.append.php`, the
> SQLite database and the logs would be downloadable.

## Install it

1. Install Caddy (the distribution's package or caddyserver.com) and create the PHP-FPM pool
   ([PHP-FPM pool](php-fpm.md)); Caddy's user (`caddy`) must be able to open the pool's socket (`listen.owner`,
   `listen.group`).
2. Copy the file to `/etc/caddy/Caddyfile` (or `import` it from there), replace `example.com`, the e-mail address,
   `/var/www/example.com` and the socket.
3. Ports 80 and 443 must reach the machine for the certificate.
4. Check and reload:

   ```bash
   caddy validate --config /etc/caddy/Caddyfile
   systemctl reload caddy
   ```

## The file

```caddy
# Exponential 6: Caddy with PHP-FPM (automatic HTTPS, HTTP/2 and HTTP/3)
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md; rules derived from .htaccess_root (GPL v2 or later).
#
# Derived, not shipped by the kernel: the routing of the Caddyfile that
# "exp:velocity" generates for its FrankenPHP engine
# (kernel/classes/expvelocityfrankenphp.php, caddyfileText()), with PHP
# handed to a PHP-FPM pool instead of FrankenPHP's built-in PHP, plus the
# first two rules of .htaccess_root (dot paths, scripts below the root).
# Explained in caddy.md next to this file.
#
# Never use php_fastcgi or php_server for Exponential: they serve every file
# that exists, settings/*.ini and the SQLite database included.
#
# Install: copy to /etc/caddy/Caddyfile (or import it from there), replace
# example.com, the e-mail address, /var/www/example.com and the PHP-FPM
# socket, then "caddy validate --config /etc/caddy/Caddyfile" and
# "systemctl reload caddy". Caddy obtains and renews the certificate itself
# and redirects plain HTTP to HTTPS.

{
	# The address Let's Encrypt writes to about the certificate
	email webmaster@example.com
}

example.com, www.example.com {
	# The installation root, where index.php is
	root * /var/www/example.com

	log {
		output file /var/log/caddy/example.com-access.log
		format json
	}
	encode zstd gzip

	# The values of the kernel's site.ini [HTTPHeaderSettings] SecurityHeaders[];
	# "?" sets a header only when the response does not have it yet, so a page
	# from index.php keeps the kernel's own.
	header {
		?X-Content-Type-Options "nosniff"
		?X-Frame-Options "SAMEORIGIN"
		?Referrer-Policy "strict-origin-when-cross-origin"
		?Permissions-Policy "camera=(), microphone=(), payment=(), usb=()"
		# HSTS once every name is served over HTTPS for good; start short. Here or
		# in site.ini SecurityHeaders[Strict-Transport-Security], not both.
		#?Strict-Transport-Security "max-age=300"
	}

	route {
		# 1. No dot file or dot directory, anywhere; .well-known stays reachable
		#    (Caddy's regular expressions have no look-ahead, hence two matchers)
		@dot {
			path_regexp dot /\.
			not path /.well-known/*
		}
		respond @dot 404

		# 2. No script below the root runs because its path was asked for
		@script path_regexp script ^/.+/[^/]+\.(php[0-9]?|phtml|phar)$
		respond @script 404

		# 3. Uploaded SVG images run nothing and load nothing when opened directly
		@svg path_regexp svg (?i)\.svgz?$
		header @svg Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'"

		# 4. Files served as they are: expVelocity::STATIC_PATHS, the list of
		#    .htaccess_root, never a script or a dot path below them
		@static {
			path_regexp static ^/(design/[^/]+/(stylesheets|images|javascript|fonts)/|share/icons/|extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$|extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/|var/([^/]+/)?storage/images(-versioned)?/|var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$|var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$|var/([^/]+/)?cache/(texttoimage|public)/|packages/styles/.+/(stylesheets|images|javascript)/[^/]+/|packages/styles/.+/thumbnail/|var/storage/packages/.+\.(png|jpe?g|gif|webp)$|favicon\.ico$|design/standard/images/favicon\.ico$|robots\.txt$|(index|sw)\.js$|w3c/p3p\.xml$)
			not path_regexp (?i)(\.(php\d?|phtml|phar)$|/\.)
		}
		file_server @static

		# 5. Front controllers: the REST API, the tree menu, and index.php for
		#    everything else (settings/, var/, vendor/, kernel/ and composer.json
		#    included, which index.php answers with a page, never the file).
		#    The rewrite keeps REQUEST_URI: Caddy passes the original request URI.
		@rest path_regexp rest ^/(api/|index_rest\.php)
		rewrite @rest /index_rest.php
		@treemenu path_regexp treemenu ^/([^/]+/)?content/treemenu
		rewrite @treemenu /index_treemenu.php
		@front not path /index_rest.php /index_treemenu.php
		rewrite @front /index.php

		reverse_proxy unix//run/php-fpm/www.sock {
			transport fastcgi {
				split .php
			}
		}
	}
}
```

## How it works

- **`route`** keeps the directives in the written order (Caddy otherwise sorts them by its own rules), which is what
  makes the list read like `.htaccess_root`.
- **Dot paths and scripts below the root** answer 404 first. Caddy's regular expressions (Go's RE2) have no
  look-ahead, so the `.well-known` exception is a second matcher (`not path /.well-known/*`).
- **`@static`** is exactly `expVelocity::STATIC_PATHS`, minus anything matched by `expVelocity::NEVER_STATIC` (a
  script or a dot path below a listed directory). `file_server @static` answers 404 for a listed path that does not
  exist, as Apache does.
- **The rewrites** send the REST API to `index_rest.php`, the tree menu to `index_treemenu.php` and everything else to
  `index.php`, including `settings/`, `var/log/`, `vendor/` and `composer.json`, which `index.php` answers with a page,
  never the file. Caddy's FastCGI transport passes the original request URI as `REQUEST_URI`, so the kernel sees the
  address the visitor asked for.
- **Headers**: `?Name` sets a header only when the response does not have it yet, so the kernel's own headers on its
  pages win. Uploaded SVGs get the `sandbox` policy.

## Check it

```bash
caddy validate --config /etc/caddy/Caddyfile
for p in /.git/config /.env /kernel/classes/ezini.php; do
  printf '%-28s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"   # 404 each
done
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'   # 0: a page, not the file
```

How this example was checked: `frankenphp validate --adapter caddyfile` with FrankenPHP 1.12.7 (Caddy 2.11), which
contains Caddy's standard modules including the FastCGI transport (`http.reverse_proxy.transport.fastcgi`). It was not
run against a PHP-FPM pool here; the routing it shares with the generated FrankenPHP configuration is what Velocity
serves with its `frankenphp` engine.

## Related pages

- [FrankenPHP](frankenphp.md): the same routing with PHP built into the server.
- [Chapter 8, section 8.4: FrankenPHP](../../install/08-serving-the-site.md#84-frankenphp) and
  [FrankenPHP as the Exponential web engine](../../bc/6.0/frankenphp.md).
- [Chapter 13, section 13.2: What the web server must never hand out](../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out).
- [PHP-FPM pool](php-fpm.md), [Web server examples](README.md) and [all examples](../README.md).
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
