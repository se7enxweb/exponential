---
title: Publish a public notice with an expiry date
audience: government, editor, admin
category: public-sector
order: 40
summary: For editors and the administrators who set it up - publish a public notice that disappears by itself after its end date, using a hide date attribute and the hide.php cronjob, and know the alternatives when automatic hiding is not configured.
keywords: public notice, statutory notice, expiry date, hide date, hide.php, HideSettings, unpublish.php, cronjob, automatic hiding, notice period
type: help
---
# Publish a public notice with an expiry date

This page is for editors who publish notices that are valid for a fixed period (a planning application, a road closure,
a statutory notice), and for the administrator who sets up automatic hiding once. Exponential hides content after a
date with the `hide.php` cronjob; it needs a date attribute on the class and one setting
([Cronjobs](../../../../guides/cronjobs.md), [Archive and expiry](../../building/news-and-alerts/archive-and-expiry.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps for the editor](#steps-for-the-editor)
3. [One-time setup for the administrator](#one-time-setup-for-the-administrator)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- Your notice class has a Date and time attribute for the end, for example **Hide after** (`hide_date`), as in
  [Content model for municipal and local government](../../levels/municipal-and-local/content-model.md#class-to-add-public-notice).
- Automatic hiding is set up for the Notices folder (ask the web team; setup below).
- You know the notice period required by your rules.

## Steps for the editor

1. Open the Notices folder, **Create new**, choose the notice class.
2. Fill title, type, text and documents.
3. Set **Hide after** to the end of the notice period (date and time).
4. **Preview**, then **Send for publishing**.

## One-time setup for the administrator

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the Notices folder>
HideDateAttributeList[public_notice]=hide_date
```

Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`). `hide.php` is in the global cronjob part
(`[CronjobSettings] Scripts[]`); check that the crontab runs it (**Setup > Cronjobs**). Test once:
`php runcronjobs.php -s <siteaccess> --script=hide.php`.

## Check the result

- [ ] The notice is visible to signed-out visitors now.
- [ ] After the end time and the next cronjob run, it is hidden: visitors no longer see it; in the administration it is
  shown as hidden and can be unhidden.
- [ ] The notice still exists for the record.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| No end date filled | the notice stays forever; make the attribute required if every notice ends |
| The cronjob does not run | nothing is hidden; check the cronjobs page after server changes |
| Expecting the notice to vanish at the exact minute | it is hidden at the first run after the time |
| Using `unpublish.php` instead | it moves objects with an `unpublish_date` attribute to the trash; hiding keeps the record |
| Without automatic hiding | hide it by hand on the end date ([Hide a page temporarily](hide-a-page-temporarily.md)); put a reminder in your calendar |

Object states and sections do not end content by date on their own; only the cronjobs act on dates.

## Related pages

- [Publishing tasks](README.md)
- [Publish an urgent alert on the front page](publish-an-urgent-alert-on-the-front-page.md)
- [Archive and expiry](../../building/news-and-alerts/archive-and-expiry.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
