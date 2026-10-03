# Template editor: create, order and edit overrides without touching INI files

A template override says "for this class, this node or this section, use this
template instead of the default". Exponential keeps the rules in `override.ini`.
The visual template editor, in the admin under **Design > Template Editor**
(`/visual/templatelist`), lets a designer create, reorder, change and remove
those rules and edit the template files. Between 12 July and 20 July 2026
(commit `12a2577f00` and follow ups) it was made safe to use: a rule can no
longer be lost or silently ignored.

## Open it

- Admin menu: **Design** tab, left menu entry **Template Editor** (setting
  `settings/menu.ini [Leftmenu_design]` `Links[template_editor]=visual/templatelist`,
  with the matching `PolicyList_template_editor[]`).
- In `menu.ini` the Setup menu's older `Links[templates]=visual/templatelist` lines are commented out ("Removed from 4.3"), so only the Design entry exists unless you enable them. Check with `grep -n templatelist settings/menu.ini`.
- A user needs the `visual/templatelist` policy (the *Design* module).

## What changed for you

### New overrides take effect at once

A new override is written with `Priority=0`, the highest. The template engine
evaluates overrides from the lowest `Priority` value up, and a rule without a
`Priority` sorts last. Before July the order shown on screen was not the order
the engine used; now it is (`kernel/common/eztemplatedesignresource.php`,
`overrideArray()`).

### Reorder with the priority list

On the override list, change the numbers and press **Update overrides**. The
numbers are saved as `Priority` in `override.ini`, and the stale override and
INI caches are cleared before and after, so the new order applies at once.

### Reorder by drag and drop (September 2026)

**Design > Templates** (`/visual/templateview/...`) lists a template's overrides as
cards in exactly the order they are tried and lets you reorder them by drag and
drop. Every move is saved at once.

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

Paging: the overrides are shown 20 per page with the pager above and below, and
positions counted over the whole list. A drag or an arrow within a page sends that
page's new order and the server puts it in the page's place in the full order. The
up arrow of a page's first override and the down arrow of its last move it across
to the page before or after, swapping it with the override there, and the page is
loaded again.

| File | Block | Key | Default |
|---|---|---|---|
| `settings/admininterface.ini` (set it in `settings/override/admininterface.ini.append.php`) | `[PaginationSettings]` | `ItemsPerPage[visual/templateview]` | 20 (built in; the key is not in the shipped file) |

See [Where the page sizes live](../../bc/6.0/pagination-settings.md).

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
moves within page 1, a move from page 2 to page 1 and back, and saving a condition
on a later page each left the settings file with only `Priority` lines or the one
changed condition; changing a condition and changing it back restored the file byte
for byte. See also [Extension loading order](extension-loading-order.md), which
decides which extension's overrides are found first, and
[Admin list paging](admin-list-paging.md).

### Choose where the copy starts

When you create an override with *Default copy*, the editor needs a template to
copy. It chooses in this order:

1. The template you pick in the **TemplateSource** drop down.
2. The class specific override that already exists for the class you match
   (for example `full/frontpage.tpl` for the class `frontpage`).
3. The real default template.

This replaces the old behaviour that could copy the wrong template.

### Match by a single object

The node view form has a new **Object ID** field before **Node ID**. Matching on
`object` is the most precise override there is, and it is evaluated before the
node condition. Use it to restyle one article without changing anything else.

### Edit and remove match conditions

Each override on the list shows its conditions as text fields (`class_identifier`,
`node`, `object`, `section`, ...). You can:

- change a value and press **Update overrides**;
- tick the box beside a condition to remove it (`RemoveMatchArray`);
- add a condition with the **Add condition** drop down. The keys offered are
  `class_identifier`, `class`, `node`, `object`, `section`, `section_identifier`,
  `remote_id`, `node_remote_id`, `parent_node`, `class_group`, `depth`,
  `url_alias`, `viewmode`, `navigation_part_identifier`, `persistent_variable`,
  `state`, `state_identifier`.

An empty value, or `-1`, removes the condition. Pressing Enter in a field now
presses **Update overrides** instead of **Remove selected**, so a stray Enter
cannot delete a rule (`64736998cd`).

### Remove cleans up

**Remove** deletes the INI block as well as the `.tpl` file, even when the file
is already gone.

### A better editing box

The template text area is full width, monospace, at least 70% of the window high
and keeps its line breaks (`80d3fef827`).

### The New override button only appears when it can work

If there is no source template for the resource, the editor says so instead of
showing a button that fails.

## Example: restyle one article

1. **Design > Template Editor**, pick the siteaccess design and open `node/view/full.tpl`.
2. Press **New override**, choose *Default copy*, and enter the **Object ID** of the article.
3. Press **Create**. The new rule has `Priority=0` and wins over the class rule.
4. Edit the new template and save.
5. Check in the page source with [template path comments](template-path-comments.md) that your file answers.

The editor saves the block in `settings/siteaccess/<siteaccess>/override.ini.append.php` and expires the content view cache. It looks like this:

```ini
[full_object_1234]
Source=node/view/full.tpl
MatchFile=full/object_1234.tpl
Subdir=templates
Priority=0
Match[object]=1234
```

(Block and file names are chosen from what you enter; the keys `Source`, `MatchFile`,
`Subdir`, `Match` and `Priority` are the ones the editor writes.)

## Smaller related change

`fetch( 'content', 'class_list', hash( ... ) )` returns the content classes
(optionally by group; any `sorts` value sorts them by name, ascending; `limit` takes `offset` and `length`), for
visual modules that browse classes (`a219ec4839`):

Parameters of the function (`kernel/content/function_definition.php`, all optional):

| Parameter | Default | Meaning |
|---|---|---|
| `as_object` | `true` | Return class objects (`false` returns rows). |
| `group_list` | `false` | Array of class group ids to restrict the result. |
| `sorts` | `null` | Sort array, for example `array( 'name' => 'asc' )`. |
| `limit` | `null` | `hash( 'offset', 0, 'length', 20 )`. |

```
{def $classes = fetch( 'content', 'class_list', hash( 'sorts', array( 'name' => 'asc' ), 'limit', hash( 'offset', 0, 'length', 20 ) ) )}
```

## Limits

- The editor writes the siteaccess override file (`settings/siteaccess/<siteaccess>/override.ini.append.php`); run `php bin/php/ezcache.php --clear-tag=template --allow-root-user`
  if a change does not appear (the editor clears the global INI and override caches itself).
- Keep a backup before bulk edits: the editor edits files on disk.

## Related

- [Template path comments](template-path-comments.md)
- Month page: [July 2026](../../history/2026/2026-07.md); [Template override ordering](template-override-ordering.md); [6.0.15 changelog](../../changelogs/6.0/6.0.15.md); [Behaviour changes of July and August 2026](../../bc/6.0/behaviour-changes-2026-07-08.md)
