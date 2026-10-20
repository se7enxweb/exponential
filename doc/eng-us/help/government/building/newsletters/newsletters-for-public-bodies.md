---
title: Newsletters for public bodies
audience: government, project-manager, admin, editor
category: public-sector
order: 20
summary: What the cjw_newsletter extension gives a public body for e-mail newsletters and alerts (lists, double opt-in, editions as content, approval, languages, bounces, statistics only with consent, SMS), what it needs to run, and where its consent and unsubscribe features come from.
keywords: newsletter, cjw_newsletter, alerts, e-mail updates, public information, double opt-in, consent, mail preferences, editions, lists, bounces
type: help
---
# Newsletters for public bodies

This page is for managers and administrators who plan e-mail newsletters or alert lists: council news, consultation
alerts, service updates. Exponential's newsletter system is the `cjw_newsletter` extension, opened at **Newsletter**
in the administration (`/newsletter/index`) ([Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md),
[cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md)).

## Contents

1. [What it provides](#what-it-provides)
2. [What it needs](#what-it-needs)
3. [Newsletters or notifications](#newsletters-or-notifications)
4. [Decisions before you start](#decisions-before-you-start)

## What it provides

| Area | What |
|---|---|
| Lists | newsletter systems group lists; visitors subscribe to the lists they want |
| Consent | double opt-in; subscriptions only count after confirmation |
| Editions | written as content with drafts and preview; one edition in several languages |
| Approval | editions can be approved in the collaboration inbox |
| Sending | in the background by cron, with rate limits and batches, test groups |
| Clean lists | bounces, a blacklist that wins over every list, a suppression list |
| Statistics | anonymous totals; opens and clicks per person only with the recipient's consent to "Newsletter statistics" |
| Unsubscribe | every edition carries an unsubscribe link; one-click unsubscribe through the mail gate |
| SMS | newsletters by SMS through a provider-neutral transport, off until switched on |

## What it needs

- The extension active and its class packages installed.
- The cron parts `cjw_newsletter` (every ten minutes in the documented crontab) and `cjw_newsletter_mailbox` (bounces).
- A mail server and a sender domain with SPF and DKIM at your mail provider.
- The kernel's e-mail preferences set up (sender details, footer), which the newsletter uses since 4.2.0
  ([Mail preferences for administrators](../../../../guides/mail-preferences-administrator.md)).

## Newsletters or notifications

| Need | Tool |
|---|---|
| An edited newsletter or alert sent to a list | `cjw_newsletter` |
| "Tell me when this page or section changes" for signed-in users | the kernel's notifications (see "Notifications instead of a newsletter") |

## Decisions before you start

- Which lists (by topic, by area), and who writes and approves each.
- The sender name and address (a function mailbox of the body).
- How often each list is sent, said on the subscribe form.
- Whether per-person statistics are used at all; anonymous totals are often enough for a public body.

## Related pages

- [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md)
- [Privacy standards](../../../../standards/privacy.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
