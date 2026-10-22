---
title: Integrationen überwachen
audience: integrator, admin, hosting
category: integration
order: 390
summary: Wissen, dass eine Integration funktioniert, bevor der Kunde merkt, dass sie es nicht tut - was Sie beobachten (Läufe, Aktualität, Rückstand, Fehler, Zugangsdaten), die Signale, die Exponential liefert (Zusammenfassungszeilen, Cronjob-Seiten, der Audit-Trail, der Protokollbetrachter, Systeminformationen), Alarmregeln und eine Vorlage für einen Überwachungsplan.
keywords: Überwachung, Monitoring, Alarmierung, Health Check, Aktualität, Rückstand, Outbox, Dead Letter, Cronjob, Zusammenfassungszeile, Protokollbetrachter, exp:logs, Audit, exp:audit, Systeminformationen, systeminfo, Ablauf von API-Schlüsseln, Verfügbarkeit, SLA, monitoring, alerting, health check, freshness, backlog, summary line, log viewer, system information, API key expiry, uptime
type: help
---
# Integrationen überwachen

Diese Seite ist für Betreiber und Integratoren, die Integrationen am Laufen halten. Eine Integration fällt meist still
aus: Der Cronjob hört auf, ein Schlüssel läuft ab, das andere System ändert ein Feld, und die Website zeigt einfach alte
Daten. Überwachung macht aus diesen stillen Ausfällen Alarme. Die Serverseite der Überwachung steht in
[Überblick über die Überwachung](../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md) und
[Health Checks und Verfügbarkeit](../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md).

## Inhalt

