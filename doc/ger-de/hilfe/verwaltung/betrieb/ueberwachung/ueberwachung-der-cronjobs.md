---
title: Überwachung der Cronjobs
audience: government, admin, hosting
category: public-sector
order: 90
summary: Sicherstellen, dass die geplanten Jobs einer Exponential-Website des öffentlichen Sektors wirklich laufen - was von ihnen abhängt (Freigaben, Audit-Warnungen, Benachrichtigungen, Indexierung), Setup > Cronjobs mit seinen Läufen und Problemen, Pausen, die Prüfung in den Systeminformationen und die Überwachung der Crontab von außen.
keywords: Cronjobs, Cron, geplante Jobs, runcronjobs, Crontab, Setup Cronjobs, frequent, infrequent, workflow.php, Audit-Cronjob, Pause, letzte Läufe, Überwachung, cronjobs, scheduled jobs, pause, recent runs, monitoring
type: help
---
# Überwachung der Cronjobs

Diese Seite richtet sich an die Administration, die bemerken muss, wenn geplante Arbeit stehen bleibt. Auf einer
öffentlichen Website hängt mehr von Cronjobs ab, als man erwartet: Freigegebene Inhalte veröffentlicht der
Workflow-Cronjob, Audit-Warnungen und Archive kommen aus dem Teil `audit`, Benachrichtigungen und Newsletter werden per
Cron versendet. Referenz: [Cronjobs](../../../../anleitungen/cronjobs.md); Sicht des Hostings:
[Cronjobs (Hosting)](../../../hosting/betrieb/cronjobs.md).

## Inhalt
1. [Was stehen bleibt, wenn Cron stehen bleibt](#was-stehen-bleibt-wenn-cron-stehen-bleibt)
2. [Wo Sie nachsehen](#wo-sie-nachsehen)
3. [Pausen](#pausen)
4. [Überwachung von außen](#überwachung-von-außen)
5. [Verwandte Seiten](#verwandte-seiten)

## Was stehen bleibt, wenn Cron stehen bleibt

| Teil oder Skript | Folge, wenn er nicht läuft |
|---|---|
| `workflow.php` (frequent) | freigegebene Inhalte werden nie veröffentlicht |
| Teil `audit` (in frequent) | keine Indexierung, keine Warnungen, keine tägliche Rotation, Prüfung und Archivierung |
| `notification.php` (frequent) | keine Benachrichtigungs-E-Mails |
| `mailpreferences` (infrequent) | Einwilligungsprotokoll und Bestätigungen werden nicht innerhalb der Aufbewahrungsfrist geführt |
| Indexierung, Sitemaps, RSS-Import | Suche und Feeds veraltet |
| `hide.php`, `unpublish.php` (Teil global) | geplantes Verbergen und Entfernen findet nicht statt |

## Wo Sie nachsehen

- **Setup > Cronjobs** (`/setup/cronjobs`, erfordert `setup/managecronjobs`): eine Karte je Teil mit seinem Zeitplan
  aus der installierten Crontab, dem nächsten und letzten Lauf, **Teil starten** und **Letzte Läufe** (die letzten 20
  von der Seite gestarteten) mit *OK* oder *n Probleme*. Probleme sind Zeilen mit Wörtern wie *error* oder *failed*: ein
  Hinweis, kein Urteil.
- **Setup > Workflow-Prozesse**: Die Statusleiste zeigt, ob `workflow.php` eingeplant ist.
- **Setup > Systeminformationen**: Die Zustandsprüfung *Cronjobs* warnt, wenn kein Lauf gefunden wird oder der letzte
  mehr als zwei Tage zurückliegt.
- Shell: `./console crontab:list` zeigt die installierten Zeilen; `php runcronjobs.php --list --allow-root-user` die
  Teile.
- Audit-Trail: Jeder Lauf ist `system.cronjob.run` oder `system.cronjob.fail`, gleich wer ihn gestartet hat.

## Pausen

Ein Teil kann während eines Releases oder einer Migration auf **Setup > Cronjobs** angehalten werden (**Anhalten**,
**Alle Teile anhalten**); ein angehaltener Teil wird von jedem Ausführungsprogramm ausgelassen, und die Karte zeigt,
seit wann, von wem und warum ([Cronjob-Teile anhalten](../../../../../eng-us/features/6.0/cronjob-pause.md)). Die Überwachung
sollte „angehalten“ als Zustand behandeln, der täglich gemeldet wird: Eine nach einem Wartungsfenster vergessene Pause
stoppt Freigaben und Warnungen unbemerkt.

## Überwachung von außen

- Lösen Sie eine Warnung aus, wenn die Prüfung der Systeminformationen fehlschlägt:
  `./console exp:system:info --json --allow-root-user` endet mit 1, wenn eine Prüfung fehlschlägt.
- Schreiben Sie die Cron-Ausgabe in ein Log und lösen Sie eine Warnung aus, wenn es nicht mehr wächst, zum Beispiel
  mit den Zeilen, die die Cronjob-Seite vorschlägt:

  ```
  */5 * * * * cd /path/to/installation && php runcronjobs.php --siteaccess=site frequent --allow-root-user >> var/log/cron-frequent.log 2>&1
  ```

- Führen Sie bei mehreren Webservern jeden Teil nur auf einem Server aus, und denken Sie daran, dass Pausendateien auf
  jedem Server lokal sind, sofern `cronjob.ini [PauseSettings] Directory` nicht auf ein gemeinsames Verzeichnis zeigt.
- Unter Velocity kann dessen eigener Scheduler die Teile statt des System-Crons starten
  ([Velocity-Scheduler](../../../../../eng-us/features/6.0/velocity-scheduler.md)).

## Verwandte Seiten
- [Prüfen, ob die Cronjobs laufen](../../../../../eng-us/help/government/tasks/operations/check-that-cronjobs-run.md)
- [Einen Cronjob-Teil von Hand ausführen](../../../../../eng-us/help/government/tasks/operations/run-a-cronjob-part-by-hand.md)
- [Workflow-Fehlerbehebung](../redaktionelle-workflows/workflow-fehlerbehebung.md)
- [Cronjob-Konsole](../../../../../eng-us/features/6.0/cronjobs-console.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
