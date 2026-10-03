# Custom items per page

The Table options panel of the sub items list used to offer a fixed set of page
sizes (10, 25, 50, 100, 200, 500). Since June 2026 there is also a **Custom**
field: type any number from 1 to 10000 and the list shows exactly that many
rows.

## Use it

1. Open a node with sub items and click **Table options**.
2. Under items per page choose **Custom**, type a number and press Enter (or
   leave the field). The list reloads at once.
3. A value that is not a whole number between 1 and 10000 is refused with a
   message and the previous size stays.

## When it helps

- Bulk work on exactly the rows you need on screen (select all, then move, copy
  or hide them).
- Export and import checks that need a precise slice.
- Testing how a folder behaves at a given size.

## Where the sizes are defined

The preset sizes and the stored preference are described in
[pagination settings](../../bc/6.0/pagination-settings.md) and
[sub items table options](subitems-table-options.md); read those
before changing defaults, because the list's page size is stored as a user
preference. The June 2026 change added the custom input to the table options
template (`design/admin/templates/children_detailed.tpl`, label **Custom**), a
handler to the sub items script of the admin design and a layout rule to the
design's container stylesheet (the files of the YUI era; later work moved the
list to jQuery, see [yui removal](../../bc/6.0/yui-removal.md), and renamed
them).
