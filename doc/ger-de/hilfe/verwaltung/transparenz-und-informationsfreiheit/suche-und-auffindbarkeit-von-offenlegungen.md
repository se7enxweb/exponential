---
title: Suche und Auffindbarkeit von Offenlegungen
audience: government, website-creator, editor, admin
category: public-sector
order: 200
summary: Wie Sie veröffentlichte Auskünfte, Berichte und Register auf einer Exponential-Website leicht auffindbar machen - durchsuchbare Felder, Textextraktion aus PDFs, Suchstatistiken, URL-Aliasse, Feeds und Sitemaps, und was jedes davon kostet.
keywords: Suche, Auffindbarkeit, durchsuchbare Attribute, binaryfile.ini, PDF-Text, Suchstatistiken, LogSearchStats, Sitemaps, RSS, URL-Alias, Offenlegungsprotokoll, ezfind, Solr, search, findability, searchable attributes, PDF text, search statistics, sitemaps, URL alias, disclosure log
type: help
---
# Suche und Auffindbarkeit von Offenlegungen

Diese Seite richtet sich an Websiteersteller und Redaktionen, die möchten, dass Menschen Informationen finden, die die
Stelle bereits veröffentlicht hat, sodass weniger Anträge nötig sind. Exponential hat eine eingebaute Suchmaschine und
optional Solr über `ezfind` ([Suche auf der Website](../../../../eng-us/help/creators/website-creators/site-search.md)).

## Inhalt

1. [Die richtigen Felder durchsuchbar machen](#die-richtigen-felder-durchsuchbar-machen)
2. [In Dokumenten suchen](#in-dokumenten-suchen)
3. [Aus dem lernen, wonach Menschen suchen](#aus-dem-lernen-wonach-menschen-suchen)
4. [Adressen, Feeds und Sitemaps](#adressen-feeds-und-sitemaps)
5. [Wann der Wechsel zu Solr lohnt](#wann-der-wechsel-zu-solr-lohnt)

## Die richtigen Felder durchsuchbar machen

Die eingebaute Suchmaschine indexiert beim Veröffentlichen von Inhalten die Attribute, die in jeder Klasse als
**Durchsuchbar** markiert sind. Bei einem Eintrag im Offenlegungsprotokoll markieren Sie Titel, Zusammenfassung und
Auskunft; bei einem Beschluss Titel und Beschlusstext. Markieren Sie keine internen Kennungen. Bauen Sie nach dem
Ändern einer Klasse den Index neu auf:

```bash
php bin/php/updatesearchindex.php --clean
```

Besucher finden nur, was sie lesen dürfen, sodass Inhalte in einer nicht öffentlichen Sektion nie in ihren Ergebnissen
erscheinen.

## In Dokumenten suchen

`settings/binaryfile.ini` hat Handler, die Text aus PDF- und Word-Dateien für den Suchindex extrahieren
(`[PDFHandlerSettings]`, `[WordHandlerSettings]`), mit externen Programmen, die Sie auf dem Server installieren. Ohne
sie sind nur der Name der Datei und die Felder des Objekts durchsuchbar. Gescannte PDFs ohne Textebene liefern so oder
so keinen Text. Mit Solr (`ezfind`) können auch Dateiinhalte indexiert werden
([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#searching-inside-files)).

## Aus dem lernen, wonach Menschen suchen

Schalten Sie die Suchstatistiken auf dem öffentlichen Siteaccess ein:

```ini
# settings/siteaccess/<public siteaccess>/site.ini.append.php
[SearchSettings]
LogSearchStats=enabled
```

**Setup > Suchstatistiken** zeigt dann jeden Suchbegriff, wie oft danach gesucht wurde und die durchschnittliche
Anzahl der Ergebnisse; Suchbegriffe ohne Treffer weisen auf fehlende Inhalte oder fehlende Wörter hin. Suchbegriffe
sind das, was Besucher eingetippt haben, und können personenbezogene Daten enthalten; setzen Sie die Statistik zurück,
wenn Sie sie nicht mehr brauchen ([Suchstatistiken](../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)).

## Adressen, Feeds und Sitemaps

- Lesbare URL-Aliasse entstehen aus den Namen; wählen Sie klare Titel
  ([URL-Aliasse und sprechende URLs](../../../../eng-us/help/creators/website-creators/url-aliases-and-nice-urls.md)).
- Ein RSS-Export des Offenlegungsprotokolls lässt Menschen neuen Herausgaben folgen
  ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).
- XML-Sitemaps (`xrowmetadata`, `bcgooglesitemaps`) helfen Suchmaschinen, jeden Eintrag zu finden
  ([Sitemaps](../../../../eng-us/help/creators/website-creators/sitemaps.md)).
- Eine Meta-Beschreibung pro Seite hilft bei den Ergebnissen von Suchmaschinen
  ([Metadaten und Teilen in sozialen Medien](../../../../eng-us/help/creators/website-creators/metadata-and-social-sharing.md)).

## Wann der Wechsel zu Solr lohnt

| Bedarf | Eingebaut | Solr (`ezfind`) |
|---|---|---|
| Nach Wörtern finden | ja | ja |
| Facetten nach Jahr, Ergebnis, Thema | nein | ja |
| Feinabstimmung der Relevanz | einfach | ja |
| Sehr viele Dokumente | wird langsamer | dafür gebaut |

Solr ist ein eigener Java-Server, der betrieben, überwacht und gesichert werden muss
([Suche mit ezfind und Solr](../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md)).

## Verwandte Seiten

- [Suche auf der Website](../../../../eng-us/help/creators/website-creators/site-search.md)
- [Suchen (für Redaktionen)](../../suchen.md)
- [Offenlegungsprotokolle](offenlegungsprotokolle.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
