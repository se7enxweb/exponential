---
title: Card payments and PCI basics
audience: businesses, admin, developer
category: business-solutions
order: 150
summary: Basics, not compliance advice - how card payments reach an Exponential store, the difference between redirect gateways and an on-site card form, what the ezauthorize extension does with card data according to its code, and a checklist for small businesses.
keywords: card payments, credit card, PCI, PCI DSS, card security, ezauthorize, Authorize.Net, redirect, card form, CVV, security code, HTTPS, payment security
type: help
---
# Card payments and PCI basics

This page is for business owners who want to accept card payments and need to understand what that means for their
website's security. It explains the two ways card payments reach an Exponential store, what the gateways in the
project actually do with card data according to their code, and what to ask your payment provider.

> **Warning**
> **Basics, not legal or compliance advice.** Card security obligations come from the card industry's Payment Card
> Industry Data Security Standard (PCI DSS) and from your contract with your payment provider or acquiring bank. Read
> the standard's own text, ask your provider which obligations apply to your integration, and involve a qualified
> assessor or adviser where they require it. This page only describes what the software does.

## Contents

1. [Where card details are typed](#where-card-details-are-typed)
2. [Redirect gateways](#redirect-gateways)
3. [On-site card form with ezauthorize](#on-site-card-form-with-ezauthorize)
4. [What ezauthorize stores](#what-ezauthorize-stores)
5. [Choosing between them](#choosing-between-them)
6. [Basics for any card-taking store](#basics-for-any-card-taking-store)
7. [Questions for your payment provider](#questions-for-your-payment-provider)

## Where card details are typed

The single most important question for card security is: **where does the customer type the card number?**

| Where | Gateway kind | Card data on your server |
|---|---|---|
| On the payment provider's page | Redirect (`ezpaypal`) | No: your site sends the customer away and receives a payment notification |
| In a form on your site | On-site form (`ezauthorize`) | Yes: the card number passes through your web server on its way to the provider |

Both kinds plug into the checkout the same way, as a workflow event on `shop_checkout`
([Payment gateways for businesses](payment-gateways-for-businesses.md)).

## Redirect gateways

With a redirect gateway such as ezpaypal, the customer pays on the provider's page; your site only receives a
notification, which the extension verifies with the provider and checks against the order's amount and currency
([PayPal payments](paypal-payments.md#what-the-extension-checks)). Your site never sees the card number.

For a small business this usually means much less to secure. Your site still matters, because it sends the customer to
the provider and records the payment: keep it patched, on HTTPS, and protected against tampering
([Store security checklist](store-security-checklist.md)).

## On-site card form with ezauthorize

`ezauthorize` (suggested in the root `composer.json` as `se7enxweb/ezauthorize`, version 1.4.3 in its `ezinfo.php`)
takes card payments through Authorize.Net. According to its files:

- The workflow shows a card form on your site, `design/standard/templates/workflow/eventtype/result/ezauthorize_form.tpl`,
  asking for the name on the card, card type, card number, expiry date and security code.
- The gateway sends the data with cURL to Authorize.Net's AIM interface (`classes/ezauthorizeaim.php`), with response
  verification (`SSLVerify=true`) and hash verification (`MD5HashVerification=true`) switched on in the shipped
  `settings/ezauthorize.ini.append.php`.
- The shipped settings have `TestMode=true`, which sends test transactions until you switch it off.
- `doc/FEATURES` lists address verification (AVS), security code handling, transaction hash checking and "Requires usage
  of SSL to transmit payment information".
- A `refund` module lets staff refund an order from the administration.

The extension's README dates from the eZ Publish years (it names version 1.3.0 of October 2006 and PHP 4.4 as the
minimum); the 7x releases keep it installable, but read its documentation with that age in mind and check with
Authorize.Net that the interface and verification methods it uses are still supported for your account.

## What ezauthorize stores

Reading `classes/ezauthorizegateway.php` and `classes/ezcurlgateway.php`, we found that:

1. The card name, card number, expiry date and security code from the form are written into the order's stored
   account data (the order's `data_text_1` field), so that the gateway can read them in the next step and the refund
   module can use the card number later.
2. They are encrypted with GPG **only** when `StoreTransactionInformation` is `true` (the shipped value) **and** the
   separate `ezgpg` extension is installed and configured with a key. Otherwise the code keeps the values as entered.
3. The `ezgpg` extension is not part of this installation's extension directory.

> **Warning**
> As the code stands, an ezauthorize setup without a working `ezgpg` keeps card numbers and security codes readable in
> the order table of your database. Card security standards place strict limits on storing card data, and in
> particular the security code. Do not take ezauthorize live without a developer reviewing this behavior against your
> provider's requirements, and changing it where needed (for example removing the stored values after authorization).

Also replace the shipped placeholder value of `MD5HashSecretWord` with the secret configured in your Authorize.Net
account, and replace the card image in the form template, which points at an external `http://` address that a site on
HTTPS with a Content Security Policy will block.

## Choosing between them

| Question | Redirect (PayPal) | On-site form (ezauthorize) |
|---|---|---|
| Card numbers on your server | No | Yes |
| Customer leaves your site to pay | Yes | No |
| Work before launch | Configure and test | Configure, review card data handling, test |
| Ongoing security effort | Lower | Higher |

For most small businesses a redirect gateway is the simpler choice. If staying on your own site matters to you, ask a
partner whether your provider offers a hosted payment page or hosted fields, and whether a gateway for it exists or
can be written as a redirect-style extension ([Payment gateways for businesses](payment-gateways-for-businesses.md#other-providers)).

## Basics for any card-taking store

- [ ] The whole site runs on HTTPS ([TLS and HTTPS](../../hosting/security/tls-and-https.md)).
- [ ] Exponential and its extensions are kept up to date ([Security updates and disclosure](../security/security-updates-and-disclosure.md)).
- [ ] Administrators use strong passwords and two-factor sign-in ([Two-factor login](../../../guides/two-factor-login.md)).
- [ ] The administration is restricted ([Restricting the administration](../../hosting/security/restricting-the-administration.md)).
- [ ] Logs and debug output never show card data; debug output is off in production.
- [ ] Backups that contain order data are protected like the database itself.
- [ ] Test mode is off only on launch day, after a full test.

## Questions for your payment provider

- Which card security obligations apply to a site that redirects to you, and to one that posts card data to you?
- Which self-assessment or evidence do you require from us, and how often?
- Do you offer a hosted payment page or hosted card fields?
- Which integration methods and signature checks do you still support?
- Who handles disputes and chargebacks, and what records do you need from us?

## Related pages

- [Payment gateways](../../creators/website-creators/payment-gateways.md)
- [Security for applications](../../creators/app-creators/security-for-applications.md)
- [Security basics for businesses](../security/security-basics-for-businesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
