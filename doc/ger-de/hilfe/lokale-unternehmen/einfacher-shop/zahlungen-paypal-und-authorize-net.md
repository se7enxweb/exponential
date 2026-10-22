---
title: "Zahlungen: PayPal und Authorize.Net"
audience: local-business, businesses, customer
category: local-business
order: 43
summary: Wie ein lokales Unternehmen im Exponential-Shop Zahlungen annimmt - PayPal über ezpaypal (in der Standarddistribution, eine Weiterleitung zu PayPal) und Kartenzahlungen über ezauthorize (Authorize.Net, ein Kartenformular auf der eigenen Website, ein vorgeschlagenes Paket) - was jedes für die Kartensicherheit bedeutet, was das Unternehmen mit dem Anbieter regelt, Tests und andere Möglichkeiten.
keywords: Zahlung, PayPal, ezpaypal, Authorize.Net, ezauthorize, Kartenzahlungen, Weiterleitung, PCI, Zahlungsanbieter, Gebühren, Testmodus, Sandbox, lokales Unternehmen, payment, card payments, redirect, payment provider, fees, test mode, local business
type: help
---
# Zahlungen: PayPal und Authorize.Net

Diese Seite ist für Inhaber des Shops eines lokalen Unternehmens, die wählen, wie Kunden bezahlen. Exponential
wickelt selbst kein Geld ab: Eine Erweiterung für ein **Zahlungs-Gateway** verbindet die Kasse mit einem
Zahlungsanbieter, und der Anbieter bewegt das Geld ([Zahlungs-Gateways](../../ersteller/website-ersteller/payment-gateways.md)).

## Inhalt

