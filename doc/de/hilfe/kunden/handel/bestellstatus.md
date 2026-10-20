---
title: Bestellstatus
audience: customer, admin, businesses
category: customer-success
order: 90
summary: Den Statusablauf Ihres Shops in Exponential festlegen und aufschreiben - die siebzehn ausgelieferten Status in einfachen Worten, welche Ihr Team verwendet, ein Arbeitsblatt für den Statusablauf, eigene Status und eine jährliche Überprüfung des Ablaufs.
keywords: Bestellstatus, Statusablauf, ausstehend, bezahlt, verpackt, versendet, abgeschlossen, storniert, erstattet, eigener Status, shop/status, Status-Arbeitsblatt, order status, status path, pending, paid, packed, shipped, completed, cancelled, refunded, custom status, status worksheet
type: help
---
# Bestellstatus

Diese Seite ist für den Eigentümer und die Shop-Leitung. Sie hilft Ihnen zu entscheiden, welche Bestellstatus Ihr Team
verwendet, sie aufzuschreiben, damit alle sie gleich setzen, und sie zu überprüfen, wenn sich Ihr Ablauf ändert. Die
vollständige Tabelle der Status, mit der Bedeutung von „abgeschlossen“, „kein Umsatz“ und „wartet auf den Kunden“ für
das Dashboard, steht unter [Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md).

## Die ausgelieferten Status in einfachen Worten

Seit 6.0.15 hat jede neue Installation, und jede Installation nach dem Datenbank-Update auf 6.0.15, siebzehn Status
(dieselbe Seite). Die Statusnamen erscheinen in der Administration so, wie sie in der Datenbank stehen:

| Status (ID) | Bedeutung für Ihr Team |
|---|---|
| Pending (1) | Aufgegeben, noch hat niemand sie angesehen |
| Processing (2) | Jemand arbeitet daran |
| Delivered (3) | Zugestellt; die Bestellung ist abgeschlossen |
| Awaiting payment (4) | Aufgegeben, das Geld ist noch nicht eingegangen |
| Paid (5) | Das Geld ist eingegangen |
| Payment failed (6) | Die Zahlung ist nicht durchgegangen; kein Umsatz |
| On hold (7) | Angehalten wegen einer Frage oder einer Prüfung |
| Backordered (8) | Wartet auf Ware |
| Packed (9) | Bereit zum Versand |
| Shipped (10) | An den Versanddienstleister übergeben |
| Ready for pickup (11) | Wartet an Ihrer Theke auf den Kunden |
| Completed (12) | Erledigt; die Bestellung ist abgeschlossen |
| Cancelled (13) | Endgültig angehalten; abgeschlossen, kein Umsatz |
| Return requested (14) | Der Kunde möchte die Ware zurückschicken |
| Returned (15) | Die Ware ist zurück |
| Partially refunded (16) | Ein Teil des Geldes ist erstattet; abgeschlossen |
| Refunded (17) | Das ganze Geld ist erstattet; abgeschlossen, kein Umsatz |

## Ihren Ablauf wählen

Verwenden Sie nur die Status, die Ihr Ablauf braucht. Wenige Status, die zuverlässig gesetzt werden, sind mehr wert als
viele, die willkürlich gesetzt werden. Beispiele aus [Bestellungen und Bestellstatus für Unternehmen](../../../../en/us/help/businesses/stores/orders-and-order-statuses.md):

| Art des Shops | Ablauf |
|---|---|
| Dienstleistungen, kein Versand | Pending, Paid, Completed |
| Kleiner Paketversand | Pending, Paid, Packed, Shipped, Completed |
| Shop mit Abholung | Paid, Packed, Ready for pickup, Completed |
| Shop mit Retouren | ... Shipped, Return requested, Returned, Refunded |

Wenn Sie online Zahlungen annehmen, prüfen Sie, welchen Status der Zahlungsschritt setzt, wenn das Geld eingeht. Bei
PayPal ist das `paypal.ini [OrderSettings] PaymentMadeOrderStatusID`, ausgeliefert als `1000`, eine Nummer für einen
eigenen Status, nicht Paid (5); siehe [PayPal](paypal.md).

## Arbeitsblatt für den Statusablauf

Kopieren, ausfüllen und dort aufhängen, wo Bestellungen bearbeitet werden.

```text
STATUS PATH - [store name]          Version: ____   Date: ________

Status            | We set it when ...                    | Who sets it | Next status
------------------|---------------------------------------|-------------|------------
Pending           |                                       |             |
Paid              |                                       |             |
Packed            |                                       |             |
Shipped           |                                       |             |
Completed         |                                       |             |
Cancelled         |                                       |             |
Return requested  |                                       |             |
Refunded          |                                       |             |

Statuses we do NOT use: ______________________________________
Where tracking numbers are noted: ____________________________
Who may set Refunded: ________________________________________
```

## Eigene Status

Status mit einer ID unter 1000 sind intern und können nicht versehentlich entfernt werden; eigene Status beginnen bei
1000. Sie werden unter **Shop > Bestellstatus** verwaltet (`shop/status`, Richtlinie `shop/edit_status`). Ein eigener
Status ist dann richtig, wenn Ihr Ablauf einen Schritt hat, den die ausgelieferten nicht benennen, zum Beispiel
„Gravur“ oder „Wartet auf Zollabfertigung“.

> **Hinweis**
> Wenn Ihr Shop vor 6.0.15 eigene Status mit den Nummern 4 bis 17 angelegt hat, prüfen Sie nach dem Update die
> Statusliste. Das Update fügt jeden ausgelieferten Status nur hinzu, wenn noch kein Status diese Nummer hat
> ([Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md)).

## Festlegen, wer welchen Status setzt

`shop/setstatus` nimmt die Einschränkungen `FromStatus` und `ToStatus` an (`kernel/shop/module.php`), sodass eine
Rolle nur die Wechsel erlauben kann, die zur Aufgabe der Person passen. Die Vorschläge stehen unter
[Bestellungen im Alltag](bestellungen-im-alltag.md).

## Einmal im Jahr überprüfen

- [ ] Jeder Status auf dem Arbeitsblatt wird noch verwendet; die Statusaufschlüsselung im Dashboard zeigt, wo sich Bestellungen stauen.
- [ ] Kein Status wird für zwei verschiedene Dinge verwendet.
- [ ] Die Rollen passen noch zu den Personen, die Bestellungen bearbeiten.
- [ ] Der Status des Zahlungsschritts ist einer aus Ihrem Ablauf.
- [ ] Neue Mitarbeitende haben den Ablauf vom Arbeitsblatt gelernt, nicht durch Raten.

## Verwandte Seiten

- [Bestellungen im Alltag](bestellungen-im-alltag.md)
- [Retouren und Erstattungen](retouren-und-erstattungen.md)
- [Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md)
- [Bestellungen und Bestellstatus für Unternehmen](../../../../en/us/help/businesses/stores/orders-and-order-statuses.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
