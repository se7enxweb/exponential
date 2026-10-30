---
title: robots.txt für Website-Ersteller
audience: website-creator, admin
category: website-creation
order: 400
summary: Die mitgelieferte robots.txt im Web-Root anpassen - was sie bereits sperrt, Ihre Sitemap und eigene Bereiche ergänzen, wie sie ausgeliefert wird und eigene Dateien je Host in einer Multisite.
keywords: robots.txt, Crawler, Suchmaschinen, Web-Root, Rewrite-Regeln, Multisite, Website-Ersteller, crawlers, search engines, Disallow, Sitemap, Crawl-delay, web root, rewrite rules, website creator
type: help
---
# robots.txt für Website-Ersteller

Diese Seite richtet sich an Website-Ersteller, die den Crawlern der Suchmaschinen sagen, was sie auslassen sollen. Eine
neue Installation liefert im Web-Root eine `robots.txt` mit sinnvollen Regeln aus
([Eine nützliche robots.txt ab Werk](../../../funktionen/6.0/robots-txt.md)).

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

1. [Was mitgeliefert wird](#was-mitgeliefert-wird)
2. [Passen Sie sie an](#passen-sie-sie-an)
3. [Wie sie ausgeliefert wird](#wie-sie-ausgeliefert-wird)
4. [Mehrere Websites](#mehrere-websites)
5. [Was robots.txt nicht ist](#was-robotstxt-nicht-ist)

## Was mitgeliefert wird

```text
User-agent: *
Allow: /

Disallow: /content/search/
Disallow: /content/advancedsearch/
Disallow: /content/tipafriend/
Disallow: /layout/set/print/
Disallow: /media/
Disallow: /test-area/

Crawl-delay: 1
```

Jeder gesperrte Pfad ist zusätzlich in großgeschriebener Form aufgeführt, weil ein Crawler beiden Schreibweisen begegnet
sein kann. Die Datei beginnt mit einem Kommentarblock, den Sie entfernen dürfen, und enthält auskommentierte
Sitemap-Zeilen.

## Passen Sie sie an

1. Entfernen Sie `/media/` und `/test-area/`, wenn sie nicht zu Ihrem Baum passen; ergänzen Sie Ihre eigenen Bereiche
   mit den Adressen, die Besucher sehen (URL-Aliasse).
2. Ergänzen Sie Ihre Sitemap: `Sitemap: https://www.example.com/sitemap.xml` ([Sitemaps](sitemaps.md)).
3. Prüfen Sie: `curl -s https://www.example.com/robots.txt`.

Halten Sie die Datei mit Ihrem Deployment in der Versionsverwaltung, statt sie auf dem Server zu bearbeiten.

## Wie sie ausgeliefert wird

`.htaccess_root` enthält `RewriteRule ^robots\.txt - [L]`, sodass Apache die Datei direkt ausliefert. Die eingebauten
Rewrite-Regeln von Velocity sind dieselbe Liste
([8.1 Die Wahl in einer Tabelle](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle)). Stellen Sie unter
nginx sicher, dass `/robots.txt` die Datei erreicht.

## Mehrere Websites

Eine Datei bedient jeden Host der Installation. Wenn Websites unterschiedliche Regeln brauchen, lassen Sie den Webserver
eine Datei je Host ausliefern (zum Beispiel `robots-site-b.txt` für `www.site-b.example`), oder halten Sie die Regeln
allgemein genug für alle.

## Was robots.txt nicht ist

- Sie ist keine Zugriffskontrolle: Jeder kann lesen, was Sie sperren. Schützen Sie private Inhalte mit Rechten
  ([Sektionen und Objektzustände für Website-Ersteller](sektionen-und-objektzustaende.md)).
- Eine gesperrte Seite kann trotzdem mit ihrer Adresse gelistet werden, wenn andere Websites auf sie verlinken;
  verwenden Sie ein `noindex`-Meta-Tag in ihrem Template, um sie aus den Ergebnissen herauszuhalten.
- Staging- und Test-Websites sollten eine Anmeldung verlangen, statt sich auf `robots.txt` zu verlassen.

## Verwandte Seiten

- [SEO-Grundlagen](seo-grundlagen.md)
- [Sitemaps](sitemaps.md)
- [Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
