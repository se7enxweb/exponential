# The locations tab: paging and sorting

The **Locations** tab of the item view lists every place a piece of content is
put. It used to list all of them, on one screen, with no way to order them.

That is fine for the three or four locations most items have. It is not fine
for content that is reused: a landing page placed under every product, a shared
form, a piece of boilerplate. Those objects reach thousands of locations, and
at that size the tab did not merely get long — it did not render. The template
fetched the entire assignment list, then asked the database for the ancestor
path of every row to print it, then asked again for the sub item count of every
row. Fifty thousand locations meant a hundred thousand queries before the first
byte, and the request died.

The tab now fetches one page and sorts in the database.

---

## Using it

### Paging

The page size is `content.ini`:

```ini
[LocationsSettings]
LocationsPerPage=25
```

Override it per siteaccess like any other setting. The pager appears only when
there is more than one page, and it is the same pager the rest of the
administration interface uses.

The position is carried on the address as `(location_offset)`:

```
/content/view/full/133/(location_offset)/25/(tab)/locations
```

It is deliberately **not** `(offset)`. The item view already pages its sub
items list with `(offset)`, and sharing the name would have meant that turning
to the second page of locations also turned the sub items list, and the
reverse.

### Sorting

Every column heading except the selection checkbox is a link. Following one
sorts the whole list — not the twenty five rows on screen — and returns to the
first page, because page eleven of the old order says nothing about the new
one. Following the heading that is already sorted reverses it. The sorted
heading is marked with the administration interface's own arrow.

| Heading | Sorts on |
|---|---|
| Location | the location's path, alphabetically |
| Sub items | how many children each location has |
| Visibility | visible, then hidden by a parent, then hidden |
| Main | the main location first when descending |

The order is carried as `(location_sort)` and `(location_sort_order)`:

```
/content/view/full/133/(location_sort)/children/(location_sort_order)/desc/(tab)/locations
```

Sorting and paging combine: the pager keeps the sort, and the headings keep
everything else on the address, the tab included.

With no sort asked for, the list is in tree order exactly as it always was.

---

## What changed

### `eZContentObject::assignedNodes()`

```php
function assignedNodes( $asObject = true, $checkVisibility = false, $offset = false, $limit = false,
                        $sortField = false, $sortOrder = 'asc' )
```

The four existing arguments behave as before, and every existing caller — there
are around twenty in the kernel — passes at most two, so all of them keep the
whole list in tree order.

`$offset` and `$limit` reach the query rather than the result. Slicing
afterwards would have kept the fetch, which is the expensive half.

`$sortField` is a key of `eZContentObject::sortColumnsForAssignedNodes()`:

```php
static function sortColumnsForAssignedNodes()
{
    return array(
        'path'       => 'ezcontentobject_tree.path_identification_string',
        'children'   => 'sort_children_count',
        'visibility' => 'ezcontentobject_tree.is_invisible, ezcontentobject_tree.is_hidden',
        'main'       => 'sort_is_main',
    );
}
```

Anything that is not a key of that table is ignored and the list comes back in
tree order. That is what allows the template to hand a view parameter straight
through: the value arrives from the address bar, and no part of it is ever
concatenated into sql. `$sortOrder` is likewise reduced to one of two literals.

Two of the four columns are not stored. `children` needs a count of each
location's children and `main` needs a comparison against the main node, so
they are added to the select **only when they are the column being sorted on** —
counting the children of fifty thousand locations is not something an ordinary
listing should pay for.

Every sort ends with `path_string`. Without a final unique key, a sort on a
column where most rows are equal — every location visible, one location main —
leaves the order of the equal rows to the database, and it is free to choose
differently on each query. Paging through such a list drops some rows and
repeats others.

The mongo branch does the same work: the same window, the same four sorts, with
`sort_is_main` and `sort_children_count` built in the pipeline.

### `eZContentObject::assignedNodeCount()`

New. One `COUNT(*)`, because the pager needs the total and the page does not
contain it.

The template uses it for the "is there more than one location here" tests as
well — those used to count the fetched array, which is now a single page, and
would have said "only one location" on the last page of a long list and
disabled the controls.

### Fetch functions

```
{fetch( 'content', 'assigned_nodes',
        hash( 'object_id', 74, 'offset', 0, 'limit', 25,
              'sort_field', 'children', 'sort_order', 'desc' ) )}

{fetch( 'content', 'assigned_node_count', hash( 'object_id', 74 ) )}
```

`offset`, `limit`, `sort_field` and `sort_order` are all optional.

### `navigator/google.tpl`

The shared pager gained one parameter:

