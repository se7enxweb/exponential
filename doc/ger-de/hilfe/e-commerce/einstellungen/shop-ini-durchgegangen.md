---
title: shop.ini durchgegangen
audience: ecommerce, website-creator, admin
category: e-commerce
order: 31
summary: Jeder Block von settings/shop.ini mit seinem ausgelieferten Wert erklärt - CurrencySettings, MathSettings, ExchangeRatesSettings, ECBExchangeRatesSettings, ShippingSettings, BasketSettings, BasketInfoSettings, VATSettings, OrderViewSettings und OrderReceiptSettings - und wann Sie jeden ändern.
keywords: shop.ini, CurrencySettings, PreferredCurrency, MathSettings, RoundingType, RoundingPrecision, RoundingTarget, ExchangeRatesSettings, BaseCurrency, eZECB, ShippingSettings, BasketSettings, BasketViewName, BasketInfoSettings, VATSettings, OrderViewSettings, OrderLinkView, OrderReceiptSettings, Secret, BcryptCost, Währung, Rundung, Wechselkurse, Versand, Warenkorb, Umsatzsteuer, Belege, currency, rounding, exchange rates, shipping, basket, VAT, receipts
type: help
---
# shop.ini durchgegangen

Diese Seite ist für Entwickler und Administratoren, die den Shop konfigurieren. Sie geht `settings/shop.ini` Block
für Block durch, mit dem ausgelieferten Wert, was jede Einstellung bewirkt und wann Sie sie ändern. Legen Sie
Änderungen in `settings/override/shop.ini.append.php` oder einer Siteaccess-Datei ab ([Die Shop-Einstellungen durchgegangen](README.md#wohin-shop-einstellungen-gehören)).

## Inhalt

1. [CurrencySettings](#currencysettings)
2. [MathSettings](#mathsettings)
3. [ExchangeRatesSettings](#exchangeratessettings)
4. [ShippingSettings](#shippingsettings)
5. [BasketSettings](#basketsettings)
6. [BasketInfoSettings](#basketinfosettings)
7. [VATSettings](#vatsettings)
8. [OrderViewSettings](#orderviewsettings)
9. [OrderReceiptSettings](#orderreceiptsettings)

## CurrencySettings

| Einstellung | Ausgeliefert | Bedeutung | Ändern, wenn |
|---|---|---|---|
| `PreferredCurrency` | `USD` | Die Währung des Shops: die Währung der `ezprice`-Preise und die Voreinstellung für Besucher | Fast immer: Setzen Sie Ihren eigenen Währungscode |

## MathSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `MathHandler` | `eZPHPMath` | Gleitkommarechnung in PHP; `eZBCMath` verwendet die Erweiterung bcmath |
| `MathScale` | `10` | Nachkommastellen, nur für `eZBCMath` |
| `RoundingType` | `round` | Wie umgerechnete Preise gerundet werden: `none`, `round`, `ceil`, `floor` |
| `RoundingPrecision` | `2` | Beibehaltene Nachkommastellen |
| `RoundingTarget` | `false` | Erzwingt die letzten Ziffern: Bei Genauigkeit 2 macht das Ziel `5` aus 89.468543 den Wert 89.45 und das Ziel `99` den Wert 89.99 |

`RoundingTarget` ist die Einstellung hinter „Schwellenpreisen“ für umgerechnete Währungen. Sie wirkt auf
automatische Umrechnungen, nicht auf von Hand eingegebene Preise.

## ExchangeRatesSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `ExchangeRatesUpdateHandler` | `eZECB` | Der Handler, der die Kurse abruft, wenn **Auto-Umrechnungsfaktoren aktualisieren** geklickt wird |
| `RepositoryDirectories[]` | `kernel/shop/classes/exchangeratehandlers` | Wo Handler gefunden werden |
| `ExtensionDirectories[]` | leer | Erweiterungen, die Handler in `exchangeratehandlers/<name>/<name>handler.php` mitbringen |
| `BaseCurrency` | `EUR` | Die Währung, auf die sich die Kurse beziehen |

Der Block `[ECBExchangeRatesSettings]` enthält, wo der mitgelieferte Handler die täglichen Kurse der Europäischen
Zentralbank abruft (`ServerName`, `ServerPort`, `RatesURI`). Prüfen Sie von Ihrem Server aus, dass die Adresse
antwortet, bevor Sie sich darauf verlassen. Ein eigener Handler ist unter [Wechselkurs-Handler](../erweitern/wechselkurs-handler.md)
beschrieben.

## ShippingSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `Handler` | auskommentiert (`#Handler=ezdefault`) | Der Versand-Handler; keiner ist aktiv |
| `RepositoryDirectories[]` | `kernel/classes/shippinghandlers` | Wo nach Handlern gesucht wird (dieses Verzeichnis wird nicht mitgeliefert) |
| `ExtensionDirectories[]` | leer | Erweiterungen mit einem Verzeichnis `shippinghandlers/` |

Lassen Sie `Handler` ungesetzt, es sei denn, Sie haben einen Handler geschrieben: Im Kernel wird keine
Handler-Datei mitgeliefert, und das `ezdefault` aus dem Kommentar würde nicht gefunden. Pauschaler Versand verwendet
stattdessen ein Workflow-Ereignis ([Das einfache Versand-Ereignis](../kasse/das-ereignis-einfacher-versand.md), [Eigener Versand-Handler](../erweitern/eigener-versand-handler.md)).

## BasketSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `BasketViewName` | `basket` | Der Adressname des Warenkorbs (`/shop/<name>/`); `/shop/basket/` funktioniert weiter, egal, was Sie wählen |

Buchstaben, Ziffern, Bindestrich und Unterstrich sind erlaubt; alles andere fällt auf `basket` zurück. Ein Design kann
`shop/<name>.tpl` hinzufügen; `shop/cart.tpl` wird als Beispiel mitgeliefert ([Name der Warenkorb-View des Shops](../../../bc/6.0/view-name-des-shop-warenkorbs.md)).

## BasketInfoSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `Handler` | `ezdefault` | Der Handler für Warenkorbinformationen, der berechnete Preisinformationen zum Warenkorb hinzufügt |
| `RepositoryDirectories[]` | `kernel/classes/basketinfohandlers` | Wo Handler gefunden werden |
| `ExtensionDirectories[]` | leer | Erweiterungen mit einem Verzeichnis `basketinfohandlers/` |

Siehe [Handler für Warenkorbinformationen und Bestellbestätigung](../erweitern/handler-fuer-warenkorbinformationen-und-bestellbestaetigung.md).

## VATSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `Handler` | auskommentiert (`#Handler=ezdefault`) | Der USt-Handler; ohne ihn werden USt-Regeln nicht verwendet |
| `ProductCategoryAttribute` | leer | Bezeichner des Feldes für die Produktkategorie |
| `UserCountryAttribute` | leer | Bezeichner des Feldes für das Land des Benutzers |
| `RequireUserCountry` | `true` | Ein fehlendes Land des Kunden melden |
| `DynamicVatTypeName` | `Determined by VAT charging rules` | Die Beschriftung des dynamischen USt-Typs |
| `RepositoryDirectories[]`, `ExtensionDirectories[]` | `kernel/classes/vathandlers`, leer | Wo Handler gefunden werden (`vathandlers/` in Erweiterungen) |

Details und die Priorität der Regeln stehen unter [Produktkategorien und USt-Regeln](../katalog/produktkategorien-und-ust-regeln.md).

## OrderViewSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `OrderLinkView` | `orderview` | Worauf eine aufgegebene Bestellung nach der Kasse, in den Bestelllisten des Kunden und in der Bestellmail verlinkt: `orderview` (`shop/orderview/<id>`) oder `orderreceipt` (`shop/orderreceipt/<token>`) |

Mit `orderview` funktioniert der Bestelllink eines Gastes nicht mehr, wenn die Sitzung endet. Mit `orderreceipt`
läuft der Link nie ab und öffnet sich für jeden, der ihn hat. Die Administration verlinkt immer auf `shop/orderview`
([Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md)).

## OrderReceiptSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `Secret` | leer | Signiert die Adressen der Belege. Leer: wird bei der ersten Verwendung erzeugt und in `<VarDir>/secrets/orderreceipt.key` aufbewahrt |
| `BcryptCost` | `5` | Aufwand der Signatur, 4 bis 6 |

Setzen Sie `Secret` (32 Zeichen oder mehr, in einer Überschreibungsdatei) nur, wenn sich mehrere Installationen eine
Datenbank teilen und gegenseitig ihre Belege öffnen müssen. Eine Änderung ändert jede Beleg-Adresse, die bereits an
Kunden versendet wurde.

## Verwandte Seiten

- [Die Shop-Einstellungen durchgegangen](README.md)
- [Preisfelder im Vergleich](../katalog/preisfelder-im-vergleich.md)
- [Preise und Währungen (Unternehmen)](../../unternehmen/shops/preise-und-waehrungen.md)
- [Bestellbelege und Bestätigungsmail](../../unternehmen/shops/bestellbelege-und-bestaetigungsmails.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
