---
title: Prüfen, ob die Cronjobs laufen
audience: government, admin, hosting
category: public-sector
order: 100
summary: Schritt für Schritt - bestätigen, dass die geplanten Aufgaben der Site laufen - die Einträge der Crontab, die Karten und letzten Ausführungen unter Setup > Cronjobs, die Statusleiste der Workflows, die Prüfung in den Systeminformationen - und die vorgeschlagenen Zeilen der Crontab installieren, wenn sie fehlen.
keywords: Cronjobs, Crontab, crontab:list, runcronjobs, Setup Cronjobs, frequent, infrequent, workflow.php, geplant, nicht geplant, letzte Ausführungen, Systeminformationen, cronjobs, scheduled, not scheduled, recent runs, system information
type: help
---
# Prüfen, ob die Cronjobs laufen

Diese Aufgabe ist für Administratoren bei einer wöchentlichen Prüfung oder auf der Suche nach „freigegebene Inhalte
wurden nicht veröffentlicht“. Am Ende wissen Sie, dass jeder Teil planmäßig läuft. Referenz:
[Cronjobs](../../../../anleitungen/cronjobs.md); Hintergrund:
[Überwachung der Cronjobs](../../../../../eng-us/help/government/operations/monitoring/cronjob-monitoring.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Fehlende Zeilen installieren](#fehlende-zeilen-installieren)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `setup/managecronjobs` für **Setup > Cronjobs**; Shell-Zugang als Benutzer der Site für die Crontab.

## Schritte

1. Der installierte Zeitplan:

   ```bash
   ./console crontab:list
   php runcronjobs.php --list --allow-root-user
   ```

2. **Setup > Cronjobs** (`/setup/cronjobs`): Die Karte jedes Teils zeigt seinen Zeitplan (aus der Crontab gelesen),
   den nächsten und letzten Lauf und ob er **Angehalten** ist. **Letzte Ausführungen** listet die letzten 20 von der
   Seite aus gestarteten Läufe mit *OK* oder *n Probleme*.
3. **Setup > Workflow Prozesse**: Die Statusleiste muss sagen, dass `workflow.php` geplant ist.
4. **Setup > Systeminformationen**, Zustandsprüfung *Cronjobs*: Eine Warnung bedeutet, dass kein Lauf gefunden wurde
   oder der letzte mehr als zwei Tage zurückliegt.
5. Das Audit-Protokoll: `./console exp:audit search --name='system.cronjob.*' --limit=20 --allow-root-user` zeigt Läufe
   (`system.cronjob.run`) und Fehlschläge (`system.cronjob.fail`).

## Fehlende Zeilen installieren

Die Seite der Cronjobs schlägt Zeilen vor; ein üblicher Anfang (als Benutzer der Site, mit Ihren Pfaden und Ihrem
Siteaccess):

```
*/5 * * * * cd /path/to/installation && php runcronjobs.php --siteaccess=site frequent --allow-root-user >> var/log/cron-frequent.log 2>&1
17 * * * *  cd /path/to/installation && php runcronjobs.php --siteaccess=site infrequent --allow-root-user >> var/log/cron-infrequent.log 2>&1
```

Öffnen Sie die Crontab mit `./console crontab:edit`. Unter Velocity kann stattdessen dessen Scheduler die Teile
ausführen ([Scheduler von Velocity](../../../../../eng-us/features/6.0/velocity-scheduler.md)). Auf mehreren Webservern führen
Sie jeden Teil nur auf einem Server aus.

Hinweis: `--allow-root-user` ist nur nötig, wenn die Zeile als root läuft; als Benutzer der Site lassen Sie es weg.

## Verwandte Seiten
- [Einen Teil der Cronjobs von Hand ausführen](einen-teil-der-cronjobs-von-hand-ausfuehren.md)
- [Cronjobs während der Wartung anhalten](cronjobs-waehrend-der-wartung-anhalten.md)
- [Cronjobs (Hosting)](../../../hosting/betrieb/cronjobs.md)
- [Konsole der Cronjobs](../../../../../eng-us/features/6.0/cronjobs-console.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
