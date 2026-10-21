---
title: Die Fehlerübersicht lesen
audience: government, admin, developer
category: public-sector
order: 130
summary: Schritt für Schritt - der tägliche Fünf-Minuten-Blick auf Setup > Logdateien > Fehlerübersicht - was neu ist, was wächst, wie man eine Gruppe öffnet, wann man sie als bekannt markiert und wie man einen echten Fehler an die Entwickler übergibt.
keywords: Fehlerübersicht, tägliche Prüfung, Logs, Setup Logdateien, verschiedene Fehler, Als bekannt markieren, Permalink, Entwickler, Ticket, logsdigest, error digest, daily check, distinct errors, Mark as known, developers
type: help
---
# Die Fehlerübersicht lesen

Diese Aufgabe ist für Administratoren bei der täglichen Prüfung der Logs. Am Ende wissen Sie, ob etwas Neues nicht
stimmt. Die Übersicht gruppiert und zählt die verschiedenen Fehler und Warnungen der letzten 24 Stunden jedes
Kernel-Logs ([Fehlerübersicht und Zustand der Logs](../../../../../eng-us/help/government/operations/monitoring/error-digest-and-log-health.md)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Entscheiden, was eine Gruppe bedeutet](#entscheiden-was-eine-gruppe-bedeutet)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `setup/logs` (siehe [Zugriff auf Logs ohne Administratorrechte geben](../personen-und-berechtigungen/zugriff-auf-logs-ohne-administratorrechte-geben.md)).

## Schritte

1. **Setup > Logdateien**, dann **Fehlerübersicht** (`/setup/logsdigest`).
2. Lesen Sie die Tabelle jedes Logs, die häufigsten zuerst: die normalisierte Meldung, Anzahl, schwerste Stufe, zuletzt
   gesehen.
3. Gruppen, die Sie früher akzeptiert haben, sind ausgeblendet; **Bekannte Meldungen zeigen** listet sie.
4. Öffnen Sie bei einer Gruppe, die Sie nicht kennen, ihren Link: das auf sie gefilterte Log. Öffnen Sie eine Zeile, um
   die ganze Anfrage und die Audit-Einträge derselben Minute zu sehen.
5. Für ein anderes Zeitfenster: `/setup/logsdigest/(hours)/72` (1 bis 168 Stunden); für ein Log: `/setup/logsdigest/error`.

## Entscheiden, was eine Gruppe bedeutet

| Gruppe | Maßnahme |
|---|---|
| Neu seit gestern, aus einem Deployment | mit dem Entwickler der Änderung klären; [Ein fehlerhaftes Deployment zurückrollen](../../../../../eng-us/help/government/operations/incidents/broken-deploy-rollback.md), wenn es Besuchern schadet |
| Eine bekannte harmlose Meldung (der Hinweis einer alten Erweiterung) | **Als bekannt markieren** und den Grund festhalten |
| Wächst schnell | die Ursache finden, bevor das Log den Datenträger füllt |
| „Nicht gefundene Anfragen (404)“ nach Grund gruppiert | defekte Links: Links korrigieren oder URL-Aliase hinzufügen |
| „Sicherheit (Anfrageklassifizierung)“ | [Das Sicherheits-Log wöchentlich prüfen](das-sicherheits-log-woechentlich-pruefen.md); sofort, wenn Injections eine 200 erhielten |

Verwenden Sie für ein Ticket an die Entwickler **Permalink kopieren** in der gefilterten Ansicht: Der Link stellt den
Filter wieder her.

## Verwandte Seiten
- [Den Zustand der Logs prüfen](den-zustand-der-logs-pruefen.md)
- [Einen Fehler anhand seiner Referenz finden](einen-fehler-anhand-seiner-referenz-finden.md)
- [Der Log-Viewer](../../../../../eng-us/help/government/operations/monitoring/the-log-viewer.md)
- [Die Funktion Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md#error-digest)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
