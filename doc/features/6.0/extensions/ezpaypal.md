# ezpaypal: PayPal payment gateway

`ezpaypal` is a payment gateway for the shop and workflow system that uses PayPal's **NVP** (name-value pair) API, a redirection based API: the
customer is sent to PayPal to pay and PayPal calls back the site's notify URL (`/paypal/notify_url`). It provides the workflow event that
starts the payment and the module `paypal`.

## Settings (`paypal.ini`)

| Block | Key | Default | Meaning |
|---|---|---|---|
| ServerSettings | `ServerName`, `ServerPort`, `RequestURI` | `https://www.paypal.com`, `443`, `/cgi-bin/webscr` | Where the customer is sent |
| PaypalSettings | `MaxDescriptionLength` | `127` | |
| PaypalSettings | `Business` | empty | The PayPal business account (your address) |
| PaypalSettings | `NoNote`, `NoteLabel`, `PageStyle`, `BackgroundColor`, `LogoURI` | `0`, empty, empty, `0`, empty | Look of the PayPal page |
| OrderSettings | `PaymentMadeOrderStatusID` | `1000` | Order status set when the payment is made |

Set `Business`, put your own values in `settings/override/paypal.ini.append.php` and test with PayPal's sandbox server name before going live.

## What changed in the Exponential 6 releases

* 1.2.0 (24 January 2024): `composer.json` package configuration and funding configuration; the extension is installable with Composer.
* 1.2.1 (27 September 2026): `ezinfo.php` has a static `info()` and states the version, license and website, so the about page reads it.
* 1.2.2, 1.2.3: the description names Exponential; the command line scripts and module views are classes the files call; entry point files carry a
  header of 7x and the Exponential Foundation; copyright notices name 1998 - 2026 7x & Exponential Foundation first. Payment behaviour is unchanged.

The extension's README still describes its original dual licensing; the Exponential releases are GPL v2 or later.

## Related

* [Chronicle](../../../history/extensions/ezpaypal.md) and [release notes](../../../changelogs/extensions/ezpaypal.md)
