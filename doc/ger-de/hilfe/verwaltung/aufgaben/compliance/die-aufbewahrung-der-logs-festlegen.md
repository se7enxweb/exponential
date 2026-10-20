---
title: Die Aufbewahrung der Logs festlegen
audience: government, admin, hosting
category: public-sector
order: 330
summary: Schritt für Schritt - den Kernel-Logs von Exponential mit MaxAgeDays je Log eine Altersgrenze geben (404, security und CSP haben schon eine), das Bereinigen mit dem Teil logs der Cronjobs planen und mit einem Probelauf prüfen.
keywords: Aufbewahrung der Logs, MaxAgeDays, LogFileMaxAge, logviewer.ini, RotationSettings, exp:logs prune, Cronjob logs, error.log, auth.log, Speicherbegrenzung, öffentlicher Sektor, log retention, cronjob logs, storage limitation, public sector
type: help
---
# Die Aufbewahrung der Logs festlegen

Für Administratoren. Abgesehen von `404.log` (30 Tage), `security.log` (90) und `csp.log` (30) werden die Kernel-Logs
nur nach Größe rotiert und haben standardmäßig keine Altersgrenze
([Aufbewahrung der Logs](../../../../../eng-us/help/government/privacy/log-retention.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die mit Ihrem Datenschutzbeauftragten und Sicherheitsbeauftragten beschlossenen Zeiträume (lang genug für
  Untersuchungen).
- Die Schlüssel der Logs: siehe **Setup > Logdateien** (der Schlüssel steht im Permalink jedes Logs,
  `setup/logs/view/<key>`), oder `./console exp:logs --archives --allow-root-user`.

## Schritte

1. In `settings/override/logviewer.ini.append.php`:

   ```ini
   [RotationSettings]
   MaxAgeDays[error]=90
   MaxAgeDays[warning]=30
   MaxAgeDays[notice]=30
   MaxAgeDays[auth]=90
   Compress[error]=enabled
   ```

   oder ein Standardwert für jedes Log ohne eigenen Eintrag in `settings/override/site.ini.append.php`:
   `[FileSettings] LogFileMaxAge=90`.
2. Planen Sie das Bereinigen: Fügen Sie den Teil `logs` der Cronjobs der Crontab des Benutzers der Site hinzu, zum
   Beispiel `15 3 * * * cd /path/to/exponential && php runcronjobs.php -q logs`.
3. Leeren Sie den INI-Cache.

## Prüfen, ob es funktioniert hat

```bash
./console exp:logs --archives --allow-root-user          # shows the limits per log
./console exp:logs --prune --dry-run --allow-root-user   # what would go now
```

Nach der ersten Nacht zeigt das Audit `system.log.delete` für bereinigte Archive.

## Verwandte Seiten

- [Aufbewahrung der Logs](../../../../../eng-us/help/government/privacy/log-retention.md)
- [Rotation und Bereinigung der Logs](../../../../../eng-us/features/6.0/log-rotation.md)
- [Aufbewahrung personenbezogener Daten: ein Überblick](../../../../../eng-us/help/government/privacy/retention-overview.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
