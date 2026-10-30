---
title: Velocity in einem Bundle
audience: bundler, hosting
category: bundling
order: 270
summary: Wie ein Bundle Exponential mit Velocity ausliefert, dem Web- und Anwendungsserver, der seit 6.0.15 Teil von Exponential ist - woher er kommt (Composer, deb und rpm, Docker-Images, Binärdateien), die Engine-Einstellung, Benutzer und root, Start beim Booten, das Ausrollen einer Änderung und was Sie prüfen.
keywords: Velocity, Engine, Benutzer, root, Start beim Booten, Ausrollen, exponential-velocity, qbix, velocity.ini, ServerSettings, User, Group, AllowRootWorkers, systemd, exp:velocity start, status, deploy, ext check, deb, rpm, ghcr.io, bundle
type: help
---
# Velocity in einem Bundle

Diese Seite richtet sich an Bündler, die die gebündelte Website mit Exponential Velocity ausliefern. "Seit 6.0.15
**verlangt** die `composer.json` im Wurzelverzeichnis `se7enxweb/exponential-velocity ~0.0.4.57` (über `se7enxweb/exponential-php-floor`, auf PHP 8.0 und neuer)", daher hat jede mit
Composer gebaute Installation es bereits
([Velocity-Engines](../../bc/6.0/velocity-engines.md#velocity-ist-seit-6015-teil-von-exponential)).

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
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [Woher Velocity kommt](#woher-velocity-kommt)
2. [Einstellungen, die ein Bundle setzt](#einstellungen-die-ein-bundle-setzt)
3. [Benutzer und root](#benutzer-und-root)
4. [Starten](#starten)
5. [Eine Änderung ausrollen](#eine-änderung-ausrollen)
6. [Prüfungen](#prüfungen)

## Woher Velocity kommt

| Form | Für | Quelle |
|---|---|---|
| Composer-Paket `se7enxweb/exponential-velocity` | jede Exponential-Installation | `composer.json` |
| deb- und rpm-Pakete | einen Systemdienst auf Debian 12 und 13, Ubuntu 22.04 und 24.04, Enterprise Linux 9 und 10 | [Velocity-Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#betriebssystempakete) |
| Docker-Images unter `ghcr.io/se7enxweb/exponential-velocity` | Container, amd64 und arm64 | [Velocity-Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#docker) |
| statische Binärdateien, das Phar, ein Quell-Kit | einzelne Maschinen, Laptops, Appliances, ungewöhnliche Plattformen | [Velocity-Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#eine-form-wählen) |

"Eine Exponential-Installation hat die Engine bereits über Composer und braucht nichts davon"; die anderen Formen sind
für Velocity außerhalb einer Installation. "Alle Dateien eines Releases tragen Prüfsummen in `SHA256SUMS`."

## Einstellungen, die ein Bundle setzt

Die Einstellungen stehen in `settings/velocity.ini`; ein Bundle überschreibt sie in
`settings/override/velocity.ini.append.php` (ausgehend von
`doc/eng-us/examples/web-servers/velocity.ini.append.php.example`) oder mit
`exp:velocity config set <Block> <Setting> <value>`, das dieselbe Datei schreibt
([Velocity-Beispiel](../../beispiele/webserver/velocity.md)).

```ini
[ServerSettings]
Engine=qbix
```

"`Engine` ist die **Standard**-Engine ... Sie wird als `php` ausgeliefert ... jede Website, von der Entwicklung bis
zur Produktion, setzt `qbix`." Der ausgelieferte Port von `qbix` ist `8088` auf `127.0.0.1`, "nur lokal oder über
einen Reverse Proxy erreichbar"; ein Bundle, das auf 80 und 443 antwortet, ändert die Adressen wie im Beispiel
gezeigt.

## Benutzer und root

- `[ServerSettings] User` und `Group` nennen den Benutzer, unter dem die Worker laufen; "Velocity als root zu starten
  ist in Ordnung: Nur sein Elternprozess bleibt root, und root-Worker werden abgelehnt, sofern nicht
  `AllowRootWorkers=enabled` gesetzt ist (ausgeliefert `disabled`)"
  ([Beschreibbare Verzeichnisse und Rechte](../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md#einrichtung-1-ein-benutzer)).
- `exp:velocity` selbst lehnt `root` ab, "sofern nicht `--allow-root-user` angegeben ist"
  ([4.6](../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)).
- Wenn Velocity und Apache oder nginx mit PHP-FPM dieselbe Installation ausliefern, "teilen sie `var/`, die Caches und
  die Sitzungen: Geben Sie ihnen denselben Benutzer oder diese Gruppeneinrichtung".

## Starten

- **Auf einem Server:** eine systemd-Unit, die `php bin/php/console exp:velocity start --allow-root-user` ausführt.
  Das Beispiel `doc/eng-us/examples/web-servers/exponential-velocity.service` wird "nicht als Unit ausgeliefert";
  kopieren Sie es, ersetzen Sie die Pfade und die Datenbank-Unit und "kombinieren Sie es nicht mit der Unit des
  Betriebssystempakets für dieselben Ports".
- **In einem Container:** `exp:velocity start` "löst den Server ab und kehrt zurück, sobald er antwortet", daher
  wiederholt der Einstiegspunkt `exp:velocity status` in einer Schleife
  ([Container-Images](container-images.md#ein-einstiegspunkt)).

## Eine Änderung ausrollen

"Eine geänderte PHP-Klasse erreicht die Worker von Velocity erst nach einem Neustart; `deploy` erledigt das in der
richtigen Reihenfolge":

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user
php bin/php/console exp:velocity deploy --allow-root-user
php bin/php/console exp:velocity deploy --kernel --allow-root-user   # a kernel class was added or renamed
```

Quelle: [Velocity-Engines](../../bc/6.0/velocity-engines.md#eine-php-änderung-bereitstellen-expvelocity-deploy). In Containern
"ist die übliche Entsprechung ein neues Image und ein neuer Container".

Auch `config.php` wird von jedem Worker einmal gelesen. Ein Bundle, das einen Wert mit `exp:config:set ... --apply`
setzt, startet Velocity danach neu (`exp:velocity restart`). Velocity liest `.htaccess` nicht; ein Bundle kopiert sie
(`exp:setup:htaccess --apply`) nur, wenn Apache denselben Baum ausliefern kann
([.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)).

## Prüfungen

```bash
php bin/php/console exp:velocity status
php bin/php/console exp:velocity ext check
```

`status` "meldet das Engine-Paket und seine Version"; `ext check` listet die PHP-Erweiterungen auf, die die
Installation braucht, und was fehlt. Velocity braucht `ext-sockets` (`pcntl` und `openssl` empfohlen).

## Verwandte Seiten

- [Webserver- und Engine-Wahl](webserver-und-engine-wahl.md)
- [Velocity für Hoster](../hosting/server/velocity-fuer-hoster.md), [Velocity startet nicht](../hosting/runbooks/velocity-startet-nicht.md)
- [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md)
- [.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
