---
title: Spendenartikel im Shop, Schritt für Schritt
audience: non-profit, businesses, website-creator, admin
category: non-profits
order: 40
summary: Spendenartikel im eingebauten Shop von Exponential anlegen - Produkte mit festem Betrag aus der mitgelieferten Produktklasse, die Mehrwertsteuer nach Rat Ihres Steuerberaters, das Gateway ezpaypal (paymentgateways.ini, paypal.ini Business, Sandbox, der Checkout-Workflow) oder ezauthorize, Bestellbelege, die Prüfung im Shop-Dashboard, die nötigen Tests und die wöchentliche Routine - mit offen benannten Grenzen.
keywords: Spendenartikel, Spende im Shop, Spendenprodukt, fester Betrag, Produktklasse, Mehrwertsteuer, Zahlungs-Gateway, Bestellbelege, Shop-Dashboard, shop donation, donation product, fixed amount, product class, ezprice, ezpaypal, ezauthorize, paymentgateways.ini, paypal.ini, sandbox, shop_checkout, workflow, order receipts, store dashboard, PaymentMadeOrderStatusID
type: help
---
# Spendenartikel im Shop, Schritt für Schritt

Diese Seite richtet sich an Website-Ersteller oder Partner, die Weg B umsetzen: Spenden als Produkte mit festem Betrag
im eingebauten Shop. Lesen Sie zuerst [Spendenwege im Überblick](spendenwege-im-ueberblick.md); dieser Weg hat
Grenzen, die ein Fundraiser akzeptieren muss.

> **Hinweis**
> Die Grenzen: Spender wählen aus festen Beträgen (oder von Ihnen festgelegten Optionen), nicht einen beliebigen
> Betrag; wiederkehrende Zahlungen sind für den Shop und seine Gateways nicht dokumentiert; der Beleg des Shops ist ein
> Bestellbeleg, keine Zuwendungsbestätigung; und Spender durchlaufen einen Warenkorb und einen Bestellabschluss, was sich
> wie ein Kauf anfühlt.

## Inhalt

