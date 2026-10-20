---
title: Prepare a newsletter edition
audience: government, editor
category: public-sector
order: 540
summary: For editors - prepare a newsletter edition with cjw_newsletter - create the edition under the list, add articles (or reuse content from the article pool), send a test, check it, and schedule the send.
keywords: newsletter, edition, cjw_newsletter, Newsletter dashboard, article pool, test send, preview, schedule, send, list, skin, subscribers
type: help
---
# Prepare a newsletter edition

This page is for editors who put together a newsletter: a monthly digest of news, consultations and events. The
`cjw_newsletter` extension holds editions as content below a list; the **Newsletter** area of the administration
(`/newsletter/index`, the **Newsletter dashboard**) shows subscribers, sending, bounces and the editorial tools
([Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md),
[Sending newsletters](../../../creators/website-creators/sending-newsletters.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- The newsletter system and list exist, and you may create editions below the list.
- You know who approves editions, if your site uses approvals.
- The site sends real mail only after a rehearsal; ask the web team which transport is active.

## Steps

1. Open the list in the content tree (below the newsletter root folder), **Create new**, choose
   **CJW Newsletter Edition**; give it a title (the subject line).
2. Add articles below the edition (**CJW Newsletter Article**), or reuse existing content from the article pool
   (**Article pools** on the Newsletter dashboard).
3. Keep each article short, with a link to the full page on the site; give images alternative texts.
4. **Preview** the edition in the newsletter skin.
5. Send a test to yourself and a colleague (the test send on the edition); read it on a phone and with images off.
6. Go through the checklist below; then schedule it with **Send**. Cron queues and sends it.

## Check the result

- [ ] The test mail arrived, the subject and sender are right, every link works and points to the public site.
- [ ] The mail reads well without images.
- [ ] After sending, the Newsletter dashboard shows the send under **Last sends**; bounces appear under **Bounces**.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Sending to the wrong list | check the list before **Send** |
| Links to the administration or a staging site | test links signed out |
| An edition used for urgent alerts | newsletters are scheduled; urgent information goes on the site first ([Publish an urgent alert on the front page](publish-an-urgent-alert-on-the-front-page.md)) |
| Personal data in the edition | the newsletter goes to every subscriber; publish nothing you would not publish on the site |

A send can be stopped while it runs (`newsletter/send_abort`); ask the web team if something went wrong.

## Related pages

- [Publishing tasks](README.md)
- [Newsletters (news and alerts)](../../building/news-and-alerts/newsletters.md)
- [Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
