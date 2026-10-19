---
title: Known editor behaviors
audience: editor, website-creator, admin, designer, developer
category: build-two-ways
order: 280
summary: Behaviors of the Layouts editor and its tools seen while writing these pages on Exponential 6.0.15+, each with what was observed and how to work with it - view type changes, item positions, search in the content browser, imports and the page cache, and broken helper scripts.
keywords: known issues, behaviors, layouts editor, view type not saved, js-skip-on-change, collection item position, content browser search, import cache, block_list, layout_info, collection_list, workaround
type: help
---
# Known editor behaviors

Each entry was observed on a copy of a live installation while writing this help. They are reported so you know what
to expect; check the release notes of your version, as they may be fixed there.

## A view type change alone is not saved

**Seen:** on the Design tab, choosing another **View type** changed the form, but the draft kept the old view type;
after **Publish layout** the block was unchanged. The select carries the class `js-skip-on-change`, which the editor's
script uses to skip saving on that change.

**Work with it:** after choosing the view type, change any other field of the same form (type and delete a character
in **CSS class**): the whole form is saved, view type included. The block header then shows the new view type in
capitals.

## Two items with position 0

**Seen:** after **+ Add items** added a fourth article to a manual collection of three, the published collection had
positions 0, 0, 1, 2 (the new item and the first one both 0). The page showed all four.

**Work with it:** if the order on the site is not the order in the editor, drag the items once more into the order
you want before publishing.

## The content browser's search

**Seen:** in **Add content to collection**, the search field returned "No content found." for the word tried, while
browsing with **Open** found the articles.

**Work with it:** browse the tree; the search depends on the installation's search setup.

## A command-line import keeps the old page in the cache

**Seen:** `exp:layouts import ... --strategy=overwrite --publish` wrote and published the layout, but pages kept coming
from the page cache (`X-Exp-Cache: HIT`) with the old blocks. Publishing in the editor purged them by itself
(`MISS (purged)`).

**Work with it:** after a command-line import, `./console exp:cache clear --tag=content`.

## Some explayouts helper commands stop with an error

**Seen:** `./console ext:explayouts:block_list <id>`, `ext:explayouts:layout_info <id>` and
`ext:explayouts:collection_list <id>` ended with `PHP Fatal error: Uncaught Error: Class "..." not found`.

**Work with it:** use `./console exp:layouts list` and `exp:layouts export --only=<identifier>` (the archive's
`layouts.json` has zones, blocks, parameters and items).

## Template blocks in the header and footer zones

**Seen:** on the media theme, a template block at the root of a `header` zone renders the theme's header and one in a
`footer` zone the theme's footer, whatever its template name, because the theme maps template blocks by place.

**Work with it:** put your own template blocks in other zones ([Your own template blocks](template-blocks.md)).

## Related pages

- [The Layouts editor, a tour](layouts-editor-tour.md)
- [Why doesn't my change appear?](why-doesnt-my-change-appear.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
