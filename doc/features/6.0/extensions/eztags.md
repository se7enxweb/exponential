# eztags: Tags, a taxonomy for content

This page is for site builders who classify content, and for editors who tag it. `eztags` (shown in the admin as
**Tags** since 2.3.5) adds a tag datatype, a tag tree you manage in the admin, and a tag view with its own pages. It
does more than the kernel's `ezkeyword` datatype:

- a **tree hierarchy** of tags, with synonyms;
- add, edit, move, merge and delete tags in the admin (**Tags** top menu, `/tags/dashboard`);
- tagging in the object edit form with **autocomplete**, suggestions and in-place addition of new tags;
- a tag view (`/tags/view/...`) like the content view, with the current tag in `$tag`;
- an **extended attribute filter** for `content/list` and `content/tree` fetches;
- closed (predefined) and open (user tags) classification, or a combination of both, switchable later.

## When to use it

- To replace `ezkeyword`.
- To replace a closed classification built on `ezselection` or `ezobjectrelation(list)`: better input, easier
  maintenance.
- To make dynamic pages from tagged content.

The extension's own `doc/` folder has an install guide, a usage guide, upgrade notes, a user manual and the changelog.
This page covers what the Exponential 6 releases (2.3.1 to 2.4.11) changed.

## Use it

1. Open **Tags** in the admin (`/tags/dashboard`) and build the tag tree.
2. Add a tags attribute to a class in **Setup > Classes** and choose its edit view (Default, Select or Tree).
3. Edit an object and type in the tags field: autocomplete suggests existing tags.
4. Open `/tags/view/<tag>` to see everything tagged with it.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `eztags.ini` | `GeneralSettings` | `URLPrefix` | `tags/view` | URL prefix of a tag page |
| `eztags.ini` | `GeneralSettings` | `ShowOldStyleChildrenList` | `disabled` | Server rendered children list instead of the table |
| `eztags.ini` | `GeneralSettings` | `DefaultAlwaysAvailable` | `false` | New tags are available in every language |
| `eztags.ini` | `GeneralSettings` | `TagCloudOverSolr` | `enabled` | Build the tag cloud from Solr when available |
| `eztags.ini` | `GeneralSettings` | `MaxResults` | `24` | Number of autocomplete results |
| `eztags.ini` | `GeneralSettings` | `AutoCompleteType` | `start` | Match the start or any part |
| `eztags.ini` | `TreeMenu` | `ToolTips`, `MaxTags`, `AutoopenCurrentTag`, `MenuPersistence` | `enabled`, `100`, `enabled`, `enabled` | Tag tree in the admin |
| `eztags.ini` | `SearchSettings` | `SearchLimit`, `IndexSynonyms`, `IncludeSynonyms`, `IndexParentTags` | `15`, `enabled`, `disabled`, `disabled` | Indexing and search |
| `eztags.ini` | `EditSettings` | `AvailableViews[]` | Default, Select, Tree | Edit views of the tag field |

Policies, module `tags`: `read`, `dashboard`, `id`, `view`, `add` (limited by tag), `addsynonym`, `edit`,
`editsynonym`, `delete`, `deletesynonym`, and more.

## What changed in the Exponential 6 releases

### Works on every database

- **SQLite** (2.3.2, 2.3.3): the `MOD()` function and an `ORDER BY` after a `UNION` were not portable; they are now
  `%` and positional references. A SQLite schema (`share/db_schema.dba` equivalent with `eztags_keyword` and
  `IF NOT EXISTS` guards) lets the tags package export work on the SQLite backend.
- **MongoDB** (2.3.4): tags rendered as nothing, because every read of a tag joins `eztags` with `eztags_keyword` and
  MongoDB has no join; the driver answered with an empty result. A MongoDB branch reads the two collections separately
  and matches them, keeping the translation rules (main language when asked, else an explicit locale, else the site's
  language priority with the tag's main language as the last resort). `EZTagsObject::fetchList()`,
  `fetchListCount()` and the datatype's `createFromAttribute()` are branched; MySQL and SQLite keep the original
  statements.
- **Oracle and PostgreSQL** (2.4.5): tag lookups by main translation took a bit with `%`, which Oracle rejects, so
  tagged content lost its tags; they use `eZDB::bitAnd()` now. The tag tree filter and the tag search by subtree
  compared with a double-quoted pattern, which is an identifier outside MySQL; patterns are single-quoted, and the
  "and" variant of the tree filter, which lacked the closing quote of its pattern, works on every database.

### Admin

- The top menu entry, tab title and tooltip say **Tags** (2.3.5, 2.4.1) instead of "eZ Tags".
- The administration renders in every admin design (2.4.8). eZ Tags shipped its admin templates in `design/admin2`
  only, so the dashboard, tag pages, forms, left menu and node tab were blank in the `admin` and `admin3` designs.
  `design/admin` now carries one small template per admin2 template that includes the admin2 original, so there is
  one copy to maintain.
- The tags field edit page works in designs that do not load eZ Tags' `FrontendJavaScriptList` (2.4.8): the field's
  template requires its own scripts and styles, so tags can be edited on every site design.
- Every visible text is a translation string with German; the tab and tooltip are translated (2.4.2, 2.4.3).

### jQuery 4 and no YUI (2.4.7, 2.4.8, 2.4.9)

- jsTree 3.3.17 replaces the older build. The tag field, modal dialog, translations tab and children list filter use
  `.on()`, `.off()`, `.trigger()`, `.prop()`, `Array.isArray` and `Function.prototype.bind`, so the admin tag pages
  work on jQuery 4 without jQuery Migrate warnings.
- The children table of a tag runs on Exponential UI's `exp::datatable` (`$.fn.eZTagsChildrenExp`), and the YUI 2
  DataTable is gone in 2.4.9: `design.ini` no longer adds `ezjsc::yui2` to every admin page. `$.fn.eZTagsChildren`
  remains as the old name of the same table, and the template `eztags_children_yui.tpl` is now
  `eztags_children_table.tpl`. The extension requires `se7enxweb/expui ^1.0.0.1`. See
  [YUI removal](../../../bc/6.0/yui-removal.md).

### Packaging

`ezinfo.php` (2.4.6) states name, version, copyright, license and website next to `extension.xml`, so the about page
and the upgrade checks read the same release from either file. The entry point files carry a header of 7x and the
Exponential Foundation above the original headers (2.4.10), and the command line scripts, cronjob parts and module
views are classes the files call (2.4.10, 2.4.11); see
[CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md).

## Related pages

- [xrowextract](xrowextract.md): exports tags through its keyword and tags handlers and an eztags extended filter
- [MongoDB database support](../mongodb-database-support.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/eztags.md) and [release notes](../../../changelogs/extensions/eztags.md)
- [Change ledger](../../../history/ledger/eztags.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-04](../../../history/extensions/months/2026-04.md), [2026-08](../../../history/extensions/months/2026-08.md), [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
