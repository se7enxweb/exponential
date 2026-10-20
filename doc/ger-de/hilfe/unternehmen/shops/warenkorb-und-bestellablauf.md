---
title: Warenkorb und Bestellablauf
audience: businesses, project-manager, website-creator
category: business-solutions
order: 80
summary: Der Weg des Kunden durch einen Exponential-Shop Schritt für Schritt - in den Warenkorb legen, Warenkorb, Kundendaten, Bestätigung, Zahlung, aufgegebene Bestellung - mit den geschäftlichen Entscheidungen bei jedem Schritt.
keywords: Warenkorb, Kasse, zum Warenkorb hinzufügen, Bestellung bestätigen, Bestellablauf, abgebrochener Warenkorb, Warenkorb-Bereinigung, Konversion, Kundenweg, basket, cart, checkout, add to basket, confirm order, checkout flow, abandoned basket, basket cleanup, conversion, customer journey
type: help
---
# Warenkorb und Bestellablauf

Diese Seite richtet sich an Geschäftsinhaber und Projektleiter, die den Weg eines Kunden von einer Produktseite bis zur
aufgegebenen Bestellung verstehen und testen möchten. Sie beschreibt jeden Schritt so, wie der Kunde ihn sieht, welche
Entscheidungen das Unternehmen dort trifft und was schiefgehen kann. Die technische Beschreibung steht unter
[Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md).

## Inhalt

