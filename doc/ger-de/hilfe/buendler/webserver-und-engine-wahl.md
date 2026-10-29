---
title: Webserver- und Engine-Wahl für Bundles
audience: bundler, hosting
category: bundling
order: 260
summary: Welche Webserver und Engines ein gebündeltes Exponential ausliefern können - Velocity (empfohlen), FrankenPHP, Apache, nginx, Caddy, LiteSpeed, IIS mit PHP-FPM - die Routing-Regeln, die jeder davon umsetzen muss, die geprüften Beispieldateien als Ausgangspunkt und wie Sie für jede Art von Bundle wählen.
keywords: Webserver, Engine, Routing, Document-Root, Beispielkonfiguration, web server, engine, Velocity, qbix, FrankenPHP, Apache, nginx, Caddy, LiteSpeed, IIS, PHP-FPM, .htaccess_root, routing, document root, example configuration, bundle
type: help
---
# Webserver- und Engine-Wahl für Bundles

Diese Seite richtet sich an Bündler, die entscheiden, was die Website in ihrem Bundle ausliefert. Sie beschreibt nur,
was die Dokumentation und die Beispiele des Projekts zeigen; die Beispiele liegen in `doc/eng-us/examples/web-servers/`.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hosting/control-panels/plesk.md), [cPanel](../hosting/control-panels/cpanel.md), [DirectAdmin](../hosting/control-panels/directadmin.md), [ISPConfig](../hosting/control-panels/ispconfig.md).

## Inhalt

