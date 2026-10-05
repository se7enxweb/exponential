# Bookmark folders

Bookmarks are no longer one flat list. A user can organise them in a tree of virtual folders of any depth, and every
bookmark stays one click away: the tree is open by default, shown in the sidebar, in the browse dialog and in the
online editor.

## Where it is

| Place | What you get |
|---|---|
| My bookmarks (`content/bookmark`) | The tree with counts, expand/collapse all, search over all bookmarks, new, rename, delete and move folders, move bookmarks, order within a folder (buttons and drag and drop), remove selected, move selected to a folder |
| Bookmarks box (right column) | The whole tree, folders open and close (remembered in the browser, shared with the page), "Add to bookmarks" with a folder to choose (top level by default) |
| Browse dialog | The tree of bookmarks next to the content; a container in a folder opens with one click |
| Online editor, TinyMCE 3 and 8 | The Bookmarks tab shows a heading row per folder, indented by depth |
| Remote services | `expbookmark::tree`, `rows`, `folders`, `bookmarks`, `createFolder`, `renameFolder`, `deleteFolder`, `moveFolder`, `moveBookmark`, `place`, `addBookmark`, `removeBookmark` |

Designs: admin4 (and admin4l, which uses its templates), admin (and admin2, admin3, which fall back to it) and standard.

## Using it

- **New folder**: the *New folder* button on the page, or the *New folder* button on a folder row for a subfolder.
- **Move**: drag a bookmark or folder onto a folder (into it, last), onto another entry (in front of it), or onto the *Top level* strip. Without a mouse use the *Move* button of a row, or the up and down buttons. A folder cannot go into itself or into a folder below it; those targets are disabled.
- **Delete a folder**: its bookmarks and subfolders move up one level and no bookmark is deleted. Tick *Also delete the bookmarks and folders inside* to delete everything in it.
- **Search**: the box above the tree filters all bookmarks and opens the folders that hold a match.
- Without JavaScript the page still works: the tree is open, and the forms under *Folders* create, rename and delete folders and the *Move selected to* box moves bookmarks.

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
