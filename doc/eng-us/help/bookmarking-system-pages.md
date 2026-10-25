---
title: Bookmarking system pages
audience: editor, admin
category: administration
order: 30
summary: Keep the pages of the administration you open every day, such as Setup, the help, the audit trail or a filtered list, in your bookmarks next to your content, in the same folders.
keywords: bookmark, bookmarks, bookmark this page, bookmarked, system page, module view, setup, folders, rename bookmark, favorite, favourite, shortcut
context: content/bookmark
type: help
---
# Bookmarking system pages

Bookmarks used to point only to content: an article, a folder, an image. Since Exponential 6.0.15 you can also
bookmark the **pages of the administration itself**, the system pages: System information, the cache page, the help
dashboard, the audit trail filtered to one user, a list of drafts, your own bookmark page opened at one folder. They
sit in the same folders as your content bookmarks, in the Bookmarks box at the side of every page and on the
**My bookmarks** page.

This page shows, step by step, how to bookmark a system page, how to find it again, and how to rename, move and remove
it. It is for everybody who works in the administration; you need the right to use bookmarks (the policy
*content/bookmark*, which editors and administrators have).

[Help](README.md) · Related: [the bookmarks guide](../guides/bookmarks.md) ·
[Bookmarks of system pages (feature)](../features/6.0/bookmarks-of-system-pages.md) ·
[A tour of the administration](admin-tour.md)

## In short

- On every system page there is a **Bookmark this page** button above the page, on the right of the breadcrumb, and
  the same button in the **Bookmarks** box of the right column.
