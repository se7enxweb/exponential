# Bookmarks of system pages: upgrade and developer notes

Exponential 6.0.15 lets a bookmark point to the page of a module view (a "system page") as well as to a node. This
page lists what changed for code, templates, extensions and databases. The feature itself:
[Bookmarks of system pages](../../features/6.0/bookmarks-of-system-pages.md); for users:
[Bookmarking system pages](../../help/bookmarking-system-pages.md).

## Database

- New table `expbookmark_view` (`bookmark_id` primary key, `user_id` indexed, `module_uri` longtext, `created`), in
  `share/db_schema.dba`, the kernel schemas of MySQL, PostgreSQL and SQLite, and the 6.0.0 to 6.0.15 update files.
- New update script `update/common/scripts/6.0/createbookmarkviewtable.php` (`--dry-run`), for every engine through
  its schema handler (`expBookmarkView::install()`).
- `ezcontentbrowsebookmark` is unchanged. A system page bookmark is a row with `node_id = 0`.
- Nothing is migrated; existing bookmarks stay node bookmarks.

## Code that reads bookmarks

| Before | Now |
|---|---|
| `eZContentBrowseBookmark::fetchListForUser( $user, $offset, $limit )` returned every bookmark | Returns the bookmarks of **nodes** only (the list of the browse page and of `fetch( 'content', 'bookmarks' )`, as it always meant). Pass a fourth argument `false` for both kinds |
| `$bookmark.node` always a node, or null for a removed node | Also null for a system page bookmark (`node_id = 0`); `fetchNode()` does not query node 0 |
| — | New function attributes `kind` (`'node'` or `'view'`) and `module_uri` (`''` for a node) |
| `eZContentBrowseBookmark::remove()` | Removes the address of a system page bookmark too |
| `eZContentBrowseBookmark::removeByNodeID( 0 )` would have removed all system page bookmarks | Does nothing for 0 or less |
| `fetch( 'content', 'bookmark_rows' )` | Every bookmark row has `kind`; a system page row also `uri`, `module`, `view`, `can_open` (`expBookmarkView::decorateRows()`) |
| `eZContentBrowseBookmarkFolder::fetchRowsForUser()`, `fetchTreeForUser()` | Unchanged: both kinds, without the decoration |

Templates that draw bookmark rows themselves should test `$row.bookmark.node` (as `bookmark_tree.tpl` always did) or
`$row.kind`; a template that assumed every bookmark has a node gets null for system pages from `bookmark_rows`, but
`fetch( 'content', 'bookmarks' )` still returns nodes only.

`expBookmarkPage::items()` and `item()` take a third/fourth argument, the label of system pages (default
`'System page'`); items have `kind` and `uri` (system pages also `module`, `view`), and `summary()` has `views`.

## New API

| Where | What |
|---|---|
| `expBookmarkView` (`kernel/classes/expbookmarkview.php`) | The persistent object of `expbookmark_view` and the rules: `isAvailable()`, `exists()`, `install()`, `normalizeURI()`, `moduleURI()`, `withRequestParameters()`, `defaultName()`, `urisForUser()`, `findForUser()`, `access()`, `decorateRows()`, `createNew()`, `rename()`, `removeForBookmarks()`, `handleAction()`, `pageState()` |
| `fetch( 'content', 'view_bookmark', hash( 'uri', $requested_uri_string, 'path', $module_result.path ) )` | `available`, `uri`, `name`, `bookmark`, `folder_id` for the page shown |
| `content/bookmark` POST | `BookmarkViewAction=add` (`BookmarkViewURI`, `BookmarkViewName`, `BookmarkFolderID`, optional `RedirectURI` back to a page of the site) and `BookmarkViewAction=rename` (`BookmarkID`, `BookmarkName`); the page's cards post `BookmarkRenameButton` with `BookmarkRenameName[<id>]` |
| expservices `expbookmark` | `addPageBookmark` (POST `uri`, `name`, `folder_id`; 422 not a module view, 403 not allowed, 409 before the update), `renameBookmark` (POST `id`, `name`; 409 for a node bookmark); exported bookmarks carry `kind`, and `uri`, `can_open` for system pages; `bookmarks` (flat) lists both kinds |
| ezoe | The online editor's bookmark list counts and lists node bookmarks only |

Example, a bookmark made from code:

```php
$uri = expBookmarkView::moduleURI( 'setup/systemupgrade' );        // false when it is not a module view
$check = expBookmarkView::access( $uri );                            // the current user's access, as a request
if ( $uri !== false && $check['result'] )
    expBookmarkView::createNew( eZUser::currentUserID(), $uri, 'Upgrade check', $folderID );
```

## Kinds, settings and reasons

Added with the saved searches and the greyed bookmarks:

| Where | What |
|---|---|
| `settings/bookmark.ini` | New: `[ViewBookmarkSettings] NonBookmarkableViews[]`, `QueryParameters[]`, `ModuleIcons[]`, `DefaultIcon`; `[BookmarkTypeSettings] TypeList[]`, `TypeClass[]` |
| Module definitions | A view entry of `$ViewList` may carry `'bookmarkable' => false` (`user/logout` does) |
| `expBookmarkType` (abstract), `expBookmarkTypeSystemPage`, `expBookmarkTypeSearch` | The kinds: `identifier()`, `accepts( $module, $view )`, `queryParameters()`, `icon( $uri )`, `label()` |
| `expBookmarkView` | New: `bookmarkable( $uri )` (`result`, `reason`), `queryOf()`, `pathOf()`, `canonicalQuery( $module, $view, $query )`, `configuredQueryParameters()`, `moduleIcon()`, `types()`, `typeFor()`, `reasonText( $reason )`. `moduleURI()` keeps the query string keys of the view's kind (it dropped every query string before); `parts()` ignores the query string; `pageState()` takes the request's query string as a third argument (default `$_SERVER['QUERY_STRING']`) and is not available on a view that cannot be bookmarked; `handleAction()` refuses such a view |
| `expBookmarkView::decorateRows()` | A system page row also has `reason`, `reason_text`, `bookmark_type`, `type_label` and `icon`; a view that may no longer be bookmarked has `can_open` false with the reason `not-bookmarkable`. The row's `type` stays `bookmark` |
| `expBookmarkPage::viewItem()` | Items also have `icon`, `reason` and `reason_text`; the type (`class_name`) is the label of the row's kind when it has one |
| Templates | `bookmark_tree.tpl` and the `bookmark.tpl` cards draw `a4-bi-<icon>` instead of the gear and show `reason_text`; `design/admin3/templates/pagelayout.tpl` includes the button too |

A stored address can now end in a query string (`content/search?SearchText=news`); code that reads `module_uri`
directly and splits it on `/` should use `expBookmarkView::parts()` or `pathOf()`.

## Tests

- `tests/tests/kernel/classes/bookmarks/expBookmarkViewTest.php`: addresses, query strings, names, items, search and order, the excluded views, the kinds (an extension's too), the icons and the reasons (no database)
- `tests/tests/kernel/classes/bookmarks/BookmarkViewSchemaSqlTest.php`: the table everywhere it is written; the SQLite update twice
- `tests/tests/kernel/classes/bookmarks/BookmarkViewsTest.php`: the model on a live installation (skipped without one or without the table)
