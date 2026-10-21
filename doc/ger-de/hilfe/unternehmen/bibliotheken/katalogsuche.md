---
title: Katalogsuche
audience: businesses, website-creator, admin
category: business-solutions
order: 40
summary: Wie Leser Einträge in einem Katalog auf Exponential finden - die eingebaute Suchmaschine, die erweiterte Suche, was indiziert wird, Suche in Dateien, Suchstatistiken und wann Sie Solr über die Erweiterung ezfind hinzufügen, mit seinen Kosten.
keywords: Katalogsuche, Bibliothekssuche, Suche auf der Website, erweiterte Suche, Solr, ezfind, Facetten, Relevanz, Suchstatistiken, Volltext, PDF-Suche, Indizierung, catalog search, library search, site search, advanced search, facets, relevance, search statistics, full text, PDF search, indexing
type: help
---
# Katalogsuche

Diese Seite ist für Verantwortliche von Bibliotheken und Archiven, die entscheiden, wie Leser den Katalog durchsuchen,
und für den Website-Ersteller, der es einrichtet. Sie erklärt, was die eingebaute Suche tut, wie Sie aus dem lernen,
wonach Leser suchen, und wann sich der zusätzliche Aufwand eines Solr-Servers über `ezfind` auszahlt.

## Inhalt

