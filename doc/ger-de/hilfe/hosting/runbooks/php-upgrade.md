---
title: "Runbook: PHP-Upgrade"
audience: hosting, admin
category: hosting
order: 70
summary: Ein geplanter Wechsel einer Exponential-Website auf eine neuere PHP-Version 8.x - Unterstützung und Erweiterungen auf einer Staging-Kopie prüfen, das Web-PHP und das PHP der Kommandozeile gemeinsam umstellen, PHP-FPM neu laden oder Velocity neu starten, Caches leeren, prüfen und einen Weg zurück behalten.
keywords: Runbook, PHP-Upgrade, PHP 8.0, 8.1, 8.2, 8.3, 8.4, 8.5, PHP-FPM, Velocity, Erweiterungen, OPcache, PHP für Cron, Plesk, Staging, Rückfall, Deprecations, exp:velocity ext check, runbook, PHP upgrade, extensions, cron PHP, staging, rollback, deprecations
type: help
---
# Runbook: PHP-Upgrade

Für Systemadministratoren, die Kunden-Websites auf eine neuere PHP-Version umstellen, eine Website nach der anderen oder einen ganzen
Server. Ein PHP-Upgrade ist geplante Arbeit, kein Vorfall, aber es ist die Änderung, die am ehesten den eigenen Code eines Kunden
kaputtmacht; behandeln Sie es mit einem Runbook.

## Inhalt

1. [Was unterstützt wird](#was-unterstützt-wird)
2. [Vorher: auf einer Staging-Kopie](#vorher-auf-einer-staging-kopie)
3. [Die Umstellung](#die-umstellung)
4. [Prüfen](#prüfen)
5. [Rückfall](#rückfall)

## Was unterstützt wird

| PHP | Exponential 6.0 | Quelle |
|---|---|---|
| 8.0 | die älteste unterstützte; Velocity läuft darauf ab seinem Release 0.0.4.54 | [Kapitel 2](../../../installation/02-voraussetzungen.md) |
| 8.1 bis 8.4 | unterstützt, einschließlich Velocity | Kapitel 2 |
| 8.5 | unterstützt | Kapitel 2 |

Die CI des Projekts führt die Testsuite auf PHP 8.1 bis 8.5 aus und prüft, dass 8.0 noch startet
([Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md#php-versionen)). Aktualisieren Sie Exponential vor
einem PHP-Upgrade auf das neueste 6.0.x, damit bereits gemachte Korrekturen für PHP vorhanden sind.

## Vorher: auf einer Staging-Kopie

1. Kopieren Sie die Website auf eine Staging-Installation, die das neue PHP ausführt
   ([Eine Website zwischen Hostern umziehen](../migrationen/eine-website-zwischen-hostern-umziehen.md)).
2. Installieren Sie die PHP-Erweiterungen, die die Website für die neue Version braucht; die Systemprüfung des Setup-Assistenten oder
   der Schritt `SystemCheck` von `exp:install` listet fehlende auf ([PHP-Versionen und Erweiterungen](../grundlagen/php-versionen-und-erweiterungen.md)).
   Für Velocity:

   ```bash
   php bin/php/console exp:velocity ext check --allow-root-user
   ```

3. Übernehmen Sie die `php.ini`-Einstellungen: `memory_limit`, `max_execution_time`, `date.timezone`, Upload-Grenzen, OPcache
   ([PHP-Einstellungen für Exponential](../grundlagen/php-einstellungen-fuer-exponential.md), [OPcache](../leistung/opcache.md)).
4. Lassen Sie die Website laufen, klicken Sie sich durch die wichtigsten Seiten und die Administration, veröffentlichen, laden hoch,
   suchen. Beobachten Sie `var/log/error.log` und `warning.log` auf Deprecations aus den eigenen Erweiterungen des Kunden.
5. Führen Sie davor und danach einen Benchmark aus, wenn die Leistung für den Tarif zählt
   ([Benchmark einer gehosteten Website](../leistung/benchmark-einer-gehosteten-website.md)).
6. Sagen Sie dem Kunden, was Sie in seinem eigenen Code gefunden haben; es zu beheben, ist meist sein Teil
   ([Support-Stufen](../geschaeft/support-stufen-fuer-exponential-hosting.md)).

## Die Umstellung

1. Kündigen Sie das Fenster an.
2. Stellen Sie das **Web**-PHP der Website um:
   - PHP-FPM: Richten Sie die Website auf den Pool der neuen Version (in einem Panel: die Einstellung der PHP-Version der Website) und laden
     Sie den PHP-FPM-Dienst dieser Version neu. Auf Plesk ist der Dienst `plesk-php<XY>-fpm`, nicht `php-fpm`
     ([11.6](../../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand)).
   - Velocity: Der Server läuft auf dem PHP der Kommandozeile, das ihn startet; starten Sie ihn mit der neuen Binärdatei (siehe
     [Velocity für Hoster](../server/velocity-fuer-hoster.md)).
3. Stellen Sie das PHP der **Kommandozeile** um, das die Cronjobs und Skripte der Website nutzen. Verwenden Sie dieselbe PHP-Binärdatei,
   die der Webserver nutzt (`/opt/.../php` auf Hosts mit mehreren PHP-Versionen); `php -v` in der Cron-Umgebung kann sich von dem Ihrer
   Shell unterscheiden ([10.3](../../../installation/10-nach-der-installation.md#eine-crontab)).
4. Leeren Sie die Caches und erzeugen Sie die Autoloads neu, dann starten Sie neu:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/console exp:velocity deploy --allow-root-user     # under Velocity: every step in order
   ```

## Prüfen

- [ ] `php -v` als Benutzer der Website, in der Cron-Umgebung und im Web (**Setup > Systeminformationen** zeigt das PHP, das die
      Administration ausliefert).
- [ ] Startseite, Administration, Anmeldung, Veröffentlichen, Hochladen, Suchen.
- [ ] Keine neuen Einträge in `error.log`: `./console exp:logs --tail=var/log/error.log --lines=50`.
- [ ] Die Cronjobs liefen mit dem neuen PHP (ihre Logs).
- [ ] OPcache aktiv: `php bin/php/cache.php opcache status --allow-root-user`.

## Rückfall

Behalten Sie die alte PHP-Version installiert, bis die Website für den vereinbarten Zeitraum auf der neuen gelaufen ist. Der Rückfall ist
die Umstellung in umgekehrter Richtung: die PHP-Einstellung der Website zurück, das alte PHP-FPM neu laden oder Velocity mit der alten
Binärdatei starten, Cron zurück, Caches geleert. Kompilierte Caches gehören zu dem PHP, das sie geschrieben hat; leeren Sie sie also auch
auf dem Rückweg.

## Verwandte Seiten

- [PHP-Versionen und Erweiterungen](../grundlagen/php-versionen-und-erweiterungen.md)
- [PHP-Auswahl und FPM-Pools in Panels](../control-panels/php-auswahl-und-fpm-pools.md)
- [Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
