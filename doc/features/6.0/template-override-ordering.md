# Design > Templates: drag and drop override order

A template can have many overrides, and the order in which they are tried
decides which one shows. **Design > Templates** (`/visual/templateview/...`)
now lists a template's overrides as cards in exactly that order and lets you
reorder them by drag and drop. Every move is saved at once.

## Use it

1. Open **Design > Templates**, choose a template such as `node/view/full.tpl`.
2. The overrides appear as cards in the order they are tried. Each card shows its
   position, whether the siteaccess settings or an extension define it, its file
   and its conditions.
3. Drag a card by its handle to a new place, or move it with its arrows
   (keyboard and touch work). The new order is saved immediately as the
   `Priority` (10, 20, 30 ...) of this template's overrides.
4. Use the filter box to narrow the cards on the page ("filters this page").
5. **Remove selected**, **New override** and **Save conditions** are drawn above
   the list as well as below it, so a long list needs no scrolling.

### Paging

The overrides are shown 20 per page with the pager above and below, and
positions counted over the whole list. Moving stays instant: a drag or an arrow
within a page sends that page's new order and the server puts it in the page's
place in the full order. The up arrow of a page's first override and the down
arrow of its last move it across to the page before or after, swapping it with
the override there, and the page is loaded again. The page keeps its offset when
a form is posted.

| File | Block | Key | Default |
|---|---|---|---|
| `settings/admininterface.ini` | `[PaginationSettings]` | `ItemsPerPage[visual/templateview]` | 20 |

## What changed underneath, and why it matters

Saving is done by `ezpTemplateOverrides`, which changes only
`settings/siteaccess/<siteaccess>/override.ini.append.php`. It is read from disk,
a copy is kept first and the file is checked afterwards. The old page was
dangerous:

| Before | Now |
|---|---|
| Update wrote a `Priority` into every override of the file, `0` for all those the page did not show. | Writes only the conditions that changed, of the overrides the page showed. |
| Update saved `override.ini` merged from every source into the siteaccess file, copying extensions' overrides into it. | The file only gains `Priority` lines (and the one condition you edited). |
| Remove took any override and deleted its file wherever it was. | Removes only overrides the siteaccess file defines, and their files only inside `design/`. An extension's override is left to the extension, with a notice. |
| Reset markers were written in front of the conditions, discarding conditions an extension gives the same override. | The file is written without reset markers. |
| A reorder of any content was accepted. | A reorder holding anything but exactly the overrides shown is refused, and after a save the order in effect is read back and compared. |
| Any siteaccess could be named. | The siteaccess to change must be one of `RelatedSiteAccessList`. |

Verified against a fresh installation with 56 overrides of `node/view/full.tpl`:
moves within page 1, a move from page 2 to page 1 and back, and saving a
condition on a later page each left the settings file with only `Priority` lines
or the one changed condition; changing a condition and changing it back restored
the file byte for byte.

## Related pages

- [Template editor overrides](template-editor-overrides.md)
- [Extension loading order](extension-loading-order.md)
- [Admin list paging](admin-list-paging.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
