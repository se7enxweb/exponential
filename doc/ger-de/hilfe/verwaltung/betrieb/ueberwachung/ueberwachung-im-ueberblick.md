---
title: Überwachung im Überblick
audience: government, admin, hosting, project-manager
category: public-sector
order: 20
summary: Was Sie auf einer Exponential-Website des öffentlichen Sektors beobachten sollten und woher jedes Signal kommt - Prüfungen von außen, Systeminformationen, Logs und ihre Zusammenfassungen, Anfrage- und CSP-Logs, Audit-Warnungen, Cronjobs und Dateikonsistenz - mit einer täglichen, wöchentlichen und monatlichen Routine für ein internes Team.
keywords: Überwachung, Beobachtbarkeit, Zustandsprüfung, Verfügbarkeit, Logs, Fehlerübersicht, Zustand der Logs, Sicherheitslog, Audit-Warnungen, Cronjobs, Systeminformationen, Routine, Rufbereitschaft, Service-Management, monitoring, observability, health check, uptime, error digest, log health, security log, audit alerts, system information, routine, on-call, service management
type: help
---
# Überwachung im Überblick

Diese Seite richtet sich an interne IT-Teams und die Verantwortlichen für den Dienst, die wissen müssen, dass eine
öffentliche Website erreichbar, gesund und nicht unter Angriff ist, und dies auch belegen können müssen. Sie ordnet
jeder Frage die Stelle in Exponential zu, die sie beantwortet, und schlägt eine Routine vor. Die Sicht des Hostings ist
[Überwachung im Überblick (Hosting)](../../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md).

## Inhalt
1. [Fragen und wo sie beantwortet werden](#fragen-und-wo-sie-beantwortet-werden)
2. [Signale für Ihr Überwachungssystem](#signale-für-ihr-überwachungssystem)
3. [Eine Routine](#eine-routine)
4. [Wer was beobachtet](#wer-was-beobachtet)
5. [Verwandte Seiten](#verwandte-seiten)

## Fragen und wo sie beantwortet werden

| Frage | Wo | Seite |
|---|---|---|
| Ist die Website für Bürgerinnen und Bürger erreichbar? | eine Prüfung der öffentlichen Adresse und der Anmeldeseite von außen | [Externe Verfügbarkeitsüberwachung](externe-verfuegbarkeitsueberwachung.md) |
| Ist die Installation gesund? | **Setup > Systeminformationen**, Zustandsprüfungen; `./console exp:system:info` | [Systeminformationen und Zustandsprüfungen](systeminformationen-und-zustandspruefungen.md) |
| Welche Fehler treten auf? | **Setup > Logdateien**, **Fehlerübersicht**, **Zustand der Logs** | [Der Log-Viewer](der-log-viewer.md), [Fehlerübersicht und Zustand der Logs](fehleruebersicht-und-zustand-der-logs.md) |
| Sondiert uns jemand? | `security.log`, `404.log` | [Anfrage-Logs](anfrageprotokolle-404-und-sicherheit.md) |
| Würde die Content Security Policy etwas kaputt machen? | `csp.log` | [CSP-Berichte](csp-berichte.md) |
| Hat jemand etwas Riskantes getan? | die Audit-Warnungen | [Audit-Warnungen](audit-warnungen.md) |
| Ist die Aufzeichnung unversehrt? | `exp:audit verify` | [Prüfung des Prüfprotokolls](pruefung-des-audit-trails.md) |
| Laufen die geplanten Jobs? | **Setup > Cronjobs**, die Zustandsprüfung der Cronjobs | [Überwachung der Cronjobs](ueberwachung-der-cronjobs.md) |
| Haben sich Dateien unerwartet geändert? | **Setup > Upgrade-Prüfung**; `checkmanifest.php` | [Upgrade-Prüfung als Überwachung](upgrade-pruefung-als-ueberwachung.md) |
| Bleiben die Logs im Rahmen? | **Zustand der Logs**; Rotationseinstellungen | [Aufbewahrung und Rotation von Logs](aufbewahrung-und-rotation-von-protokollen.md) |

## Signale für Ihr Überwachungssystem

Befehle, die mit einem aussagekräftigen Exit-Code oder JSON enden und sich für einen Überwachungsagenten eignen:

| Befehl | Signal |
|---|---|
| `./console exp:system:info --json --allow-root-user` | endet mit 1, wenn eine Zustandsprüfung fehlschlägt |
| `./console exp:audit verify --allow-root-user` | endet mit 1, wenn eine Kette gebrochen ist |
| `php bin/php/checkmanifest.php --all` | endet mit einem Wert ungleich null, wenn Dateien geändert sind oder fehlen |
| `php bin/php/cache.php status --allow-root-user` | endet mit `PASS` oder `FAIL`; `--json` verfügbar |
| `./console exp:logs --search=... --json` | Exit 0 gefunden, 1 nichts gefunden |
| die Syslog-Senke des Audits | Einträge im Syslog, `journalctl -t exponential` mit `Transport=local` |

Die Warnungssenken des Audits (Syslog, ein signierter Webhook, E-Mail) schicken Ihnen Ereignisse zu
([Prüfprotokoll, „Sinks, alerts and archives“](../../../../funktionen/6.0/pruefprotokoll.md#sinks-alarme-und-archive)).

## Eine Routine

| Wann | Was |
|---|---|
| Laufend | Prüfungen der öffentlichen Website und der Anmeldeseite von außen; E-Mails und Syslog der Audit-Warnungen |
| Täglich | **Fehlerübersicht** der letzten 24 Stunden; ausgelöste Audit-Warnungen |
| Wöchentlich | Durchsicht von `security.log` ([Das Sicherheitslog wöchentlich durchsehen](../../aufgaben/betrieb/das-sicherheits-log-woechentlich-pruefen.md)); Läufe der Cronjobs; `exp:audit verify`; Zustandsprüfungen der Systeminformationen |
| Monatlich | Upgrade-Prüfung; Zustand der Logs und Festplatte; Pflege der Konten; Wiederherstellungstest einer Datensicherung, wie es Ihr Plan vorsieht ([Monatliche Betriebsüberprüfung](../../aufgaben/betrieb/monatliche-betriebsueberpruefung.md)) |

Die [wöchentliche Checkliste des Betriebsleitfadens](../../../../anleitungen/betrieb-einer-website.md#10-eine-wöchentliche-checkliste) ist
ein gutes Minimum.

## Wer was beobachtet

Halten Sie schriftlich fest, wer welche Warnung erhält und wer außerhalb der Dienstzeiten handelt. Exponential sendet
Audit-Warnungen an die Empfänger der Regel, sonst an die konfigurierten Gruppen, sonst an die `AdminEmail` der Website;
prüfen Sie das mit `./console exp:audit alerts recipients --allow-root-user`. Rufbereitschaftspläne und Eskalation sind
eine organisatorische Angelegenheit außerhalb der Software.

## Verwandte Seiten
- [Vorfälle](../vorfaelle/README.md)
- [Zustandsprüfungen und Verfügbarkeit (Hosting)](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)
- [Betrieb einer Website](../../../../anleitungen/betrieb-einer-website.md)
- [Kursmodul Logs und Prüfprotokoll](../../../trainer/website-administratoren/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
