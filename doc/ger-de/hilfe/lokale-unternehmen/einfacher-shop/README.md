---
title: "Ein einfacher Shop für ein lokales Unternehmen"
audience: local-business, businesses, customer
category: local-business
order: 40
summary: Die Seiten für ein lokales Unternehmen, das mit dem in Exponential eingebauten Shop ein wenig online verkaufen will - entscheiden, ob sich ein Shop lohnt, ein kleiner Katalog, Zahlungen mit PayPal und Authorize.Net, Mehrwertsteuer, Versand und Abholung und was Restaurants brauchen, das nicht eingebaut ist - mit Verweisen auf die vollständigen Shop-Leitfäden.
keywords: einfacher Shop, kleiner Shop, Onlineshop, Gutscheine, Geschenkkarten, eingebauter Shop, ezpaypal, ezauthorize, Mehrwertsteuer, Versand, Abholung, Click and Collect, Restaurant, lokales Unternehmen, simple shop, small shop, online store, vouchers, gift cards, built-in shop, VAT, shipping, pickup, local business
type: help
---
# Ein einfacher Shop für ein lokales Unternehmen

Diese Seitengruppe ist für ein lokales Unternehmen, das ein paar Dinge online verkaufen will: Geschenkgutscheine, ein
Sortiment, Eingemachtes aus dem Restaurant, einen Kursplatz, ein Reparaturpaket. Exponential hat einen **im Kernel
eingebauten** Shop: Produkte sind Inhalte mit einem Preisfeld, Besucher füllen einen Warenkorb, gehen zur Kasse und
bezahlen über ein Zahlungs-Gateway, und Bestellungen werden im Reiter **Web-Shop** der Administration bearbeitet
([Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md)).

Diese Seiten geben die Sicht eines kleinen Unternehmens wieder und verweisen dann auf die vollständigen Leitfäden, die
hier nicht wiederholt werden:

- [Onlineshops und kleine Läden](../../unternehmen/shops/README.md): einen Shop planen und einrichten;
- [Der Bereich für Shop-Betreiber](../../kunden/handel/README.md): ihn im Alltag betreiben.

## Inhalt

1. [Die Seiten dieser Gruppe](#die-seiten-dieser-gruppe)
2. [Was der eingebaute Shop einem kleinen Unternehmen bietet](#was-der-eingebaute-shop-einem-kleinen-unternehmen-bietet)
3. [Was er nicht kann](#was-er-nicht-kann)

## Die Seiten dieser Gruppe

| Seite | In einer Zeile |
|---|---|
| [Brauchen Sie einen Shop?](brauchen-sie-einen-shop.md) | Fragen, die entscheiden, ob sich Online-Verkauf für ein lokales Unternehmen lohnt |
| [Ein kleiner Katalog](ein-kleiner-katalog.md) | Produkte, Gutscheine und Leistungen mit der mitgelieferten Produktklasse |
| [Zahlungen: PayPal und Authorize.Net](zahlungen-paypal-und-authorize-net.md) | die Zahlungserweiterungen und was ein kleines Unternehmen regeln muss |
| [Mehrwertsteuer, Versand und Abholung](mehrwertsteuer-versand-und-abholung.md) | Steuern, eine Versandpauschale, Abholung im Geschäft |
| [Restaurantbestellungen und was nicht eingebaut ist](restaurantbestellungen-und-was-nicht-eingebaut-ist.md) | was ein Restaurant oder Café mit dem Shop kann und was nicht |

## Was der eingebaute Shop einem kleinen Unternehmen bietet

| Teil | Eingebaut oder Erweiterung | Quelle |
|---|---|---|
| Produkte mit Preisen, in einer oder mehreren Währungen | eingebaut (`ezprice`, `ezmultiprice`) | [Produkte, Preise und Mehrwertsteuer](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md) |
| Warenkorb, Kasse, Bestätigungs-E-Mail zur Bestellung | eingebaut | [Warenkorb, Kasse und Bestellungen](../../ersteller/website-ersteller/warenkorb-kasse-und-bestellungen.md) |
| USt-Typen und Regeln | eingebaut | [Mehrwertsteuerregeln und USt-Typen](../../unternehmen/shops/ust-regeln-und-ust-typen.md) |
| Eine Versandpauschale | eingebauter Workflow (`ezsimpleshipping`) | [Versand und Abwicklung](../../unternehmen/shops/versand-und-bearbeitung.md) |
| Bestellstatus, einschließlich **Ready for pickup** (abholbereit) | eingebaut | [Bestellungen und Bestellstatus](../../unternehmen/shops/bestellungen-und-bestellstatus.md) |
| Shop-Dashboard mit offenen Bestellungen und abgebrochenen Warenkörben | eingebaut | [Shop-Dashboard und Berichte](../../unternehmen/shops/shop-uebersicht-und-berichte.md) |
| Dauerhafte Belege | eingebaut | [Bestellbelege und Bestätigungs-E-Mail](../../unternehmen/shops/bestellbelege-und-bestaetigungsmails.md) |
| PayPal | `ezpaypal`, in der Standard-`composer.json` | [Zahlungen mit PayPal](../../unternehmen/shops/zahlungen-mit-paypal.md) |
| Kartenzahlungen über Authorize.Net | `ezauthorize`, ein vorgeschlagenes Composer-Paket | [Kartenzahlungen und PCI-Grundlagen](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md) |

## Was er nicht kann

Aus [Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md#was-der-shop-nicht-leistet) und
den Shop-Seiten: Lagerverwaltung, Rechnungen in einem Buchhaltungssystem, Versandetiketten und Marktplätze sind als
Erweiterungen oder Integrationen geplant. Für ein lokales Unternehmen außerdem: keine Anbindung an eine Ladenkasse
(POS), keine Buchung von Tischen oder Lieferzeitfenstern, keine Gutscheincodes ([Was einen externen Dienst braucht](../budget/was-einen-externen-dienst-braucht.md)).

## Verwandte Seiten

- [Lokale Unternehmen: hier beginnen](../lokale-unternehmen-hier-beginnen.md)
- [Shop-Planung für kleine Unternehmen](../../unternehmen/shops/shop-planung-fuer-kleine-unternehmen.md)
- [Onlineshop: hier beginnen](../../kunden/typen/onlineshops-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
