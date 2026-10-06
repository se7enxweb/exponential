# RSS feeds: publishing exports and reading imports

This guide teaches the RSS page of the administration interface (Setup > RSS, `/rss/list`): what an RSS export and
an RSS import are, what every part of the page says, how to create, check and remove a feed, and what to do when a
feed reader gets nothing or an import brings nothing in.

It is for administrators who publish feeds or podcasts and for operators who keep imports running. Every figure
and message below was checked against the code (`kernel/private/classes/views/rss/list.php`,
`kernel/classes/ezrssexport.php`, `kernel/classes/ezrssimport.php`, `kernel/private/classes/cronjobs/rssimport.php`
and `kernel/private/classes/views/rss/feed.php`) on the demonstration server (alpha.se7enx.com) on 6 October 2026.

[Guides](README.md) · Related: [RSS: Apple Podcasts feeds, a paged feed list and safer exports](../features/6.0/rss-podcast-and-feed-list.md),
[OPML exports](../bc/6.0/opml.md), [Cronjobs](cronjobs.md)

## In short

- An **export** is a feed this site publishes at `/rss/feed/<address>`. It lists the newest objects below its
  **sources** (locations, each with a class and optionally their subitems), as RSS 1.0, RSS 2.0, Atom, OPML or an
  Apple Podcasts feed.
- An **import** reads the feed of another site and creates an object below its **destination** for every item it
  has not seen before. The **rssimport cronjob** (`rssimport.php`) runs the active imports; nothing is read without
  it.
- The page leads with an overview (exports, active exports, imports, active imports, imported objects), a search
  and a filter (All, Active, Inactive, Need attention), then one card per export and one per import.
- **Remove selected** asks first and says what removing means: a removed export's address stops answering its
  readers; a removed import keeps the objects it created.

## 1. Opening the page

Setup > RSS, or `/rss/list` in the admin siteaccess. The page needs the `rss/edit` policy. The editor siteaccess
does not offer the RSS module at all.

## 2. The overview and the controls

| Figure | What it counts |
|---|---|
| Exports, Active exports | Published exports, and those that answer at their address |
| Imports, Active imports | Imports, and those the cronjob reads |
| Imported objects | Objects whose remote id starts with `RSSImport_`: every object any import has created |

Below them:

- **Find a feed** filters the cards on this page of each list by name, address, format, source, destination or ID.
- **Show** keeps only active, inactive or attention-needing cards.
- **Per page** sets the size of both lists (25, 50, 250 by default, `content.ini [RSSListSettings] ItemsPerPageList`)
  and is remembered for your user.
- A line says how long a feed is cached: `site.ini [RSSSettings] CacheTime` seconds, or "written anew for every
  request" when it is 0.

The search and the filter need javascript; without it every card is shown and every button still works.

## 3. An export card

The head shows the name (a link to its edit page), the ID, **Active** or **Inactive**, the format in words
(*RSS 2.0*, *Atom 1.0*, *OPML 2.0 (list of feeds)*, *Apple Podcasts (RSS 2.0 + iTunes)*) and **Needs attention**
when something is wrong. **Open feed** opens the public address in a new tab; **Edit** opens the form.

| Field | What it says |
|---|---|
| Feed address | The address readers subscribe to: the public site of the export's siteaccess, then `/rss/feed/<address>` |
| Sources | Up to five sources with their class and "with subitems", then "and N more"; a source whose location is gone says "(missing)". OPML exports count the feeds they list |
| Items in the feed | The most objects one feed holds, and "main locations only" when that is set |
| Last written | When the cached copy of the feed was last written (the newest of all siteaccesses); "Not requested since the cache was cleared" until a reader asks; "On every request" when CacheTime is 0; inactive exports are not served |
| Links point to | The siteaccess whose address the feed's links use |
| Modified | When and by whom the export was last saved |

**Needs attention** lists the reasons under the card: no feed address, no source, a source that no longer exists.
Only active exports are marked.

Sort the exports by name, modified, status, format, address or ID with the **Sort by** chips; the chip in use
turns the order round. Sorting starts the list at its first page and keeps the imports where they are.

## 4. An import card and the cronjob

| Field | What it says |
|---|---|
| Source URL | The feed the import reads |
| Destination | The location the new objects go below (a link), or "missing" |
| Creates | The class of the new objects, and the user who owns them |
| Imported | How many objects this import has created (remote id `RSSImport_<id>_...`) |
| Newest item | When the newest of them was published: the last time the import brought something in |

Above the imports a status bar names the cronjob part that runs `rssimport.php`, its schedule in words and its next
run, with **Open the cronjob** for that part on the cronjobs page. It warns when the crontab does not run that part
and is red when no part runs the script.

## 5. Creating and removing

**New export** and **New import** open the forms, as before.

To remove: tick the cards, press **Remove selected** under that list. A confirmation lists each feed with its
address or source and says what happens:

- exports: the addresses stop answering, readers get an error from then on; the sources go with the export; the
  content is not touched;
- imports: the cronjob stops reading them; the objects they created stay (their number is shown).

**Remove N exports** removes them, with the draft of an export being edited and the sources and OPML lines of
both; **Cancel** goes back and changes nothing. The list then says "Removed: ..." once. Pressing Remove selected
with nothing ticked says so instead of doing nothing silently.

To stop a feed for a while instead, edit it and clear **Active**.

## 6. Troubleshooting

| Problem | Look at | Fix |
|---|---|---|
| A reader gets an error | The card: Inactive? Feed address "not set"? | Edit, set an address, tick Active |
| A feed is empty | Sources: none, or "(missing)" | Add a source whose location exists, with the right class |
| A change does not show in the feed | Last written | Wait for CacheTime, or clear the RSS cache (`php bin/php/ezcache.php --clear-id=rss_cache --allow-root-user`) |
| An import brings nothing | The status bar; Imported and Newest item | Schedule the part with `rssimport.php`; check the Source URL opens; check Destination and Creates are set |

## References

- Module and view: `kernel/rss/module.php`, `kernel/private/classes/views/rss/list.php`
  (`ListView::exportInfo()`, `importInfo()`, `importedObjects()`, `lastGenerated()`, `summary()`,
  `removeExports()`, `removeImports()`); the pager `kernel/rss/ezrsslistpager.php`.
- Templates: `design/admin4/templates/rss/list.tpl`, `confirmremove.tpl`, `exp_style.tpl`, `exp_list_script.tpl`
  (the same files in `design/admin/templates/rss/`); the shared pager `design/standard/templates/rss/pagination.tpl`.
- The cronjob part: `Processlist::scriptCronjob()` in `kernel/private/classes/views/workflow/processlist.php`.
- Tests: `tests/tests/kernel/classes/expAdminListsRedesignTest.php` (no database).
