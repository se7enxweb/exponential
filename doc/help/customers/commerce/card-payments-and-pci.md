---
title: Card payments and PCI
audience: customer, admin, businesses
category: customer-success
order: 120
summary: The store owner's card security routine for an Exponential store - knowing where card numbers are typed, the yearly questions for your payment provider, what to check after changes, and the special care an on-site card form needs. Basics, not compliance or legal advice.
keywords: card payments, PCI, PCI DSS, card security, credit card, redirect gateway, ezauthorize, card data, payment provider, self-assessment, not legal advice
type: help
---
# Card payments and PCI

This page is for owners who accept card payments, directly or through a provider. It turns card security into a
routine you can keep: know how cards reach your store, keep the evidence your provider asks for, and check after
changes. The background, and what the project's gateways do with card data according to their code, is on
[Card payments and PCI basics for businesses](../../businesses/stores/card-payments-and-pci-basics.md).

> **Warning**
> Basics, not legal or compliance advice. Card security obligations come from the Payment Card Industry Data
> Security Standard (PCI DSS) and your contract with your payment provider or acquiring bank. Ask your provider which
> obligations apply to your integration, and involve a qualified adviser where they require it.

## The one question to answer

**Where does your customer type the card number?** Write the answer down and keep it with your contract.

| Answer | Gateway kind | What it means for you |
|---|---|---|
| On the provider's page | Redirect, for example `ezpaypal` | Card numbers do not pass your server |
| In a form on your site | On-site form, for example `ezauthorize` | Card numbers pass your server on their way to the provider |

Source: [Card payments and PCI basics for businesses](../../businesses/stores/card-payments-and-pci-basics.md).
If you are not sure which one your store uses, ask your partner; the answer decides most of what follows.

## A yearly routine

| Task | Who | With |
|---|---|---|
| Confirm with the provider which obligations apply to your integration | Owner | The provider's documents |
| Complete whatever self-assessment or evidence the provider requires | Owner, with the partner | The provider's forms |
| Confirm the gateway extension and Exponential are up to date | Partner or host | [Upgrading](../../../guides/upgrading.md) |
| Review who has administrator and shop access | Owner | [Roles and policies](../../../guides/roles-and-policies.md) |
| Confirm two-step sign-in for administrators and shop staff | Owner, with the host | [Two-factor login](../../../guides/two-factor-login.md) |

## After any change to the store

- [ ] The whole site, and above all the checkout, still runs on HTTPS.
- [ ] Test mode or sandbox addresses are not left on.
- [ ] Debug output is off on the live site; logs and error pages show no card data
  ([Security hardening](../../security-hardening.md)).
- [ ] Backups containing order data are still protected like the database itself.

## If your store uses an on-site card form

The `ezauthorize` extension shows a card form on your site and sends the data to Authorize.Net. According to its
code, the card details are written into the order's stored data, and they are encrypted only when the separate
`ezgpg` extension is installed and configured with a key
([Card payments and PCI basics for businesses](../../businesses/stores/card-payments-and-pci-basics.md)).

> **Warning**
> Do not run an on-site card form without a developer having reviewed, against your provider's requirements, what it
> stores and for how long. Ask your partner for that review in writing, and repeat it after every update of the
> extension.

Questions to ask your partner:

- Which card data does our gateway store, where, and is it encrypted?
- Is any card data, in particular the security code, kept after authorization?
- Does our provider offer a hosted payment page or hosted card fields, so card data stays off our server?

## Questions for your payment provider

From [Card payments and PCI basics for businesses](../../businesses/stores/card-payments-and-pci-basics.md):

- Which card security obligations apply to a site that redirects to you, and to one that posts card data to you?
- Which self-assessment or evidence do you require from us, and how often?
- Which integration methods and signature checks do you still support?
- Who handles disputes and chargebacks, and what records do you need from us?

## If card data may have been exposed

1. Tell your payment provider at once; they will say what to do next.
2. Ask your host to follow their runbook for a compromised site
   ([Compromised site](../../hosting/runbooks/compromised-site.md)).
3. Report a suspected vulnerability in Exponential privately to security@se7enx.com, as `SECURITY.md` asks, never in
   a public issue.
4. Keep notes of what happened and when.

## Related pages

- [Payment gateways](payment-gateways.md)
- [Card payments and PCI basics for businesses](../../businesses/stores/card-payments-and-pci-basics.md)
- [Store security checklist](../../businesses/stores/store-security-checklist.md)
- [Security hardening](../../security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
