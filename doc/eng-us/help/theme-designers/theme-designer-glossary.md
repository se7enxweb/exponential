---
title: "Theme designer glossary"
audience: theme-designer
category: theme-design
order: 43
summary: The terms theme designers meet in this section that the Design glossary does not already define - image alias, view type, layout type, shared layout, remote id, preview siteaccess, skin, ramp, role token, state matrix and others - each with a link to where it is explained.
keywords: theme designer, glossary, terms, definitions, image alias, view type, layout type, remote id, preview siteaccess, skin, ramp, role token, design engine
type: help
---
# Theme designer glossary

This page is for theme designers who want a quick definition of a word. It adds the terms used in the theme designer
pages that the [Design glossary](../designers/theme-design/design-glossary.md) does not already cover. For attribute,
block, cascade, class, datatype, design, node, object, override, packer, pagelayout, siteaccess, token, view mode,
zone and the other core words, read that glossary first.

## Contents

1. [Content and templates](#content-and-templates)
2. [Layouts](#layouts)
3. [Images and media](#images-and-media)
4. [Design system](#design-system)
5. [Process](#process)
6. [Exponential Platform DXP](#exponential-platform-dxp)

## Content and templates

**Field description.** A short text per field of a content class, shown next to the field in the edit form. A place
for guidance to editors. See [Designing for editors](designing-for-editors.md#telling-editors-what-the-design-expects).

**Information collection.** Fields of a content class that collect input from visitors, which is how feedback and
contact forms are usually built. See [Designing forms](designing-forms.md).

**Locale.** The language and regional format of a siteaccess, which decides how dates and numbers are printed. See
[Dates, numbers and l10n](../designers/templates/dates-numbers-and-l10n.md).

**Preview siteaccess.** A siteaccess that serves the live content with a design under development, at its own
address. See [Previewing a design on a siteaccess](../designers/theme-design/previewing-a-design-on-a-siteaccess.md).

**Remote id.** A text identifier of an object or node that stays the same when content is installed elsewhere; used to
match overrides to one specific page. See [Override a single node](../designers/templates/override-a-single-node.md).

**Section.** A label on a part of the content tree, used for access rights and for overrides by area. See
[Override by section](../designers/templates/override-by-section.md).

**Template path comments.** HTML comments around every rendered template, naming its file, switched on with
`ShowTemplatePathComments` on a development siteaccess. See [Template path comments](../designers/templates/template-path-comments.md).

**Template part.** A small template included by others, the usual home of a component's markup. See
[Component inventory](component-inventory.md#from-inventory-to-code).

## Layouts

**Block type.** A kind of layout block (text, list, gallery, hero ...), defined in the layouts settings with its
handler and view types. See [Block definitions](../designers/theme-design/block-definitions.md).

**Collection.** The content a list-like block shows, picked by hand or found by a query. See
[Layouts for designers](../designers/theme-design/layouts-for-designers.md).

**Container block.** A block that holds other blocks in named places, such as two or three columns. See
[Designing layout blocks](designing-layout-blocks.md#containers).

**Layout type.** A page skeleton with named zones, chosen when a layout is created. See
[Layout types and zones](../designers/theme-design/layout-types-and-zones.md).

**Shared layout.** A layout whose zones are linked into many other layouts, often for header or pre-footer content.
See [Shared layouts and linked zones](../designers/theme-design/shared-layouts-and-linked-zones.md).

**View type.** One of the looks a block type offers; editors choose it per block. A template and a settings line, no
PHP. See [Designing layout blocks](designing-layout-blocks.md#block-types-and-view-types).

## Images and media

**Art direction.** Showing a different crop or image at different screen widths. See
[Designing image crops and art direction](designing-image-crops-and-art-direction.md#art-direction).

**Image alias.** A named, generated variant of an uploaded image, made by filters such as "scale down to fit". See
[Image aliases](../designers/theme-design/image-aliases.md).

**Open Graph tags.** The `og:*` tags in a page's head that social networks read to build a preview card. See
[Social share and metadata images](social-share-and-metadata-images.md).

**Safe area.** The central part of a share image that survives any crop a network applies. See
[Social share and metadata images](social-share-and-metadata-images.md#the-share-image).

**Skin (newsletter).** The pair of templates (HTML part and text part) that gives newsletter mails their look in the
CJW Newsletter extension. See [Designing newsletter emails](designing-newsletter-emails.md).

## Design system

**Brand matrix.** A table of what varies per brand in a multi-brand installation. See
[Designing for several brands in multisite](designing-for-several-brands-in-multisite.md#the-brand-matrix).

**Component inventory.** The table of every reusable piece of a theme with its variants, states, tokens, template and
class. See [Component inventory](component-inventory.md).

**Ramp.** A brand or neutral color in about ten lightness steps, from which role tokens are picked. See
[Color palettes and ramps](color-palettes-and-ramps.md).

**Role token.** A token named after its use (`--site-ink`, `--site-accent`) rather than its value. See
[Design tokens](../designers/theme-design/design-tokens.md#naming).

**State matrix.** A table of the interactive states of each component, drawn in light and dark. See
[Designing interactive states](designing-interactive-states.md#the-state-matrix).

**Type scale.** The short list of font sizes a theme uses. See [Type scale](type-scale.md).

## Process

**Handoff.** Giving a design to the people who build it, with everything they need written down. See
[Handoff checklist](handoff-checklist.md).

**Mapping sheet.** The table that connects each element of a mockup to a class, field and view. See
[Mapping a design to content classes](mapping-a-design-to-content-classes.md#the-mapping-sheet).

**Stress set.** Test objects with extreme content that every design must survive. See
[Designing with real content](designing-with-real-content.md#the-stress-set).

**Style guide page.** A page of the site that shows every token and component drawn by the theme's own code. See
[Building a style guide page](building-a-style-guide-page.md).

**View matrix.** A table of content classes against view modes, marking which combinations need a design. See
[Designing view modes](designing-view-modes.md#the-view-matrix).

## Exponential Platform DXP

**Design (DXP).** An ordered list of themes; the first theme that has a template wins. See
[Twig templates and the design engine](../dxp-developers/twig-templates-and-the-design-engine.md).

**@ibexadesign.** The Twig namespace through which DXP templates refer to theme templates, resolved by the design
engine. See [Themes for Exponential Platform](themes-for-exponential-platform.md).

**Theme (DXP).** A folder of Twig templates under `templates/themes/<theme>/`. See
[The standard design and your theme](../dxp-developers/the-standard-design-and-your-theme.md).

## Related pages

- [Design glossary](../designers/theme-design/design-glossary.md)
- [Theme designer FAQ](theme-designer-faq.md)
- [Theme designers: start here](theme-designers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
