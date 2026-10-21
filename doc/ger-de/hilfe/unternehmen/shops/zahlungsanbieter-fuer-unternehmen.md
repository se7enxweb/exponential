---
title: Zahlungsanbieter für Unternehmen
audience: businesses, project-manager, website-creator
category: business-solutions
order: 130
summary: Wie Zahlungen in einem Exponential-Shop funktionieren - Zahlungsanbieter als Workflow-Schritt an der Kasse, Anbieter mit Weiterleitung und mit Kartenformular auf der Website, die Erweiterungen für PayPal und Authorize.Net, die Anbindung weiterer Anbieter und wie Sie auswählen.
keywords: Zahlungsanbieter, Zahlungsdienstleister, Payment-Gateway, PayPal, ezpaypal, Authorize.Net, ezauthorize, Zahlung mit Weiterleitung, Kartenformular, paymentgateways.ini, Kassen-Workflow, Zahlungsanbieter auswählen, payment gateway, payment provider, redirect payment, card form, checkout workflow, choosing a payment provider
type: help
---
# Zahlungsanbieter für Unternehmen

Diese Seite richtet sich an Geschäftsinhaber, die entscheiden, wie ihre Kunden bezahlen. Sie erklärt, wie ein
Zahlungsanbieter in einen Exponential-Shop eingebunden wird, die zwei Arten der Anbindung, die Anbieter, für die das
Projekt Erweiterungen anbietet, und die Fragen, die Sie stellen sollten, bevor Sie einen Vertrag mit einem Anbieter
schließen. Die Einrichtung ist unter [Zahlungsanbieter](../../ersteller/website-ersteller/payment-gateways.md) beschrieben.

## Inhalt

