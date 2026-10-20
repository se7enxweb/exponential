---
title: "Launch examples, every page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1199
summary: The 33 examples of the launch phase of a site's life, each one task done in the Layouts editor and with templates. Map every page type, fill the menus, check the pages and the caches, keep a baseline.
keywords: launch, examples, life cycle, layouts, templates, two ways to build
type: help
---
# Launch examples, every page

Map every page type, fill the menus, check the pages and the caches, keep a baseline.

1. [Give the home page its layout](map-the-home-page.md): A Location mapping to the site root node: on Fit & Healthy, Landing Page / Home on Location 89 at priority 210.
2. [Give every article the article layout](map-a-class.md): A Subtree mapping with a Class condition: Full / Article on Subtree 89 for ng_article, ng_blog_post, ng_news at priority 130.
3. [Give one page its own layout](map-a-single-page.md): A Location (or Path) mapping with a high priority beats the class mapping for one page.
4. [A plain layout for the legal pages](map-legal-pages.md): Privacy Policy and Cookie Policy are articles but use Full / Article (Plain): a mapping with two Location targets and the article class condition at priority 150.
5. [A layout for every other page](catch-all-layout.md): Default Layout catches the rest: a Path prefix "/" mapping at priority 10, and [ResolverSettings] DefaultLayout as the fallback.
6. [Check that every layout links the header and footer](link-every-header-and-footer.md): Before launch, each page layout's header and footer zones must link to Header / Footer; exp:layouts list shows it.
7. [Fill the header menu for launch](fill-the-header-menu.md): Write the final MainMenuID[] list in one edit (administration) or a clear plus adds (console).
8. [Fill both footer lines for launch](fill-the-footer.md): FooterMenuID[] for the first line, Menu items in the Footer links folder for the second.
9. [Set the social media links](social-links.md): The footer's social icons come from the fields facebook, twitter, instagram and linkedin of the site info object; each icon shows only when its field has content.
10. [Fill the prefooter for launch](prefooter-on-launch.md): Pick the launch articles of the shared Prefooter in the Layouts editor; it shows on articles and recipes.
11. [Check every page type by its markup](check-pages-by-markup.md): Layouts and templates leave markers in the HTML (zone sections, ngl-block classes, menu item IDs) that a script can check before launch.
12. [Clear the caches once before launch](clear-caches-before-launch.md): After the last template and settings changes, one Template & content clear, then a warm-up of the main pages.
13. [Take a baseline export at launch](baseline-export.md): Export every layout, shared layout and mapping on launch day, to compare and restore later.
14. [Make sure no draft is left open](no-open-drafts.md): Unpublished drafts are invisible to visitors but confuse the next editor; list them before launch.
15. [Check that pages come from the page cache](http-cache-check.md): The response header X-Exp-Cache tells HIT, MISS or MISS (purged); check the main pages after launch.
16. [The header menu of a second language](menu-language-version.md): A German siteaccess (bold_ger, starter_ger on this installation) has its own menu.ini; write its list in its own scope.
17. [A different layout for one language](layout-per-language.md): A mapping with a Siteaccess condition gives the German version of a page type its own layout.
18. [The contact page](contact-page-launch.md): Contact (node 185, class Contact form) is a form page; give it a layout with a map and text around the form.
19. [Launch the blog list page](launch-the-blog.md): A landing page with a dynamic List of NG Blog posts, mapped by Location; the posts use the article layout already.
20. [The Layouts part of a launch checklist](launch-checklist-layouts.md): Mappings, links, drafts, shared layouts, export: the five checks of the Layouts side before go-live.
21. [The template part of a launch checklist](launch-checklist-templates.md): Extension order, overrides, styles, menus, caches: the checks of the template side before go-live.
22. [Keep test pages out of the launch](robots-and-hidden-pages.md): Test layouts and pages built during the project: unmap or delete them before go-live.
23. [A last-minute header menu change on launch day](launch-day-header-change.md): The quickest safe path: exp:ini add with --dry-run first, then the cache clear; about two commands.
24. [Launch on Exponential Velocity](launch-on-velocity.md): On Velocity, settings changes need exp:velocity restart (as exp:ini says); plan it into the launch steps.
25. [Hand the site over to the editors](editor-handover.md): Show editors the three places they work: the content tree, the Layouts editor and the footer links folder.
26. [Launch the section landing pages](launch-landing-pages.md): Fitness, Healthy eating, Running, Video and Showcase each have a Location mapping to their own landing page layout.
27. [Old addresses after a relaunch](redirect-old-addresses.md): URL aliases and wildcards are content settings, not layouts; keep old addresses working when pages move.
28. [Add an analytics tag at launch](launch-metrics-tags.md): The theme has a Google Tag Manager include (parts/google_tag_manager_code_script.tpl and _noscript.tpl); measurement code belongs in templates, not in HTML snippet blocks.
29. [Measure the launch pages](launch-performance-check.md): Time the main pages cached and uncached, as on the test copy, and keep the numbers.
30. [Preview a layout before it goes live](layout-preview-before-launch.md): Layouts > Layouts and Shared layouts have Preview for each layout; the editor's middle area previews the draft.
31. [Prepare a mapping and switch it on at launch](disable-a-mapping-for-launch.md): Create the launch mapping with Enabled off, then tick Enabled at go-live.
32. [Launch a second site on the same installation](launch-second-site.md): Bold Agency and Starter run beside Fit & Healthy here: their own root nodes, siteaccesses, menus and layouts, with mappings by subtree and siteaccess.
33. [Tell editors what changed at launch](go-live-communication.md): List what moved from templates to Layouts (or back), so editors know where to change things after go-live.

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
