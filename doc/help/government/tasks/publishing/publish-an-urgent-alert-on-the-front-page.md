---
title: Publish an urgent alert on the front page
audience: government, editor
category: public-sector
order: 50
summary: For on-call editors - put an urgent notice in front of visitors fast, using the notices folder your front page shows, check it through every cache, update it, and take it down; Exponential has no built-in alert banner.
keywords: urgent alert, front page, notice, banner, emergency, closure, disruption, cache, publish fast, hide, on-call
type: help
---
# Publish an urgent alert on the front page

This page is for editors on call who must tell visitors about a disruption, a closure or a hazard now. Exponential has
no built-in alert banner; sites show alerts with a notices folder that the front page (or every page) lists, or with a
block in the layouts editor. How your site does it was decided when it was built
([The urgent notices pattern](../../building/news-and-alerts/urgent-notices-pattern.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Update and end the alert](#update-and-end-the-alert)
5. [Pitfalls](#pitfalls)

## Before you start

- You know where alerts go on your site: a Notices (or Alerts) folder, or a layouts block.
- You can sign in, including the second step (keep your authenticator app or e-mail at hand).
- Your role may publish there without approval (on-call roles usually may).
- You have the approved wording, or a prepared template text.

## Steps

**With a notices folder:**

1. Open the Notices folder, **Create new**, choose the notice class.
2. Headline: what and where, in a few words. Text: what people should do, first.
3. Severity: the right level. **Hide after**: the end time, if known.
4. **Send for publishing**.

**With a layouts block** ([The layouts editor](../../../layouts-editor.md)):

1. **Layouts**, open the layout that holds the alert zone (often a shared header layout).
2. Select the alert block, type the text in its properties, **Publish layout**.

## Check the result

- [ ] Open the front page signed out, in a private window: the alert is there.
- [ ] Open another page if alerts show site-wide.
- [ ] If the alert does not appear within the time your procedure says, ask the web team to refresh the caches
  (static cache, Velocity's response cache, a CDN), as rehearsed ([Refresh a page that shows old content](refresh-a-page-that-shows-old-content.md)).
- [ ] Tell the second person on call to read it.

## Update and end the alert

| Need | Do |
|---|---|
| New information | **Edit**, change the text, add "Updated <time>: <change>" at the top, publish |
| End now | **Hide / unhide** on the notice (or empty the layouts block and publish the layout) |
| End at a known time | set **Hide after**; it is hidden at the next cronjob run after it |

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Waiting for an approval at night | on-call roles should be outside the approval workflow; check in a rehearsal |
| The alert only in an image | text first; images may not load and cannot be read by everyone |
| Forgetting to end it | set an end time or a reminder; a stale alert costs trust |
| Assuming everyone saw it | the website is one channel; use the others in your procedure |

## Related pages

- [Publishing tasks](README.md)
- [Publish a public notice with an expiry date](publish-a-public-notice-with-an-expiry-date.md)
- [Alerts, availability, maintenance mode and static cache](../../levels/emergency-services/alerts-availability-maintenance-and-static-cache.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