```
{include uri='design:navigator/google.tpl'
         offset_name='location_offset'
         page_uri=concat( '/content/view/full/', $node.node_id )
         item_count=$assignment_count
         view_parameters=$view_parameters
         item_limit=$locations_limit}
```

`offset_name` defaults to `'offset'`, so the twenty or so templates that
include this pager are untouched. It controls both the parameter the pager
writes and the one it leaves out when it copies the other view parameters
forward — which is what lets one page carry two independent pagers.

No `page_uri_suffix` is given: `view_parameters` already carries
`(tab)/locations`, and the pager appends every parameter except its own offset,
so a suffix would put the tab in the address twice.

### The heading itself

The headings are `design/standard/templates/parts/sortheader.tpl`, the same
component the RSS list uses, so the two pages look and behave alike rather than
each growing its own. It takes the column key, the label, the current sort, and
a `suffix` appended to the address as it is — which is how the locations tab
keeps `(tab)/locations` on every heading link.

```
{include uri='design:parts/sortheader.tpl'
         key='children'
         label='Sub items'|i18n( 'design/admin/node/view/full' )
         sort=$locations_sort_state
         page_uri=$locations_sort_uri
         sort_name='location_sort'
         dir_name='location_sort_order'
         suffix=$locations_carried_parameters
         cell_class='tight'}
```

`sort` is `hash( 'field', …, 'direction', 'asc'|'desc', 'opposite', … )`.

The heading is a plain link, so it works with JavaScript switched off.

The rules for `th.sortable` / `th.sorted` / `.sort-arrow` live in
`design/admin/stylesheets/content.css`, which every administration page loads,
so the two lists cannot drift apart. The column being sorted keeps the ordinary
heading colour and is marked by the bold label and the arrow — not by
repainting the cell.

---

## Files

| File | Change |
|---|---|
| `kernel/classes/ezcontentobject.php` | `assignedNodes()` window and sort, `assignedNodeCount()`, `sortColumnsForAssignedNodes()` |
| `kernel/content/function_definition.php` | `assigned_nodes`, `assigned_node_count` |
| `kernel/content/ezcontentfunctioncollection.php` | `fetchAssignedNodes()`, `fetchAssignedNodeCount()` |
| `design/admin/templates/locations.tpl` | one page, sortable headings, pager |
| `design/admin/templates/navigator/google.tpl` | `offset_name` |
| `design/standard/templates/parts/sortheader.tpl` | the shared sortable heading, moved here from `rss/` |
| `design/admin/templates/rss/list.tpl` | includes it from its new place; its heading rules moved to the stylesheet |
| `design/admin/stylesheets/content.css` | `th.sortable`, `th.sorted`, `.sort-arrow` |
| `settings/content.ini` | `[LocationsSettings] LocationsPerPage` |

---

## The tab label counted the whole list

The paging made the *list* cheap and left the *label* above it expensive. Both
`design/admin/templates/window_controls.tpl` and the `admin3` copy wrote the
Locations tab like this:

```
{'Locations (%count)'|i18n( ..., hash( '%count', $node.object.assigned_nodes|count ) )}
```

`assigned_nodes` is `assignedNodes()` with no limit. So every view of every node
loaded every location of that object to put a number on a tab — whichever tab
was open, including the ones that have nothing to do with locations. On an
object with fifty thousand locations that is the whole cost the paging exists
to avoid, paid on every page of the administration interface that shows it.

It is `fetch( 'content', 'assigned_node_count', ... )` now: one `COUNT(*)`.

The Policies tab beside it had the same shape and worse:

```
{def $assigned_policies = fetch( 'user', 'user_role', hash( 'user_id', ... ) )}
```

`user_role` merges `accessArray()` from every role the user holds and builds
their entire permission set in PHP — asked for here only to count it. It is now
the sum of `policy_count` per assigned role, which is one small query per role
and no policy rows at all.

---

## Tests

`ai/bin/one/test_locations_pagination.py` drives the tab in a browser:

```
NODE=133 LIMIT=25 EZ_ADMIN_PASSWORD=... python3 ai/bin/one/test_locations_pagination.py
```

Point `NODE` at an item with more locations than one page holds; with fewer the
run proves nothing and says so. It checks that one page is drawn and not the
whole list, that the pager appears and uses its own offset, that the second
page holds different rows, that each column sorts both ways, marks itself and draws the same as the RSS
list,
that a sort value that is not a column is ignored rather than run, that the
pager keeps the sort and the headings keep the tab, and — because the shared
pager was changed — that an ordinary list elsewhere still pages on `(offset)`.

Measured against an item with 2001 locations: the tab renders in about a
second. Before this it did not render.
