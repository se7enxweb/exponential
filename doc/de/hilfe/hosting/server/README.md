---
title: Webserver und Engines
audience: hosting
category: hosting
order: 10
summary: Überblick über den Abschnitt zu Webservern für Hosting-Unternehmen - welche Server und Engines Exponential ausliefern, wie sie sich vergleichen, und ein Wegweiser in einer Zeile zu jeder Seite des Abschnitts.
keywords: Webserver, Engine, Velocity, qbix, FrankenPHP, Apache, nginx, Caddy, LiteSpeed, OpenLiteSpeed, IIS, PHP-FPM, Überblick, Hosting, web server, overview, hosting
type: help
---
# Webserver und Engines

Dieser Abschnitt ist für Systemadministratoren und Produktmanager bei Hosting-Unternehmen, die entscheiden müssen, was eine
Exponential-Website auf ihren Maschinen ausliefert, und es einrichten. Er beantwortet: hinter welchen Webservern und Engines Exponential
läuft, welche das Projekt empfiehlt, welche zu welchem Hosting-Modell passt und wo die genauen Einstellungen stehen.

Zurück zu [Hosting-Unternehmen: hier beginnen](../hosting-anbieter-hier-beginnen.md).

## Inhalt

1. [Die kurze Antwort](#die-kurze-antwort)
2. [Die Server auf einen Blick](#die-server-auf-einen-blick)
3. [Seiten in diesem Abschnitt](#seiten-in-diesem-abschnitt)
4. [Was jede Einrichtung gemeinsam hat](#was-jede-einrichtung-gemeinsam-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Die kurze Antwort

- **Exponential Velocity** (die Engine `qbix`) ist der vom Projekt **empfohlene** Weg, eine Website in jeder Phase auszuliefern, von der
  Entwicklung bis zur Produktion. Es ist ein in PHP geschriebener Webserver, der die Anwendung in persistenten Workern geladen hält,
  wiederholte Seiten aus seinem eigenen Antwort-Cache beantwortet und HTTPS selbst ausliefert
  ([Kapitel 8](../../../../en/us/install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way)).
- **FrankenPHP**, gesteuert über denselben Befehl `exp:velocity`, ist die **produktionsreife Alternative**.
- **Apache mit PHP-FPM** und **nginx mit PHP-FPM** sind die klassischen Einrichtungen. Sie bleiben vollständig unterstützt und sind das, was
  Shared Hosting meist anbietet, weil Velocity einen dauerhaft laufenden Prozess braucht, den Shared Hosting meist nicht erlaubt
  (`README.md`, Abschnitt darüber, wann ein klassischer Server noch die richtige Antwort ist).
- Jeder andere Webserver, der eine Anfrage an PHP weitergeben und eine URL auf `index.php` umschreiben kann, liefert Exponential ebenfalls
  aus ([Unterstützung von Betriebssystemen, Webserver](../../../../en/us/help/os-support.md#web-servers)).

## Die Server auf einen Blick

Die Stufen stammen aus [Unterstützung von Betriebssystemen](../../../../en/us/help/os-support.md#web-servers).

| Server | Status in den Quellen | Typisches Hosting-Modell | Seite |
|---|---|---|---|
| Exponential Velocity (`qbix`) | für jede Phase empfohlen; Stufe 1 | VPS, dediziert, Container, verwaltet | [Velocity für Hoster](velocity-fuer-hoster.md) |
| Apache httpd 2.4 mit PHP-FPM | Stufe 1 | Shared, Panels, VPS | [Apache mit PHP-FPM](apache-mit-php-fpm.md) |
| FrankenPHP (über `exp:velocity`) | produktionsreife Alternative | VPS, dediziert, Container | [FrankenPHP](frankenphp.md) |
| nginx mit PHP-FPM | Stufe 2 | VPS, dediziert | [nginx mit PHP-FPM](nginx-mit-php-fpm.md) |
| LiteSpeed, OpenLiteSpeed | Stufe 3 | Shared Hosting auf LiteSpeed | [LiteSpeed](litespeed-und-openlitespeed.md) |
| Caddy mit PHP-FPM | Stufe 3 | VPS | [Caddy](caddy.md) |
| Microsoft IIS mit FastCGI | Stufe 3 | Windows-Hosting | [IIS](microsoft-iis.md) |
| Der eingebaute Server von PHP | nur Entwicklung | nie für Besucher | [Einen Webserver wählen](einen-webserver-waehlen.md) |

> **Hinweis**
> Stufe 3 heißt „sollte funktionieren“: eine begründete Erwartung, kein Testergebnis
> ([Die vier Stufen](../../../../en/us/help/os-support.md#the-four-tiers)). Testen Sie einen Server der Stufe 3 auf einer Staging-Maschine, bevor Sie einen
> Tarif verkaufen, der davon abhängt.

## Seiten in diesem Abschnitt

| Seite | Was sie beantwortet |
|---|---|
| [Einen Webserver wählen](einen-webserver-waehlen.md) | Welcher Server oder welche Engine zu welchem Tarif passt, mit einer Entscheidungstabelle |
| [Apache mit PHP-FPM](apache-mit-php-fpm.md) | Die klassische Einrichtung: `.htaccess_root`, der virtuelle Host, PHP-FPM-Pools, Plesk |
| [nginx mit PHP-FPM](nginx-mit-php-fpm.md) | Der abgeleitete Server-Block, Regel für Regel, und wie Sie ihn prüfen |
| [Caddy](caddy.md) | Was die Quellen über Caddy sagen, und das nachzubildende Routing |
| [LiteSpeed](litespeed-und-openlitespeed.md) | LiteSpeed und OpenLiteSpeed mit den mitgelieferten Apache-Regeln |
| [IIS](microsoft-iis.md) | Windows-Hosting mit IIS, FastCGI und dem Modul URL Rewrite |
| [Velocity für Hoster](velocity-fuer-hoster.md) | Velocity auf einem Hosting-Server installieren, konfigurieren und betreiben |
| [Velocity-Worker und Pools](velocity-worker-und-pools.md) | Poolgröße, der dynamische Pool, Instanzen, Speicher als PSS gemessen |
| [HTTPS-Zertifikate in Velocity](https-zertifikate-in-velocity.md) | Eigene Zertifikate, Let's Encrypt, der selbstsignierte Ersatz, HSTS |
| [FrankenPHP](frankenphp.md) | Die verifizierte Binärdatei, ihr eingebettetes PHP und ihre Grenzen |
| [Velocity neben Apache oder nginx](velocity-neben-apache-oder-nginx.md) | Beide auf denselben Dateien betreiben, hinter einem Proxy oder während einer Migration |
| [Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben](rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md) | Eine öffentliche Demo im Demomodus (`EXP_DEMO_MODE=1`), alle zwei Stunden aus dem aktuellen Code neu aufgebaut |

## Was jede Einrichtung gemeinsam hat

Was auch immer die Website ausliefert, es gelten dieselben Regeln
([Kapitel 8, Wie eine Anfrage ausgeliefert wird](../../../../en/us/install/08-serving-the-site.md#82-how-a-request-is-served)):

- Der **Document Root ist das Wurzelverzeichnis der Installation**, das Verzeichnis, das `index.php` enthält.
- Das Routing ist einmal aufgeschrieben, in `.htaccess_root`. Die Engines von Velocity und die erzeugte Konfiguration von FrankenPHP
  setzen dieselbe Liste Regel für Regel um.
- Keine PHP-Datei läuft, weil ihr Pfad angefragt wurde, außer den Front-Controllern in der Wurzel (`index.php`, `index_rest.php`,
  `index_treemenu.php`).
- Nur aufgeführte Asset-Pfade werden als Dateien gesendet; `settings/site.ini`, die SQLite-Datenbank oder eine Quelldatei des Kernels,
  die per Pfad angefragt werden, erreichen `index.php`, nie die Datei.
- Der Benutzer, der PHP ausführt, muss `var/` und die Verzeichnisse unter `settings/` schreiben können, und **ein** Benutzer (oder eine
  Gruppe) sollte sie schreiben, welcher Server auch läuft
  ([Kapitel 8, Dateiberechtigungen und Eigentum](../../../../en/us/install/08-serving-the-site.md#87-file-permissions-and-ownership)).

Prüfen Sie das Routing auf jedem Server mit den vier Anfragen aus Kapitel 8 und ersetzen Sie die Adresse durch Ihre eigene:

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/.git/config                      # 404 (403 from qbix)
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/kernel/classes/ezcontentobject.php  # 404
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/robots.txt                        # 200 when the file exists
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'                # 0
```

Ein `200` für die Anfrage an `.git` oder den Kernel bedeutet, dass die Regeln nicht aktiv sind. Halten Sie an und beheben Sie das, bevor
die Website von außen erreichbar ist.

## Verwandte Seiten

- [Hosting-Unternehmen: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Leistung](../leistung/README.md)
- [Kapitel 8: Die Website ausliefern](../../../../en/us/install/08-serving-the-site.md)
- [Unterstützung von Betriebssystemen](../../../../en/us/help/os-support.md)
- [Velocity-Engines](../../../../en/us/bc/6.0/velocity-engines.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
