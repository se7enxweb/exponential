---
title: Classic landing pages with ezflow
audience: website-creator, developer
category: website-creation
order: 320
summary: The ezflow page builder of the classic design - the page datatype, zone layouts, blocks from block.ini, push to block, the timeline and its cronjobs - and when to choose it over the layouts editor.
keywords: ezflow, page datatype, zone layout, blocks, block.ini, zone.ini, push to block, timeline, frontpage, landing page, ezflowupdate, cronjob, website creator
type: help
---
# Classic landing pages with ezflow

This page is for website creators who maintain or build sites on the classic design with `ezflow`, the page builder
that came before the layouts editor. It describes how ezflow pages work, what to configure, and how to decide between
ezflow and the layouts editor for new work. Reference: [ezflow](../../../features/6.0/extensions/ezflow.md).

## Contents

1. [How ezflow works](#how-ezflow-works)
2. [Set it up](#set-it-up)
3. [Defining your own block](#defining-your-own-block)
4. [The cronjobs](#the-cronjobs)
5. [ezflow or the layouts editor](#ezflow-or-the-layouts-editor)

## How ezflow works

- A class gets a **page** attribute (the demo's `frontpage` and `landing_page` classes have one).
- When editing, the editor chooses a **zone layout**; each zone holds **blocks**.
- A block lists items: chosen by **push to block** from the content view, or arriving from its source (a fetch, RSS,
  keywords).
- Items can be scheduled; the **timeline** shows the page at a chosen moment.

## Set it up

1. Activate `ezflow` (it requires `ezjscore` and extends `ezwebin` and `ezwt`).
2. Allowed zone layouts are in `zone.ini [General] AllowedTypes[]`: `GlobalZoneLayout`, `2ZonesLayout1`,
   `2ZonesLayout2`, `2ZonesLayout3`, `3ZonesLayout1`, `3ZonesLayout2`, `CallForActionLayout`, restricted per class
   by `AvailableForClasses[]`.
3. Block types are defined in `block.ini`, one block per type.
4. `ezflow.ini [eZFlowOperations] UpdateOnPublish=enabled` updates blocks when content is published.

## Defining your own block

A block type is a `block.ini` entry plus a template. In your design extension:

```ini
# extension/mysite_design/settings/block.ini.append.php
[General]
AllowedTypes[]=MyEvents

[MyEvents]
Name=Upcoming events
NumberOfValidItems=5
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=my_events
ViewName[my_events]=Upcoming events
```

The keys are those of the shipped `[ItemList]` block in `extension/ezflow/settings/block.ini`. A block renders
`block/view/view.tpl` (`extension/ezflow/design/standard/templates/block/view/view.tpl`, which lists
`$block.valid_nodes`); each view gets its own template through an override, as the shipped
`extension/ezflow/design/ezflow/override/templates/block/itemlist1.tpl` does:

```ini
# settings/siteaccess/<site>/override.ini.append.php
[block_my_events]
Source=block/view/view.tpl
MatchFile=block/my_events.tpl
Subdir=templates
Match[view]=my_events
```

This is the same shape as the rules the `ezflow_site` package's installer writes for the shipped blocks (for example
`[block_itemlist1]` with `Source=block/view/view.tpl`, `MatchFile=block/itemlist1.tpl`, `Match[view]=itemlist1`).

## The cronjobs

```bash
php runcronjobs.php ezflow          # ezflowupdate.php: rotates and updates blocks
php runcronjobs.php ezflow-cleanup  # ezflowcleanup.php
```

Without them, scheduled items do not appear. Add both to cron ([Cronjobs](../../../guides/cronjobs.md)).

## ezflow or the layouts editor

| | ezflow | Layouts editor |
|---|---|---|
| Where the page lives | in a content attribute of the object (versioned with it) | in its own layout tables, mapped by rules |
| Reusing a header/footer | per page | shared layouts and linked zones |
| One layout for many pages | no, each page has its own | yes, by rules |
| Scheduling and timeline | yes | time conditions on rules |
| Block catalogue | `block.ini` | 46 block types plus template blocks |
| Choose for | existing ezflow sites | new sites |

Existing ezflow sites keep working; new landing pages are easier with the layouts editor
([Landing pages with the layouts editor](landing-pages-with-layouts.md)).

## Related pages

- [ezflow](../../../features/6.0/extensions/ezflow.md)
- [ezwebin](../../../features/6.0/extensions/ezwebin.md)
- [Cronjobs for applications](../app-creators/cronjobs-for-applications.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
