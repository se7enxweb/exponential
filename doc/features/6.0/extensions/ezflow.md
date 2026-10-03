# ezflow: pages, zones and blocks

`ezflow` ("eZ Flow LS") is the page builder of the classic design: a **page** datatype whose
content is laid out in **zones** (for example left and right) holding **blocks** (a list of items,
a campaign, a banner, RSS, a keyword list and so on). Editors rearrange blocks with drag and drop,
push an object into a block from the content view, schedule when items appear in a block and see the
change on a **timeline**. It requires `ezjscore`, extends `ezwebin` and `ezwt`, and comes with workflow
event types, a datatype, cronjobs and a Flash chart module (`modules/flash`).
Version 6.1.5 (2 October 2026) is current.

## Use it

* A class with a `page` attribute (the demo's `frontpage` and `landing_page`) lets editors choose a **zone
  layout** from `zone.ini [General] AllowedTypes[]` (`GlobalZoneLayout`, `2ZonesLayout1`, `2ZonesLayout2`,
  `2ZonesLayout3`, `3ZonesLayout1`, `3ZonesLayout2`, `CallForActionLayout`) restricted by
  `AvailableForClasses[]`.
* Blocks are defined in `block.ini`; items are pushed into a block from **push to block** in the content
  view, or arrive automatically from the block's source (a fetch, RSS, keywords).
* `ezflow.ini [eZFlowOperations] UpdateOnPublish=enabled` updates blocks when content is published;
  `[SafetyDelay] DelayInSeconds=5`.
* Two cronjob parts: `php runcronjobs.php ezflow` (`ezflowupdate.php`, rotates and updates blocks) and
  `ezflow-cleanup` (`ezflowcleanup.php`).

## What changed in the Exponential 6 releases

| Release | Change |
|---|---|
| 6.0.0 to 6.0.2 | Bugfix release for PHP 8.2; package vendor and documentation updated from `ezflow-ezpackage` |
| 6.1.0 | Module views no longer declare functions or classes at top level without guard, so a persistent worker (Velocity) survives the second request ([details](../../../bc/6.0/extensions-behaviour-changes.md#1-persistent-php-workers-module-views-no-longer-declare-at-file-level-without-a-guard)) |
| 6.1.1, 6.1.2 | `ezinfo.php`, extension name and license; templates write `<br>` as HTML5 does; texts name Exponential |
| 6.1.3 | **Oracle**: a block pool over its `Limit` is trimmed (the old `DELETE ... ORDER BY ts_publication LIMIT n` is rejected by PostgreSQL, Oracle and most SQLite builds, so the pool was never trimmed there; the oldest items are selected with the row limit of `eZDB::arrayQuery()` and removed by key), and `eZPageBlock::fetch()` finds a page block (table aliases without `AS`) |
| 6.1.4 | **YUI removed.** The page editor, block tools, push to block, schedule dialog and timeline run on jQuery with the same server calls, POST fields, ids and events (`blocktools.js`, `zonetools.js`, `scheduledialog.js`, `ezpushtoblock.js`, `ezajaxsearch.js`); drag and drop uses pointer events; the new `ezflowcalendar.js` and `ezflowwidgets.css` provide calendar, tabs, dialog, buttons and menu; the ezpage edit tabs use `.ezpage-tabs*` and the timeline page has the class `ezflow-skin`; the unused Prototype library and YUI's sprite copy are removed |
| 6.1.5 | Commands and cronjob parts list a description; copyright notices name 1998 - 2026 7x & Exponential Foundation first |

If you wrote custom block tools that call the YUI widgets, port them to the jQuery modules; the server API
did not change. See [YUI removal](../../../bc/6.0/yui-removal.md).

## Related

* [ezwebin](ezwebin.md), [ezdemo](ezdemo.md), [ezwt](ezwt.md)
* [Chronicle](../../../history/extensions/ezflow.md) and [release notes](../../../changelogs/extensions/ezflow.md)
