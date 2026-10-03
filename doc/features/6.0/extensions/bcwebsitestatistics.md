# bcwebsitestatistics: Google Analytics tags and order tracking

This page is for site owners who measure traffic and shop sales with Google Analytics. `bcwebsitestatistics` ("BC
Website Statistics") adds the analytics script to pages and, through the workflow type `ezreceipt`, reports shop
orders.

## Before you start

- The site must be reachable from the Internet.
- You need a configured Google Analytics website profile.

## Set it up

1. Activate the extension.
2. Create `settings/override/bcwebsitestatistics.ini.append.php` with your own values (see the table). Always set
   `Urchin`: the shipped value is an example, not yours.
3. To report orders, set `OrderSubmit=enabled` and add the `ezreceipt` workflow type to the shop's workflow.
4. Clear the INI cache and open a page; its source contains the analytics script.

## Settings

| File | Block | Key | Shipped value | Meaning |
|---|---|---|---|---|
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `OrderSubmit` | `disabled` | Report orders |
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `PageSubmit` | `enabled` | Report page views |
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `Urchin` | an example tracking id | **Set your own** Analytics id |
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `HostName`, `ShopName` | `disabled`, a placeholder shop name | Host and shop names reported |
| `bcwebsitestatistics.ini` | `BCWebsiteStatisticsSettings` | `Script`, `SecureScript`, `SecureTagManagerScript` | Google script addresses | Scripts included |

## What changed

| Version | Date | Change |
|---|---|---|
| 1.0.4, 1.0.5 | January 2024 | `composer.json` switched to the 7x package; dependency on the ezsystems repository replaced; version updated. |
| 1.0.6 | 18 July 2026 | The generated analytics (gtag) script tags no longer carry the obsolete `async`, `type=text/javascript` and `language=Javascript` attributes (HTML5 validation). |
| 1.0.7 to 1.0.9 | 27 to 30 September 2026 | `ezinfo.php` has a static `info()` and states version, license and website; every visible text is a translation string with German; the description names Exponential. |

## Related pages

- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/bcwebsitestatistics.md) and [release notes](../../../changelogs/extensions/bcwebsitestatistics.md)
- [Change ledger](../../../history/ledger/bcwebsitestatistics.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-07](../../../history/extensions/months/2026-07.md), [2026-09](../../../history/extensions/months/2026-09.md) (all extensions)
