---
title: Newsletters for local businesses
audience: local-business, businesses, customer
category: business-solutions
order: 62
summary: A newsletter for the regulars of a local business with cjw_newsletter, which ships with Exponential - double opt-in sign-up, editions as content, sending by cron, unsubscribe, what it needs from the host (mail, cron, a bounce mailbox), a realistic sending rhythm and what to write.
keywords: newsletter, cjw_newsletter, mailing list, regulars, double opt-in, consent, unsubscribe, edition, cron, mail delivery, bounce, local business
type: help
---
# Newsletters for local businesses

This page is for local businesses that want to stay in touch with regular customers by e-mail. The newsletter system
`cjw_newsletter` is part of the installation (`se7enxweb/cjw_newsletter` in the `require` block of `composer.json`),
so there is no extra software to buy
([cjw_newsletter for businesses](../../businesses/newsletters/cjw-newsletter-for-businesses.md)).

## Contents

1. [What it does for you](#what-it-does-for-you)
2. [What it needs](#what-it-needs)
3. [Getting subscribers the right way](#getting-subscribers-the-right-way)
4. [What to send and how often](#what-to-send-and-how-often)
5. [Sending an edition](#sending-an-edition)
6. [Checklist](#checklist)

## What it does for you

From [cjw_newsletter for businesses](../../businesses/newsletters/cjw-newsletter-for-businesses.md#in-short):

- Subscribers sign up on your site and **confirm by mail** (double opt-in).
- Every mail carries an **unsubscribe** link.
- An **edition** is content in the content tree, written in the same editor as your pages.
- Sending runs in the background by cron, with an archive of every send.
- You can rehearse a send by writing mail to files, without mailing anyone.
- Several lists are possible, for example "News and offers" and "Events only".

## What it needs

| Need | Who | Why |
|---|---|---|
| A server that can send mail, with a sender address set up for your domain (SPF, DKIM) | host | otherwise mail lands in spam ([Newsletter mail delivery](../../businesses/newsletters/newsletter-mail-delivery.md)) |
| Cron jobs for the mail queue | host or builder | sending happens in the background |
| A mailbox for bounces | host or builder | addresses that no longer work are removed |
| Setup of lists and sign-up page | builder | [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md) |

`cjw_newsletter` does not deliver mail by itself and is not a hosted mailing service: it hands mail to your server's
mail transport. For a small list, that is usually fine; ask your host about sending limits.

## Getting subscribers the right way

- A sign-up box on the website, and a sign-up form at the counter only if you then enter people through the same
  confirmation process.
- Do not add customers who wrote to you, bought from you or gave you a business card, unless they asked to receive the
  newsletter. Rules differ by country; this is not legal advice
  ([Opt-in and consent](../../businesses/newsletters/opt-in-and-consent.md)).
- Say on the sign-up form what you will send and how often.

## What to send and how often

For a local business, once a month is a realistic rhythm that can be kept up. A simple edition:

| Part | Example |
|---|---|
| One line greeting | "Spring is here, and so are the new bikes." |
| What is new | a new product, service or dish, with a photo |
| What is on | one or two events |
| A special for subscribers | if you have one |
| Practical | holiday hours, changed hours |
| Footer | address, phone, unsubscribe (added automatically) |

Keep it short; most people read on a phone ([Mobile visitors](mobile-visitors.md)).

## Sending an edition

From [Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md):

1. Create a new edition in the list.
2. Write it, add photos, preview it.
3. Send a test to yourself and a colleague; check on a phone.
4. Send, or schedule the send.
5. A day later, look at bounces and unsubscribes on the newsletter dashboard.

## Checklist

- [ ] Host confirmed: mail sending, sender domain records, cron, bounce mailbox.
- [ ] One list to start with, with a clear promise on the sign-up form.
- [ ] Double opt-in tested with your own address.
- [ ] Unsubscribe tested.
- [ ] A monthly date in the calendar for the edition.

## Related pages

- [Newsletter planning](../../businesses/newsletters/newsletter-planning.md)
- [Newsletter routine](../../customers/growth/newsletter-routine.md)
- [Bounces and list hygiene](../../businesses/newsletters/bounces-and-list-hygiene.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
