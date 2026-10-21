---
title: Caddy
audience: hosting
category: hosting
order: 50
summary: Was die Quellen von Exponential über Caddy sagen, die zwei Wege, es zu nutzen (FrankenPHP, also Caddy mit eingebautem PHP, oder Caddy mit PHP-FPM), das Routing, das jede Caddy-Einrichtung nachbilden muss, und die Fallen, die Sie meiden sollten.
keywords: Caddy, Caddyfile, FrankenPHP, php_fastcgi, php_server, PHP-FPM, Rewrite, route, automatisches HTTPS, SiteInclude, Stufe 3, rewrite, automatic HTTPS, tier 3
type: help
---
# Caddy

Diese Seite ist für Systemadministratoren bei Hosting-Unternehmen, die Caddy nutzen oder nutzen wollen. Sie beantwortet: wie Caddy mit
Exponential zusammenhängt, welchen der zwei Wege mit Caddy das Projekt am besten unterstützt, welches Routing eine Caddy-Konfiguration
umsetzen muss und was die Quellen bieten und was nicht.

## Inhalt

1. [Zwei Wege, Caddy zu nutzen](#zwei-wege-caddy-zu-nutzen)
2. [FrankenPHP: Caddy, von Exponential verwaltet](#frankenphp-caddy-von-exponential-verwaltet)
3. [Caddy mit PHP-FPM](#caddy-mit-php-fpm)
4. [Das Routing, das jede Caddy-Konfiguration braucht](#das-routing-das-jede-caddy-konfiguration-braucht)
5. [HTTPS](#https)
6. [Checkliste](#checkliste)

## Zwei Wege, Caddy zu nutzen

| | FrankenPHP (über `exp:velocity`) | Caddy mit PHP-FPM |
|---|---|---|
| Was es ist | Caddy mit eingebautem PHP, als eine Binärdatei mit geprüfter Prüfsumme heruntergeladen (`README.md`) | der Webserver Caddy, der Anfragen an einen PHP-FPM-Pool weitergibt |
| Status | produktionsreife alternative Engine | Stufe 3, sollte funktionieren ([Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md#webserver)) |
| Konfiguration | ein Caddyfile, bei jedem Start aus `settings/velocity.ini` **erzeugt** | ein Caddyfile, das Sie schreiben |
| Vom Projekt ausgeliefert | ja, gesteuert über `exp:velocity` | keine Konfiguration wird ausgeliefert |

Für einen Hoster, der Caddy mag, ist FrankenPHP der Weg, den das Projekt dokumentiert und für den es Konfiguration erzeugt. Lesen Sie
[FrankenPHP](frankenphp.md).

## FrankenPHP: Caddy, von Exponential verwaltet

`exp:velocity` schreibt `var/vc/frankenphp/run/Caddyfile` bei jedem `start`, `graceful` und `restart`, prüft es mit
`frankenphp validate`, bevor es das alte ersetzt, und steuert den Server über die Admin-API von Caddy auf einem Socket, der nur dem
Eigentümer gehört ([Kapitel 8, FrankenPHP](../../../installation/08-die-website-ausliefern.md#84-frankenphp)). Der Kopf der Datei sagt, dass sie jedes
Mal neu geschrieben wird; bearbeiten Sie sie also nie, sondern setzen Sie Ihre eigenen Direktiven in eine Datei, die
`[FrankenPHPSettings] SiteInclude` nennt und die in jeden Site-Block importiert wird
([FrankenPHP als Web-Engine von Exponential](../../../../eng-us/bc/6.0/frankenphp.md)).

```bash
php bin/php/console exp:velocity ctl caddyfile --engine=frankenphp --allow-root-user   # print the generated Caddyfile
php bin/php/console exp:velocity ctl validate  --engine=frankenphp --allow-root-user   # write and validate it
```

## Caddy mit PHP-FPM

Die Quellen beschreiben diese Einrichtung in einer Zeile: `php_fastcgi` und ein Rewrite auf `index.php`
([Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md#webserver)), und die Tabelle der klassischen Server im README nennt `rewrite`,
`handle` und `try_files` von Caddy. Mit Exponential wird kein Caddyfile für PHP-FPM ausgeliefert; die Konfiguration schreiben und testen
also Sie, wie bei nginx.

> **Warnung**
> Nutzen Sie keine Direktive, die jede vorhandene Datei unterhalb der Wurzel ausliefert. Die Integration von FrankenPHP meidet bewusst
> `php_server` von Caddy, weil dessen Standard `settings/*.ini`, die SQLite-Datenbank und Quellen des Kernels ausliefern würde
> ([FrankenPHP, Das erzeugte Caddyfile](../../../../eng-us/bc/6.0/frankenphp.md#5-the-generated-caddyfile)). Was Sie auch für PHP-FPM schreiben, es
> darf nur die aufgeführten Asset-Pfade als Dateien senden und alles andere an einen Front-Controller. Prüfen Sie in der Dokumentation
> Ihrer Caddy-Version, wie deren PHP-Direktiven vorhandene Dateien behandeln.

Ein praktischer Anfang: Erzeugen Sie das Caddyfile von FrankenPHP auf einer Testmaschine mit
`exp:velocity ctl caddyfile --engine=frankenphp`, lesen Sie seinen Block `route` und bilden Sie dieselben Matcher vor Ihrem
PHP-FPM-Handler nach.

## Das Routing, das jede Caddy-Konfiguration braucht

Das erzeugte Caddyfile setzt `.htaccess_root` so um
([FrankenPHP, Was es enthält](../../../../eng-us/bc/6.0/frankenphp.md#what-it-contains-verified-output)):

| Schritt | Matcher | Aktion |
|---|---|---|
| 1 | `@static`: `expVelocity::STATIC_PATHS` und nicht `expVelocity::NEVER_STATIC` | die Datei ausliefern, mit `Cache-Control`, wenn `StaticMaxAge` über 0 liegt |
| 2 | `@rest`: `^/(api/|index_rest\.php)` | auf `/index_rest.php` umschreiben |
| 3 | `@treemenu`: `^/([^/]+/)?content/treemenu` | auf `/index_treemenu.php` umschreiben |
| 4 | `@front`: alles andere | auf `/index.php` umschreiben |

`STATIC_PATHS` trifft Stylesheets, Bilder, Skripte und Schriften von Designs und Erweiterungen, `share/icons/`, die
Icon-Themes der Erweiterungen (`extension/<name>/icons/`, Bilder mit genau einer Dateiendung), `var/*/storage/images`,
gespeicherte Originalbilder, gespeicherte PDF-Exporte, die Caches public und texttoimage, Paketstile, `var/storage/packages/`,
`favicon.ico`, `robots.txt`, `index.js`, `sw.js` und `w3c/p3p.xml`. `NEVER_STATIC` ist `(?i)(\.(php\d?|phtml|phar)$|/\.)`: Keine
PHP-Quelle und keine Datei mit Punkt wird je als Datei ausgeliefert, auch nicht unterhalb eines Asset-Verzeichnisses. Beide Konstanten
stehen in `kernel/classes/expvelocity.php`.

## HTTPS

Das README erwähnt das automatische HTTPS, das Caddy von Haus aus mitbringt. In der Integration von FrankenPHP ist es ausgeschaltet
(`auto_https off`): Die Einstellungen von Velocity entscheiden über das Zertifikat, entweder `[HTTPSSettings] Certificate` und `Key` oder
ein für die Maschine erstelltes selbstsigniertes Zertifikat. Bei einem eigenständigen Caddy mit PHP-FPM ist die Verwaltung der
Zertifikate Sache von Caddy; folgen Sie seiner Dokumentation. Wer auch immer TLS beendet, Exponential muss erfahren, dass die Anfrage
HTTPS war: direkt (`HTTPS` an PHP übergeben) oder von einem vertrauenswürdigen Proxy
([Varnish und Reverse Proxys](../leistung/varnish-und-reverse-proxys.md)).

## Checkliste

- [ ] Zwischen FrankenPHP (erzeugte Konfiguration) und Caddy mit PHP-FPM (eigene Konfiguration) entschieden.
- [ ] Für PHP-FPM: die Routing-Tabelle oben nachgebildet; nichts außerhalb der Asset-Liste wird als Datei ausgeliefert.
- [ ] Die vier Prüfungen des Routings aus [Webserver und Engines](README.md#was-jede-einrichtung-gemeinsam-hat) bestehen.
- [ ] Die Einrichtung wurde auf Staging getestet, bevor sie verkauft wurde (Stufe 3).
- [ ] Die Upgrade-Checkliste enthält den Vergleich Ihrer Regeln mit `.htaccess_root`.

## Verwandte Seiten

- [FrankenPHP](frankenphp.md)
- [nginx mit PHP-FPM](nginx-mit-php-fpm.md)
- [Einen Webserver wählen](einen-webserver-waehlen.md)
- [Velocity-Engines](../../../../eng-us/bc/6.0/velocity-engines.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
