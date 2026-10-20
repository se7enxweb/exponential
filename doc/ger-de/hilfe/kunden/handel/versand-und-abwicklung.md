---
title: Versand und Abwicklung
audience: customer, admin, businesses
category: customer-success
order: 130
summary: Die Versandroutine eines laufenden Exponential-Shops - Verpacken und Versenden über die Bestellstatus, Sendungsnummern, Abholbestellungen, die pauschalen Versandkosten ändern und wann Sie Ihren Partner um Versandregeln bitten.
keywords: Versand, Abwicklung, Verpacken, versendet, Sendungsnummer, Abholung, abholbereit, Versandkosten, einfacher Versand, Versand-Handler, Versanddienstleister, shipping, handling, packing, shipped, tracking number, pickup, ready for pickup, shipping cost, simple shipping, shipping handler, carrier
type: help
---
# Versand und Abwicklung

Diese Seite ist für die Personen, die verpacken und versenden, und für den Eigentümer, der die Versandbedingungen
festlegt. Sie behandelt die tägliche Routine und die Änderungen, die ein laufender Shop vornimmt. Welche
Versandwerkzeuge der Shop hat und wie Sie von einer Pauschale zu Regeln nach Zone oder Gewicht wachsen, steht unter
[Versand und Abwicklung für Unternehmen](../../../../en/us/help/businesses/stores/shipping-and-handling.md).

## Was Ihr Shop berechnet

Von Haus aus hat der Shop ein einziges Versandwerkzeug: das Workflow-Ereignis für einfachen Versand, das jeder
Bestellung einmal eine feste Versandposition hinzufügt, umgerechnet in die Währung der Bestellung. Es läuft vor der
Bestätigungsseite, sodass der Kunde die Kosten vor dem Bezahlen sieht. Betrag und Bezeichnung kommen aus
`settings/workflow.ini [SimpleShippingWorkflow]`, ausgeliefert als `ShippingCost=12` und
`ShippingDescription=Shipping`; Ihr Partner setzt Ihre Werte in einem Override
([Versand und Abwicklung für Unternehmen](../../../../en/us/help/businesses/stores/shipping-and-handling.md)).

Alles Weitere, etwa kostenloser Versand ab einem Bestellwert, Tarife nach Land oder Gewicht oder eine Auswahl für
Expressversand, erfordert einen Versand-Handler, den ein Entwickler schreibt (`settings/shop.ini [ShippingSettings]`).

## Die Routine für Verpacken und Versand

| Schritt | Zu setzender Status (Namen aus `kernel/classes/ezorderstatus.php`) | Wer |
|---|---|---|
| Bestellung geprüft und bezahlt | Paid, oder der Status Ihres Zahlungsschritts | Shop-Leitung |
| Etwas fehlt | Backordered, oder On hold mit einer Notiz | Verpackung |
| Paket fertig | Packed | Verpackung |
| An den Versanddienstleister übergeben | Shipped | Verpackung |
| Zugestellt, oder keine Beanstandung nach der vereinbarten Frist | Completed | Shop-Leitung |

Die Status sind unter [Bestellstatus](bestellstatus.md) beschrieben. Begrenzen Sie, was die Rolle für die Verpackung
setzen darf, mit `FromStatus` und `ToStatus` an `shop/setstatus` (`kernel/shop/module.php`).

## Sendungsnummern

Der Shop hat standardmäßig kein Feld für eine Sendungsnummer
([Einen Shop im Alltag betreiben](../../../../en/us/help/businesses/stores/running-a-store-day-to-day.md)). Einigen Sie sich auf einen
Ort und verwenden Sie ihn jedes Mal, zum Beispiel:

- das eigene Portal des Versanddienstleisters, durchsuchbar nach Ihrer Bestellnummer;
- ein gemeinsames Versandprotokoll mit Bestellnummer, Versanddienstleister, Sendungsnummer und Datum;
- ein Feld, das Ihr Partner dem Bestellablauf hinzufügt, wenn Sie viel versenden.

Wofür Sie sich auch entscheiden: Der Kundenservice muss die Nummer allein anhand der Bestellnummer finden können.

## Abholbestellungen

Der ausgelieferte Status **Ready for pickup** (11) zählt als „wartet auf den Kunden“, sodass das Dashboard solche
Bestellungen nach der kürzeren Wartefrist hervorhebt ([Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md)).

- [ ] Teilen Sie dem Kunden mit, dass die Bestellung bereitliegt und wie lange Sie sie aufbewahren.
- [ ] Prüfen Sie jeden Tag die hervorgehobenen Abholbestellungen und erinnern Sie den Kunden.
- [ ] Setzen Sie Completed, wenn abgeholt wurde; vereinbaren Sie, was mit nie abgeholten Bestellungen geschieht.

## Die Versandkosten ändern

Die Pauschale ist eine Einstellung, eine Änderung ist also ein Auftrag an Ihren Partner oder Administrator:

1. Legen Sie den neuen Betrag fest, und klären Sie seine Behandlung bei der Mehrwertsteuer mit Ihrem Steuerberater.
2. Beauftragen Sie die Änderung von `[SimpleShippingWorkflow] ShippingCost` (und, falls nötig, der Bezeichnung) in einem Override.
3. Vereinbaren Sie den Zeitpunkt, zu dem sie live geht, und aktualisieren Sie gleichzeitig Ihre Seite mit den Versandbedingungen.
4. Geben Sie eine Testbestellung auf: Die Bestätigungsseite zeigt die neue Versandposition, in jeder aktiven Währung.

## Wenn die Pauschale nicht genügt

Anzeichen dafür, dass Sie Versandregeln brauchen:

- viele Bestellungen, bei denen die Versandkosten weit von dem entfernt sind, was der Versanddienstleister Ihnen berechnet;
- Kunden fragen nach Expresszustellung oder nach kostenlosem Versand ab einem Betrag;
- Bestellungen aus Ländern mit sehr unterschiedlichen Zustellkosten.

Bitten Sie Ihren Partner um einen Versand-Handler, und geben Sie ihm die Preisliste Ihres Versanddienstleisters, die
Länder, in die Sie versenden, und die gewünschte Regel ([Versand und Abwicklung für Unternehmen](../../../../en/us/help/businesses/stores/shipping-and-handling.md)
listet die gängigen Modelle). Etiketten, Buchung beim Versanddienstleister und Sendungsverfolgung bleiben außerhalb
von Exponential, sofern sie nicht hinzugefügt werden ([Einen Shop betreiben: Überblick](../../../../en/us/help/creators/website-creators/shop-overview.md)).

## Monatliche Prüfungen

- [ ] Den Kunden berechnete Versandkosten mit den Rechnungen des Versanddienstleisters verglichen.
- [ ] Bestellungen, die lange in Packed oder Shipped standen: verlorene Pakete, oder nicht gesetzte Status?
- [ ] Ihre Seite mit den Versandbedingungen entspricht noch dem, was der Shop berechnet.

## Verwandte Seiten

- [Versand und Abwicklung für Unternehmen](../../../../en/us/help/businesses/stores/shipping-and-handling.md)
- [Bestellungen im Alltag](bestellungen-im-alltag.md)
- [Retouren und Erstattungen](retouren-und-erstattungen.md)
- [Warenkorb, Kasse und Bestellungen](../../../../en/us/help/creators/website-creators/basket-checkout-and-orders.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
