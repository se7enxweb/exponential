---
title: Die Kassenoperationen Schritt für Schritt
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 41
summary: Die Exponential-Kasse durch den Code verfolgt - addtobasket, updatebasket, die Schaltfläche Kasse und der Konto-Handler, die Prüfung des Warenkorbs, confirmorder mit Land und Versand und checkout mit Währungsprüfung, Zahlung, Aktivierung und Bestellmail - mit jedem benannten Triggerpunkt.
keywords: Kassenoperationen, addtobasket, updatebasket, confirmorder, checkout, operation_definition, pre_checkout, post_checkout, pre_confirmorder, handle-user-country, handle-shipping, activate-order, send-order-email, Warenkorb prüfen, entfernte Posten, checkout operations, basket verify, removed items
type: help
---
# Die Kassenoperationen Schritt für Schritt

Diese Seite ist für Entwickler, die genau wissen müssen, was in jedem Schritt der Kasse geschieht, etwa um ein
Workflow-Ereignis richtig zu platzieren oder eine Bestellung zu verstehen, die falsch aussieht. Sie folgt einem Kunden
von der Produktseite bis zur aufgegebenen Bestellung und nennt die Operation, die Triggerpunkte und die Methoden, die
laufen, aus `kernel/shop/operation_definition.php` und den Shop-Views.

## Inhalt

