---
title: Die Website freundlich für die Webarchivierung machen
audience: government, admin, developer
category: public-sector
order: 210
summary: Für öffentliche Stellen, deren Websites von einem nationalen oder institutionellen Webarchiv eingesammelt werden - die mitgelieferte robots.txt von Exponential und was Sie darin ändern sollten, Sitemaps aus bcgooglesitemaps oder xrowmetadata, stabile URLs, request-shield und Ratenbegrenzungen, die Crawler blockieren können, und Prüfungen.
keywords: Webarchivierung, Crawler, Harvesting, Einsammeln, robots.txt, Crawl-delay, Sitemaps, bcgooglesitemaps, xrowmetadata, stabile URLs, request-shield, verifizierte Suchmaschinen, Drucklayout, Nationalarchiv, Nationalbibliothek, öffentlicher Sektor, web archiving, crawler, harvesting, sitemaps, stable URLs, verified search engines, print layout, national archive, public sector
type: help
---
# Die Website freundlich für die Webarchivierung machen

Diese Seite richtet sich an Administratoren und Entwickler. Viele nationale und institutionelle Archive sammeln
öffentliche Websites mit Crawlern ein. Eine Website, die Crawler durchlaufen können, mit stabilen Adressen und einer
Sitemap, wird besser archiviert. Exponential liefert eine brauchbare `robots.txt` mit und unterstützt Sitemaps; einige
Sicherheitsmaßnahmen brauchen eine Ausnahme für den Crawler des Archivs.

## Inhalt

1. [robots.txt](#robotstxt)
2. [Sitemaps](#sitemaps)
3. [Sicherheitsmaßnahmen und Crawler](#sicherheitsmaßnahmen-und-crawler)
4. [Stabile, durchsuchbare Seiten](#stabile-durchsuchbare-seiten)
5. [Prüfen](#prüfen)
6. [Verwandte Seiten](#verwandte-seiten)

## robots.txt

Eine neue Installation liefert im Wurzelverzeichnis des Webservers eine `robots.txt` mit, die über die Rewrite-Regel
`RewriteRule ^robots\.txt - [L]` direkt ausgeliefert wird
([Eine brauchbare robots.txt ab Werk](../../../../eng-us/features/6.0/robots-txt.md)). Ihre Anweisungen:

```
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

- `/media/` und `/test-area/` sind Beispiele: Passen Sie sie an Ihren Baum an. Wenn Ihre Dokumente unter einer
  Mediensektion liegen, die das Archiv aufbewahren soll, entfernen Sie diese Zeile.
- Ersetzen Sie die auskommentierten Sitemap-Zeilen durch Ihre eigene Zeile `Sitemap:`.
- Wenn Ihr Archiv besondere Regeln für seinen Crawler verlangt, fügen Sie einen Block `User-agent:` für ihn hinzu.

## Sitemaps

Zwei Erweiterungen schreiben Sitemaps als Cronjob-Teile: `bcgooglesitemaps` (Teil der Distribution) und `xrowmetadata`
([Sitemaps](../../../../eng-us/help/creators/website-creators/sitemaps.md)). Tragen Sie die Sitemap in die `robots.txt` ein.

## Sicherheitsmaßnahmen und Crawler

- **request-shield** bremst, wenn eingesetzt, Clients über 120 Seiten pro Minute mit einer Browserprüfung und sperrt
  nach wiederholten Ablehnungen; verifizierte Suchmaschinen werden nie gesperrt, aber der Crawler eines Archivs wird
  möglicherweise nicht erkannt. Vereinbaren Sie mit dem Archiv dessen Adressbereich oder User-Agent und fügen Sie eine
  Regel dafür hinzu ([request-shield](../../../../eng-us/help/government/security/request-shield.md)).
- System-URLs (`/content/view/full/<id>`) können Besuchern durch Anfrageregeln oder request-shield verweigert werden;
  Crawler folgen dann den Aliassen, was Sie auch wollen.
- Der Antwort-Cache von Velocity und der HTTP-Cache bedienen Crawler schnell; keine Änderung nötig.

## Stabile, durchsuchbare Seiten

- Inhalte, die über normale Links und die Sitemap erreichbar sind, nicht nur über Suchformulare.
- Stabile URL-Aliasse (siehe „URLs erhalten“).
- Von Seiten aus verlinkte Dokumente.
- Seiten, die sich nach Möglichkeit ohne Skripte darstellen.

## Prüfen

```bash
curl -s https://www.example.gov/robots.txt
curl -sI https://www.example.gov/sitemap.xml | head -3
```

Bitten Sie das Archiv nach dem ersten Einsammeln um sein Crawl-Protokoll und beheben Sie, was es nicht erreichen
konnte.

## Verwandte Seiten

- [Eine brauchbare robots.txt ab Werk](../../../../eng-us/features/6.0/robots-txt.md)
- [robots.txt für Websiteersteller](../../../../eng-us/help/creators/website-creators/robots-txt.md)
- [Sitemaps](../../../../eng-us/help/creators/website-creators/sitemaps.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
