# explayouts_ui (the Layouts admin screens): release notes

What each release of `explayouts_ui` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/explayouts_ui.md); the story is in the [chronicle](../../history/extensions/explayouts_ui.md).

## v1.3.8 (2026-10-02)

**Fixed**

- The Layouts stylesheet's page-wide rules no longer reset the whole admin page on node views (`d21be9c`; one rule in `design/admin/stylesheets/netgen/layouts-admin.css`)

## v1.3.7 (2026-10-01)

**Updated**

- The layouts admin pages run on the admin's jQuery 4 from ezjscore and carry Exponential names: `layouts-ibexa.js` and `layouts-ibexa.css` are now `layouts-exponential.js` and `layouts-exponential.css`, `nglayouts-ui.css` is `explayouts-ui.css`, and the pages no longer load `layouts-admin.js` (`e3d2723`). If your own template overrides load the old file names, change them
- The Components page lists the usages of the renamed `exp_component_<type>` blocks; an installation shows them once the `updatecomponentblockidentifiers.php` script of `explayouts` has renamed its stored blocks (`667e862`)

**Fixed**

- The Roboto fonts the layouts stylesheet points to ship in `design/admin/stylesheets/media`, so the layouts pages show their fonts instead of 404 errors (`3e56537`)
- The rule list's layout actions use a condition the template language supports; before, a parser error was logged on every view and the actions did not show for an editor (`0c6083f`)

## v1.3.6 (2026-09-30)

**Updated**

