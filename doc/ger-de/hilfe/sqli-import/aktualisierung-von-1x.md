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

5. Migrieren Sie. Mit `--roles` bekommt jede Rolle mit `sqliimport/manageimports` die Richtlinie
   `sqliimport/manage` (Manage imports) mit derselben Handler-Einschränkung:

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
| Rollen (`--roles`) | Rollen mit `manageimports` bekommen `manage` (Manage imports) mit derselben Handler-Einschränkung. Rollen nur mit `listimports` brauchen nichts Neues: `listimports` lässt sie weiterhin den Reiter Importe ansehen. Rollen mit `sqliimport/*` bleiben unverändert. |

Die Migration lässt sich erneut ausführen; bereits Migriertes bleibt unangetastet. Offene Importaufträge von 1.x
startet der Cron-Teil `sqliimport` als echte Läufe.

## Was sich ändert

- Importe werden im Import-Editor bearbeitet ([Der Import-Editor](der-import-editor.md)). Ein migrierter Import
  eines Handlers von 1.x hat die Schritte Quelle, Verarbeitung, Zeitplan und Prüfung.
- Zwei Richtlinien regeln den Zugriff: Manage imports (`manage`) und Run imports (`run`). `manageimports` zählt
  weiter als Manage imports, `listimports` erlaubt das Ansehen ([Erste Schritte](erste-schritte.md#der-reiter-importe)).
- Handler von 1.x laufen unverändert über die Job-Warteschlange. Ihre Datensätze haben das Ergebnis „Processed
  (1.x)“ (verarbeitet), ihre Fortschrittsnotizen gehen in das Protokoll des Laufs. Nur fehlgeschlagene Zeilen zu
  wiederholen und einen Lauf rückgängig zu machen ist für sie nicht möglich.
- Die Seiten von 1.x sind Teil der Seiten von 2.0, und das linke Menü hat keine Gruppe „sqliimport 1.x“ mehr:

  | Seite von 1.x | Wo sie jetzt ist |
  |---|---|
  | `sqliimport/items` (die Importaufträge) | Die Übersicht `sqliimport/list`, Abschnitt „1.x import requests“, mit **Cancel** und **Interrupt** |
  | `sqliimport/addimport` (einen Import anfordern) | Das Formular **Request import** in diesem Abschnitt |
  | `sqliimport/purgelist` | **Purge history** in diesem Abschnitt |
  | `sqliimport/scheduledlist`, `sqliimport/addscheduled` | `sqliimport/schedules`, Abschnitt „1.x scheduled imports“ |
  | der Fortschritt eines Auftrags | die Seite des Laufs `sqliimport/run/<id>` |

  Die alten Adressen funktionieren weiter: Sie leiten auf die neuen Seiten um, und ein POST mit den Feldern von 1.x
  tut weiter, was er tat; Skripte, die dorthin senden, brauchen keine Änderung. Die Aktionen sind POST-Formulare mit
  Bestätigung.
- Ein Import blockiert die anderen nicht mehr: 1.x hatte eine globale Sperre, 2.x führt verschiedene Importe
  parallel aus.
- Neu in 2.0 für jeden Import, auch die migrierten: Probeläufe, die Live-Seite eines Laufs, Benachrichtigungen und
  Statistik ([Importe und Läufe](importe-und-laeufe.md)). Ein Handler von 1.x, der nur Felder aus einer Datei in eine
  Klasse kopiert, lässt sich durch eine Quelle und eine Zuordnung ohne Programmierung ersetzen; das bringt auch das
  Rückgängigmachen und die Konfliktregeln.

## Behobene Fehler von 1.x

Unter Exponential 6 behebt Version 2 diese Fehler von 1.x:

- Die Felder von Importaufträgen und geplanten Importen aus 1.x waren leer, weil ihr Getter den persistenten
  Speicher des Kernels umging; Liste und Cronjob von 1.x sahen daher nichts.
- Mit NUL-Bytes serialisierte Optionen wurden unter SQLite abgeschnitten.
- Optionstext mit einer Zeile ohne „=“ erzeugte unter PHP 8 eine Warnung.

Das Handbuch der Erweiterung enthält die vollständige Seite zur Migration, auch dazu, wie Sie einen einfachen Handler
von 1.x durch eine Zuordnung ohne Programmierung ersetzen: `/exphelp/viewer/ext/sqliimport/ger-de/migration-1x.md`.
