---
title: Cronjobs
audience: cms-developer
category: cms-development
order: 36
summary: Für CMS-Entwickler die geplanten Arbeiten, von denen eine Exponential-Website abhängt. Die Seite behandelt Teile und Skripte, was die Standardteile tun, die Planung mit der Crontab, das Ausführen von Teilen in der Shell und der Konsole, die Seite Cronjobs in der Administration und das Hinzufügen eines eigenen Cronjobs.
keywords: Cronjobs, Cron, runcronjobs.php, cronjob.ini, Cronjob-Teil, CronjobPart, frequent, infrequent, globaler Teil, workflow.php, indexcontent.php, notification.php, Crontab, Konsolenbefehl cron, geplante Aufgaben, eigener Cronjob, cronjob part, global part, cron console command, scheduled tasks, custom cronjob
type: help
---
# Cronjobs

Diese Seite richtet sich an Entwickler, die wissen müssen, was im Hintergrund einer Website läuft und wie sie eigene
geplante Arbeiten hinzufügen. Mehrere Funktionen werden nur über Cronjobs abgeschlossen: Freigegebene Inhalte werden
veröffentlicht, verzögert indexierte Inhalte indexiert, Benachrichtigungen versendet, datierte Inhalte verborgen. Eine
Website ohne ihre Crontab sieht gut aus und hört still und leise auf, diese Dinge zu tun.

## Inhalt

