---
title: PHP-FPM-Pool
audience: admin, developer
category: examples
order: 90
summary: Ein PHP-FPM-Pool für eine Exponential-6-Installation hinter Apache, nginx oder Caddy: eigener Benutzer und Socket, Bemessung der Worker, die PHP-Grenzen, die Exponential braucht, Sessions, OPcache und welcher Master nach einer Änderung neu geladen werden muss.
keywords: PHP-FPM, Pool, Socket, Worker, Sessions, Neu laden, Beispiel, FPM, pool, php-fpm.d, listen, socket, pm, max_children, memory_limit, upload_max_filesize, post_max_size, OPcache, session.save_path, php_admin_value, reload, Plesk, plesk-php85-fpm, example
type: example
---
# PHP-FPM-Pool

Apache, nginx und Caddy geben PHP an einen **PHP-FPM-Pool** weiter. Ein Pool pro Website hält die Websites
voneinander getrennt: eigener Benutzer, eigener Socket, eigene Grenzen.
[`php-fpm-pool.conf`](../../../eng-us/examples/web-servers/php-fpm-pool.conf) ist ein Pool für eine Installation,
abgeleitet aus [Kapitel 8, Abschnitt 8.5.2](../../installation/08-die-website-ausliefern.md#852-php-fpm) und dem
Kapitel zur Sicherheitshärtung. Velocity und FrankenPHP brauchen ihn nicht: Sie führen PHP selbst aus.

## Installieren

1. Kopieren Sie die Datei in das Pool-Verzeichnis des PHP, das die Website ausliefert:
   `/etc/php-fpm.d/` (Red-Hat-Stil), `/etc/php/8.x/fpm/pool.d/` (Debian und Ubuntu). Unter Plesk schreibt Plesk die
   Pool-Dateien selbst (`/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`): Setzen Sie die Werte stattdessen in
   den PHP-Einstellungen der Domain.
2. Ersetzen Sie `example.com`, Benutzer und Gruppe (den Eigentümer der Dateien der Installation) sowie `listen.owner` /
   `listen.group` (den Benutzer des Webservers: `apache`, `www-data`, `nginx` oder `caddy`).
3. Legen Sie das Log-Verzeichnis an und, für Sessions in Dateien, das Session-Verzeichnis (`mkdir -p
   /var/lib/php/session/example.com; chown example: ...; chmod 700 ...`).
4. Prüfen und neu laden:

   ```bash
   php-fpm -t                     # or php-fpm8.3 -t, the binary of that PHP version
   systemctl reload php-fpm       # the master that serves the site, see below
   ```

## Die Datei

```ini
; Exponential 6: a PHP-FPM pool for one installation
;
; Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md.
;
; Derived from doc/eng-us/install/08-serving-the-site.md (8.5.2) and 13 (security
; hardening); not shipped by the kernel. Explained in php-fpm.md next to this
; file. One pool per site, running as the user the installation's files
; belong to, listening on a socket only the web server can open.
;
; Install: copy to the pool directory of the PHP that serves the site
; (/etc/php-fpm.d/ on Red Hat style systems, /etc/php/8.x/fpm/pool.d/ on
; Debian, /opt/plesk/php/8.x/etc/php-fpm.d/ is Plesk's own and written by
; Plesk), replace "example" and the paths, check with "php-fpm -t" and reload
; the master that serves the site ("systemctl reload php-fpm", or the unit
; [DeploySettings] PhpFpmService=auto finds: exp:velocity deploy --dry-run).

[example.com]
; The user and group the installation's files belong to (8.7)
user = example
group = example

; The socket the web server passes requests to: the same path as
; "proxy:unix:...|fcgi://localhost" (Apache), fastcgi_pass (nginx) or
; reverse_proxy unix//... (Caddy). Only the web server's user may open it.
listen = /run/php-fpm/example.com.sock
listen.owner = apache
listen.group = apache
listen.mode = 0660

; Workers: "ondemand" starts them when requests come and suits small sites;
; "dynamic" keeps spares for busy ones. Size max_children by memory: each
; worker can use up to memory_limit.
pm = dynamic
pm.max_children = 16
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 1000

; Requests slower than this are logged with a backtrace
request_slowlog_timeout = 10s
slowlog = /var/log/php-fpm/example.com-slow.log
; A hard limit per request, a little above max_execution_time
request_terminate_timeout = 300s

; The front controllers are .php files; no other extension runs
security.limit_extensions = .php

; Environment: FPM clears it by default, which is right; PATH for the
; programs the kernel may start (image conversion, git for the help)
clear_env = yes
env[PATH] = /usr/local/bin:/usr/bin:/bin

; PHP settings of this pool (php_admin_*: a script cannot change them)
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 120
php_admin_value[upload_max_filesize] = 32M
php_admin_value[post_max_size] = 64M
php_admin_value[max_input_vars] = 10000
php_admin_flag[display_errors] = off
php_admin_flag[log_errors] = on
php_admin_value[error_log] = /var/log/php-fpm/example.com-error.log
php_admin_flag[expose_php] = off
php_admin_value[date.timezone] = UTC

; Sessions: with site.ini [Session] Handler empty (the default) PHP keeps
; them, in files. A directory of this site only, owned by the pool's user,
; mode 0700 (not shared with other sites on the machine). With
; Handler=ezpSessionHandlerDB the database keeps them and this is unused.
php_value[session.save_handler] = files
php_value[session.save_path] = /var/lib/php/session/example.com
php_admin_flag[session.cookie_httponly] = on
php_admin_flag[session.use_strict_mode] = on

; OPcache: on, with room for the kernel and the extensions. After a change of
; a PHP class, reload the pool (or set revalidate_freq above 0 and wait).
php_admin_flag[opcache.enable] = on
php_admin_value[opcache.memory_consumption] = 256
php_admin_value[opcache.max_accelerated_files] = 20000
php_admin_value[opcache.interned_strings_buffer] = 32
php_admin_value[opcache.revalidate_freq] = 2
```

## Worauf es für Exponential ankommt

- **Grenzen.** `memory_limit` mindestens 256M (mehr für große Importe und Bildbearbeitung), `upload_max_filesize` und
  `post_max_size` über Ihrem größten Upload (und über der eigenen Grenze des Webservers: `client_max_body_size` in nginx,
  `maxAllowedContentLength` in IIS), `max_input_vars` hoch genug für große Formulare von Klassen und Rollen.
- **`php_admin_*`**-Werte kann ein Skript nicht ändern, `php_value`-Werte schon (den Session-Pfad, damit eine Website
  ihn verlegen kann).
- **Sessions.** Ist `site.ini [Session] Handler` leer (der Standard), hält PHP die Sessions in Dateien: Geben Sie jeder
  Website ein eigenes Verzeichnis, nicht das gemeinsame Standardverzeichnis. Mit `Handler=ezpSessionHandlerDB` hält die
  Datenbank sie.
- **OPcache** eingeschaltet, mit Platz für den Kernel und die Erweiterungen. Mit `revalidate_freq=2` wird eine
  geänderte Datei innerhalb von zwei Sekunden erkannt; setzen Sie den Wert während der Entwicklung auf `0`, oder lassen
  Sie ihn höher und laden Sie den Pool bei jedem Deployment neu.
- **`security.limit_extensions = .php`**: Nur `.php`-Dateien werden ausgeführt, und die Regeln des Webservers schicken
  nur die drei Front-Controller dorthin.

## Den richtigen Master neu laden

Laden Sie nach einer Änderung an einer PHP-Klasse, einem Operator oder einer INI-Datei den FPM-Master neu, der **die
Website ausliefert**, und leeren Sie danach die Caches, die gerenderte Ausgaben halten. Auf Rechnern mit mehreren
PHP-Versionen ist das oft nicht der Master namens `php-fpm` (Plesk: `plesk-php85-fpm` für PHP 8.5). `exp:velocity
deploy` findet ihn anhand der Pool-Dateien (`[DeploySettings] PhpFpmService=auto`) und führt die Schritte in der
richtigen Reihenfolge aus:

```bash
php bin/php/console exp:velocity deploy --dry-run --no-velocity --allow-root-user   # shows the unit it found and why
php bin/php/console exp:velocity deploy --no-velocity --allow-root-user
```

So wurde dieses Beispiel geprüft: `php-fpm -t` mit einer globalen Wegwerf-Konfiguration, die den Pool einbindet
(Socket- und Log-Pfade in ein Arbeitsverzeichnis verlegt); der Pool wurde nicht gestartet.

## Verwandte Seiten

- [Kapitel 8, Abschnitt 8.5.2: PHP-FPM](../../installation/08-die-website-ausliefern.md#852-php-fpm) und
  [Abschnitt 8.3.14: Eine Änderung bereitstellen](../../installation/08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy).
- [Kapitel 8, Abschnitt 8.7: Dateiberechtigungen und Eigentümer](../../installation/08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer).
- [Apache virtueller Host](apache-virtueller-host.md), [nginx](nginx.md), [Caddy](caddy.md).
- [Webserver-Beispiele](README.md) und [alle Beispiele](../README.md).
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite dürfen Sie frei kopieren, übersetzen und verbessern.
