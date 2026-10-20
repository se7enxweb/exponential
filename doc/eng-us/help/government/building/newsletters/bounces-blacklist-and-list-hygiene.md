---
title: Bounces, blacklist and list hygiene
audience: government, admin
category: public-sector
order: 60
summary: How to keep a public body's newsletter lists clean with cjw_newsletter: the bounce mailbox and its cron part, the bounce threshold, the blacklist that wins over every list, subscriber exports, and retention of subscriber data.
keywords: bounces, bounce mailbox, blacklist, suppression list, list hygiene, BounceThresholdValue, cjw_newsletter_mailbox, subscriber export, retention, data minimization
type: help
---
# Bounces, blacklist and list hygiene

This page is for administrators of newsletter lists. Clean lists protect the sender's reputation and the
subscribers' data ([Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md)).

## Contents

1. [Bounces](#bounces)
2. [The blacklist](#the-blacklist)
3. [Where subscribers are managed](#where-subscribers-are-managed)
4. [Retention](#retention)

## Bounces

1. Set up a mailbox that receives bounces (**Newsletter > Mail accounts**, `newsletter/mailbox_list`, `mailbox_edit`).
2. Run the `cjw_newsletter_mailbox` cron part (hourly in the documented crontab).
3. Bounces are counted per subscriber; after `BounceThresholdValue` bounces (default 3) the subscriber stops receiving.

**Newsletter > Bounces** (`newsletter/mailbox_item_list`) lists the messages. Since 4.2.0 hard bounces and complaints go
on the suppression list and soft bounces are retried ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md)).

## The blacklist

**Newsletter > Blacklists** (`newsletter/blacklist_item_list`, `blacklist_item_add`, `blacklist_item_remove`): addresses
that never get mail. It wins over every list. Use it for people who asked never to be contacted.

## Where subscribers are managed

| View | Use |
|---|---|
| `newsletter/subscription_list` | subscriptions of one list |
| `newsletter/user_list`, `user_view`, `user_edit` | newsletter users across lists |
| `subscription_list_csvexport` | export a list's subscribers |

Exports contain personal data: keep them only where your rules allow, and delete them when done.

## Retention

Decide how long removed and bounced subscribers are kept, and how long confirmations that were never completed stay.
Removing a user account erases the person's mail preferences and anonymizes the consent log; an erased person's
newsletter user, interests, SMS data and per-person statistics go too ([Privacy standards](../../../../standards/privacy.md#gdpr),
[cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md)).

## Related pages

- [Mail consent and bounces](../../../hosting/operations/mail-consent-and-bounces.md)
- [Double opt-in and consent](double-opt-in-and-consent.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
