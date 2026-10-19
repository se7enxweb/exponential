---
title: "Lesson 4.2: Building with the shared components"
audience: trainer, designer, developer
category: training
order: 42
summary: Learners find the shared .a4-* components in admin4, and add a dashboard block to their extension whose template uses a table, badges, an empty state and a button without a line of its own CSS. 60 minutes.
keywords: theme design, admin4, components.css, a4-card, a4-table, a4-badge, a4-btn, a4-empty, a4-notice, a4-sr-only, a4-actions, dashboard block, dashboard.ini, DashboardBlocks, extension admin design
type: help
---
# Lesson 4.2: Building with the shared components

Rule 2 says: use the shared components first. admin4's `components.css` already has cards, tables, badges, buttons,
notices, empty states, toolbars and form fields, in light and dark mode, with focus rings and forced-colors support.
In this lesson learners add a block to the administration's dashboard from their own extension and build it entirely
from those classes.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 4: Styling the administration](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who build administration pages |
| Prerequisites | [Lesson 4.1](the-administration-styling-rules.md); `training_theme` active; the administration in `admin4` or `admin4l` |

## Learning objectives

After this lesson, learners can:

- find a component and its states in the component table and in `components.css`;
- recognize the components on an admin4 page with the browser's inspector;
- add a dashboard block from an extension (`dashboard.ini`, a template in the extension's `design/admin4/`);
- write administration markup with `.a4-table`, `.a4-badge`, `.a4-empty`, `.a4-actions` and `.a4-btn`, and no CSS of
  their own.

## Trainer's notes

### How to present it

Source: [Styling the administration, components](../../../../bc/6.0/admin4-styling.md#components).

1. **The component table** (6 minutes). Walk through the groups: page scope (`.a4-page`), utilities (`.a4-sr-only`,
   `.a4-muted`, `.a4-meta`), cards (`.a4-card`, `.a4-card-head`, `.a4-card-body`, `.a4-card-foot`), tags and badges
   (`.a4-badge` with `.is-ok`, `.is-warn`, `.is-bad`, `.is-info`, `.is-accent`), buttons (`.a4-btn`,
   `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-ghost`, sizes), toolbars, tables (`.a4-table-wrap` around
   `table.a4-table`), the empty state `.a4-empty`, notices `.a4-notice`.
2. **States are classes** (3 minutes). A component is one class plus at most a state (`.is-*`) or an element, so a
   module rule with one more class always wins.
3. **On a real page** (5 minutes). Open the admin4 dashboard with the inspector; find a card, a button and a table, and
   their classes. Show that the dashboard's blocks come from `dashboard.ini [DashboardSettings] DashboardBlocks[]`,
   each drawn by `dashboard/<identifier>.tpl` inside a card the dashboard provides.
4. **An extension adds a block** (6 minutes). Show the two files of the exercise. The template goes in the extension's
   `design/admin4/templates/dashboard/`: the extension registers its design folders with `DesignExtensions[]`, and
   `admin4` is in the administration's chain.

### Common questions

**What if no component fits?** First check again (the table is long). Then the CSS goes in the extension's own
stylesheet with its own prefix and tokens only (lesson 4.4); a component missing for everyone is added centrally by
the core, never copied into a module.

**Do I need `.a4-page` around my block?** Module pages are wrapped in `.a4-page`. A dashboard block sits inside the
dashboard's own markup, so it is not needed there.

**Why `dir="auto"` on the name link?** An item name may be in another script direction than the interface; lesson
5.4 explains it.

### Pitfalls

- **The block does not appear.** The INI cache still has the old list; or the administration uses the classic design,
  whose chain does not include `admin4`. Check `SiteDesign` of the administration siteaccess.
- **Copying classes from a classic template.** `table.list`, `bglight`/`bgdark` belong to older templates; new markup
  uses `.a4-table`.
- **A link without a name.** An icon-only link needs a name (`.a4-sr-only` text or `aria-label`); keep text links in
  this exercise.

## Hands-on exercise

On your training installation, administration in `admin4` or `admin4l`, signed in as your training administrator.

1. Open the dashboard and the browser's inspector. Find one element each with the classes `a4-btn`, `a4-badge` and
   `a4-table` (or `table.list`), and note the page part it belongs to.
2. Create `extension/training_theme/settings/dashboard.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [DashboardSettings]
   DashboardBlocks[]=training_notes

   [DashboardBlock_training_notes]
   Priority=70
   NumberOfItems=5
   PolicyList[]=content/read

   */ ?>
   ```

3. Create the folder `extension/training_theme/design/admin4/templates/dashboard/` and in it `training_notes.tpl`:

   ```
   <h2>{'Latest in the content tree'|i18n( 'design/admin/dashboard/training_notes' )}</h2>
   {def $training_items = fetch( 'content', 'list', hash( 'parent_node_id', ezini( 'NodeSettings', 'RootNode', 'content.ini' ),
                                                          'limit', $block.number_of_items,
                                                          'sort_by', array( 'modified', false() ) ) )}
   {if $training_items}
   <div class="a4-table-wrap">
   <table class="a4-table">
       <thead>
           <tr>
               <th scope="col">{'Name'|i18n( 'design/admin/dashboard/training_notes' )}</th>
               <th scope="col">{'Type'|i18n( 'design/admin/dashboard/training_notes' )}</th>
           </tr>
       </thead>
       <tbody>
       {foreach $training_items as $training_item}
           <tr>
               <td><a href={concat( 'content/view/full/', $training_item.node_id )|ezurl} dir="auto">{$training_item.name|wash}</a></td>
               <td><span class="a4-badge">{$training_item.class_name|wash}</span></td>
           </tr>
       {/foreach}
       </tbody>
   </table>
   </div>
   {else}
   <p class="a4-empty">{'Nothing published below the top of the tree yet.'|i18n( 'design/admin/dashboard/training_notes' )}</p>
   {/if}
   <div class="a4-actions">
       <a class="a4-btn" href={concat( 'content/view/full/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )|ezurl}>{'Open the content tree'|i18n( 'design/admin/dashboard/training_notes' )}</a>
   </div>
   {undef $training_items}
   ```

4. Check it: `php bin/php/eztemplatecheck.php -sadmin extension/training_theme/design/admin4/templates/dashboard/training_notes.tpl`.
5. Clear the caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=template-override
   php bin/php/ezcache.php --clear-tag=template
   ```

   On a Velocity lab, also `php bin/php/console exp:velocity restart`.
6. Reload the dashboard. Find the new block, below the shipped ones.
7. Change the badge to `<span class="a4-badge is-info">` and reload. Try `is-ok` and `is-accent`.
8. Change the button to `a4-btn a4-btn-primary`, reload, and decide which of the two fits a block that is not the
   page's main action.
9. Press the color mode button in the header to switch to dark mode and look at your block. Switch back.
10. Search your two new files for `style` and for `#`: there must be no inline CSS and no color.

## Expected result

- Step 6: a card on the dashboard with the heading "Latest in the content tree", a striped table of five items with a
  badge per type, and a button "Open the content tree".
- Step 9: the block is dark with the rest of the page, with no white box, without any CSS written by the learner.
- Step 10: no match.
- The trainer checks one learner's dashboard in light and dark mode on the projector.

## Quiz

1. Where does an extension put the template of its dashboard block so that admin4 finds it?
2. Which class wraps a data table so that it scrolls inside its card on a narrow window?
3. A list is empty. Which component says so?
   A. `.a4-notice` B. `.a4-empty` C. a plain `<p>` D. `.a4-meta`
4. How is a badge shown as "ok" or "warning"?
5. Why did the block look right in dark mode without any CSS of the learner's own?

<details>
<summary>Answers</summary>

1. In `extension/<ext>/design/admin4/templates/dashboard/<identifier>.tpl`; the extension registers its design
   folders with `DesignExtensions[]`, and admin4 is in the administration's chain.
2. `.a4-table-wrap` around `table.a4-table`
   ([Styling the administration, components](../../../../bc/6.0/admin4-styling.md#components)).
3. **B.** An empty list says so in `.a4-empty`, never in a plain paragraph.
4. With a state class: `.a4-badge.is-ok`, `.is-warn`, `.is-bad`, `.is-info`, `.is-accent`.
5. The components take their colors from tokens, and every color token has a dark value.

</details>

## Further reading

- [Styling the administration](../../../../bc/6.0/admin4-styling.md#components): the full component table, cards,
  tables and the code review checklist.
- [The admin4 design](../../../../features/6.0/admin4-design.md): what admin4 provides around the components.
- [Template operators in a theme](../../../designers/extensions/template-operators-in-a-theme.md): when a block needs
  more than a fetch.

## Related pages

- [Module 4: Styling the administration](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
