---
title: Überwachung
audience: government, admin, hosting, project-manager
category: public-sector
order: 10
summary: Übersicht der Seiten zur Überwachung einer Exponential-Website des öffentlichen Sektors - ein Überblick mit einer Routine, der Log-Viewer, Fehlerübersicht und Zustand der Logs, Anfrage- und CSP-Logs, Audit-Warnungen und deren Prüfung, Cronjobs, Systeminformationen, Dateiintegrität, Verfügbarkeitsprüfungen von außen und die Aufbewahrung von Logs.
keywords: Überwachung, Logs, Log-Viewer, Fehlerübersicht, Zustand der Logs, Sicherheitslog, 404-Log, CSP-Berichte, Audit-Warnungen, Audit-Prüfung, Cronjobs, Systeminformationen, Verfügbarkeit, Aufbewahrung, Übersicht, monitoring, logs, log viewer, error digest, log health, security log, CSP reports, audit alerts, audit verification, system information, uptime, retention
type: help
---
# Überwachung

Dieser Ordner richtet sich an interne IT-Teams, Informationssicherheitsbeauftragte und die Verantwortlichen für den
Dienst, die wissen müssen, ob eine öffentliche Website gesund, verfügbar und sicher ist, und dies auch belegen können
müssen. Exponential bringt einen Großteil der Instrumentierung selbst mit (Logs mit Zusammenfassungen,
Anfrageklassifizierung, einen per Hash-Kette gesicherten Audit-Trail mit Warnungen, Zustandsprüfungen); diese Seiten
zeigen, wie Sie sie nutzen und was Sie von außen ergänzen.

## Inhalt
1. [Seiten](#seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Verwandte Seiten](#verwandte-seiten)

## Seiten

| Seite | Beantwortet |
|---|---|
| [Überwachung im Überblick](ueberwachung-im-ueberblick.md) | Welche Frage wo beantwortet wird; Signale für Ihr Überwachungssystem; eine Routine. |
| [Der Log-Viewer](der-log-viewer.md) | Logs lesen, verfolgen und durchsuchen, mit maskierten personenbezogenen Daten. |
| [Fehlerübersicht und Zustand der Logs](fehleruebersicht-und-zustand-der-logs.md) | Die tägliche Zusammenfassung der unterschiedlichen Fehler; Platz, Wachstum und Berechtigungen der Logs. |
| [Anfrage-Logs: 404 und Sicherheit](anfrageprotokolle-404-und-sicherheit.md) | Antworten „nicht gefunden“ und vermutete Angriffe, getrennt von echten Fehlern. |
| [CSP-Berichte](csp-berichte.md) | Berichte zur Content Security Policy sammeln, bevor sie durchgesetzt wird. |
| [Audit-Warnungen](audit-warnungen.md) | Die sieben ausgelieferten Regeln, Empfänger, Feinabstimmung, Weiterleitung an ein Sicherheitsteam. |
| [Prüfung des Audit-Trails](pruefung-des-audit-trails.md) | Belegen, dass die Aufzeichnung unversehrt ist; was zu tun ist, wenn eine Kette bricht. |
| [Überwachung der Cronjobs](ueberwachung-der-cronjobs.md) | Was stehen bleibt, wenn Cron stehen bleibt, und wie Sie es bemerken. |
| [Systeminformationen und Zustandsprüfungen](systeminformationen-und-zustandspruefungen.md) | Die Zustandsprüfungen und der Support-Bericht. |
| [Upgrade-Prüfung als Überwachung](upgrade-pruefung-als-ueberwachung.md) | Geplante Prüfungen der Dateiintegrität gegen das Release-Manifest. |
| [Externe Verfügbarkeitsüberwachung](externe-verfuegbarkeitsueberwachung.md) | Die Website so prüfen, wie Bürgerinnen und Bürger sie erreichen. |
| [Aufbewahrung und Rotation von Logs](aufbewahrung-und-rotation-von-protokollen.md) | Standardfristen und wie Sie eigene festlegen. |

## Lesereihenfolge

1. [Überwachung im Überblick](ueberwachung-im-ueberblick.md).
2. [Externe Verfügbarkeitsüberwachung](externe-verfuegbarkeitsueberwachung.md) und [Systeminformationen und Zustandsprüfungen](systeminformationen-und-zustandspruefungen.md): erreichbar und gesund.
3. [Der Log-Viewer](der-log-viewer.md), [Fehlerübersicht und Zustand der Logs](fehleruebersicht-und-zustand-der-logs.md), [Überwachung der Cronjobs](ueberwachung-der-cronjobs.md): der tägliche Betrieb.
4. Sicherheit: [Anfrage-Logs](anfrageprotokolle-404-und-sicherheit.md), [Audit-Warnungen](audit-warnungen.md), [Prüfung des Audit-Trails](pruefung-des-audit-trails.md), [CSP-Berichte](csp-berichte.md), [Upgrade-Prüfung als Überwachung](upgrade-pruefung-als-ueberwachung.md).
5. [Aufbewahrung und Rotation von Logs](aufbewahrung-und-rotation-von-protokollen.md) gemeinsam mit den Zuständigen für Schriftgut und Datenschutz.

Nach oben: [Exponential im öffentlichen Sektor betreiben](../README.md). Die Einstiegsseite des Bereichs ist
[Verwaltungen: hier beginnen](../../verwaltungen-hier-beginnen.md).

## Verwandte Seiten
- [Vorfälle](../vorfaelle/README.md)
- [Aufgaben im Betrieb](../../../../../eng-us/help/government/tasks/operations/README.md)
- [Überwachung im Überblick (Hosting)](../../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md)
- [Leitfaden zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
