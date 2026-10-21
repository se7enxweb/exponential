---
title: Cronjobs für Builder
audience: developer, designer, app-creator, admin
category: extension-development
order: 200
summary: Was zeitgesteuert läuft - Cronjob-Teile und Skripte in cronjob.ini, runcronjobs.php, die Seite Setup > Cronjobs und die Crontab-Zeilen - und wie ein Builder ein vorhandenes Skript einem Teil hinzufügt oder einen Teil plant.
keywords: Cronjob, cronjob.ini, CronjobPart, Cronjob-Teil, frequent, infrequent, runcronjobs.php, crontab, workflow.php, notification.php, Zeitplan, Setup Cronjobs, --list, --script, schedule
type: help
---
# Cronjobs für Builder

Teil 2, Kapitel 20. Manche Arbeit geschieht außerhalb einer Seitenanfrage: Workflows fortsetzen, Benachrichtigungen
senden, Entwürfe aufräumen, Logs rotieren. Exponential fasst solche Skripte in **Cronjob-Teilen** zusammen und führt sie
mit `runcronjobs.php` aus, das von der Crontab des Systems gestartet wird. Dieses Kapitel ist die Sicht des Builders;
wie Sie ein eigenes Skript schreiben, steht in [Cronjob-Teile](../teil-3-erweiterungsentwickler/cronjob-teile.md) in Teil 3,
die vollständige Anleitung ist [Cronjobs](../../../anleitungen/cronjobs.md).

## Teile und Skripte

`settings/cronjob.ini` legt sie fest. Die Skripte des **globalen Teils** stehen in `[CronjobSettings] Scripts[]`; jeder
benannte Teil ist ein Block `[CronjobPart-<name>]` mit eigenem `Scripts[]`. Zu den mitgelieferten Teilen gehören:

| Teil | Skripte (mitgeliefert) | Läuft |
|---|---|---|
| global | `unpublish.php`, `rssimport.php`, `indexcontent.php`, `hide.php`, `internal_drafts_cleanup.php` | üblich ist jede Viertelstunde |
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | alle paar Minuten |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | stündlich |
| `contentcheck`, `contentjobs`, `audit`, `logs` | je eine Aufgabe | nach Bedarf |

Skripte werden in den Verzeichnissen von `[CronjobSettings] ScriptDirectories[]` gesucht (dem `cronjobs/` des Kernels)
und in denen aktiver Erweiterungen, die in `ExtensionDirectories[]` aufgeführt sind.

## Ausführen

```bash
php runcronjobs.php --list                        # every part and its scripts
php runcronjobs.php -s site frequent              # one part, for the siteaccess "site"
php runcronjobs.php -s site --script=workflow.php # one script
php runcronjobs.php -s site                       # the global part
```

`-q` läuft ohne Ausgabe. Führen Sie Cronjobs als der Benutzer aus, unter dem der Webserver läuft, damit die Dateien, die
sie schreiben, für die Website schreibbar bleiben.

## Die Seite in der Administration

**Setup > Cronjobs** listet die Teile mit ihren Skripten, führt einen Teil oder ein Skript aus, während Sie die Ausgabe
verfolgen, zeigt die letzten Läufe und Probleme und schlägt Crontab-Zeilen vor, die mit dem Pfad und dem PHP-Programm
Ihrer Installation geschrieben sind, zum Beispiel:

```text
*/5 * * * * cd /path/to/installation && php runcronjobs.php -q -s site frequent >/dev/null 2>&1
```

In die Crontab wird nichts für Sie eingetragen: Kopieren Sie die Zeilen in die Crontab des Benutzers der Website
(`crontab -e`). Den Zeitplan, den die Seite vorschlägt, können Sie je Teil mit `[AdminSettings] CrontabSchedule_<part>`
in Ihrer `cronjob.ini.append.php` festlegen.

## Was ein Builder ändert

**Ein vorhandenes Skript einem Teil hinzufügen** (zum Beispiel ein Aufräumskript, das eine Erweiterung mitbringt, das
aber kein Teil ausführt):

```ini
# extension/mysite_settings/settings/cronjob.ini.append.php
[CronjobPart-infrequent]
Scripts[]=session_gc.php
```

**Ein eigener Teil**, nachts geplant:

```ini
[CronjobPart-nightly]
Scripts[]=internal_drafts_cleanup.php

[AdminSettings]
CrontabSchedule_nightly=30 3 * * *
```

**Während eines Releases anhalten**: **Setup > Cronjobs** hält einen Teil (**Anhalten** auf seiner Karte) oder alle Teile
an, bis jemand sie fortsetzt, und `runcronjobs.php` überspringt sie dann (`[PauseSettings] Pause=disabled` schaltet die
Prüfung ab); siehe [Cronjobs anhalten](../../../../eng-us/features/6.0/cronjob-pause.md).

## Prüfungen

| Symptom | Prüfung |
|---|---|
| Artikel warten ewig auf Freigabe, Benachrichtigungen kommen nie an | `frequent` steht nicht in der Crontab |
| Ein Skript wird aufgeführt, läuft aber nie | es gehört zu keinem Teil, oder sein Teil ist nicht geplant |
| Ein Skript erscheint in `--list` dreimal | `ScriptDirectories[]` nennt dasselbe Verzeichnis mehrfach; für `--list` harmlos |
| Von Cron geschriebene Dateien kann die Website nicht ändern | Cron läuft als ein anderer Benutzer |

## Verwandte Seiten

- Zurück: [Workflows für Builder](workflows-fuer-builder.md)
- Weiter: [Eine Website bauen, ohne Code zu schreiben](eine-website-ohne-code-bauen.md)
- [Cronjobs](../../../anleitungen/cronjobs.md), [Cronjobs auf der Konsole](../../../../eng-us/features/6.0/cronjobs-console.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
