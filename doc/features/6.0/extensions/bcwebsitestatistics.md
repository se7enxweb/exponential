# bcwebsitestatistics: Google Analytics tags and order tracking

`bcwebsitestatistics` ("BC Website Statistics") submits page view statistics and shop order purchases to Google Analytics from your site.
It adds the analytics script to pages and, through the workflow type `ezreceipt`, reports orders.

## Settings (`bcwebsitestatistics.ini`)

| Block | Key | Shipped value | Meaning |
|---|---|---|---|
| BCWebsiteStatisticsSettings | `OrderSubmit` | `disabled` | Report orders |
| BCWebsiteStatisticsSettings | `PageSubmit` | `enabled` | Report page views |
| BCWebsiteStatisticsSettings | `Urchin` | an example tracking id | **Set your own** Analytics id. The shipped value is not yours |
| BCWebsiteStatisticsSettings | `HostName`, `ShopName` | `disabled`, a placeholder shop name | Host and shop names reported |
| BCWebsiteStatisticsSettings | `Script`, `SecureScript`, `SecureTagManagerScript` | Google script addresses | Scripts included |

Put your values in `settings/override/bcwebsitestatistics.ini.append.php`. The site must be reachable from the Internet and you need a
configured Google Analytics website profile.

## What changed

* 1.0.4, 1.0.5 (January 2024): `composer.json` switched to the 7x package, dependency on the ezsystems repository replaced, version updated.
* 1.0.6 (18 July 2026): the generated analytics (gtag) script tags no longer carry the obsolete `async`, `type=text/javascript` and
  `language=Javascript` attributes (HTML5 validation).
* 1.0.7 to 1.0.9 (27 to 30 September): `ezinfo.php` has a static `info()` and states version, license and website; every visible text is a
  translation string with German; the description names Exponential.

## Related

* [Chronicle](../../../history/extensions/bcwebsitestatistics.md) and [release notes](../../../changelogs/extensions/bcwebsitestatistics.md)
* [Change ledger](../../../history/ledger/bcwebsitestatistics.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
* [Month: 2026-07 (all extensions)](../../../history/extensions/months/2026-07.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