1. [Die Regel, der jeder Server folgt](#die-regel-der-jeder-server-folgt)
2. [Die Auswahl](#die-auswahl)
3. [Die Engines von Velocity](#die-engines-von-velocity)
4. [Welcher für welches Bundle](#welcher-für-welches-bundle)
5. [Bevor Sie eine Serverkonfiguration ausliefern](#bevor-sie-eine-serverkonfiguration-ausliefern)

## Die Regel, der jeder Server folgt

"Was auch immer ausliefert, das Document-Root ist das **Wurzelverzeichnis der Installation** (das Verzeichnis mit
`index.php`), und es gilt dasselbe Routing: Eine kurze Liste von Asset-Pfaden wird als Dateien ausgeliefert, drei
Front-Controller laufen, und **jede andere Anfrage beantwortet `index.php`**, das nie eine Datei herausgibt. Das hält
`settings/`, `var/`, `vendor/`, `kernel/`, `composer.json` und die SQLite-Datenbank sicher, obwohl sie im
Document-Root liegen" ([Webserver-Beispiele](../../beispiele/webserver/README.md)).

Die Liste steht einmal in `.htaccess_root`, und Velocity setzt sie Regel für Regel um. Ein Bundle, das seine eigene
Webserver-Konfiguration schreibt, muss dieselbe Liste in derselben Reihenfolge umsetzen; die Beispiele tun das.

Nur Apache (mit `AllowOverride All`) und LiteSpeed Enterprise lesen `.htaccess` so, wie sie ausgeliefert wird; für sie
kopiert ein Bundle die Vorlage mit `php bin/php/console exp:setup:htaccess --apply` (`--static` für
`.htaccess_root_static`). OpenLiteSpeed liest nur ihre Rewrite-Regeln, mit `autoLoadHtaccess` und erst nach einem
Neustart; nginx, Caddy, FrankenPHP und IIS lesen sie nie; Velocity hat die Regeln eingebaut
([.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)).

## Die Auswahl

| Server | Beispiel als Ausgangspunkt | Wie das Beispiel geprüft wurde |
|---|---|---|
| **Velocity** (empfohlen) | [Velocity](../../beispiele/webserver/velocity.md), `velocity.ini.append.php.example`, `exponential-velocity.service` | jede Einstellung existiert in `settings/velocity.ini`; `systemd-analyze verify` |
| Apache, `.htaccess` | [Apache .htaccess](../../beispiele/webserver/apache-htaccess.md) | `httpd -t` und Testanfragen |
| Apache, virtueller Host | [Apache Virtual Host](../../beispiele/webserver/apache-virtueller-host.md) | `httpd -t`, Testanfragen über HTTP und HTTPS |
| nginx | [nginx](../../beispiele/webserver/nginx.md), `nginx.conf` | `nginx -t`, Testanfragen |
| Caddy | [Caddy](../../beispiele/webserver/caddy.md), `Caddyfile.example` | `frankenphp validate` |
| FrankenPHP | [FrankenPHP](../../beispiele/webserver/frankenphp.md), `frankenphp.Caddyfile.example` | `frankenphp validate` |
| LiteSpeed, OpenLiteSpeed | [LiteSpeed](../../beispiele/webserver/litespeed.md) | Regeln in Apaches `mod_rewrite` getestet, nicht auf LiteSpeed |
| IIS | [IIS](../../beispiele/webserver/iis.md), `web.config` | nur auf wohlgeformtes XML geprüft, nicht auf IIS ausgeführt |
| PHP-FPM hinter einem davon | [PHP-FPM-Pool](../../beispiele/webserver/php-fpm.md) | `php-fpm -t` |

Quelle: die Tabelle unter [Webserver-Beispiele](../../beispiele/webserver/README.md#welche-konfiguration). Beachten Sie die
beiden letzten Zeilen: Ein Bundle auf LiteSpeed oder IIS muss selbst testen.

## Die Engines von Velocity

`settings/velocity.ini [ServerSettings] Engine` wählt die Standard-Engine von Velocity
([Velocity-Engines](../../bc/6.0/velocity-engines.md#kurz-gesagt)):

| Engine | Rolle | Hinweise |
|---|---|---|
| `qbix` | empfohlen: jede Stufe, von der Entwicklung bis zur Produktion | dauerhafte Worker, Antwort-Cache, TLS, HTTP/2 |
| `frankenphp` | produktionsreife Alternative | eine heruntergeladene Binärdatei, per SHA-256 geprüft; PHP im klassischen Modus; noch kein Antwort-Cache |
| `php` | Entwicklung (der ausgelieferte Standard) | der eingebaute Server von PHP; reines HTTP |

"Die ausgelieferte Standard-Engine bleibt `php`"; "Um den Server zu verwenden, setzen Sie `Engine=qbix`". Ein Bundle,
das mit Velocity ausliefert, setzt die Engine bewusst ([Velocity in einem Bundle](velocity-in-einem-bundle.md)).

## Welcher für welches Bundle

| Bundle | Server |
|---|---|
| Container-Image | Velocity in der Vordergrundschleife des Containers ([Container-Images](container-images.md)) oder das PHP-FPM-Image Ihrer Plattform hinter einem Proxy |
| Server-Image | Velocity mit einer systemd-Unit, oder Apache oder nginx mit PHP-FPM |
| Panel-Installer | was das Panel betreibt (Apache oder nginx mit PHP-FPM, LiteSpeed); installieren Sie die passenden Regeln ([Installer für Hosting-Panels](installer-fuer-hosting-panels.md)) |
| Appliance | Velocity: ein Produkt von der Anfrage bis zur Seite, mit eigenem TLS |
| Demo oder Schulungsraum | Velocity, oder `php` für einen Laptop |

Der Hosting-Bereich vergleicht sie ausführlicher: [Einen Webserver wählen](../hosting/server/einen-webserver-waehlen.md).

## Bevor Sie eine Serverkonfiguration ausliefern

- [ ] Sie leitet genau so weiter wie `.htaccess_root`; die Testanfragen der Beispielseite (`/.git/config`, `/.env`,
      Skripte unterhalb des Wurzelverzeichnisses) liefern in Ihrem Bundle 404. Für ein Apache-Bundle endet
      `exp:setup:htaccess` mit Exit-Code 0 (die `.htaccess` ist eine Kopie der Vorlage).
- [ ] Das Document-Root ist das Wurzelverzeichnis der Installation.
- [ ] HTTPS ist konfiguriert oder ausdrücklich dem Empfänger überlassen.
- [ ] Das PHP, das die Website ausliefert, hat die Erweiterungen, nach denen `exp:velocity ext check` fragt.
- [ ] Die Einstellungen für vertrauenswürdige Proxys passen zum Netzwerk des Bundles
      ([13.10](../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header)).

## Verwandte Seiten

- [Kapitel 8: Die Website ausliefern](../../installation/08-die-website-ausliefern.md)
- [Velocity in einem Bundle](velocity-in-einem-bundle.md)
- [Velocity für Hoster](../hosting/server/velocity-fuer-hoster.md)
- [.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
