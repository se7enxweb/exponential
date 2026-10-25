# Bookmarks of system pages

Since Exponential 6.0.15 a bookmark can point to a **system page**, a page of a module view such as
`setup/info`, `exphelp/dashboard` or `audit/console/(user)/14`, as well as to a node. System page bookmarks have a
name of their own, sit in the same [bookmark folders](bookmark-folders.md) as node bookmarks and are arranged,
moved and removed with them.

[Features](README.md) · User help: [Bookmarking system pages](../../help/bookmarking-system-pages.md) ·
Guide: [Bookmarks](../../guides/bookmarks.md) · Upgrade and API notes:
[Bookmarks of system pages (upgrade)](../../bc/6.0/bookmarks-of-system-pages.md)

## Where it is

| Place | What you get |
|---|---|
| Above the breadcrumb of every module view (admin4, admin4l, admin and the designs that fall back to it) | **Bookmark this page**: a button that opens a form in place with the name (the page title, editable), the folder and **Add bookmark**. A bookmarked page shows **Bookmarked**; the form then saves the name and the folder |
| Bookmarks box (right column) | The same button on module views, instead of the disabled *Add to bookmarks*. The tree lists system pages with a gear icon; a page the user may no longer open is shown greyed out, without a link |
| My bookmarks (`content/bookmark`) | A figure **System pages** and a hint how to add one; one card per system page with its address, its module and view, **Open**, **Rename**, and everything a node card has for arranging, moving and removing. A page the user cannot open is marked **No access** and has no link. The search finds addresses; the order *Type* sorts them under *System page* |
| Browse dialog, online editor | Not listed: both select content |
| Remote services | `expbookmark::addPageBookmark` (uri, name, folder_id) and `expbookmark::renameBookmark` (id, name); every exported bookmark has `kind` (`node` or `view`) and, for a system page, `uri` and `can_open` |

## What is stored

A system page bookmark is a row of `ezcontentbrowsebookmark` with `node_id = 0`, so its name, folder and priority
are the ones every bookmark has. Its address is a row of the new table `expbookmark_view`:

| Column | Holds |
|---|---|
| `bookmark_id` | The id of the bookmark row (primary key) |
| `user_id` | The owner, as on the bookmark row |
| `module_uri` | The address: module, view and the view's parameters, without the siteaccess and without a query string or fragment (`content/bookmark/(folder)/top`) |
| `created` | When the page was bookmarked |

One address is bookmarked once per user: bookmarking it again updates the name and, when one is chosen, the folder.

## Rules

- **What can be bookmarked**: the page of a module view of this installation. The address must start with a module
  that exists; a leading siteaccess name is dropped; a URL alias (a node), an address of another site, `..` steps,
  backslashes and control characters are refused. The button is not shown on node pages, while editing or browsing,
  or on error pages.
- **Access when added**: the user must be able to open the page at that moment (`expViewAccess::check()`, the rules
  of a request: siteaccess rules, the login policy, the view's policy functions, `PolicyOmitList`).
- **Access when shown**: checked again every time the bookmarks are shown (once per address and request). A page the
  user may no longer open, or whose view no longer exists, is shown without a link; it can still be renamed, moved
  and removed.
- **Names**: the page title by default (the path of the page, the last step first, as the window title shows it);
  tags and control characters are removed, at most 255 characters, never empty. Node bookmarks keep showing the name
  of their node and are not renamed.
- **Removing**: removing a bookmark removes its address; removing a folder with everything in it removes the addresses
  of the system page bookmarks in it. Removing a node (`removeByNodeID()`) never touches system page bookmarks.
- **Auditing**: like node bookmarks, adding or removing a bookmark writes no audit event: bookmarks are personal.

## Installing and updating

A new installation gets the table from `share/db_schema.dba`, on every engine. An installation made before 6.0.15
gets it from:

- the SQL update of its engine: `update/database/<mysql|postgresql|sqlite>/6.0/dbupdate-6.0.0-6.0.15.sql`
  (section *Bookmarks of system pages*, guarded, so it can run again), or
- on any engine, Oracle and MongoDB included:

  ```
  php update/common/scripts/6.0/createbookmarkviewtable.php --dry-run -s <admin siteaccess>   # says whether it is missing
  php update/common/scripts/6.0/createbookmarkviewtable.php -s <admin siteaccess>
  ```

Until the table exists the administration does not offer **Bookmark this page** and node bookmarks work exactly as
before. After the update, clear the caches (`php bin/php/ezcache.php --clear-all`) and regenerate the kernel autoloads
when the code was copied by hand (`php bin/php/ezpgenerateautoloads.php -k`).

## Templates and styles

| File | Part |
|---|---|
| `design/standard/templates/content/bookmark_view_action.tpl` | The button and its form (`mode='toolbar'` or `'box'`) |
| `design/admin4/templates/pagelayout.tpl`, `design/admin4l/templates/pagelayout.tpl`, `design/admin/templates/pagelayout.tpl` | Include it above the breadcrumb |
| `design/admin4/templates/toolbar/full/admin_bookmarks.tpl` and the admin copy | The Bookmarks box: the button on module views |
| `design/standard/templates/content/bookmark_tree.tpl` | The tree: system page rows (`row.kind = 'view'`) |
| `design/admin4/templates/content/bookmark.tpl` and the admin copy | The cards of system pages, Rename |
| `design/admin4/stylesheets/modules/content.css` and the admin copy | `.a4-content-bookmark-pageaction`, `.a4-content-bookmark-pageform`, `.a4-content-bookmark-kind`, `-uri`, `-rename`, `-viewhint`; tokens only, so light and dark follow the theme |
| `design/standard/stylesheets/exp_bookmarks.css` | `.exp-bm-view`, `.exp-bm-kind` in the tree |

No inline styles, no script: the form is a `<details>` element and posts to `content/bookmark`
(`BookmarkViewAction=add`, `BookmarkViewURI`, `BookmarkViewName`, `BookmarkFolderID`, `RedirectURI`).
