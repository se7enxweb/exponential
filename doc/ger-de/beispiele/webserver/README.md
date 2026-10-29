---
title: Webserver-Beispiele
audience: admin, developer
category: examples
order: 10
summary: Vollständige, geprüfte Konfigurationen, mit denen Sie Exponential 6 über Velocity, Apache, nginx, Caddy, FrankenPHP, LiteSpeed, IIS und PHP-FPM ausliefern, jede mit denselben Routing-Regeln wie die ausgelieferte .htaccess_root.
keywords: Webserver, Konfiguration, Beispiel, Rewrite-Regeln, virtueller Host, Apache, .htaccess, nginx, Caddy, FrankenPHP, LiteSpeed, OpenLiteSpeed, IIS, web.config, PHP-FPM, Velocity, HTTPS, web server, virtual host, rewrite rules, configuration, example
type: example
---
# Webserver-Beispiele

Exponential ist ein Verzeichnis mit PHP-Dateien. Gleich welcher Webserver es ausliefert: Das Document Root ist das
**Wurzelverzeichnis der Installation** (das Verzeichnis mit `index.php`), und es gilt dasselbe Routing. Eine kurze Liste
von Asset-Pfaden wird als Datei gesendet, drei Front-Controller werden ausgeführt, und **jede andere Anfrage beantwortet
`index.php`**, das niemals eine Datei herausgibt. Genau das schützt `settings/`, `var/`, `vendor/`, `kernel/`,
`composer.json` und die SQLite-Datenbank, obwohl sie innerhalb des Document Root liegen. Die Liste ist an einer
einzigen Stelle festgehalten, in `.htaccess_root`, und Velocity setzt sie Regel für Regel um
(`expVelocity::STATIC_PATHS`, `FRONT_CONTROLLERS` und `ENTRY_SCRIPTS` in `kernel/classes/expvelocity.php`).

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

Jedes Beispiel auf diesen Seiten ist eine vollständige Datei zum Kopieren; ersetzen Sie darin `example.com` und
`/var/www/example.com`. Jedes setzt dieselbe Liste um, in dieser Reihenfolge:

1. Eine Punktdatei oder ein Punktverzeichnis (`.git`, `.env`, `.htaccess`) beantwortet überall 404; `.well-known` ist
   davon ausgenommen.
2. Eine vorhandene `.php`-, `.phtml`- oder `.phar`-Datei unterhalb des Wurzelverzeichnisses beantwortet 404: Kein Skript
   wird ausgeführt, nur weil sein Pfad angefragt wurde.
3. `/api/...` und `/index_rest.php` gehen an `index_rest.php`, `/content/treemenu...` (mit oder ohne vorangestellten
   Siteaccess) an `index_treemenu.php`.
4. Die Asset-Liste wird als Datei gesendet: Design- und Erweiterungs-Design-Verzeichnisse, `share/icons`, die
   Icon-Themes der Erweiterungen (`extension/<name>/icons/`: SVG-, PNG- und GIF-Bilder mit genau einer Dateiendung),
   gespeicherte Bilder, die Bild-Originale, gespeicherte PDF-Exporte, die öffentlichen Caches, Paketvorschauen,
   `favicon.ico`, `robots.txt`, `index.js`, `sw.js` und `w3c/p3p.xml`.
5. Alles andere geht an `index.php`.

## Welche Konfiguration

| Server | Seite | Datei zum Kopieren | Wie geprüft wurde |
|---|---|---|---|
| **Velocity** (empfohlen) | [Velocity](velocity.md) | `velocity.ini.append.php.example`, `exponential-velocity.service` | jede Einstellung existiert in `settings/velocity.ini`; `systemd-analyze verify` |
| Apache, `.htaccess` | [Apache .htaccess](apache-htaccess.md) | `../htaccess` (= `.htaccess_root` + optionale Blöcke) | `httpd -t`; ein Wegwerf-Apache beantwortete jede Testanfrage wie erwartet |
| Apache, virtueller Host | [Apache, virtueller Host](apache-virtueller-host.md) | `../ezpublish.conf` | `httpd -t`; ein Wegwerf-Apache beantwortete über HTTP und HTTPS jede Testanfrage wie erwartet |
| nginx | [nginx](nginx.md) | `nginx.conf` | `nginx -t`; ein Wegwerf-nginx beantwortete über HTTP und HTTPS jede Testanfrage wie erwartet |
| Caddy | [Caddy](caddy.md) | `Caddyfile.example` | `frankenphp validate` (Caddy 2.11 in FrankenPHP 1.12.7) |
| FrankenPHP | [FrankenPHP](frankenphp.md) | `frankenphp.Caddyfile.example` | `frankenphp validate` |
| LiteSpeed, OpenLiteSpeed | [LiteSpeed](litespeed.md) | `openlitespeed-vhost.conf` | nicht auf LiteSpeed ausgeführt; seine Rewrite-Regeln beantworteten in Apaches `mod_rewrite` jede Testanfrage wie erwartet |
| IIS | [IIS](iis.md) | `web.config` | nur wohlgeformtes XML (`xmllint`); nicht auf IIS ausgeführt |
| PHP-FPM (hinter Apache, nginx, Caddy) | [PHP-FPM-Pool](php-fpm.md) | `php-fpm-pool.conf` | `php-fpm -t` |

