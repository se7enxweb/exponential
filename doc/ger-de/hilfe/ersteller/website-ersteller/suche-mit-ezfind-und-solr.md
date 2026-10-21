---
title: Suche mit ezfind und Solr
audience: website-creator, admin
category: website-creation
order: 570
summary: Wann sich die Suche der Website auf Solr umstellen lässt, wie die Erweiterung ezfind und die Einstellungen in solr.ini sie einschalten, Solr mit exp:solr steuern, neu indexieren und was Solr hinzufügt (Facetten, Relevanz, Dateiinhalte, Tag-Wolken).
keywords: Solr, Suchmaschine, Facetten, Relevanz, neu indexieren, Suchindex, Tag-Wolke, Website-Ersteller, ezfind, ezsolr, search engine, facets, relevance, solr.ini, exp:solr, reindex, updatesearchindex, tag cloud, website creator
type: help
---
# Suche mit ezfind und Solr

Diese Seite richtet sich an Website-Ersteller, deren Website der eingebauten Suche entwächst. Die Erweiterung `ezfind`
verbindet Exponential mit einem Apache-Solr-Server; Solr ist optional, und nichts ändert sich, bis Sie es einschalten
([exp:solr](../../../../eng-us/features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server)).

## Inhalt

1. [Wann sich Solr lohnt](#wann-sich-solr-lohnt)
2. [Die Bestandteile](#die-bestandteile)
3. [Einschalten](#einschalten)
4. [Indexieren](#indexieren)
5. [Was sich für Templates ändert](#was-sich-für-templates-ändert)

## Wann sich Solr lohnt

| Bedarf | Eingebaute Suche | Solr |
|---|---|---|
| Seiten über Wörter finden | ja | ja |
| Relevanzsortierung und Gewichtung | einfach | ja |
| Facetten (Ergebnisse nach Klasse, Tag, Datum filtern) | nein | ja |
| Sehr große Anzahl von Objekten | wird langsamer | dafür gebaut |
| Tag-Wolken aus der Suche (`eztags` `TagCloudOverSolr`) | nein | ja |

Solr ist ein eigener Server (Java), den Sie installieren, betreiben, überwachen und sichern müssen. Für eine kleine
Website reicht die eingebaute Suche.

## Die Bestandteile

| Bestandteil | Was |
|---|---|
| `extension/ezfind` | die Erweiterung: `ezfind.ini`, `solr.ini` und `site.ini.append.php` mit `SearchEngine=ezsolr` |
| `settings/solr.ini` `[SolrSettings]` | die Solr-Steuerung des Kernels: `Enabled`, `Url`, `BinaryPath`, `Host`, `Port`, `DataDir` |
| `exp:solr` | der Konsolenbefehl: `status`, `start`, `stop`, `restart`, `ping` |

## Einschalten

1. Installieren Sie einen Solr-Server (lokal mit `BinaryPath` oder entfernt mit `Url`).
2. Setzen Sie `[SolrSettings] Enabled=true` und die Adresse in `settings/override/solr.ini.append.php`.
3. Prüfen Sie: `./console exp:solr status --allow-root-user`, dann `ping`.
4. Aktivieren Sie `ezfind`; seine `site.ini.append.php` stellt `[SearchSettings] SearchEngine` auf `ezsolr` um.
5. Leeren Sie den INI-Cache und bauen Sie den Index auf (siehe unten).

## Indexieren

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user
```

Bei verzögerter Indexierung stellen Verschiebungen und Änderungen der Sichtbarkeit Aktionen `index_moved_node` für Solr
in die Warteschlange, weil Solr Pfade speichert
([10.8 Suche](../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung)). Halten Sie den
Cronjob-Teil für die Indexierung am Laufen.

## Was sich für Templates ändert

`content/search` funktioniert weiter; die Ergebnisse kommen aus Solr. Facetten und Filter stehen über die
Fetch-Funktionen von ezfind und seine eigenen Such-Templates zur Verfügung; lesen Sie die Dokumentation der Erweiterung
in `extension/ezfind/` für die genauen Namen in Ihrer Version, bevor Sie Facetten-Templates schreiben. Auch die
Suchdialoge des Online-Editors verwenden Solr, wenn es aktiv ist
([4. Der Online-Editor](../../../anleitungen/inhaltsmodell-und-bearbeitung.md#4-der-online-editor-und-tinymce-8)).

## Verwandte Seiten

- [Suche auf der Website](suche-auf-der-website.md)
- [Befehle zur Serversteuerung](../../../../eng-us/features/6.0/web-server-and-solr-commands.md)
- [Anwendungen skalieren](../anwendungsentwickler/anwendungen-skalieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
