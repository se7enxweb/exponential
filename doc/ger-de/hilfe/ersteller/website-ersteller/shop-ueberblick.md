---
title: "Einen Shop betreiben: Überblick"
audience: website-creator, admin
category: website-creation
order: 520
summary: Was der eingebaute Shop leistet und wie seine Teile zusammenpassen - Produktklassen mit einem Preisfeld, Warenkorb, Kasse, Bestellbestätigung, Zahlung über einen Workflow, Bestellungen und das Shop-Dashboard - und eine Reihenfolge für die Einrichtung.
keywords: Shop, E-Commerce, Webshop, Produkte, Warenkorb, Kasse, Bestellungen, Mehrwertsteuer, Zahlung, Shop-Dashboard, Shop-Modul, Website-Ersteller, shop, e-commerce, store, products, basket, checkout, orders, VAT, payment, store dashboard, shop module, website creator
type: help
---
# Einen Shop betreiben: Überblick

Diese Seite richtet sich an Website-Ersteller, die über eine Exponential-Website verkaufen. Der Kernel enthält einen
Shop: Produkte sind Inhalte mit einem Preisfeld, Besucher legen sie in einen Warenkorb, gehen zur Kasse, bestätigen,
bezahlen über ein Payment-Gateway, und Bestellungen werden im Reiter **Shop** der Administration verwaltet, der das
Shop-Dashboard öffnet ([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)).

## Inhalt

1. [Die Bestandteile](#die-bestandteile)
2. [Der Weg des Besuchers](#der-weg-des-besuchers)
3. [Einen Shop einrichten, der Reihe nach](#einen-shop-einrichten-der-reihe-nach)
4. [Rechte](#rechte)
5. [Was der Shop nicht leistet](#was-der-shop-nicht-leistet)

## Die Bestandteile

| Bestandteil | Wo |
|---|---|
| Produkte | Inhalte einer Klasse mit einem Feld `ezprice` oder `ezmultiprice` (die mitgelieferte Klasse `product`) ([Produkte, Preise und Mehrwertsteuer](produkte-preise-und-mehrwertsteuer.md)) |
| Warenkorb | `shop/basket` (der Name seiner Ansicht ist `[BasketSettings] BasketViewName` in `shop.ini`) |
| Kasse | `shop/register` (Kundendaten), `shop/confirmorder`, `shop/checkout` |
| Zahlung | ein Workflow am Kassen-Trigger mit einem Gateway-Ereignis ([Payment-Gateways für Website-Ersteller](payment-gateways.md)) |
| Bestellungen | `shop/orderlist`, `shop/orderview`, Status, Archiv, Kunden, Statistiken |
| Mehrwertsteuer | `shop/vattype`, `shop/vatrules` |
| Währungen | `shop/currencylist`, Wechselkurse (`[ExchangeRatesSettings]`) |
| Rabatte | `shop/discountgroup` |
| Dashboard | `shop/dashboard` |

![Das Shop-Dashboard: Bestellungen, Kunden, Produkte und die Shop-Einstellungen auf einen Blick](../../../../eng-us/help/images/creators/shop-dashboard.webp)

Modul und Ansichten des Shops sind in `kernel/shop/module.php` definiert, seine Einstellungen in `settings/shop.ini`.

## Der Weg des Besuchers

1. Eine Produktseite hat eine Schaltfläche **In den Warenkorb** (`shop/add`).
2. Der Warenkorb (`shop/basket`) listet Elemente, Mengen und Summen auf.
3. Die Kasse fragt die Daten des Kunden ab (`shop/register` oder `shop/userregister`).
4. Die Bestätigungsseite (`shop/confirmorder`) zeigt Versand und Mehrwertsteuer.
5. Zahlung: Der Kassen-Workflow schickt den Besucher zum Gateway.
6. Die Bestellung wird aufgegeben; eine Bestätigungsmail geht hinaus, und der Besucher sieht die Bestellung (oder einen
   dauerhaften Beleg, [Bestellbelege](../../../../eng-us/features/6.0/order-receipts.md)).

Details: [Warenkorb, Kasse und Bestellungen](warenkorb-kasse-und-bestellungen.md).

## Einen Shop einrichten, der Reihe nach

1. Produktklasse mit einem Preisfeld; Produktordner; ein paar Produkte.
2. Mehrwertsteuertypen und, für mehrere Länder, Mehrwertsteuerregeln.
3. Währung (`[CurrencySettings] PreferredCurrency`) und Rundung (`[MathSettings]`).
4. Templates: Vollansicht des Produkts mit dem Formular für den Warenkorb, Warenkorb, Kassenseiten (die Themes ezwebin
   und simple liefern sie mit).
5. Payment-Gateway und der Kassen-Workflow.
6. Versand, falls nötig (ein Versand-Handler oder der einfache Versand-Workflow in `workflow.ini`).
7. Bestätigungsmail für Bestellungen und, falls gewünscht, dauerhafte Belege.
8. Eine vollständige Testbestellung, dann der Bereich "So funktioniert Ihr Shop" im Shop-Dashboard, um die Konfiguration
   zu prüfen.

## Rechte

Die Funktionen des Shop-Moduls sind `buy`, `administrate`, `setup`, `edit_status` und `setstatus`. Besucher brauchen
`shop/buy`; die Shop-Mitarbeiter brauchen `shop/administrate` (Bestellungen, Dashboard, Statistiken) und `shop/setup`
für Mehrwertsteuer und Währungen ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

## Was der Shop nicht leistet

Planen Sie Folgendes als Erweiterungen oder Integrationen: Lagerverwaltung, Rechnungen in einem Buchhaltungssystem,
Versandetiketten, Marktplätze. Der Bereich "Nächste Schritte" im Shop-Dashboard schlägt vor, was in Ihrer Konfiguration
fehlt.

## Verwandte Seiten

- [Produkte, Preise und Mehrwertsteuer](produkte-preise-und-mehrwertsteuer.md)
- [Warenkorb, Kasse und Bestellungen](warenkorb-kasse-und-bestellungen.md)
- [Payment-Gateways für Website-Ersteller](payment-gateways.md)
- [Name der Warenkorb-Ansicht im Shop](../../../bc/6.0/view-name-des-shop-warenkorbs.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
