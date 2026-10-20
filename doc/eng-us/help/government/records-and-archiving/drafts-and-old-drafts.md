---
title: Drafts and old drafts
audience: government, editor, admin
category: public-sector
order: 50
summary: For editors and administrators - what drafts are in Exponential, how editors find and remove their own old drafts, the cleanup settings and cronjob scripts that exist for old drafts, and why drafts are rarely records.
keywords: drafts, old drafts, My drafts, content/draft, remove old drafts, DraftsDuration, old_drafts_cleanup.php, internal drafts, internal_drafts_cleanup.php, pending items, records, public sector
type: help
---
# Drafts and old drafts

This page is for editors and administrators. Drafts are versions that were never published. They accumulate, they can
contain unfinished or sensitive text, and they are usually not records. Exponential lets editors tidy their own drafts
and has cleanup scripts for administrators ([Drafts and pending items](../../../guides/drafts-and-pending.md)).

## Contents

1. [Editors: My drafts](#editors-my-drafts)
2. [Administrators: cleanup settings](#administrators-cleanup-settings)
3. [Records view](#records-view)
4. [Related pages](#related-pages)

## Editors: My drafts

**My drafts** (`/content/draft`) shows your own drafts only:

- figures (yours, of new objects, not modified for 30 days or the age chosen);
- filters by translation, class and age (7, 30, 90, 365 days);
- **Remove old drafts** after 7, 30, 90 or 365 days without a change, **Remove selected**, **Remove all**, each confirmed
  in place.

A draft of a new object (never published) takes the object with it when removed; a draft of a published object leaves
the published version as it is. Every draft is checked again when removed: you can only remove your own.

## Administrators: cleanup settings

`settings/content.ini [VersionManagement]` holds the durations the cleanup scripts use:

| Setting | Shipped | Used by |
|---|---|---|
| `DraftsDuration[days]` | 90 | `cronjobs/old_drafts_cleanup.php`, "Remove user drafts that exceed the configured age limit" |
| `DraftsCleanUpLimit` | 100 | the same, per run |
| `InternalDraftsDuration[days]` | 1 | `cronjobs/internal_drafts_cleanup.php` (internal drafts) |
| `InternalDraftsCleanUpLimit` | 100 | the same, per run |

`internal_drafts_cleanup.php` is in the default cronjob part; `old_drafts_cleanup.php` is in no part of
`settings/cronjob.ini` as shipped, so old user drafts are removed only if you add it to a part
(in `settings/override/cronjob.ini.append.php`). Decide that with your editors: a draft unchanged for 90 days may still
be wanted.

## Records view

- Drafts are work in progress; the published and archived versions are the candidates for records.
- A draft may still hold personal data (a draft reply to a citizen); removing old drafts supports data minimization.
- Pending versions (in an approval workflow) are not drafts; they are listed on **My pending items** and cannot be
  removed while the workflow runs.

## Related pages

- [Drafts and pending items](../../../guides/drafts-and-pending.md)
- [Versions and content history](versions-and-content-history.md)
- [Cronjobs](../../../guides/cronjobs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
