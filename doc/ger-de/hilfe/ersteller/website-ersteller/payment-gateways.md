---
title: Payment-Gateways für Website-Ersteller
audience: website-creator, developer
category: website-creation
order: 550
summary: Zahlungen im Shop annehmen - wie Gateways sich in den Kassen-Workflow einfügen, die mitgelieferten Erweiterungen für PayPal und Authorize.Net, paymentgateways.ini, Testmodus, Callbacks und was ein eigenes Gateway braucht.
keywords: Zahlung, Payment-Gateway, Zahlungsanbieter, Kassen-Workflow, Callback, Testmodus, Website-Ersteller, payment, payment gateway, PayPal, ezpaypal, ezauthorize, Authorize.Net, paymentgateways.ini, checkout workflow, callback, notify URL, test mode, website creator
type: help
---
# Payment-Gateways für Website-Ersteller

Diese Seite richtet sich an Website-Ersteller, die einen Shop mit einem Zahlungsanbieter verbinden. In Exponential ist
ein Payment-Gateway ein **Workflow-Ereignis** an der Kasse: Wenn der Kunde zur Kasse geht, schickt das Ereignis ihn zum
Anbieter (oder belastet direkt) und markiert die Bestellung als bezahlt, wenn der Anbieter bestätigt.

## Inhalt

1. [Wie sich die Zahlung in die Kasse einfügt](#wie-sich-die-zahlung-in-die-kasse-einfügt)
2. [Die Gateways, die Sie verwenden können](#die-gateways-die-sie-verwenden-können)
3. [PayPal einrichten](#paypal-einrichten)
4. [Testen](#testen)
5. [Ein eigenes Gateway](#ein-eigenes-gateway)

## Wie sich die Zahlung in die Kasse einfügt

1. `settings/paymentgateways.ini [GatewaysSettings] AvailableGateways[]` listet die aktiven Gateways auf;
   `GatewaysDirectories[]`, wo ihre Klassen liegen (`kernel/shop/paymentgateways` und Erweiterungen).
2. Ein Workflow mit dem Ereignis für das Payment-Gateway wird mit dem Trigger `shop_checkout` (davor) verbunden
   ([Workflows](../../../anleitungen/workflows.md)).
3. An der Kasse läuft das Ereignis. Ein Weiterleitungs-Gateway schickt den Kunden zum Anbieter; der Anbieter ruft die
   Website zur Bestätigung zurück; die Bestellung erhält ihren Status „bezahlt“.

Die Zahlungsklassen des Kernels sind in `doc/eng-us/shop/` dokumentiert (Gateway, Weiterleitungs-Gateway,
Zahlungsobjekt, Callback-Prüfer und ein PayPal-Beispiel).

## Die Gateways, die Sie verwenden können

| Erweiterung | Anbieter | Lesen |
|---|---|---|
| `ezpaypal` | PayPal (Weiterleitung, NVP-API, Notify-URL `/paypal/notify_url`) | [ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md) |
| `ezauthorize` | Authorize.Net | die eigene Dokumentation in `extension/ezauthorize/` |

## PayPal einrichten

Aus [ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md):

1. Aktivieren Sie die Erweiterung.
2. Legen Sie `settings/override/paypal.ini.append.php` an und setzen Sie mindestens `[PaypalSettings] Business` (Ihr
   PayPal-Geschäftskonto).
3. Testen Sie mit dem Sandbox-Server von PayPal in `[ServerSettings] ServerName`, bevor Sie live gehen.
4. Fügen Sie das PayPal-Workflow-Ereignis dem Kassen-Workflow hinzu.
5. `[OrderSettings] PaymentMadeOrderStatusID` (`1000`) ist der Status, der gesetzt wird, wenn die Zahlung erfolgt ist.

Die Notify-URL muss aus dem Internet erreichbar sein und darf keine Anmeldung verlangen.

## Testen

- [ ] Eine Zahlung im Testmodus des Anbieters wird abgeschlossen, und die Bestellung wird bezahlt.
- [ ] Eine abgebrochene Zahlung lässt die Bestellung unbezahlt, und der Kunde kann es erneut versuchen.
- [ ] Der Callback funktioniert über HTTPS und durch jeden Proxy (prüfen Sie die vertrauenswürdigen Proxys Ihrer
  Umgebung).
- [ ] Das Shop-Dashboard meldet nicht mehr, dass es an der Kasse keinen Zahlungsschritt gibt.

## Ein eigenes Gateway

Ein Gateway ist eine Klasse, die die Gateway-Klassen des Kernels erweitert (`eZPaymentGateway` oder `eZRedirectGateway`
für Anbieter mit Weiterleitung), registriert in `paymentgateways.ini` einer Erweiterung, plus eine Modulansicht für den
Callback des Anbieters. Die Seiten in `doc/eng-us/shop/examples/paypal/` gehen eines Schritt für Schritt durch. Das ist
Arbeit für Anwendungsentwickler: siehe
[Einen Workflow-Ereignistyp schreiben](../anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md).

**Setup > RAD** (`/setup/handlerextension/paymentgateway`, oder `paymentgatewaydirect` für ein Gateway, das die
Zahlung ohne Verlassen der Website annimmt) schreibt das Gerüst. Prüfen Sie, dass die erzeugte Klasse unter
`extension/<name>/paymentgateways/<alias>/classes/<alias>gateway.php` liegt: Der Kernel lädt für jeden Namen in
`AvailableGateways[]` nur `<GatewaysDirectories>/<gateway>/classes/<gateway>gateway.php`. Frühere Versionen des
Werkzeugs schrieben `paymentgateways/<alias>gateway.php`, das nie geladen wird.

## Verwandte Seiten

- [Einen Shop betreiben: Überblick](shop-ueberblick.md)
- [Warenkorb, Kasse und Bestellungen](warenkorb-kasse-und-bestellungen.md)
- [Sicherheit für Anwendungen](../anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
