---
title: Katalogmodellierung
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 20
summary: Wie der Katalog eines Exponential-Shops aus Inhaltsklassen, Preisfeldern, Optionsfeldern, Produktkategorien und Ordnern aufgebaut wird - ein Überblick über die Katalogseiten, den Bildschirm Produktübersicht und die Fragen, die Sie vor der Modellierung beantworten sollten.
keywords: Katalog, Produktklasse, Produktmodell, Preisfeld, ezprice, ezmultiprice, ezproductcategory, Optionen, Varianten, Ordner, Produktübersicht, shop/productsoverview, catalog, product class, price field, options, variants, folders, products overview
type: help
---
# Katalogmodellierung

Dieser Teil ist für Händler und Entwickler, die den Produktkatalog eines Exponential-Shops entwerfen. In Exponential ist
ein Produkt gewöhnlicher Inhalt: Jede Inhaltsklasse wird verkäuflich, wenn sie ein Preisfeld hat. Das gibt Ihnen
Freiheit und damit einige Entscheidungen, die sich später nur schwer ändern lassen. Diese Seiten gehen tiefer als der
geschäftliche Überblick in [Produktklassen und Katalog](../../unternehmen/shops/produktklassen-und-katalog.md).

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Fragen vor der Modellierung](#fragen-vor-der-modellierung)
3. [Die Seiten dieses Teils](#die-seiten-dieses-teils)
4. [Der Bildschirm Produktübersicht](#der-bildschirm-produktübersicht)

## Die Bausteine

| Baustein | Was er ist | Wo er definiert wird |
|---|---|---|
| Produktklasse | Eine Inhaltsklasse mit einem Preisfeld; der Shop erkennt sie an diesem Feld | Setup > Klassen |
| Preisfeld | `ezprice` (ein Preis) oder `ezmultiprice` (ein Preis pro Währung) | `kernel/classes/datatypes/ezprice/`, `ezmultiprice/` |
| Optionsfelder | `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption`: Auswahlen des Kunden, die mit dem Warenkorbposten gespeichert werden | `kernel/classes/datatypes/` |
| Produktkategorie | `ezproductcategory`: die Steuerklasse eines Produkts, von USt-Regeln verwendet | `kernel/classes/datatypes/ezproductcategory/`, **Web-Shop > Produkt Kategorien** (`shop/productcategories`) |
| Ordner und Orte | Wo Produkte im Inhaltsbaum liegen; ein Produkt kann mehrere Orte haben | Der Inhaltsbaum |

Welche Datentypen als Preisfelder gelten, entscheidet `eZShopFunctions` (`kernel/shop/classes/ezshopfunctions.php`):
`ezprice` und `ezmultiprice`, oder die Liste in `site.ini [ShopSettings] ProductDatatypeStringList[]`, wenn diese
Einstellung vorhanden ist ([Eigene Preis-Datentypen](../erweitern/eigene-preis-datentypen.md)).

## Fragen vor der Modellierung

- [ ] Welche Arten von Produkten verkaufen Sie, und braucht jede Art andere Felder?
- [ ] Eine Währung oder mehrere? Preise von Hand pro Währung festgelegt oder umgerechnet?
- [ ] Enthalten die Preise die USt. oder nicht, und gilt das für alle Produkte gleich?
- [ ] Hängt die USt. vom Land des Kunden und der Steuerklasse des Produkts ab?
- [ ] Welche Auswahlen des Kunden ändern den Preis (Größe, Material, Serviceumfang)?
- [ ] Welche Auswahlen sind eigentlich eigene Produkte (anderer Lagerbestand, andere Bilder, andere Artikelnummer)?
- [ ] Wie stöbern Kunden: über Kategorieordner, über Tags, über Suchfacetten?
- [ ] Woher kommen die Produktdaten zum Start und später?

## Die Seiten dieses Teils

| Seite | Beantwortet |
|---|---|
| [Katalogmodellierung im Detail](katalogmodellierung-im-detail.md) | Klassenentwurf, Felder, Ordner, mehrere Orte, Übersetzungen und importfreundliche Modelle |
| [Preisfelder im Vergleich](preisfelder-im-vergleich.md) | `ezprice` gegenüber `ezmultiprice`, individuelle und automatische Preise, Zeichenkettenformate |
| [Optionen und Varianten](optionen-und-varianten.md) | Die vier Options-Datentypen, wann eigene Produkte sinnvoll sind |
| [Produktkategorien und USt-Regeln](produktkategorien-und-ust-regeln.md) | Steuerklassen, USt-Regeln, der USt-Handler und seine Prioritätslogik |

## Der Bildschirm Produktübersicht

**Web-Shop > Produkt Übersicht** (`shop/productsoverview`, Richtlinie `shop/administrate`) listet die Produkte einer
Produktklasse mit ihren Preisen auf und lässt Sie die Liste sortieren. Nutzen Sie ihn, um einen Katalog nach einem
Import oder einer Preisänderung zu prüfen: Fehlende Preise fallen sofort auf. Auch das Shop-Dashboard zählt Produkte
ohne Preis ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)).

## Verwandte Seiten

- [Produkte, Preise und USt.](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md)
- [Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)
- [Produkte und Katalog (Ihren Shop betreiben)](../../kunden/handel/produkte-und-katalog.md)
- [Produktimporte und Migrationen](../betrieb/produktimporte-und-migrationen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
