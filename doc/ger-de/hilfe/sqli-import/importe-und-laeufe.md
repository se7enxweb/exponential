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

Ein Import legt fest, woher die Daten kommen (eine Quelle für CSV, XML, RSS/Atom oder JSON, oder ein Handler von
1.x, der seine Daten selbst liest), was sie verarbeitet (die Zuordnung ohne Programmierung oder ein Handler), wohin
neue Inhalte kommen und wie bestehende Inhalte behandelt werden (aktualisieren, nur anlegen oder nur aktualisieren).

In 2.0.0-alpha.1 entstehen Importe durch die Migration von 1.x, aus den Handlern von 1.x in `sqliimport.ini` oder
in PHP. Der Import-Editor der Administration kommt mit 2.0.0-beta.1.

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
- eine Leiste **Run now** (jetzt ausführen);
- die Importe mit letztem und nächstem Lauf und **Run now** und **Dry run** (Probelauf) pro Import;
- die Läufe, gefiltert nach Status (alle, laufend, fehlgeschlagen, abgeschlossen, abgebrochen).

Das linke Menü verlinkt außerdem die Seiten von 1.x (Importaufträge, geplante Importe, ein neuer Auftrag für 1.x)
und die Job-Warteschlange.

## Die Seite eines Laufs

`sqliimport/run/<id>` zeigt den Fortschritt, die Ergebnisse als Kennzahlen (jede filtert die Datensätze), die
Datensätze mit Objekt, Version und Meldung und das Protokoll des Laufs. Zwei Schaltflächen wirken auf den Lauf:

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
