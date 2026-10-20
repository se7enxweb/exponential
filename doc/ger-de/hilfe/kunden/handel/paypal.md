---
title: PayPal
audience: customer, admin, businesses
category: customer-success
order: 110
summary: PayPal-Zahlungen in einem Exponential-Shop mit der Erweiterung ezpaypal betreiben - die täglichen und monatlichen Prüfungen, den PayPal-Status mit Ihren Bestellstatus in Einklang halten, worauf beim Start und nach Änderungen zu achten ist und was zu tun ist, wenn eine bezahlte Bestellung nicht als bezahlt markiert wird.
keywords: PayPal, ezpaypal, paypal.ini, Benachrichtigungs-URL, notify URL, PaymentMadeOrderStatusID, Sandbox, Zahlung nicht als bezahlt markiert, Erstattungen, PayPal-Konto, payment not marked paid, refunds, PayPal account
type: help
---
# PayPal

Diese Seite ist für Eigentümer und Shop-Leitungen, deren Shop PayPal über die Erweiterung `ezpaypal` annimmt. Es geht
um den Betrieb: die Prüfungen, die eine Einstellung, die die meisten Shops falsch haben, und was zu tun ist, wenn etwas
nicht übereinstimmt. Wie die Erweiterung funktioniert und was sie prüft, bevor sie eine Bestellung als bezahlt
markiert, steht unter [PayPal-Zahlungen für Unternehmen](../../../../en/us/help/businesses/stores/paypal-payments.md); die Referenz der
Einstellungen ist [ezpaypal](../../../../en/us/features/6.0/extensions/ezpaypal.md).

## Was in Ihrem Shop läuft

`se7enxweb/ezpaypal` wird von der `composer.json` im Wurzelverzeichnis verlangt und daher mit Exponential installiert.
Wenn die Erweiterung aktiviert und in die Kasse eingebunden ist, wird der Kunde zum Bezahlen zu PayPal geschickt, und
PayPal benachrichtigt Ihre Website unter `/paypal/notify_url`. Bevor die Bestellung als bezahlt markiert wird, prüft
die Erweiterung bei PayPal, dass die Benachrichtigung echt ist, dass der Zahlungsstatus Completed ist und dass Betrag
und Währung mit der Bestellung übereinstimmen ([PayPal-Zahlungen für Unternehmen](../../../../en/us/help/businesses/stores/paypal-payments.md)).

Kartendaten werden auf der Seite von PayPal eingegeben, nie auf Ihrer ([Kartenzahlungen und PCI](kartenzahlungen-und-pci.md)).

## Die Einstellung, die Sie zuerst prüfen sollten

`paypal.ini [OrderSettings] PaymentMadeOrderStatusID` ist der Bestellstatus, der gesetzt wird, wenn PayPal eine
Zahlung bestätigt. Ihr ausgelieferter Wert ist `1000`, die erste Nummer für eigene Status, nicht der ausgelieferte
Status **Paid** (5) ([ezpaypal](../../../../en/us/features/6.0/extensions/ezpaypal.md)). Fragen Sie Ihren Partner, welchen
Wert Ihr Shop verwendet, und stellen Sie sicher, dass es ein Status aus Ihrem Ablauf ist ([Bestellstatus](bestellstatus.md)):

| Ihre Wahl | Dann |
|---|---|
| Auf Paid (5) zeigen lassen | Die Zahlen zu bezahlten Bestellungen im Dashboard und Ihr Statusablauf verwenden den Standardstatus |
| 1000 beibehalten | Ein eigener Status mit der Nummer 1000 muss existieren, zum Beispiel „Mit PayPal bezahlt“ |

## Wer was tut

| Aufgabe | Wer |
|---|---|
| Das PayPal-Geschäftskonto, Gebühren, Reklamationen | Eigentümer |
| Erstattungen im PayPal-Konto | Eigentümer, oder eine namentlich benannte Person |
| Overrides von `paypal.ini`, die Benachrichtigungsadresse, Updates | Partner oder Administrator (Einstellungen gehören in `settings/override/paypal.ini.append.php`) |
| Täglicher Abgleich von PayPal-Zahlungen und Bestellungen | Shop-Leitung |

## Jeden Tag

- [ ] Zu jeder eingegangenen PayPal-Zahlung gibt es eine Bestellung mit Ihrem „bezahlt“-Status.
- [ ] Keiner Bestellung mit Ihrem „bezahlt“-Status fehlt eine PayPal-Zahlung.
- [ ] Bestellungen, die nach einer PayPal-Zahlung in Payment failed oder noch in Pending stehen, werden am selben Tag geprüft.

## Jeden Monat

- [ ] Die Monatsabrechnung von PayPal stimmt mit den bezahlten Bestellungen des Monats überein ([Shop-Berichte](shop-berichte.md)).
- [ ] In PayPal vorgenommene Erstattungen sind an den Bestellungen als Refunded oder Partially refunded vermerkt.
- [ ] Mitteilungen von PayPal zu Änderungen am Konto oder an der Schnittstelle sind an Ihren Partner weitergeleitet.

## Wenn eine bezahlte Bestellung nicht als bezahlt markiert wird

Gehen Sie die Tabelle aus [PayPal-Zahlungen für Unternehmen](../../../../en/us/help/businesses/stores/paypal-payments.md) durch:

| Symptom | Was Sie zuerst prüfen oder Ihren Partner fragen |
|---|---|
| Kunde hat bezahlt, Bestellung nicht als bezahlt markiert | Ist `/paypal/notify_url` aus dem Internet ohne Anmeldung erreichbar? Zeigt das Log der Erweiterung eine fehlgeschlagene Prüfung? |
| „Amount does not match“ im Log | Rundungs- oder Währungsunterschiede ([Preise und Währungen](preise-und-waehrungen.md)) |
| Kunden landen in der Sandbox | `[ServerSettings] ServerName` zeigt noch auf die Sandbox |
| Ein Status wird gesetzt, aber niemand sieht ihn | `PaymentMadeOrderStatusID` zeigt auf einen Status, den Sie nicht verwenden |

Bis das behoben ist, bearbeiten Sie die betroffenen Bestellungen von Hand: Bestätigen Sie die Zahlung in PayPal,
setzen Sie die Bestellung dann selbst auf Ihren „bezahlt“-Status, und notieren Sie den Grund in Ihrem Protokoll.

> **Warnung**
> Der Wartungsmodus, eine Anmeldung vor der Website oder eine IP-Freigabeliste blockieren auch die Benachrichtigung von
> PayPal. Planen Sie Arbeiten, die so etwas tun, außerhalb der Geschäftszeiten des Shops, und prüfen Sie die
> Bestellungen, die kurz davor und danach aufgegeben wurden.

## Nach jeder Änderung

Änderungen an der Website, am Server, an HTTPS oder an der PayPal-Erweiterung können die Benachrichtigung
beeinträchtigen. Nach jeder solchen Änderung:

- [ ] Eine kleine echte Zahlung, dann eine Erstattung in PayPal, und die Bestellstatus geprüft.
- [ ] Oder, wenn Ihr Partner eine Sandbox-Konfiguration auf einer Kopie hat, zuerst ein vollständiger Test dort.

## Verwandte Seiten

- [Zahlungsanbieter](zahlungsanbieter.md)
- [PayPal-Zahlungen für Unternehmen](../../../../en/us/help/businesses/stores/paypal-payments.md)
- [ezpaypal](../../../../en/us/features/6.0/extensions/ezpaypal.md)
- [Bestellstatus](bestellstatus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
