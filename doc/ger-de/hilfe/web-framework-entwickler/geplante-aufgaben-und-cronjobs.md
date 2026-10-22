---
title: "Geplante Aufgaben: Cronjobs"
audience: web-framework-developer, developer
category: web-frameworks
order: 330
summary: Das Exponential-Gegenstück zum Scheduler von Laravel, zu Symfony Scheduler, Celery beat, whenever und Springs @Scheduled - Cronjob-Teile in cronjob.ini, die runcronjobs.php aus der System-Crontab ausführt, die mitgelieferten Teile, die die Site braucht, einen eigenen Teil in Ihrer Erweiterung hinzufügen, Sperren, Pausen und die Konsole unter Setup > Cronjobs.
keywords: Cron, Cronjob, Cronjob-Teil, geplante Aufgaben, Zeitplanung, Scheduler, scheduled tasks, runcronjobs.php, cronjob.ini, CronjobPart, frequent, infrequent, crontab, cron:, MaxScriptExecutionTime, Pause, pause, Laravel scheduler, Symfony Scheduler, Celery beat, Spring Scheduled, Web-Framework-Entwickler, web framework developer
type: help
---
# Geplante Aufgaben: Cronjobs

Diese Seite richtet sich an Entwickler, die geplante Aufgaben im Code deklarieren (`$schedule->command()->hourly()`,
`#[AsCronTask]`, Celery beat, `@Scheduled`). In Exponential wird geplante Arbeit in **Cronjob-Teilen** gebündelt, die in
`settings/cronjob.ini` deklariert sind; die Crontab des Systems führt `php runcronjobs.php <part>` in dem Rhythmus aus,
den jeder Teil benötigt. Der Zeitplan selbst steht in der Crontab, nicht im Code ([Cronjobs für Anwendungen](../ersteller/anwendungsentwickler/cronjobs-fuer-anwendungen.md)).

## Inhalt

1. [Teile und Skripte](#teile-und-skripte)
2. [Teile, die die Site braucht](#teile-die-die-site-braucht)
3. [Einen eigenen Teil hinzufügen](#einen-eigenen-teil-hinzufügen)
4. [Ausführen und planen](#ausführen-und-planen)
5. [Sperren, Pausen und Überwachung](#sperren-pausen-und-überwachung)
6. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Teile und Skripte

| Block | Enthält |
|---|---|
| `[CronjobSettings] Scripts[]` | den globalen Teil (wird ausgeführt, wenn kein Teil angegeben ist) |
| `[CronjobSettings] ExtensionDirectories[]` | Erweiterungen, die ein Verzeichnis `cronjobs/` haben |
| `[CronjobPart-<name>] Scripts[]` | einen benannten Teil |

`eZRunCronjobs` (`kernel/classes/ezruncronjobs.php`) führt ein Skript aus; der Code eines Kernel-Teils ist eine
Klasse `Exponential\Runnable\CronjobPart` in `kernel/private/classes/cronjobs/`
([Cronjobs](../../standards/architektur/kernel-architektur.md#cronjobs)).

## Teile, die die Site braucht

Zu den mitgelieferten Teilen gehören unter anderem `frequent` (Benachrichtigungen, Workflow, Inhaltsaufträge, Audit)
und `infrequent` (Bereinigung von Warenkörben, Linkprüfung, Mail-Einstellungen). Ohne `frequent` werden keine
Benachrichtigungen versendet, und Inhalte, die in einem verzögerten Workflow warten, werden nie veröffentlicht. Planen
Sie diese Teile in jeder Installation ein ([Cronjob-Leitfaden](../../anleitungen/cronjobs.md)).

## Einen eigenen Teil hinzufügen

```ini
# extension/expdemo/settings/cronjob.ini.append.php
<?php /* #?ini charset="utf-8"?
[CronjobSettings]
ExtensionDirectories[]=expdemo

[CronjobPart-expdemo]
Scripts[]=expdemo_import.php
*/ ?>
```

Das Skript liegt in `extension/expdemo/cronjobs/expdemo_import.php` ([Ein Cronjob-Skript schreiben](../ersteller/anwendungsentwickler/einen-cronjob-teil-schreiben.md)).
Ein eigener Teil lässt sich unabhängig von denen des Kernels einplanen.

## Ausführen und planen

```bash
php runcronjobs.php --list --allow-root-user
php runcronjobs.php -s site expdemo --allow-root-user
php bin/php/console cron:expdemo -s site
```

```text
*/5 * * * *  cd /path/to/installation && php runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1
15  * * * *  cd /path/to/installation && php runcronjobs.php -q -s site expdemo >> var/log/cron-expdemo.log 2>&1
```

Führen Sie die Skripte als Benutzer des Webservers aus, niemals als root. `-s <siteaccess>` ist wichtig, wenn Ihre
Erweiterung nur in einigen Siteaccesses aktiv ist. **Setup > Cronjobs** schlägt passende Crontab-Zeilen vor.

## Sperren, Pausen und Überwachung

- Von einem Cronjob-Skript läuft immer nur eine Instanz gleichzeitig; mit `MaxScriptExecutionTime` kann eine neue
  Instanz eine hängengebliebene ablösen.
- Teile lassen sich unter **Setup > Cronjobs** pausieren; ein pausierter Teil wird übersprungen, gleich wer ihn startet
  ([Cronjob-Teile pausieren](../../funktionen/6.0/cronjobs-pausieren.md)).
- **Setup > Cronjobs** zeigt jeden Teil mit seinen letzten Läufen, seiner Ausgabe und seinen Problemen ([Cronjob-Konsole](../../funktionen/6.0/cronjobs-in-der-konsole.md)).
- Schreiben Sie Skripte so, dass sie gefahrlos zweimal laufen und unterbrochen werden können.

Velocity hat einen eigenen Scheduler für die Handler seiner Anwendungsschicht; er führt die Cronjob-Skripte von
Exponential nicht aus, behalten Sie für diese also die System-Crontab bei ([Velocity-Scheduler](../../funktionen/6.0/velocity-zeitplaner.md)).

## Im Vergleich mit anderen Frameworks

| Framework | Konzept | Exponential |
|---|---|---|
| Laravel | `schedule:run` jede Minute, Zeitplan im Code | eine Crontab-Zeile pro Teil, Teile in INI |
| Symfony | Scheduler-Komponente, wiederkehrende Nachrichten | Cronjob-Teile; wiederkehrende Aufträge können expqueue-Jobs einreihen |
| Django, Celery | Management-Befehle aus Cron, Celery beat | Cronjob-Teile |
| Rails | whenever, wiederkehrende Aufgaben in Solid Queue | Cronjob-Teile |
| Spring | `@Scheduled` | Cronjob-Teile |

## Verwandte Seiten

- [Warteschlangen und Hintergrundjobs](warteschlangen-und-hintergrundjobs.md)
- [Filter für Cronjob-Teile](../../funktionen/6.0/filter-fuer-cronjob-teile.md)
- [Ausführbare Befehle, Cronjob-Teile und Views](../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
