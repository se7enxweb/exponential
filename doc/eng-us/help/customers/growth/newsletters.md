---
title: Newsletters
audience: customer, businesses, editor
category: customer-success
order: 2
summary: The owner's view of starting or reviving a newsletter on an Exponential site with the cjw_newsletter extension that ships with it - what you get, the decisions before the first send, the roles on the team, consent, delivery and what to ask your partner.
keywords: newsletter, cjw_newsletter, email, mailing list, subscribers, double opt-in, editions, consent, bounces, mail delivery, site owner
type: help
---
# Newsletters

This page is for the owner or marketing lead who wants to send a newsletter from the site, or to put an existing one
on a better footing. Exponential installs the newsletter extension `cjw_newsletter` with the product (it is in the
`require` block of `composer.json`), so there is nothing extra to buy or license
([Newsletters for businesses](../../businesses/newsletters/README.md)). The weekly work is on
[Newsletter routine](newsletter-routine.md).

## Contents

1. [What you get](#what-you-get)
2. [Decide before the first send](#decide-before-the-first-send)
3. [Roles on the team](#roles-on-the-team)
4. [Consent and the law](#consent-and-the-law)
5. [Delivery](#delivery)
6. [What to ask your partner](#what-to-ask-your-partner)

## What you get

From [Newsletters for businesses](../../businesses/newsletters/README.md) and the
[cjw_newsletter feature page](../../../features/6.0/extensions/cjw_newsletter.md):

- Editions are written as content, with drafts and preview like any other page.
- Visitors subscribe to lists with a double opt-in.
- Editions are sent by cron in the background; every send is archived.
- Bounces, a blacklist and a suppression list keep lists clean.
- Release 4.2.0 added test groups, retries, rate limits, recurring sends, article pools, approval of editions,
  consent-based statistics and a CSV import with a column mapping.
- The newsletter is opened at **Newsletter** in the top menu of the administration (`/newsletter/index`), a
  dashboard with the lists, editions, last sends, the transport and a list of problems to fix.

## Decide before the first send

| Question | Why it matters | Read |
|---|---|---|
| What is the newsletter for, and for whom? | content, frequency and success all follow from it | [Planning a newsletter](../../businesses/newsletters/newsletter-planning.md) |
| One list or several (by topic, by language)? | subscribers choose what they get | [Lists and subscriptions](../../businesses/newsletters/lists-and-subscriptions.md) |
| How often? | a promise to readers; recurring sends can keep it | [Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md) |
| Must editions be approved? | approval is switched on per list | same |
| Which look (skin)? | 4.2.0 ships `default`, `company`, `news` and `shop` | same |
| Statistics, and with what consent? | tracking is consent-based | [Opt-in and consent](../../businesses/newsletters/opt-in-and-consent.md) |
| Moving an existing list? | consent records must come with it | [Lists and subscriptions](../../businesses/newsletters/lists-and-subscriptions.md) |

## Roles on the team

| Role | Does | Needs |
|---|---|---|
| Writer | creates the edition and its articles, previews, sends to a test group | edit rights below the list |
| Approver | approves or rejects editions of lists with approval | the policy `newsletter/approve` |
| Sender | schedules the send and watches it | access to the newsletter dashboard |
| List owner | answers subscribers, looks at bounces and the suppression list | access to subscribers |

Approvers are the users and groups named in the extension's approval settings, by default the Administrator users
group of a standard installation; nobody approves their own request unless self-approval is switched on
([Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md)).

## Consent and the law

Double opt-in, a consent log, an unsubscribe link in every mail and statistics only with consent are described on
[Opt-in and consent](../../businesses/newsletters/opt-in-and-consent.md). The site's other optional e-mail follows the
same idea: off until the person turns it on, with a one-click unsubscribe
([E-mail preferences](../../../features/6.0/mail-preferences.md)). The pages describe what the software records;
what your law requires is a question for your lawyer or data protection officer. This is not legal advice.

## Delivery

> **Warning**
> Test mails use the preview transport, which in the shipped `cjw_newsletter.ini` really sends. During a rehearsal
> have it set to file delivery, or test sends leave the server
> ([Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md),
> [Newsletter mail delivery](../../businesses/newsletters/newsletter-mail-delivery.md)).

Good delivery depends on a real sender address on your own domain, with the domain records your mail provider asks
for, and on rate limits that suit your mail server
([Newsletter mail delivery](../../businesses/newsletters/newsletter-mail-delivery.md)).

## What to ask your partner

- [ ] Are the newsletter classes, cron parts (`cjw_newsletter_mailqueue_create`, `cjw_newsletter_mailqueue_process`)
      and the transport set up and scheduled?
- [ ] Which transport is used for test sends and for real sends?
- [ ] Is the sender domain prepared for the mail provider?
- [ ] Who receives bounce reports?

The technical setup is on [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md)
and [Sending newsletters safely](../../creators/website-creators/sending-newsletters.md).

## Related pages

- [Newsletter routine](newsletter-routine.md)
- [Newsletter checklist](../../businesses/newsletters/newsletter-checklist.md)
- [Member communications](../../businesses/members/member-communications.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
