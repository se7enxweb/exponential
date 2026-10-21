---
title: Known editor behaviors
audience: editor, website-creator, admin, designer, developer
category: build-two-ways
order: 280
summary: Behaviors of the Layouts editor and its tools seen while writing these pages on Exponential 6.0.15+, each with what was observed and how to work with it - view type changes, item positions, search in the content browser, template blocks in the header and footer, and the import and helper command behaviors fixed since explayouts_core 1.0.10 and explayouts 1.4.42.
keywords: known issues, behaviors, layouts editor, view type not saved, js-skip-on-change, collection item position, content browser search, import cache, block_list, layout_info, collection_list, workaround
type: help
---
# Known editor behaviors

Each entry was observed on a copy of a live installation while writing this help. They are reported so you know what
to expect; check the release notes of your version, as they may be fixed there.

## A view type change alone is not saved (fixed in explayouts_ui_api 1.3.29)

**Seen:** on the Design tab, choosing another **View type** changed the form, but the draft kept the old view type;
after **Publish layout** the block was unchanged. The select carries the class `js-skip-on-change`, which the editor's
script uses to skip saving on that change. explayouts_ui_api 1.3.28 saved most view types; 1.3.29 also saves a list's
view types that leave no item view type to choose (`list_zigzag`, `list_accordion`, a view type of your own, such as
`list_compact`).

**Work with it:** update explayouts_ui_api to 1.3.29 or later. On an older version, after choosing the view type,
change any other field of the same form (type and delete a character in **CSS class**): the whole form is saved, view
type included. Either way, the block header shows the saved view type in capitals.

## Two items with position 0 (fixed in explayouts_ui_api 1.3.29)

**Seen:** after **+ Add items** added a fourth article to a manual collection of three, the published collection had
positions 0, 0, 1, 2 (the new item and the first one both 0). The page showed all four. The editor sends every chosen
item at the collection's offset, and each was stored at exactly that position.

**Work with it:** update explayouts_ui_api to 1.3.29 or later: each new item gets a position of its own, the new items
come first in the order you chose them, and moving an item moves the ones in between. Positions that were shared
before are sorted out on the next change to that collection. On an older version, drag the items once more into the
order you want before publishing.

## The content browser's search

**Seen:** in **Add content to collection**, the search field returned "No content found." for the word tried, while
browsing with **Open** found the articles.

**Work with it:** browse the tree; the search depends on the installation's search setup.

## A command-line import purges the page cache

**Fixed:** since explayouts_core 1.0.10, an `exp:layouts import` that publishes a layout (`--publish`) or writes a rule
purges every page of the page cache itself, on the command line too, as publishing in the editor does. Clearing the
content cache by hand after a command-line import is no longer needed. Drafts, dry runs and refused imports purge
nothing.

## The explayouts helper commands run on their own

**Fixed:** since explayouts 1.4.42, `./console ext:explayouts:block_list <layout_id>`, `layout_info <id>`,
`collection_list <block_id>`, `layout_copy`, `layout_list`, `rule_list <layout_id>` and `share_create` (and the other
helpers of `extension/explayouts/bin/php`) run on their own; they no longer end with `Class "..." not found`.
`-s <siteaccess>` picks the siteaccess. `./console ext:explayouts:layout_info 2` prints, among others:

```text
ID:          2
Identifier:  fd0aa7af77a85a03bf1080018f5db1dc
Name:        Header / Footer
```

`exp:layouts` stays the tool for export and import.

## Template blocks in the header and footer zones

**Seen:** on the media theme, a template block at the root of a `header` zone renders the theme's header and one in a
`footer` zone the theme's footer, whatever its template name, because the theme maps template blocks by place.

**Work with it:** put your own template blocks in other zones ([Your own template blocks](template-blocks.md)).

## Related pages

- [The Layouts editor, a tour](layouts-editor-tour.md)
- [Why doesn't my change appear?](why-doesnt-my-change-appear.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
