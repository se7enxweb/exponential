---
title: FrankenPHP
audience: admin, developer
category: examples
order: 60
summary: Serving Exponential 6 with FrankenPHP, the Caddy server with PHP built in: through Velocity's frankenphp engine (the usual way, a few settings) or on its own with a checked Caddyfile that keeps the routing of .htaccess_root.
keywords: FrankenPHP, Caddy, Caddyfile, Velocity, exp:velocity, engine, frankenphp engine, php directive, php_server, automatic HTTPS, num_threads, php_ini, SiteInclude, example
type: example
---
# FrankenPHP

FrankenPHP is the Caddy web server with PHP built in, as one binary. There are two ways to run Exponential on it:

- **Through Velocity** (recommended): `exp:velocity` downloads and verifies the binary, writes the Caddyfile from
  `velocity.ini` on every start, validates it before use and drives the server with the same commands as its other
  engines. You write no Caddyfile.
- **On its own**: you start `frankenphp run` with a Caddyfile of your own, for example
  [`frankenphp.Caddyfile.example`](frankenphp.Caddyfile.example) below.

> **Warning:** Never use FrankenPHP's `php_server` directive for Exponential. It serves every file that exists,
> `settings/*.ini` and the SQLite database included. Both ways below route like `.htaccess_root` instead.

## Through Velocity

```bash
php bin/php/console exp:velocity config set ServerSettings Engine frankenphp --allow-root-user
php bin/php/console exp:velocity install --allow-root-user     # optional: start installs it when missing
php bin/php/console exp:velocity start --allow-root-user
php bin/php/console exp:velocity ctl caddyfile --allow-root-user   # print the generated configuration
```

The settings are in `settings/velocity.ini [FrankenPHPSettings]`; set yours in
`settings/override/velocity.ini.append.php` (or with `exp:velocity config set`):

```ini
[FrankenPHPSettings]
# HTTP and HTTPS ports (shipped: 8089 and 8444)
Port=8089
HTTPSPort=8444
# PHP threads; empty: FrankenPHP's default, twice the CPU cores
Workers=
# The binary reads no php.ini of the machine: these are passed to it
IniOptions[]
IniOptions[]=display_errors=Off
IniOptions[]=log_errors=On
IniOptions[]=memory_limit=512M
IniOptions[]=opcache.enable=1
IniOptions[]=post_max_size=64M
IniOptions[]=upload_max_filesize=32M
# A file with Caddy directives of your own, imported into every site block
SiteInclude=

[HTTPSSettings]
# Your certificate; with both empty FrankenPHP makes a self-signed one
Certificate=/etc/ssl/example.com/fullchain.pem
Key=/etc/ssl/example.com/privkey.pem
```

The generated file is `var/vc/frankenphp/run/Caddyfile`. Its shape, with this machine's paths and versions replaced:

```caddy
{
	auto_https off
	admin "unix//var/www/example.com/var/vc/frankenphp/run/admin.sock"
	persist_config off
	grace_period 15s
	servers {
		protocols h1 h2
	}
	frankenphp {
		num_threads 16
		max_threads 16
		php_ini "memory_limit" "512M"
		php_ini "opcache.enable" "1"
	}
}

(exp) {
	root * "/var/www/example.com"
	encode zstd br gzip
	route {
		respond /Q/health 200
		@static {
			path_regexp static ^/(design/[^/]+/(stylesheets|images|javascript|fonts)/|...|w3c/p3p\.xml$)
			not path_regexp (?i)(\.(php\d?|phtml|phar)$|/\.)
		}
		header @static Cache-Control "public, max-age=31536000"
		file_server @static
		@rest path_regexp rest ^/(api/|index_rest\.php)
		rewrite @rest /index_rest.php
		@treemenu path_regexp treemenu ^/([^/]+/)?content/treemenu
		rewrite @treemenu /index_treemenu.php
		@front not path /index_rest.php /index_treemenu.php
		rewrite @front /index.php
		php {
			env EXP_VELOCITY_ENGINE "FrankenPHP"
		}
	}
}

http://:8089 {
	import exp
}
https://:8444 {
	tls "/etc/ssl/example.com/fullchain.pem" "/etc/ssl/example.com/privkey.pem"
	import exp
}
```

Do not edit it: it is rewritten on every `start`, `graceful` and `restart`. Directives of your own go in the file named
by `SiteInclude`, which is imported into both site blocks and validated with the rest.

## On its own

