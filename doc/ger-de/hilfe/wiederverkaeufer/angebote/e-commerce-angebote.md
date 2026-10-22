---
title: E-Commerce-Angebote
audience: reseller, partner, sales-marketing
category: reselling
order: 240
summary: Wie Sie Shop-Angebote auf Basis des in Exponential eingebauten Shops verkaufen - was der Shop kann (Produkte, Preise und Währungen, Mehrwertsteuer, Warenkorb und Kasse, Bestellungen, Dashboard, Zahlungs-Gateways, Rabatte), die Zahlungserweiterungen, was nicht enthalten ist und gebaut oder eingekauft werden muss, Kartenzahlungen und PCI und eine Checkliste für das Angebot.
keywords: E-Commerce, Shop, Laden, Produkte, Warenkorb, Kasse, Bestellungen, Mehrwertsteuer, MwSt., Währungen, Zahlungs-Gateway, PayPal, ezpaypal, ezauthorize, Rabatte, Shop-Dashboard, PCI, nicht enthalten, Lagerbestand, Gutscheine, e-commerce, shop, store, products, basket, checkout, orders, VAT, currencies, payment gateway, discounts, store dashboard, not included, stock, coupons
type: help
---
# E-Commerce-Angebote

Diese Seite ist für Wiederverkäufer, die Online-Shops auf Basis von Exponential verkaufen. Der Shop ist Teil von
Exponential selbst: `README.md` nennt „Advanced e-Commerce Stores (Built in)“ (fortgeschrittene E-Commerce-Shops,
eingebaut), und alle Shop-Bildschirme sind Views eines einzigen Moduls, `kernel/shop/module.php`. Verkaufen Sie, was
er kann, und seien Sie klar darüber, was er nicht kann.

## Inhalt

