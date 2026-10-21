---
title: "Thema: der eingebaute Shop"
audience: press
category: press-media
order: 77
summary: Ein Themenbeitrag über den in Exponential eingebauten E-Commerce - Produkte, Warenkörbe, Bestellungen, Mehrwertsteuer, Währungen, PayPal - und die Verbesserungen in 6.0.x wie das Shop-Dashboard, Bestellstatus, dauerhafte Bestellbelege und das Sortieren der Bestellliste, mit Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, E-Commerce, Shop, Onlineshop, Bestellungen, Produkte, Mehrwertsteuer, USt, Währungen, PayPal, Shop-Dashboard, Bestellbelege, Kasse, story, e-commerce, shop, online store, orders, products, VAT, currencies, store dashboard, order receipts, checkout
type: help
---
# Thema: der eingebaute Shop

Diese Seite ist für Journalisten, die über E-Commerce berichten. Viele Content-Management-Systeme ergänzen einen Shop
über ein Plugin; Exponential hat einen im Kernel, seit seinen frühen Generationen.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
3. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
4. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

Die README nennt „Advanced e-Commerce Stores (Built in)“, also eingebaute, fortgeschrittene E-Commerce-Shops, unter den
wichtigsten Funktionen: Produkte, Warenkörbe, Bestellungen, Mehrwertsteuer, Währungen und Zahlungen mit PayPal über die
Erweiterung `ezpaypal` ([Wer ist 7x?, Ein CMS, ein CMF und ein Shop in einem](../wer-ist-7x.md#cms-cmf-und-shop-in-einem)).
Die Produktlinie 6.0.x hat ihn verbessert: Der Reiter **Shop** öffnet ein Dashboard, das „auf einer Seite zeigt, ...
wie es dem Shop geht, welche Bestellungen Aufmerksamkeit brauchen und wie die Kasse eingerichtet ist“, mit einem
Bestellstatus für jede Stufe ([Shop-Dashboard und Bestellstatus](../../funktionen/6.0/shop-dashboard.md)), und jede
Bestellung kann einen Beleg „mit einer Adresse, die nie abläuft“ haben ([Dauerhafte Bestellbelege](../../funktionen/6.0/bestellbelege.md)).

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| E-Commerce ist in den Kernel eingebaut | README, „Main Exponential features“ |
| Zahlungen mit PayPal über `ezpaypal` | [ezpaypal](../../funktionen/6.0/erweiterungen/ezpaypal.md) |
| Ein Shop-Dashboard und Bestellstatus für jede Stufe | [Shop-Dashboard und Bestellstatus](../../funktionen/6.0/shop-dashboard.md) |
| Dauerhafte Bestellbelege, die Kunden als Lesezeichen speichern, drucken oder herunterladen können, mit oder ohne Konto | [Dauerhafte Bestellbelege](../../funktionen/6.0/bestellbelege.md) |
| Bestelllisten lassen sich sortieren | [Sortieren der Bestellliste](../../funktionen/6.0/sortierung-der-bestellliste.md) |
| Ländergruppen für Shop-Einstellungen | [Ländergruppen](../../funktionen/6.0/laendergruppen.md) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Ein vollständiger Ersatz für <genannte Shop-Plattform>“ | Beschreiben Sie, was der eingebaute Shop tut ([Vergleiche, fair gemacht](faire-vergleiche.md)) |
| „PCI-zertifiziert“ | Es ist keine Zertifizierung dokumentiert; Zahlungen laufen über Gateways wie PayPal |
| „Der Shop funktioniert auf MongoDB“ | Die Kasse auf MongoDB ist „untested end to end“, nicht durchgehend getestet ([Thema: MongoDB als Datenbank](thema-mongodb-als-datenbank.md)) |

## Blickwinkel und Ideen für Überschriften

- „Inhalte und Handel in einem Open-Source-System.“
- „Belege, die nie ablaufen.“

## Verwandte Seiten

- [Thema: Inhalte bearbeiten](thema-inhalte-bearbeiten.md)
- [Funktionsthemen: Übersicht](themenbeitraege-uebersicht.md)
- [Funktionshöhepunkte für Kunden](../geschaeft/funktionshighlights-fuer-kunden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
