---
title: "Maintain examples, every page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1299
summary: The 35 examples of the maintain phase of a site's life, each one task done in the Layouts editor and with templates. Change lists, banners, menus and campaigns week by week, and recover from mistakes.
keywords: maintain, examples, life cycle, layouts, templates, two ways to build
type: help
---
# Maintain examples, every page

Change lists, banners, menus and campaigns week by week, and recover from mistakes.

1. [Swap one article in a hand-picked list](swap-a-teaser.md): Remove an item from a manual collection and add another in its place, in the Layouts editor.
2. [Reorder a hand-picked list](reorder-a-list.md): Drag the items of a manual collection into a new order; check the order after publishing.
3. [Point a dynamic list at another section](change-a-dynamic-list-source.md): Change the Parent location of a dynamic collection; the list fills from the new section at once.
4. [Show more items in a list](show-more-items.md): Offset & number of items (dynamic collections) or more manual items; Enable pagination for long lists.
5. [Replace the hero image](replace-the-hero-image.md): Change the Image URL and Image alt text of the Hero block; or upload a new image to the content the template reads.
6. [Change a button's text and target](change-a-call-to-action.md): Edit Text and link of a Button / Link block, or Link text and Link URL of a Hero.
7. [Rename a header menu item](rename-a-menu-item.md): The header menu prints the node's name: rename the page in the content tree and publish.
8. [Take an item out of the header menu](remove-a-menu-item.md): Delete its line from MainMenuID[] (administration) or exp:ini rem (console), then Template & content.
9. [Move a header menu item to the front](move-a-menu-item.md): Reorder the lines of MainMenuID[] in the administration, or exp:ini clear and add in the new order.
10. [Add a link to the footer's second line](add-a-footer-link.md): Create a Menu item in Media > Configuration > Footer links with Title and Item URL; no cache clear.
11. [Hide a footer link for a while](hide-a-footer-link.md): Hide the Menu item in the content tree; the footer lists only what the visitor may see.
12. [Change a social profile address](update-social-profile.md): Edit the matching field (facebook, twitter, instagram, linkedin) of the site info object.
13. [Swap a seasonal layout by hand](seasonal-swap-by-hand.md): Two mappings for the same page, the seasonal one with a higher priority: tick or untick Enabled to switch.
14. [Swap a seasonal layout by date](seasonal-swap-by-time.md): A Time condition (from, to) on the seasonal mapping switches it at its start and end without anyone clicking.
15. [Put up a temporary notice](temporary-notice.md): An Alert block in the page's post_header zone, removed again after the event.
16. [Rotate the featured item of a grid](rotate-the-featured-item.md): In a grid_featured list, the first item is the large one: drag another item to the top.
17. [Change the number of columns of a grid](change-columns.md): Number of columns on the Design tab: 2, 3, 4 or 6.
18. [Change a list from grid to list](change-a-view-type.md): View type on the Design tab - with the quirk that the change is stored with the next change of another field.
19. [Give a section a background color](change-a-background.md): Set background color on the Design tab; the wrapper gets bg-color-<color>.
20. [Adjust the spacing of a section](adjust-spacing.md): Use vertical whitespace on top and bottom (Design tab) instead of Spacer blocks.
21. [Throw away a draft that went wrong](discard-a-bad-draft.md): Discard removes the draft; the published layout is untouched.
22. [Undo a published layout change](undo-a-published-change.md): Import the export taken before the change with --strategy=overwrite --publish; the import purges the page cache itself.
23. [Export layouts every week](weekly-export.md): A scheduled exp:layouts export --all keeps a history of the layouts.
24. [Find forgotten drafts](find-forgotten-drafts.md): exp:layouts list --drafts lists every open draft; open each and publish or discard.
25. [A change did not show](change-did-not-show.md): Draft not published, linked zone, another layout, settings in the wrong file, compiled template, page cache, Velocity: the ten checks.
26. [A new section joins the header menu](menu-item-for-new-section.md): Publish the section page, find its node ID, add it to MainMenuID[], clear Template & content.
27. [Retire a campaign page](retire-a-campaign.md): Disable its mapping, keep the layout for next year, hide or archive the page.
28. [Reuse last year's campaign](recycle-last-years-campaign.md): Import last year's export as a copy (--strategy=copy), change the texts, map it.
29. [Keep block texts in two languages](translate-block-texts.md): Texts typed into blocks belong to the layout; for a second language, a layout per siteaccess, or text from translated content.
30. [Check the links typed into blocks](check-links-in-blocks.md): Links typed into Button, Hero, Card and Image blocks do not follow moved pages; check them after restructuring.
31. [Update the privacy or cookie policy](update-legal-pages.md): Edit the article (Privacy Policy, node 90; Cookie Policy, node 91); their layout and footer links follow.
32. [A monthly look at the caches](monthly-cache-sanity.md): exp:cache list shows every cache; look for growth and make sure page caching still works.
33. [When an editor changed the shared layout by mistake](editor-changed-shared-layout.md): A change to Header / Footer or Prefooter shows on many pages; roll it back from the last export.
34. [A new recipe appears in the lists](add-a-recipe-to-the-index.md): Publish the recipe below Recipes; dynamic lists show it, manual lists need it added.
35. [Keep the site documentation current](keep-the-plan-current.md): After each change of menus, mappings or overrides, update the site guide written at planning time.

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
