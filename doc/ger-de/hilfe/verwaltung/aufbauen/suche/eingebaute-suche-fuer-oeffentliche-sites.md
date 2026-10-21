---
title: Eingebaute Suche für öffentliche Sites
audience: government, website-creator, admin
category: public-sector
order: 20
summary: Was die mit Exponential gelieferte Suchmaschine für eine öffentliche Site tut (Index in der Datenbank, beachtete Berechtigungen, durchsuchbare Felder, erweiterte Suche), ihre Einstellungen und wann sie für eine Behörde genügt.
keywords: Site-Suche, eingebaute Suche, erweiterte Suche, Berechtigungen, Suche auf öffentlichen Sites, site search, built-in search, eZSearchEngine, content/search, advancedsearch, SearchSettings, permissions, public site search
type: help
---
# Eingebaute Suche für öffentliche Sites

Diese Seite richtet sich an Site-Ersteller und Administratoren öffentlicher Sites. Exponential hat eine eingebaute
Suchmaschine, die ihren Index in der Datenbank hält; für die meisten Sites genügt sie
([Site-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md)).

## Inhalt

1. [Was sie tut](#was-sie-tut)
2. [Wichtige Einstellungen](#wichtige-einstellungen)
3. [Genügt sie?](#genügt-sie)
4. [Pflege](#pflege)

## Was sie tut

- `site.ini [SearchSettings] SearchEngine=eZSearchEngine` indiziert beim Veröffentlichen von Inhalten die Attribute,
  die in jeder Klasse als **Durchsuchbar** markiert sind.
- Besucher finden nur, was sie lesen dürfen: Inhalte in einer nicht öffentlichen Sektion erscheinen nie in ihren
  Ergebnissen.
- `content/search` nimmt einen Suchtext entgegen; `content/advancedsearch` fügt Felder für Phrase, Klasse und Datum
  hinzu. Beide sind für Crawler in der mitgelieferten `robots.txt` gesperrt.
- Ergebnisse sind auf die Sprachen beschränkt, die der Siteaccess zeigt (siehe den Bereich Sprachen, „Mehrsprachige
  Suche“).

## Wichtige Einstellungen

| Schlüssel | Mitgeliefert | Bedeutung |
|---|---|---|
| `MaximumSearchLimit` | `30` | die meisten Ergebnisse, die eine Seite anfordern darf |
| `AllowEmptySearch` | `disabled` | eine leere Suche wird abgelehnt |
| `EnableWildcard`, `MinCharacterWildcard` | `false`, `3` | Suchen nach `term*`; auf großen Sites aufwendig |
| `StopWordThresholdPercent` | `60` | Wörter in mehr als diesem Anteil der Objekte werden ignoriert |
| `DelayedIndexing` | `disabled` | `enabled` oder `classbased`: per Cron indizieren statt beim Veröffentlichen |
| `LogSearchStats` | `disabled` | Suchbegriffe aufzeichnen |

## Genügt sie?

| Bedarf | Eingebaut |
|---|---|
| Seiten nach Wörtern finden | ja |
| Berechtigungen beachten | ja |
| Relevanzsortierung | einfach |
| Facetten (nach Typ, Datum, Thema filtern) | nein |
| Sehr viele Objekte | wird langsam |
| Stammformreduktion je Sprache | nein |

Wenn die Zeilen mit „nein“ wichtig sind, siehe „Solr mit ezfind“.

## Pflege

Bauen Sie den Index nach Importen oder einem Umzug der Datenbank neu auf:

```bash
php bin/php/updatesearchindex.php --clean
```

Mit verzögerter Indizierung muss der Cronjob-Teil `indexcontent.php` laufen
([10.8 Suche](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung)).

## Verwandte Seiten

- [Site-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md)
- [Suchen (für Redakteure)](../../../suchen.md)
- [Such-Backends beim Hosting](../../../hosting/grundlagen/such-backends.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
