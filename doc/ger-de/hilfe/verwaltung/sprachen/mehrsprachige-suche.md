---
title: Mehrsprachige Suche
audience: government, website-creator, admin
category: public-sector
order: 250
summary: Wie sich die Suche auf einer mehrsprachigen Exponential-Website verhält - die eingebaute Suchmaschine indiziert alle Übersetzungen eines Objekts gemeinsam und filtert die Ergebnisse nach den Sprachen des Siteaccess, während Solr über ezfind ein Verhalten pro Sprache bietet (SearchMainLanguageOnly, ein Core pro Sprache); mit Suchstatistiken pro Sprache.
keywords: mehrsprachige Suche, Suche pro Sprache, eingebaute Suche, ezfind, Solr, SearchMainLanguageOnly, MultiCore, LanguagesCoresMap, Stammformreduktion, Stemming, Suchstatistiken, multilingual search, search per language, built-in search, stemming, search statistics
type: help
---
# Mehrsprachige Suche

Diese Seite richtet sich an Websiteersteller und Administratoren von Websites in mehreren Sprachen. Besucher erwarten,
dass die Suche auf der französischen Website französische Seiten findet. Wie nahe Sie dem kommen, hängt von der
Suchmaschine ab.

## Inhalt

1. [Die eingebaute Suchmaschine](#die-eingebaute-suchmaschine)
2. [Solr über ezfind](#solr-über-ezfind)
3. [Die Wahl](#die-wahl)
4. [Suchstatistiken pro Sprache](#suchstatistiken-pro-sprache)
5. [Prüfen](#prüfen)

## Die eingebaute Suchmaschine

`site.ini [SearchSettings] SearchEngine=eZSearchEngine` hält ihren Index in der Datenbank
([Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)). Im Kernel geprüft
(`kernel/search/plugins/ezsearchengine/ezsearchengine.php`):

- Wenn ein Objekt indiziert wird, kommen die durchsuchbaren Attribute **aller seiner Übersetzungen** in einen Index
  für das Objekt; der Wortindex hat keine Spalte für die Sprache.
- Die Ergebnisse sind auf Objekte beschränkt, die der Siteaccess in seinen Sprachen zeigt
  (`eZContentLanguage::languagesSQLFilter()`), und jedes Ergebnis wird in der bevorzugten Übersetzung des Siteaccess
  angezeigt.

Auf der französischen Website kann also ein Wort, das nur in der englischen Übersetzung einer Seite vorkommt, diese
Seite trotzdem finden, die dann auf Französisch (oder in der Ersatzsprache) angezeigt wird. Für zwei Amtssprachen mit
deutlich verschiedenem Wortschatz ist das oft akzeptabel; für eine strikte Trennung verwenden Sie Solr.

Die eingebaute Suchmaschine führt keine Stammformreduktion pro Sprache durch; `EnableWildcard` erlaubt Suchen wie
`term*`, auf Kosten der Leistung.

## Solr über ezfind

Die Erweiterung `ezfind` bindet einen Solr-Server an
([Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)). Ihre Einstellungen in
`ezfind.ini [LanguageSearch]`:

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `SearchMainLanguageOnly` | `enabled` | nur Ergebnisse in der ersten Sprache der `SiteLanguageList[]` des Siteaccess |
| (dieselbe) | `disabled` | die Ergebnisse folgen dem Ersatz aus `SiteLanguageList[]` und der Markierung „Immer verfügbar“ |
| `MultiCore` | `disabled` | `enabled`: jede Sprache wird in einem eigenen Solr-Core für eine sprachspezifische Analyse indiziert |
| `LanguagesCoresMap[<locale>]` | leer | welcher Core welche Sprache enthält; nicht zugeordnete Sprachen werden nicht indiziert |

Die sprachspezifische Analyse (Stammformreduktion, Stoppwörter) wird in Solr selbst eingerichtet, pro Core.

## Die Wahl

| Bedarf | Suchmaschine |
|---|---|
| Eine kleine zweisprachige Website | eingebaut |
| Ergebnisse strikt in der Sprache des Besuchers | Solr mit `SearchMainLanguageOnly=enabled` |
| Gute Erkennung von Wortformen pro Sprache | Solr mit einem Core pro Sprache |
| Facetten nach Sprache, Thema, Datum | Solr |

## Suchstatistiken pro Sprache

`LogSearchStats=enabled` wird pro Siteaccess gesetzt, sodass Sie es für jeden Sprach-Siteaccess einschalten und unter
**Setup > Suchstatistiken** sehen können, wonach Menschen in jeder Sprache suchen
([Suchstatistiken](../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)). Suchbegriffe können personenbezogene
Daten enthalten.

## Prüfen

Suchen Sie auf jeder Sprachwebsite nach einem Wort, das nur im Text einer Sprache vorkommt, und prüfen Sie, was
gefunden wird und in welcher Sprache es angezeigt wird.

## Verwandte Seiten

- [Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)
- [Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)
- [Ersatzregeln für unübersetzte Inhalte](ersatzregeln-fuer-unuebersetzte-inhalte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
