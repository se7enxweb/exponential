---
title: "Designing for editors"
audience: theme-designer
category: theme-design
order: 8
summary: Design rules that keep an Exponential theme working when editors, not designers, fill it - variable text length, optional fields, images of any shape, rich text, translations, and the field descriptions that guide editors in the edit form.
keywords: theme designer, editors, robust design, optional fields, variable length, empty fields, rich text, field description, editorial guidance, content-proof design
type: help
---
# Designing for editors

This page is for theme designers whose themes will be filled by editors. After launch, the people who decide what a
page contains are the editors, not you. A design that only works with the content you chose will look broken within
weeks. This page lists the rules that make a design editor-proof, and how to tell editors what the design expects.

## Contents

1. [Who fills the design](#who-fills-the-design)
2. [Rule 1: every text grows](#rule-1-every-text-grows)
3. [Rule 2: every optional field has an empty design](#rule-2-every-optional-field-has-an-empty-design)
4. [Rule 3: images come in any shape](#rule-3-images-come-in-any-shape)
5. [Rule 4: rich text is a design surface](#rule-4-rich-text-is-a-design-surface)
6. [Rule 5: lists have any number of items](#rule-5-lists-have-any-number-of-items)
7. [Rule 6: no design depends on editors remembering it](#rule-6-no-design-depends-on-editors-remembering-it)
8. [Telling editors what the design expects](#telling-editors-what-the-design-expects)

## Who fills the design

Editors write in the administration or the editor interface, preview a draft, and publish
([Editing content](../editing-content.md), [Publishing and versions](../publishing-and-versions.md)). On sites with
layouts they also arrange blocks ([The layouts editor](../layouts-editor.md)). They are not designers, they work
under time pressure, and they work in several languages. Design for that.

## Rule 1: every text grows

- Headlines wrap; decide the maximum number of lines and what happens after (usually nothing: let it wrap).
- Buttons and labels hold translated text, often a third longer than English.
- Never fit text into a fixed-height box. Use `min-height` if a minimum is needed.
- Truncation (`text-overflow: ellipsis` or a line clamp) is a design decision for lists and cards only, never for
  the full view, and the full text must stay reachable.

## Rule 2: every optional field has an empty design

For each optional field in your [mapping sheet](mapping-a-design-to-content-classes.md#the-mapping-sheet), draw the
layout without it. Typical answers:

| Field missing | Design answer |
|---|---|
| Image on a card | a text-only card variant, not a gray placeholder box |
| Intro | the card shows title and date only |
| Author | no byline line at all, not "By ." |
| Caption | the image without a caption area |
| Body | the full view ends after the intro, with no empty section |

The template then wraps each optional element in a condition, so empty wrappers never reach the page
([If and conditions](../designers/templates/if-and-conditions.md)).

## Rule 3: images come in any shape

Editors upload portraits, panoramas, screenshots and logos. Image aliases scale an image down to fit a box; decide
per slot whether the image is shown whole (letterboxed in its slot) or cut to a fixed ratio with `object-fit: cover`
in the stylesheet, and accept that cutting may lose faces or text. See
[Designing image crops and art direction](designing-image-crops-and-art-direction.md).

## Rule 4: rich text is a design surface

Rich text fields let editors use headings, lists, tables, quotes, links, embedded objects and custom tags. Each needs
a style, and each must work next to the others. Design a "kitchen sink" article with every element and keep it in
your stress set ([Designing with real content](designing-with-real-content.md#the-stress-set)). Templates for custom
tags and embeds: [Rich text templates](../designers/templates/rich-text-templates.md),
[Custom tags templates](../designers/templates/custom-tags-templates.md), [Embed view](../designers/templates/embed-view.md).

## Rule 5: lists have any number of items

A three-column grid with two items, a slider with one slide, a list of 400 news items: design for zero, one, a few
and many. Long lists need pagination ([Pagination](../designers/templates/pagination.md)); empty lists need an empty
state ([Designing empty and error states](designing-empty-and-error-states.md)).

## Rule 6: no design depends on editors remembering it

If an element only looks right when the editor uploads a 1600 by 900 image, writes exactly two lines, or picks the
right color class, it will look wrong. Prefer designs where the template enforces the rule (an alias, a ratio, a
fixed set of block view types) over rules editors must remember.

## Telling editors what the design expects

Where guidance helps, put it where editors see it:

- **Field descriptions.** A content class can carry a description per field, shown next to the field in the edit
  form. Ask the developer to add short guidance there: "Shown on cards; 60 characters or fewer reads best." The admin
  edit template prints it from the class attribute's description.
- **Block view types.** In layouts, a view type with a clear name ("Cards, three columns") is guidance in itself
  ([Designing layout blocks](designing-layout-blocks.md)).
- **A style guide page** on the site that shows each component with real content
  ([Building a style guide page](building-a-style-guide-page.md)).
- **Training.** The editor courses are in [Editors 101](../trainers/editors-101/README.md).

## Related pages

- [Designing with real content](designing-with-real-content.md)
- [Accessibility basics for themes](../designers/theme-design/accessibility-basics-for-themes.md)
- [Common template mistakes](../designers/templates/common-template-mistakes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
