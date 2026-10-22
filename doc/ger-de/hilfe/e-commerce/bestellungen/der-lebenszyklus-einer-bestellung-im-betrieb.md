---
title: Der Lebenszyklus einer Bestellung im Betrieb
audience: ecommerce, customer, businesses
category: e-commerce
order: 51
summary: Das Leben einer Exponential-Bestellung als Ablauf, den Sie steuern können - von der temporären Bestellung bis zum Archiv, die siebzehn ausgelieferten Status nach Phase gruppiert, welche als offen, abgeschlossen oder ohne Umsatz zählen, drei Beispielabläufe und eigene Status.
keywords: Lebenszyklus einer Bestellung, Bestellablauf, Bestellstatus, Pending, Paid, Shipped, Completed, Cancelled, Refunded, abgeschlossen, offene Bestellung, ohne Umsatz, wartet auf Kunden, eigener Status, Status 1000, Ablaufentwurf, order lifecycle, order process, order statuses, finished, open order, no revenue, custom status, process design
type: help
---
# Der Lebenszyklus einer Bestellung im Betrieb

Diese Seite ist für Händler und Shop-Manager, die entwerfen, wie Bestellungen einen Exponential-Shop durchlaufen, und für
Entwickler, die darauf aufbauen. Sie macht aus den siebzehn ausgelieferten Status Abläufe, die Sie übernehmen können, und
erklärt, was jeder Status für die „offenen“ Bestellungen und den Umsatz im Dashboard bedeutet. Die Statusnamen stehen so
da, wie die Administration sie zeigt.

## Inhalt