- The button opens a small form: the **name** (the page's title, which you can change) and the **folder**. Choose
  **Add bookmark** and you are back on the page; the button now says **Bookmarked**.
- The bookmark keeps the page's address with its parameters (`/content/bookmark/(folder)/top`, `/audit/console/(user)/14`),
  but not the siteaccess: it opens in every administration of the installation.
- System page bookmarks have a **gear icon**; content bookmarks keep the icon of their class.
- A system page you may no longer open (your rights changed, or the page no longer exists) is shown **without a
  link**, marked **No access**. You can still rename, move or remove it.

## 1. Bookmark a page

1. Open the page you want to come back to, for example **Setup > System information**.
2. Look at the top of the page, on the right of the breadcrumb: the **Bookmark this page** button.

   ![The top of the System information page with the Bookmark this page button on the right](images/bookmarks/bookmark-this-page-button.webp)

3. Choose **Bookmark this page**. A small form opens under the button:

   ![The open form under the button: Name, Folder, the address of the page, Add bookmark and My bookmarks](images/bookmarks/bookmark-this-page-form.webp)

   - **Name**: filled in with the page's title, as the browser tab shows it. Change it to whatever helps you, for
     example *System information (server)*.
   - **Folder**: *No folder (top level)*, or one of your bookmark folders. Folders are made on the My bookmarks page
     (see [the bookmarks guide](../guides/bookmarks.md#5-folders)).
   - Under the fields the form shows the **address** the bookmark keeps.
4. Choose **Add bookmark**. The page reloads and the button now reads **Bookmarked**:

   ![The same page after adding: the button reads Bookmarked and is highlighted](images/bookmarks/bookmarked-button.webp)

The form works without JavaScript; it is a part of the page that opens and closes. Choose the button again to close
it without saving.

### From the Bookmarks box

The **Bookmarks** box in the right column offers the same button on every system page, under your bookmarks. It
opens the same form. On a content page the box keeps its **Add to bookmarks** button for the item, as before.

### Change the name or the folder later

Open the page again and choose **Bookmarked**. The form shows the saved name and folder; change them and choose
**Save bookmark**. A page is bookmarked only once: saving again never makes a second bookmark of the same address.

## 2. Find it again

The **Bookmarks** box lists your bookmarks in their folders, on every page. System pages have a gear icon:

![The Bookmarks box with the folder Administration holding three system pages, one of them greyed out, and Help at the top level](images/bookmarks/bookmarks-box-system-pages.webp)

Choose the name to open the page. Content bookmarks and system pages are listed together, in your order:

![The Bookmarks box with system pages in a folder and a content bookmark (Websites) with its class icon](images/bookmarks/bookmarks-box-both-kinds.webp)

On **My bookmarks** (Dashboard > My bookmarks) the overview counts your **System pages**, and a line above it says how
to add one:

![The overview of My bookmarks: the hint about system pages, then the figures Bookmarks, Folders, System pages, Not in a folder, Hidden items and No longer available](images/bookmarks/bookmark-page-overview.webp)

Each system page has a card like a content bookmark. It shows the gear icon, the name, the label **System page**, the
**address** and the **module and view** the page belongs to, **Open**, **Move up** and **Move down**, and **Rename**:

![A folder on My bookmarks with three system page cards: System information (server), My bookmarks and a card marked No access](images/bookmarks/bookmark-page-system-page-card.webp)

**Find a bookmark** on that page also searches the addresses, so `systemupgrade` or `audit/` finds the system pages
with that address. In the order **Type**, system pages are sorted under *System page*.

## 3. Rename, move, remove

**Rename** (system pages only):

1. On **My bookmarks**, find the card and choose **Rename** under its address.
2. Type the new name and choose **Save name** (or press Enter in the field).

   ![The card of the Help bookmark with Rename open, the new name Help for my team typed in and Save name](images/bookmarks/bookmark-page-rename.webp)

3. The page says *The bookmark was renamed to "…"*. The Bookmarks box shows the new name at once.

A content bookmark always shows the current name of its item, so it has no Rename.

**Move** to another folder: tick the card and use **Move the selected bookmarks to** under the list, or drag the
card onto a folder of the folder list; on the page itself you can also choose **Bookmarked** and pick another folder.
**Move up**, **Move down**, the grip and the position field arrange system pages and content bookmarks together, in
one order per folder.

**Remove**: tick the card, choose **Remove selected** and confirm. Only the bookmark is removed, never the page. A
removed folder takes its system page bookmarks with it only when you choose *Remove everything inside with it*.

## 4. A page you can no longer open

Whether you may open a system page is checked every time your bookmarks are shown, with the same rules as when you
open the page: your roles and policies, the siteaccess rules, and whether the module and view still exist. When you
may not open it any more, the bookmark is not a link: the Bookmarks box shows it greyed out, and its card on
My bookmarks says **No access** and what to do:

![A card marked System page and No access: You may no longer open this page. Remove the bookmark, or ask an administrator for access.](images/bookmarks/bookmark-page-no-access.webp)

Remove the bookmark, or ask an administrator for the access you need. When your access comes back, the link comes back
by itself. The overview counts such bookmarks under **No longer available**.

## 5. Questions and answers

**I do not see Bookmark this page.** It is shown on the pages of a module view only, not on a content item (there you
use *Add to bookmarks*), not while you edit or browse, and not on an error page. It is missing everywhere when you
may not use bookmarks (ask for the policy *content/bookmark*) or when the database update of Exponential 6.0.15 has
not been run on your installation (your administrator runs `update/common/scripts/6.0/createbookmarkviewtable.php`;
see [Bookmarks of system pages](../features/6.0/bookmarks-of-system-pages.md#installing-and-updating)).

**The bookmark opens the page without my filter.** The bookmark keeps the parameters that are part of the address,
such as `/(folder)/top` or `/(user)/14`. A filter that the page keeps only in a form or after a `?` in the address is
not part of the bookmark. Most lists of the administration keep their filters in the address; open the filtered list
and bookmark it from there.

**I bookmarked the page in another administration.** Bookmarks keep the address without the siteaccess, so a
bookmark made in one administration (for example the German one) opens the same page in the one you are working in.

**Who sees my bookmarks?** Nobody else. Bookmarks are personal, for system pages as for content.

**The online editor's bookmark list does not show my system pages.** It lists content you can link to or embed;
system pages are not content.