1. [Teile und Skripte](#teile-und-skripte)
2. [Was die mitgelieferten Teile tun](#was-die-mitgelieferten-teile-tun)
3. [Teile ausführen](#teile-ausführen)
4. [Planung](#planung)
5. [Die Seite Cronjobs](#die-seite-cronjobs)
6. [Ein eigener Cronjob](#ein-eigener-cronjob)
7. [Prüfungen vor dem Live-Gang](#prüfungen-vor-dem-live-gang)

## Teile und Skripte

Ein **Skript** ist eine PHP-Datei in einem Cronjob-Verzeichnis (`cronjobs/` der Installation oder einer Erweiterung, die
in `[CronjobSettings] ExtensionDirectories[]` genannt ist). Ein **Teil** ist eine Gruppe von Skripten, die zusammen nach
einem Zeitplan laufen, in `cronjob.ini` als `[CronjobPart-<name>] Scripts[]` deklariert. Die Skripte aus
`[CronjobSettings] Scripts[]` bilden den Standardteil, angezeigt als **global**
([Cronjobs, Abschnitt 1](../../anleitungen/cronjobs.md#1-teile-skripte-und-der-globale-teil)).

## Was die mitgelieferten Teile tun

| Teil | Skripte, darunter | Warum es wichtig ist |
|---|---|---|
| global (Standard) | `unpublish.php`, `rssimport.php`, `indexcontent.php`, `hide.php`, `internal_drafts_cleanup.php` | datiertes Entfernen und Verbergen, RSS-Import, verzögertes Indexieren für die Suche |
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | Benachrichtigungen, wartende Workflows (Freigaben), Inhaltsjobs im Hintergrund |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | Warenkörbe des Shops, Prüfen von Links, Aufbewahrung der E-Mail-Einstellungen |
| die anderen Teile | Logs, Cache-Bereinigung, `unlock`, `cleanuprss` und mehr | Pflege |

`settings/cronjob.ini` enthält die vollständige Liste (`infrequent`, `frequent`, `contentcheck`, `contentjobs`, `audit`,
`logs`, `notification`, `mailpreferences`, `mailbounces`, `unlock`, `cluster_maintenance`, `cleanuprss`,
`cache_cleanup`), und Erweiterungen fügen eigene Teile hinzu, etwa für Sitemaps oder Newsletter. So listen Sie auf, was
Ihre Installation hat:

```bash
php runcronjobs.php --list --allow-root-user
```

## Teile ausführen

```bash
php runcronjobs.php -s <siteaccess> frequent --allow-root-user        # one part
php runcronjobs.php -s <siteaccess> --allow-root-user                 # the default part
php runcronjobs.php -s <siteaccess> --script=workflow.php --allow-root-user   # one script
./console list cron                                                    # every part as a console command, cron:<part>
```

Führen Sie sie als der Benutzer aus, unter dem der Webserver läuft, damit Dateien, die die Cronjobs schreiben, für die
Website lesbar bleiben. Jedes Skript nimmt eine eigene Sperre, sodass ein zweiter Lauf desselben Skripts wartet oder
übersprungen wird ([Cronjobs in der Shell ausführen](../../anleitungen/cronjobs.md#13-cronjobs-in-der-shell-ausführen)).

## Planung

Fügen Sie je Teil eine Crontab-Zeile hinzu, vom Hauptverzeichnis der Installation aus:

```text
*/5 * * * *  cd /var/www/example && php runcronjobs.php -s site frequent >/dev/null 2>&1
*/15 * * * * cd /var/www/example && php runcronjobs.php -s site >/dev/null 2>&1
17 * * * *   cd /var/www/example && php runcronjobs.php -s site infrequent >/dev/null 2>&1
```

Das sind die Standardzeitpläne, die die Seite Cronjobs vorschlägt; legen Sie je Teil eigene mit
`cronjob.ini [AdminSettings] CrontabSchedule_<part>` fest
([Planung mit der Crontab](../../anleitungen/cronjobs.md#12-planen-mit-der-crontab)). Hosting-Panels haben eigene
Cron-Bildschirme ([Cron und Shell-Zugang](../hosting/control-panels/cron-und-shell-zugang.md)).

## Die Seite Cronjobs

Die Administration listet jeden Teil und jedes Skript, zeigt, ob die Crontab es einplant, führt einen Teil oder ein
Skript mit Live-Ausgabe aus, bewahrt die letzten Läufe auf, zählt Probleme und schlägt Crontab-Zeilen vor. Verwenden
Sie sie, um ein neues Skript auszuprobieren, bevor Sie es einplanen, und um herauszufinden, warum über Nacht etwas
nicht geschehen ist ([Cronjobs](../../anleitungen/cronjobs.md), [Cronjob-Konsole](../../funktionen/6.0/cronjobs-in-der-konsole.md)).

## Ein eigener Cronjob

1. Schreiben Sie das Skript in `extension/exp_mysite/cronjobs/exp_feed_sync.php`, mit einem `@description`-Tag in
   seinem Doc-Kommentar (Konsole und Seite zeigen ihn an).
2. Deklarieren Sie das Verzeichnis und einen Teil in `extension/exp_mysite/settings/cronjob.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [CronjobSettings]
   ExtensionDirectories[]=exp_mysite

   [CronjobPart-exp_mysite]
   Scripts[]=exp_feed_sync.php
   */ ?>
   ```

3. Leeren Sie den INI-Cache, führen Sie den Teil von Hand aus (`php runcronjobs.php -s <siteaccess> exp_mysite`) und
   planen Sie ihn dann ein.

Ruhiger Modus, Protokollierung und die Stoppschalter stehen unter
[Cronjob-Teile](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cronjob-teile.md) und im Tutorial
[Ein Cronjob-Teil von Hand](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tutorials/von-hand-cronjob-teil.md).
Lange Arbeit gehört in einen Cronjob oder einen Inhaltsjob, nicht in eine Seitenanfrage.

## Prüfungen vor dem Live-Gang

- [ ] Der Teil `frequent` und der Standardteil sind für jeden öffentlichen Siteaccess eingeplant, der sie braucht.
- [ ] Workflows mit Freigaben, verzögertes Indexieren und Benachrichtigungen wurden bei laufendem Cron von Anfang bis
      Ende getestet.
- [ ] Cron läuft als Benutzer des Webservers, und seine Ausgabe wird protokolliert oder an jemanden gemailt, der sie
      liest.
- [ ] Ihre eigenen Teile sind eingeplant und im Projekt dokumentiert.

## Verwandte Seiten

- [Cronjobs für Hoster](../hosting/betrieb/cronjobs.md)
- [Cronjobs für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/cronjobs-fuer-builder.md)
- [Workflows und Auslöser](workflows-und-ausloeser.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
