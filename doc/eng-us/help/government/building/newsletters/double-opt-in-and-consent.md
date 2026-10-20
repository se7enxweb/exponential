---
title: Double opt-in and consent
audience: government, admin, editor
category: public-sector
order: 40
summary: How subscriptions become valid in cjw_newsletter (double opt-in and statuses), what the subscribe form must say, how consent is recorded by the kernel's e-mail preferences, importing existing lists only with consent, and per-person statistics.
keywords: double opt-in, consent, subscription status, pending, confirmed, consent log, GDPR, ePrivacy, CSV import, newsletter statistics, mail preferences
type: help
---
# Double opt-in and consent

This page is for administrators and editors responsible for newsletter subscribers. A public body's mailing lists
contain personal data and must rest on a clear basis; double opt-in proves that the address owner asked.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [How a subscription becomes valid](#how-a-subscription-becomes-valid)
2. [What the subscribe form says](#what-the-subscribe-form-says)
3. [Records of consent](#records-of-consent)
4. [Importing existing lists](#importing-existing-lists)
5. [Statistics and consent](#statistics-and-consent)

## How a subscription becomes valid

| Status | Meaning |
|---|---|
| Pending | subscribed, confirmation mail sent, not yet confirmed |
| Confirmed | the subscriber clicked the confirmation link |
| Approved | confirmed and, where the list requires it, approved by an administrator |
| Removed by user / by administrator | no longer receives mail |
| Soft or hard bounced | mail could not be delivered |

Only confirmed or approved subscribers receive editions
([Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md)).

## What the subscribe form says

- What subscribers get and how often.
- Who sends it (the body), and the contact for questions.
- What data is kept, why, for how long, and how to unsubscribe.
- A link to the privacy notice.

## Records of consent

The kernel's e-mail preferences keep a consent log (`expmail_consent_log`): time, category, old and new state, source,
the exact text shown, acting user, siteaccess. Marketing categories are opt-in, withdrawal is as easy as consent, and a
person can download their preferences and consent history ([Privacy standards](../../../../standards/privacy.md#gdpr),
[Mail preferences for administrators](../../../../guides/mail-preferences-administrator.md)). The newsletter works through
these e-mail preferences since 4.2.0 ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md)).

## Importing existing lists

`newsletter/subscription_list_csvimport` imports subscribers from CSV. Import only people who agreed to receive this
newsletter, and record where the consent came from. When in doubt, send a single re-permission mail through another
channel or start the list fresh.

## Statistics and consent

Per-person opens and clicks are counted only with the recipient's consent to the category "Newsletter statistics";
otherwise only anonymous totals ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md)). Tracking is
off until switched on. Many public bodies need no per-person statistics at all.

## Related pages

- [One-click unsubscribe and the mail gate](one-click-unsubscribe-and-the-mail-gate.md)
- [E-mail preferences](../../../../features/6.0/mail-preferences.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
