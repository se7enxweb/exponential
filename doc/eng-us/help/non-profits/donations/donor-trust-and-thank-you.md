---
title: Donor trust and thank-you
audience: non-profit, businesses, editor, admin
category: non-profits
order: 60
summary: What makes donors trust a non-profit's Exponential site and come back - HTTPS everywhere, a privacy notice that names every processor, protected administrator accounts, a thank-you page, honest receipts (the shop's order receipt is not a tax receipt), follow-up by newsletter only with consent, and handling donor data with care.
keywords: donor trust, HTTPS, privacy notice, two-factor login, thank-you page, receipts, tax receipt, follow-up, newsletter consent, donor data, stewardship
type: help
---
# Donor trust and thank-you

This page is for fundraisers and whoever looks after the site. A donor decides in seconds whether a site looks safe,
and decides over months whether to give again. Both depend on things you control.

## Contents

1. [Before the gift: signs of a safe site](#before-the-gift-signs-of-a-safe-site)
2. [Behind the scenes: protecting the site](#behind-the-scenes-protecting-the-site)
3. [Right after the gift: the thank-you page](#right-after-the-gift-the-thank-you-page)
4. [Receipts](#receipts)
5. [Afterward: follow-up with consent](#afterward-follow-up-with-consent)
6. [Donor data](#donor-data)
7. [Trust checklist](#trust-checklist)

## Before the gift: signs of a safe site

| Sign | How | Source |
|---|---|---|
| HTTPS on every page | Certificate on the host; redirect from HTTP | [Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md) |
| A real contact | Address, phone or form on every page footer | [Shared header and footer](../../creators/website-creators/shared-header-and-footer.md) |
| A current site | News and events from the last months, no broken links | [Link and content checks](../../creators/website-creators/link-and-content-checks.md) |
| Openness about money | Annual publications and financial documents as downloads | [Transparency](../transparency/README.md) |
| A privacy notice | Names every processor (payment provider, newsletter, CAPTCHA, analytics) | [Privacy notice outline](../../government/privacy/privacy-notice-outline.md) |

## Behind the scenes: protecting the site

A defaced donation page or a hijacked Donate link costs more trust than any campaign can win back.

- Few administrator accounts, each with two-factor login ([Two-factor login](../../../guides/two-factor-login.md)).
- Updates applied on time ([Backups and updates routine](../operations/backups-and-updates-routine.md)).
- Only trusted people may edit the Donate page; limit roles by section
  ([Volunteers as editors](../volunteers/volunteers-as-editors.md)).
- The audit trail shows who changed the page and when ([The audit trail](../../../features/6.0/audit-trail.md)).
  If the Donate link ever changes unexpectedly, that is where to look first.
- Check the Donate link monthly ([Monthly checklist](../checklists/monthly-checklist.md)).

## Right after the gift: the thank-you page

A page on your own site, out of the menu, that the payment platform returns donors to:

- [ ] Thanks in one sentence, warmly and specifically.
- [ ] What happens next: a confirmation mail from the provider, a receipt, when.
- [ ] One next step: follow your work, share the campaign, or volunteer. Only one.
- [ ] A contact if something went wrong.

With the shop route, the order view and its receipt play this role
([Order receipts](../../../features/6.0/order-receipts.md)).

## Receipts

| Route | What the donor gets from the site or provider |
|---|---|
| External platform | Whatever the platform issues |
| Shop (`ezpaypal`, `ezauthorize`) | An order receipt, with print and download; "not an invoice system" ([Invoices and receipts](../../customers/commerce/invoices-and-receipts.md)) |

Neither route makes Exponential issue a **tax or donation receipt**; there is no such feature. If your law requires
one, issue it from your accounting process. Ask your accountant.

## Afterward: follow-up with consent

- Add donors to the newsletter **only** if they subscribed themselves; `cjw_newsletter` uses double opt-in
  ([Supporter newsletter](../communications/supporter-newsletter-with-cjw-newsletter.md),
  [Opt-in and consent](../../businesses/newsletters/opt-in-and-consent.md)).
- A gift is not consent to marketing mail. Offer the newsletter on the thank-you page instead.
- Report back: what the money did, in a news item or the annual publication.

## Donor data

Donor data lives at the payment provider and, with the shop route, in orders on your site. Read
[Donor and volunteer data protection](../operations/donor-and-volunteer-data-protection.md) and
[Data export and erasure](../../customers/commerce/data-export-and-erasure.md) for requests from donors.

> **Note**
> Basics, not legal advice.

## Trust checklist

- [ ] HTTPS everywhere, tested.
- [ ] Contact details and privacy notice on every page.
- [ ] Two-factor login for every administrator.
- [ ] Donate page editable only by named people.
- [ ] Thank-you page live and set as the return address.
- [ ] Receipts agreed with the accountant.
- [ ] Newsletter offered, never imposed.

## Related pages

- [Donations](README.md)
- [The giving page](the-giving-page.md)
- [Security basics for businesses](../../businesses/security/security-basics-for-businesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
