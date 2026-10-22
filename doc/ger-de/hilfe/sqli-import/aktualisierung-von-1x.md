---
title: Aktualisierung von SQLI Import 1.x
audience: admin, integrator, developer
category: integration
order: 40
summary: Eine Installation von SQLI Import 1.x auf 7x SQLI Import 2 umstellen - über 1.x installieren, der Probelauf und die Optionen --apply und --roles von migrate-1x, was aus Handlern, geplanten Importen, Verlauf und Rollen wird, was sich in der Administration ändert und welche Fehler von 1.x unter Exponential 6 behoben sind.
keywords: sqliimport, SQLI Import 1.x, Aktualisierung, migrate-1x, Migration, Legacy-Handler, ISQLIImportHandler, geplanter Import, sqliimport_scheduled, sqliimport_item, listimports, manageimports, Rollen, sqliimport_run, sqliimport/items, upgrade, legacy handler, scheduled import, roles
type: help
---
# Aktualisierung von SQLI Import 1.x

Version 2 lässt jeden Handler, jede Einstellung, Tabelle, Richtlinie und Seite von 1.x funktionieren. Nichts von
1.x wird gelöscht.

## Inhalt

1. [Schritte](#schritte)
2. [Was die Migration tut](#was-die-migration-tut)
3. [Was sich ändert](#was-sich-ändert)
4. [Behobene Fehler von 1.x](#behobene-fehler-von-1x)

## Schritte

1. Sichern Sie die Datenbank.
2. Installieren Sie 2.x über 1.x und aktivieren Sie expqueue und expui ([Erste Schritte](erste-schritte.md)).
3. Legen Sie die Tabellen an:

   ```bash
   ./console ext:expqueue:install-schema
   ./console ext:sqliimport:install-schema
   ```

4. Sehen Sie sich an, was die Migration tun würde. Dabei ändert sich nichts:

   ```bash
   ./console ext:sqliimport:migrate-1x
   ```

5. Migrieren Sie. Mit `--roles` bekommen die Rollen mit Richtlinien von 1.x die passenden Richtlinien von 2.x:

   ```bash
   ./console ext:sqliimport:migrate-1x --apply --roles
   ```

6. Ersetzen Sie die Cron-Teile von 1.x durch `expqueue` und `sqliimport`, beide jede Minute
   ([Zeitpläne und Cron](zeitplaene-und-cron.md)).

## Was die Migration tut

| Bereich | Ergebnis |
|---|---|
| Handler | Jeder Handler von 1.x wird ein Import mit dem Bezeichner `legacy-<handler>` |
| Geplante Importe | Jeder wird ein Import mit seinen Optionen und einem Zeitplan mit derselben Häufigkeit. Der geplante Import von 1.x wird abgeschaltet, nicht entfernt. |
| Verlauf | Vergangene Importaufträge von 1.x werden schreibgeschützte Läufe (Status, Zeit, Benutzer) |
| Rollen (`--roles`) | Rollen mit `listimports` oder `manageimports` bekommen `view`; Rollen mit `manageimports` bekommen außerdem `edit` und `run` mit derselben Handler-Einschränkung |

Die Migration lässt sich erneut ausführen; bereits Migriertes bleibt unangetastet. Offene Importaufträge von 1.x
startet der Cron-Teil `sqliimport` als echte Läufe.

## Was sich ändert

- Handler von 1.x laufen unverändert über die Job-Warteschlange. Ihre Datensätze haben das Ergebnis „verarbeitet“,
  ihre Fortschrittsnotizen gehen in das Protokoll des Laufs. Nur fehlgeschlagene Zeilen zu wiederholen ist für sie
  nicht möglich.
- `sqliimport/list` ist jetzt die Übersicht von 2.x. Die Liste der Importaufträge von 1.x ist nach
  `sqliimport/items` umgezogen. Die anderen Seiten von 1.x (geplante Importe, neuer Importauftrag) bleiben im
  linken Menü unter „sqliimport 1.x“.
- Die Aktionen der Seiten von 1.x (abbrechen, unterbrechen, entfernen, bereinigen) sind POST-Formulare mit
  Bestätigung.
- Ein Import blockiert die anderen nicht mehr: 1.x hatte eine globale Sperre, 2.x führt verschiedene Importe
  parallel aus.

## Behobene Fehler von 1.x

Unter Exponential 6 behebt Version 2 diese Fehler von 1.x:

- Die Felder von Importaufträgen und geplanten Importen aus 1.x waren leer, weil ihr Getter den persistenten
  Speicher des Kernels umging; Liste und Cronjob von 1.x sahen daher nichts.
- Mit NUL-Bytes serialisierte Optionen wurden unter SQLite abgeschnitten.
- Optionstext mit einer Zeile ohne „=“ erzeugte unter PHP 8 eine Warnung.

Das Handbuch der Erweiterung (Seiten-ID `ext/sqliimport/index.md`, in der Hilfe unter Erweiterungen) enthält die
vollständige Seite zur Migration, auch dazu, wie Sie einen einfachen Handler von 1.x durch eine Zuordnung ohne
Programmierung ersetzen.
