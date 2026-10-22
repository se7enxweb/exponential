---
title: Das Ereignis Zahlungs-Gateway
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 44
summary: Wie das Workflow-Ereignis Payment Gateway von Exponential funktioniert - verfügbare, ausgewählte und aktuelle Gateways, die Wahl des Kunden, wenn mehrere ausgewählt sind, Weiterleitungs- und transparente Gateways, das Zahlungsobjekt, der Rückruf des Anbieters, der den Workflow fortsetzt, und seine Logs.
keywords: Ereignis Zahlungs-Gateway, ezpaymentgateway, eZPaymentGatewayType, selectgateway.tpl, eZRedirectGateway, eZPaymentGateway, eZPaymentObject, eZPaymentCallbackChecker, Rückruf, Callback, Notify-URL, continueWorkflow, STATUS_REDIRECT_REPEAT, Zahlungslogs, payment gateway event, callback, notify URL, payment logs
type: help
---
# Das Ereignis Zahlungs-Gateway

Diese Seite ist für Entwickler und Administratoren, die in einem Exponential-Shop die Zahlung einrichten. Die Zahlung ist
ein Workflow-Ereignis, **Payment Gateway** (`ezpaymentgateway`), am Vorher-Trigger von `shop_checkout`. Diese Seite
erklärt, was das Ereignis mit den Gateways tut, wie eine Weiterleitungszahlung zurückkommt und wo Sie nachsehen, wenn
das nicht geschieht.

## Inhalt

