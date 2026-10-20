---
title: Retouren und Erstattungen
audience: customer, admin, businesses
category: customer-success
order: 140
summary: Retouren und Erstattungen in einem Exponential-Shop abwickeln - die Retourenstatus, die Schritte von der Anfrage bis zur Erstattung, wer erstatten darf, Geld und Status in Einklang halten und ein Retourenprotokoll. Keine Rechtsberatung zu Verbraucherrechten.
keywords: Retouren, Erstattungen, Rücksendung angefragt, zurückerhalten, erstattet, teilweise erstattet, Stornierung, Verbraucherrechte, Widerruf, Retourenprotokoll, Erstattung im Anbieterkonto, returns, refunds, return requested, returned, refunded, partially refunded, cancellation, consumer rights, returns log
type: help
---
# Retouren und Erstattungen

Diese Seite ist für die Shop-Leitung, den Kundenservice und den Eigentümer. Sie gibt eine Routine für Retouren und
Erstattungen vor, die Geld, Ware und Bestellstatus in Einklang hält. Die Bedeutung der Status steht unter
[Bestellstatus](bestellstatus.md); die allgemeine Routine für Unternehmen unter
[Einen Shop im Alltag betreiben](../../../../en/us/help/businesses/stores/running-a-store-day-to-day.md).

> **Hinweis**
> Keine Rechtsberatung. Welche Rückgabe- und Widerrufsrechte Ihre Kunden haben, hängt davon ab, wo und an wen Sie
> verkaufen. Formulieren Sie Ihre Rückgabebedingungen mit Ihrem Anwalt oder Ihrem Branchenverband, und stellen Sie sie
> vor der Kasse auf die Website.

## Die Status für die Zeit nach dem Abschluss

Seit 6.0.15 liefert der Shop vier Status für das aus, was nach dem Abschluss einer Bestellung geschieht
([Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md)):

| Status (ID) | Gesetzt, wenn | Wirkung im Dashboard |
|---|---|---|
| Return requested (14) | Der Kunde um eine Rücksendung bittet | Nicht abgeschlossen: Die Bestellung ist wieder offen |
| Returned (15) | Die Ware wieder bei Ihnen ist | Nicht abgeschlossen |
| Partially refunded (16) | Ein Teil des Geldes zurückgezahlt wurde | Abgeschlossen, zählt weiter als Umsatz |
| Refunded (17) | Das ganze Geld zurückgezahlt wurde | Abgeschlossen, kein Umsatz |

Eine vor dem Versand stornierte Bestellung ist einfach **Cancelled** (13): abgeschlossen, kein Umsatz.

## Die Schritte

1. **Anfrage**: Der Kunde fragt an. Prüfen Sie die Anfrage anhand Ihrer Rückgabebedingungen. Setzen Sie **Return
   requested**, und sagen Sie dem Kunden, wie er die Ware zurückschicken soll.
2. **Eingang**: Prüfen Sie die Ware. Setzen Sie **Returned**.
3. **Erstattung**: Nehmen Sie die Erstattung im Konto bei Ihrem Zahlungsanbieter vor. Eine Statusänderung in
   Exponential bewegt kein Geld.
4. **Festhalten**: Setzen Sie **Refunded** oder **Partially refunded**, und notieren Sie Betrag und Grund in Ihrem
   Retourenprotokoll.
5. **Bestand**: Wenn Sie Bestände außerhalb des Shops führen, aktualisieren Sie sie; der Shop hat keine Lagerverwaltung.

> **Tipp**
> Setzen Sie den Status dann, wenn der jeweilige Schritt tatsächlich geschieht. Das Dashboard zählt Return requested und
> Returned als offene Bestellungen, sodass Retouren, die auf Sie warten, in der Liste der Shop-Leitung erscheinen.

## Wer erstatten darf

Erstattungen bewegen Geld; beschränken Sie sie daher auf wenige Personen:

| Schritt | Wer |
|---|---|
| Return requested und Returned setzen | Kundenservice (`shop/setstatus`, eingeschränkt mit `ToStatus`, `kernel/shop/module.php`) |
| Die Erstattung im Konto des Anbieters vornehmen | Der Eigentümer, oder eine namentlich benannte Person |
| Refunded und Partially refunded setzen | Wer die Erstattung vorgenommen hat |

Jede Statusänderung wird in der Statushistorie der Bestellung festgehalten (`kernel/classes/ezorderstatushistory.php`).

Wenn Ihr Shop die Erweiterung `ezauthorize` verwendet, hat diese ein Erstattungsmodul in der Administration
([Grundlagen zu Kartenzahlungen und PCI für Unternehmen](../../../../en/us/help/businesses/stores/card-payments-and-pci-basics.md));
bei PayPal und anderen Anbietern mit Weiterleitung erfolgen Erstattungen im Konto des Anbieters.

## Ein Retourenprotokoll

Der Bestellstatus sagt, wo eine Retoure steht, nicht warum. Führen Sie ein kurzes Protokoll:

```text
RETURNS LOG
Order | Customer request date | Reason | Goods back on | Condition | Refund amount | Refunded on | By | Status set
------|-----------------------|--------|---------------|-----------|---------------|-------------|----|-----------
```

Kopieren Sie keine Adressen oder Zahlungsdaten von Kunden hinein; die Bestellnummer genügt, um sie zu finden.

## Monatliche Prüfung

- [ ] Zu jeder Bestellung mit Refunded oder Partially refunded gibt es eine passende Erstattung in der Abrechnung des Anbieters.
- [ ] Keine Bestellung blieb länger in Return requested oder Returned, als Ihre Bedingungen erlauben.
- [ ] Gründe im Protokoll durchgesehen: Ein Produkt, das oft zurückkommt, braucht eine bessere Beschreibung oder ein Gespräch mit dem Lieferanten.
- [ ] Der Umsatz im Dashboard sinkt bei erstatteten Bestellungen wie erwartet (erstattete Bestellungen zählen als kein Umsatz).

## Verwandte Seiten

- [Bestellstatus](bestellstatus.md)
- [Bestellungen im Alltag](bestellungen-im-alltag.md)
- [Zahlungsanbieter](zahlungsanbieter.md)
- [Bestellungen und Bestellstatus für Unternehmen](../../../../en/us/help/businesses/stores/orders-and-order-statuses.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
