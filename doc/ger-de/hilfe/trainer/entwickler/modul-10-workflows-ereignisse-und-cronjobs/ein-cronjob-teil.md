---
title: "Lektion 10.3: Ein Cronjob-Teil"
audience: trainer, developer
category: training
order: 103
summary: Die Teilnehmenden schreiben in ihrer Erweiterung ein Cronjob-Skript, legen es in cronjob.ini in einen eigenen Teil, führen es mit runcronjobs.php und über die Konsole als cron:<part> aus und finden es unter Setup > Cronjobs. 45 Minuten.
keywords: entwickler, cronjob, cronjob-teil, cronjob.ini, CronjobSettings, ExtensionDirectories, CronjobPart, Scripts, runcronjobs.php, cron:-namespace, isQuiet, cli, @description, Setup Cronjobs, crontab, sperre, developers course, cronjob part, cron: namespace, lock
type: help
---
# Lektion 10.3: Ein Cronjob-Teil

Arbeit, die regelmäßig und ohne Besucher geschehen soll, läuft als Cronjob: Aufräumarbeiten, Importe, Berichte. In
dieser Lektion schreiben die Teilnehmenden ein Cronjob-Skript, das alte Zeilen der Grußtabelle aus Lektion 9.3
entfernt, legen es in einen eigenen Teil, führen es aus der Shell und über die Konsole aus und finden es auf der
Cronjob-Seite der Administration.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 10: Workflows, Ereignisse und Cronjobs](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 9.3](../modul-09-die-konsole-und-persistente-objekte/persistente-objekte.md): die Tabelle und die Klasse `mysiteGreeting` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- ein Cronjob-Skript schreiben, das die vom Runner gesetzten Variablen `$cli` und `$isQuiet` verwendet;
- mit `[CronjobPart-<name>] Scripts[]` einen Teil hinzufügen und die Erweiterung in `[CronjobSettings]
  ExtensionDirectories[]` registrieren;
- einen Teil mit `php runcronjobs.php -s <sa> <part>`, ein einzelnes Skript mit `--script=` und beides über die
  Konsole ausführen;
