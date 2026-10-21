---
title: Eine Veröffentlichung für einen festen Zeitpunkt planen
audience: government, admin, editor
category: public-sector
order: 410
summary: Aufgabe für Administratoren und Redakteure - eine Seite (eine Statistikveröffentlichung, eine Tagesordnung) zu einem festen Datum und einer festen Uhrzeit mit dem Workflow-Ereignis Bis Datum warten veröffentlichen und wissen, wie genau der Zeitpunkt ist.
keywords: zeitgesteuertes Veröffentlichen, Sperrfrist, Bis Datum warten, Workflow, Veröffentlichungszeitpunkt, Cronjob, scheduled publishing, embargo, Wait until date, workflow, release time, publish at, cronjob
type: help
---
# Eine Veröffentlichung für einen festen Zeitpunkt planen

Diese Aufgabe richtet sich an Administratoren (Einrichtung) und Redakteure (jede Veröffentlichung). Hintergrund:
[Zeitgesteuertes Veröffentlichen und Sperrfristen](../../transparenz-und-informationsfreiheit/zeitgesteuertes-veroeffentlichen-und-sperrfristen.md).

## Schritte für Administratoren

1. Fügen Sie der Klasse ein Feld **Datum und Zeit** „Veröffentlichen am“ hinzu.
2. **Setup > Workflows**: ein Workflow mit dem Ereignis **Bis Datum warten**; wählen Sie im Ereignis die **Klasse**,
   **Eigenschaften aktualisieren**, das **Attribut** „Veröffentlichen am“, und fügen Sie die Kombination hinzu.
3. **Setup > Auslöser**: Verbinden Sie ihn mit **Vor dem Veröffentlichen von Inhalten** (kombinieren Sie ihn über einen
   **Multiplexer** mit einem Freigabe-Workflow, falls es einen gibt).
4. Stellen Sie sicher, dass die Cronjob-Gruppe `frequent` oft genug für Ihre gewünschte Genauigkeit läuft.

## Schritte für Redakteure

1. Bereiten Sie die Seite vor; setzen Sie **Veröffentlichen am** auf den Veröffentlichungszeitpunkt.
2. **Zur Veröffentlichung schicken**. Die Version wartet; **Setup > Workflow Prozesse** listet sie.

## Prüfen

Die Seite ist vor dem Zeitpunkt nicht öffentlich, und danach ist sie es nach dem ersten folgenden Lauf des
Workflow-Cronjobs. Prüfen Sie es zum Veröffentlichungszeitpunkt von Hand.

## Probleme

| Problem | Lösung |
|---|---|
| Minuten zu spät veröffentlicht | das Intervall des Cronjobs; führen Sie ihn öfter aus |
| Sofort veröffentlicht | das Datum lag in der Vergangenheit, oder die Kombination aus Klasse und Attribut wurde nicht hinzugefügt |
| Dateien zu früh öffentlich | hängen Sie vor der Veröffentlichung keine Dateien an ein veröffentlichtes Objekt |

## Verwandte Seiten

- [Workflows](../../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
