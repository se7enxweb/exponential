---
title: Publishing decisions with care for personal data
audience: government, project-manager, admin, editor
category: public-sector
order: 70
summary: A careful publishing flow for court decisions in Exponential - anonymize before upload, review in a closed section, publish, correct without leaving personal data in old versions, withdraw quickly, and know which copies remain in caches, search indexes, the trash and backups.
keywords: court decisions, judgments, anonymization, pseudonymization, personal data, review, withdraw, correction, versions, version removal, trash, search index, cache, backups
type: help
---
# Publishing decisions with care for personal data

This page is for the people who publish court or tribunal decisions and for the administrators who support them. It
describes a flow built from Exponential features (sections, the *Approve* workflow event, object states, versions,
hiding and the trash) and says plainly what the system does not do. Read it with the court's publication policy.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The flow at a glance](#the-flow-at-a-glance)
2. [Before upload: anonymize](#before-upload-anonymize)
3. [Prepare and review](#prepare-and-review)
4. [Publish](#publish)
5. [Correct a published decision](#correct-a-published-decision)
6. [Withdraw a decision](#withdraw-a-decision)
7. [Where copies remain](#where-copies-remain)

## The flow at a glance

| Step | Who | Where |
|---|---|---|
| Anonymize the document | decision team | outside Exponential |
| Create the Decision object, upload the file | decision preparer | the review folder, section `decisions_review` |
| Review the text, title and summary | reviewer | **My account > Collaboration**, the preview |
| Publish and move to the public folder | publisher | section `decisions` |
| Correct or withdraw | publisher lead | the object, its versions |

The roles and sections are set up as in [Structure, sections and roles for courts](structure-sections-and-roles.md).

## Before upload: anonymize

Exponential does not find or remove personal data in documents. Anonymize (or pseudonymize, as your rules require)
before the file reaches the system:

- names of parties, witnesses and third parties, as the rules say;
- addresses, dates of birth, identification numbers, account numbers;
- indirect identifiers: places, employers, rare circumstances;
- document metadata: the author and title fields of the file properties, tracked changes and comments;
- the file name: `decision-<case number>.pdf`, never a party's name.

Do the same for what you type into the object: the title, the summary and the tags are as public as the document.

## Prepare and review

1. In the review folder, **Create new** > **Decision**. Fill case number, chamber, date, area of law, summary, and
   upload the anonymized file in the **Decision text** field.
2. **Send for publishing**. The *Approve* event holds the version; it is now pending
   ([Drafts and pending items](../../../../guides/drafts-and-pending.md)).
3. The reviewer opens the item in **My account > Collaboration**, opens the preview and the file, checks every field
   against the policy, and presses **Approve**, or **Deny** with a comment
   ([Workflows](../../../../guides/workflows.md)).
4. After the workflow cronjob runs, the version is published, still in the closed section.

## Publish

1. The publisher moves the object to the public Decisions folder (**Move**); check on the **Details** tab that its
   section is now `decisions` ([Sections, 8.2](../../../../guides/sections.md#82-moving-and-locations)).
2. On the **Details** tab, **Content state**, set *Published* and press **Set states**.
3. Open the decision logged out and check the page and the download.

## Correct a published decision

| Correction | Steps |
|---|---|
| A typo in the summary | **Edit**, correct, add "Corrected on <date>" to the summary, publish; set the state *Corrected* |
| Personal data found in the document | replace the file at once with a corrected file (**Edit**, upload, publish); then remove the earlier versions that hold the old file |

Removing the earlier versions: open **Manage versions**, filter by status *Archived*, tick the versions that contain
the old file or text, **Remove selected versions**, check the list, **Remove for good**. The published version can
never be removed this way, which is why you publish the corrected version first
([Versions of an object](../../../../guides/content-history.md)). Removal needs `content/versionremove`.

## Withdraw a decision

| Need | Action | Effect |
|---|---|---|
| Stop showing it now, keep it for the record | **Hide / unhide** in the item's menu (or **Hide** on its location) | visitors no longer see it; the object and versions stay |
| Take it off the site entirely | **Remove** (moves to the trash) | visitors cannot reach it; it can be restored from **Trash** |
| Delete it | remove from the trash (**Empty trash**, or the trash purge) | final; the files go with it |

Set the state *Withdrawn* before hiding if you keep the object, so the record says why. Every step is recorded in the
audit trail ([Audit trail](../../../../features/6.0/audit-trail.md)).

## Where copies remain

| Place | What to do |
|---|---|
| Archived versions | remove them as above |
| The trash | an object in the trash still holds its files; empty it when the decision must be deleted |
| Search index | hiding and removing update the index; with eZ Find, check the decision is gone from search |
| Caches | publishing clears the object's view cache; if you use the static cache, Velocity's response cache (`./console exp:velocity cache clear --apply`) or a CDN, clear those too ([Caches](../../../../guides/caches.md), [Velocity response cache](../../../../features/6.0/velocity-response-cache.md)) |
| Backups | the decision remains in backups until they expire; your retention rules decide |
| Copies elsewhere | search engines, archives and people who downloaded it; request removal where the law provides for it |

Agree in advance who informs the data protection officer when personal data was published by mistake.

## Related pages

- [Courts](README.md)
- [Requirements emphasis for courts](requirements-emphasis.md)
- [Publishing, versions and the trash](../../../publishing-and-versions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
