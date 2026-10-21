---
title: Produkte, Preise und Mehrwertsteuer
audience: website-creator, admin
category: website-creation
order: 530
summary: Produkte für den Shop modellieren - die Preis-Datentypen (ezprice, ezmultiprice), Optionen und Varianten, Mehrwertsteuertypen und Mehrwertsteuerregeln nach Land und Produktkategorie, Währungen, Rundung und Rabatte.
keywords: Produkte, Preis, Mehrwertsteuer, USt-Typen, Mehrwertsteuerregeln, Produktkategorie, Währung, Wechselkurse, Rundung, Rabatte, Optionen, Varianten, Website-Ersteller, products, price, ezprice, ezmultiprice, VAT, VAT types, VAT rules, product category, currency, exchange rates, rounding, discounts, options, variants, website creator
type: help
---
# Produkte, Preise und Mehrwertsteuer

Diese Seite richtet sich an Website-Ersteller, die die Produkte eines Shops einrichten: wie Preise gespeichert werden,
wie Mehrwertsteuer berechnet wird und welche Einstellungen über Währungen und Rundung entscheiden.

## Inhalt

1. [Die Produktklasse](#die-produktklasse)
2. [Preis-Datentypen](#preis-datentypen)
3. [Optionen und Varianten](#optionen-und-varianten)
4. [Mehrwertsteuer](#mehrwertsteuer)
5. [Währungen und Rundung](#währungen-und-rundung)
6. [Rabatte](#rabatte)

## Die Produktklasse

Ein Produkt ist ein Inhalt einer beliebigen Klasse mit einem Preisfeld. Die mitgelieferte Klasse `product` ist ein guter
Anfang; ergänzen Sie Felder für das, was Ihre Produkte brauchen (Artikelnummer als `ezstring`, Bilder als Relationen,
eine Kategorie). Produkte liegen wie andere Inhalte in Ordnern und werden über die Suche und Menüs gefunden.

## Preis-Datentypen

| Datentyp | Verwendung |
|---|---|
| `ezprice` | ein Preis, mit einem Mehrwertsteuertyp und der Angabe, ob der Preis die Mehrwertsteuer enthält |
| `ezmultiprice` | ein Preis je Währung |
| `ezproductcategory` | die Produktkategorie, die die Mehrwertsteuerregeln verwenden |

## Optionen und Varianten

`ezoption`, `ezmultioption`, `ezmultioption2` und `ezrangeoption` lassen Besucher Optionen wählen (Größe, Farbe), die
den Preis verändern können; die Wahl wird mit dem Element im Warenkorb gespeichert. Für Produkte, die sich im Bestand
oder in den Bildern unterscheiden, sind getrennte Produkte meist übersichtlicher.

## Mehrwertsteuer

| Werkzeug | Wo | Bedeutung |
|---|---|---|
| Mehrwertsteuertypen | **Shop > USt-Typen** (`shop/vattype`) | benannte Prozentsätze: Normalsatz, ermäßigt, null |
| Mehrwertsteuerregeln | `shop/vatrules`, `shop/editvatrule` | welcher Mehrwertsteuertyp je Land des Kunden und Produktkategorie gilt |
| Einstellungen | `shop.ini [VATSettings]` | `ProductCategoryAttribute`, `UserCountryAttribute`, `RequireUserCountry=true`, `DynamicVatTypeName` |

Mit Mehrwertsteuerregeln wählen Sie am Preis den Mehrwertsteuertyp "Durch Regeln für die Mehrwertsteuer bestimmt"
(`DynamicVatTypeName`), legen fest, welches Attribut die Produktkategorie und welches Benutzerattribut das Land enthält,
und es gilt die Regel für das Land des Kunden und die Kategorie des Produkts. Das Shop-Dashboard warnt, wenn ein
Mehrwertsteuertyp mit 0 Prozent verwendet wird.

> **Hinweis**
> Das Mehrwertsteuerrecht unterscheidet sich von Land zu Land. Der Shop berechnet, was Sie konfigurieren; ob das für
> Ihr Unternehmen richtig ist, ist eine Frage für Ihren Steuerberater.

## Währungen und Rundung

| Einstellung (`shop.ini`) | Ausgeliefert | Bedeutung |
|---|---|---|
| `[CurrencySettings] PreferredCurrency` | `USD` | die Währung des Shops |
| `[ExchangeRatesSettings] BaseCurrency`, `ExchangeRatesUpdateHandler` | `EUR`, `eZECB` | Wechselkurse für `ezmultiprice` |
| `[MathSettings] RoundingType`, `RoundingPrecision` | `round`, `2` | Rundung der Preise |

Währungen werden unter `shop/currencylist` verwaltet; Besucher können ihre Währung mit `shop/setpreferredcurrency`
wählen.

## Rabatte

Rabattgruppen (`shop/discountgroup`) geben Benutzergruppen einen prozentualen Nachlass auf Produkte oder Klassen, mit
Regeln je Gruppe (`shop/discountruleedit`).

## Verwandte Seiten

- [Einen Shop betreiben: Überblick](shop-ueberblick.md)
- [Warenkorb, Kasse und Bestellungen](warenkorb-kasse-und-bestellungen.md)
- [Datentypen wählen](datentypen-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
