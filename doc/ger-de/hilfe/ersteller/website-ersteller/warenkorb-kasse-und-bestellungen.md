---
title: Warenkorb, Kasse und Bestellungen
audience: website-creator, admin
category: website-creation
order: 540
summary: Der Weg der Besucher durch den Shop im Detail - Formulare zum Hinzufügen in den Warenkorb, der Warenkorb, die Kundenregistrierung, die Bestätigung, die Workflow-Trigger der Kasse, Bestellstatus, Quittungen, Bestell-Mails und die Bestellverwaltung.
keywords: Warenkorb, In den Warenkorb, Kasse, Bestellung, Bestellstatus, Bestellquittung, Bestell-Mail, Workflow-Trigger, Website-Ersteller, basket, add to basket, checkout, confirmorder, shop/register, order, order status, order receipt, orderreceipt, order mail, workflow trigger, shop_checkout, website creator
type: help
---
# Warenkorb, Kasse und Bestellungen

Diese Seite richtet sich an Website-Ersteller, die den Kaufweg eines Shops bauen, von der Schaltfläche „Zum Warenkorb
hinzufügen“ bis zur Bestellung, die das Shop-Personal bearbeitet.

## Inhalt

1. [Zum Warenkorb hinzufügen](#zum-warenkorb-hinzufügen)
2. [Der Warenkorb](#der-warenkorb)
3. [Kasse](#kasse)
4. [Workflow-Trigger](#workflow-trigger)
5. [Bestellungen](#bestellungen)
6. [Checkliste für eine Testbestellung](#checkliste-für-eine-testbestellung)

## Zum Warenkorb hinzufügen

Das Full-Template des Produkts sendet an `content/action` mit der Schaltfläche `ActionAddToBasket` (das Formular der
Produkt-Templates der Themes ezwebin und simple):

```text
<form method="post" action={'content/action'|ezurl}>
  <input type="hidden" name="ContentNodeID" value="{$node.node_id}">
  <input type="hidden" name="ContentObjectID" value="{$node.object.id}">
  <input type="hidden" name="ViewMode" value="full">
  <button type="submit" name="ActionAddToBasket">{'Add to basket'|i18n( 'design/mysite' )}</button>
</form>
```

Optionen werden mitgesendet, wenn das Produkt Optionsfelder hat.

## Der Warenkorb

`shop/basket` (umbenennbar mit `[BasketSettings] BasketViewName`, siehe
[Name der Warenkorb-Ansicht](../../../bc/6.0/view-name-des-shop-warenkorbs.md)) listet die Artikel auf; `shop/updatebasket`
ändert die Mengen. Das Pagelayout kann eine Warenkorb-Anzahl aus `fetch( shop, basket )` anzeigen, wie es das Theme
simple tut. Alte Warenkörbe entfernt der Cronjob-Teil `basket_cleanup`.

## Kasse

| Schritt | Ansicht | Template |
|---|---|---|
| Kundendaten | `shop/register` (oder `shop/userregister`) | `shop/register.tpl`, `shop/userregister.tpl` |
| Bestätigung mit Versand und Mehrwertsteuer | `shop/confirmorder` | `shop/confirmorder.tpl` |
| Kasse und Zahlung | `shop/checkout` | die Seiten des Gateways |
| Die Bestellung | `shop/orderview` oder `shop/orderreceipt/<token>` | `shop/orderview.tpl` |

Wie Kundendaten behandelt werden, entscheidet der Shop-Account-Handler (`shopaccount.ini`).

## Workflow-Trigger

`settings/workflow.ini` bietet Shop-Vorgänge als Trigger an: `shop_addtobasket`, `shop_updatebasket`,
`before_shop_confirmorder` und `shop_checkout`. Ein Ereignis für ein Payment-Gateway kommt an `shop_checkout` (davor);
Versandkosten an `before_shop_confirmorder` ([Workflows](../../../anleitungen/workflows.md)).

## Bestellungen

- **Shop** öffnet das Dashboard; die Bestellliste (`shop/orderlist`) und die Bestellansicht (`shop/orderview`) sind
  einen Klick entfernt.
- Jede Bestellung hat einen Status; das Release mit dem Dashboard hat für jede Stufe einen Status hinzugefügt
  ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)).
- Bestellungen können archiviert (`shop/archiveorder`) und entfernt werden.
- **Dauerhafte Quittungen**: Mit `[OrderViewSettings] OrderLinkView=orderreceipt` in `shop.ini` verlinken die
  Bestätigungsseite und die Mail auf `shop/orderreceipt/<token>` statt auf `shop/orderview`
  ([Dauerhafte Bestellquittungen](../../../funktionen/6.0/bestellbelege.md)).
- Kunden: `shop/customerlist`; Statistik: `shop/statistics`.

## Checkliste für eine Testbestellung

- [ ] Von einer Produktseite in den Warenkorb legen, die Menge ändern, einen Artikel entfernen.
- [ ] Als Besucher und als angemeldeter Kunde zur Kasse gehen.
- [ ] Mehrwertsteuer und Versand auf der Bestätigungsseite stimmen für zwei Länder.
- [ ] Die Zahlung im Testmodus des Gateways gelingt und scheitert wie erwartet.
- [ ] Die Bestellung erscheint unter Shop mit dem richtigen Status; die Mail kommt an.
- [ ] Der Quittungslink funktioniert für den Kunden und nicht für andere.

## Verwandte Seiten

- [Einen Shop betreiben: Überblick](shop-ueberblick.md)
- [Payment-Gateways für Website-Ersteller](payment-gateways.md)
- [Sortierung der Bestellliste](../../../funktionen/6.0/sortierung-der-bestellliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