Die Testanfragen: `/.git/config`, `/.env`, Skripte unterhalb des Wurzelverzeichnisses (in `extension/`, `kernel/`,
`vendor/`, im Paketspeicher, in einem Asset-Verzeichnis) und eine Punktdatei in einem Asset-Verzeichnis müssen 404
beantworten, ohne ihren Inhalt zu zeigen; `/settings/site.ini`, `/composer.json`, `/config.php`, `/var/log/error.log`
und ein gespeichertes Original, das kein Bild ist, müssen bei `index.php` ankommen und dürfen niemals gesendet werden;
`/api/...` muss bei `index_rest.php` ankommen und `/content/treemenu/...` bei `index_treemenu.php`; jede Art von
gelistetem Asset muss gesendet werden (200), und ein hochgeladenes SVG muss die Content Security Policy `sandbox`
tragen. Führen Sie dieselben Anfragen gegen Ihren eigenen Server mit den Befehlen aus
[Kapitel 13, Abschnitt 13.2](../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf) aus.

## Zwei Dinge, die jedes Setup gemeinsam hat

- **Verwenden Sie niemals eine generische Konfiguration nach dem Muster „Datei ausliefern, wenn sie existiert, sonst
  `index.php`“** (nginx `try_files $uri /index.php`, Caddys `php_fastcgi` oder `php_server`): Sie gibt jede vorhandene
  Datei heraus, `settings/*.ini` eingeschlossen.
- **`.well-known` ist erreichbar, steht aber nicht auf der Asset-Liste.** Die Punktregel lässt es in Ruhe, danach geht
  die Anfrage wie jede andere an `index.php`. Ein Zertifikats-Client, der dort Challenge-Dateien ablegt
  (`certbot --webroot`), braucht einen Host für reines HTTP, der sie ausliefert, wie es die Hosts für Port 80 in den
  Beispielen für Apache und nginx tun; Caddy, FrankenPHP und die Engine `qbix` von Velocity beantworten
  ACME-Challenges selbst.

**Welche Server `.htaccess` lesen.** Apache (mit `AllowOverride All`) und LiteSpeed Enterprise lesen sie bei der
nächsten Anfrage: Dort kopiert `./console exp:setup:htaccess --apply` die Datei `.htaccess_root` an ihren Platz
(`--static` kopiert `.htaccess_root_static`), mehr ist nicht nötig. OpenLiteSpeed liest nur ihre Rewrite-Regeln, nur
mit eingeschaltetem `autoLoadHtaccess` und erst nach einem Neustart. nginx, Caddy, FrankenPHP und IIS lesen sie nie:
Ihre Regeln stehen in den Konfigurationen dieser Seiten. Velocity hat die Regeln von `.htaccess_root` eingebaut;
kopieren Sie die Datei trotzdem, wenn Apache denselben Baum ausliefern könnte. Siehe
[.htaccess und config.php von der Konsole einrichten](../../anleitungen/setup-dateien-von-der-konsole.md).

**WebDAV** wird von keinem dieser Beispiele weitergeleitet: Es ist standardmäßig aus. Seit 6.0.15 ist sein
Einstiegsskript `index_webdav.php` (`webdav.php` leitet bis zu einer späteren Hauptversion nur dorthin weiter). Um es
auszuliefern, fügen Sie auf Apache, nginx oder IIS eine Regel für `index_webdav.php` hinzu oder richten einen eigenen
WebDAV-Host ein; Velocity, Caddy und FrankenPHP führen es nicht aus. Regeln vorher/nachher für jeden Server und wie Sie
sie testen: [Das WebDAV-Einstiegsskript heißt jetzt index_webdav.php](../../bc/6.0/webdav-einstiegsskript-index-webdav.md).

## Ebenfalls in diesem Verzeichnis

- [`../staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf): Apache-Regeln, die eine vom
  statischen Inhalts-Cache erzeugte Seite ausliefern, ohne PHP zu starten (`.htaccess_root_static` ist `.htaccess_root`,
  in dem sie bereits enthalten sind).
- [`../README.md`](../README.md): alle Beispiele der Dokumentation.

**Öffentliche Demos.** Die Einstellungen, die für jeden dieser Server den Demomodus einschalten (`EXP_DEMO_MODE=1`),
finden Sie unter [Beispiele für den Demomodus](../demomodus/README.md).

## Verwandte Seiten

- [Kapitel 8, Die Site ausliefern](../../installation/08-die-website-ausliefern.md): Velocity, Apache, nginx und FrankenPHP Schritt für Schritt.
- [Kapitel 13, Sicherheitshärtung](../../installation/13-sicherheitshaertung.md): was der Webserver niemals ausliefern darf, Header, HTTPS.
- [Velocity-Engines](../../bc/6.0/velocity-engines.md) und [FrankenPHP](../../bc/6.0/frankenphp.md) (auf Englisch).
- [.htaccess und config.php von der Konsole einrichten](../../anleitungen/setup-dateien-von-der-konsole.md): `exp:setup:htaccess`, `exp:setup:config`, `exp:config:*`.
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert werden.
