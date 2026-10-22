---
title: Sitemaps und robots.txt für ein lokales Unternehmen
audience: local-business, businesses, customer
category: local-business
order: 23
summary: Was eine XML-Sitemap und die robots.txt für die Website eines kleinen Unternehmens leisten, wie Exponential sie mit bcgooglesitemaps und der mitgelieferten robots.txt erzeugt, worum Sie Ihren Ersteller bitten und wie Sie beides selbst im Browser prüfen.
keywords: Sitemap, XML-Sitemap, bcgooglesitemaps, robots.txt, Cronjob, Suchmaschinen, Webmaster-Tools, Crawlen, lokales Unternehmen, sitemap, XML sitemap, cron job, search engines, webmaster tools, local business
type: help
---
# Sitemaps und robots.txt für ein lokales Unternehmen

Diese Seite ist für Inhaber der Website eines kleinen Unternehmens, die wollen, dass Suchmaschinen jede Seite kennen.
Das erledigen zwei kleine Dateien: Die **Sitemap** listet Ihre Seiten auf, und die **robots.txt** sagt Crawlern,
wohin sie nicht gehen sollen und wo die Sitemap liegt.

## Inhalt

1. [Die Sitemap](#die-sitemap)
2. [robots.txt](#robotstxt)
3. [Worum Sie Ihren Ersteller bitten](#worum-sie-ihren-ersteller-bitten)
4. [Prüfen Sie es selbst](#prüfen-sie-es-selbst)
5. [Häufige Probleme](#häufige-probleme)

## Die Sitemap

Exponential erzeugt Sitemaps mit einer von zwei Erweiterungen, beide in der Standard-`composer.json`:

| Erweiterung | Teil des Cronjobs | Wann |
|---|---|---|
| `bcgooglesitemaps` | `googlesitemaps`, `googlesitemapsmultilingual` | eine einfache Sitemap; die übliche Wahl |
| `xrowmetadata` | `sitemap` und andere | wenn `xrowmetadata` ohnehin für Seitenmetadaten verwendet wird |

Quelle: [XML-Sitemaps](../../ersteller/website-ersteller/sitemaps.md) und
[bcgooglesitemaps](../../../funktionen/6.0/erweiterungen/bcgooglesitemaps.md).

Die Sitemap ist eine Datei, die der Server schreibt, wenn der Cronjob läuft, meist einmal pro Nacht. Eine neue Seite
erscheint darin nach dem nächsten Lauf. Für eine Website in zwei Sprachen schreibt der mehrsprachige Teil eine Sitemap
pro Sprache.

Einstellungen, die man kennen sollte (in `bcgooglesitemaps.ini`):

| Einstellung | Was sie bewirkt | Für ein lokales Unternehmen |
|---|---|---|
| `SitemapRootNodeID` | wo die Sitemap beginnt | meist die Startseite |
| `ClassFilterType`, `ClassFilterArray[]` | Klassen ein- oder ausschließen | Bilder, Dateien und Banner ausschließen, die keine Seiten sind |
| `Main_Node_Only` | jede Seite nur einmal auflisten | `true`, wenn Seiten an zwei Stellen erscheinen |

## robots.txt

Eine `robots.txt` wird im Web-Wurzelverzeichnis jeder Installation mitgeliefert
([robots.txt für Ihre Website](../../ersteller/website-ersteller/robots-txt.md)). Sie hält Crawler bereits von den
Suchergebnissen der Website, Druckansichten und ähnlichen Seiten fern (zum Beispiel `Disallow: /content/search/` in
der mitgelieferten Datei). Ihr Ersteller ergänzt eine Zeile mit der Adresse der Sitemap:

```text
Sitemap: https://www.example.com/sitemap.xml
```

Die robots.txt ist eine höfliche Bitte, kein Schloss. Inhalte, die privat bleiben müssen, brauchen Berechtigungen,
keine robots.txt.

## Worum Sie Ihren Ersteller bitten

1. Schalten Sie `bcgooglesitemaps` ein, und stellen Sie es so ein, dass Klassen, die keine Seiten sind,
   ausgeschlossen werden.
2. Nehmen Sie den Cronjob für die Sitemap in den Zeitplan auf ([Cronjobs](../../../anleitungen/cronjobs.md)).
3. Ergänzen Sie die Zeile `Sitemap:` in der `robots.txt`.
4. Melden Sie die Sitemap in den Webmaster-Tools der Suchmaschinen an, die Ihnen wichtig sind. Diese Tools liegen
   außerhalb von Exponential; das Konto sollte auf den Namen des Unternehmens laufen.

## Prüfen Sie es selbst

| Prüfung | Wie | Gutes Ergebnis |
|---|---|---|
| Die Sitemap existiert | öffnen Sie `https://<your site>/sitemap.xml` im Browser | eine Liste von Adressen |
| Neue Seiten erscheinen | veröffentlichen Sie eine Seite, warten Sie bis zur nächsten Nacht, sehen Sie erneut nach | die neue Adresse ist aufgeführt |
| Nur echte Seiten | lesen Sie die Liste | keine Bild- oder Dateiobjekte, keine Testseiten |
| Die robots.txt nennt sie | öffnen Sie `https://<your site>/robots.txt` | eine Zeile `Sitemap:` mit Ihrer Adresse |
| Adressen verwenden HTTPS und Ihren echten Host | lesen Sie die Liste | `https://www.example.com/...`, kein Testhost |

## Häufige Probleme

| Problem | Wahrscheinliche Ursache | Wer es behebt |
|---|---|---|
| Die Sitemap ändert sich nie | der Cronjob läuft nicht | Hoster oder Ersteller |
| Die Sitemap zeigt eine Testadresse | die Einstellung für Protokoll oder Host ist falsch | Ersteller |
| Seiten nur für Mitglieder erscheinen | Berechtigungen oder Klassenfilter | Ersteller; prüfen Sie mit einer Seite, von der Sie wissen, dass sie eingeschränkt ist |
| Die Suchmaschine meldet „durch robots.txt blockiert“ | eine zu weit gefasste Zeile `Disallow:` | Ersteller |

## Verwandte Seiten

- [Grundlagen der lokalen Suchmaschinenoptimierung](grundlagen-der-lokalen-suchmaschinenoptimierung.md)
- [XML-Sitemaps](../../ersteller/website-ersteller/sitemaps.md)
- [Funktion robots.txt](../../../funktionen/6.0/robots-txt.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
