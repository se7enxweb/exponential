---
title: Der eingebaute Shop
audience: cms-developer
category: cms-development
order: 35
summary: Für CMS-Entwickler ein kurzer, sachlicher Überblick über den Shop im Kernel von Exponential. Die Seite behandelt Produkte als Inhalte mit Preis-Datentypen, den Warenkorb, Kasse und Bestellungen, Mehrwertsteuer, Währungen und Rabatte, Zahlung über Workflows und Gateway-Erweiterungen, das Shop-Dashboard und wo Sie weiterlesen.
keywords: Shop, Webshop, Store, E-Commerce, Produkte, ezprice, ezmultiprice, Warenkorb, Kasse, Bestellungen, Mehrwertsteuer, MwSt, Währungen, Rabatte, Wunschliste, Zahlungsgateway, ezpaypal, ezauthorize, shop.ini, paymentgateways.ini, Shop-Dashboard, Bestellbelege, shop, e-commerce, products, basket, checkout, orders, VAT, currencies, discounts, wish list, payment gateway, order receipts
type: help
---
# Der eingebaute Shop

Diese Seite richtet sich an Entwickler, die gefragt werden, ob eine Website etwas verkaufen kann. Der Kernel von
Exponential hat ein Shop-Modul: Produkte sind Inhaltsobjekte, und Warenkörbe, Bestellungen, Mehrwertsteuer, Währungen
und Rabatte sind eingebaut. Diese Seite sagt, was vorhanden ist und wo es beschrieben ist, damit Sie beurteilen können,
ob es zu einem Projekt passt.

## Inhalt

