---
title: Planning a newsletter
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 10
summary: The decisions a business or organization takes before setting up a newsletter in Exponential - purpose, audience and lists, frequency, who writes and who approves, consent, sending infrastructure and how success is measured.
keywords: newsletter planning, email marketing plan, audience, lists, frequency, editorial calendar, approval, consent, sender domain, newsletter goals
type: help
---
# Planning a newsletter

This page is for owners, managers and marketing leads who want a newsletter and need to decide what it should be
before anybody configures software. It turns the features of the `cjw_newsletter` extension into a short list of
business decisions. Each decision names the feature that carries it out and links to the page that explains it.

## Contents

1. [Why plan first](#why-plan-first)
2. [Decision 1: what the newsletter is for](#decision-1-what-the-newsletter-is-for)
3. [Decision 2: audiences and lists](#decision-2-audiences-and-lists)
4. [Decision 3: frequency and rhythm](#decision-3-frequency-and-rhythm)
5. [Decision 4: who writes, who approves, who sends](#decision-4-who-writes-who-approves-who-sends)
6. [Decision 5: consent and data](#decision-5-consent-and-data)
7. [Decision 6: how mail leaves the server](#decision-6-how-mail-leaves-the-server)
8. [Decision 7: what you will measure](#decision-7-what-you-will-measure)
9. [A one-page plan](#a-one-page-plan)

## Why plan first

A newsletter goes to many people at once and cannot be called back
([Sending newsletters safely](../../creators/website-creators/sending-newsletters.md)). A list built without clear
consent is hard to repair later, and a sender domain that lands in spam folders takes time to recover. A plan of one
page, agreed before setup, avoids most of these problems.

## Decision 1: what the newsletter is for

Write one sentence that says what a subscriber gets and why it is worth their inbox. Typical purposes:

| Purpose | Example of a promise to subscribers |
|---|---|
| Store news | New products and offers, once a month |
| Library news | New acquisitions and events, every two weeks |
| Supporter updates | What the organization achieved with its supporters' help, each quarter |
| Member information | Meeting dates and decisions for members only |

The sentence goes on the subscribe form. The website creator's checklist asks for exactly that: "The subscribe form
says what subscribers get and how often"
([Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).

## Decision 2: audiences and lists

In `cjw_newsletter` a **newsletter system** groups **lists**, and visitors subscribe to lists
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#the-parts)). Decide:

- [ ] How many lists you need. Start with as few as possible; one list per promise is a good rule.
- [ ] Whether a list is public (anyone may subscribe) or filled by an administrator.
- [ ] Whether subscribers choose interests within a list. Release 4.2.0 supports interests and a block "articles for
      your interests" (`doc/rendering.md` of the extension, see
      [rendering.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/rendering.md)).
- [ ] Which languages each list is written in. One edition can be sent in several languages (same guide).

More in [Lists and subscriptions](lists-and-subscriptions.md).

## Decision 3: frequency and rhythm

Pick a rhythm you can keep. The extension can send on a schedule: release 4.2.0 has **recurring sends** on chosen
weekdays, weekly or monthly (Newsletter > Recurring sends, `newsletter/schedule_list`), and **article pools** from
which the articles of an edition are picked
([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)). A schedule helps only if
there is content to send; agree who fills each edition and by when.

## Decision 4: who writes, who approves, who sends

| Role | Typical person | What the software offers |
|---|---|---|
| Writer | Editor, marketing staff | Editions are content with drafts and preview |
| Approver | Manager, head of communications | Optional approval per list; a version that is not approved is not sent |
| Sender | Editor or administrator | **Send** schedules the edition; cron does the work |
| Administrator | IT, hosting partner | Transports, cron, bounce mailbox, settings |

Approval is switched on per list ("Approval: yes"). Approvers by default are the members of user group 12, the
Administrator users of a standard installation, and nobody approves their own request unless that is allowed in the
settings ([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)). Details in
[Writing and sending editions](writing-and-sending-editions.md).

## Decision 5: consent and data

Decide where subscribers come from and how you will prove they agreed:

- public subscribe forms use a double opt-in ([Opt-in and consent](opt-in-and-consent.md));
- an imported list needs a written **consent source**, which the import records in the consent log
  ([importexport.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/importexport.md));
- per-person open and click statistics are only counted for people who switched on the preference
  "Newsletter statistics", which is off by default
  ([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)).

> **Note**
> This page describes what the software does. It is not legal advice. Ask your lawyer or data protection officer
> which rules apply to your newsletter.

## Decision 6: how mail leaves the server

Three transports exist per kind of mail: `file` (write to disk, nothing is sent), `smtp` and `sendmail`
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).
Decide with your hosting partner:

- [ ] Which mail server or provider sends the newsletter.
- [ ] Which sender address and domain, and who sets up SPF and DKIM for it
      ([Sending newsletters safely](../../creators/website-creators/sending-newsletters.md#from-rehearsal-to-real-mail)).
- [ ] Whether the provider limits how many mails per minute or hour may be sent; the extension can keep to such
      limits with its rate limits (`[ThrottleSettings]`, disabled by default in the shipped `cjw_newsletter.ini`).

More in [Newsletter mail delivery](newsletter-mail-delivery.md).

## Decision 7: what you will measure

The mail queue and the list always give you the numbers sent, delivered, bounced and unsubscribed, even with tracking
off. Opens and clicks need tracking, which is switched off in the shipped settings (`[TrackingSettings] Tracking`)
and must be switched on deliberately
([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)). Choose two or three
numbers you will actually look at each month, for example subscribers gained and lost, and the bounce count.

## A one-page plan

```text
Newsletter name:            ...
Promise to subscribers:     ... (what, how often)
Lists:                      ...
Languages:                  ...
Writer(s):                  ...
Approver:                   ... (approval on: yes / no)
Sender address and domain:  ...
Mail provider:              ... (SPF / DKIM done by: ...)
Source of subscribers:      form with double opt-in / import (consent source: ...)
Tracking:                   off / anonymous totals / per person with consent
Numbers we review monthly:  ...
First send date:            ... (after a full rehearsal with the file transport)
```

## Related pages

- [Newsletters for businesses](README.md)
- [cjw_newsletter for businesses](cjw-newsletter-for-businesses.md)
- [Newsletter checklist](newsletter-checklist.md)
- [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md)
- [Planning an Exponential project](../../leadership/planning-an-exponential-project.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
