---
title: Cron
audience: dxp-developer
category: dxp-development
order: 33
summary: Der Cron-Runner der DXP (ibexa/cron) - die eine Crontab-Zeile, die jeden geplanten Job ausführt, welcher Benutzer sie ausführt, Protokollierung und was dort läuft und was nicht (mit der DXP wird kein Scheduler für das Veröffentlichen ausgeliefert).
keywords: Cron, ibexa/cron, ibexa:cron:run, Crontab, geplante Jobs, www-data, Log, Wartung, Scheduler, cron, scheduled jobs, maintenance
type: help
---
# Cron

Diese Seite richtet sich an Entwickler und Administratoren, die die geplanten Jobs eines DXP-Projekts einrichten. Die DXP
liefert `ibexa/cron` mit: Ein Konsolenbefehl führt jeden Job aus, den Bundles registriert haben.

## Inhalt

1. [Die Crontab-Zeile](#die-crontab-zeile)
2. [Welcher Benutzer](#welcher-benutzer)
3. [Was dort läuft](#was-dort-läuft)
4. [Einen eigenen Job hinzufügen](#einen-eigenen-job-hinzufügen)
5. [Prüfen, dass es funktioniert](#prüfen-dass-es-funktioniert)

## Die Crontab-Zeile

Aus der Anleitung des Skeletons (Abschnitt 17), alle fünf Minuten:

```bash
*/5 * * * * /usr/bin/php /var/www/exponential/bin/console ibexa:cron:run --env=prod >> /var/log/exponential-cron.log 2>&1
```

Der Befehl trägt noch seinen Upstream-Namen; es gibt keinen Alias `exponential:` dafür
([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)). `--quiet` unterdrückt seine Ausgabe.

## Welcher Benutzer

Tragen Sie ihn in die Crontab des Webserver-Benutzers ein (`crontab -e -u www-data`), damit Dateien, die er schreibt,
demselben Benutzer gehören wie die Dateien der Webanfragen ([Dateiberechtigungen](dateiberechtigungen.md)).

## Was dort läuft

Was immer die installierten Bundles als Cron-Jobs registriert haben. Die DXP enthält **keinen** Scheduler für das
Veröffentlichen; ab Werk gibt es also keinen Job "zu einem Datum veröffentlichen"
([Was die DXP mitliefert](was-die-dxp-mitliefert-und-was-nicht.md#was-sie-nicht-mitliefert)). Wartung, die Sie selbst
einplanen möchten, als eigene Crontab-Zeilen:

| Job | Befehl |
|---|---|
| Alte Versionen aufräumen (nachts, den Wert für `--keep` zuerst testen) | `exponential:content:cleanup-versions --keep=5` |
| Externe Links prüfen | `exponential:check-urls` |
| Eingereihte Nachrichten verarbeiten, wenn Sie einen asynchronen Transport verwenden | `messenger:consume ...` unter einem Prozessmanager ([Messenger](messenger-und-benachrichtigungen.md)) |

## Einen eigenen Job hinzufügen

Schreiben Sie einen Symfony-Konsolenbefehl in `src/Command/` und fügen Sie dafür eine Crontab-Zeile hinzu, oder
registrieren Sie ihn beim Cron-Bundle, wenn er innerhalb von `ibexa:cron:run` laufen soll. Halten Sie jeden Job
idempotent: Er muss sicher sein, wenn er zweimal läuft.

## Prüfen, dass es funktioniert

```bash
sudo -u www-data php bin/console ibexa:cron:run --env=prod -vv   # run once by hand
tail -n 50 /var/log/exponential-cron.log
```

## Verwandte Seiten

- [Messenger und Benachrichtigungen](messenger-und-benachrichtigungen.md)
- [Befehle für den Alltag](befehle-fuer-den-alltag.md)
- [DXP-Skeleton, im Alltag](../../funktionen/6.0/plattform-dxp-skeleton.md#im-alltag)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
