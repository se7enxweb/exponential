---
title: Versions, drafts and publishing
audience: cms-developer
category: cms-development
order: 17
summary: For CMS developers, the life of a version. It covers drafts, publishing and archived versions, what the publish operation does for you, version history limits and cleanup, autosave, preview, scheduled hiding and removal, and asynchronous publishing.
keywords: versions, drafts, publish, publishing operation, version status, archived, pending, internal draft, version history limit, cleanupversions, autosave, ezautosave, preview, unpublish, hide date, asynchronous publishing, workflows
type: help
---
# Versions, drafts and publishing

This page is for developers who need to know what happens between an editor pressing **Edit** and the page
changing on the site, because their templates, workflows, imports and caches depend on it.

## Contents

1. [A version is never edited after publishing](#a-version-is-never-edited-after-publishing)
2. [Version statuses](#version-statuses)
3. [What publishing does](#what-publishing-does)
4. [How many versions are kept](#how-many-versions-are-kept)
5. [Drafts, autosave and abandoned drafts](#drafts-autosave-and-abandoned-drafts)
6. [Preview](#preview)
7. [Publishing and hiding on a date](#publishing-and-hiding-on-a-date)
8. [Asynchronous publishing](#asynchronous-publishing)
9. [For your code](#for-your-code)

## A version is never edited after publishing

Each **Edit** creates a new draft version with a copy of every translation. Editors change the draft; the published
version stays on the site until the draft is published. Publishing makes the draft the current version and archives
the previous one. Editors find their open drafts under **My drafts** (`/content/draft`) and compare or restore old
versions on an object's version history ([Versions of an object](../../guides/content-history.md)).

## Version statuses

The statuses are constants of `eZContentObjectVersion` (`kernel/classes/ezcontentobjectversion.php`):

| Status | Value | Meaning |
|---|---|---|
| `STATUS_DRAFT` | 0 | being edited |
| `STATUS_PUBLISHED` | 1 | the current version |
| `STATUS_PENDING` | 2 | waiting, for example for an approval workflow |
| `STATUS_ARCHIVED` | 3 | an earlier published version |
| `STATUS_REJECTED` | 4 | refused by an approver |
| `STATUS_INTERNAL_DRAFT` | 5 | a draft the system made, not yet touched by the editor |
| `STATUS_REPEAT` | 6 | used by workflows that run again |
| `STATUS_QUEUED` | 7 | waiting for asynchronous publishing |

## What publishing does

Publishing runs the kernel's publish operation (`content/publish`). Among other things it:

- runs the workflows connected to the publish triggers, which can stop or delay publishing
  ([Workflows and triggers](workflows-and-triggers.md));
- makes the version current, sets the object's name from the name pattern and updates the URL aliases;
- updates the search index, at once or through the indexing cronjob when delayed indexing is on ([Search](search.md));
- clears the view caches of the object's nodes and of related nodes according to `viewcache.ini`
  ([Caching for CMS developers](caching-for-cms-developers.md));
- sends notifications to subscribers, unless an editor chose to publish without notification
  ([Publish without notification](../../features/6.0/publish-without-notification.md)).

That is why code must publish through the kernel and never write version rows itself
([Content from PHP](content-from-php.md)).

## How many versions are kept

`settings/content.ini [VersionManagement]`:

| Setting | Shipped | Meaning |
|---|---|---|
| `DefaultVersionHistoryLimit` | `45` | versions kept per object; the oldest archived version goes when the limit is reached |
| `VersionHistoryClass[<class id>]` | `[1]=75` | a different limit for one class (class 1 is the folder) |
| `DraftsDuration[]`, `DraftsCleanUpLimit` | 90 days, 100 | the age and number per run of old drafts that the `old_drafts_cleanup.php` cronjob removes; that cronjob is in no part when shipped |
| `InternalDraftsDuration[]`, `InternalDraftsCleanUpLimit` | 1 day, 100 | removal of untouched internal drafts by the `internal_drafts_cleanup.php` cronjob |

To remove old versions now, read the help first; without `--help` the command removes versions:

```bash
./console exp:cleanupversions --help --allow-root-user
```

## Drafts, autosave and abandoned drafts

With the `ezautosave` extension active, the edit form saves the draft in the background every 180 seconds
(`autosave.ini [AutosaveSettings] Interval`), so an editor who loses the browser tab finds the draft again under
**My drafts**. Drafts that nobody publishes stay until removed; add `old_drafts_cleanup.php` to a cronjob part, or ask
editors to remove them ([My drafts and my pending items](../../guides/drafts-and-pending.md)).

## Preview

Editors preview a draft before publishing. `content.ini [VersionView]` decides which designs a preview can use
(`AvailableSiteDesignList[]`) and which view modes (`ViewModes[]`, `full` shipped); a preview shows the draft with
the site's templates, so test your full view templates with drafts as well
([Version preview view modes](../../features/6.0/version-preview-view-modes.md)).

## Publishing and hiding on a date

Two cronjobs of the default part act on dates stored in content (`settings/cronjob.ini`):

| Cronjob | Setting | What it does |
|---|---|---|
| `unpublish.php` | `content.ini [UnpublishSettings] RootNodeList[]`, `ClassList[]` | removes objects of the listed classes below the listed nodes once their `unpublish_date` attribute is in the past |
| `hide.php` | `content.ini [HideSettings] RootNodeList[]`, `HideDateAttributeList[<class>]=<attribute>` | hides nodes whose date attribute is in the past |

Both lists are empty when shipped, so nothing happens until you configure them and add the date attribute to your
classes. An approval before publishing is a workflow, not a date ([Workflows and triggers](workflows-and-triggers.md)).

## Asynchronous publishing

`content.ini [PublishingSettings] AsynchronousPublishing=disabled` is the shipped value. When enabled, publishing
queues the version (status `STATUS_QUEUED`) and a daemon, `bin/php/ezasynchronouspublisher.php`, publishes it; the
Composer metadata notes that this needs the PHP `pcntl` extension. Use it only when publishing large objects makes
editors wait, and run the daemon under supervision.

## For your code

- In templates, `$node.object.current_version` and `$node.contentobject_version` give the published version number;
  `$node.object.published` and `$node.object.modified` give the dates.
- In PHP, create versions through `eZContentFunctions::updateAndPublishObject()`, which creates the version, sets the
  attributes and runs the publish operation ([Content from PHP](content-from-php.md)).
- Workflow events see the version being published and can hold it ([Workflow event types](../extension-development/part-3-extension-developer/workflow-event-types.md)).

## Related pages

- [Publishing, versions and the trash](../publishing-and-versions.md): the editor's view
- [Translations and languages](translations-and-languages.md)
- [The content model](the-content-model.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
