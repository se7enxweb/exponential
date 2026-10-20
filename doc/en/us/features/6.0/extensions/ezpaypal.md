# ezpaypal: PayPal payment gateway

This page is for shop owners who take payments with PayPal. `ezpaypal` is a payment gateway for the shop and workflow
system. It uses PayPal's **NVP** (name-value pair) API, which works by redirection: the customer is sent to PayPal to
pay, and PayPal calls back the site's notify URL (`/paypal/notify_url`). The extension provides the workflow event
that starts the payment and the module `paypal`.

## Set it up

1. Activate the extension.
2. Create `settings/override/paypal.ini.append.php` and set at least `Business` (your PayPal business account).
3. Test with PayPal's sandbox server name in `ServerName` before going live.
4. Add the PayPal workflow event to the shop's checkout workflow.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `paypal.ini` | `ServerSettings` | `ServerName`, `ServerPort`, `RequestURI` | `https://www.paypal.com`, `443`, `/cgi-bin/webscr` | Where the customer is sent |
| `paypal.ini` | `PaypalSettings` | `MaxDescriptionLength` | `127` | Longest order description sent |
| `paypal.ini` | `PaypalSettings` | `Business` | empty | The PayPal business account (your address) |
| `paypal.ini` | `PaypalSettings` | `NoNote`, `NoteLabel`, `PageStyle`, `BackgroundColor`, `LogoURI` | `0`, empty, empty, `0`, empty | Look of the PayPal page |
| `paypal.ini` | `OrderSettings` | `PaymentMadeOrderStatusID` | `1000` | Order status set when the payment is made |

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 1.2.0 | 24 January 2024 | `composer.json` package and funding configuration; installable with Composer. |
| 1.2.1 | 27 September 2026 | `ezinfo.php` has a static `info()` and states the version, license and website, so the about page reads it. |
| 1.2.2, 1.2.3 | | The description names Exponential; command line scripts and module views are classes the files call; entry point files carry a header of 7x and the Exponential Foundation; copyright notices name 1998 - 2026 7x & Exponential Foundation first. Payment behaviour is unchanged. |

The extension's README still describes its original dual licensing; the Exponential releases are GPL v2 or later.

## Related pages

- [Order receipts](../order-receipts.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/ezpaypal.md) and [release notes](../../../changelogs/extensions/ezpaypal.md)
- [Change ledger](../../../history/ledger/ezpaypal.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
