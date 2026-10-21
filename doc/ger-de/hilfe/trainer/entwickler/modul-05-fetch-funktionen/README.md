---
title: "Modul 5: Fetch-Funktionen"
audience: trainer, developer
category: training
order: 50
summary: Das fünfte Modul des Kurses Entwickler. Die Teilnehmenden holen Knoten und Listen, filtern, sortieren und blättern sie seitenweise, verwenden die Fetches der Module für Benutzer, Sektionen und Beziehungen und schreiben eine eigene Fetch-Funktion. 3 Stunden 30 Minuten.
keywords: entwickler, modul 5, fetch, fetch-funktion, inhaltsliste, baum, list_count, attribute_filter, class_filter, sort_by, seitenweises blättern, navigator, has_access_to, current_user, function_definition.php, funktionssammlung, developers course, module 5, fetch functions, content list, tree, paging, function collection
type: help
---
# Modul 5: Fetch-Funktionen

Templates erhalten ihre Daten über Fetch-Funktionen. Dieses Modul führt vom ersten `fetch( 'content', 'node', ... )`
über die Fetches anderer Kernel-Module bis zu einer gefilterten Liste mit Seitennavigation auf einer echten Seite und
endet damit, dass die Teilnehmenden eine Fetch-Funktion in ihrer eigenen Erweiterung schreiben.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 3 Stunden 30 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 5.1: Knoten und Listen holen](knoten-und-listen-abrufen.md) | 45 Minuten | `node`, `list`, `list_count`, `function_definition.php` lesen |
| [Lektion 5.2: Filter, Sortierung und Seitennavigation](filter-sortierung-und-blaettern.md) | 55 Minuten | Klassen- und Attributfilter, `tree` und `depth`, `sort_by`, der Navigator |
| [Lektion 5.3: Fetches anderer Module](fetches-anderer-module.md) | 40 Minuten | `current_user`, `has_access_to`, Sektionen, Beziehungen, Website-Daten |
| [Lektion 5.4: Eine eigene Fetch-Funktion](eine-eigene-fetch-funktion.md) | 60 Minuten | Ein Modul, `function_definition.php` und eine Funktionssammlungsklasse |

## Dieses Modul vorbereiten

- Die Schulungsinstallation braucht genug Inhalte, damit Filter und Seitennavigation etwas zeigen: einen Ordner mit
  mindestens sechs Artikeln oder Notizen und einige Ordner unterhalb von Knoten 2.
- `mysite_hello` ist auf jeder Installation aktiv (Lektion 3.1), und der Override aus Lektion 3.2 ist für die Übung
  zur Seitennavigation vorhanden.
- Für Lektion 5.3 haben alle Teilnehmenden ein Website-Konto (keinen Administrator), um den angemeldeten mit dem
  abgemeldeten Zustand zu vergleichen.
- Proben Sie Lektion 5.4 einmal; halten Sie eine fertige Kopie der drei Dateien bereit, die Sie an Teilnehmende
  ausgeben, die zurückfallen.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 5.1 | 45 |
| Lektion 5.2 | 55 |
| Pause | 10 |
| Lektion 5.3 | 40 |
| Lektion 5.4 | 60 |
| **Gesamt** | **210** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
