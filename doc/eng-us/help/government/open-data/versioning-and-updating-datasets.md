---
title: Versioning and updating datasets
audience: government, editor, admin
category: public-sector
order: 140
summary: How to update, correct, supersede and withdraw datasets in Exponential without breaking addresses or losing the record: versions and their limit, periods as separate distributions, change notices, and withdrawal instead of removal.
keywords: versions, update dataset, correct data, supersede, withdraw, version history, DefaultVersionHistoryLimit, change notice, archive, open data, accountability
type: help
---
# Versioning and updating datasets

This page is for editors and administrators who keep published datasets current. Every save of a content object in
Exponential is a version, and the older versions stay in the history up to a limit
([Content history](../../../guides/content-history.md)). That is the basis for updating data in a way re-users can
follow.

## Contents

1. [Three kinds of change](#three-kinds-of-change)
2. [What the version history keeps](#what-the-version-history-keeps)
3. [Updating a dataset regularly](#updating-a-dataset-regularly)
4. [Correcting an error](#correcting-an-error)
5. [Superseding and withdrawing](#superseding-and-withdrawing)
6. [Keeping more versions](#keeping-more-versions)

## Three kinds of change

| Change | Example | Recommended approach |
|---|---|---|
| Regular update | new month of figures | replace the file of the "current" distribution, or add a distribution per period |
| Correction | wrong values in last month's file | replace the file, explain the correction on the dataset page |
| Retirement | the data is no longer collected | keep the page, mark it withdrawn, say why and where to go |

## What the version history keeps

- Each publish makes a new version; the previous published version becomes **archived**.
- Every version keeps its fields, including the file of a file field.
- The **Versions** page of an object (`/content/history/<object ID>`) lists them and can **Compare two versions**.
- The number of versions kept is limited: `content.ini [VersionManagement] DefaultVersionHistoryLimit=45` by
  default, and `VersionHistoryClass[]` per class. When the limit is reached, the oldest archived version is removed
  to make room.

The history is an internal record for staff with `content/versionread`; it is not shown to visitors. If re-users
need older files, publish them as their own distributions.

## Updating a dataset regularly

Two patterns, pick one per dataset:

**One current file.** One distribution "Current data", whose file you replace at each update. Re-users always fetch
the same page. Keep the file name the same so the download address stays the same.

**One file per period.** A new distribution per month or year ("2025", "2026"). Old files stay downloadable at their
own addresses; the dataset page lists all of them. This suits statistics that are cited by period.

In both cases update the dataset's "Last changed" date, and, if your catalog has one, its feed picks up a change
notice (see "Feeds for new and changed datasets").

## Correcting an error

1. Edit the distribution, replace the file, publish.
2. On the dataset page, add a dated line under "Corrections": what was wrong, which period, when it was fixed.
3. If the error was large, publish a news item and link it.

The old file stays in the version history, so you can show later exactly what was published before the correction.

## Superseding and withdrawing

Do not remove a dataset page: removal makes its address answer "not found" and nothing redirects
([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)). Instead:

1. Add a notice at the top of the description: "Withdrawn on <date>: <reason>. Replaced by <link>."
2. Change the update frequency to "no longer updated".
3. Optionally move it to an "Archived datasets" folder; the old address redirects to the new place.

If a dataset must really go (for example personal data was published by mistake), remove the file, then decide on
the page with your records officer. Removing a file from the current version does not remove it from older
versions: remove those versions too in the Versions page, or the file stays in the history.

## Keeping more versions

For dataset and distribution classes you may want a longer history than the default 45:

```ini
# settings/override/content.ini.append.php
[VersionManagement]
VersionHistoryClass[dataset]=200
VersionHistoryClass[distribution]=200
```

Clear the INI cache afterwards (`php bin/php/ezcache.php --clear-tag=ini`). Lowering a limit later does not remove
versions by itself; `php bin/php/cleanupversions.php` removes the archived versions beyond the limit. More
versions take more database and storage space; check with your administrator.

## Related pages

- [Content history](../../../guides/content-history.md)
- [Publishing, versions and the trash](../../publishing-and-versions.md)
- [Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
