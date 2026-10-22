---
title: Zahlungsanbieter und der Shop
audience: integrator, developer
category: integration
order: 250
summary: Das Shop-Modul mit Zahlungsanbietern und ERP-Systemen verbinden - wie sich Zahlungsgateways in den Ablauf der Bestellung einfügen, ein eigenes Gateway registrieren, die Erweiterung ezpaypal als Vorbild, Bestellungen beim Abschluss an ein ERP übergeben, Bestellungen aus PHP lesen und die Audit-Ereignisse des Handels.
keywords: Shop, Zahlungsgateway, Zahlungsanbieter, Bestellung, ERP, Bestellexport, Audit, shop, payment gateway, paymentgateways.ini, eZPaymentGateway, eZRedirectGateway, eZPaymentCallbackChecker, eZPaymentGatewayType, registerGateway, event_ezpaymentgateway, ezpaypal, checkout, confirmorder, eZOrder, ERP, order export, commerce audit
type: help
---
# Zahlungsanbieter und der Shop

Diese Seite richtet sich an Integratoren, die das Shop-Modul von Exponential mit einem Zahlungsanbieter verbinden oder
Bestellungen an ein ERP übergeben. Der Shop führt seinen Bestellabschluss über Operationen mit Triggern aus
(`shop / checkout`, `shop / confirmorder`), und die Zahlung ist ein **Workflow-Ereignis** (*Zahlungsgateway*,
`event_ezpaymentgateway`), das an das in `paymentgateways.ini` gewählte Gateway übergibt.

## Inhalt