1. [Die Phasen](#die-phasen)
2. [Was ein Status für das System bedeutet](#was-ein-status-für-das-system-bedeutet)
3. [Drei Beispielabläufe](#drei-beispielabläufe)
4. [Eigene Status](#eigene-status)
5. [Wer die Bestellung weiterbewegt](#wer-die-bestellung-weiterbewegt)
6. [Ein Arbeitsblatt für den Statusablauf](#ein-arbeitsblatt-für-den-statusablauf)

## Die Phasen

| Phase | Was geschieht | Status |
|---|---|---|
| Kasse | Temporäre Bestellung, noch nicht sichtbar | (Pending, intern) |
| Aufgegeben | Aktiviert, nummeriert, gemailt | Pending |
| Zahlung | Auf die Zahlung warten, sie erhalten oder scheitern | Awaiting payment, Paid, Payment failed |
| Abwicklung | Vorbereiten und versenden | Processing, On hold, Backordered, Packed, Shipped, Ready for pickup |
| Ende | Auf die eine oder andere Weise abgeschlossen | Delivered, Completed, Cancelled |
| Nach dem Ende | Rücksendungen und Erstattungen | Return requested, Returned, Partially refunded, Refunded |
| Archiv | Aus der Bestellliste, für die Unterlagen aufbewahrt | jeder Status, Archiv-Kennzeichen gesetzt |

Die temporäre Phase beschreibt [Temporäre Bestellungen und Aktivierung](../kasse/temporaere-bestellungen-und-aktivierung.md).

## Was ein Status für das System bedeutet

`eZOrderStatus` (`kernel/classes/ezorderstatus.php`) hat drei Hilfslisten, die das Shop-Dashboard verwendet
([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md#bestellstatus-für-den-ganzen-lebenszyklus)):

| Hilfsfunktion | Status | Wirkung im Dashboard |
|---|---|---|
| `finishedStatusIDs()` | Delivered, Completed, Cancelled, Partially refunded, Refunded | Keine offene Bestellung mehr |
| `noRevenueStatusIDs()` | Payment failed, Cancelled, Refunded | Als Bestellungen gezählt, nicht im Umsatz, im Durchschnitt oder bei den Top-Produkten |
| `waitingForCustomerStatusIDs()` | Pending, Awaiting payment, Payment failed, Ready for pickup | Nach der kürzeren Wartegrenze hervorgehoben |

Die Wahl des Status ist also nicht kosmetisch: Eine Bestellung, die in Shipped bleibt, bleibt für immer „offen“, und eine
als Completed erfasste Erstattung zählt weiterhin als Umsatz.

## Drei Beispielabläufe

**Kleiner Paketversand**

```text
Pending -> Paid -> Packed -> Shipped -> Completed
                 \-> On hold (problem) -> Packed
Payment failed -> (customer retries) or Cancelled
```

**Click and Collect**

```text
Pending -> Paid -> Processing -> Ready for pickup -> Completed
```

**Anfertigung auf Bestellung mit Rücksendungen**

```text
Pending -> Awaiting payment -> Paid -> Processing -> Backordered -> Shipped -> Delivered
Delivered -> Return requested -> Returned -> Refunded or Partially refunded
```

Welchen Status die Zahlung setzt, hängt vom Gateway ab: Bei `ezpaypal` ist es
`paypal.ini [OrderSettings] PaymentMadeOrderStatusID` ([PayPal-Zahlungen](../../unternehmen/shops/zahlungen-mit-paypal.md)).
Rücksendungen werden so behandelt, wie es [Rücksendungen und Erstattungen](../../kunden/handel/retouren-und-erstattungen.md)
beschreibt.

## Eigene Status

Status mit einer ID unter 1000 sind intern und können nicht entfernt werden; eigene Status beginnen bei 1000. Legen Sie
sie unter **Web-Shop > Bestellstatus** an (`shop/status`, Richtlinie `shop/edit_status`). Ein eigener Status steht in
keiner der drei Hilfslisten, das Dashboard behandelt ihn daher als offen und als Umsatz. Verwenden Sie einen, wenn Sie
einen Schritt brauchen, den die ausgelieferte Liste nicht hat, zum Beispiel „Wartet auf Gravur“, und nicht, um eine
ausgelieferte Bedeutung umzubenennen.

## Wer die Bestellung weiterbewegt

| Änderung | Meist vorgenommen von |
|---|---|
| Pending zu Paid | Dem Zahlungs-Gateway, automatisch |
| Paid zu Packed, Shipped | Mitarbeitenden im Versand, von Hand |
| Shipped zu Completed | Dem Shop-Manager, von Hand, nach der Zustellung oder einer festen Frist |
| Zu Cancelled, Refunded | Dem Shop-Manager oder Inhaber, nachdem die Geldseite geklärt ist |

Jede Änderung wird in `ezorder_status_history` mit Benutzer und Zeitpunkt aufgezeichnet und bei aktivem Prüfpfad als
`commerce.order.status` ([Prüfpfad für den Handel](../betrieb/audit-trail-fuer-den-handel.md)). Wer welche Änderung vornehmen
darf, legen Richtlinien fest ([Statusübergänge und Berechtigungen](statusuebergaenge-und-berechtigungen.md)).
Automatische Statusänderungen außer der des Gateways brauchen ein eigenes Ereignis oder Skript.

## Ein Arbeitsblatt für den Statusablauf

| Frage | Ihre Antwort |
|---|---|
| Welchen Status setzt die Zahlung? | |
| Welche Status setzen die Mitarbeitenden im Versand? | |
| Wann ist eine Bestellung Completed? | |
| Wer darf stornieren, und nur vor welchem Status? | |
| Welche Status bedeuten „nach dem Ende“? | |
| Wann werden Bestellungen archiviert? | |
| Welche eigenen Status, falls überhaupt? | |

## Verwandte Seiten

- [Bestellbetrieb](README.md)
- [Bestellstatus (Ihren Shop betreiben)](../../kunden/handel/bestellstatus.md)
- [Bestellungen und Bestellstatus (Unternehmen)](../../unternehmen/shops/bestellungen-und-bestellstatus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