1. [Verfügbar, ausgewählt und aktuell](#verfügbar-ausgewählt-und-aktuell)
2. [Wenn mehrere Gateways ausgewählt sind](#wenn-mehrere-gateways-ausgewählt-sind)
3. [Weiterleitungs-Gateways](#weiterleitungs-gateways)
4. [Transparente Gateways](#transparente-gateways)
5. [Der Rückruf](#der-rückruf)
6. [Logs](#logs)
7. [Checkliste](#checkliste)

## Verfügbar, ausgewählt und aktuell

Der Ereignistyp (`kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php`) kennt drei Mengen:

| Menge | Bedeutung |
|---|---|
| Verfügbar | Jedes Gateway, das geladen wurde und `registerGateway()` aufgerufen hat ([paymentgateways.ini durchgegangen](../settings/paymentgateways-ini-durchgegangen.md)) |
| Ausgewählt | Die Gateways, die Sie beim Bearbeiten des Ereignisses im Workflow ankreuzen |
| Aktuell | Das Gateway, über das diese Zahlung läuft |

## Wenn mehrere Gateways ausgewählt sind

Ist ein Gateway ausgewählt, wird es verwendet. Sind es mehrere, zeigt das Ereignis das Template
`workflow/selectgateway.tpl`, und der Kunde wählt eines; der Workflow läuft dann mit diesem Gateway weiter. Gestalten
Sie dieses Template wie den Rest der Kasse und geben Sie jeder Wahl eine klare Beschriftung
([Barrierefreiheit im Shop](../betrieb/barrierefreiheit-im-shop.md)).

## Weiterleitungs-Gateways

Ein Weiterleitungs-Gateway erweitert `eZRedirectGateway` (`kernel/shop/classes/ezredirectgateway.php`), wie es
`ezpaypal` tut:

1. Erster Durchlauf: Es erstellt ein **Zahlungsobjekt** (`eZPaymentObject`: ID des Workflow-Prozesses, Bestell-ID,
   Zahlungsart, Status „nicht genehmigt“), speichert es, baut die Adresse des Anbieters und gibt
   `STATUS_REDIRECT_REPEAT` zurück. Der Kunde wird zum Anbieter geschickt; der Workflow-Prozess wartet.
2. Der Anbieter ruft die Website zurück (siehe unten), was das Zahlungsobjekt genehmigt und den Workflow fortsetzt.
3. Zweiter Durchlauf: Das Zahlungsobjekt ist genehmigt, also gibt das Gateway `STATUS_ACCEPTED` zurück; andernfalls
   `STATUS_REJECTED`.
4. Die Kassenoperation läuft weiter: Aktivierung, Bestellmail, Nachher-Trigger.

Eine Zahlung, die der Kunde abbricht, hinterlässt ein Zahlungsobjekt; `cleanup()` des Gateways entfernt es, wenn der
Workflow-Prozess aufgeräumt wird.

## Transparente Gateways

Ein transparentes Gateway erweitert `eZPaymentGateway` direkt (`ezauthorize` tut es über `eZCurlGateway`). Es zeigt ein
Kartenformular auf Ihrer Website, sendet die Abbuchung von Ihrem Server und antwortet in derselben Anfrage mit
angenommen oder abgelehnt. Es ist leichter nachzuvollziehen, aber Kartendaten laufen über Ihren Server; lesen Sie
[Kartenzahlungen und PCI-Grundlagen](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md), bevor Sie eines wählen.

## Der Rückruf

`eZPaymentCallbackChecker` (`kernel/shop/classes/ezpaymentcallbackchecker.php`) ist die Basis für den Rückruf des
Anbieters. Seine Methoden zeigen, was ein sorgfältiger Rückruf tut:

| Methode | Prüft |
|---|---|
| `requestValidation()` | Bittet den Anbieter zu bestätigen, dass die Nachricht echt ist |
| `checkServerIP()` | Die Anfrage kommt von den Servern des Anbieters |
| `checkAmount()`, `checkCurrency()` | Betrag und Währung passen zur Bestellung |
| `checkDataField()` | Ein Feld hat den erwarteten Wert |
| `setupOrderAndPaymentObject()` | Lädt die Bestellung und ihr Zahlungsobjekt |
| `approvePayment()` | Genehmigt das Zahlungsobjekt und setzt den Workflow fort (`eZPaymentObject::continueWorkflow()`) |

Bei `ezpaypal` ist die Rückrufadresse `paypal/notify_url`, von Richtlinienprüfungen ausgenommen, damit der Anbieter sie
ohne Anmeldung erreicht. Die Adresse muss aus dem Internet über HTTPS erreichbar sein und darf nicht durch eine
Anmeldung, eine Wartungsseite oder einen IP-Filter blockiert werden. Ist der Prüfpfad aktiv, zeichnet das Genehmigen
einer Zahlung `commerce.payment.approve` auf ([Prüfpfad für den Handel](../betrieb/audit-trail-fuer-den-handel.md)).

## Logs

| Datei | Geschrieben von |
|---|---|
| `var/log/eZPaymentGatewayType.log` | Dem Ereignis: Ausführung, Gateway-Auswahl, Fehler |
| `var/log/eZRedirectGateway.log` | Weiterleitungs-Gateways: Zahlungsobjekt, Weiterleitung, angenommen oder abgelehnt |

Lesen Sie sie zusammen mit der Liste der Workflow-Prozesse (**Setup > Workflow-Prozesse**), die einen noch wartenden
Zahlungsprozess zeigt ([Fehlersuche an der Kasse](../betrieb/fehlersuche-an-der-kasse.md)).

## Checkliste

- [ ] Das Ereignis liegt an `shop` `checkout` **vorher**.
- [ ] Im Ereignis sind nur die gewünschten Gateways ausgewählt.
- [ ] Beim Testen ist der Testmodus oder die Sandbox des Anbieters eingestellt, und vor dem Start wird zurückgestellt.
- [ ] Die Rückrufadresse antwortet von außerhalb Ihres Netzwerks.
- [ ] Der bei der Zahlung gesetzte Status existiert in Ihrer Statusliste ([PayPal-Zahlungen](../../unternehmen/shops/zahlungen-mit-paypal.md)).
- [ ] Eine abgebrochene Zahlung hinterlässt keine aufgegebene Bestellung und lässt den Kunden es erneut versuchen.

## Verwandte Seiten

- [Kasse und Workflows](README.md)
- [Entwicklung von Zahlungs-Gateways](../erweitern/entwicklung-von-zahlungs-gateways.md)
- [Zahlungs-Gateways (Website-Ersteller)](../../ersteller/website-ersteller/payment-gateways.md)
- [Zahlungs-Gateways für Unternehmen](../../unternehmen/shops/zahlungsanbieter-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