1. [Woraus der Shop besteht](#woraus-der-shop-besteht)
2. [Produkte sind Inhalte](#produkte-sind-inhalte)
3. [Warenkorb, Kasse und Bestellungen](#warenkorb-kasse-und-bestellungen)
4. [Zahlung](#zahlung)
5. [Mehrwertsteuer, Währungen und Rabatte](#mehrwertsteuer-währungen-und-rabatte)
6. [Der Shop in der Administration](#der-shop-in-der-administration)
7. [Templates](#templates)
8. [Ist es das richtige Werkzeug?](#ist-es-das-richtige-werkzeug)

## Woraus der Shop besteht

| Teil | Wo |
|---|---|
| Das Modul `shop`: Warenkorb, Kasse, Bestellung bestätigen, Bestellliste und -ansicht, Wunschliste, Mehrwertsteuer, Währungen, Rabatte, Statistik | `kernel/shop/` |
| Einstellungen: Währungen, Wechselkurse, Versand, Warenkorb, Mehrwertsteuer, Bestellansicht und Belege | `settings/shop.ini` |
| Rahmenwerk für Zahlungsgateways | `kernel/shop/paymentgateways/`, `settings/paymentgateways.ini` |
| Datentypen für Preise und Optionen | `ezprice`, `ezmultiprice`, `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption`, `ezproductcategory` |
| Shop-Auslöser für Workflows | `workflow.ini [OperationSettings]`: `before_shop_confirmorder`, `shop_checkout`, `shop_addtobasket`, `shop_updatebasket` |

## Produkte sind Inhalte

Ein Produkt ist ein Objekt einer Klasse mit einem Preisattribut: `ezprice` (ein Preis mit einer
Mehrwertsteuerart) oder `ezmultiprice` (ein Preis je Währung). Optionen und Varianten verwenden die Options-Datentypen;
`ezproductcategory` gruppiert Produkte für Mehrwertsteuerregeln. Alles andere (Beschreibungen, Bilder, Relationen,
Übersetzungen, Sektionen, URLs) funktioniert wie bei jedem Inhalt; ein Produktkatalog ist also ein Inhaltsmodell wie
jedes andere ([Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md)).

## Warenkorb, Kasse und Bestellungen

Besucher legen Produkte in den Warenkorb (`shop/add`, `shop/basket`) und gehen dann zur Kasse: Der Kontohandler des
Shops erfasst die Angaben des Kunden, `shop/confirmorder` zeigt die Bestellung mit Versand und Mehrwertsteuer, die
Zahlung läuft, und die Bestellung wird mit einem Status gespeichert. Bestellstatus, die Bestellliste und dauerhafte
Bestellbelege werden in der Administration verwaltet ([Bestellbelege](../../funktionen/6.0/bestellbelege.md),
[Sortierung der Bestellliste](../../funktionen/6.0/sortierung-der-bestellliste.md)).

## Zahlung

Die Zahlung ist ein Workflow an den Shop-Auslösern mit dem Event **Zahlungsgateway**; ein Gateway ist eine Klasse in
einem Gateway-Verzeichnis, das in `paymentgateways.ini` aufgeführt ist. Die `composer.json` im Hauptverzeichnis verlangt
`ezpaypal` (eine Weiterleitung zu PayPal) und schlägt `ezauthorize` vor (laut seiner dortigen Beschreibung ein
Zahlungsformular auf der Website, ohne Weiterleitung). Das Rahmenwerk zum Schreiben eines Gateways ist in der
Shop-Referenz des Kernels beschrieben ([Zahlungsgateways](../../../eng-us/shop/index.html)) und der Event-Typ unter
[Workflows und Auslöser](workflows-und-ausloeser.md). **Einfacher Versand** fügt über einen weiteren Event-Typ
Versandkosten hinzu.

## Mehrwertsteuer, Währungen und Rabatte

`shop.ini` enthält die Währungseinstellungen, Wechselkurse (mit einem Wechselkurs-Handler für die EZB), Versand und die
Behandlung der Mehrwertsteuer; Mehrwertsteuerarten und -regeln, Währungen und Rabattgruppen werden in der
Administration bearbeitet. Länderlisten für Kunden lassen sich gruppieren
([Die gruppierte Länderliste](../../funktionen/6.0/laendergruppen.md)).

## Der Shop in der Administration

Die Registerkarte **Shop** hat ein Dashboard mit Bestellungen, Umsatz je Währung, offenen Bestellungen, Warenkörben,
Bestsellern, der Kasse, wie sie in der Installation konfiguriert ist, und Vorschlägen; dafür ist die Richtlinie
`shop/administrate` nötig ([Shop-Dashboard und Bestellstatus](../../funktionen/6.0/shop-dashboard.md)). `expservices`
bietet außerdem Handelsdienste (Produkte, Warenkorb und Kasse, Bestellungen, Mehrwertsteuer, Währungen, Rabatte) über
HTTP an ([Entfernte Dienste: expservices](../../funktionen/6.0/entfernte-dienste-expservices.md)).

## Templates

Die Seiten des Shops verwenden Templates in `shop/` der Design-Kette; überschreiben Sie sie wie jedes Template in Ihrer
Design-Erweiterung, und zeichnen Sie Preise in Produkt-Templates mit `attribute_view_gui` oder mit dem Operator
`l10n( 'currency' )` ([Datum, Zahlen und l10n](../designer/vorlagen/datum-zahlen-und-l10n.md)).

## Ist es das richtige Werkzeug?

Der eingebaute Shop passt zu einem Katalog, der im CMS lebt, mit einer geradlinigen Kasse: Produkte, die Redakteure als
Inhalte pflegen, eine oder wenige Währungen, Zahlung über eine Gateway-Erweiterung. Prüfen Sie früh anhand Ihrer
Anforderungen, wie Steuern, Versandregeln, Lagerbestand, Rechnungsstellung und der benötigte Zahlungsanbieter abgedeckt
sind, und planen Sie Entwicklung ein, wo sie es nicht sind. Ein Link von der Website zu einem externen Shop ist die
Alternative, wenn der Handel das Hauptgeschäft der Website ist
([Inhalte abbilden: Produkte](../../installation/migration/inhaltsmodell-zuordnen.md#9-kommentare-formulare-produkte)).

## Verwandte Seiten

- [Datentypen wählen](datentypen-waehlen.md)
- [Handler, die Einstellungen benennen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md): die Handler-Stellen des Shops
- [Workflows und Auslöser](workflows-und-ausloeser.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
