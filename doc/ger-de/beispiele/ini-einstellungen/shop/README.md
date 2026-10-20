---
title: shop.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von shop.ini.
keywords: Beispiele, Referenz, shop.ini, examples, reference
type: generated
generated: exp:help:examples
---
# shop.ini

Die ausgelieferten Einstellungen von `settings/shop.ini`: 10 Blöcke, 28 Einstellungen.

> Exponential configuration file.  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/shop.ini.append or  
>       settings/override/shop.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  

| Block | Einstellungen |
|---|---|
| [CurrencySettings](CurrencySettings.md) | 1 |
| [MathSettings](MathSettings.md) | 5 |
| [ExchangeRatesSettings](ExchangeRatesSettings.md) | 4 |
| [ECBExchangeRatesSettings](ECBExchangeRatesSettings.md) | 3 |
| [ShippingSettings](ShippingSettings.md) | 2 |
| [BasketSettings](BasketSettings.md) | 1 |
| [BasketInfoSettings](BasketInfoSettings.md) | 3 |
| [VATSettings](VATSettings.md) | 6 |
| [OrderViewSettings](OrderViewSettings.md) | 1 |
| [OrderReceiptSettings](OrderReceiptSettings.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
