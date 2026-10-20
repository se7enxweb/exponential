---
title: Bestellungen im Alltag
audience: customer, admin, businesses
category: customer-success
order: 80
summary: Bestellungen in einem laufenden Exponential-Shop bearbeiten - der Weg einer Bestellung, die Ansichten, die das Team verwendet, wer was ändern darf, Kundenfragen, Archivierung und die Fehler, die die meisten Supportanrufe verursachen.
keywords: Bestellungen, Bestellabwicklung, Bestellliste, Bestellansicht, Bestellstatus, Archiv, Kundenfragen, Verpacken, Versand, shop/orderlist, shop/orderview, orders, order handling, order list, order view, order status, archive, customer questions, packing, shipping
type: help
---
# Bestellungen im Alltag

Diese Seite ist für die Shop-Leitung und die Personen, die verpacken, versenden und Kunden antworten. Sie beschreibt,
wie ein Team jeden Tag mit Bestellungen in Exponential arbeitet. Die Ansichten und Status sind für Unternehmen unter
[Bestellungen und Bestellstatus](../../../../en/us/help/businesses/stores/orders-and-order-statuses.md) beschrieben; die Morgenroutine
steht unter [Tägliche Shop-Routine](taegliche-shop-routine.md).

## Die Ansichten

| Ansicht | Adresse | Wofür | Benötigt |
|---|---|---|---|
| Shop-Dashboard | `shop/dashboard` | Was jetzt zu tun ist | `shop/administrate` |
| Bestellliste | `shop/orderlist` | Alle aktiven Bestellungen, nach jeder Spalte sortierbar | `shop/administrate` |
| Bestellansicht | `shop/orderview/<id>` | Eine Bestellung: Artikel, Kunde, Adresse, Statushistorie | `shop/administrate`, aus der Liste |
| Archiv | `shop/archivelist` | Abgeschlossene Bestellungen, aus der aktiven Liste genommen | `shop/administrate` |
| Kunden | `shop/customerlist`, `shop/customerorderview` | Wer gekauft hat, und die Bestellungen eines Kunden | `shop/administrate` |

Quelle: `kernel/shop/module.php`. Die Sortierung der Bestellliste ist Teil der Adresse, zum Beispiel
`/shop/orderlist/(sort)/created/(dir)/asc` für die ältesten zuerst, sodass jede Person die Ansicht, mit der sie
arbeitet, als Lesezeichen speichern kann ([Sortierung der Bestellliste](../../../../en/us/features/6.0/order-list-sorting.md)).

## Der Weg einer Bestellung

1. Die Bestellung kommt als **Pending** an, oder mit dem Status, den Ihr Zahlungsschritt bei Zahlung setzt
   ([Zahlungsanbieter](zahlungsanbieter.md)).
2. Jemand prüft sie: Artikel und Optionen, Adresse, den Kommentar des Kunden.
3. Sie wird verpackt (**Packed**), angehalten (**On hold**) oder wartet auf Ware (**Backordered**).
4. Sie geht raus (**Shipped**) oder wartet an der Theke (**Ready for pickup**).
5. Sie endet (**Completed** oder **Delivered**) oder wird gestoppt (**Cancelled**).
6. Nach dem Ende kann sie zurückkommen (**Return requested**, **Returned**, **Refunded**,
   **Partially refunded**); siehe [Retouren und Erstattungen](retouren-und-erstattungen.md).

Ihr Shop wird nicht jeden Status verwenden. Schreiben Sie den Ablauf, den Ihr Team verwendet, unter
[Bestellstatus](bestellstatus.md) auf.

## Wer was ändern darf

Das Ändern eines Status erfordert `shop/setstatus`, das sich mit `FromStatus` und `ToStatus` einschränken lässt
(`kernel/shop/module.php`). Jede Änderung wird in der Statushistorie der Bestellung festgehalten
(`kernel/classes/ezorderstatushistory.php`).

| Person | Eine sinnvolle Einschränkung |
|---|---|
| Verpackung | Von Paid nach Packed, und von Packed nach Shipped |
| Kundenservice | Nach Return requested und Returned |
| Shop-Leitung | Jeder Status der Auftragsabwicklung, Cancelled |
| Eigentümer | Jeder Status, einschließlich Refunded |

Bitten Sie Ihren Administrator, diese als Rollen anzulegen ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

## Kundenfragen

| Der Kunde sagt | Nachsehen in | Dann |
|---|---|---|
| „Wo ist meine Bestellung?“ | Bestellansicht: Status und Statushistorie | Den Status nennen und, falls versendet, die Sendungsnummer |
| „Ich habe die Bestätigung verloren“ | Der Beleg-Link der Bestellung, wenn dauerhafte Belege eingeschaltet sind | Den Link erneut senden ([Rechnungen und Belege](rechnungen-und-belege.md)) |
| „Ich habe bezahlt, aber keine Bestellung“ | Das Konto bei Ihrem Zahlungsanbieter, dann Bestellungen in Pending oder Payment failed | Siehe [Zahlungsanbieter](zahlungsanbieter.md) |
| „Bitte ändern Sie meine Adresse“ | Die Bestellansicht | Im Team vereinbaren, wie Änderungen erfolgen, bevor die Bestellung Shipped ist |
| „Stornieren Sie meine Bestellung“ | Der Status der Bestellung | Cancelled setzen; im Konto des Anbieters erstatten, falls bezahlt wurde |
| „Ich habe die E-Mail nie bekommen“ | Die Kopie im Bestellpostfach, der Spam-Ordner des Kunden | Siehe [Bestellbestätigung per E-Mail](bestellbestaetigung-per-e-mail.md) |

> **Hinweis**
> Eine Statusänderung bewegt nie Geld. Erstattungen erfolgen im Konto bei Ihrem Zahlungsanbieter; der Status hält
> fest, dass sie erfolgt sind.

## Archivieren

Archivieren Sie abgeschlossene Bestellungen regelmäßig (`shop/archiveorder`), zum Beispiel einmal pro Woche jene, die
älter als Ihre Rückgabefrist sind. Archivierte Bestellungen verschwinden aus der aktiven Liste, werden aber nicht
gelöscht und können zurückgeholt werden (`shop/unarchiveorder`). Das Entfernen von Bestellungen (`shop/removeorder`)
löscht sie; Ihre Buchhaltungsvorschriften können verlangen, dass Sie Bestellunterlagen über Jahre aufbewahren, daher
lieber archivieren als entfernen ([Datenexport und Löschung](datenexport-und-loeschung.md)).

## Fehler, die Supportanrufe verursachen

- Status werden am Ende des Tages gesetzt statt dann, wenn die Arbeit geschah.
- Bestellungen bleiben in Pending, weil sich an einem bestimmten Tag niemand zuständig fühlt.
- Ein Zahlungsschritt, der Bestellungen mit einem Status markiert, den niemand verwendet; siehe die PayPal-Einstellung unter [PayPal](paypal.md).
- Sendungsnummern liegen im Posteingang einer einzigen Person.
- Testbestellungen sind mit echten vermischt; benennen Sie Testkunden eindeutig und entfernen oder stornieren Sie deren Bestellungen.

## Verwandte Seiten

- [Bestellstatus](bestellstatus.md)
- [Tägliche Shop-Routine](taegliche-shop-routine.md)
- [Bestellungen und Bestellstatus für Unternehmen](../../../../en/us/help/businesses/stores/orders-and-order-statuses.md)
- [Warenkorb, Kasse und Bestellungen](../../../../en/us/help/creators/website-creators/basket-checkout-and-orders.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
