---
title: Archive and expiry
audience: government, admin, website-creator, project-manager
category: public-sector
order: 80
summary: What happens to old news and notices on an Exponential site - automatic hiding with hide.php, moving to the trash with unpublish.php, archives by year, labels with object states, and what to keep for the record.
keywords: archive, expiry, hide.php, unpublish.php, HideSettings, UnpublishSettings, unpublish_date, hide date, trash, object states, news archive, records
type: help
---
# Archive and expiry

This page is for administrators, website creators and project managers who decide what happens to news and notices
when they are old. Exponential has two cronjob scripts that act on dates, plus hiding, states and the trash.
Sources: [Cronjobs](../../../../guides/cronjobs.md), [Object states](../../../../guides/object-states.md) and the
cronjob code in `kernel/private/classes/cronjobs/hide.php` and `unpublish.php`.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The two scripts](#the-two-scripts)
2. [Setting up hide.php](#setting-up-hidephp)
3. [Setting up unpublish.php](#setting-up-unpublishphp)
4. [Archives by year](#archives-by-year)
5. [What to keep](#what-to-keep)

## The two scripts

| Script | What it does | Settings | Reversible |
|---|---|---|---|
| `hide.php` | hides nodes whose date attribute has passed (as **Hide** does), and tells the search engine | `content.ini [HideSettings] RootNodeList[]`, `HideDateAttributeList[<class>]=<attribute>` | yes: unhide |
| `unpublish.php` | moves objects of the listed classes whose `unpublish_date` attribute has passed to the trash (recorded in the audit as a removal to the trash) | `content.ini [UnpublishSettings] RootNodeList[]`, `ClassList[]`; the attribute must be called `unpublish_date` | yes, from the trash, until the trash is emptied |

Both are in the global cronjob part (`[CronjobSettings] Scripts[]`); they do nothing until their settings name root
nodes. Run one by hand to test: `php runcronjobs.php -s <siteaccess> --script=hide.php`.

## Setting up hide.php

1. Give the class a Date and time attribute, for example `hide_date`.
2. In `settings/override/content.ini.append.php`:

   ```ini
   [HideSettings]
   RootNodeList[]=<node id of the Notices folder>
   HideDateAttributeList[notice]=hide_date
   ```

3. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini`.
4. Create a test notice with a past hide date; run the script; the notice is hidden.

An empty date is ignored (the filter requires a value greater than 0).

## Setting up unpublish.php

```ini
[UnpublishSettings]
RootNodeList[]=<node id of the folder>
ClassList[]=<class identifier>
```

The class needs an attribute with the identifier `unpublish_date`. Prefer `hide.php` for public-sector content:
hidden content stays where it was, with its versions, and is easier to prove later; trashed content disappears for
good when the trash is emptied (**Empty trash** or `./console exp:trashpurge --apply`).

## Archives by year

| Pattern | Use |
|---|---|
| News stays visible in year folders | news is part of the public record; old items remain findable |
| A banner "This is an archived item" for items older than a period | a template condition on the publication date |
| An object state "Archived" | a state group with Current and Archived; templates show it; lists of current news filter it out ([Object states](../../../../guides/object-states.md)) |
| Notices hidden after their end | they are no longer valid information |

## What to keep

| Content | Usual practice |
|---|---|
| News and press releases | keep published; they are cited |
| Notices and alerts | hide after the end; keep for the record |
| Job vacancies, tenders | hide after the deadline; keep as your records rules say |
| Event pages | keep or hide by year |

The website is rarely the official archive. Transfer records to your records system as your schedule says; the
[records and archiving](../../records-and-archiving/README.md) guides of this Governments help go deeper.

## Related pages

- [News and alerts](README.md)
- [The urgent notices pattern](urgent-notices-pattern.md)
- [Publishing, versions and the trash](../../../publishing-and-versions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
