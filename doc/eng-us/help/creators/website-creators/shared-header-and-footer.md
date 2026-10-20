---
title: "Shared layouts: header, footer and prefooter"
audience: website-creator, editor
category: website-creation
order: 310
summary: Define a header, footer or prefooter once in a shared layout and link the zones of every page layout to it, so a change is made in one place - with the rules for linking, publishing and removing.
keywords: shared layout, linked zone, header, footer, prefooter, link zone, unlink, layouts editor, site-wide blocks, website creator
type: help
---
# Shared layouts: header, footer and prefooter

This page is for website creators who want one header and one footer for every page built with the layouts editor.
A layout marked **shared** is not mapped to pages by a rule; it exists to be borrowed. Any zone of another layout can
be **linked** to one of its zones and then shows that layout's blocks instead of its own
([Shared layouts and linked zones](../../../bc/6.0/LAYOUTS.md#shared-layouts-and-linked-zones)).

![Shared layouts with the Header / Footer layout outlined, the layout other layouts link their header and footer zones to](../../images/creators/shared-layout-header.webp)

## Contents

1. [Why shared layouts](#why-shared-layouts)
2. [Build it](#build-it)
3. [Rules to know](#rules-to-know)
4. [Changing and removing](#changing-and-removing)
5. [Checklist](#checklist)

## Why shared layouts

Without them, every page layout carries its own copy of the footer, and a new phone number means editing every layout.
With them, the footer is defined once; change it, publish, and every page follows.

## Build it

1. **Create the shared layout.** **Layouts > Create**, a layout type whose zones fit (for example `layout_2` with
   `header`, `post_header`, `main`, `pre_footer`, `footer`), identifier `site_header_footer`, and mark it **shared**.
2. **Fill its zones**: the logo, menu and search in `header`; contact details and legal links in `footer`; a
   newsletter teaser in `pre_footer`.
3. **Publish** it. Only published shared layouts can be linked.
4. **Link the zones of each page layout.** Open a page layout, choose its `header` zone and link it to the `header`
   zone of `site_header_footer` (a tour of this step: [Admin tour](../../admin-tour.md)). Do the same for `footer` and
   `pre_footer`.
5. **Publish** the page layout and check a page.

## Rules to know

- A zone links to a zone of a **published** shared layout, never to a draft.
- A linked zone ignores its own blocks while the link is in place; unlinking restores them.
- A zone needs both the layout and the zone name to be linked; the editor sets both.
- Blocks of a published shared layout cannot be changed from a linking layout; open the shared layout, draft it and
  edit there.
- The form-based screen **Shared layouts** (`/explayouts_ui/dashboard`) lists each shared layout with the number of
  zones that link to it.

## Changing and removing

| You want | Do |
|---|---|
| Change the footer everywhere | draft the shared layout, edit, publish |
| One page without the shared footer | unlink that zone in that page's layout |
| Retire a shared layout | check its link count first; unlink every zone, then delete |

## Checklist

- [ ] One shared layout per site (or per brand on a multisite).
- [ ] Every page layout links its header and footer zones.
- [ ] Editors know where the footer is edited.
- [ ] After publishing, the content cache and Velocity's response cache are clear, so every page shows the change.

## Related pages

- [Landing pages with the layouts editor](landing-pages-with-layouts.md)
- [Layout types, zones and blocks](layout-types-zones-and-blocks.md)
- [The layouts editor (for editors)](../../layouts-editor.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
