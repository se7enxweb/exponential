---
title: Zahlungsanbieter
audience: customer, admin, businesses
category: customer-success
order: 100
summary: Mit dem Zahlungsschritt eines Exponential-Shops leben - tägliche und monatliche Prüfungen, worauf zu achten ist, wenn der Anbieter etwas ändert, Anzeichen dafür, dass Zahlungen nicht bei den Bestellungen ankommen, und was Sie Ihren Partner und Ihren Anbieter fragen sollten.
keywords: Zahlungsanbieter, Payment-Gateway, Zahlungsschritt, Checkout-Workflow, fehlgeschlagene Zahlung, Zahlungsbenachrichtigung, paymentgateways.ini, Änderungen beim Anbieter, Abgleich, payment gateway, payment provider, payment step, checkout workflow, payment failed, payment notification, provider changes, reconciliation
type: help
---
# Zahlungsanbieter

Diese Seite ist für Eigentümer und Shop-Leitungen, deren Shop Zahlungen online annimmt. Sie behandelt den laufenden
Shop: was zu prüfen ist, was kaputtgehen kann und wen Sie fragen. Wie die Zahlung in die Kasse eingebunden ist und wie
Sie einen Anbieter wählen, steht unter [Zahlungsanbieter für Unternehmen](../../../../en/us/help/businesses/stores/payment-gateways-for-businesses.md).

## Wie es funktioniert, in einem Absatz

In Exponential ist ein Payment-Gateway ein Workflow-Ereignis an der Kasse. `settings/paymentgateways.ini` listet die
aktiven Gateways auf, und ein Workflow mit dem Ereignis für das Payment-Gateway ist mit dem Trigger `shop_checkout`
verbunden. An der Kasse bezahlt der Kunde, und die Bestellung wird aufgegeben, wenn der Workflow abgeschlossen ist
([Payment-Gateways für Website-Ersteller](../../../../en/us/help/creators/website-creators/payment-gateways.md)). Das Projekt bietet
`ezpaypal` (eine Weiterleitung zu PayPal) und `ezauthorize` (ein Kartenformular, das an Authorize.Net gesendet wird)
([Zahlungsanbieter für Unternehmen](../../../../en/us/help/businesses/stores/payment-gateways-for-businesses.md)).

Wenn an der Kasse kein Zahlungsschritt eingerichtet ist, werden Bestellungen ohne Zahlung aufgegeben, und das
Shop-Dashboard weist unter „Nächste Schritte“ darauf hin ([Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md)).

## Wer was tut

| Aufgabe | Üblicherweise |
|---|---|
| Der Vertrag und die Gebühren mit dem Anbieter | Eigentümer |
| Erstattungen und Reklamationen im Konto des Anbieters | Eigentümer, oder eine namentlich benannte Person |
| Täglicher Abgleich von Zahlungen und Bestellungen | Shop-Leitung |
| Gateway-Einstellungen, Testmodus, Schlüssel | Partner oder Administrator, in Override-Dateien |
| Die Gateway-Erweiterung zusammen mit Exponential aktualisieren | Hoster oder Partner |

## Jeden Tag

- [ ] Bestellungen in **Payment failed**: Eine liegt am Kunden; mehrere hintereinander liegen am Zahlungsschritt
  ([Tägliche Shop-Routine](taegliche-shop-routine.md)).
- [ ] Die Zahlungen im Konto des Anbieters stimmen mit den bezahlten Bestellungen überein.

## Jeden Monat

- [ ] Die Abrechnung des Anbieters stimmt mit den bezahlten Bestellungen des Monats überein (**Shop > Produkt-Statistiken**, [Shop-Berichte](shop-berichte.md)).
- [ ] Die Mitteilungen des Anbieters sind gelesen: Änderungen an seiner Schnittstelle, Sicherheitsanforderungen, Gebühren.
- [ ] Ihr Partner weiß von jeder Änderung, die der Anbieter angekündigt hat.

## Anzeichen dafür, dass Zahlungen nicht bei den Bestellungen ankommen

| Anzeichen | Wahrscheinliche Ursache | Fragen Sie |
|---|---|---|
| Kunden haben bezahlt, Bestellungen sind nicht als bezahlt markiert | Die Benachrichtigung des Anbieters erreicht die Website nicht | Partner oder Hoster: Ist die Benachrichtigungsadresse ohne Anmeldung erreichbar? |
| Seit einem bestimmten Tag schlagen alle Zahlungen fehl | Ein Schlüssel, ein Zertifikat oder die Schnittstelle des Anbieters hat sich geändert | Partner, und die Statusseite des Anbieters |
| Kunden landen auf einer Testseite | Testmodus oder eine Sandbox-Adresse ist noch gesetzt | Partner, sofort |
| Bezahlte Bestellungen haben einen Status, den niemand verwendet | Der „bezahlt“-Status des Gateways passt nicht zu Ihrem Ablauf | [Bestellstatus](bestellstatus.md) |

Für PayPal stehen die Prüfungen, die die Erweiterung durchführt, bevor sie eine Bestellung als bezahlt markiert, und
die Anzeichen für jedes Problem unter [PayPal](paypal.md).

> **Warnung**
> Eine Website hinter einer Anmeldung, einer IP-Freigabeliste oder im Wartungsmodus kann Zahlungen annehmen, die nie
> als bezahlt markiert werden, weil die Benachrichtigung des Anbieters abgewiesen wird ([PayPal-Zahlungen für Unternehmen](../../../../en/us/help/businesses/stores/paypal-payments.md)).
> Sagen Sie Ihrem Hoster Bescheid, bevor während der Geschäftszeiten des Shops der Wartungsmodus eingeschaltet wird.

## Wenn der Anbieter etwas ändert

Anbieter ändern ihre Schnittstellen, Signaturen und Sicherheitsregeln. Wenn sie eine Änderung ankündigen:

1. Leiten Sie sie noch am selben Tag an Ihren Partner weiter.
2. Fragen Sie, ob die Gateway-Erweiterung die Änderung unterstützt und ob ein Update nötig ist.
3. Bitten Sie um einen Test im Testmodus des Anbieters vor dessen Stichtag.
4. Planen Sie die Umstellung außerhalb Ihrer geschäftigen Zeiten.

## Einen Anbieter hinzufügen oder wechseln

Ein anderer Anbieter braucht eine Gateway-Erweiterung: eine Klasse, die `eZPaymentGateway` oder `eZRedirectGateway`
erweitert, dokumentiert in `doc/en/us/shop/` und Schritt für Schritt gezeigt in `doc/en/us/shop/examples/paypal/`.
Das ist Arbeit für den Partner. Fragen Sie nach Möglichkeit nach einem Gateway mit Weiterleitung, damit Kartendaten
nicht über Ihre Website laufen ([Kartenzahlungen und PCI](kartenzahlungen-und-pci.md)).

## Fragen an Ihren Partner

- Welches Gateway in welcher Version läuft in unserem Shop, und wann wurde es zuletzt aktualisiert?
- Wo werden seine Einstellungen und Schlüssel aufbewahrt, und wer kann sie ändern?
- Ist die Benachrichtigungsadresse von außen erreichbar, und wird sie überwacht?
- Wie testen wir eine Änderung ohne echte Zahlungen?

## Verwandte Seiten

- [PayPal](paypal.md)
- [Kartenzahlungen und PCI](kartenzahlungen-und-pci.md)
- [Zahlungsanbieter für Unternehmen](../../../../en/us/help/businesses/stores/payment-gateways-for-businesses.md)
- [Payment-Gateways für Website-Ersteller](../../../../en/us/help/creators/website-creators/payment-gateways.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