1. [Wo die Zahlung hineinpasst](#wo-die-zahlung-hineinpasst)
2. [Zwei Arten der Anbindung](#zwei-arten-der-anbindung)
3. [Die Anbindungen, die das Projekt anbietet](#die-anbindungen-die-das-projekt-anbietet)
4. [Andere Anbieter](#andere-anbieter)
5. [Einen Anbieter auswählen](#einen-anbieter-auswählen)
6. [Zahlung und Bestellstatus](#zahlung-und-bestellstatus)
7. [Zahlungen testen](#zahlungen-testen)

## Wo die Zahlung hineinpasst

In Exponential ist eine Zahlungsanbindung ein **Workflow-Ereignis** an der Kasse. Wenn der Kunde bezahlt, schickt das
Ereignis ihn zum Anbieter oder belastet ihn direkt und markiert die Bestellung als bezahlt, sobald der Anbieter
bestätigt ([Zahlungsanbieter](../../ersteller/website-ersteller/payment-gateways.md)). In der Praxis:

1. `settings/paymentgateways.ini` listet die aktiven Anbindungen auf.
2. Ein Workflow mit dem Ereignis der Zahlungsanbindung (`ezpaymentgateway`, in `kernel/classes/workflowtypes/event/`)
   ist mit dem Auslöser `shop_checkout` verbunden.
3. An der Kasse bezahlt der Kunde; die Bestellung ist aufgegeben, wenn der Workflow abgeschlossen ist.

Der Kernel liefert die Bausteine: `eZPaymentGateway`, `eZRedirectGateway`, `eZPaymentObject` und
`eZPaymentCallbackChecker` in `kernel/shop/classes/`, dokumentiert in `doc/eng-us/shop/`.

Gibt es an der Kasse keinen Zahlungsschritt, werden Bestellungen ohne Zahlung aufgegeben. Das Shop-Dashboard warnt
davor in seinen „Nächste Schritte“ ([Shop-Dashboard und Berichte](shop-uebersicht-und-berichte.md#nächste-schritte)). Manche
Unternehmen wollen genau das, für Bestellungen auf Rechnung oder mit Bezahlung bei Abholung.

## Zwei Arten der Anbindung

| Art | Was der Kunde sieht | Was Ihre Website verarbeitet | Beispiel |
|---|---|---|---|
| Weiterleitung | Verlässt Ihre Website, bezahlt auf der Seite des Anbieters und kehrt zurück | Keine Kartendaten; einen Rückruf des Anbieters | `ezpaypal` |
| Kartenformular auf der Website | Gibt Kartendaten in ein Formular auf Ihrer Website ein | Kartendaten laufen über Ihren Server zum Anbieter | `ezauthorize` |

Der Unterschied ist für die Kartensicherheit wichtig. Bei einer Weiterleitung werden Kartendaten auf der Seite des
Anbieters eingegeben; bei einem Formular auf Ihrer Website laufen sie über Ihre Website. Siehe
[Kartenzahlungen und PCI-Grundlagen](kartenzahlungen-und-pci-grundlagen.md).

## Die Anbindungen, die das Projekt anbietet

| Erweiterung | Anbieter | Wie sie funktioniert | Status |
|---|---|---|---|
| `ezpaypal` 1.2.3 | PayPal | Weiterleitung zu PayPal; PayPal benachrichtigt `/paypal/notify_url` | Vom `composer.json` des Hauptverzeichnisses verlangt (`se7enxweb/ezpaypal ~1.2.3`) |
| `ezauthorize` | Authorize.Net | Kartenformular auf Ihrer Website, an die AIM-Schnittstelle von Authorize.Net gesendet | Im `composer.json` vorgeschlagen (`se7enxweb/ezauthorize`) |

Quellen: [ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md), das `composer.json` des Hauptverzeichnisses und die
eigenen Dateien der Erweiterung (`extension/ezauthorize/doc/README`, `classes/ezauthorizeaim.php`). Der Vorschlag im
`composer.json` beschreibt ezauthorize als „transparent (no redirect away to paypal.com website distractions)“
Zahlungslösung.

Mehr dazu: [Zahlungen mit PayPal](zahlungen-mit-paypal.md) und [Kartenzahlungen und PCI-Grundlagen](kartenzahlungen-und-pci-grundlagen.md).

## Andere Anbieter

Viele Unternehmen haben bereits eine Bank oder einen Zahlungsanbieter. Hat das Projekt keine Erweiterung dafür, lässt
sich eine Anbindung schreiben: eine Klasse, die `eZPaymentGateway` erweitert oder, für Anbieter mit Weiterleitung,
`eZRedirectGateway`, registriert in der `paymentgateways.ini` einer Erweiterung, dazu eine Ansicht für den Rückruf des
Anbieters. Die Seiten unter `doc/eng-us/shop/examples/paypal/` gehen eine solche Schritt für Schritt durch
([Zahlungsanbieter](../../ersteller/website-ersteller/payment-gateways.md#ein-eigenes-gateway)).

Das ist Arbeit für Anwendungsentwickler ([Einen Workflow-Ereignistyp schreiben](../../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md)).
Eine Anbindung mit Weiterleitung ist meist das einfachere und sicherere Projekt. Wenn Sie eine in Auftrag geben,
bitten Sie darum, dass sie als Erweiterung veröffentlicht wird, die andere nutzen können
([Erweiterungen zurückgeben: Ihre Arbeit mit allen teilen](../../erweiterungen-zurueckgeben.md)).

## Einen Anbieter auswählen

- [ ] Gibt es dafür eine Exponential-Erweiterung, oder muss eine geschrieben werden?
- [ ] Weiterleitung oder Kartenformular auf Ihrer Website? (Eine Weiterleitung hält Kartendaten von Ihrem Server fern.)
- [ ] Welche Zahlungsarten erwarten Ihre Kunden?
- [ ] In welchen Währungen rechnet er ab, und zu welchen Kursen ([Preise und Währungen](preise-und-waehrungen.md))?
- [ ] Hat er einen Testmodus, und können Sie dort den ganzen Bestellvorgang testen?
- [ ] Wie funktionieren Erstattungen, und wer führt sie durch?
- [ ] Was berechnet er pro Transaktion und pro Monat? (Vergleichen Sie die Angebote selbst; Preise ändern sich.)
- [ ] Was verlangt Ihr Vertrag von Ihnen bei der Kartensicherheit?

## Zahlung und Bestellstatus

Eine Anbindung kann den Status der Bestellung setzen, wenn die Zahlung bestätigt ist. Zu den mitgelieferten Status
gehören **Awaiting payment**, **Paid** und **Payment failed** ([Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md)).
Stimmen Sie den Status der Anbindung auf sie ab, damit das Dashboard bezahlte und fehlgeschlagene Bestellungen richtig
zählt. Für PayPal lautet die Einstellung `paypal.ini [OrderSettings] PaymentMadeOrderStatusID`
([Zahlungen mit PayPal](zahlungen-mit-paypal.md#bestellstatus-nach-der-zahlung)).

## Zahlungen testen

Aus [Zahlungsanbieter](../../ersteller/website-ersteller/payment-gateways.md#testen):

- [ ] Eine Zahlung im Testmodus des Anbieters wird abgeschlossen, und die Bestellung wird bezahlt.
- [ ] Eine abgebrochene Zahlung lässt die Bestellung unbezahlt, und der Kunde kann es erneut versuchen.
- [ ] Der Rückruf funktioniert über HTTPS und durch jeden Proxy.
- [ ] Das Shop-Dashboard meldet nicht mehr, dass es an der Kasse keinen Zahlungsschritt gibt.

## Verwandte Seiten

- [Zahlungsanbieter](../../ersteller/website-ersteller/payment-gateways.md)
- [Zahlungen mit PayPal](zahlungen-mit-paypal.md)
- [Kartenzahlungen und PCI-Grundlagen](kartenzahlungen-und-pci-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