1. [Was Sie beobachten](#was-sie-beobachten)
2. [Signale, die Exponential liefert](#signale-die-exponential-liefert)
3. [Einzurichtende Prüfungen](#einzurichtende-prüfungen)
4. [Alarme, die helfen](#alarme-die-helfen)
5. [Ein Überwachungsplan](#ein-überwachungsplan)

## Was Sie beobachten

| Signal | Frage | Beispiel für einen Schwellenwert |
|---|---|---|
| **Lauf hat stattgefunden** | Ist der nächtliche Import gelaufen? | Alarm, wenn 26 Stunden lang keine Zusammenfassungszeile kommt |
| **Lauf war erfolgreich** | Ist er ohne Fehlschläge fertig geworden? | Alarm bei `failed > 0` oder einem Exit-Code ungleich null |
| **Aktualität** | Ist die neueste importierte Änderung aktuell? | Alarm, wenn das neueste `modified` importierter Objekte älter als 2 Tage ist |
| **Rückstand** | Wächst die Outbox? | Alarm bei mehr als 500 ungesendeten Zeilen oder wenn die älteste ungesendete älter als 1 Stunde ist |
| **Dead Letters** | Hat etwas aufgegeben? | Alarm bei jedem |
| **Fehler** | Neue Einträge in `error.log`, die Ihre Erweiterung nennen | Alarm nach Rate, nicht bei jeder Zeile |
| **Zugangsdaten** | Läuft bald ein Schlüssel ab? | Warnung 14 Tage vorher; Setup > oAuth-Verwaltung > API-Schlüssel filtert „expiring within seven days“ (läuft innerhalb von sieben Tagen ab) |
| **Menge** | Ist die heutige Anzahl von Datensätzen plausibel? | Warnung, wenn sie um mehr als einen festgelegten Anteil vom Durchschnitt abweicht |

## Signale, die Exponential liefert

| Signal | Wo |
|---|---|
| Ihre Zusammenfassungszeilen | `var/log/<your log>.log` (`eZLog::write()`), durchsuchbar mit `php bin/php/console exp:logs --tail=... --search=...` |
| Cronjob-Läufe und ihre Ausgabe | **Setup > Cronjobs**: letzte Läufe, Probleme, die Ausgabe eines laufenden Teils ([Cronjobs](../../anleitungen/cronjobs.md)) |
| Fehler mit einer Referenz | `error.log`, der Protokollbetrachter unter **Setup > Logdateien**, `exp:logs --reference=ERR-...` |
| Wartende Workflow-Prozesse | **Setup > Workflow-Prozesse**; eine wachsende Liste bedeutet, dass der Workflow-Cronjob nicht läuft |
| Nutzung und Abweisung von API-Schlüsseln | Audit-Ereignisse `access.apikey.use.failed` mit Gründen; `php bin/php/console exp:audit tail --channel=access` |
| Audit-Senken | `php bin/php/console exp:audit sinks list`: bereit oder nicht, wie viel zwischengespeichert ist |
| Job-Warteschlange | **Setup > Job queue** und `php bin/php/console ext:expqueue:status`, wenn `expqueue` verwendet wird |
| Zustand der Installation | **Setup > Systeminformationen** und `php bin/php/systeminfo.php --json` ([Systeminformationen](../../anleitungen/systeminformationen.md)) |
| Wartungsmodus | `php bin/php/maintenance.php status` |

## Einzurichtende Prüfungen

Ein kleines Prüfskript Ihrer Erweiterung, vom Überwachungssystem ausgeführt (oder von Cron, mit per E-Mail an die
Betreiber geschickter Ausgabe), macht aus diesen Signalen einen Status:

```php
<?php
// extension/myintegration/bin/php/check.php: exit 0 OK, 1 WARNING, 2 CRITICAL; one line of output
// (bootstrap with eZScript as in the CLI chapter, then:)
$db = eZDB::instance();
$pending = $db->arrayQuery( 'SELECT COUNT(*) AS n, MIN(created) AS oldest FROM myintegration_outbox WHERE sent = 0' );
$n = (int)$pending[0]['n'];
$age = $pending[0]['oldest'] ? time() - (int)$pending[0]['oldest'] : 0;
$status = ( $n > 500 || $age > 3600 ) ? 2 : ( $n > 100 ? 1 : 0 );
$cli->output( array( 'OK', 'WARNING', 'CRITICAL' )[$status] . " outbox pending=$n oldest_age={$age}s" );
$script->shutdown( $status );
```

Stellen Sie dieselbe Prüfung nur dann über einen geschützten Endpunkt bereit, wenn das Überwachungssystem keine Befehle
auf dem Server ausführen kann.

## Alarme, die helfen

- Sagen Sie, **was** aufgehört hat und **seit wann**, und verlinken Sie den Abschnitt des Runbooks
  („Produktimport: kein Lauf seit 02:15; siehe Runbook 3.2“).
- Alarmieren Sie die Personen, die handeln können: den Betreiber bei einem angehaltenen Cronjob, das ERP-Team des
  Kunden bei einer defekten Quelldatei.
- Vermeiden Sie Alarmfluten: ein Alarm pro Zustand, in langsamem Rhythmus wiederholt, automatisch aufgehoben, wenn der
  Zustand endet.
- Halten Sie Warnungen aus der Nacht heraus; wecken Sie nur für das, was nicht bis zum Morgen warten kann.

## Ein Überwachungsplan

```text
Integration: ERP product import                        Owner: ops@example.com
Check                     How                                  Warning        Critical       Who
Run happened              summary line in myintegration.log    > 25 h         > 26 h         operator
Run succeeded             exit code / failed=               skipped > 50   failed > 0     operator
Freshness                 newest modified of erp-product-*     > 1 day        > 2 days       customer ERP team
Outbox backlog            check.php                            > 100          > 500 / 1 h    operator
Key expiry                API keys page                        < 14 days      < 3 days       operator
Error rate                error.log lines naming myintegration > 10 / h       > 100 / h      developer
```

Die Spalten bedeuten: Prüfung, Wie, Warnung, Kritisch, Wer; die Zeilen entsprechen der Tabelle unter „Was Sie beobachten“.

## Verwandte Seiten

- [Fehlerbehandlung und Protokollierung](fehlerbehandlung-und-protokollierung.md)
- [Überblick über die Überwachung für Hoster](../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md)
- [Kennzahlen aus Protokollen und dem Audit](../fuehrung/kennzahlen-aus-logs-und-audit.md)
- [Übergabe einer Integration](uebergabe-einer-integration.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
