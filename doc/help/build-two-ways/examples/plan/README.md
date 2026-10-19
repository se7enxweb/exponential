---
title: "Plan examples, every page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 999
summary: The 35 examples of the plan phase of a site's life, each one task done in the Layouts editor and with templates. Decide page types, layouts, menus, languages and who changes what, before building.
keywords: plan, examples, life cycle, layouts, templates, two ways to build
type: help
---
# Plan examples, every page

Decide page types, layouts, menus, languages and who changes what, before building.

1. [Make an inventory of the page types](inventory-page-types.md): Before building, list which kinds of pages the site has and which layout each gets: the content classes in the tree and the layout mappings.
2. [Choose the layout type for a page type](choose-a-layout-type.md): The layout type fixes the zones: Header / Main / Footer (layout_2) for most Fit & Healthy pages, Header / Left / Right / Footer (layout_4) for a sidebar.
3. [Plan the shared zones first](shared-zones-first.md): Decide which zones every page shares (header, footer) and which some share (prefooter), before building page layouts.
4. [Decide who changes what](who-changes-what.md): Split the site between editors (content and Layouts) and developers (templates, settings) before building, so each change has an owner.
5. [Prototype a page in a draft](prototype-in-a-draft.md): Build the layout in the editor, look at it, and throw it away with Discard: nothing is published until Publish layout.
6. [Wireframe a page with titles and spacers](wireframe-with-placeholders.md): Lay out the sections of a new page with Title blocks and Spacers before the real content exists.
7. [Plan which lists are hand-picked and which fill themselves](manual-or-dynamic.md): For every list, decide manual collection (editors pick) or dynamic collection (a query) before building.
8. [Plan how items look in lists](item-view-types.md): Item view types (Standard, Overlay, Card, Line, Mini, List item, with or without intro) decide each item's look; plan them per list.
9. [Plan the header and footer menus](plan-the-menus.md): On the Fit & Healthy theme the header menu and the footer's first line are menu.ini lists and the footer's second line is content; plan who maintains which.
10. [Plan a second language](language-versions.md): Each language version is a siteaccess with its own settings (menus) and can get its own layouts through a Siteaccess condition.
11. [Plan the mapping priorities](mapping-priorities.md): The first enabled mapping by priority whose targets and conditions match wins; plan priorities so specific mappings come first.
12. [Plan your design extension](design-extension-plan.md): One extension for everything site-specific: templates, stylesheets, settings, translations - named, versioned and activated in the right place.
13. [List the block types you will need](new-block-types-needed.md): Compare the page designs with the block types the editor offers; plan new block types only for what is missing.
14. [Plan which cache each change needs](caching-plan.md): Write down per kind of change which cache to clear, so launches and updates do not end in "it does not show".
15. [Plan how layouts are kept and versioned](layouts-under-version-control.md): Layouts live in the database; plan regular exports (exp:layouts export) so they can be compared, restored and moved.
16. [Inventory layouts and settings from the console](inventory-from-the-console.md): exp:layouts list and exp:ini where give a complete, scriptable picture of an installation's layouts and menu settings.
17. [Collect the node IDs the menus will use](node-ids-for-menus.md): Menus and manual collections refer to node IDs; collect them (and their remote IDs) when the tree is built.
18. [Plan campaigns with start and end dates](campaign-calendar.md): A mapping can have a Time condition (from, to); plan campaign layouts with it, knowing the resolver remembers answers for up to an hour.
19. [Plan A/B variants of a page](ab-variants.md): The Query parameter condition exists, but the resolver remembers its answer per path, so one address cannot show two layouts at once; plan variants on two addresses.
20. [Plan accessibility into the blocks](accessibility-of-blocks.md): Use the fields the blocks have for it - Image alt text, Heading level, link texts - and keep one h1 per page.
21. [Plan image sizes](image-sizes.md): Galleries take a thumbnail size alias, templates an image class; plan which sizes the design needs.
22. [Plan the forms](forms-plan.md): Forms on Fit & Healthy are content (Contact form, Feedback form, Lead form classes), shown by their full views; layouts place them.
23. [Plan a blog section](blog-section.md): Blog posts (class NG Blog post) already share the article mapping on Fit & Healthy; plan the listing page and its lists.
24. [Plan a recipes section](recipes-section.md): Recipes have their own layout (Full / Recipe, layout_4 with a sidebar) and their own mapping; plan the category page and its lists.
25. [Plan topic pages](topic-pages.md): Topics (class Topic: Running, Workout, Health, HIIT) have the layout Full / Topic; plan what a topic page lists.
26. [Plan a video section](video-section.md): Video content (NG Video) lives below Video (node 164) and gets Full / Video; plan the landing page's lists.
27. [Plan where galleries come from](gallery-plan.md): Gallery blocks read images from a folder; gallery content items (NG Gallery) are stories of their own. Plan which is which.
28. [Plan moving layouts from staging to production](staging-to-production.md): Build on a staging installation, then export and import the layouts; content is matched by remote ID and path.
29. [Plan a sandbox for risky changes](sandbox-testing.md): Try changes that must be published to be seen on a copy of the installation, never on the live site.
30. [Plan to measure the work](measure-the-work.md): Count the steps and time the waiting parts of typical changes, as on these pages, to compare the two ways on your own site.
31. [Plan the footer links folder](footer-links-folder.md): The footer's second line is the children of one folder (remote ID in menu.ini [SiteInfo] FooterLinksRemoteID); plan its items and their order.
32. [Plan who publishes layouts](publishing-rules-for-layouts.md): A layout is changed in a draft and published as a whole; plan who may publish shared layouts, as they change many pages at once.
33. [Plan the content model before the layouts](content-model-first.md): Layouts arrange content; the classes and fields come first. Check what the existing classes offer.
34. [Plan for speed](performance-budget.md): Cached pages answered in 22 to 102 ms on the test copy, a page after a full template clear in about 3 s; plan launches and cache clears with that in mind.
35. [Plan the site's own documentation](document-the-site.md): Write down the layouts, mappings, menus and templates of the site in the same structure as these pages, so the next person can change them.

## Related pages

- [Life-cycle examples](../README.md)
- [Two ways to build](../../two-ways-to-build.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
