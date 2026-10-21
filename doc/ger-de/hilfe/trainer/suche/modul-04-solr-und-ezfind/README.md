---
title: "Modul 4: Solr und ezfind"
audience: trainer, admin, developer, hosting
category: training
order: 40
summary: Das vierte Modul des Kurses Suche, ezfind und einen Solr-Server installieren, exp:solr und die Solr-Einstellungen, Indizierung mit Solr sowie Relevanz, Elevation und Facetten. Vier Lektionen, 2 Stunden 10 Minuten.
keywords: suchkurs, modul 4, Solr, ezfind, exp:solr, solr.ini, SolrBase, updatesearchindexsolr, gewichtung, elevation, facetten, search course, module 4, boost, facets
type: help
---
# Modul 4: Solr und ezfind

Dieses Modul stellt eine Schulungsinstallation von der eingebauten Suchmaschine auf Solr über die Erweiterung ezfind
um und vermittelt, was sich ändert: die Installationsschritte, die beiden Blöcke der Solr-Einstellungen und den Befehl
`exp:solr`, die Indizierung und das Festschreiben sowie die Hebel, die entscheiden, was in den Ergebnissen zuerst
kommt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Dauer | 2 Stunden 10 Minuten (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Experten |
| Form | Praxisorientiert; zwei Terminals und die Administration auf der eigenen Schulungsinstallation |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [4.1 ezfind und einen Solr-Server installieren](ezfind-und-solr-installieren.md) | 35 Min. | ezfind hinzufügen, aktivieren und einrichten; das mitgelieferte Solr starten |
| [4.2 exp:solr und die Solr-Einstellungen](exp-solr-und-die-solr-einstellungen.md) | 30 Min. | `[SolrSettings]` konfigurieren, mit `[SolrBase]` vergleichen, mit `status` und `ping` prüfen |
| [4.3 Indizierung mit Solr](indizierung-mit-solr.md) | 30 Min. | Den Solr-Index aufbauen, die Commit-Einstellungen lesen, die Cronjob-Teile der Erweiterung finden |
| [4.4 Relevanz, Elevation und Facetten](relevanz-elevation-und-facetten.md) | 35 Min. | Eine Klasse gewichten, ein Objekt für eine Anfrage hervorheben, beides rückgängig machen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person **auf MySQL oder PostgreSQL** (`exp:install --db=mysql` oder `--db=pgsql`,
  siehe [Installation über die Kommandozeile](../../../../installation/07-installation-ueber-die-konsole.md)): Die Erweiterung liefert für
  ihre Tabelle keine SQLite-Schemadatei mit.
- Composer und Netzwerkzugang zu Packagist und GitHub auf jedem Rechner, oder das vorab vorbereitete Paket
  `se7enxweb/ezfind`.
- Eine Java-Laufzeitumgebung, die das in `extension/ezfind/java` mitgelieferte Solr startet. Die Installationshinweise
  der Erweiterung nennen die Java-Version, für die sie geschrieben wurden; proben Sie den Start vor dem Kurs auf einem
  Rechner der Übungsumgebung mit Ihrem Java, und halten Sie ein gemeinsames Solr als Ausweichlösung bereit (dann setzen
  die Teilnehmenden `SearchServerURI` und `Url` darauf).
- Genug Speicher für einen Solr-Prozess pro Rechner.
- Proben Sie `exp:solr ping` aus Lektion 4.2 gegen das Solr Ihrer Übungsumgebung.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 4.1 (35 Min.) |
| 0:35 | Lektion 4.2 (30 Min.) |
| 1:05 | Lektion 4.3 (30 Min.) |
| 1:35 | Lektion 4.4 (35 Min.) |
| 2:10 | Ende des Moduls |

## Verwandte Seiten

- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
