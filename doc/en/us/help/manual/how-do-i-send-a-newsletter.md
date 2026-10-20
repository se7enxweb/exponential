---
title: How do I send a newsletter?
audience: editor, admin
category: manual
order: 405
summary: In the newsletter's list, create an edition, fill and publish it, check the preview and a test mail, then click Send Newsletter and confirm; the mail queue cronjob sends it.
keywords: newsletter, edition, send newsletter, cjw_newsletter, preview, test mail, subscribers, mailing
type: help
---
# How do I send a newsletter?

A newsletter edition is content like an article. You write it, check it, and then send it to the subscribers of its
list.

## Who can do this

Users who may create content in the newsletter list and whose role has the newsletter policies (the extension
cjw_newsletter, in a new installation). Where editions need approval, someone with `newsletter/approve` releases them
first.

## Steps

1. Click **Newsletter** in the top menu (or **More** > **Newsletter**).
   The **Newsletter dashboard** opens.
2. Open the newsletter list and click **Create newsletter here**.
3. Fill in the edition: subject, texts, the articles it shows. Click **Send for publishing**.
4. Open the edition and check the **Newsletter edition preview** of each output format.
5. Send a test to your own address and read it in your mail program. Check the links and the unsubscribe link.
6. Click **Send Newsletter**, choose when, and confirm "Do you really want to send out this newsletter?".
   The **Edition State** changes while the queue is sent.

## What can go wrong

- **Attention: a sent newsletter cannot be called back.** Check the test mail first. To stop a send that is still
  running, use **Abort newsletter send out process**.
- **The test mail does not arrive or lands in spam.** The sender domain is not set up at the mail provider. Ask an
  administrator.
- **Nothing is sent.** The mail queue cronjob does not run. The dashboard shows waiting mail.

## Related pages

- [Sending newsletters safely](../creators/website-creators/sending-newsletters.md)
- [Newsletters](../customers/growth/newsletters.md)
- [Newsletter routine](../customers/growth/newsletter-routine.md)
- [cjw_newsletter](../../features/6.0/extensions/cjw_newsletter.md)
- [User manual](README.md)
