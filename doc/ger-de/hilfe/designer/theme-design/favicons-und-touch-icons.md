---
title: Favicons und Touch-Icons
audience: designer
category: theme-design
order: 320
summary: Ein Favicon, Touch-Icons und ein Web-App-Manifest mit einem Design ausliefern: wohin die Dateien gehören, die link-Tags mit ezimage, die Rewrite-Regel für /favicon.ico und ein minimaler moderner Satz.
keywords: Favicon, favicon.ico, Touch-Icon, apple-touch-icon, Web-App-Manifest, Website-Symbol, Symbol im Browser-Tab, Symbolgrößen, SVG-Favicon, touch icon, web app manifest, site icon, browser tab icon, icon sizes, svg favicon
type: help
---
# Favicons und Touch-Icons

Diese Seite ist für Designer, die einer Website ihr Tab-Symbol und ihr Symbol für den Startbildschirm geben. Sie beantwortet: welche
Dateien nötig sind, wohin sie in einem Design gehören und wie Sie sie verlinken.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../beispiele/webserver/litespeed.md), [Caddy](../../../beispiele/webserver/caddy.md), [IIS](../../../beispiele/webserver/iis.md),
> [Velocity](../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hosting/control-panels/plesk.md), [cPanel](../../hosting/control-panels/cpanel.md), [DirectAdmin](../../hosting/control-panels/directadmin.md), [ISPConfig](../../hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [Ein minimaler moderner Satz](#ein-minimaler-moderner-satz)
2. [Wohin die Dateien gehören](#wohin-die-dateien-gehören)
3. [Die link-Tags](#die-link-tags)
4. [Die Anfrage nach /favicon.ico](#die-anfrage-nach-faviconico)
5. [Prüfungen](#prüfungen)

## Ein minimaler moderner Satz

| Datei | Zweck |
|---|---|
| `favicon.svg` | das Tab-Symbol in aktuellen Browsern (skaliert, kann sich mit einer Media Query im SVG an den dunklen Modus anpassen) |
| `favicon.ico` (32 x 32) | ältere Browser und Werkzeuge, die nach `/favicon.ico` fragen |
| `apple-touch-icon.png` (180 x 180) | Symbol für den Startbildschirm auf Apple-Geräten |
| `icon-192.png`, `icon-512.png` | Startbildschirm und Installation unter Android, in einem Web-App-Manifest genannt |

Das Design starter liefert einen größeren Satz von PNG-Größen (16 bis 152 Pixel) in `images/favicon/` mit; beide Ansätze funktionieren.

## Wohin die Dateien gehören

In `images/favicon/` Ihres Designs, sodass sie mit `ezimage` gefunden und als statische Dateien ausgeliefert werden:

```
extension/mytheme/design/mytheme/images/favicon/favicon.svg
extension/mytheme/design/mytheme/images/favicon/favicon.ico
extension/mytheme/design/mytheme/images/favicon/apple-touch-icon.png
```

## Die link-Tags

In einem Kopfteil Ihres Pagelayouts (zum Beispiel `pagelayout/head/link.tpl`, wie es das Design starter tut):

```tpl
<link rel="icon" href={'favicon/favicon.svg'|ezimage} type="image/svg+xml">
<link rel="icon" href={'favicon/favicon.ico'|ezimage} sizes="32x32">
<link rel="apple-touch-icon" href={'favicon/apple-touch-icon.png'|ezimage}>
```

`ezimage` gibt die URL in Anführungszeichen zurück; fügen Sie keine eigenen hinzu.

## Die Anfrage nach /favicon.ico

Browser fragen an der Wurzel der Website nach `/favicon.ico`, auch wenn die Seite ein anderes Symbol verlinkt. Die mitgelieferte
`.htaccess_root` liefert `design/standard/images/favicon.ico` aus und hat eine auskommentierte Regel, die `/favicon.ico` darauf abbildet:

```apache
#RewriteRule ^favicon\.ico /design/standard/images/favicon.ico [L]
```

Ein Administrator kann eine solche Regel stattdessen auf die Datei Ihres Designs zeigen lassen. Ohne Regel geht `/favicon.ico` an
`index.php` und kostet eine Seitenanfrage mit einer 404-Antwort.

## Prüfungen

- [ ] Tab-Symbol in hellen und dunklen Browser-Themes sichtbar.
- [ ] Kein 404 für `/favicon.ico` im Serverprotokoll oder im Netzwerkbereich.
- [ ] Symbol für den Startbildschirm auf einem Telefon getestet.

## Verwandte Seiten

- [Bilder in einem Design](bilder-in-einem-design.md)
- [SVG-Bilder](svg-bilder.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