1. [Schritt 1 In den Warenkorb legen](#schritt-1-in-den-warenkorb-legen)
2. [Schritt 2 Den Warenkorb aktualisieren](#schritt-2-den-warenkorb-aktualisieren)
3. [Schritt 3 Die Schaltfläche Kasse](#schritt-3-die-schaltfläche-kasse)
4. [Schritt 4 Die Bestellung bestätigen](#schritt-4-die-bestellung-bestätigen)
5. [Schritt 5 Zur Kasse gehen und bezahlen](#schritt-5-zur-kasse-gehen-und-bezahlen)
6. [Schritt 6 Zurück vom Anbieter](#schritt-6-zurück-vom-anbieter)
7. [Der gesamte Ablauf](#der-gesamte-ablauf)

## Schritt 1 In den Warenkorb legen

Operation `addtobasket` (Parameter `object_id`, `option_list`, `quantity`, `basket_id`):

1. Trigger `pre_addtobasket` (der Vorher-Trigger von `shop_addtobasket`).
2. Methode `add-to-basket`: Der Posten und seine Optionen werden in der Produktsammlung des Warenkorbs gespeichert.
3. Methode `update-shipping-info`: Der Versand-Handler, falls einer konfiguriert ist, erfährt, dass sich der Warenkorb
   geändert hat.
4. Trigger `post_addtobasket`.

Danach wird der Kunde zum Warenkorb oder zurück geschickt, wie es `site.ini [ShopSettings] RedirectAfterAddToBasket`
festlegt.

## Schritt 2 Den Warenkorb aktualisieren

Operation `updatebasket` (Parameter `item_count_list`, `item_id_list`): Trigger `pre_updatebasket`, Methode
`update-basket`, Trigger `post_updatebasket`. Eine Menge von null oder weniger oder eine, die keine Zahl ist, schickt den
Kunden mit einem Fehler zum Warenkorb zurück (`(error)/invaliditemcount`).

## Schritt 3 Die Schaltfläche Kasse

Bei **Kasse** im Warenkorb (`kernel/private/classes/views/shop/basket.php`):

1. Gesendete Mengen werden gespeichert.
2. Der Shop-Konto-Handler wird gefragt, ob er die Daten des Kunden hat (`verifyAccountInformation()`). Falls nicht,
   schickt er den Kunden zu seinem Formular (`shop/userregister`, `shop/register`) oder zur Anmeldung
   ([shopaccount.ini durchgegangen](../settings/shopaccount-ini-durchgegangen.md)). Dieses Formular legt die Bestellung
   an, wenn es abgeschickt wird.
3. Die Preise werden aktualisiert und der Warenkorb wird **geprüft**: Das Produkt jedes Postens muss noch veröffentlicht
   sein, und sein Preis, die Angabe zur enthaltenen USt. und der USt-Prozentsatz müssen mit dem übereinstimmen, was der
   Warenkorb enthält. Posten, die durchfallen, werden aus dem Warenkorb entfernt und auf der Warenkorbseite aufgeführt
   (`removed_items` in `shop/basket.tpl`).
4. Aus dem Warenkorb wird eine **temporäre Bestellung** erstellt, und ihre ID wird in der Session gehalten
   ([Temporäre Bestellungen und Aktivierung](temporaere-bestellungen-und-aktivierung.md)).
5. Der Kunde wird zu `shop/confirmorder` geschickt.

## Schritt 4 Die Bestellung bestätigen

Operation `confirmorder` (Parameter `order_id`), ausgeführt, wenn die Bestätigungsseite vorbereitet wird:

1. Trigger `pre_confirmorder`: der Trigger `before_shop_confirmorder` in `workflow.ini`. Hier laufen
   Versandereignisse ([Das Ereignis einfacher Versand](das-ereignis-einfacher-versand.md)).
2. Methode `handle-user-country`: Das Land des Kunden wird für die USt. festgelegt.
3. Methode `handle-shipping`: Versandinformationen eines Versand-Handlers werden angewendet.
4. Methode `fetch-order`.

`confirmorder` hat keinen Nachher-Trigger. Die Seite zeigt die Bestellung mit Versand und USt. **Bestätigen** führt
weiter zu `shop/checkout` (und leert jetzt den Warenkorb, wenn `ClearBasketOnCheckout=enabled`); **Abbrechen** kehrt mit
seinen Posten zum Warenkorb zurück. Eine Bestellung, die nicht mehr temporär ist (Zurück-Schaltfläche, ein zweiter
Tab), wird nicht noch einmal bestätigt: Der Kunde wird zu ihrem Beleg oder ihrer Bestellansicht geschickt
([Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md#eine-korrektur-am-bestellvorgang-nebenbei)).

## Schritt 5 Zur Kasse gehen und bezahlen

`shop/checkout` führt die Operation `checkout` aus (Parameter `order_id`):

1. Methode `check-currency`.
2. Trigger `pre_checkout`: der Vorher-Trigger von `shop_checkout`. Hier läuft das Ereignis Zahlungs-Gateway
   ([Das Ereignis Zahlungs-Gateway](das-ereignis-zahlungs-gateway.md)).
3. Methode `activate-order`: Die Bestellung ist nicht mehr temporär und erhält ihre Bestellnummer
   ([Temporäre Bestellungen und Aktivierung](temporaere-bestellungen-und-aktivierung.md)).
4. Methode `send-order-email`: Der Handler für die Bestellbestätigung versendet die Bestellmail.
5. Trigger `post_checkout`: der Nachher-Trigger von `shop_checkout`; hier laufen Ereignisse, die eine aufgegebene
   Bestellung brauchen.

Wenn ein Ereignis an `pre_checkout` weiterleitet (ein Weiterleitungs-Gateway) oder eine Seite anzeigt
(Gateway-Auswahl, ein Kartenformular), stoppt die Operation dort und läuft weiter, wenn der Workflow fortgesetzt wird.
Wird sie abgebrochen, sieht der Kunde `shop/cancelcheckout.tpl`.

## Schritt 6 Zurück vom Anbieter

Wenn der Kunde nach einem Weiterleitungs-Gateway zu `shop/checkout` zurückkehrt, wartet die View bis zu 25 Sekunden und
prüft alle 2 Sekunden, ob der Rückruf des Anbieters die Bestellung aktiviert hat. Ist die Bestellung aktiviert, wird
der Kunde zur Bestellansicht oder zum Beleg geschickt. Andernfalls lässt `shop/checkoutagain.tpl` den Kunden es erneut
versuchen, bis zu drei Mal; danach wird `shop/cancelcheckout.tpl` mit dem Fehlercode `NO_CALLBACK` angezeigt
(`kernel/private/classes/views/shop/checkout.php`). Das ist das häufigste Symptom eines Rückrufs, der die Website nicht
erreicht ([Fehlersuche an der Kasse](../betrieb/fehlersuche-an-der-kasse.md)).

## Der gesamte Ablauf

```text
shop/add          addtobasket:  pre_addtobasket > add-to-basket > update-shipping-info > post_addtobasket
shop/basket       updatebasket: pre_updatebasket > update-basket > post_updatebasket
Checkout button   account handler > verify basket > temporary order > shop/confirmorder
shop/confirmorder confirmorder: pre_confirmorder > handle-user-country > handle-shipping > fetch-order
Confirm           shop/checkout
shop/checkout     checkout:     check-currency > pre_checkout > activate-order > send-order-email > post_checkout
```

## Verwandte Seiten

- [Kasse und Workflows](README.md)
- [Workflows an Shop-Triggern](workflows-an-shop-triggern.md)
- [Ablauf von Warenkorb und Kasse](../../unternehmen/shops/warenkorb-und-bestellablauf.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