```caddy
# Exponential 6: FrankenPHP on its own (without exp:velocity)
#
# Free documentation: GFDL 1.3 or later, see doc/README-LICENSE.md; rules derived from .htaccess_root (GPL v2 or later).
#
# The shape of the Caddyfile that "exp:velocity" generates for its FrankenPHP
# engine (kernel/classes/expvelocityfrankenphp.php, caddyfileText(), in
# var/vc/frankenphp/run/Caddyfile), for a FrankenPHP you start yourself:
# public names with Caddy's automatic HTTPS instead of the generated ports,
# example paths instead of this machine's, and the first two rules of
# .htaccess_root added. Explained in frankenphp.md next to this file.
#
# With Velocity, do not use this file: set [ServerSettings] Engine=frankenphp
# and let "exp:velocity start" write and validate its own Caddyfile; put own
# directives in the file named by [FrankenPHPSettings] SiteInclude.
#
# Never use php_server for Exponential: it serves every file that exists,
# settings/*.ini and the SQLite database included.
#
# Run: frankenphp run --config /etc/frankenphp/Caddyfile
# Check: frankenphp validate --config /etc/frankenphp/Caddyfile --adapter caddyfile

{
	email webmaster@example.com
	grace_period 15s
	frankenphp {
		# PHP threads; FrankenPHP's default is twice the CPU cores
		#num_threads 16
		# The binary reads no php.ini of the machine: what velocity.ini
		# [FrankenPHPSettings] IniOptions[] ships
		php_ini display_errors Off
		php_ini log_errors On
		php_ini memory_limit 512M
		php_ini opcache.enable 1
		php_ini post_max_size 64M
		php_ini upload_max_filesize 32M
	}
}

(exp) {
	# The installation root, where index.php is
	root * /var/www/example.com
	log {
		output file /var/log/frankenphp/example.com-access.log
		format json
	}
	encode zstd br gzip
	header {
		?X-Content-Type-Options "nosniff"
		?X-Frame-Options "SAMEORIGIN"
		?Referrer-Policy "strict-origin-when-cross-origin"
		?Permissions-Policy "camera=(), microphone=(), payment=(), usb=()"
	}
	route {
		# No dot path (.well-known stays reachable) and no script below the root
		@dot {
			path_regexp dot /\.
			not path /.well-known/*
		}
		respond @dot 404
		@script path_regexp script ^/.+/[^/]+\.(php[0-9]?|phtml|phar)$
		respond @script 404

		@svg path_regexp svg (?i)\.svgz?$
		header @svg Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'"

		# As generated: expVelocity::STATIC_PATHS, never a script or a dot path
		@static {
			path_regexp static ^/(design/[^/]+/(stylesheets|images|javascript|fonts)/|share/icons/|extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/|var/([^/]+/)?storage/images(-versioned)?/|var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$|var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$|var/([^/]+/)?cache/(texttoimage|public)/|packages/styles/.+/(stylesheets|images|javascript)/[^/]+/|packages/styles/.+/thumbnail/|var/storage/packages/.+\.(png|jpe?g|gif|webp)$|favicon\.ico$|design/standard/images/favicon\.ico$|robots\.txt$|(index|sw)\.js$|w3c/p3p\.xml$)
			not path_regexp (?i)(\.(php\d?|phtml|phar)$|/\.)
		}
		header @static Cache-Control "public, max-age=31536000"
		file_server @static
		@rest path_regexp rest ^/(api/|index_rest\.php)
		rewrite @rest /index_rest.php
		@treemenu path_regexp treemenu ^/([^/]+/)?content/treemenu
		rewrite @treemenu /index_treemenu.php
		@front not path /index_rest.php /index_treemenu.php
		rewrite @front /index.php
		php
	}
}

example.com, www.example.com {
	import exp
}
```

Install the binary from FrankenPHP's releases (or use the one Velocity downloaded, `var/vc/frankenphp/bin/`), copy the
file to `/etc/frankenphp/Caddyfile`, replace `example.com`, the e-mail address and `/var/www/example.com`, then:

```bash
frankenphp validate --config /etc/frankenphp/Caddyfile --adapter caddyfile
frankenphp run --config /etc/frankenphp/Caddyfile
```

Compared with the generated file it adds Caddy's automatic HTTPS for the public names, the security headers (set only
when the page does not have them yet), the `sandbox` policy for SVG files, and the first two rules of `.htaccess_root`
(dot paths and scripts below the root answer 404; the generated file sends those to `index.php`, which answers them).
FrankenPHP runs in classic mode here: each request starts from a clean state, as under PHP-FPM.

How this example was checked: `frankenphp validate --adapter caddyfile` with FrankenPHP 1.12.7 (Caddy 2.11); the
generated configuration it follows is the one Velocity validates and serves with its `frankenphp` engine.

## Related pages

- [Caddy](caddy.md): the same routing with a PHP-FPM pool.
- [Chapter 8, section 8.4: FrankenPHP](../../install/08-serving-the-site.md#84-frankenphp),
  [FrankenPHP as the Exponential web engine](../../bc/6.0/frankenphp.md) and
  [Velocity engines](../../bc/6.0/velocity-engines.md).
- [Velocity](velocity.md), [Web server examples](README.md) and [all examples](../README.md).
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