- Fixed: The admin Layouts pages load their stylesheets in the page head, so the sidebar is painted styled at once ([`379fe88`](https://github.com/se7enxweb/explayouts_ui/commit/379fe88))
- Fixed: The dark sidebar and hidden scrollbars of the Layouts pages no longer reach the admin's node views ([`107ccf2`](https://github.com/se7enxweb/explayouts_ui/commit/107ccf2))

1 version, merge or metadata commit not listed.

## v1.3.5 (2026-09-30)

**Maintenance, documentation and packaging**

- The about page names the extension Exponential Layouts UI ([`b838c3b`](https://github.com/se7enxweb/explayouts_ui/commit/b838c3b))

1 version, merge or metadata commit not listed.

## v1.3.4 (2026-09-29)

**Updated**

- The top menu tab and its tooltip have German translations ([`c3ecea9`](https://github.com/se7enxweb/explayouts_ui/commit/c3ecea9))

2 version, merge or metadata commits not listed.

## v1.3.3 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`9edfda2`](https://github.com/se7enxweb/explayouts_ui/commit/9edfda2))

1 version, merge or metadata commit not listed.

## v1.3.2 (2026-09-27)

**Updated**

- Fixed: The ezinfo.php declares its keys as strings, so the about page shows the extension's name, version and license ([`55379a7`](https://github.com/se7enxweb/explayouts_ui/commit/55379a7))

## v1.3.1 (2026-09-27)

**Updated**

- Fixed: Load more works in the layout preview ([`35bae6c`](https://github.com/se7enxweb/explayouts_ui/commit/35bae6c))

## v1.3.0 (2026-09-22)

**Updated**

- Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. ([`6ec73b2`](https://github.com/se7enxweb/explayouts_ui/commit/6ec73b2))

## v1.2.3 (2026-09-20)

**Updated**

- Fixed: Fixed the New layout button and the sorting controls running off the edge of the layout list on a phone, caused by a header row that could not wrap around selects that could not shrink, so the whole header now stays inside the screen. ([`1a1bc79`](https://github.com/se7enxweb/explayouts_ui/commit/1a1bc79))

## v1.2.2 (2026-09-19)

**Updated**

- Fixed: Fixed the component usage and shared layout listings showing nothing and no reference counts on MongoDB, caused by a JOIN and a GROUP BY the driver cannot translate, so both admin screens report what is actually there. ([`60de39e`](https://github.com/se7enxweb/explayouts_ui/commit/60de39e))

## v1.2.1 (2026-09-17)

**Updated**

- Updated the shared layouts list to read the shared flag rather than infer it from what links to a layout, so a shared layout nothing links to yet is listed. ([`cf1e932`](https://github.com/se7enxweb/explayouts_ui/commit/cf1e932))

## v1.2.0 (2026-09-15)

**Added**

- Added paging to the layout, shared layout, component and rule lists, which drew every row they had on one screen. ([`bdb8b44`](https://github.com/se7enxweb/explayouts_ui/commit/bdb8b44))

## v1.1.0 (2026-09-07)

**Added**

- Material Icons font assets for admin layout UI ([`bd10b7c`](https://github.com/se7enxweb/explayouts_ui/commit/bd10b7c))
- styled enabled/disabled toggle in rule details. ([`ab3866d`](https://github.com/se7enxweb/explayouts_ui/commit/ab3866d))
- rule list can be preloaded from a Map layout or Edit mapping link via TargetType/TargetValue and RuleID query parameters. ([`f8b489f`](https://github.com/se7enxweb/explayouts_ui/commit/f8b489f))
- explayouts_ui components list view and template. ([`6c36efd`](https://github.com/se7enxweb/explayouts_ui/commit/6c36efd))
- Added layout preview rendering against a representative content node, resolved from the mapping's own target, so content driven blocks show real content instead of rendering empty. A dedicated full view template breaks the circular reference the module result would otherwise produce. ([`63a53ba`](https://github.com/se7enxweb/explayouts_ui/commit/63a53ba))

**Updated**

- rule list with layout/target/condition details and inline save panel ([`7dcd433`](https://github.com/se7enxweb/explayouts_ui/commit/7dcd433))
- rule list UI to match Nexus layout resolver rule list ([`2dac252`](https://github.com/se7enxweb/explayouts_ui/commit/2dac252))
- rule list template visibility, layout wrapper, and icon font overrides. ([`82d2c02`](https://github.com/se7enxweb/explayouts_ui/commit/82d2c02))
- rule condition type dropdown options. ([`dc2bb42`](https://github.com/se7enxweb/explayouts_ui/commit/dc2bb42))
- rule detail layout icon and target content browser. ([`d9b2962`](https://github.com/se7enxweb/explayouts_ui/commit/d9b2962))
- Fixed: rule detail layout icon src and content browser URL escaping. ([`b4098b1`](https://github.com/se7enxweb/explayouts_ui/commit/b4098b1))
- Fixed: layout detail image height so SVG icon is visible. ([`b715050`](https://github.com/se7enxweb/explayouts_ui/commit/b715050))
- condition dropdown keeps used ibexa condition types. ([`3ffe820`](https://github.com/se7enxweb/explayouts_ui/commit/3ffe820))
- remove ibexa-prefixed condition options from rule UI. ([`029af55`](https://github.com/se7enxweb/explayouts_ui/commit/029af55))
- New rule opens the modern rule details popin. ([`678ca69`](https://github.com/se7enxweb/explayouts_ui/commit/678ca69))
- rule list and rule detail "Edit layout" links now open the modern layout SPA. ([`2e016af`](https://github.com/se7enxweb/explayouts_ui/commit/2e016af))
- dashboard and layout lists now use modern SPA editor links and a labeled-button UI. ([`036f4cb`](https://github.com/se7enxweb/explayouts_ui/commit/036f4cb))
- layout list grid is now responsive so cards no longer overflow the admin container. ([`9d50fe4`](https://github.com/se7enxweb/explayouts_ui/commit/9d50fe4))
- layout list sorting controls are now a single bar with the same height as the New layout button. ([`d865269`](https://github.com/se7enxweb/explayouts_ui/commit/d865269))
- layout list header now places sorting bar on the left and the New layout button on the right. ([`c222fd2`](https://github.com/se7enxweb/explayouts_ui/commit/c222fd2))
- sorting dropdowns on layout list are now capped at 120px and the sorting bar padding is reduced so it sits compactly next to the New layout button. ([`ff3ba57`](https://github.com/se7enxweb/explayouts_ui/commit/ff3ba57))
- added 4px left padding to the sort icon label in the layout list sorting bar. ([`dc94dd8`](https://github.com/se7enxweb/explayouts_ui/commit/dc94dd8))
- sort icon label now has equal left and right padding inside the sorting bar. ([`15e6a2d`](https://github.com/se7enxweb/explayouts_ui/commit/15e6a2d))
- register components view and add side nav link. ([`12a86b4`](https://github.com/se7enxweb/explayouts_ui/commit/12a86b4))
- components list CSS padding, column widths and responsive card layout. ([`19cd484`](https://github.com/se7enxweb/explayouts_ui/commit/19cd484))
- components list name links to view, add traditional edit icon column. ([`e844193`](https://github.com/se7enxweb/explayouts_ui/commit/e844193))
- component name links use nice URL alias instead of content/view/full. ([`af712d6`](https://github.com/se7enxweb/explayouts_ui/commit/af712d6))
- rule list View in CMS link uses system URL content/view/full for node targets. ([`9573f09`](https://github.com/se7enxweb/explayouts_ui/commit/9573f09))
- Set content_info viewmode to 'layout_preview' in layout preview. ([`a633075`](https://github.com/se7enxweb/explayouts_ui/commit/a633075))
- Renamed the rule administration to layout mappings throughout the list and detail screens, and rebuilt the forms to offer content classes and siteaccesses as dropdowns instead of free text, add a per mapping cache clear, and scaffold a new mapping with a priority that actually resolves. Drops the obsolete legacy condition typ... ([`9673451`](https://github.com/se7enxweb/explayouts_ui/commit/9673451))

**Renamed**

- content_node target type to node in rule list and detail templates. ([`079ccae`](https://github.com/se7enxweb/explayouts_ui/commit/079ccae)) Upgrade note.
- top admin tab from "Exponential Layouts UI" to "Layouts". ([`1e807eb`](https://github.com/se7enxweb/explayouts_ui/commit/1e807eb)) Upgrade note.
- condition type "content_type" now uses the Exponential 4 term "class" in rule list UI. ([`f902903`](https://github.com/se7enxweb/explayouts_ui/commit/f902903)) Upgrade note.

## v1.0.0 (2026-07-30)

**Added**

- Initial import. Import of explayouts_ui extension providing exponential Layouts UI - Admin user interface for Exponential Layouts. ([`392dff9`](https://github.com/se7enxweb/explayouts_ui/commit/392dff9))

**Updated**

- Fixed: Fixed the composer.json license to the SPDX identifier GPL-2.0-or-later required by Packagist. ([`aa294a9`](https://github.com/se7enxweb/explayouts_ui/commit/aa294a9))
- Set white leftmenu background for section_id_7 dashboard ([`497ecd8`](https://github.com/se7enxweb/explayouts_ui/commit/497ecd8))

**Maintenance, documentation and packaging**

- Updated README.md to the classic 7x documentation standard with a doc/ index and expanded usage information. ([`54b3bae`](https://github.com/se7enxweb/explayouts_ui/commit/54b3bae))
- Added .gitattributes with export-ignore rules to keep composer dist archives lean. ([`f7d4c04`](https://github.com/se7enxweb/explayouts_ui/commit/f7d4c04))

## Related

* [Feature page](../../features/6.0/extensions/explayouts_ui.md)
* [Chronicle](../../history/extensions/explayouts_ui.md)
* [Change ledger](../../history/ledger/explayouts_ui.md)
* [Specification](../../specifications/6.0/explayouts-ui-api.md)
* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)

## See also

* months: [2026-07](../../history/extensions/months/2026-07.md), [2026-08](../../history/extensions/months/2026-08.md), [2026-09](../../history/extensions/months/2026-09.md)