1. [Die zwei Gateways](#die-zwei-gateways)
2. [PayPal mit ezpaypal](#paypal-mit-ezpaypal)
3. [Kartenzahlungen mit ezauthorize](#kartenzahlungen-mit-ezauthorize)
4. [Was Sie mit dem Anbieter regeln](#was-sie-mit-dem-anbieter-regeln)
5. [Bezahlen bei Abholung](#bezahlen-bei-abholung)
6. [Ein anderer Anbieter](#ein-anderer-anbieter)
7. [Test vor dem Livegang](#test-vor-dem-livegang)

## Die zwei Gateways

| | `ezpaypal` | `ezauthorize` |
|---|---|---|
| Anbieter | PayPal | Authorize.Net |
| In `composer.json` | `require`: Teil der Standarddistribution | `suggest`: bei Bedarf installieren |
| Wo der Kunde die Kartendaten eingibt | auf den Seiten von PayPal (eine Weiterleitung) | in einem Formular auf Ihrer Website |
| Aufwand für Kartensicherheit bei Ihnen | geringer: Kartendaten berühren Ihren Server nie | größer: Kartendaten laufen über Ihren Server |
| Lesen | [Zahlungen mit PayPal](../../unternehmen/shops/zahlungen-mit-paypal.md) | [Kartenzahlungen und PCI-Grundlagen](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md) |

Für die meisten kleinen Unternehmen ist eine Weiterleitung zum Anbieter (PayPal) der einfachere und sicherere Start.

## PayPal mit ezpaypal

Aus [ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md) und [Zahlungs-Gateways](../../ersteller/website-ersteller/payment-gateways.md#paypal-einrichten):

1. Das Unternehmen eröffnet ein PayPal-Geschäftskonto (außerhalb von Exponential).
2. Der Ersteller aktiviert `ezpaypal`, trägt das Geschäftskonto in `paypal.ini` ein und fügt das PayPal-Ereignis dem
   Workflow der Kasse hinzu.
3. An der Kasse wird der Kunde zu PayPal geschickt, bezahlt und kehrt zurück. PayPal benachrichtigt die Website, die
   die Bestellung als bezahlt markiert (Status gesetzt durch `PaymentMadeOrderStatusID`).
4. Die Benachrichtigungsadresse (`/paypal/notify_url`) muss aus dem Internet ohne Anmeldung erreichbar sein.

Tägliche und monatliche Prüfungen: [PayPal](../../kunden/handel/paypal.md).

## Kartenzahlungen mit ezauthorize

`ezauthorize` stellt ein Kartenformular in Ihre eigene Kasse und schickt die Karte an Authorize.Net. Der
`suggest`-Eintrag in der `composer.json` beschreibt es als transparente Zahlungslösung ohne Weiterleitung. Weil die
Kartennummer über Ihren Webserver läuft, gelten die Regeln zur Kartensicherheit (PCI DSS) für Ihre Website stärker.
Lesen Sie [Kartenzahlungen und PCI-Grundlagen](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md) mit Ihrem
Anbieter, bevor Sie es wählen; das ist keine Compliance-Beratung, und diese Seite ist es auch nicht.

## Was Sie mit dem Anbieter regeln

| Punkt | Wer | Hinweis |
|---|---|---|
| Händler- oder Geschäftskonto | das Unternehmen | auf den Namen des Unternehmens |
| Gebühren | vom Anbieter festgelegt | dieser Leitfaden nennt keine Gebühren; lesen Sie die aktuellen Bedingungen des Anbieters |
| Auszahlung auf Ihr Bankkonto | das Unternehmen | prüfen Sie, wie viele Tage sie dauert |
| Erstattungen | das Unternehmen, im Konto beim Anbieter | und der Bestellstatus in Exponential ([Rücksendungen und Erstattungen](../../kunden/handel/retouren-und-erstattungen.md)) |
| Streitfälle und Rückbuchungen | das Unternehmen | bewahren Sie Bestellunterlagen auf |

## Bezahlen bei Abholung

Wenn Kunden im Geschäft abholen, kassieren manche Unternehmen an der Ladenkasse. Der Shop kann ohne Zahlungsschritt
laufen: Die Bestellung wird aufgegeben, und Mitarbeitende kassieren bei der Abholung. Bitten Sie Ihren Ersteller, das
Gateway wegzulassen oder es als Wahlmöglichkeit anzubieten; das Shop-Dashboard warnt, wenn es keinen Zahlungsschritt
gibt, was in diesem Fall zu erwarten ist ([Zahlungs-Gateways für Unternehmen](../../unternehmen/shops/zahlungsanbieter-fuer-unternehmen.md)).
Vereinbaren Sie, was passiert, wenn eine Bestellung nicht abgeholt wird.

## Ein anderer Anbieter

Ein Gateway für einen anderen Anbieter ist eine Klasse plus eine Callback-Ansicht in einer Erweiterung, geschrieben von
einem Entwickler ([Ein eigenes Gateway](../../ersteller/website-ersteller/payment-gateways.md#ein-eigenes-gateway)).
Planen Sie es als individuelle Arbeit ein; kein Gateway eines anderen Anbieters steht in der Standard-`composer.json`.

## Test vor dem Livegang

- [ ] Eine Zahlung im Testmodus des Anbieters (PayPal-Sandbox) wird abgeschlossen; die Bestellung wird bezahlt.
- [ ] Eine abgebrochene Zahlung lässt die Bestellung unbezahlt.
- [ ] Die Bestätigungs-E-Mail erreicht den Kunden und den Shop.
- [ ] Eine Erstattung im Konto beim Anbieter wird von Mitarbeitenden im Bestellstatus nachgetragen.
- [ ] Nach dem Umschalten in den Live-Modus funktioniert ein echter kleiner Kauf und wird erstattet.

## Verwandte Seiten

- [Zahlungs-Gateways für Unternehmen](../../unternehmen/shops/zahlungsanbieter-fuer-unternehmen.md)
- [Zahlungs-Gateways (Shop-Betreiber)](../../kunden/handel/zahlungsanbieter.md)
- [Kartenzahlungen und PCI](../../kunden/handel/kartenzahlungen-und-pci.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
