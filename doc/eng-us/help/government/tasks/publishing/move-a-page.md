---
title: Move a page
audience: government, editor
category: public-sector
order: 170
summary: For editors - move a page and everything below it to another place in the content tree with Move, understand what happens to its address, menu place, section and permissions, and handle large moves.
keywords: move page, move, content tree, new parent, URL alias, redirect, section, permissions, subtree, content job, Move selected
type: help
---
# Move a page

This page is for editors who reorganize content. **Move** places an item, with everything below it, under a new
parent. The old address keeps working: it redirects to the new one ([Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What changes](#what-changes)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- You may create content in the new place and move the item (the **Move** button is grayed out otherwise, and says why).
- You know whether the page should move or appear in a second place as well ([Publish one item in several places](publish-one-item-in-several-places.md)).

## Steps

1. Open the page, press **Move**.
2. In the browser that opens, go to the new parent and select it; confirm.
3. For several pages: in the parent's **Sub items** list tick them, **More actions** > **Move selected**.

A move of a large subtree may be offered as a background job (from 50 nodes on, background is offered first); its
progress shows at `/content/job/<id>`, and all jobs are listed at `/content/jobs` ([Content model and editing, 8](../../../../guides/content-model-and-editing.md#8-large-operations-as-content-jobs)).

## What changes

| Thing | After the move |
|---|---|
| Address | a new URL alias from the new path; the old one redirects |
| Menu | it appears where the new parent puts it |
| Section | objects that had the old parent's section get the new parent's section; objects with a section of their own keep it ([Sections, 8.2](../../../../guides/sections.md#82-moving-and-locations)) |
| Who may read and edit | follows the new section and place |
| Relations and embeds | unchanged: they point to the object, not the address |

## Check the result

- [ ] The page opens at its new place; the old address redirects (open it signed out).
- [ ] On the **Details** tab, the section is what you expect; if visitors now get "access denied", the new section is
  not readable by them.
- [ ] Menus show the page in the new place.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Moving into a section visitors cannot read | check the target's section first |
| Moving a page that is also listed elsewhere through a second location | **Move** moves the location you are on; check the **Locations** tab |
| Breaking an editor's permissions | after the move, editors limited by subtree may no longer reach the page |

## Related pages

- [Publishing tasks](README.md)
- [Redirect old addresses after a reorganization](redirect-old-addresses-after-a-reorganization.md)
- [Creating and editing content](../../../editing-content.md#move-copy-and-remove)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