1. [Zwei Suchmaschinen](#zwei-suchmaschinen)
2. [Die eingebaute Suche](#die-eingebaute-suche)
3. [Was indiziert wird](#was-indiziert-wird)
4. [In Dateien suchen](#in-dateien-suchen)
5. [Aus dem lernen, wonach Leser suchen](#aus-dem-lernen-wonach-leser-suchen)
6. [Wann sich Solr lohnt](#wann-sich-solr-lohnt)
7. [Der Wechsel zu Solr in Umrissen](#der-wechsel-zu-solr-in-umrissen)
8. [Stöbern ergänzt das Suchen](#stöbern-ergänzt-das-suchen)
9. [Checkliste zur Suche](#checkliste-zur-suche)

## Zwei Suchmaschinen

| | Eingebaute Suchmaschine | Solr über `ezfind` |
|---|---|---|
| Mit dem Produkt installiert | Ja (`SearchEngine=eZSearchEngine`) | Nein: `se7enxweb/ezfind` ist ein Composer-Vorschlag, dazu kommt ein eigener Solr-Server |
| Einträge nach Wörtern finden | Ja | Ja |
| Relevanzbewertung und Gewichtung | Einfach | Ja |
| Facetten (nach Klasse, Tag, Datum filtern) | Nein | Ja |
| Sehr viele Objekte | Wird langsamer | Dafür gebaut |
| Tag-Wolken aus der Suche | Nein | Ja |

Quellen: [Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md#wann-sich-solr-lohnt)
und der Block `suggest` von `composer.json`.

## Die eingebaute Suche

- `site.ini [SearchSettings] SearchEngine=eZSearchEngine` indiziert beim Veröffentlichen die Attribute, die als
  **Searchable** markiert sind. „Visitors only find what they may read“ (Besucher finden nur, was sie lesen dürfen);
  eingeschränkte Einträge bleiben also vor der Öffentlichkeit verborgen.
- `content/search` nimmt ein Suchfeld entgegen; `content/advancedsearch` bietet mehr Felder (Wortfolge, Klasse,
  Datum), nützlich für einen Katalog: „nur Bücher, veröffentlicht nach 2000“.
- Zu den ausgelieferten Grenzen gehören `MaximumSearchLimit=30` Ergebnisse pro Seite und ausgeschaltete Platzhalter
  (`EnableWildcard=false`), weil Suchen mit `term*` „costly on large sites“ (auf großen Websites aufwendig) sind.

Quelle: [Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md).

## Was indiziert wird

Markieren Sie nur nützliche Felder als **Searchable**: „titles, intros, bodies; not internal codes“ (Titel,
Einleitungen, Haupttexte; keine internen Codes, dieselbe Seite). Für einen Katalog markieren Sie auch die Kennungen, die
Leser eintippen, etwa eine ISBN, und die Namen der Urheber in Person-Objekten.

Schlagwörter aus `eztags` werden standardmäßig mit ihren Synonymen indiziert (`IndexSynonyms=enabled`); ein Leser, der
einen nicht bevorzugten Begriff eintippt, findet den Eintrag also trotzdem
([Tags und Schlagwörter](tags-und-schlagwoerter.md#schlagwörter-in-der-suche)).

Bauen Sie nach einem großen Import den ganzen Index neu auf:

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user
```

Mit eingeschaltetem `DelayedIndexing` werden Inhalte per Cron statt beim Veröffentlichen indiziert, was das
Veröffentlichen während Importen schnell hält
([Suche auf der Website, Indexing](../../ersteller/website-ersteller/suche-auf-der-website.md#indexierung)).

## In Dateien suchen

`settings/binaryfile.ini` hat Handler, die für den Suchindex Text aus PDF- und Word-Dateien extrahieren
(`[PDFHandlerSettings]`, `[WordHandlerSettings]`), „using external programs you install“ (mit externen Programmen, die
Sie installieren). Mit Solr lassen sich auch Dateiinhalte indizieren
([Dateien und Downloads, Searching inside files](../../ersteller/website-ersteller/dateien-und-downloads.md#in-dateien-suchen)).
Bitten Sie Ihren Hosting-Partner, die Extraktionsprogramme zu installieren, wenn digitalisierte Dokumente über ihren
Text gefunden werden sollen.

> **Hinweis**
> Die Textextraktion funktioniert bei Dokumenten, die Text enthalten. Ein Scan, der nur als Bild gespeichert ist, hat
> keinen Text zum Extrahieren, es sei denn, er wurde vor dem Upload durchsuchbar gemacht (mit OCR). Code und
> Einstellungen des Repositorys enthalten keine OCR-Funktion; erledigen Sie diesen Schritt in Ihrer Scan-Software.

## Aus dem lernen, wonach Leser suchen

Mit `LogSearchStats=enabled` im öffentlichen Siteaccess zeigt **Setup > Suchstatistiken** (`/search/stats`) jeden
Suchbegriff, wie oft danach gesucht wurde und die durchschnittliche Zahl der Ergebnisse; „phrases that found nothing
show what content is missing“ (Begriffe ohne Treffer zeigen, welche Inhalte fehlen). Suchbegriffe können
personenbezogene Daten sein, deshalb ist die Aufzeichnung standardmäßig aus
([Suche auf der Website, Search statistics](../../ersteller/website-ersteller/suche-auf-der-website.md#suchstatistiken)).

Nutzen Sie sie monatlich: Suchen ohne Ergebnisse weisen auf fehlende Einträge, fehlende Synonyme oder
Schreibvarianten hin, die Sie als Tag-Synonyme hinzufügen. Entscheiden Sie mit Ihrer Ansprechperson für den
Datenschutz, ob Sie die Aufzeichnung einschalten und wie lange die Begriffe aufbewahrt werden.

## Wann sich Solr lohnt

Ziehen Sie Solr in Betracht, wenn einer dieser Punkte zutrifft:

- [ ] Leser müssen Ergebnisse nach Facetten eingrenzen (Format, Thema, Datum, Sprache).
- [ ] Der Katalog ist so gewachsen, dass Suchen langsamer werden.
- [ ] Die Relevanzbewertung ist wichtig (beste Treffer zuerst), und die eingebaute Bewertung reicht nicht.
- [ ] Der Volltext vieler Dateien muss durchsuchbar sein.

Und wägen Sie die Kosten ab: „Solr is a separate server (Java) to install, run, monitor and back up. For a small site
the built-in engine is fine“ (Solr ist ein eigener Java-Server, der installiert, betrieben, überwacht und gesichert
werden muss; für eine kleine Website reicht die eingebaute Suchmaschine,
[Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md#wann-sich-solr-lohnt)).
Zum Hosting siehe [Such-Backends](../../hosting/grundlagen/such-backends.md).

## Der Wechsel zu Solr in Umrissen

1. Installieren Sie einen Solr-Server (lokal oder einen entfernten).
2. Setzen Sie `[SolrSettings] Enabled=true` und seine Adresse in `settings/override/solr.ini.append.php`.
3. Prüfen Sie mit `./console exp:solr status`, dann mit `ping`.
4. Installieren und aktivieren Sie `ezfind`; seine Einstellungen schalten `[SearchSettings] SearchEngine` auf `ezsolr`
   um.
5. Leeren Sie den INI-Cache und bauen Sie den Index neu auf.

Quelle: [Suche mit ezfind und Solr, Switching it on](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md#einschalten)
und [Befehle zur Serversteuerung](../../../funktionen/6.0/befehle-fuer-webserver-und-solr.md). `content/search` funktioniert
weiter; Facetten brauchen Templates, die die eigenen Fetch-Funktionen von ezfind verwenden.

## Stöbern ergänzt das Suchen

Viele Leser stöbern, statt zu suchen. Geben Sie ihnen:

- Themenseiten aus Tags ([Tags und Schlagwörter](tags-und-schlagwoerter.md#themenseiten-und-listen-nach-thema));
- Sammlungen als Container im Menü ([Katalogeinträge modellieren](katalogeintraege-modellieren.md#sammlungen-und-reihen));
- eine Liste und einen Feed der Neuerwerbungen ([Feeds für Neuerwerbungen](feeds-fuer-neuerwerbungen.md)).

## Checkliste zur Suche

- [ ] Searchable bewusst für jedes Feld jeder Eintragsklasse gesetzt.
- [ ] Ein Template für Suchergebnisse, das die Zahl der Ergebnisse, das Blättern und Vorschläge zeigt, wenn nichts
      gefunden wird.
- [ ] Erweiterte Suche angeboten, wenn Leser Filter nach Klasse oder Datum brauchen.
- [ ] Index nach jedem großen Import neu aufgebaut.
- [ ] Programme zur Textextraktion installiert, wenn Dateiinhalte gefunden werden müssen.
- [ ] Suchstatistiken mit der Ansprechperson für den Datenschutz entschieden (ein oder aus, Aufbewahrung).
- [ ] Eine datierte Entscheidung zu Solr: noch nicht, oder mit einem Hosting-Partner geplant.

## Verwandte Seiten

- [Bibliotheken und Kataloge](README.md)
- [Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)
- [Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)
- [Suchen (für Redakteure)](../../suchen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