1. [Was der eingebaute Shop kann](#was-der-eingebaute-shop-kann)
2. [Zahlung](#zahlung)
3. [Was nicht enthalten ist](#was-nicht-enthalten-ist)
4. [Ein Shop-Angebot paketieren](#ein-shop-angebot-paketieren)
5. [Checkliste für das Angebot](#checkliste-für-das-angebot)

## Was der eingebaute Shop kann

| Teil | Was er tut | Seite für den Kunden |
|---|---|---|
| Produkte | Jede Inhaltsklasse mit einem Preisfeld wird verkäuflich | [Produktklassen und Katalog](../../unternehmen/shops/produktklassen-und-katalog.md) |
| Preise | Ein Preis oder ein Preis je Währung, mit Optionen, die den Preis ändern | [Preise und Währungen](../../unternehmen/shops/preise-und-waehrungen.md) |
| Mehrwertsteuer | Benannte Steuersätze und Regeln nach Land des Kunden und Produktkategorie | [Mehrwertsteuerregeln und Mehrwertsteuertypen](../../unternehmen/shops/ust-regeln-und-ust-typen.md) |
| Versand | Ein Workflow mit pauschalen Versandkosten oder ein eigener Versand-Handler | [Versand und Bearbeitung](../../unternehmen/shops/versand-und-bearbeitung.md) |
| Warenkorb und Kasse | In den Warenkorb legen, Mengen, Kundendaten, Bestätigung, Zahlung | [Ablauf von Warenkorb und Kasse](../../unternehmen/shops/warenkorb-und-bestellablauf.md) |
| Bestellungen | Bestellliste, Bestellansicht, siebzehn Status, Archiv, Kunden | [Bestellungen und Bestellstatus](../../unternehmen/shops/bestellungen-und-bestellstatus.md) |
| Dashboard | Bestellungen heute, in 7 und 30 Tagen, Umsatz je Währung, offene Bestellungen, verlassene Warenkörbe, Bestseller | [Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md) |
| Rabatte und Wunschlisten | Prozentuale Rabatte für Kundengruppen; angemeldete Kunden merken sich Produkte für später | [Rabatte und Wunschlisten](../../unternehmen/shops/rabatte-und-wunschlisten.md) |
| Berechtigungen | Shop-Funktionen `buy`, `administrate`, `setup`, `edit_status` und `setstatus` | [Ihren Shop betreiben](../../kunden/handel/README.md) |

Quelle: [Was der eingebaute Shop kann](../../unternehmen/shops/was-der-eingebaute-shop-kann.md).

## Zahlung

| Erweiterung | Fügt hinzu | Status im Projekt |
|---|---|---|
| `ezpaypal` | Zahlungen über PayPal | Von der `composer.json` im Wurzelverzeichnis verlangt (`se7enxweb/ezpaypal`) |
| `ezauthorize` | Kartenzahlungen über Authorize.Net | In der `composer.json` im Wurzelverzeichnis vorgeschlagen |

Zahlungs-Gateways laufen als Workflow-Schritt an der Kasse
([Zahlungs-Gateways für Unternehmen](../../unternehmen/shops/zahlungsanbieter-fuer-unternehmen.md)). Kartendaten und
PCI-Pflichten hängen vom Gateway ab und davon, wie die Zahlung entgegengenommen wird: Lesen Sie
[Grundlagen zu Kartenzahlungen und PCI](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md), bevor Sie etwas
versprechen, und überlassen Sie die PCI-Bewertung dem Kunden und seinem Zahlungsanbieter.

## Was nicht enthalten ist

Aus [Was der eingebaute Shop kann](../../unternehmen/shops/was-der-eingebaute-shop-kann.md#was-nicht-enthalten-ist):

- Lagerverwaltung, Rechnungen in einem Buchhaltungssystem, Versandetiketten und Marktplätze;
- Gutschein- oder Rabattcodes (Rabatte sind an Kundengruppen gebunden);
- Export von Bestellungen aus der Bestellliste in eine Tabellendatei;
- automatische Auslieferung einer gekauften Datei nach der Zahlung;
- mehr als eine Versandart ab Werk (ein pauschaler Betrag je Bestellung).

Jedes davon lässt sich mit einer Erweiterung, einem Workflow-Ereignis oder einem eigenen Handler ergänzen. Verkaufen
Sie es als Projektarbeit mit eigenem Preis oder lassen Sie es weg und sagen Sie das. Nehmen Sie es nie in ein
Angebot auf, als wäre es Standard.

## Ein Shop-Angebot paketieren

| Ebene | Inhalt |
|---|---|
| Einrichtung | Produktklassen, Mehrwertsteuertypen und -regeln, Währungen, Zahlungs-Gateway, Bestätigungsmail, Shop-Seiten im Design |
| Betrieb (monatlich) | Hosting für Spitzenlasten dimensioniert, Updates, Datensicherungen einschließlich Bestellungen, Überwachung der Kasse |
| Shop-Support | Fragen zum Bestellstatus, Änderungen der Mehrwertsteuer, Probleme mit dem Gateway |
| Zusatzleistungen | Eigener Versand-Handler, Anbindung der Lagerverwaltung, Export für die Buchhaltung, als Projektarbeit |
| Schulung | Shop-Personal: Bestellungen, Status, Dashboard ([Tägliche Routine im Shop](../../kunden/handel/taegliche-shop-routine.md)) |

Hochsaisons brauchen Kapazitätsplanung und einen Code-Freeze
([Shop-Leistung zu Spitzenzeiten](../../unternehmen/shops/shop-leistung-zu-spitzenzeiten.md)).

## Checkliste für das Angebot

- [ ] Die Anforderungen des Kunden mit der Tabelle dessen verglichen, was enthalten ist und was nicht.
- [ ] Zahlungs-Gateway gewählt, seine Gebühren und der PCI-Umfang vom Zahlungsanbieter des Kunden erklärt.
- [ ] Mehrwertsteuerregeln für die Länder des Kunden konfiguriert und von seinem Steuerberater geprüft.
- [ ] Bestellungen und Kundendaten in den Datensicherungen und in den Bedingungen zur Auftragsverarbeitung enthalten
      ([Datenschutz für Wiederverkäufer](../verantwortlichkeiten/datenschutz.md)).
- [ ] Rollen des Shop-Personals auf die Shop-Funktionen beschränkt, die es braucht.
- [ ] Checkliste für den Start erledigt ([Checkliste für den Start eines Shops](../../unternehmen/shops/checkliste-fuer-den-shop-start.md)).

## Verwandte Seiten

- [Was der eingebaute Shop kann](../../unternehmen/shops/was-der-eingebaute-shop-kann.md)
- [Ihren Shop betreiben](../../kunden/handel/README.md)
- [Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
