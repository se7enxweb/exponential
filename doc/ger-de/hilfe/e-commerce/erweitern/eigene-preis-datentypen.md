---
title: Eigene Preis-Datentypen
audience: ecommerce, app-creator, developer
category: e-commerce
order: 67
summary: Wie ein eigener Datentyp im Exponential-Shop als Preis zählt - die Einstellung ProductDatatypeStringList, was der Shop aus dem Inhalt des Datentyps liest, der Aufbau auf eZSimplePrice und wann sich ein eigener Preis-Datentyp lohnt.
keywords: eigener Preis-Datentyp, ProductDatatypeStringList, isProductDatatype, eZShopFunctions, eZSimplePrice, eZPrice, eZMultiPrice, Preisobjekt, VATType, VATPercent, is_vat_included, Währung, eigener Datentyp, custom price datatype, price object, currency, custom datatype
type: help
---
# Eigene Preis-Datentypen

Diese Seite ist für Entwickler, die ein Preisfeld brauchen, das sich anders verhält als `ezprice` und
`ezmultiprice`, zum Beispiel einen Preis, der von der Kundengruppe oder von einem Datum abhängt. Der Shop entscheidet
anhand einer Einstellung, welche Datentypen Preise enthalten; ein eigener Datentyp kann also an Warenkorb und Kasse
teilnehmen.

## Inhalt

1. [Wie der Shop Preise findet](#wie-der-shop-preise-findet)
2. [Was Ihr Datentyp bereitstellen muss](#was-ihr-datentyp-bereitstellen-muss)
3. [Ihn registrieren](#ihn-registrieren)
4. [Lohnt es sich?](#lohnt-es-sich)

## Wie der Shop Preise findet

`eZShopFunctions::isProductDatatype()` (`kernel/shop/classes/ezshopfunctions.php`) prüft einen Datentyp gegen
`productDatatypeStringList()`: die Liste in `site.ini [ShopSettings] ProductDatatypeStringList[]`, wenn diese
Einstellung existiert, sonst `ezprice` und `ezmultiprice`. Eine Klasse mit einem Feld eines aufgeführten Datentyps ist
eine Produktklasse. Das Hinzufügen zum Warenkorb, die Prüfung des Warenkorbs und die dynamische Umsatzsteuer suchen
alle nach einem solchen Feld (`kernel/shop/ezshopoperationcollection.php`,
`kernel/classes/ezproductcollectionitem.php`).

## Was Ihr Datentyp bereitstellen muss

Der Shop liest das Inhaltsobjekt des Feldes. Die Preisinhalte des Kernels, `eZPrice` und `eZMultiPrice`, erweitern
beide `eZSimplePrice` (`kernel/shop/classes/ezsimpleprice.php`), und das ist die einfachste Basis für Ihren. Der Shop
verwendet mindestens:

| Aus dem Inhalt | Verwendet für |
|---|---|
| `attribute( 'price' )` | Den Betrag, der in den Warenkorb gelegt und bei der Prüfung des Warenkorbs verglichen wird |
| `attribute( 'currency' )` | Die Währung des Warenkorbs |
| `attribute( 'is_vat_included' )`, `attribute( 'vat_percent' )` | Prüfung des Warenkorbs und Summen |
| `VATType()`, `VATPercent( $object, $country )` | Dynamische Aktualisierungen der Umsatzsteuer |

Das Schreiben des Datentyps selbst (Speicherung, Bearbeitungs-Template, Validierung, `toString()`/`fromString()` für
Importe) wird unter [Eigene Datentypen](../../ersteller/anwendungsentwickler/eigene-datentypen.md) und
[Lebenszyklus eines Datentyps](../../ersteller/anwendungsentwickler/lebenszyklus-eines-datentyps.md) behandelt.

## Ihn registrieren

Das Setzen der Liste ersetzt die Voreinstellung; nennen Sie also auch die mitgelieferten Datentypen:

```ini
# extension/mystore_shop/settings/site.ini.append.php
[ShopSettings]
ProductDatatypeStringList[]
ProductDatatypeStringList[]=ezprice
ProductDatatypeStringList[]=ezmultiprice
ProductDatatypeStringList[]=mystoreprice
```

## Lohnt es sich?

| Bedarf | Einfachere Alternative |
|---|---|
| Ein prozentualer Nachlass für einige Kunden | Rabattgruppen und -regeln ([Rabatte](../../kunden/handel/rabatte.md)) |
| Preise je Währung | `ezmultiprice` |
| Aufschläge für Auswahlmöglichkeiten | Options-Datentypen ([Optionen und Varianten](../katalog/optionen-und-varianten.md)) |
| Preise, die sich zu einem Datum ändern | Eine geplante Änderung durch einen Redakteur oder ein Skript, das `ezprice`-Werte aktualisiert |
| Aus einem externen System berechnete Preise | Ein eigener Preis-Datentyp oder ein Import, der `ezprice` schreibt |

Ein eigener Preis-Datentyp berührt Warenkorb, Prüfung, Umsatzsteuer, Templates, Importe und Berichte. Wählen Sie ihn
nur, wenn die Alternativen den Preis nicht ausdrücken können.

## Verwandte Seiten

- [Den Shop erweitern](README.md)
- [Preisfelder im Vergleich](../katalog/preisfelder-im-vergleich.md)
- [Die Shop-Einstellungen in site.ini durchgegangen](../einstellungen/shop-einstellungen-in-site-ini-durchgegangen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