1. [Wie die Zahlung in den Bestellabschluss passt](#wie-die-zahlung-in-den-bestellabschluss-passt)
2. [Ein eigenes Gateway](#ein-eigenes-gateway)
3. [Bestellungen an ein ERP übergeben](#bestellungen-an-ein-erp-übergeben)
4. [Bestellungen aus PHP lesen](#bestellungen-aus-php-lesen)
5. [Audit-Ereignisse des Shops](#audit-ereignisse-des-shops)
6. [Checkliste](#checkliste)

## Wie die Zahlung in den Bestellabschluss passt

```text
 basket -> shop/confirmorder (trigger before) -> shop/checkout (trigger)
                                                   |
                                                   v
                                     workflow with the event "Payment gateway"
                                                   |
                         gateway from paymentgateways.ini (for example ezpaypal)
                                                   |
                         customer redirected to the provider, provider calls back
                                                   |
                                     order status set, checkout continues
```

| Teil | Wo |
|---|---|
| Liste der Gateways | `settings/paymentgateways.ini [GatewaysSettings] AvailableGateways[]`, `GatewaysDirectories[]` (Standard `kernel/shop/paymentgateways`) |
| Basisklassen | `eZPaymentGateway` und `eZRedirectGateway` (`kernel/shop/classes/`), `eZPaymentCallbackChecker` für den Rückruf des Anbieters |
| Registrierung | `eZPaymentGatewayType::registerGateway( $gateway, $class_name, $description )` (`kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php`) |
| Ein echtes Gateway | `ezpaypal` (erforderlich als `~1.2.3`): `classes/ezpaypalgateway.php` registriert sich mit `registerGateway(...)`; das Modul `paypal` beantwortet den Rückruf unter `/paypal/notify_url` |
| Klassenreferenz | `doc/eng-us/shop/index.html` (die Klassendokumentation des Shops) |

So verwenden Sie ein Gateway: Aktivieren Sie es, konfigurieren Sie es (für `ezpaypal`:
`settings/override/paypal.ini.append.php`, `Business`, beim Testen den Sandbox-`ServerName`), legen Sie einen Workflow
mit dem Ereignis *Zahlungsgateway* an und verbinden Sie ihn unter **Setup > Trigger** mit dem Bestellabschluss
([ezpaypal](../../funktionen/6.0/erweiterungen/ezpaypal.md)).

## Ein eigenes Gateway

Folgen Sie dem Aufbau von `ezpaypal`:

1. Eine Klasse, die `eZRedirectGateway` (für Anbieter, die den Kunden umleiten) oder `eZPaymentGateway` erweitert.
2. Am Ende der Klassendatei `eZPaymentGatewayType::registerGateway( '<gateway id>', '<class name>', '<label>' );`
3. Das Verzeichnis des Gateways in `paymentgateways.ini [GatewaysSettings] GatewaysDirectories[]` und seine ID in
   `AvailableGateways[]`, in den Einstellungen Ihrer Erweiterung.
4. Eine Modulansicht für den Rückruf des Anbieters (Notify-URL), die den Rückruf mit einer Klasse prüft, die
   `eZPaymentCallbackChecker` erweitert: Prüfen Sie die Signatur des Anbieters, den Betrag und die Währung gegen die
   Bestellung, und setzen Sie erst dann den Bestellstatus.
5. Testen Sie mit der Sandbox des Anbieters; schicken Sie nie echte Kartendaten durch eine Entwicklungsinstallation.

> **Hinweis**
> Zahlungsintegrationen bringen Pflichten der Kartensysteme und rechtliche Pflichten mit sich (PCI DSS, starke
> Kundenauthentifizierung). Integrationen mit Umleitung oder gehosteten Feldern halten Kartendaten von Exponential
> fern und sind die übliche Wahl. Dies ist keine Rechts- oder Compliance-Beratung.

## Bestellungen an ein ERP übergeben

Verwenden Sie das Outbox-Muster
([Workflows und Trigger](workflows-und-trigger-als-hooks.md#das-outbox-muster-schnell-vermerken-später-senden)): Ein
Workflow-Ereignis beim Bestellabschluss hält die Bestell-ID fest (den Parameter `order_id` der Operationen), und ein
Cronjob-Teil sendet die Bestellung mit Wiederholungen an das ERP. Ein Ereignis bei `shop / confirmorder` (vorher) kann
das ERP auch nach dem Bestand fragen und `STATUS_REJECTED` zurückgeben, wenn ein Artikel nicht verfügbar ist.

## Bestellungen aus PHP lesen

```php
<?php
$order = eZOrder::fetch( $orderID );
if ( $order instanceof eZOrder )
{
    $payload = array(
        'order_id' => (int)$order->attribute( 'id' ),
        'customer' => $order->accountName(),
        'email'    => $order->accountEmail(),
        'currency' => $order->currencyCode(),
        'total'    => $order->totalIncVAT(),
        'items'    => array(),
    );
    foreach ( $order->productItems() as $item )
    {
        $payload['items'][] = $item;   // per item: the product object, count, price; map to the ERP's article numbers
    }
}
```

Ordnen Sie jedes Produkt über die `remote_id` des Produktobjekts, die Ihr Produktimport setzt, der Artikelnummer des
ERP zu ([Klassen externen Daten zuordnen](inhaltsklassen-auf-externe-daten-abbilden.md)). `accountInformation()` liefert
die Adressdaten des Kunden so, wie der Kontohandler des Shops sie gespeichert hat.

## Audit-Ereignisse des Shops

Der Kanal `commerce` zeichnet standardmäßig das Entfernen von Bestellungen sowie Änderungen an Mehrwertsteuer,
Währungen und Rabatten auf; `commerce.order.create`, `commerce.order.status` und `commerce.payment.approve` existieren
und sind standardmäßig **aus**. Schalten Sie sie in `[AuditEventSettings] Enabled[]` ein, wenn die ERP-Integration eine
Spur braucht, oder um sie an den Audit-Webhook zu leiten
([Ausgehende Webhooks](ausgehende-webhooks-aus-dem-audit-trail.md)).

## Checkliste

- [ ] Gateway durchgehend in der Sandbox des Anbieters getestet, einschließlich abgebrochener und fehlgeschlagener
      Zahlungen.
- [ ] Rückruf geprüft (Signatur, Betrag, Währung, Bestell-ID), bevor sich der Bestellstatus ändert.
- [ ] Bestellungen aus einem Cronjob mit Wiederholungen an das ERP gesendet; das ERP lehnt Duplikate anhand der
      Bestell-ID ab.
- [ ] Produkt-IDs über `remote_id` zugeordnet, nicht über die Objekt-ID.
- [ ] Bestelldaten als personenbezogene Daten behandelt ([Kundendaten schützen](../partner/kundendaten-schuetzen.md)).

## Verwandte Seiten

- [ezpaypal](../../funktionen/6.0/erweiterungen/ezpaypal.md)
- [Store-Dashboard](../../funktionen/6.0/shop-dashboard.md), [Bestellbelege](../../funktionen/6.0/bestellbelege.md)
- [Workflows und Trigger als Integrations-Hooks](workflows-und-trigger-als-hooks.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