- das Skript mit `@description` für die Konsole und die Cronjob-Seite beschreiben;
- die Sperre erklären, die jedes Skript hält, während es läuft.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Cronjobs: ausführen, planen und verfolgen](../../../../anleitungen/cronjobs.md) und
[Exponential Console, Cronjob-Teile](../../../../bc/6.0/konsole.md#cronjob-teile-und-die-crontab-hinzugefügt-im-juni-2026).

1. **Skripte und Teile** (4 Minuten). Ein Skript ist eine PHP-Datei in `cronjobs/` der Installation oder einer
   Erweiterung aus `[CronjobSettings] ExtensionDirectories[]`. Ein Teil fasst Skripte mit einem gemeinsamen Zeitplan
   zusammen: `[CronjobPart-frequent]` führt `notification.php`, `workflow.php` und weitere aus. Die Skripte aus
   `[CronjobSettings] Scripts[]` bilden den Standardteil, angezeigt als **global**.
2. **Das Skript** (3 Minuten). Der Runner stellt `$cli`, `$isQuiet` und die Datenbank bereit; das Skript erledigt
   seine Aufgabe und gibt nur dann etwas aus, wenn es nicht still sein soll. Eine `@description` im Docblock benennt
   es in der Konsole und auf der Cronjob-Seite.
3. **Ausführen** (3 Minuten). `php runcronjobs.php -s <sa> <part>` führt einen Teil aus; `--script=<file>` führt ein
   Skript aus; `php bin/php/console cron:<part>` tut dasselbe über die Konsole.
4. **Sperren** (2 Minuten). Jedes Skript hält eine eigene Sperre, während es läuft; ein zweiter Lauf gibt „Cronjob
   part locked by other process“ aus und überspringt es.
5. **Planen** (3 Minuten). **Setup** > **Cronjobs** zeigt die Teile, führt sie aus und schlägt Crontab-Zeilen vor;
   `php bin/php/console crontab:list` zeigt, was geplant ist.

### Häufige Fragen

**Welchen Siteaccess verwendet ein Cronjob?** Den mit `-s` angegebenen; ohne ihn den Standard-Siteaccess.

**Kann ein Cronjob eine lange Aufgabe ausführen?** Ja, aber halten Sie jeden Lauf kurz und wiederaufnehmbar:
Arbeiten Sie in Stapeln und lassen Sie den nächsten Lauf weitermachen, wie es die Content-Jobs tun.

**Wie teste ich ein Skript, bevor ich es plane?** Führen Sie es mit `--script=` aus der Shell oder mit der
Ausführen-Schaltfläche der Seite aus und lesen Sie die Ausgabe und die Logs.

### Stolperfallen

- **Als root ausführen.** Dateien, die ein Cronjob als root schreibt, sind für die Website womöglich nicht
  beschreibbar. Führen Sie ihn als Benutzer des Webservers aus.
- **Ein Teil, den niemand plant.** Ein Teil, der nicht in der Crontab steht, läuft nie. Prüfen Sie
  **Setup** > **Cronjobs**.
- **Löschen ohne Grenze.** Eine Aufräumarbeit löscht in Stapeln und nach einer klaren Regel (hier: älter als 30 Tage).

## Praktische Übung

Auf der Entwicklungsinstallation, in `extension/mysite_hello`, mit der Tabelle `mysite_greeting`.

1. Legen Sie `cronjobs/greeting_cleanup.php` an:

   ```php
   <?php
   /**
    * Cronjob greeting_cleanup.php (part "mysite")
    *
    * @description Remove stored greetings older than 30 days
    */
   $limit = time() - 30 * 86400;
   $db = eZDB::instance();
   $rows = $db->arrayQuery( 'SELECT COUNT(*) AS total FROM mysite_greeting WHERE created < ' . (int)$limit );
   $db->query( 'DELETE FROM mysite_greeting WHERE created < ' . (int)$limit );
   if ( !$isQuiet )
   {
       $cli->output( 'Removed ' . (int)$rows[0]['total'] . ' greetings older than 30 days' );
   }
   ```

2. Registrieren Sie die Cronjobs der Erweiterung und den Teil:

   ```bash
   php bin/php/console exp:ini add cronjob.ini/CronjobSettings/ExtensionDirectories mysite_hello extension:mysite_hello
   php bin/php/console exp:ini add cronjob.ini/CronjobPart-mysite/Scripts greeting_cleanup.php extension:mysite_hello
   ```

3. Prüfen Sie die Datei mit `php -l`.
4. Listen Sie die Cronjob-Teile auf: `php bin/php/console list cron`. Suchen Sie `cron:mysite`.
5. Führen Sie den Teil aus: `php runcronjobs.php -s <sa> mysite`.
6. Führen Sie nur das Skript aus: `php runcronjobs.php -s <sa> --script=greeting_cleanup.php`.
7. Führen Sie es über die Konsole aus: `php bin/php/console cron:mysite -s <sa>`.
8. Öffnen Sie in der Administration **Setup** > **Cronjobs** und suchen Sie den Teil **mysite** und sein Skript.
9. Committen Sie das Skript und die Einstellungsdatei.

## Erwartetes Ergebnis

- Schritt 4 listet `cron:mysite` mit „Remove stored greetings older than 30 days“.
- Die Schritte 5 bis 7 geben „Removed 0 greetings older than 30 days“ aus (die Grüße aus Lektion 9.3 sind neu).
- Schritt 8: die Karte des Teils auf der Cronjob-Seite, noch nicht geplant.
- Der Trainer fragt alle Teilnehmenden, wie der Teil geplant würde.

## Quiz

1. Welche Einstellung lässt den Kernel in einer Erweiterung nach Cronjob-Skripten suchen?
2. Wie führen Sie nur ein Skript eines Teils aus?
   A. `runcronjobs.php <script>` B. `runcronjobs.php --script=<script>` C. `exp:ezexec <script>` D. `cron:<script>`
3. Welche Variablen gibt der Runner einem Cronjob-Skript für die Ausgabe?
4. Was geschieht, wenn dasselbe Skript gestartet wird, während es noch läuft?
5. Wo sehen Sie, ob ein Teil geplant ist?

<details>
<summary>Antworten</summary>

1. `cronjob.ini [CronjobSettings] ExtensionDirectories[]`.
2. **B.** `php runcronjobs.php -s <sa> --script=<script>`.
3. `$cli` für die Ausgabe und `$isQuiet`, um auf Wunsch still zu bleiben.
4. Der zweite Lauf findet die Sperre des Skripts, gibt „Cronjob part locked by other process“ aus und überspringt es.
5. Unter **Setup** > **Cronjobs** oder mit `php bin/php/console crontab:list`.

</details>

## Weiterführende Literatur

- [Cronjobs: ausführen, planen und verfolgen](../../../../anleitungen/cronjobs.md).
- [Die Cronjob-Konsole](../../../../../eng-us/features/6.0/cronjobs-console.md) und
  [Cronjobs pausieren](../../../../../eng-us/features/6.0/cronjob-pause.md).
- [Content-Jobs](../../../../../eng-us/features/6.0/content-jobs.md): große Aufgaben in Stapeln.

## Verwandte Seiten

- [Modul 10: Workflows, Ereignisse und Cronjobs](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
