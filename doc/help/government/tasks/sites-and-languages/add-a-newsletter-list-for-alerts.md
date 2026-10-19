---
title: Add a newsletter list for alerts
audience: government, admin, editor
category: public-sector
order: 340
summary: Task for administrators: add an alert list (for example consultations) to cjw_newsletter, link its subscribe form from the topic's pages, and send a first alert after a rehearsal.
keywords: newsletter list, alerts, cjw_newsletter, subscribe form, newsletter system, double opt-in, edition
type: help
---
# Add a newsletter list for alerts

This task is for administrators and editors. Background:
[Alert lists for public notices](../../building/newsletters/alert-lists-for-public-notices.md).

## Before you start

- `cjw_newsletter` active, its classes installed, `[NewsletterSettings] RootFolderNodeId` set, cron parts running.
- A newsletter system (for example "Public alerts") exists below the newsletter folder.

## Steps

1. In **Content structure**, open the newsletter system; **Create new**, the newsletter list class; name it
   "Consultations"; publish.
2. On the consultation pages, link the subscribe form (`newsletter/subscribe`), with a sentence on what subscribers get
   and how often.
3. Rehearse with the file transport (see "Rehearse a newsletter with the file transport").
4. Write the first alert: under the list, **Create new**, an edition; preview; send a test; **Send**.

## Check

**Newsletter** dashboard (`/newsletter/index`) lists the list under **Lists**, its subscribers under **Subscribers**,
and the send under **Last sends**.

## Problems

| Problem | Fix |
|---|---|
| Subscribers receive nothing | they are still *pending*: the confirmation was not clicked |
| Nothing is sent | the `cjw_newsletter` cron part does not run for the siteaccess, or the transport is `file` |

## Related pages

- [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
