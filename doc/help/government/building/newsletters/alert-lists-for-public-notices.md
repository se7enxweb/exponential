---
title: Alert lists for public notices
audience: government, admin, editor
category: public-sector
order: 30
summary: How to set up cjw_newsletter lists for public alerts (consultations, road works, planning notices, service changes): the structure, one list per topic or area, a short alert edition, translation, and how fast alerts go out.
keywords: alerts, public notices, alert list, consultations, road works, planning notices, topics, areas, cjw_newsletter, lists, editions
type: help
---
# Alert lists for public notices

This page is for administrators and editors who send short alerts by e-mail when something is published: a new
consultation, road works in a district, a planning notice. With `cjw_newsletter`, each alert topic is a list people
subscribe to ([Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md)).

## Contents

1. [Structure](#structure)
2. [Setting up a list](#setting-up-a-list)
3. [Writing an alert](#writing-an-alert)
4. [How fast alerts go out](#how-fast-alerts-go-out)
5. [Languages](#languages)

## Structure

```
Newsletters (folder, RootFolderNodeId)
  Public alerts (newsletter system)
    Consultations (list)
    Road works, North district (list)
    Planning notices (list)
```

One list per topic or area lets people choose exactly what they want; too many lists confuse. The extension's
interests feature is another way to tailor content within one list (see the extension's rendering guide linked from
[cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md)).

## Setting up a list

1. Make sure the newsletter folder exists and `[NewsletterSettings] RootFolderNodeId` points to it in
   `settings/override/cjw_newsletter.ini.append.php`.
2. Below it, create a newsletter system "Public alerts", then a list per topic.
3. Link the subscribe form (`newsletter/subscribe`) from the pages of each topic: the consultation section links the
   "Consultations" list.
4. Rehearse with the file transport (see "Rehearsing with the file transport").

## Writing an alert

Keep alerts short: what, where, when, and one link to the page with the details. Create an edition under the list,
preview it, send a test to the test group, then **Send**. With approval switched on, the edition waits in the
collaboration inbox until approved.

## How fast alerts go out

Sends run by cron: the documented crontab runs the `cjw_newsletter` part every ten minutes, and rate limits spread
large lists over time. An alert is therefore not instant; for emergencies use the channels your body has for that,
and the website's front page.

## Languages

One edition can be sent in several languages; subscribers get their language (see the extension's rendering guide).
Offer the subscribe form on each language site.

## Related pages

- [Newsletters for public bodies](newsletters-for-public-bodies.md)
- [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
