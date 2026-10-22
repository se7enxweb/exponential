---
title: Das Ereignis einfacher Versand
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 45
summary: Das Workflow-Ereignis für pauschalen Versand in Exponential im Detail - was eZSimpleShippingType einer Bestellung hinzufügt, Währungsumrechnung, warum es nur einmal hinzugefügt wird, seine USt., seine Grenzen und wann Sie es durch einen Versand-Handler ersetzen.
keywords: einfacher Versand, ezsimpleshipping, eZSimpleShippingType, ShippingCost, ShippingDescription, Pauschalversand, Bestellposten, USt. auf Versand, convertAdditionalPrice, before_shop_confirmorder, Versand-Handler, simple shipping, flat shipping, order item, shipping VAT, shipping handler
type: help
---
# Das Ereignis einfacher Versand

Diese Seite ist für Entwickler und Händler, die den pauschalen Versand nutzen, der mit dem Kernel ausgeliefert wird.
Das Ereignis **Simple shipping** (`ezsimpleshipping`,
`kernel/classes/workflowtypes/event/ezsimpleshipping/ezsimpleshippingtype.php`) fügt jeder Bestellung eine Versandzeile
mit einem festen Betrag hinzu. Die geschäftliche Sicht auf den Versand steht unter
[Versand und Bearbeitung](../../unternehmen/shops/versand-und-bearbeitung.md); diese Seite beschreibt genau, was das
Ereignis tut.

## Inhalt

1. [Was das Ereignis tut](#was-das-ereignis-tut)
2. [Währung](#währung)
3. [USt. auf der Versandzeile](#ust-auf-der-versandzeile)
4. [Was es nicht kann](#was-es-nicht-kann)
5. [Templates](#templates)
6. [Wann Sie zu einem Versand-Handler wechseln](#wann-sie-zu-einem-versand-handler-wechseln)

## Was das Ereignis tut

1. Es darf nur mit `shop` `confirmorder` vorher verbunden werden; die Klasse beschränkt sich selbst mit
   `setTriggerTypes()`.
2. Wenn die Bestätigungsseite vorbereitet wird, liest es `workflow.ini [SimpleShippingWorkflow] ShippingCost` und
   `ShippingDescription`.
3. Es sieht sich die Posten der Bestellung an. Ist bereits einer vom Typ `ezsimpleshipping` vorhanden, tut es nichts;
   ein erneutes Laden der Bestätigungsseite fügt den Versand also nicht doppelt hinzu.
4. Andernfalls fügt es einen **Bestellposten** (`eZOrderItem`) mit der Beschreibung, dem Betrag und dem Typ
   `ezsimpleshipping` hinzu und nimmt an.

Bestellposten sind von Produktposten getrennt: Die Summen der Bestellung rechnen sie ein (`orderTotalIncVAT()`,
`orderTotalExVAT()` in `kernel/classes/ezorder.php`), und Templates listen sie über `order_items` der Bestellung auf.

## Währung

Der Betrag ist in der Währung des Shops. Bevor er gespeichert wird, wird er mit
`eZShopFunctions::convertAdditionalPrice()` in die Währung der Bestellung umgerechnet; ein Kunde, der in einer anderen
Währung einkauft (mit `ezmultiprice`), sieht also den umgerechneten Betrag.

## USt. auf der Versandzeile

Das Ereignis legt den Bestellposten nur mit Beschreibung, Preis und Typ an; der Posten behält daher die Vorgaben der
Tabelle für die USt. (`vat_value` 0, `is_vat_inc` 0 in `ezorder_item`): Die Versandzeile trägt keine USt. Ob der Versand
in Ihrem Land USt. tragen muss und wie sie ausgewiesen werden muss, klären Sie mit Ihrer Steuerberatung. Muss der Versand
USt. tragen, oder USt., die von den Produkten abhängt, verwenden Sie einen Versand-Handler: Sein `getShippingInfo()`
liefert `vat_value` und `is_vat_inc` pro Versandposten ([Eigener Versand-Handler](../erweitern/eigener-versand-handler.md)).

## Was es nicht kann

| Bedarf | Einfacher Versand |
|---|---|
| Kostenloser Versand ab einem Betrag | Nein |
| Tarife nach Land oder Zone | Nein |
| Tarife nach Gewicht oder Größe | Nein |
| Mehrere Versandarten zur Wahl für den Kunden | Nein |
| Abholung ohne Versandkosten | Nein (die Zeile wird immer hinzugefügt) |
| Ein Pauschalbetrag pro Bestellung | Ja |

## Templates

Die Templates für Bestätigung, Bestellansicht und Bestellmail des Standarddesigns listen Bestellposten auf, die
Versandzeile erscheint also ohne Arbeit an den Templates. Wenn Sie eigene Kassen-Templates bauen, listen Sie
`$order.order_items` mit Beschreibung und Preis jedes Postens auf.

## Wann Sie zu einem Versand-Handler wechseln

Wechseln Sie, sobald eine der „Nein“-Zeilen oben für Ihre Kunden wichtig ist. Ein Handler wird in
`shop.ini [ShippingSettings] Handler` gewählt und berechnet den Versand aus dem Warenkorb. Entfernen Sie dann den
Workflow für einfachen Versand vom Trigger, damit Kunden nicht doppelt belastet werden. Der RAD-Assistent für Handler hat
ein Rezept für einen Versand-Handler ([Pfad für Entwickler](../rollen/pfad-fuer-entwickler.md#generatoren-die-zeit-sparen)).

## Verwandte Seiten

- [Kasse und Workflows](README.md)
- [workflow.ini für den Shop](../einstellungen/workflow-ini-fuer-den-shop.md)
- [Versand und Bearbeitung (Ihren Shop betreiben)](../../kunden/handel/versand-und-abwicklung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
