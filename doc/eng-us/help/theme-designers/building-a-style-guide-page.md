---
title: "Building a style guide page"
audience: theme-designer
category: theme-design
order: 12
summary: How to build a living style guide inside an Exponential theme - a page that shows every token and component with the theme's real templates and stylesheets, made with a node override and template parts, kept out of navigation and search.
keywords: theme designer, style guide, living style guide, pattern library, component page, override, node remote id, tokens, swatches, design review
type: help
---
# Building a style guide page

This page is for theme designers (with a developer, if you do not write templates yourself) who want a page that
shows every token and component of the theme, drawn by the theme's own templates and stylesheets. Unlike a style
guide in a design tool, this one cannot drift from the code: if a component changes, the page changes. It is the
place where reviews start and where editors can see what the design offers.

## Contents

1. [What the page shows](#what-the-page-shows)
2. [How it is built](#how-it-is-built)
3. [Step by step](#step-by-step)
4. [Showing tokens without inline styles](#showing-tokens-without-inline-styles)
5. [Keeping it out of the way](#keeping-it-out-of-the-way)
6. [Using it](#using-it)

## What the page shows

1. **Tokens:** the color palette with each role name, the type scale, the spacing scale, the radius.
2. **Typography:** every heading level, body text, lists, quotes and tables, as rich text renders them.
3. **Components:** every entry of the [component inventory](component-inventory.md), in each variant and state that
   can be shown statically.
4. **Views:** a few real objects in each view mode (line, card, embed).

## How it is built

The page is a normal content object (for example a folder named "Style guide") whose full view gets its own template
through an override that matches the node's remote id. The override technique is documented in
[Override a single node](../designers/templates/override-a-single-node.md); a remote id keeps the rule working when
the content is moved to another installation.

## Step by step

1. On the development installation, create a folder "Style guide" in a place visitors do not browse (see
   [Keeping it out of the way](#keeping-it-out-of-the-way)). Give it a readable remote id, or ask the developer to
   create it from the theme's content package with one ([Demo content for a theme](../designers/distribution/demo-content.md)).
2. Add the override to your design extension's `settings/override.ini.append.php`:

   ```ini
   [mytheme_style_guide]
   Source=node/view/full.tpl
   MatchFile=full/style_guide.tpl
   Subdir=templates
   Match[node_remote_id]=mytheme-style-guide
   ```

3. Write `design/mytheme/override/templates/full/style_guide.tpl`. Build it from the same template parts the theme
   uses, so the page shows the real markup:

   ```tpl
   {* Style guide: every component, drawn by the theme's own parts *}
   <article class="mytheme-styleguide">
     <h1>{'Style guide'|i18n( 'design/mytheme' )}</h1>

     <section aria-labelledby="sg-buttons">
       <h2 id="sg-buttons">{'Buttons'|i18n( 'design/mytheme' )}</h2>
       {include uri='design:parts/button.tpl' label='Primary' variant='primary'}
       {include uri='design:parts/button.tpl' label='Secondary' variant='secondary'}
     </section>

     <section aria-labelledby="sg-cards">
       <h2 id="sg-cards">{'Cards'|i18n( 'design/mytheme' )}</h2>
       {foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 6 ) ) as $child}
         {node_view_gui content_node=$child view='card'}
       {/foreach}
     </section>
   </article>
   ```

4. Put a few stress-set objects under the style guide folder, so the card section shows real extremes
   ([Designing with real content](designing-with-real-content.md#the-stress-set)).
5. Clear the template override cache, because the template file is new (a developer task:
   [Caching for designers](../designers/theme-design/caching-for-designers.md)), and open the page.

## Showing tokens without inline styles

A swatch page is where inline styles creep in (`style="background: #0b5cad"`). Never do that: it breaks the
no-inline-CSS rule and a strict Content Security Policy. Give each swatch a class instead, in a small stylesheet of
the style guide:

```css
/* design/mytheme/stylesheets/styleguide.css */
.mytheme-swatch            { inline-size: 6rem; block-size: 3rem; border: 1px solid var(--site-line);
                             border-radius: var(--site-radius); }
.mytheme-swatch--ink       { background: var(--site-ink); }
.mytheme-swatch--surface   { background: var(--site-surface); }
.mytheme-swatch--accent    { background: var(--site-accent); }
.mytheme-swatch--muted     { background: var(--site-muted); }
```

Because the swatches use the tokens, switching the page to dark shows the dark palette with no extra work. Load
`styleguide.css` only where it is needed, or accept the few lines in the packed file; do not add a version query to
its link ([No version queries on asset links](../designers/theme-design/no-version-queries-on-asset-links.md)).

## Keeping it out of the way

- Place the folder where navigation fetches do not reach, or in a section the main menu skips.
- Hide it from visitors if the site is public: a section with no anonymous read access is the usual choice, decided
  by the administrator ([Sections, roles and policies](../cms-developers/sections-roles-and-policies.md)).
- Keep it out of search results and sitemaps the same way.

## Using it

- Start every design review here, in light and dark, at the three widths
  ([Working with clients and reviews](working-with-clients-and-reviews.md)).
- Show editors which components and block view types exist.
- Compare against the inventory once per release; anything on the page that is not in the inventory, or the other way
  round, is drift.

## Related pages

- [Component inventory](component-inventory.md)
- [Design tokens](../designers/theme-design/design-tokens.md)
- [Override basics](../designers/templates/override-ini-basics.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
