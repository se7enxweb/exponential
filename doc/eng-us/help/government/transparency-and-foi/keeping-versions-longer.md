---
title: Keeping versions longer
audience: government, admin
category: public-sector
order: 160
summary: For administrators: how many versions Exponential keeps (content.ini VersionManagement), how to raise the limit for record-relevant classes, what cleanupversions.php does, and who should be allowed to remove versions.
keywords: version limit, DefaultVersionHistoryLimit, VersionHistoryClass, cleanupversions.php, versions, retention, archived versions, content.ini, versionremove, records
type: help
---
# Keeping versions longer

This page is for administrators who decide how much version history a site keeps. For transparency content
(decisions, policies, published responses) a longer history makes it easier to show what was published when.

## Contents

1. [The shipped limits](#the-shipped-limits)
2. [Raising the limit for some classes](#raising-the-limit-for-some-classes)
3. [Lowering a limit](#lowering-a-limit)
4. [Who may remove versions](#who-may-remove-versions)
5. [Drafts](#drafts)
6. [Costs](#costs)

## The shipped limits

`settings/content.ini`, block `[VersionManagement]`:

| Setting | Shipped | Meaning |
|---|---|---|
| `DefaultVersionHistoryLimit` | `45` | the most versions kept per object |
| `VersionHistoryClass[1]` | `75` | class 1 (the folder class) keeps 75 |
| `VersionHistoryClass[<class identifier>]` | not set | a limit per class, by ID or identifier |
| `DeleteDrafts` | `disabled` | |

When an object reaches its limit, the oldest **archived** version is removed to make room for a new one
([Content history](../../../guides/content-history.md)).

## Raising the limit for some classes

```ini
# settings/override/content.ini.append.php
[VersionManagement]
VersionHistoryClass[decision]=500
VersionHistoryClass[disclosure_entry]=200
VersionHistoryClass[meeting]=200
```

Then clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`) and, when Velocity serves the site,
`./console exp:velocity cache clear`. The new limit applies from the next version made.

## Lowering a limit

Lowering a value does not remove versions at once. `php bin/php/cleanupversions.php` removes the archived versions
that exceed the limits:

```bash
php bin/php/cleanupversions.php --help
```

Run it only after checking your records schedule, and after a database backup: removed versions are gone.

## Who may remove versions

`content/versionremove` lets a user remove draft, archived and rejected versions on the versions page; the published
version and versions in a running workflow are never removed there. For record-relevant sections, give it only to
administrators, and note that removals are recorded in the audit trail's `content` channel
([Audit trail](../../../features/6.0/audit-trail.md)).

## Drafts

Old drafts are not records of what was published. Editors can remove their own old drafts with **Remove old drafts**
([Drafts and pending items](../../../guides/drafts-and-pending.md)); `content.ini [VersionManagement] DraftsDuration`
and the `old_drafts_cleanup` cronjob handle them automatically when configured.

## Costs

Each version stores all fields, files included. A class with large files and a high limit grows the database and the
storage directory. Check disk usage after a few months ([Operating a site](../../../guides/operating-a-site.md)).

## Related pages

- [Version history as accountability](version-history-as-accountability.md)
- [Content history](../../../guides/content-history.md)
- [Settings page](../../../guides/settings-page.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
