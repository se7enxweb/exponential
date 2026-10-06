# Bookmark folders

Bookmarks are no longer one flat list. A user can organise them in a tree of virtual folders of any depth, and every
bookmark stays one click away: the tree is open by default, shown in the sidebar, in the browse dialog and in the
online editor.

## Where it is

| Place | What you get |
|---|---|
| My bookmarks (`content/bookmark`) | An overview, the folders with their counts beside the list, the bookmarks grouped by folder with their class, location, last change and whether they are hidden or gone; search, five orders and paging; new, rename, move and remove folders (saying what happens to what is inside); move selected bookmarks (select, or drag onto a folder), order within a folder, remove selected after a confirmation. See the [bookmarks guide](../../guides/bookmarks.md) |
| Bookmarks box (right column) | The whole tree, folders open and close (remembered in the browser, shared with the page), "Add to bookmarks" with a folder to choose (top level by default) |
| Browse dialog | The tree of bookmarks next to the content; a container in a folder opens with one click |
| Online editor, TinyMCE 3 and 8 | The Bookmarks tab shows a heading row per folder, indented by depth |
| Remote services | `expbookmark::tree`, `rows`, `folders`, `bookmarks`, `createFolder`, `renameFolder`, `deleteFolder`, `moveFolder`, `moveBookmark`, `place`, `addBookmark`, `removeBookmark` |

Designs: admin4 (and admin4l, which uses its templates), admin (and admin2, admin3, which fall back to it) and standard.

## Using it

The [bookmarks guide](../../guides/bookmarks.md) walks through the page step by step. In short:

- **New folder**: the *New folder* form beside the list; the folder shown is preselected as its place.
- **Move bookmarks**: tick them and use *Move the selected bookmarks to*, or drag them onto a folder of the folder list. Folders move with *Move* in the panel of the folder shown; a folder cannot go into itself or a folder below it, so those are not offered.
- **Remove a folder**: *Remove folder* asks whether what is inside moves to the folder it was in (the default, no bookmark removed) or is removed with it.
- **Search, order, pages**: the search covers names, classes, locations and folders; the orders are your own, name, recently added, type and recently modified; bookmarks stay grouped by folder.
- Without JavaScript the page works the same; the script adds *Select all* and dragging.

## Templates

Old templates keep working: `fetch( 'content', 'bookmarks' )` returns all bookmarks of the user, flat, newest first, as before.
New fetch functions:

```
{def $rows    = fetch( 'content', 'bookmark_rows',    hash() )}   {* the tree, flat, in display order *}
{def $folders = fetch( 'content', 'bookmark_folders', hash() )}   {* the folders, flat, with depth *}
```

A row is `type` (`folder` or `bookmark`), `depth`, `id`, `name`, `path` (folder names above it), and for a folder `parent_id` and `count`
(bookmarks inside, subfolders included), for a bookmark `folder_id` and `bookmark` (the `eZContentBrowseBookmark`). `bookmark_rows` takes
`folder_id` to list only the content of one folder. `design:content/bookmark_tree.tpl` renders a tree (`mode='page'`, `'sidebar'` or `'browse'`).

## PHP

`eZContentBrowseBookmarkFolder` (`kernel/classes/ezcontentbrowsebookmarkfolder.php`): `createNew`, `fetch`, `fetchForUser`, `fetchListForUser`,
`fetchChildren`, `fetchTreeForUser` (nested, two queries), `fetchRowsForUser`, `rename`, `moveFolder`, `moveBookmark`, `place`, `reorder`,
`removeFolder( $deleteBookmarks )`, `isInside`, `subtreeIDs`, `handleAction`. `eZContentBrowseBookmark` gained `folder_id`, `priority`,
`fetchListForUserInFolder`, `folder()` and an optional folder for `createNew( $userID, $nodeID, $name, $folderID )`.

## Online editor

`ezoe::bookmarks::offset::limit` is unchanged. With a third argument `tree` it answers with the bookmarks in the order of the tree,
each with `folder_id` and `folder_path`, and `folders` (id, name, parent_id, depth, count).

## Tests

`tests/tests/kernel/classes/bookmarks/BookmarkFoldersTest.php` (model and upgrade SQL), `tests/tests/extension/expservices/users/expBookmarkServicesTest.php`
(services). Both are live style (rows named BMTEST, removed again) and skip without an installation.
