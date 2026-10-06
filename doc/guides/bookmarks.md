# Bookmarks: finding your way back, in folders

This guide explains the **My bookmarks** page of the administration (`/content/bookmark`, Dashboard > My bookmarks):
what a bookmark is, the overview, the folders, finding, ordering and paging bookmarks, moving them between folders,
renaming and removing folders, and what to do when something does not behave as expected.

It is written for editors and administrators. Every page, field and rule below was checked against the code of this
repository on 6 October 2026; the files are named in [References](#references).

[Guides](README.md) · Related: [Bookmark folders (feature)](../features/6.0/bookmark-folders.md) ·
[Bookmark folders (upgrade)](../bc/6.0/bookmark-folders.md) · [List paging](../features/6.0/admin-list-paging.md)

## In short

- A **bookmark** points to an item of the content tree. It is personal: nobody else sees your bookmarks, and
  removing one never changes the item.
- Bookmarks can sit in **folders** of any depth. The page, the Bookmarks box at the side of every admin page and the
  browse dialog show the same folders.
- The page shows an overview, the folders with their counts beside the list, and one card per bookmark, **grouped
  by folder**, with its type, where it is in the tree, when it was last modified and whether it is hidden or no
  longer there.
- Search, order and paging are kept in the address, so a link to the page shows the same list.
- Every action works without JavaScript. With JavaScript you can also tick **Select all** and drag bookmarks onto a
  folder.

## 1. The page

Open **Dashboard > My bookmarks**. From top to bottom:

| Part | What it shows |
|---|---|
| Figures | Your bookmarks, your folders, the bookmarks in no folder, the ones whose item is hidden, and the ones that are **no longer available** (red when there are any). Bookmarks and Not in a folder are links to those lists |
| Folders | All bookmarks, Not in a folder, then every folder indented by depth, each with the number of bookmarks in it and in the folders below it. The folder shown is marked |
| New folder | A name and the folder to put it in (the folder shown is preselected) |
| The folder shown | When you open a folder: its path, how many bookmarks it holds directly and with its subfolders, how many folders are below it, and Rename, Move and Remove folder (section 4) |
| Find a bookmark, Order | The search and the five orders (section 2) |
| The list | One card per bookmark, under a heading per folder with its size; "continued from the previous page" when a folder runs over two pages |
| Per page and pages | 25, 50 or 100 per page and the pager |
| The bar under the list | Move the selected bookmarks to a folder, Remove selected (asks first) |

Each card shows:

- a box to select it, the icon of the item's class, its name (a link to the item) and its class;
- **Hidden** when the item is hidden, **Hidden by a parent** when an item above it is hidden: visitors of the site
  do not see it;
- **Not found** when the item was removed, is in the trash, or is not in a language of this site, and **No access**
  when you may no longer read it. Such a card says what to do and shows nothing else of the item;
- the **location** (the names of the items above it) and when the item was last **modified**;
- **View**, **Edit** (when you may edit it) and, in your own order, **Move up** and **Move down**.

## 2. Finding and ordering

**Find a bookmark** searches the names, classes, locations and folder names of your bookmarks, without regard to
upper and lower case. On *All bookmarks* it searches everything; inside a folder it searches that folder and the
folders below it. **Clear search** removes the search; when nothing matches the page says so and offers to search
all bookmarks.

The orders:

| Order | Means |
|---|---|
| Your order | The order of the Bookmarks box, which you set with Move up and Move down |
| Name A to Z | Natural order, so "Page 2" comes before "Page 10" |
| Recently added | The newest bookmark first (bookmarks keep no date: a later bookmark has a higher number) |
| Type | By class name, then by name; bookmarks that are no longer available last |
| Recently modified | The item changed most recently first |

In every order the bookmarks stay **grouped by folder**: the ones in no folder first, then the folders in the order
of the folder list. Move up and Move down are offered in your own order without a search, so a button never moves a
bookmark you cannot see next to.

The page size comes from `admininterface.ini [PaginationSettings] ItemsPerPageList_content_bookmark[]` (25, 50 and
100 when the setting is not there); your choice is kept as the preference `admin_bookmark_list_limit`.

## 3. Adding and moving bookmarks

- **Add items** (above the list, or in an empty folder) opens the browse dialog. The items you choose are added to
  the folder shown; on *All bookmarks* a new bookmark goes to no folder and an item you had bookmarked already keeps
  its folder. Items you may not read are not added. The Bookmarks box and the context menu of the content tree add
  bookmarks too.
- **Move**: tick the bookmarks, choose the folder under **Move the selected bookmarks to** and press
  **Move selected**. *No folder (top level)* takes them out of every folder.
- With a mouse you can also **drag** a bookmark onto a folder in the folder list. If the bookmark is ticked, all
  ticked bookmarks move with it.
- **Remove selected** opens a short confirmation first. Only the bookmarks are removed.

## 4. Folders

Open a folder in the folder list. Its panel offers:

- **Rename**: the new name; tags are removed and names are cut at 255 characters.
- **Move**: the folder to put it in. The folder itself and the folders below it are not offered: a folder cannot go
  inside itself. *Move up* and *Move down* change its place among its neighbours.
- **Remove folder** says first what happens to what is inside:
  - **Keep what is inside** (the default): its bookmarks and folders move to the folder it was in (or to the top
    level), and no bookmark is removed;
  - **Remove everything inside with it**: the folder, every folder below it and all their bookmarks are removed.

  In both cases the items the bookmarks point to are not changed. After the removal the page shows the folder it was
  in.

## 5. Problems and answers

| You see | Why, and what to do |
|---|---|
| A card says **Not found** | The item was removed or moved to the trash, or it exists only in a language this siteaccess does not show. Remove the bookmark, or restore the item from the trash |
| A card says **No access** | A role was changed and you may no longer read the item. Remove the bookmark, or ask an administrator |
| **Move up** and **Move down** are missing | They are shown in *Your order* without a search only. Choose *Your order* and clear the search |
| "Nothing was moved" after Move selected | No bookmark was ticked, or the folder chosen is not one of yours |
| A link to someone else's folder shows all your bookmarks | Folders are personal: an address with a folder that is not yours shows your own bookmarks |
| The page is unchanged after a template change | Clear the template, template-override, content and template-block caches, and Velocity's response cache |

## 6. For developers

- The view is `kernel/private/classes/views/content/bookmark.php`. It reads the folders and bookmarks
  (`eZContentBrowseBookmarkFolder::fetchRowsForUser()`), the nodes of all bookmarks in one query and the names of
  the items above them in a second, and hands plain arrays to `expBookmarkPage`.
- `expBookmarkPage` (`kernel/classes/expbookmarkpage.php`) works out the folders and their counts, the scope of
  `(folder)`, the search, the orders, the grouping, the paging, the figures, the Move up and down targets and the
  address. It reads no database and is tested without one.
- `eZContentBrowseBookmarkFolder::shift()` moves a folder or a bookmark one place up or down within its folder.
- Addresses: `content/bookmark/(folder)/<id>|top/(sort)/name|added|type|modified/(offset)/<n>?q=<text>`. Every POST
  answers with a redirect to the same folder, order and search, with the result as a notice.
- The POST names are the ones the page always had: `RemoveButton`, `AddButton`, `DeleteIDArray[]`,
  `MoveSelectedButton` with `FolderID`, and `BookmarkFolderAction` (create, rename, delete, move_bookmark,
  move_folder, place, reorder) with `FolderName`, `ParentFolderID`, `FolderID`, `DeleteBookmarks`, `BookmarkID`,
  `BookmarkIDArray[]`. New: `BookmarkShiftButton` (`up-<id>`, `down-<id>`, `fup-<id>`, `fdown-<id>`).
- Only the user's own bookmarks and folders are read or changed; another user's ids are refused. Every form carries
  the form token. `NeedRedirectBack` with `RedirectURI` goes only to a page of this site
  ([safe redirects](../features/6.0/safe-redirects.md)).
- The template variables `view_parameters` and `bookmark_notice` are kept; the new `bookmark_page` holds everything
  else. The tree of the Bookmarks box and the browse dialog (`content/bookmark_tree.tpl`) is unchanged.
- The look is page-local in `content/bookmark_exp_style.tpl` (design/admin and design/admin4), in the visual
  language of the section, link list and session pages.

Tests, no database: `php vendor/bin/phpunit tests/tests/kernel/classes/bookmarks/expBookmarkPageTest.php`.
With an installation: `php vendor/bin/phpunit tests/tests/kernel/classes/bookmarks/BookmarkFoldersTest.php --filter testShift`.

## References

- `kernel/private/classes/views/content/bookmark.php`, `kernel/classes/expbookmarkpage.php`,
  `kernel/classes/ezcontentbrowsebookmarkfolder.php`, `kernel/classes/ezcontentbrowsebookmark.php`
- `design/admin4/templates/content/bookmark.tpl`, `design/admin4/templates/content/bookmark_exp_style.tpl` (the
  same in `design/admin`)
- `tests/tests/kernel/classes/bookmarks/expBookmarkPageTest.php`, `tests/tests/kernel/classes/bookmarks/BookmarkFoldersTest.php`
- [Changelog 6.0.15](../changelogs/6.0/6.0.15.md)
