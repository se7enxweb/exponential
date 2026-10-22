---
title: Importe und Läufe in 7x SQLI Import
audience: admin, integrator, developer
category: integration
order: 20
summary: Was ein Import und ein Lauf in 7x SQLI Import sind, wie die Job-Warteschlange einen Lauf in Abschnitten verarbeitet, die Status und Ergebnisse, die Übersicht Importe, die Seite eines Laufs, Probeläufe, Abbrechen und das Wiederholen fehlgeschlagener Zeilen.
keywords: sqliimport, Import, Lauf, Probelauf, Wiederholen, Abbrechen, Abschnitt, Prüfpunkt, Job-Warteschlange, expqueue, angelegt, aktualisiert, unverändert, übersprungen, fehlgeschlagen, Übersicht, sqliimport/list, sqliimport/run, run, dry run, retry, cancel, chunk, checkpoint, job queue, dashboard
type: help
---
# Importe und Läufe in 7x SQLI Import

## Inhalt

1. [Importe](#importe)
2. [Läufe](#läufe)
3. [Die Übersicht](#die-übersicht)
4. [Die Seite eines Laufs](#die-seite-eines-laufs)
5. [Auf der Kommandozeile](#auf-der-kommandozeile)

## Importe

Ein Import legt fest, woher die Daten kommen (eine Quelle für CSV, XML, RSS/Atom, JSON oder Tabellen, oder ein
Handler von 1.x, der seine Daten selbst liest), was sie verarbeitet (die Zuordnung ohne Programmierung oder ein
Handler), wohin neue Inhalte kommen und wie bestehende Inhalte behandelt werden (aktualisieren, nur anlegen oder nur
aktualisieren).

Importe werden im Import-Editor der Administration angelegt und geändert
([Der Import-Editor](der-import-editor.md)). Auch die Migration von 1.x legt Importe an, und ein Skript kann sie in
PHP anlegen.

## Läufe

Ein Lauf ist eine Ausführung eines Imports. Ihn verarbeitet die Job-Warteschlange, nicht die Seite oder der
Befehl, der ihn startet:

- ein Start-Job legt eine Momentaufnahme der Quelle an und zählt die Datensätze;
- Abschnitts-Jobs verarbeiten je 200 Datensätze (`[RunSettings] ChunkSize`), nacheinander, in der Reihenfolge der
  Quelle;
- ein Abschluss-Job leert die View-Caches der geänderten Objekte, indiziert sie und schreibt die Zusammenfassung.

Ein Import läuft immer nur einmal gleichzeitig; verschiedene Importe laufen parallel. Ein Abschnitt speichert alle
20 Datensätze einen Prüfpunkt; stirbt ein Worker, setzt der nächste Versuch dort fort.

| Status | Bedeutung |
|---|---|
| In der Warteschlange | Wartet auf einen Worker der Job-Warteschlange |
| Läuft | Wird verarbeitet |
| Abgeschlossen | Beendet, kein Datensatz ist fehlgeschlagen |
| Mit Fehlern abgeschlossen | Beendet, einige Datensätze sind fehlgeschlagen |
| Fehlgeschlagen | Abgebrochen, etwa weil die Quelle nicht lesbar war |
| Abgebrochen | Von einem Benutzer abgebrochen |

Jeder Datensatz bekommt eine Zeile mit seinem Ergebnis: **angelegt**, **aktualisiert** (ein Feld hat sich
geändert, daher wurde eine neue Version veröffentlicht), **unverändert** (nichts zu veröffentlichen),
**übersprungen** (etwa weil das Objekt im Papierkorb liegt), **fehlgeschlagen** (mit der Meldung) oder
**verarbeitet** (ein Handler von 1.x hat ihn verarbeitet).

Ein **Probelauf** veröffentlicht nichts. Er liest die ersten 20 Datensätze (`[RunSettings] DryRunRows`) und zeigt
pro Datensatz, welche Felder sich ändern würden, mit aktuellem und neuem Wert.

## Die Übersicht

**Importe** (`sqliimport/list`) zeigt:

- Kennzahlen: Importe, Läufe heute, laufende oder wartende Läufe, in den letzten 24 Stunden fehlgeschlagene Läufe,
  den nächsten geplanten Lauf;
- eine Leiste **Run now** (jetzt ausführen) und die Schaltfläche **New import**;
- die Importe mit letztem und nächstem Lauf. **Run now** und **Dry run** (Probelauf) sehen Benutzer mit Run imports
  oder Manage imports; **Edit** und unter **More** die Aktionen **Duplicate**, **Enable** oder **Disable** und
  **Delete** sehen Benutzer mit Manage imports;
- die Läufe, gefiltert nach Status (alle, laufend, fehlgeschlagen, abgeschlossen, abgebrochen).

Das linke Menü verlinkt außerdem die Seiten Schedules, Catalogue und Settings, die Seiten von 1.x (Importaufträge,
geplante Importe, ein neuer Auftrag für 1.x) und die Job-Warteschlange. Das Dashboard der Administration hat einen
Block **Imports** mit den letzten Läufen und einem Hinweis, wenn in den letzten 24 Stunden Läufe fehlgeschlagen
sind.

## Die Seite eines Laufs

`sqliimport/run/<id>` zeigt den Fortschritt, die Ergebnisse als Kennzahlen (jede filtert die Datensätze), die
Datensätze mit Objekt, Version und Meldung und das Protokoll des Laufs.

Solange ein Lauf wartet oder läuft, verfolgt die Seite ihn live: Alle zwei Sekunden zeigt sie den neuen Status,
den Fortschritt, die Zähler und die Protokollzeilen, ohne die Seite neu zu laden, und wenn der Lauf endet, lädt sie
noch einmal mit den endgültigen Datensätzen. **Refresh** lädt die Seite von Hand neu.

Zwei Schaltflächen wirken auf den Lauf (sie brauchen Run imports oder Manage imports):

- **Cancel** (abbrechen) hält einen Lauf an, der noch nicht beendet ist. Bereits verarbeitete Datensätze bleiben,
  wie sie sind.
- **Retry failed rows** (fehlgeschlagene Zeilen wiederholen) startet einen neuen Lauf, der nur die
  fehlgeschlagenen Datensätze eines beendeten Laufs verarbeitet. Für Handler von 1.x gibt es das nicht, weil ihre
  Zeilen keinen Datensatz enthalten; starten Sie deren Import stattdessen erneut.

## Auf der Kommandozeile

```bash
./console ext:sqliimport:run <bezeichner>          # einen Lauf einstellen
./console ext:sqliimport:run <bezeichner> --wait   # hier verarbeiten und das Ergebnis ausgeben
./console ext:sqliimport:status <lauf-id>          # der Lauf mit seinem Protokoll
./console ext:sqliimport:cancel <lauf-id>
./console ext:sqliimport:retry <lauf-id>
```

Beendete Läufe, die älter als 90 Tage sind (`[RunSettings] KeepDays`), entfernt der Cron-Teil samt Zeilen und
Protokoll.

Weiter: [Zeitpläne und Cron](zeitplaene-und-cron.md).
