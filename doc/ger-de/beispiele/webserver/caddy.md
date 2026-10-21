---
title: Caddy
audience: admin, developer
category: examples
order: 50
summary: Ein Caddyfile für Exponential 6 mit Caddy und PHP-FPM, automatischem HTTPS und dem Routing von .htaccess_root, aufgebaut wie das Caddyfile, das Velocity für FrankenPHP erzeugt; warum php_fastcgi nicht verwendet werden darf.
keywords: Caddy, Caddyfile, automatisches HTTPS, Beispiel, reverse_proxy, fastcgi, PHP-FPM, automatic HTTPS, Let's Encrypt, HTTP/3, path_regexp, file_server, php_fastcgi, php_server, rewrite, example
type: example
---
# Caddy

[Caddy](https://caddyserver.com/) beschafft und erneuert Zertifikate selbst, leitet reines HTTP auf HTTPS weiter und
spricht HTTP/2 und HTTP/3. [`Caddyfile.example`](../../../eng-us/examples/web-servers/Caddyfile.example) liefert
Exponential mit Caddy und einem PHP-FPM-Pool aus. Der Kernel liefert die Datei nicht aus; ihr Routing ist das des
Caddyfiles, das `exp:velocity` für seine FrankenPHP-Engine erzeugt (`kernel/classes/expvelocityfrankenphp.php`,
`caddyfileText()`), wobei PHP an PHP-FPM statt an das eingebaute PHP von FrankenPHP übergeben wird, ergänzt um die
ersten beiden Regeln von `.htaccess_root`.

> **Warnung:** Verwenden Sie für Exponential weder `php_fastcgi` von Caddy noch `php_server` von FrankenPHP. Beide
> liefern jede vorhandene Datei aus, bevor sie auf `index.php` zurückfallen: `settings/site.ini`,
> `settings/override/*.ini.append.php`, die SQLite-Datenbank und die Logs wären herunterladbar.

## Installieren

1. Installieren Sie Caddy (das Paket der Distribution oder von caddyserver.com) und legen Sie den PHP-FPM-Pool an
   ([PHP-FPM-Pool](php-fpm.md)); der Benutzer von Caddy (`caddy`) muss den Socket des Pools öffnen können
   (`listen.owner`, `listen.group`).
2. Kopieren Sie die Datei nach `/etc/caddy/Caddyfile` (oder binden Sie sie von dort mit `import` ein) und ersetzen Sie
   `example.com`, die E-Mail-Adresse, `/var/www/example.com` und den Socket.
3. Die Ports 80 und 443 müssen für das Zertifikat die Maschine erreichen.
4. Prüfen und neu laden:

   ```bash
   caddy validate --config /etc/caddy/Caddyfile
   systemctl reload caddy
   ```

## Die Datei

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

## Wie es funktioniert

- **`route`** behält die Direktiven in der geschriebenen Reihenfolge bei (sonst sortiert Caddy sie nach eigenen
  Regeln); erst dadurch liest sich die Liste wie `.htaccess_root`.
- **Punktpfade und Skripte unterhalb des Wurzelverzeichnisses** beantworten zuerst 404. Die regulären Ausdrücke von
  Caddy (RE2 von Go) kennen keinen Lookahead, daher ist die Ausnahme für `.well-known` ein zweiter Matcher
  (`not path /.well-known/*`).
- **`@static`** entspricht genau `expVelocity::STATIC_PATHS`, abzüglich allem, worauf `expVelocity::NEVER_STATIC`
  passt (ein Skript oder ein Punktpfad unterhalb eines gelisteten Verzeichnisses). `file_server @static` beantwortet
  einen gelisteten Pfad, der nicht existiert, mit 404, wie Apache.
- **Die Rewrites** schicken die REST-API an `index_rest.php`, das Baummenü an `index_treemenu.php` und alles andere an
  `index.php`, einschließlich `settings/`, `var/log/`, `vendor/` und `composer.json`, die `index.php` mit einer Seite
  beantwortet, niemals mit der Datei. Der FastCGI-Transport von Caddy übergibt die ursprüngliche Anfrage-URI als
  `REQUEST_URI`, sodass der Kernel die Adresse sieht, die der Besucher angefragt hat.
- **Header**: `?Name` setzt einen Header nur, wenn die Antwort ihn noch nicht hat, sodass auf den Seiten des Kernels
  dessen eigene Header gewinnen. Hochgeladene SVGs erhalten die Richtlinie `sandbox`.

## Prüfen

```bash
caddy validate --config /etc/caddy/Caddyfile
for p in /.git/config /.env /kernel/classes/ezini.php; do
  printf '%-28s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"   # 404 each
done
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'   # 0: a page, not the file
```

Wie dieses Beispiel geprüft wurde: `frankenphp validate --adapter caddyfile` mit FrankenPHP 1.12.7 (Caddy 2.11), das
die Standardmodule von Caddy einschließlich des FastCGI-Transports (`http.reverse_proxy.transport.fastcgi`) enthält. Es
wurde hier nicht gegen einen PHP-FPM-Pool ausgeführt; das Routing, das es mit der erzeugten FrankenPHP-Konfiguration
teilt, ist das, was Velocity mit seiner Engine `frankenphp` ausliefert.

## Verwandte Seiten

- [FrankenPHP](frankenphp.md): dasselbe Routing mit in den Server eingebautem PHP.
- [Kapitel 8, Abschnitt 8.4: FrankenPHP](../../installation/08-die-website-ausliefern.md#84-frankenphp) und
  [FrankenPHP als Web-Engine von Exponential](../../../eng-us/bc/6.0/frankenphp.md) (auf Englisch).
- [Kapitel 13, Abschnitt 13.2: Was der Webserver niemals ausliefern darf](../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf).
- [PHP-FPM-Pool](php-fpm.md), [Webserver-Beispiele](README.md) und [alle Beispiele](../README.md).
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert werden.
