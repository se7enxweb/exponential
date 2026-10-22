---
title: "7x SQLI Import: hier beginnen"
audience: admin, integrator, developer
category: integration
order: 1
summary: Was 7x SQLI Import ist - die Erweiterung, die Daten aus CSV, XML, RSS, Atom, JSON und NDJSON nach Exponential holt, einmal oder nach Zeitplan -, für wen sie gedacht ist, was Version 2.0.0-alpha.1 enthält und wo Sie weiterlesen.
keywords: sqliimport, SQLI Import, 7x SQLI Import, Import, Inhaltsimport, CSV, XML, RSS, Atom, JSON, NDJSON, Zuordnung, Handler, Job-Warteschlange, expqueue, Zeitplan, Cron, Migration, Abgleich, Reiter Importe, content import, mapping, job queue, schedule, sync, Imports tab
type: help
---
# 7x SQLI Import: hier beginnen

**7x SQLI Import** (die Erweiterung `se7enxweb/sqliimport`) holt Inhalte von außen nach Exponential: einen
CSV-Export eines ERP-Systems, eine XML-Datei, einen RSS- oder Atom-Feed, einen JSON- oder NDJSON-Abzug. Ein Import
läuft einmal, für eine Migration, oder nach Zeitplan, für einen nächtlichen Abgleich. Version 2 ist der Nachfolger
von SQLI Import 1.x und lässt jeden Handler von 1.x weiterlaufen.

## Inhalt

1. [Für wen es gedacht ist](#für-wen-es-gedacht-ist)
2. [Was es tut](#was-es-tut)
3. [Was diese Version enthält](#was-diese-version-enthält)
4. [Die Seiten dieses Abschnitts](#die-seiten-dieses-abschnitts)
5. [Verwandte Seiten](#verwandte-seiten)

## Für wen es gedacht ist

- **Administratoren** starten Läufe, verfolgen sie im Reiter **Importe** und richten die Cron-Teile ein.
- **Integratoren** planen Importe aus anderen Systemen und wählen zwischen einer Zuordnung ohne Programmierung und
  einem Handler.
- **Entwickler** schreiben Handler, Quellen und Umwandlungen in PHP.

## Was es tut

- Liest **CSV/TSV, XML, RSS/Atom und JSON/NDJSON**, aus einer hochgeladenen Datei, einer `http(s)`-Adresse oder
  einer Datei in einem erlaubten Verzeichnis auf dem Server.
- Ordnet Quellfelder den Attributen einer Inhaltsklasse **ohne Programmierung** zu, mit benannten Umwandlungen wie
  `trim`, `date`, `number`, `richtext`, `image` und `lookup`. Eine neue Version wird nur veröffentlicht, wenn sich
  ein Feld geändert hat.
- Führt **Handler von 1.x unverändert** aus und neue Handler für 2.x für Importe, die Programmierung brauchen.
- Verarbeitet jeden Lauf über die **Job-Warteschlange** (expqueue), in Abschnitten mit Prüfpunkten. Ein Lauf lässt
  sich jederzeit abbrechen, und nur seine fehlgeschlagenen Datensätze lassen sich wiederholen.
- Führt **eine Zeile pro Datensatz** (angelegt, aktualisiert, unverändert, übersprungen, fehlgeschlagen) und ein
  Protokoll pro Lauf.
- Startet Importe nach **Zeitplänen**, in einer Zeitzone, auch über den Wechsel der Sommerzeit.

## Was diese Version enthält

Die Engine, die Kommandozeile `./console ext:sqliimport:*`, die Übersicht des Reiters Importe und die Seite jedes
Laufs. Der **Import-Editor** der Administration (Importe anlegen und bearbeiten, Zuordnungsraster, Vorschau,
Zeitpläne) kommt mit 2.0.0-beta.1. Bis dahin entstehen Importe durch die Migration von 1.x, aus Handlern von 1.x
oder in PHP.

Voraussetzungen: PHP 8.0 oder neuer, Exponential 6.0.14 oder neuer und die Erweiterungen expqueue und expui.

## Die Seiten dieses Abschnitts

| Seite | Was sie beantwortet |
|---|---|
| [Erste Schritte](erste-schritte.md) | Die Erweiterung installieren, die Cron-Teile, der Reiter Importe, ein erster Probelauf |
| [Importe und Läufe](importe-und-laeufe.md) | Was ein Import und ein Lauf sind, die Übersicht, die Seite eines Laufs, Abbrechen und Wiederholen |
| [Zeitpläne und Cron](zeitplaene-und-cron.md) | Arten von Zeitplänen, Zeitzonen, der Cron-Teil `sqliimport` |
| [Aktualisierung von 1.x](aktualisierung-von-1x.md) | 2.x über 1.x installieren und Handler, Zeitpläne, Verlauf und Rollen migrieren |

Das vollständige Handbuch der Erweiterung mit Quellen, Zuordnungen, Umwandlungen, Handlern, Einstellungen und
Befehlen erscheint in der Hilfe unter Erweiterungen, wenn die Erweiterung aktiv ist. Seine Seiten-ID ist
`ext/sqliimport/index.md`.

## Verwandte Seiten

- [Idempotente Importe über die remote_id](../integratoren/idempotente-importe-ueber-die-remote-id.md)
- [Ein Importskript schreiben](../integratoren/ein-importskript-schreiben.md)
- [Caching nach Importen](../integratoren/caching-nach-importen.md)
- [Cronjobs für Integrationen](../integratoren/cronjobs-fuer-integrationen.md)
