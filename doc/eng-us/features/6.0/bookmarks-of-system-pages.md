# Bookmarks of system pages

Since Exponential 6.0.15 a bookmark can point to a **system page**, a page of a module view such as
`setup/info`, `exphelp/dashboard`, `role/view/3`, `audit/console/(user)/14` or a search
(`content/search?SearchText=news`), as well as to a node. System page bookmarks have a name of their own, sit in the
same [bookmark folders](bookmark-folders.md) as node bookmarks and are arranged, moved and removed with them. They are
independent of the host and the siteaccess, checked for access every time they are shown, greyed with the reason when
they cannot be opened, and extensible through `bookmark.ini`.

[Features](README.md) · User help: [Bookmarking system pages](../../help/bookmarking-system-pages.md) ·
Guide: [Bookmarks](../../guides/bookmarks.md) · Upgrade and API notes:
[Bookmarks of system pages (upgrade)](../../bc/6.0/bookmarks-of-system-pages.md) · Settings:
[bookmark.ini](../../examples/ini-settings/bookmark/README.md)

## Where it is

| Place | What you get |
|---|---|
| Above the breadcrumb of every module view, in every administration design (admin4, admin4l, admin, admin3 and the designs that fall back to them, the editor siteaccess included) | **Bookmark this page**: a button that opens a form in place with the name (the page title, editable), the folder and **Add bookmark**. A bookmarked page shows **Bookmarked**; the form then saves the name and the folder |
| Bookmarks box (right column) | The same button on module views, instead of the disabled *Add to bookmarks*. The tree lists system pages with the icon of their kind or module; a page the user cannot open there is greyed, without a link, with the reason as its tooltip and for screen readers |
| My bookmarks (`content/bookmark`) | A figure **System pages** and a hint how to add one; one card per system page with its icon, its type (*System page*, *Search* or the label of an extension's kind), its address, its module and view, **Open**, **Rename**, and everything a node card has for arranging, moving and removing. A page the user cannot open is marked **No access**, has no link and gives the reason. The search finds addresses; the order *Type* sorts system pages by the label of their kind |
| Browse dialog, online editor | Not listed: both select content |
| Front end (site siteaccesses) | Not offered: the visitors' pages are content, bookmarked as nodes, and the site designs have no administration header |
| Remote services | `expbookmark::addPageBookmark` (uri, name, folder_id) and `expbookmark::renameBookmark` (id, name); every exported bookmark has `kind` (`node` or `view`) and, for a system page, `uri` and `can_open` |

## What is stored

A system page bookmark is a row of `ezcontentbrowsebookmark` with `node_id = 0`, so its name, folder and priority
are the ones every bookmark has. Its address is a row of the table `expbookmark_view`:

| Column | Holds |
|---|---|
| `bookmark_id` | The id of the bookmark row (primary key) |
| `user_id` | The owner, as on the bookmark row (indexed) |
| `module_uri` | The address: module, view and the view's parameters, ordered (`role/view/3`) and unordered (`/(offset)/20`), without the host, the siteaccess and the fragment; a query string only with the keys its kind keeps, sorted (`content/search?SearchText=news&SectionID=1`) |
| `created` | When the page was bookmarked |

The module and the view are read from the address when it is shown (`expBookmarkView::parts()`), so the address is
the one thing to validate, compare and change: a renamed module or view is one `UPDATE expbookmark_view SET module_uri
= ...` of the rows that start with the old name. One address is bookmarked once per user: bookmarking it again
updates the name and, when one is chosen, the folder.

## Rules

- **Only internal module views.** The address must start with a module of this installation; a leading siteaccess
  name is dropped; a URL alias (a node), an address with a scheme (`https:`, `javascript:` ...), `//host`, `..` and
  `.` steps, backslashes and control characters are refused, and so is anything over 2000 characters.
- **Never a page of one moment.** `bookmark.ini [ViewBookmarkSettings] NonBookmarkableViews[]` lists the views that
  only act on a posted form, sign in or out, confirm, download or edit (`content/action`, `content/edit`,
  `user/logout`, `role/edit`, `switchlanguage/*` ...), and a module definition can say the same with
  `'bookmarkable' => false` in a view's `$ViewList` entry (as `user/logout` does). Such a view gets no
  **Bookmark this page**, is refused when posted, and an existing bookmark of it is greyed.
- **Query strings only where they mean something.** A module view takes its parameters in its address; the query
  string is dropped, except the keys its kind keeps (`QueryParameters[]`, the saved searches): single values of at
  most 255 characters, sorted, encoded.
- **Access when added**: the user must be able to open the page at that moment (`expViewAccess::check()`, the rules
  of a request: siteaccess rules, the login policy, the view's policy functions, `PolicyOmitList`, the content a
  view shows).
- **Access when shown**: checked again every time the bookmarks are shown, in the siteaccess they are shown in (once
  per address and request). A page the user cannot open there is greyed with the reason
  (`expBookmarkView::reasonText()`): switched off in this siteaccess, no role allows it, the siteaccess may not be
  used, the content may not be read, the view or module no longer exists, the view may not be bookmarked. It can
  still be renamed, moved and removed.
- **Host and siteaccess independent**: nothing of the host or the siteaccess is stored, and the link is made with
  the current siteaccess (`ezurl`), so the same bookmark opens on every host and in every administration siteaccess
  that offers the page.
- **Names**: the page title by default (the path of the page, the last step first, as the window title shows it);
  tags and control characters are removed, at most 255 characters, never empty. Node bookmarks keep showing the name
  of their node and are not renamed.
- **Forms**: adding, renaming, moving and removing are POST forms to `content/bookmark`, protected by the form token
  (ezformtoken) like every form of the administration; nothing changes on a GET.
- **Removing**: removing a bookmark removes its address; removing a folder with everything in it removes the addresses
  of the system page bookmarks in it. Removing a node (`removeByNodeID()`) never touches system page bookmarks.
- **Auditing**: like node bookmarks, adding or removing a bookmark writes no audit event: bookmarks are personal.

## Settings

`settings/bookmark.ini` (all settings: [bookmark.ini](../../examples/ini-settings/bookmark/README.md)):

| Setting | Default | Purpose |
|---|---|---|
| `[ViewBookmarkSettings] NonBookmarkableViews[]` | 40 views of actions, sign-in and edit forms | Views that can never be bookmarked, `module/view` or `module/*` |
| `[ViewBookmarkSettings] QueryParameters[module/view]` | `content/search` = `SearchText;SectionID;SubTreeArray` | The query string keys a view's bookmark keeps |
| `[ViewBookmarkSettings] ModuleIcons[module]`, `DefaultIcon` | `setup` = `tools`, `role` = `shield-lock`, ... ; `gear` | The icon of a system page bookmark by its module (`a4-bi-<name>`) |
| `[BookmarkTypeSettings] TypeList[]`, `TypeClass[kind]` | `search` = `expBookmarkTypeSearch` | The kinds of bookmark, asked in order |

Example: keep the status filter of a list of your extension and give its pages an icon:

```ini
<?php /* #?ini charset="utf-8"?
# extension/myreports/settings/bookmark.ini.append.php
[ViewBookmarkSettings]
NonBookmarkableViews[]=myreports/export
QueryParameters[myreports/list]=Status
ModuleIcons[myreports]=bar-chart-line
*/ ?>
```

## Adding a kind of bookmark

A kind decides, for the module views it takes, which query string keys their bookmarks keep, their icon and the type
label on My bookmarks. Saved searches are the kind the kernel ships (`expBookmarkTypeSearch`); every other view is a
*System page* (`expBookmarkTypeSystemPage`). An extension adds one with a class extending `expBookmarkType`:

```php
<?php
// extension/mylayouts/classes/mylayoutbookmarktype.php
class myLayoutBookmarkType extends expBookmarkType
{
    public function identifier() { return 'layout'; }
    public function accepts( $module, $view ) { return $module === 'mylayouts' && $view === 'edit'; }
    public function queryParameters( $module, $view ) { return array(); }
    public function icon( $uri ) { return 'layout-sidebar-inset'; }
    public function label() { return ezpI18n::tr( 'extension/mylayouts', 'Layout' ); }
}
```

```ini
<?php /* #?ini charset="utf-8"?
# extension/mylayouts/settings/bookmark.ini.append.php
[BookmarkTypeSettings]
TypeList[]=layout
TypeClass[layout]=myLayoutBookmarkType
*/ ?>
```

Regenerate the extension autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and clear the INI cache. The kinds are
asked in the order of `TypeList[]`; the first whose `accepts()` is true decides. A kind never makes a view bookmarkable
that `NonBookmarkableViews[]` or the module definition excludes, and access is always decided by `expViewAccess`. A
class that is missing or does not extend `expBookmarkType` is skipped with a warning in the debug output.

## Installing and updating

A new installation gets the table from `share/db_schema.dba`, on every engine. An installation made before 6.0.15
gets it from:

- the SQL update of its engine: `update/database/<mysql|postgresql|sqlite>/6.0/dbupdate-6.0.0-6.0.15.sql`
  (section *Bookmarks of system pages*, guarded, so it can run again), or
- on any engine, Oracle (ezoracle) and MongoDB included, through the engine's schema handler:

  ```
  php update/common/scripts/6.0/createbookmarkviewtable.php --dry-run -s <admin siteaccess>   # says whether it is missing
  php update/common/scripts/6.0/createbookmarkviewtable.php -s <admin siteaccess>
  ```

Until the table exists the administration does not offer **Bookmark this page** and node bookmarks work exactly as
before. `settings/bookmark.ini` ships with the kernel; an installation that kept an older settings tree without it
still works: nothing is excluded but what module definitions exclude, no query string is kept, and every system page
has the gear icon. After the update, clear the caches (`php bin/php/ezcache.php --clear-all`) and regenerate the
kernel autoloads when the code was copied by hand (`php bin/php/ezpgenerateautoloads.php -k`).

Bookmarks made with the first 6.0.15 builds keep working: their addresses never carried a query string, and a page
that is now excluded is greyed with the reason instead of failing.

## Templates and styles

| File | Part |
|---|---|
| `design/standard/templates/content/bookmark_view_action.tpl` | The button and its form (`mode='toolbar'` or `'box'`) |
| `design/admin4/templates/pagelayout.tpl`, `design/admin4l/templates/pagelayout.tpl`, `design/admin/templates/pagelayout.tpl`, `design/admin3/templates/pagelayout.tpl` | Include it above the breadcrumb |
| `design/admin4/templates/toolbar/full/admin_bookmarks.tpl` and the admin copy | The Bookmarks box: the button on module views |
| `design/standard/templates/content/bookmark_tree.tpl` | The tree: system page rows (`row.kind = 'view'`, `row.icon`, `row.reason_text`) |
| `design/admin4/templates/content/bookmark.tpl` and the admin copy | The cards of system pages: icon, type, reason, Rename |
| `design/admin4/stylesheets/modules/content.css` and the admin copy | `.a4-content-bookmark-pageaction`, `.a4-content-bookmark-pageform`, `.a4-content-bookmark-kind`, `-uri`, `-rename`, `-viewhint`; tokens only, so light and dark follow the theme |
| `design/standard/stylesheets/exp_bookmarks.css` | `.exp-bm-view`, `.exp-bm-kind` in the tree |

No inline styles, no script: the form is a `<details>` element and posts to `content/bookmark`
(`BookmarkViewAction=add`, `BookmarkViewURI`, `BookmarkViewName`, `BookmarkFolderID`, `RedirectURI`).
