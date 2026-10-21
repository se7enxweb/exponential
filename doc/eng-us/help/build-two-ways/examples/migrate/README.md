---
title: "Migrate examples, every page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1499
summary: The 34 examples of the migrate phase of a site's life, each one task done in the Layouts editor and with templates. Move layouts, settings and templates between installations, versions and sites.
keywords: migrate, examples, life cycle, layouts, templates, two ways to build
type: help
---
# Migrate examples, every page

Move layouts, settings and templates between installations, versions and sites.

1. [Move all layouts to another installation](layouts-to-another-installation.md): exp:layouts export --all on the source, import with a dry run on the target; content is matched by remote ID and path.
2. [Move only some layouts](only-some-layouts.md): --only with the layout identifiers; the shared layouts they link to come along.
3. [Move the mappings with the layouts](mappings-with-layouts.md): --rules=none|selected|all decides which layout mappings travel; selected (the default) takes those of the chosen layouts.
4. [When the target lacks some content](missing-content-on-target.md): --unmapped=drop|keep|fail decides what happens to references to content the target does not have (default drop).
5. [Import beside the existing layouts](import-as-copy.md): --strategy=copy imports items that already exist as new copies, leaving the originals.
6. [Choose the strategy per layout](per-item-strategy.md): --item=<key>=<strategy>,... sets skip, overwrite or copy per item, with the keys the dry run prints.
7. [Keep layouts in the project repository](layouts-in-git.md): Commit exports next to the design extension; layouts.json in the archive can be compared between versions.
8. [Move a site's settings into its extension](settings-into-extension.md): exp:ini move / move-all move blocks or whole siteaccess scopes into an extension (--create-extension --activate), with a dry run.
9. [Copy the menu settings to another siteaccess](menus-to-another-site.md): exp:ini copy copies a setting (a whole array with its reset line) from one scope to another.
10. [Change the settings files of another root](settings-on-another-root.md): exp:ini --root=<dir> works on the settings tree of another root (a copy of the installation); the INI cache there is not cleared, and the scope must exist there.
11. [Update stored component block identifiers](component-block-identifiers.md): Layouts from before the rename store components as ibexa_component_<type>; ext:explayouts:updatecomponentblockidentifiers renames them to exp_component_<type>.
12. [Update stored block links to remote IDs](link-location-references.md): ext:explayouts:updatelinklocationreferences converts block links from ibexa-location://<id> to exp-remote-location://<remote id>.
13. [Use exp:layouts instead of the old helper scripts](helper-scripts-replaced.md): The ext:explayouts:* helpers (block_list, layout_info, layout_copy, layout_list, rule_list, share_create, collection_list) run on their own again since explayouts 1.4.42; exp:layouts stays the tool for export and import.
14. [Migrate a template-built site to Layouts](template-site-to-layouts.md): Keep the design; turn the page layout's parts into template blocks of a shared Header / Footer; add page layouts per class; map them.
15. [Freeze a Layouts site into templates](layouts-site-to-templates.md): When a site must stop changing (an archive), its layouts can stay; freezing means fewer editors, not rewriting.
16. [Add a language and give it layouts](new-siteaccess-for-language.md): A new siteaccess gets its settings (menu.ini) by exp:ini copy and its own layouts by mappings with a Siteaccess condition.
17. [Move content to another place in the tree](move-content-keep-layouts.md): A move keeps node IDs: menus, Location mappings and manual collections follow; Path and Path prefix targets do not.
18. [Site packages and layouts](package-and-layouts.md): The site packages of this installation carry content but no layouts; install the layouts with exp:layouts import after the content.
19. [Check layouts after an upgrade](upgrade-check-layouts.md): After upgrading Exponential, export, list, open the main layouts in the editor and compare the pages.
20. [Check your template overrides after an upgrade](template-overrides-after-upgrade.md): Your copies of theme templates do not get the theme's fixes; compare them with the new theme versions.
21. [Check the extension order after moving settings](extension-order-after-migration.md): After moving settings or adding extensions, check DesignExtensions[] and AvailableBlocks[] in effect.
22. [Different node IDs on staging and production](staging-content-ids.md): Node IDs differ between installations; menus (MainMenuID) need the target's IDs, layouts are matched by remote ID and path.
23. [Rename layouts without breaking imports](rename-a-layout-identifier.md): Imports match layouts by identifier; keep identifiers stable and change names freely.
24. [Split one installation into two sites](split-a-site.md): Export the layouts of the site's subtree, import them on the new installation after its content.
25. [Merge layouts from two installations](merge-two-sites.md): Import the second site's layouts with --strategy=copy where identifiers clash, then rename and remap.
26. [Rescue a layout broken by an import](rescue-a-broken-layout.md): Import the previous export with overwrite and publish; the import purges the page cache itself.
27. [Move a hard-coded menu into settings](migrate-hardcoded-menu.md): An old template with links typed into it becomes a MainMenuID[] list read by the theme's main_menu.tpl.
28. [Replace copied HTML snippets by a block type](migrate-snippets-to-block-type.md): Many HTML snippet blocks with the same markup become one block type; replace them one by one.
29. [Move inline styles into the stylesheet](move-styles-out-of-templates.md): style="" and <style> in old templates go into your stylesheet, packed with the theme's.
30. [Write down what the migration changed](document-the-migration.md): List moved settings, imported layouts and their strategies, overrides added or removed - for the next migration.
31. [Velocity after a migration](velocity-after-migration.md): On a site served by Exponential Velocity, settings changes need exp:velocity restart, as exp:ini says.
32. [Rehearse the migration on a copy](test-migration-on-a-copy.md): Run every command against a copy first; the dry runs on live then hold no surprises.
33. [Plan the way back](migration-rollback-plan.md): Exports of the layouts, backups of the INI files (exp:ini keeps them in var/backup/ini/) and the previous extension release.
34. [The checklist after a migration](after-migration-checklist.md): Pages by markup, menus, mappings, drafts, caches, Velocity: the checks after moving.

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
