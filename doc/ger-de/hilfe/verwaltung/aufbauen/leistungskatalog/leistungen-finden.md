---
title: Leistungen finden
audience: government, website-creator, project-manager
category: public-sector
order: 50
summary: Wie Menschen Leistungen auf einer Exponential-Site finden - aufgabenorientierte Menüs, eine mit einem Fetch erzeugte Liste von A bis Z, die eingebaute Suche oder eZ Find, die Suchstatistik, um zu finden, was Menschen vermissen, und Titel in den Wörtern, die Menschen verwenden.
keywords: Auffindbarkeit, Navigation, A bis Z, Liste der Leistungen, Suche, Hervorhebung, Suchstatistik, Synonyme, Titel, findability, navigation, A to Z, service list, search, eZ Find, Solr, elevation, search statistics, LogSearchStats, synonyms, titles
type: help
---
# Leistungen finden

Diese Seite richtet sich an Site-Ersteller und Projektleiter, die Leistungen auffindbar machen. Menschen kommen auf eine
Leistungsseite über eine Suchmaschine, die Menüs der Site, eine Liste von A bis Z, eine Themen- oder Lebenslagenseite
oder die eigene Suche der Site. Jeder Weg braucht ein wenig Sorgfalt; diese Seite listet, was Exponential für jeden
bietet.

## Inhalt

1. [Menüs](#menüs)
2. [Eine Liste von A bis Z](#eine-liste-von-a-bis-z)
3. [Site-Suche](#site-suche)
4. [Aus Suchen lernen](#aus-suchen-lernen)
5. [Titel und Zusammenfassungen](#titel-und-zusammenfassungen)

## Menüs

Menüs entstehen aus dem Inhaltsbaum: Ordner unter der Wurzel werden zu Menüeinträgen
([Menüs und Navigation](../../../ersteller/website-ersteller/menues-und-navigation.md)). Für Leistungen:

- benennen Sie die obersten Ordner nach Aufgaben und Themen, die Menschen erkennen, nicht nach Abteilungen;
- geben Sie einer Leistung einen zweiten Ort in einem anderen Ordner statt einer Kopie, wenn sie an zwei Stellen
  gehört;
- halten Sie den Baum flach; eine Leistung drei Klicks von der Startseite entfernt ist leichter zu finden als eine
  sechs Klicks entfernt.

## Eine Liste von A bis Z

Ein Template-Fetch listet jede Leistung alphabetisch; gruppieren Sie im Template nach Anfangsbuchstaben.

```text
{def $services = fetch( 'content', 'tree', hash(
        'parent_node_id', ezini( 'NodeSettings', 'RootNode', 'content.ini' ),
        'class_filter_type', 'include',
        'class_filter_array', array( 'service' ),
        'sort_by', array( 'name', true() ) ) )}
<ul>
{foreach $services as $service}
  <li><a href={$service.url_alias|ezurl}>{$service.name|wash}</a></li>
{/foreach}
</ul>
{undef $services}
```

Eine Leistung mit zwei Orten erscheint in einem Baum-Fetch zweimal; fügen Sie `'main_node_only', true()` hinzu, um jede
einmal zu listen. Legen Sie die Liste in einen Cache-Block, denn ein Baum-Fetch über die ganze Site ist nicht umsonst
([View-Cache und Cache-Blöcke](../../../ersteller/website-ersteller/view-cache-und-cache-bloecke.md)).

## Site-Suche

| Suchmaschine | Wann | Lesen |
|---|---|---|
| Eingebaute Suche (`eZSearchEngine`) | kleine und mittlere Sites; kein zusätzlicher Server | [Site-Suche](../../../ersteller/website-ersteller/suche-auf-der-website.md) |
| eZ Find mit Solr | große Sites, Relevanzsortierung, Facetten, Hervorhebung gewählter Ergebnisse | [Suche mit eZ Find und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md) |

Für einen Leistungskatalog:

- markieren Sie den Namen der Leistung und **In Kürze** in der Klasse als durchsuchbar (sie sind die stärksten
  Signale);
- sortieren Sie mit eZ Find zuerst nach Relevanz; ezjscore liefert `SortBy[published]=desc` für die Dialoge des
  Editors mit, und `ezfind.ini [SearchSettings] SortBy[]` ändert es
  ([Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#die-reihenfolge-der-suchergebnisse-in-den-dialogen-des-editors));
- die Hervorhebung von eZ Find lässt Sie eine Leistung für einen Suchbegriff an die Spitze heften (die View
  `ezfind/elevate` der Erweiterung ezfind); verwenden Sie sie für die häufigsten Anliegen.

## Aus Suchen lernen

Mit `LogSearchStats=enabled` auf dem öffentlichen Siteaccess zeigt **Setup > Suchstatistiken** (`/search/stats`) jeden
Suchbegriff, wie oft er gesucht wurde, und die durchschnittliche Zahl der Ergebnisse
([URLs und Suchstatistik](../../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)). Suchbegriffe ohne Treffer
zeigen fehlende Wörter oder fehlende Seiten:

| Der Suchbegriff fand nichts, weil | Tun Sie |
|---|---|
| Menschen ein anderes Wort verwenden | ergänzen Sie das Wort in der Zusammenfassung oder als Synonym eines Schlagworts |
| ein Tippfehler | ergänzen Sie den häufigen Tippfehler als Synonym eines Schlagworts (nicht im sichtbaren Text) |
| die Leistung nicht Ihre ist | ergänzen Sie eine kurze Seite, die darauf verlinkt, wer sie anbietet |
| die Leistung fehlt | ergänzen Sie sie |

Suchbegriffe können personenbezogene Daten enthalten (Menschen tippen Namen und Nummern), weshalb die Aufzeichnung
voreingestellt aus ist; vereinbaren Sie, wie lange Statistiken aufbewahrt werden, und leeren Sie sie nach Plan
([Standards zum Datenschutz](../../../../standards/datenschutz.md)).

## Titel und Zusammenfassungen

| Tun Sie | Weil |
|---|---|
| Beginnen Sie den Titel mit der Sache: „Bewohnerparkausweise“ | Suchergebnisse und Menüs zeigen die ersten Wörter |
| Schreiben Sie die Zusammenfassung für den Auszug im Suchergebnis | Suchmaschinen und die Site-Suche zeigen ihn |
| Füllen Sie die Meta-Beschreibung mit xrowmetadata, wo die Zusammenfassung nicht verwendet wird | [xrowmetadata](../../../../funktionen/6.0/erweiterungen/xrowmetadata.md) |
| Halten Sie URL-Aliase kurz und stabil | der Alias kommt aus dem Namen; Umbenennen behält eine Weiterleitung ([Weiterleitungen und verschobene Inhalte](../../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)) |

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](README.md)
- [Lebenslagen und Themen](lebenslagen-und-themen.md)
- [SEO-Grundlagen](../../../ersteller/website-ersteller/seo-grundlagen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