1. [Schritt 1: die Spendenprodukte](#schritt-1-die-spendenprodukte)
2. [Schritt 2: Mehrwertsteuer](#schritt-2-mehrwertsteuer)
3. [Schritt 3: das Zahlungs-Gateway](#schritt-3-das-zahlungs-gateway)
4. [Schritt 4: Belege und E-Mails](#schritt-4-belege-und-e-mails)
5. [Schritt 5: testen](#schritt-5-testen)
6. [Die wöchentliche Routine](#die-wöchentliche-routine)
7. [Checkliste](#checkliste)

## Schritt 1: die Spendenprodukte

1. Legen Sie für die Artikel einen Ordner **Spenden** an.
2. Legen Sie pro Betrag ein Objekt der mitgelieferten Klasse `product` an, zum Beispiel „Spende über 10“, „Spende über
   25“, „Spende über 50“, jeweils mit seinem Preis (`ezprice`, oder `ezmultiprice` für mehrere Währungen)
   ([Produkte, Preise und Mehrwertsteuer](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md),
   [Ein Produkt hinzufügen](../../kunden/handel/ein-produkt-anlegen.md)).
3. Beschreiben Sie in der Beschreibung, was jeder Betrag bewirkt.
4. Optionen wie `ezoption` oder `ezrangeoption` können den Preis verändern, aber nur auf von Ihnen festgelegte Werte;
   einen frei wählbaren Betrag gibt es nicht ([Spenden- und Fundraising-Seiten](../../unternehmen/gemeinnuetzige/spenden-und-spendenaufrufe.md#möglichkeit-b-spendenartikel-im-eingebauten-shop)).
5. Verlinken Sie den Ordner von Ihrer [Spendenseite](die-spendenseite.md).

## Schritt 2: Mehrwertsteuer

Spenden werden steuerlich meist anders behandelt als Verkäufe, aber es gelten die Regeln Ihres Landes. Stellen Sie den
Mehrwertsteuertyp der Spendenartikel so ein, wie Ihr Steuerberater es empfiehlt
([Mehrwertsteuertypen und -regeln](../../kunden/handel/ust-typen-und-ust-regeln.md),
[Checkliste Mehrwertsteuer](../../kunden/handel/mehrwertsteuer-checkliste.md)).

## Schritt 3: das Zahlungs-Gateway

**Mit `ezpaypal` (Weiterleitung zu PayPal):**

1. Aktivieren Sie die Erweiterung.
2. Legen Sie `settings/override/paypal.ini.append.php` an und setzen Sie mindestens `[PaypalSettings] Business`, Ihr
   PayPal-Geschäftskonto.
3. Lassen Sie `[ServerSettings] ServerName` während der Tests auf die Sandbox von PayPal zeigen.
4. Fügen Sie das PayPal-Workflow-Ereignis dem Checkout-Workflow hinzu, verbunden mit dem Trigger `shop_checkout`
   ([Workflows](../../../anleitungen/workflows.md)).
5. Die Benachrichtigungs-URL (`/paypal/notify_url`) „muss aus dem Internet erreichbar sein und darf keine Anmeldung
   verlangen“.
6. `[OrderSettings] PaymentMadeOrderStatusID` (`1000`) ist der Status, der bei erfolgter Zahlung gesetzt wird.

Quelle: [Zahlungs-Gateways für Website-Ersteller](../../ersteller/website-ersteller/payment-gateways.md#paypal-einrichten),
[ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md).

**Mit `ezauthorize` (Kartenformular zu Authorize.Net):** Die eigene Dokumentation der Erweiterung liegt in
`extension/ezauthorize/`. Die Kartendaten werden auf Ihrer Site eingegeben; lesen Sie zuerst
[Kartenzahlungen und PCI-Grundlagen](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md).

Die aktiven Gateways stehen in `settings/paymentgateways.ini [GatewaysSettings] AvailableGateways[]`.

## Schritt 4: Belege und E-Mails

- Schalten Sie Bestellbelege ein, damit Spender ihren Beleg jederzeit abrufen können ([Bestellbelege](../../../funktionen/6.0/bestellbelege.md)).
- Prüfen Sie, ob sich die Bestellbestätigung per E-Mail wie eine Spende liest und nicht wie ein Kauf ([Bestellbestätigung per E-Mail](../../kunden/handel/bestellbestaetigung-per-e-mail.md)).
- Wenn Ihr Land eine Spenden- oder Zuwendungsbestätigung verlangt, stellen Sie diese außerhalb von Exponential aus, so
  wie Ihr Steuerberater es empfiehlt.

## Schritt 5: testen

Aus [Zahlungs-Gateways, Testen](../../ersteller/website-ersteller/payment-gateways.md#testen):

- [ ] Eine Zahlung im Testmodus des Anbieters wird abgeschlossen, und die Bestellung gilt als bezahlt.
- [ ] Eine abgebrochene Zahlung lässt die Bestellung unbezahlt, und der Spender kann es erneut versuchen.
- [ ] Der Callback funktioniert über HTTPS und durch einen etwaigen Proxy.
- [ ] Das Shop-Dashboard meldet nicht mehr, dass es beim Bestellabschluss keinen Zahlungsschritt gibt ([Shop-Dashboard](../../../funktionen/6.0/shop-dashboard.md)).

Wechseln Sie danach auf den Live-Server, leisten Sie eine kleine echte Spende und erstatten Sie sie zurück.

## Die wöchentliche Routine

| Aufgabe | Lesen |
|---|---|
| Bezahlte Bestellungen mit dem Kontoauszug des Anbieters abgleichen | [Bestellungen im Alltag](../../kunden/handel/bestellungen-im-alltag.md) |
| Bestellungen mit fehlgeschlagener Zahlung ansehen | [Zahlungs-Gateways für Inhaber](../../kunden/handel/zahlungsanbieter.md) |
| Fragen von Spendern zu Belegen beantworten | [Rechnungen und Belege](../../kunden/handel/rechnungen-und-belege.md) |
| Eine Erstattungsanfrage bearbeiten | [Rücksendungen und Erstattungen](../../kunden/handel/retouren-und-erstattungen.md) |

Wiederholen Sie nach jedem Update von Exponential oder einer Erweiterung den Test aus Schritt 5.

## Checkliste

- [ ] Spendenprodukte mit klaren Beschreibungen angelegt.
- [ ] Mehrwertsteuer nach Empfehlung eingestellt.
- [ ] Gateway eingerichtet, in der Sandbox getestet, danach live mit einer kleinen Spende getestet.
- [ ] Bestellbelege eingeschaltet; Bestätigungs-E-Mail geprüft.
- [ ] Zuwendungsbestätigungen außerhalb der Site geregelt, falls erforderlich.
- [ ] Die wöchentliche Routine hat einen Namen daneben stehen.

## Verwandte Seiten

- [Spenden](README.md)
- [Überblick über den Shop](../../ersteller/website-ersteller/shop-ueberblick.md)
- [Zahlungen mit PayPal](../../unternehmen/shops/zahlungen-mit-paypal.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
