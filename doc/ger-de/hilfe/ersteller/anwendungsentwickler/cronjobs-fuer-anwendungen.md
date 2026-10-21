---
title: Cronjobs für Anwendungen
audience: app-creator, developer, admin
category: app-creation
order: 280
summary: Die regelmäßige Arbeit einer Anwendung mit Cronjob-Teilen ausführen - wie Teile und Skripte in cronjob.ini definiert werden, sie mit runcronjobs.php und der Konsole starten, die Crontab, Siteaccess-Angaben, Pausieren, Sperren und Überwachung unter Setup > Cronjobs.
keywords: Cronjobs, Cronjob-Teil, Crontab, Siteaccess, Pause, Sperren, Überwachung, Anwendungsentwickler, cronjobs, cronjob part, cronjob.ini, CronjobPart, runcronjobs.php, cron:frequent, crontab, siteaccess, pause, MaxScriptExecutionTime, Setup > Cronjobs, app creator
type: help
---
# Cronjobs für Anwendungen

Diese Seite ist für Anwendungsentwickler, deren Anwendung regelmäßige Arbeit hat: Importe, Erinnerungen, Aufräumarbeiten,
Berichte. Exponential führt solche Arbeit als **Cronjob-Teile** aus: benannte Gruppen von Skripten, die die Crontab des
Systems startet. Die Anleitung für Administratoren ist [Cronjobs](../../../anleitungen/cronjobs.md).

## Inhalt

1. [Teile und Skripte](#teile-und-skripte)
2. [Ihren Teil hinzufügen](#ihren-teil-hinzufügen)
3. [Teile ausführen](#teile-ausführen)
4. [Die Crontab](#die-crontab)
5. [Sperren, Pausen und Zeitlimits](#sperren-pausen-und-zeitlimits)
6. [Sie im Blick behalten](#sie-im-blick-behalten)

## Teile und Skripte

`settings/cronjob.ini`:

| Block | Enthält |
|---|---|
| `[CronjobSettings] Scripts[]` | den globalen Teil (läuft, wenn kein Teil angegeben ist) |
| `[CronjobSettings] ExtensionDirectories[]` | Erweiterungen, die ein Verzeichnis `cronjobs/` haben |
| `[CronjobPart-<name>] Scripts[]` | einen benannten Teil |

Zu den mitgelieferten Teilen gehören `frequent` (Benachrichtigungen, Workflow, Inhalts-Jobs, Audit), `infrequent`
(Aufräumen der Warenkörbe, Linkprüfung, Mail-Einstellungen), `contentjobs`, `audit`, `unlock`, `cleanuprss` und
`cache_cleanup` ([Befehle: Cronjob-Teile](../../../bc/6.0/konsole.md#cronjob-teile-und-die-crontab-hinzugefügt-im-juni-2026)).

## Ihren Teil hinzufügen

```ini
# extension/expdemo/settings/cronjob.ini.append.php
<?php /* #?ini charset="utf-8"?
[CronjobSettings]
ExtensionDirectories[]=expdemo

[CronjobPart-expdemo]
Scripts[]=expdemo_import.php
*/ ?>
```

Das Skript liegt in `extension/expdemo/cronjobs/expdemo_import.php` ([Ein Cronjob-Skript schreiben](einen-cronjob-teil-schreiben.md)).
Fügen Sie es nur dann einem vorhandenen Teil hinzu, wenn es schnell ist und denselben Rhythmus hat; ein eigener Teil
lässt sich getrennt planen.

## Teile ausführen

```bash
php runcronjobs.php --list --allow-root-user
php runcronjobs.php -s site expdemo --allow-root-user
php runcronjobs.php -s site --script=expdemo_import.php --allow-root-user
php bin/php/console cron:expdemo -s site
```

`-s <siteaccess>` ist wichtig, wenn Ihre Erweiterung nur in einigen Siteaccesses aktiv ist (`ActiveAccessExtensions[]`):
Sonst wird das Skript nicht gefunden.

## Die Crontab

```text
*/5 * * * *  cd /path/to/installation && php runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1
15  * * * *  cd /path/to/installation && php runcronjobs.php -q -s site expdemo >> var/log/cron-expdemo.log 2>&1
```

Führen Sie Cron als Benutzer des Webservers aus, niemals als root. `php bin/php/console crontab:list` zeigt die aktuelle
Crontab; **Setup > Cronjobs** schlägt Zeilen vor ([12. Planen mit der Crontab](../../../anleitungen/cronjobs.md#12-planen-mit-der-crontab)).

## Sperren, Pausen und Zeitlimits

- Von einem Cronjob-Skript läuft immer nur eine Instanz gleichzeitig. `MaxScriptExecutionTime` (Standard 43200
  Sekunden) erlaubt einer neuen Instanz, eine hängende zu übernehmen; setzen Sie den Wert für lange Jobs pro Teil.
- Teile lassen sich unter **Setup > Cronjobs** pausieren (`[PauseSettings] Pause=enabled`); ein pausierter Teil wird
  übersprungen, ganz gleich, wer ihn startet ([Cronjob-Teile pausieren](../../../../eng-us/features/6.0/cronjob-pause.md)).
- Machen Sie Skripte wiederholbar: Auf einen abgestürzten Lauf folgt ein weiterer.

## Sie im Blick behalten

**Setup > Cronjobs** zeigt jeden Teil, seine letzten Läufe, Ausgaben und Probleme und ob die Crontab ihn plant
([Cronjob-Konsole](../../../../eng-us/features/6.0/cronjobs-console.md)). Schreiben Sie klare Ausgabezeilen; sie sind das, was ein
Administrator liest, wenn etwas fehlschlägt.

## Verwandte Seiten

- [Ein Cronjob-Skript schreiben](einen-cronjob-teil-schreiben.md)
- [Hintergrundarbeit: Warteschlangen, Cronjobs und Inhalts-Jobs](muster-fuer-hintergrundarbeit.md)
- Cronjob-Beispiele (`doc/eng-us/examples/cronjobs/`)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
