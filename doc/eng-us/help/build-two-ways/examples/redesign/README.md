---
title: "Redesign examples, every page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1399
summary: The 34 examples of the redesign phase of a site's life, each one task done in the Layouts editor and with templates. Change the look and the structure: overrides, new view and block types, new shared zones.
keywords: redesign, examples, life cycle, layouts, templates, two ways to build
type: help
---
# Redesign examples, every page

Change the look and the structure: overrides, new view and block types, new shared zones.

1. [New markup for every grid](override-the-grid.md): Override the media design's list grid template in your design extension; every List block in grid view changes.
2. [A new look for section titles](override-section-titles.md): The Title block dispatches by view type to explayouts/block/title/<view type>.tpl; override section_centered for the Prefooter style.
3. [Rebuild the header markup](new-header-markup.md): Override pagelayout/header.tpl (and its parts) in your design extension; the shared layout keeps placing it.
4. [Rebuild the footer markup](new-footer-markup.md): Override pagelayout/footer.tpl; keep its data sources (FooterMenuID, site info, Footer links) or change them.
5. [Replace the fixed header menu by the tree](menu-from-the-tree.md): Override pagelayout/header/main_menu.tpl to list the children of the root node by class; tested code.
6. [A new look for lists, chosen by editors](new-list-look.md): Add a view type (one INI line, one template) instead of changing the existing ones.
7. [A new component block for the redesign](new-component-block.md): A new block type for a component the old design did not have, defined in INI with an existing handler.
8. [Restyle blocks without touching markup](restyle-without-markup.md): Most redesigns need only CSS on the wrapper classes ngl-<type>, ngl-vt-<view type>, bg-color-*, whitespace-*.
9. [Move a page type to a sidebar layout](change-a-page-layout-type.md): A layout's type cannot be switched in place: create a layout of the new type, rebuild, remap.
10. [Move blocks to another zone](move-blocks-between-zones.md): Drag blocks between zones in the editor; between layouts, rebuild or use export and import.
11. [A new shared band for many page types](new-shared-band.md): Create a shared layout (Single zone) for a band such as a newsletter sign-up, and link a zone of each page layout to it.
12. [Show the Prefooter on more page types](prefooter-everywhere.md): Link the pre_footer zone of more layouts to Prefooter's main zone.
13. [Give one page type its own footer](unlink-a-zone.md): Unlink the footer zone of one layout and build its own blocks there.
14. [Turn a hand-picked list into a self-filling one](manual-to-dynamic.md): Change the collection type to Dynamic; the manual items are removed (the editor warns), the query fills the list.
15. [Turn a template-built page into a layout](template-page-to-layout.md): Split the page's template into blocks: reusable parts as template blocks, the rest as standard blocks.
16. [Freeze a block into a template](layout-block-to-template.md): When a block's content stops changing and must not be edited, move its markup into a template block.
17. [A new look for one class in lists](new-item-view.md): Add content/views/<view>/<class>.tpl in your extension; lists with that item view type use it for that class.
18. [Retire a view type after a redesign](retire-a-view-type.md): Remove it from ViewTypes[] only after no block uses it; find them in an export.
19. [Do the redesign on a copy](redesign-on-a-copy.md): Templates in a branch of your extension, layouts in a sandbox copy; then deploy the templates and import the layouts.
20. [Restyle the hero](restyle-the-hero.md): Override explayouts/block/hero.tpl from Layouts' standard design in your design extension.
21. [Change the breadcrumb](breadcrumb-redesign.md): The breadcrumb is a template block in the header zone, rendered by pagelayout/breadcrumbs.tpl; override it.
22. [No breadcrumb on landing pages](remove-the-breadcrumb-somewhere.md): Landing page layouts link the shared header including the breadcrumb; unlink and rebuild, or hide it by CSS on those layouts.
23. [Change the language selector](language-selector-redesign.md): Override pagelayout/header/language_selector.tpl in your design extension.
24. [Change the header's user menu](user-menu-redesign.md): The user menu (Sign in, Register, basket, administration ...) is pagelayout/header/user_menu.tpl and menu.ini [HeaderUserMenu].
25. [Change the header search](search-box-redesign.md): Override pagelayout/header/search_box.tpl; the form posts to /content/search.
26. [Change the logo](logo-change.md): The logo is content/parts/site_logo.tpl and the site info object's site_logo field.
27. [Redesign the landing pages in the editor](redesign-landing-pages-in-editor.md): Landing pages are layouts: rearrange their blocks, change view types and item views - no deploy.
28. [A different background per layout](zone-style-per-layout.md): Put a Column (with background color and container) as the first block of a zone to give one layout its own band style.
29. [Use the component style variants](component-styles.md): The exp_component_* blocks come with style view types (hero_style_1 to 3, features_style_1 to 7, about_style_1 to 5, logos_style_1 and 2, lead_style_1 and 2, quote_style_1).
30. [Print styles for articles](print-styles.md): Print rules belong in your stylesheet (@media print) on the classes the templates write.
31. [Dark color scheme for the site](dark-mode-site.md): CSS with prefers-color-scheme on the design's classes; no template or layout change.
32. [Make a redesign faster](performance-redesign.md): Fewer blocks per layout and cached pages: measure before and after.
33. [Check accessibility after a redesign](redesign-accessibility-check.md): Headings, alt texts, contrast, keyboard: check templates and the blocks editors filled.
34. [Roll out a redesign step by step](redesign-rollout.md): New templates behind new view types first, then switch blocks page by page, then retire the old looks.

## Related pages

- [Life-cycle examples](../README.md)
- [Two ways to build](../../two-ways-to-build.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
