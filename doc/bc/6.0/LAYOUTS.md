# Exponential Layouts

This document describes the **Exponential Layouts** subsystem in Exponential CMS 6.0, a port of the Netgen Layouts concept to the Exponential legacy stack.

It is written to be read start to finish by someone who has never opened the layout editor before. The first half teaches the ideas and the editor; the second half is reference material and extension work. If you already know the system, the [Reference tables](#reference-tables) at the end are the part you will keep coming back to.

---

## Table of contents

1. [Why the subsystem exists](#why-the-subsystem-exists)
2. [The five ideas you need](#the-five-ideas-you-need)
3. [Getting into the editor](#getting-into-the-editor)
4. [Layouts](#layouts)
5. [Zones](#zones)
6. [Blocks](#blocks)
7. [Containers and nesting](#containers-and-nesting)
8. [Block parameters](#block-parameters)
9. [Collections: giving a block its content](#collections-giving-a-block-its-content)
10. [Query types and their parameters](#query-types-and-their-parameters)
11. [Pinning: mixing hand-picked and dynamic content](#pinning-mixing-hand-picked-and-dynamic-content)
12. [Rules: deciding which layout a page gets](#rules-deciding-which-layout-a-page-gets)
13. [Drafts, publishing and versions](#drafts-publishing-and-versions)
14. [Shared layouts and linked zones](#shared-layouts-and-linked-zones)
15. [Import, export and share links](#import-export-and-share-links)
16. [Walkthrough A: customising the default design](#walkthrough-a-customising-the-default-design)
17. [Walkthrough B: building a new layout from scratch](#walkthrough-b-building-a-new-layout-from-scratch)
18. [How rendering actually works](#how-rendering-actually-works)
19. [Extending the system](#extending-the-system)
20. [Architecture and data model](#architecture-and-data-model)
21. [Troubleshooting](#troubleshooting)
22. [Reference tables](#reference-tables)

---

## Why the subsystem exists

In a stock eZ Publish site, what a page looks like is decided by templates. Changing the order of things on the front page, adding a promotional strip above the article list, or giving one section of the site a different sidebar all mean editing a `.tpl` file, which means a developer, a deployment, and a release.

Exponential Layouts moves those decisions into the database and puts them behind an editor. A layout is a saved arrangement of content; a rule says which pages get it. Editors rearrange pages without touching templates, and developers keep control of how each individual piece is rendered.

The division of labour is the point, and it is worth stating plainly because it governs everything below:

- **Developers own the block templates.** What a "Card" looks like is a `.tpl` file in a design.
- **Editors own the arrangement.** Which blocks exist, in what order, in which zones, holding what content, is data.

If you find yourself wanting to edit a template to move something, you probably want a layout change. If you find yourself wanting a layout to render something in a shape that does not exist yet, you need a new view type or block type, which is developer work — see [Extending the system](#extending-the-system).

---

## The five ideas you need

Everything in the editor is built from five concepts. Learn these and the interface explains itself.

**Layout** — a named arrangement, based on a *layout type* that fixes how many regions it has. `2 columns` has a left and a right; `Header / Main / Footer` has five. The layout type is chosen at creation and cannot be changed afterwards, because the zones are what blocks are attached to.

**Zone** — one named region inside a layout: `main`, `sidebar`, `footer`. Zones come from the layout type, so you never create or delete them. You fill them, or you link them to another layout (see [shared layouts](#shared-layouts-and-linked-zones)).

**Block** — one item inside a zone: a piece of text, an image, a list of articles, a hero banner. The block's *definition* says what kind of thing it is; its *view type* says which of several shapes it renders in; its *parameters* configure it.

**Collection** — the content a block displays, when the block is the kind that displays content. A collection is either **manual** (you pick the items) or **dynamic** (a query fetches them). Only some block types have one; the [block catalogue](#block-catalogue) marks which.

**Rule** — the mapping from a request to a layout. A rule points at a layout and carries *targets* (which requests it applies to) and *conditions* (extra restrictions). Rules have a priority; the first match wins.

A useful sentence to hold onto:

> A **rule** decides that this request gets that **layout**; the layout's **zones** hold **blocks**; a block may own a **collection** that supplies its content.

---

## Getting into the editor

There are two admin interfaces. They operate on the same data, so you can move between them freely.

**The SPA editor** — the modern one, and the subject of most of this document:

```
/explayouts_ui_api/app
```

Its JSON API lives beneath it at `/explayouts_ui_api/app/api/...`. You will not normally call the API by hand, but knowing it is there makes the editor's behaviour much easier to reason about, and it is how you script bulk changes.

**The legacy form-based editor** — plainer, sometimes quicker for a single field, and the place where a few administrative screens live that the SPA does not have:

```
/explayouts_ui/dashboard
```

It provides: `layout_list`, `layout_create`, `layout_edit`, `block_edit`, `rule_list`, `rule_edit`, `preview`, `components`, `shared_layouts_list`, `template_editor`, `transfer_import`, `setup`.

### Permissions

Both interfaces check the `explayouts` module's policies through the standard role editor. A user needs at least `explayouts / read` to open the dashboard, and the editing functions to change anything. Module and policy keys are declared in `extension/explayouts/settings/module.ini.append.php`.

If the editor loads but every list is empty, check policies before you suspect data.

### Checking it is alive

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://<your-admin-host>/explayouts_ui_api/app
curl -s https://<your-admin-host>/explayouts_ui_api/app/api/config
```

The config endpoint returns the CSRF token the SPA uses, the edition string, and whether automatic cache clearing is on. A `200` from both means the module, the extension and the database are all in order.

---

## Layouts

### Creating one

In the SPA, *Layouts → Create*. You supply three things:

| Field | What it is for |
|---|---|
| **Layout type** | Fixes the zones. Permanent — choose deliberately. |
| **Identifier** | The machine name. Used by templates and `fetch()` calls. Lowercase, no spaces. |
| **Name** | The human label shown in lists. |

The identifier matters more than it looks. It is how a template asks for a specific layout by name, and how a layout survives being exported and re-imported elsewhere. Treat it as permanent even though the field can be edited.

### The layout types

Twelve ship as standard. The zone names in the third column are exactly the names you will see in the editor and in templates.

| Identifier | Name | Zones |
|---|---|---|
| `1_column` | 1 column | `main` |
| `2_column` | 2 columns | `left`, `right` |
| `3_column` | 3 columns | `left`, `main`, `right` |
| `4_column` | 4 columns | `col1`, `col2`, `col3`, `col4` |
| `hero` | Hero + 3 columns | `top`, `left`, `main`, `right` |
| `sidebar_left` | Sidebar left | `sidebar`, `main` |
| `sidebar_right` | Sidebar right | `main`, `sidebar` |
| `featured` | Featured | `hero`, `feature1`, `feature2`, `feature3`, `bottom` |
| `mosaic` | Mosaic | `a`, `b`, `c`, `d`, `e` |
| `layout_1` | Single zone | `main` |
| `layout_2` | Header / Main / Footer | `header`, `post_header`, `main`, `pre_footer`, `footer` |
| `layout_4` | Header / Left / Right / Footer | `header`, `post_header`, `left`, `right`, `pre_footer`, `footer` |

`layout_1`, `layout_2` and `layout_4` are the ones to reach for when building a whole page shell, because they include header and footer regions. The numbered column types are better suited to a layout that fills a content area.

Choosing between `1_column` and `layout_1` — both have a single `main` zone — comes down to which template renders them; see [How rendering actually works](#how-rendering-actually-works).

### Layout-level actions

| Action | What it does | Why you would use it |
|---|---|---|
| **Publish** | Copies the draft over the published rows | Makes your work live |
| **Create draft** | Opens an editable copy of the published layout | Start a round of changes |
| **Discard draft** | Throws the draft away, published stays | Abandon a change safely |
| **Copy** | Duplicates the whole layout, blocks and all | Base a new layout on an existing one |
| **Delete** | Removes the layout entirely | Retire a layout — check no rule points at it |

---

## Zones

Zones are fixed by the layout type, so the editor gives you no way to add or remove them. Each zone in the editor is a drop area with the zone's identifier as its heading.

A zone does one of two things:

1. **Holds its own blocks** — the normal case. Add blocks to it, order them, done.
2. **Links to a zone of a shared layout** — the zone displays another layout's blocks instead of its own. See [Shared layouts and linked zones](#shared-layouts-and-linked-zones).

Block order within a zone is a `position` integer. In the editor you drag; the editor renumbers and saves.

---

## Blocks

A block is added by opening a zone's add menu and choosing a block type. Forty-six ship as standard. Rather than listing them alphabetically, here they are by what you would reach for them to do.

### Block catalogue

**Text and markup**

| Identifier | Name | View types | Notes |
|---|---|---|---|
| `text` | Text | `default` | Plain text |
| `title` | Title | `default` | A heading |
| `rich_text` | Rich text | `default` | CKEditor-backed |
| `markdown` | Markdown | `default` | Rendered from Markdown source |
| `html` | HTML snippet | `default` | Raw markup — trust the author |
| `quote` | Quote | `default` | Pull quote |

**Media**

| Identifier | Name | View types | Collection |
|---|---|---|---|
| `image` | Image | `default` | |
| `video` | External video | `default` | |
| `gallery` | Gallery | `default` | yes |
| `thumb_gallery` | Thumb gallery | `default` | yes |
| `grid_gallery` | Grid gallery | `default` | yes |
| `slider` | Slider | `default` | yes |
| `carousel` | Carousel | `default` | yes |
| `map` | Map | `default` | |

**Content listing** — these are the blocks that pull content from the site

| Identifier | Name | View types | Collection |
|---|---|---|---|
| `list` | List | `list`, `grid`, `list_numbered`, `list_zigzag`, `list_accordion`, `grid_featured` | yes |
| `grid` | Grid | `default` | yes |
| `list_zigzag` | Zig-Zag (List) | `list_zigzag` | yes |
| `list_accordion` | Accordion (List) | `list_accordion` | yes |
| `sushi_bar` | Sushi bar | `default` | yes |
| `single` | Single content | `default` | |
| `full_view` | Full view | `full_view` | |

`list` is the workhorse. Its six view types mean one block type covers most listing needs; prefer changing the view type over picking a different block.

`full_view` is special: it renders the current content item in its normal full view. It is how you embed the actual page content inside a layout, and most page-shell layouts need exactly one of them.

**Layout and structure**

| Identifier | Name | View types | Container |
|---|---|---|---|
| `two_columns` | Two columns | `two_columns_66_33`, `two_columns_33_66` | yes |
| `three_columns` | Three columns | `three_columns` | yes |
| `four_columns` | Four columns | `four_columns` | yes |
| `column` | Column | `column` | yes |
| `spacer` | Spacer | `default` | |
| `divider` | Divider | `default` | |

**Interface elements**

| Identifier | Name | View types |
|---|---|---|
| `button` | Button / Link | `default` |
| `card` | Card | `default` |
| `accordion` | Accordion | `default` |
| `tabs` | Tabs | `default` |
| `alert` | Alert | `default` |
| `badge` | Badge | `default` |
| `progress` | Progress bar | `default` |

**Page sections** — pre-composed marketing sections

| Identifier | Name | View types | Collection |
|---|---|---|---|
| `hero` | Hero | `default` | |
| `about` | About | `default` | |
| `features` | Features | `default` | |
| `lead` | Lead | `default` | |
| `logos` | Logos | `default` | yes |

**Styled components** — the same sections with several ready-made visual treatments

| Identifier | Name | View types |
|---|---|---|
| `ibexa_component_hero` | Hero component | `hero_style_1` … `hero_style_3` |
| `ibexa_component_features` | Features component | `features_style_1` … `features_style_7` |
| `ibexa_component_about` | About component | `about_style_1` … `about_style_5` |
| `ibexa_component_logos` | Logos component | `logos_style_1`, `logos_style_2` |
| `ibexa_component_quote` | Quote component | `quote_style_1` |
| `ibexa_component_lead` | Lead component | `lead_style_1`, `lead_style_2` |

The `ibexa_component_*` family exists so a marketing page can be assembled from pre-designed sections without a developer. Their many view types are the reason: choose the style number that suits the page. The legacy admin's **Components** screen lists where each one is used across all layouts, which is how you find every page using a style before changing it.

**Templates**

| Identifier | Name | View types |
|---|---|---|
| `tpl_block` | Template block | `tpl_block` |

`tpl_block` renders a named template chosen by its `block_name` parameter. It is the escape hatch: when a page needs something no block type covers, a developer writes one template and the editor places it anywhere. Prefer a proper block type for anything used more than once or twice.

### Block-level actions

Each block in the editor offers:

- **Edit** — open its parameter panel
- **Move** — drag within a zone, or to another zone
- **Duplicate** — copy it, inserted after the original
- **Delete** — remove it

Duplicating is usually faster than building a second configured block from scratch, and it copies parameters and collection settings with it.

---

## Containers and nesting

Four block types are *containers*: they hold other blocks instead of rendering content themselves. This is how you get a two-column strip inside a single-column zone without inventing a new layout type.

| Container | Placeholders |
|---|---|
| `two_columns` | `left`, `right` |
| `three_columns` | `col_1`, `col_2`, `col_3` |
| `four_columns` | `col_1`, `col_2`, `col_3`, `col_4` |
| `column` | `main` |

A *placeholder* is to a container what a zone is to a layout: a named slot that holds an ordered list of blocks. In the editor a container renders as a block with its own drop areas inside it.

`two_columns` has two view types, `two_columns_66_33` and `two_columns_33_66`, which set the width split. The placeholders are the same either way — changing the view type reflows without moving blocks.

Containers can nest. They are also the main way to make a layout responsive in practice, since the column templates carry the responsive classes.

A caution: deep nesting is hard to edit and hard for the next person to understand. If you are three containers deep, a purpose-built layout type or block type is usually the better answer.

---

## Block parameters

Selecting a block opens its parameter panel. Parameters are declared by the block's handler, so each block type shows a different set. A `button` block, for example, declares:

| Parameter | Type | Default |
|---|---|---|
| Button text | text | *(empty)* |
| Link URL | text | *(empty)* |
| Target | select — `_self`, `_blank`, `_parent`, `_top` | `_self` |
| CSS class | text | `btn btn-primary` |

Six parameter types exist across the standard handlers, and these are the only widgets the editor knows how to draw:

| Type | Widget |
|---|---|
| `text` | Single-line input |
| `textarea` | Multi-line input |
| `select` | Single choice from a list |
| `multiselect` | Several choices from a list |
| `checkbox` | On/off |
| `browse` | Content picker — opens the content browser |

Alongside the handler's own parameters, every block carries:

- **View type** — which template shape to render in, from that block type's list
- **Name** — an editor-facing label, to tell two similar blocks apart in the tree
- **CSS ID** and **CSS class** — written onto the block's wrapper element

The CSS class field is the seam between editor work and design work, and it is the single most useful field for customisation: a developer ships utility classes in the theme, an editor applies them per block, and nobody has to touch a template to change a background or a spacing.

---

## Collections: giving a block its content

Blocks marked *collection* in the catalogue display content from the site, and a collection is how they are told which content.

A collection is one of two kinds:

**Manual** — you choose each item by hand through the content browser. Right when the content is specific and will not change: three named products, a curated set of five articles.

**Dynamic** — a *query* fetches items at render time. Right when the content should keep itself current: the latest five articles in this section, the children of this node, everything tagged with this topic.

Both kinds support two more settings, which apply after the query has run:

| Setting | Meaning |
|---|---|
| **Offset** | Skip this many items from the start |
| **Limit** | Show at most this many |

Offset is what lets two blocks show different slices of the same query. A "featured" block with limit 1, and a "more stories" block below it with offset 1 and limit 6, together produce the familiar lead-plus-list pattern from a single ordering.

---

## Query types and their parameters

Twelve query types ship as standard.

| Identifier | Name | What it returns |
|---|---|---|
| `children` | Children of a node | Direct children of a chosen location |
| `parent` | Parent of a node | The parent location |
| `subtree` | Subtree of a node | Everything beneath a location, at any depth |
| `siblings` | Siblings of a node | Other children of the same parent |
| `latest` | Latest content | Most recently published, newest first |
| `random` | Random content | A random selection |
| `manual` | Manual collection | Exactly the items you picked |
| `exp_content_relation_list` | Exp relation list | Content this item points at |
| `exp_content_reverse_relation_list` | Exp reverse relation list | Content pointing at this item |
| `exp_content_tags` | Exp tags | Content sharing tags |
| `exponential_content_search` | Exponential | General search with filters — the most capable |
| `content_by_topic` | Topics | Content under a topic |

### Reading the parameter panel

Query parameters are grouped, and the grouping is deliberate. Four parameters are treated as *basic* and shown first because they are the ones almost every query needs:

- `use_topic_from_current_content`
- `use_current_location`
- `sort_type`
- `sort_direction`

Everything else is *advanced* and collapsed.

Some parameters are **compound**: a checkbox that reveals the fields it governs. This keeps the panel short and, more importantly, makes the dependency obvious — the child fields are meaningless unless the parent is set.

| Checkbox | Reveals | Sense |
|---|---|---|
| `use_topic_from_current_content` | `topic_content_id` | Reversed — the field appears when *unchecked* |
| `use_current_location` | `parent_location_id` | Reversed — the field appears when *unchecked* |
| `filter_by_content_type` | `content_types`, `content_types_filter` | Normal |
| `filter_by_section` | `sections` | Normal |
| `filter_by_object_state` | `object_states` | Normal |

The two reversed ones are worth dwelling on, because they are the feature that makes a layout reusable. "Use current location as parent" means *do not hard-code a location — use whatever page is being rendered*. A single layout with a `children` query and that box ticked serves every section of the site: on `/recipes` it lists recipes, on `/fitness` it lists fitness articles. Untick it and a location field appears, pinning the block to one place.

That distinction — bound to the current page, or pinned to a fixed one — is the difference between a layout you write once and a layout you copy twenty times.

### The Exponential content search

`exponential_content_search` is the general-purpose query, and where the filters live. Its parameters include the parent location, sorting, and the three compound filters above:

- **Content type** — restrict to chosen classes, with a filter mode
- **Section** — restrict to chosen sections
- **Object state** — restrict to chosen states

Use it when the simpler queries do not fit. Use `children` or `subtree` when they do — they are easier to read six months later.

---

## Pinning: mixing hand-picked and dynamic content

Manual items can be added to a *dynamic* collection, and when you do they become **pinned overrides**: each occupies its stored position, and the query fills the slots around it.

This solves the everyday editorial problem where a list should mostly look after itself, but one item has to be in position one this week. Without pinning you would have to switch the whole block to manual and then remember to switch it back.

The mechanics:

- Pinned positions are absolute within the collection, not relative to the query results.
- A pinned item at position 0 is first, whatever the query returns.
- Query results skip the pinned positions and fill the rest in order.

Practical advice: pin as little as possible, and write a note in the block's **Name** field saying why, or the next editor will not understand why position 3 never changes.

---

## Rules: deciding which layout a page gets

A layout does nothing until a rule points requests at it. Rules live under *Mappings* in the SPA and *rule_list* in the legacy UI.

A rule has:

- **A layout** — what to show
- **Targets** — which requests it applies to
- **Conditions** — extra restrictions that must also hold
- **Priority** — higher wins when several rules match
- **Enabled** — a switch, so a rule can be parked without deleting it

Rules are evaluated in priority order and **the first match wins**. This is the single most common source of confusion: if a layout is not appearing, there is very often a broader rule with a higher priority catching the request first.

### Targets

| Type | Matches when |
|---|---|
| `path_prefix` | The request path starts with the value |
| `path_info_prefix` | Same, normalised to a leading `/` |
| `path` | The path equals the value exactly |
| `path_regex` | The path matches the regular expression |
| `node` | The request resolves to this content node |
| `subtree` | The request's node lies beneath this location |

There is also a `route` target type, declared but **not implemented** — it always fails to match. Do not build on it.

`subtree` is usually what you want for "this whole section of the site", because it follows content moves. A `path_prefix` breaks the moment an editor renames a folder.

### Conditions

| Type | Restricts by | Value shape |
|---|---|---|
| `siteaccess` | Which siteaccess is serving | JSON array of siteaccess names |
| `content_type` / `class` | The content class of the node | JSON array of class identifiers |
| `query_parameter` | A `?name=value` in the URL | JSON with `parameter_name`, optional `parameter_values` |
| `route_parameter` | A user parameter in the path | JSON with `parameter_name`, optional `parameter_values` |
| `time` | The current time | JSON with `from` and/or `to` |

Two behaviours are worth knowing because they are easy to misread:

- For `query_parameter` and `route_parameter`, supplying **no** values means "the parameter merely has to be present". Supplying values means one of them must match.
- For `time`, an empty range matches always. It is designed for campaign layouts that switch themselves on and off.
- An unrecognised condition type **matches** rather than failing. A typo in a condition therefore silently widens the rule instead of disabling it.

### Worked example

To give every article under `/recipes` a special layout, but only on the public siteaccess, and only during a promotion:

| Part | Setting |
|---|---|
| Layout | `recipe_promo` |
| Target | `subtree` → the Recipes location |
| Condition | `content_type` → `["ng_recipe"]` |
| Condition | `siteaccess` → `["site"]` |
| Condition | `time` → `{"from":"2026-10-01","to":"2026-10-31"}` |
| Priority | higher than the general recipe rule |

Rules can also be **copied**, which is the quickest way to build a family of near-identical mappings.

---

## Drafts, publishing and versions

Every layout, zone and block carries a `status`:

| Status | Meaning |
|---|---|
| `1` | Draft — what the editor works on |
| `2` | Published — what the site renders |

The editor always works on a draft. Publishing copies the draft rows over the published rows; the site only ever reads published rows, so unfinished work is never visible to visitors.

The cycle:

1. **Create draft** from the published layout (the editor does this when you start editing).
2. Make changes. They are saved continuously to the draft — there is no separate save step.
3. **Publish**, or **discard draft** to abandon the changes and leave the published version untouched.

The *Versions* view lists the published and draft versions of a layout with their timestamps, which is how you confirm whether a draft is outstanding before handing work over.

Because drafts are per-layout rather than per-user, two people editing the same layout at once will overwrite each other. Coordinate, or copy the layout and merge by hand.

---

## Shared layouts and linked zones

A layout can be marked **shared**. A shared layout is not mapped to requests by a rule; it exists to be borrowed. Any zone of any other layout can be *linked* to one of its zones, and will then render that layout's blocks in place of its own.

This is how a site gets one footer, defined once. Build a shared layout holding the footer blocks, then link the `footer` zone of every page layout to it. Change the shared layout, publish, and every page follows.

Rules worth knowing:

- Only **published** shared layouts can be linked to. A zone never points at somebody's unpublished draft.
- A linked zone ignores its own blocks while the link is in place. Removing the link restores them.
- The legacy admin's **Shared layouts** screen lists shared layouts with a count of how many zones link to each — check it before changing or deleting one.

A shared layout with zero links is still perfectly valid; it is just one nobody has adopted yet.

---

## Import, export and share links

**Export** produces a JSON document describing a layout with its zones, blocks, parameters and collections. **Import** takes that document and creates a layout from it.

This is the supported way to move a layout between environments — build on staging, export, import on production — and the sensible way to keep a backup before a large restructuring. It is also how the port from Netgen Layouts works, since the data shape was kept deliberately close.

Identifiers travel with the export, so an import into an environment that already has that identifier needs care.

**Share links** are a separate feature. A share link is a token granting a preview of a layout without an admin login, for showing work to someone who does not have an account. Tokens are created, listed and revoked per layout.

> On MongoDB installations the share endpoint requires the `explayouts_share` table, which it creates on first use. This works on MySQL, SQLite and MongoDB as of `explayouts_ui_api` v1.2.3; earlier versions emitted MySQL-only DDL and reported success for tokens they had not stored.

---

## Walkthrough A: customising the default design

The goal: add a promotional strip to the top of every page in one section, without touching a template.

**1. Find the layout the section already uses.** Open *Mappings* and look for the rule whose target covers the section. Note the layout it points at.

**2. Decide whether to edit or branch.** If the layout serves only this section, edit it. If it serves several, **copy** it, give the copy a clear identifier such as `section_recipes`, and make a new rule for the section at a higher priority. Branching avoids the classic accident of changing twenty pages while aiming at three.

**3. Open the layout and create a draft.**

**4. Add the strip.** In the top-most zone, add a `two_columns` container with view type `two_columns_66_33`. Into `left` add a `rich_text` block for the message; into `right` add a `button` block. Set the button's text, link and target.

**5. Style it without a template change.** Put a theme utility class into the container's **CSS class** field. This is the step people miss — reaching for a template here is what turns a five-minute edit into a deployment.

**6. Position it.** Drag the container to the top of the zone.

**7. Preview, then publish.**

**8. Verify on the front end.** Load a page in the section and confirm the strip renders and the link works.

If the strip should later appear across the whole site, move it into a shared layout and link the zone instead of repeating it.

---

## Walkthrough B: building a new layout from scratch

The goal: a landing page layout, mapped to one location, mixing curated and automatic content.

**1. Create the layout.** *Layouts → Create*. Type `layout_2` (Header / Main / Footer), identifier `campaign_landing`, name "Campaign landing".

**2. Header.** Link the `header` zone to your shared header layout rather than rebuilding it. If none exists, this is the moment to make one.

**3. Hero.** In `post_header`, add `ibexa_component_hero` and choose `hero_style_2`. Fill its parameters.

**4. Curated highlights.** In `main`, add a `list` block with view type `grid_featured`. Give it a **manual** collection and pick three items through the content browser. Manual is right here because a campaign's highlights are chosen, not computed.

**5. Automatic supporting content.** Below it, add a second `list` with view type `list`. Give it a **dynamic** collection using `exponential_content_search`:

- Untick **use current location** and pick the campaign folder, or leave it ticked if the layout should follow whatever page it is on.
- Tick **filter by content type** and choose your article class.
- Set `sort_type` to published date, `sort_direction` descending.
- Set **limit** to 6.

**6. Avoid duplication.** If the same items could appear in both blocks, set the second block's **offset** so it starts after the highlights.

**7. Pin one item.** If one article must lead the automatic list regardless of date, add it as a manual item at position 0 — it becomes a pinned override and the query fills the rest.

**8. Footer.** Link `pre_footer` or `footer` to the shared footer layout.

**9. Map it.** In *Mappings*, create a rule pointing at `campaign_landing` with a `node` target for the campaign landing page. Add a `siteaccess` condition if the site is multi-siteaccess. Give it a priority above any broader rule that would otherwise catch the page.

**10. Publish both** the layout and the rule, then load the page.

If nothing changes, work through [Troubleshooting](#troubleshooting) — the usual answer is a higher-priority rule matching first.

---

## How rendering actually works

Understanding the chain makes debugging much faster.

1. A request arrives. `pagelayout.tpl` asks the resolver for a layout.
2. `expLayoutsResolver::resolve($path)` walks enabled rules in priority order, testing targets then conditions, and returns the first published layout that matches.
3. `layout.tpl` renders the layout shell and picks the sub-template for the layout type.
4. `zone.tpl` renders each zone's blocks in position order — or, for a linked zone, the shared layout's blocks.
5. Each block renders through the template matching its definition and view type.
6. A block with a collection has it resolved first: a dynamic collection runs its query through `expLayoutsDynamicCollection`, pinned items are merged, offset and limit applied.

Templates live in `extension/explayouts/design/standard/templates/explayouts/`:

- `layout.tpl` — outer shell
- `zone.tpl` — blocks of one zone, in order
- `block/*.tpl` — one per block definition and view type
- `layouts/*.tpl` — one per layout type

A frontend `pagelayout.tpl` typically does:

```smarty
{def $layout=fetch( 'explayouts', 'resolve_layout', hash() )}
{if $layout}
    {include uri='design:explayouts/layout.tpl' layout=$layout}
{else}
    {$module_result.content}
{/if}
```

The `else` branch matters: a site should still render when no rule matches.

### Available fetch functions

| Function | Purpose |
|---|---|
| `fetch( 'explayouts', 'layout', hash( ... ) )` | Load a specific layout |
| `fetch( 'explayouts', 'resolve_layout', hash() )` | Resolve for the current request |
| `fetch( 'explayouts', 'resolve_layout_for_node', hash( ... ) )` | Resolve as if for a given node |
| `fetch( 'explayouts', 'rules_for_node', hash( ... ) )` | List rules matching a node — useful for debugging |

`rules_for_node` is the one to reach for when a page is getting the wrong layout: it tells you what matched, rather than making you guess.

### Resolver caching

The resolver caches the resolved rule and layout id per request path under `var/<VarDir>/cache/explayouts/resolver/`, with a TTL from `explayouts.ini`:

```ini
[ResolverSettings]
DefaultLayout=homepage
CacheTTL=3600
```

Call `expLayoutsResolver::clearCache()` when rules or layouts change. With `automatic_cache_clear` on — the SPA reports it in `/api/config` — publishing handles this for you. If you change rules directly in the database, clear it yourself.

---

## Extending the system

### A new block type

1. Write a handler class declaring `getParameters()`, returning the fields the editor should draw. Use only the six supported types.
2. Register it in `explayouts.ini.append.php`:

```ini
[BlockDefinition_testimonial]
Name=Testimonial
Handler=expLayoutsTestimonialBlockHandler
ViewTypes[]=default
ViewTypes[]=compact
```

3. Add a template per view type under `block/`.
4. Regenerate autoloads and clear caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
systemctl restart php-fpm
```

Add `HasCollection=1` if the block should display site content, and the editor will offer it a collection.

### A new view type for an existing block

Often all that is wanted is a different shape. Add the view type to the block's `ViewTypes[]` and write the template. No PHP. This is much cheaper than a new block type and should be the default answer to "can it look like X as well?".

### A container block

Declare placeholders:

```ini
[BlockDefinition_split]
Name=Split
Handler=expLayoutsContainerBlockHandler
IsContainer=1
Placeholders[]=left
Placeholders[]=right
ViewTypes[]=default
```

The shipped `expLayoutsContainerBlockHandler` is usually sufficient — containers rarely need custom logic.

### A new query type

1. Write a handler implementing the query interface, with `getParameters()` for the editor panel.
2. Register it:

```ini
[QueryType_most_read]
Name=Most read
Handler=expLayoutsMostReadQueryHandler
```

3. Regenerate autoloads and clear caches.

If you add a parameter that only makes sense when another is set, follow the compound-checkbox pattern so the panel stays readable.

> **Engine note.** Query handlers run on whichever database the site uses. MongoDB has no `JOIN` and no `GROUP BY`, and the driver returns an **empty result** for SQL it cannot translate rather than raising an error. A handler written with a join will therefore return nothing on MongoDB, silently, and look exactly like a query with no matches. If your site may run on MongoDB, branch on `eZDB::instance()->databaseName() === 'mongo'` and read the collections separately. Several shipped handlers do exactly this.

---

## Architecture and data model

The implementation is deliberately modular:

| Extension | Responsibility |
|---|---|
| `explayouts` | Core persistence, rendering, resolution |
| `explayouts_core` | Domain services — layout, block, zone, rule, collection |
| `explayouts_standard` | Standard block handlers |
| `explayouts_ui` | Legacy admin module |
| `explayouts_ui_api` | SPA shell and JSON API |
| `explayouts_content_browser` | Content picker |
| `explayouts_site_api` | Site-API value converters |

### Tables

| Table | Purpose | Persistent object |
|---|---|---|
| `explayouts_layout` | A named layout with identifier, type, status | `expLayoutsLayout` |
| `explayouts_zone` | A region inside a layout; may link to another layout | `expLayoutsZone` |
| `explayouts_block` | A block in a zone, with definition and view type | `expLayoutsBlock` |
| `explayouts_block_parameter` | Key/value settings for a block | `expLayoutsBlockParameter` |
| `explayouts_collection` | A list attached to a block | `expLayoutsCollection` |
| `explayouts_collection_item` | Manual or pinned items | `expLayoutsCollectionItem` |
| `explayouts_collection_query` | Query parameters for a dynamic collection | `expLayoutsCollectionQuery` |
| `explayouts_rule` | A resolver rule with priority and enabled flag | `expLayoutsRule` |
| `explayouts_rule_target` | Target of a rule | `expLayoutsRuleTarget` |
| `explayouts_rule_condition` | Extra conditions | `expLayoutsRuleCondition` |

Schemas ship for MySQL, SQLite, PostgreSQL and Oracle under `extension/explayouts/sql/<engine>/schema.sql`. Oracle uses `NUMBER(11,0)`, `CLOB`, and a sequence plus trigger per table to emulate auto-increment.

### The JSON API

The SPA talks to `expLayoutsUIApplicationApi` under `/explayouts_ui_api/app/api/`. Knowing the resources makes scripted changes straightforward:

| Resource | Covers |
|---|---|
| `config`, `config/layout_types`, `config/block_types` | Editor bootstrap and catalogues |
| `layouts`, `layouts/shared`, `layouts/{id}/zones`, `layouts/{id}/blocks` | Layout structure |
| `layouts/{id}/publish`, `layouts/{id}/draft` | Publish, create draft, discard |
| `blocks/{id}` | Block edit, move, duplicate, delete |
| `collections`, `collections/{id}/items` | Collections and their items |
| `rules`, `mappings` | Rules, targets, conditions |
| `transfer/export`, `transfer/import` | Import and export |
| `content_browser` | Content picker |
| `forms`, `parameters`, `versions`, `share` | Supporting endpoints |

---

## Troubleshooting

**The layout does not appear on the site.**
Check, in this order: is the layout **published**; is the rule **enabled**; does a **higher-priority** rule match first; do all **conditions** hold. `fetch( 'explayouts', 'rules_for_node', ... )` answers this directly. Remember an unrecognised condition type matches rather than failing.

**Changes in the editor are not visible.**
You are probably looking at published output while editing a draft. Publish, or use preview. If publishing did not help, clear caches and restart PHP-FPM.

**A dynamic block shows nothing.**
Run the query's intent by hand. Check the offset is not past the end of the results, that the content-type filter names classes that exist, and that "use current location" is set the way you intend — a layout pinned to a location that has no children will be permanently empty. On MongoDB, also read the engine note under [Extending the system](#extending-the-system): an untranslatable query returns empty rather than erroring.

**A list is in the wrong order.**
Check `sort_type` and `sort_direction` first, then look for pinned items holding absolute positions.

**A linked zone shows nothing.**
The shared layout must be **published**. A draft-only shared layout links to nothing.

**The editor loads but every list is empty.**
Check `explayouts` policies on the user's role before suspecting the data.

**A block renders unstyled or raw.**
The template for that definition and view type combination is probably missing. Check `block/` in the active design, and remember designs are searched in `ActiveAccessExtensions` order.

---

## Reference tables

### Layout types

See [The layout types](#the-layout-types) — 12 types.

### Block types

See [Block catalogue](#block-catalogue) — 46 definitions. Blocks with collections: `list`, `grid`, `gallery`, `slider`, `thumb_gallery`, `grid_gallery`, `sushi_bar`, `list_zigzag`, `list_accordion`, `carousel`, `logos`.

### Query types

See [Query types and their parameters](#query-types-and-their-parameters) — 12 types.

### Rule targets

`path_prefix`, `path_info_prefix`, `path`, `path_regex`, `node`, `subtree`. (`route` is declared but not implemented.)

### Rule conditions

`siteaccess`, `content_type` / `class`, `query_parameter`, `route_parameter`, `time`.

### Parameter widget types

`text`, `textarea`, `select`, `multiselect`, `checkbox`, `browse`.

### Files to know

| File | Responsibility |
|---|---|
| `extension/explayouts/classes/explayoutsresolver.php` | Rule matching and layout resolution |
| `extension/explayouts/classes/explayoutsrenderer.php` | Prepares layout/zone/block data for templates |
| `extension/explayouts/classes/explayoutsdynamiccollection.php` | Runs dynamic queries, merges pinned items |
| `extension/explayouts/classes/explayoutscollectionquery.php` | Persistent object for `explayouts_collection_query` |
| `extension/explayouts/settings/explayouts.ini.append.php` | Layout types, block definitions, query types, resolver settings |
| `extension/explayouts/sql/{mysql,sqlite,pgsql,oracle}/schema.sql` | Database schemas |
| `extension/explayouts/design/standard/templates/explayouts/` | Frontend rendering templates |
| `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php` | SPA JSON API |
| `extension/explayouts_core/classes/explayoutscorelayoutservice.php` | Layout lifecycle — create, publish, draft, discard, copy |

### Installation checklist

1. Install the tables from the SQL file matching your database.
2. Activate `explayouts` and the related extensions.
3. Regenerate autoloads: `php bin/php/ezpgenerateautoloads.php -e`
4. Clear caches and restart: `php bin/php/ezcache.php --clear-all --allow-root-user && systemctl restart php-fpm`
5. Create a layout and a rule at `/explayouts_ui_api/app`.
6. Wire `pagelayout.tpl` to `fetch( 'explayouts', 'resolve_layout', hash() )`.

### Migration and parity

The port follows the Netgen Layouts data shape, so a Netgen XML/JSON export can be imported with minimal transformation. Differences are limited to Exponential-specific query handlers and the legacy template rendering layer.
