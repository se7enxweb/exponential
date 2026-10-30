---
title: FrankenPHP
audience: admin, developer
category: examples
order: 60
summary: Exponential 6 mit FrankenPHP ausliefern, dem Caddy-Server mit eingebautem PHP: über die Engine frankenphp von Velocity (der übliche Weg, wenige Einstellungen) oder eigenständig mit einem geprüften Caddyfile, das das Routing von .htaccess_root beibehält.
keywords: FrankenPHP, Caddy, Caddyfile, Velocity, Engine, automatisches HTTPS, Beispiel, exp:velocity, engine, frankenphp engine, php directive, php_server, automatic HTTPS, num_threads, php_ini, SiteInclude, example
type: example
---
# FrankenPHP

FrankenPHP ist der Webserver Caddy mit eingebautem PHP, als eine einzige Binärdatei. Es gibt zwei Wege, Exponential
darauf zu betreiben:

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](nginx.md), [LiteSpeed](litespeed.md), [Caddy](caddy.md), [IIS](iis.md),
> [Velocity](velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

- **Über Velocity** (empfohlen): `exp:velocity` lädt die Binärdatei herunter und prüft sie, schreibt bei jedem Start das
  Caddyfile aus `velocity.ini`, validiert es vor der Verwendung und steuert den Server mit denselben Befehlen wie seine
  anderen Engines. Sie schreiben kein Caddyfile.
- **Eigenständig**: Sie starten `frankenphp run` mit einem eigenen Caddyfile, zum Beispiel
  [`frankenphp.Caddyfile.example`](../../../eng-us/examples/web-servers/frankenphp.Caddyfile.example) weiter unten.

> **Warnung:** Verwenden Sie für Exponential niemals die Direktive `php_server` von FrankenPHP. Sie liefert jede
> vorhandene Datei aus, `settings/*.ini` und die SQLite-Datenbank eingeschlossen. Beide Wege unten routen stattdessen
> wie `.htaccess_root`.

FrankenPHP liest `.htaccess` nie, deshalb ändert `exp:setup:htaccess` nichts an seinem Routing. `config.php` wird wie
auf jedem Server mit `./console exp:setup:config --apply` und `./console exp:config:set ... --apply` an ihren Platz
gebracht und bearbeitet. Im klassischen Modus (dem einzigen, den Exponential nutzt) liest jede Anfrage sie erneut,
sobald OPcache die neue Datei bemerkt; zeigt sich eine Änderung nicht, starten Sie den Server neu (über Velocity:
`exp:velocity restart`). Siehe
[.htaccess und config.php von der Konsole einrichten](../../anleitungen/setup-dateien-von-der-konsole.md).

## Über Velocity

```bash
php bin/php/console exp:velocity config set --apply ServerSettings Engine frankenphp --allow-root-user
php bin/php/console exp:velocity install --allow-root-user     # optional: start installs it when missing
php bin/php/console exp:velocity start --allow-root-user
php bin/php/console exp:velocity ctl caddyfile --allow-root-user   # print the generated configuration
```

Die Einstellungen stehen in `settings/velocity.ini [FrankenPHPSettings]`; setzen Sie Ihre eigenen in
`settings/override/velocity.ini.append.php` (oder mit `exp:velocity config set`):

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

Die erzeugte Datei ist `var/vc/frankenphp/run/Caddyfile`. Ihr Aufbau, mit ersetzten Pfaden und Versionen dieser
Maschine:

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

Bearbeiten Sie sie nicht: Sie wird bei jedem `start`, `graceful` und `restart` neu geschrieben. Eigene Direktiven
gehören in die Datei, die `SiteInclude` nennt; sie wird in beide Site-Blöcke importiert und mit dem Rest validiert.

## Eigenständig

```caddy
# Exponential 6: FrankenPHP on its own (without exp:velocity)
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md; rules derived from .htaccess_root (GPL v2 or later).
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
			path_regexp static ^/(design/[^/]+/(stylesheets|images|javascript|fonts)/|share/icons/|extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$|extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/|var/([^/]+/)?storage/images(-versioned)?/|var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$|var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$|var/([^/]+/)?cache/(texttoimage|public)/|packages/styles/.+/(stylesheets|images|javascript)/[^/]+/|packages/styles/.+/thumbnail/|var/storage/packages/.+\.(png|jpe?g|gif|webp)$|favicon\.ico$|design/standard/images/favicon\.ico$|robots\.txt$|(index|sw)\.js$|w3c/p3p\.xml$)
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

Installieren Sie die Binärdatei aus den Releases von FrankenPHP (oder verwenden Sie die, die Velocity heruntergeladen
hat, `var/vc/frankenphp/bin/`), kopieren Sie die Datei nach `/etc/frankenphp/Caddyfile`, ersetzen Sie `example.com`,
die E-Mail-Adresse und `/var/www/example.com` und führen Sie dann aus:

```bash
frankenphp validate --config /etc/frankenphp/Caddyfile --adapter caddyfile
frankenphp run --config /etc/frankenphp/Caddyfile
```

Gegenüber der erzeugten Datei ergänzt sie das automatische HTTPS von Caddy für die öffentlichen Namen, die
Sicherheits-Header (nur gesetzt, wenn die Seite sie noch nicht hat), die Richtlinie `sandbox` für SVG-Dateien und die
ersten beiden Regeln von `.htaccess_root` (Punktpfade und Skripte unterhalb des Wurzelverzeichnisses beantworten 404;
die erzeugte Datei schickt sie an `index.php`, das sie beantwortet). FrankenPHP läuft hier im klassischen Modus: Jede
Anfrage beginnt mit einem sauberen Zustand, wie unter PHP-FPM.

Wie dieses Beispiel geprüft wurde: `frankenphp validate --adapter caddyfile` mit FrankenPHP 1.12.7 (Caddy 2.11); die
erzeugte Konfiguration, der es folgt, ist die, die Velocity mit seiner Engine `frankenphp` validiert und ausliefert.

## Verwandte Seiten

- [Caddy](caddy.md): dasselbe Routing mit einem PHP-FPM-Pool.
- [Kapitel 8, Abschnitt 8.4: FrankenPHP](../../installation/08-die-website-ausliefern.md#84-frankenphp),
  [FrankenPHP als Web-Engine von Exponential](../../bc/6.0/frankenphp.md) und
  [Velocity-Engines](../../bc/6.0/velocity-engines.md) (auf Englisch).
- [Velocity](velocity.md), [Webserver-Beispiele](README.md) und [alle Beispiele](../README.md).
- [.htaccess und config.php von der Konsole einrichten](../../anleitungen/setup-dateien-von-der-konsole.md).
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert werden.