1. [Der Weg auf einen Blick](#der-weg-auf-einen-blick)
2. [Schritt 1 In den Warenkorb legen](#schritt-1-in-den-warenkorb-legen)
3. [Schritt 2 Der Warenkorb](#schritt-2-der-warenkorb)
4. [Schritt 3 Kundendaten](#schritt-3-kundendaten)
5. [Schritt 4 Bestätigung](#schritt-4-bestätigung)
6. [Schritt 5 Zahlung](#schritt-5-zahlung)
7. [Schritt 6 Die aufgegebene Bestellung](#schritt-6-die-aufgegebene-bestellung)
8. [Abgebrochene Warenkörbe](#abgebrochene-warenkörbe)
9. [Den Weg testen](#den-weg-testen)

## Der Weg auf einen Blick

| Schritt | Adresse | Geschäftliche Entscheidung |
|---|---|---|
| 1. In den Warenkorb legen | `content/action` von der Produktseite | Auf der Seite bleiben oder zum Warenkorb springen |
| 2. Warenkorb | `shop/basket` | Der Adressname des Warenkorbs |
| 3. Kundendaten | `shop/register` oder `shop/userregister` | Konto oder Gast ([Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md)) |
| 4. Bestätigung | `shop/confirmorder` | Versand und USt werden hier angezeigt |
| 5. Zahlung | `shop/checkout` und der Anbieter | Der Zahlungsanbieter |
| 6. Aufgegebene Bestellung | `shop/orderview` oder `shop/orderreceipt/<token>` | Klassische Bestellansicht oder dauerhafter Beleg |

Quellen: [Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md#the-visitors-path) und
[Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md#checkout).

## Schritt 1 In den Warenkorb legen

Die Produktseite hat eine Schaltfläche **Zum Warenkorb hinzufügen**. Was danach geschieht, ist eine Einstellung,
`site.ini [ShopSettings] RedirectAfterAddToBasket`:

- `basket` (Auslieferungszustand): Der Kunde wird zum Warenkorb geführt und sieht den neuen Artikel;
- `reload`: Der Kunde kehrt dorthin zurück, wo er war, und kann weiter einkaufen.

Shops, in denen Kunden meist nur einen Artikel kaufen, behalten oft `basket`; Shops, in denen Kunden einen Warenkorb mit
vielen Artikeln füllen, bevorzugen oft `reload` mit einer Anzeige der Artikelzahl im Seitenkopf (das Theme simple
zeigt eine an, laut der technischen Seite).

## Schritt 2 Der Warenkorb

Der Warenkorb listet Artikel, Mengen und Summen auf; Kunden ändern Mengen oder entfernen Artikel. Der Adressname des
Warenkorbs ist eine Einstellung (`shop.ini [BasketSettings] BasketViewName`, ausgeliefert `basket`), sodass ein Shop
ihn `cart` oder mit einem Wort in seiner eigenen Sprache nennen kann
([Ansichtsname des Shop-Warenkorbs](../../../../eng-us/bc/6.0/shop-basket-view-name.md)).

Zwei weitere Einstellungen in `site.ini [ShopSettings]` legen fest, wann ein Warenkorb geleert wird:

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `ClearBasketOnCheckout` | `disabled` | Der Warenkorb bleibt erhalten, bis die Zahlung abgeschlossen ist, sodass ein Kunde, der die Zahlung abbricht, mit unverändertem Warenkorb zurückgehen kann |
| `ClearBasketOnLogout` | `disabled` | Das Abmelden leert den Warenkorb nicht |

Die Kommentare in `settings/site.ini` erklären, dass manche Zahlungssysteme `ClearBasketOnCheckout=enabled` brauchen;
prüfen Sie die Dokumentation Ihres Zahlungsanbieters.

## Schritt 3 Kundendaten

Der Kunde gibt Namen, E-Mail-Adresse und Anschrift an oder meldet sich an. Welches Formular erscheint, entscheidet der
Shop-Account-Handler in `shopaccount.ini` ([Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md)).
Die Länderauswahl zeigt die meistgenutzten Länder zuerst ([Ländergruppen und Regionen](laendergruppen-und-regionen.md)).

## Schritt 4 Bestätigung

Die Bestätigungsseite zeigt die Bestellung mit Versand und USt. Die Versandkosten werden hier von einem Workflow am
Auslöser `before_shop_confirmorder` hinzugefügt ([Versand und Bearbeitung](../../../../eng-us/help/businesses/stores/shipping-and-handling.md)). Seit 6.0.15
führt **Abbrechen** in diesem Schritt den Kunden zu seinem Warenkorb mit dessen Artikeln zurück statt zu einem leeren
Warenkorb, und die Zurück-Schaltfläche oder ein zweiter Tab führen die Bestätigung nicht mehr zweimal aus
([Dauerhafte Bestellbelege](../../../../eng-us/features/6.0/order-receipts.md#a-checkout-fix-made-on-the-way)).

## Schritt 5 Zahlung

Beim Bezahlen läuft der Zahlungsanbieter als Workflow am Auslöser `shop_checkout`. Ein Anbieter mit Weiterleitung wie
PayPal schickt den Kunden zum Anbieter; ein Anbieter mit Formular fragt die Kartendaten auf Ihrer Website ab
([Zahlungsanbieter für Unternehmen](../../../../eng-us/help/businesses/stores/payment-gateways-for-businesses.md)).

## Schritt 6 Die aufgegebene Bestellung

Die Bestellung ist aufgegeben, und die Bestätigungsmail geht an den Kunden und an den Shop
([Bestellbelege und Bestätigungsmails](../../../../eng-us/help/businesses/stores/order-receipts-and-confirmation-mail.md)). Der Kunde sieht die Bestellung oder
einen dauerhaften Beleg, den er drucken, herunterladen und als Lesezeichen speichern kann.

## Abgebrochene Warenkörbe

Nicht aus jedem Warenkorb wird eine Bestellung. Das Shop-Dashboard zählt laufende und abgebrochene Warenkörbe und listet
die neuesten Warenkörbe und unbestätigten Bestellvorgänge auf ([Shop-Dashboard und Berichte](../../../../eng-us/help/businesses/stores/store-dashboard-and-reports.md)).

Alte Warenkörbe entfernt der Cron-Teil `basket_cleanup` (`settings/cronjob.ini`, Teil `infrequent`), wie es
`site.ini [Session] BasketCleanup=cronjob` vorsieht. Die „Nächste Schritte“ des Dashboards warnen, wenn die
Warenkorb-Bereinigung Warenkörbe löschen würde, die in PHP-Sitzungen gehalten werden
([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)). Stellen Sie sicher, dass die Cronjobs
laufen ([Cronjobs](../../../anleitungen/cronjobs.md)).

> **Tipp**
> Eine hohe Zahl abgebrochener Warenkörbe im Bestätigungsschritt bedeutet oft eine Überraschung: Versandkosten oder USt,
> mit denen der Kunde nicht gerechnet hat. Zeigen Sie die Versandbedingungen früh.

## Den Weg testen

- [ ] Legen Sie ein Produkt von seiner Seite aus in den Warenkorb; prüfen Sie, wo der Kunde landet.
- [ ] Ändern Sie im Warenkorb eine Menge und entfernen Sie einen Artikel.
- [ ] Bestellen Sie als Gast und als angemeldeter Kunde.
- [ ] Brechen Sie auf der Bestätigungsseite ab; der Warenkorb ist noch da.
- [ ] Brechen Sie beim Zahlungsanbieter ab; der Kunde kann es erneut versuchen.
- [ ] Schließen Sie eine Zahlung im Testmodus ab; die Bestellung und die Mail kommen an.
- [ ] Wiederholen Sie alles auf einem Smartphone.

Die vollständige Testliste steht unter [Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md#test-order-checklist).

## Verwandte Seiten

- [Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md)
- [Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md)
- [Checkliste für den Shop-Start](../../../../eng-us/help/businesses/stores/store-launch-checklist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
