# explayouts_ui (the Layouts admin screens): chronicle

The Layouts admin screens were imported on 30 July 2026. In August the rule list became the layout mappings screen with details panel and quick actions; in September the lists were paged, the preview rendered real content and the screens were fixed for phones, MongoDB and persistent workers. See the [feature page](../../features/6.0/extensions/explayouts_ui.md).

This page lists **every one of the 55 changes** of the repository `explayouts_ui` between 2026-07-30 and 2026-09-30, by month, with what kind of change each is. Read it to find out when a behaviour arrived and which release you need for it. The complete machine-made record, with sizes, is the [change ledger](../ledger/explayouts_ui.md); what each release contains is in the [release notes](../../changelogs/extensions/explayouts_ui.md); how to use the extension is on its [feature page](../../features/6.0/extensions/explayouts_ui.md).

| Kind | Changes |
|---|---|
| feature | 34 |
| fix | 10 |
| upgrade note | 3 |
| docs | 2 |
| tooling | 1 |
| release | 4 |
| no user benefit | 1 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2026-07-30 | v1.0.0 | [`497ecd8`](https://github.com/se7enxweb/explayouts_ui/commit/497ecd8) |
| 2026-09-07 | v1.1.0 | [`9673451`](https://github.com/se7enxweb/explayouts_ui/commit/9673451) |
| 2026-09-15 | v1.2.0 | [`bdb8b44`](https://github.com/se7enxweb/explayouts_ui/commit/bdb8b44) |
| 2026-09-17 | v1.2.1 | [`cf1e932`](https://github.com/se7enxweb/explayouts_ui/commit/cf1e932) |
| 2026-09-19 | v1.2.2 | [`60de39e`](https://github.com/se7enxweb/explayouts_ui/commit/60de39e) |
| 2026-09-20 | v1.2.3 | [`1a1bc79`](https://github.com/se7enxweb/explayouts_ui/commit/1a1bc79) |
| 2026-09-22 | v1.3.0 | [`6ec73b2`](https://github.com/se7enxweb/explayouts_ui/commit/6ec73b2) |
| 2026-09-27 | v1.3.1 | [`35bae6c`](https://github.com/se7enxweb/explayouts_ui/commit/35bae6c) |
| 2026-09-27 | v1.3.2 | [`55379a7`](https://github.com/se7enxweb/explayouts_ui/commit/55379a7) |
| 2026-09-28 | v1.3.3 | [`12003cb`](https://github.com/se7enxweb/explayouts_ui/commit/12003cb) |
| 2026-09-29 | v1.3.4 | [`7fc0aaf`](https://github.com/se7enxweb/explayouts_ui/commit/7fc0aaf) |
| 2026-09-30 | v1.3.5 | [`40084e1`](https://github.com/se7enxweb/explayouts_ui/commit/40084e1) |
| 2026-09-30 | v1.3.6 | [`3b98c33`](https://github.com/se7enxweb/explayouts_ui/commit/3b98c33) |

## Timeline

### 2026-07

The month across all extensions: [July 2026](months/2026-07.md). [Ledger of this month](../ledger/explayouts_ui.md#2026-07-5-changes).

- 2026-07-30 [`392dff9`](https://github.com/se7enxweb/explayouts_ui/commit/392dff9) (feature) Initial import. Import of explayouts_ui extension providing exponential Layouts UI - Admin user interface for Exponential Layouts.
- 2026-07-30 [`54b3bae`](https://github.com/se7enxweb/explayouts_ui/commit/54b3bae) (docs) Updated README.md to the classic 7x documentation standard with a doc/ index and expanded usage information.
- 2026-07-30 [`aa294a9`](https://github.com/se7enxweb/explayouts_ui/commit/aa294a9) (fix) Fixed: Fixed the composer.json license to the SPDX identifier GPL-2.0-or-later required by Packagist.
- 2026-07-30 [`f7d4c04`](https://github.com/se7enxweb/explayouts_ui/commit/f7d4c04) (tooling) Added .gitattributes with export-ignore rules to keep composer dist archives lean.
- 2026-07-30 [`497ecd8`](https://github.com/se7enxweb/explayouts_ui/commit/497ecd8) (feature) Set white leftmenu background for section_id_7 dashboard **Release v1.0.0.**

### 2026-08

The month across all extensions: [August 2026](months/2026-08.md). [Ledger of this month](../ledger/explayouts_ui.md#2026-08-24-changes).

- 2026-08-27 [`bd10b7c`](https://github.com/se7enxweb/explayouts_ui/commit/bd10b7c) (feature) Material Icons font assets for admin layout UI
- 2026-08-28 [`7dcd433`](https://github.com/se7enxweb/explayouts_ui/commit/7dcd433) (feature) rule list with layout/target/condition details and inline save panel
- 2026-08-28 [`2dac252`](https://github.com/se7enxweb/explayouts_ui/commit/2dac252) (feature) rule list UI to match Nexus layout resolver rule list
- 2026-08-28 [`82d2c02`](https://github.com/se7enxweb/explayouts_ui/commit/82d2c02) (feature) rule list template visibility, layout wrapper, and icon font overrides.
- 2026-08-28 [`dc2bb42`](https://github.com/se7enxweb/explayouts_ui/commit/dc2bb42) (feature) rule condition type dropdown options.
- 2026-08-28 [`d9b2962`](https://github.com/se7enxweb/explayouts_ui/commit/d9b2962) (feature) rule detail layout icon and target content browser.
- 2026-08-28 [`b4098b1`](https://github.com/se7enxweb/explayouts_ui/commit/b4098b1) (fix) Fixed: rule detail layout icon src and content browser URL escaping.
- 2026-08-28 [`b715050`](https://github.com/se7enxweb/explayouts_ui/commit/b715050) (fix) Fixed: layout detail image height so SVG icon is visible.
- 2026-08-28 [`3ffe820`](https://github.com/se7enxweb/explayouts_ui/commit/3ffe820) (feature) condition dropdown keeps used ibexa condition types.
- 2026-08-28 [`ab3866d`](https://github.com/se7enxweb/explayouts_ui/commit/ab3866d) (feature) styled enabled/disabled toggle in rule details.
- 2026-08-28 [`029af55`](https://github.com/se7enxweb/explayouts_ui/commit/029af55) (feature) remove ibexa-prefixed condition options from rule UI.
- 2026-08-28 [`678ca69`](https://github.com/se7enxweb/explayouts_ui/commit/678ca69) (feature) New rule opens the modern rule details popin.
- 2026-08-28 [`079ccae`](https://github.com/se7enxweb/explayouts_ui/commit/079ccae) (upgrade note) content_node target type to node in rule list and detail templates.
- 2026-08-28 [`f8b489f`](https://github.com/se7enxweb/explayouts_ui/commit/f8b489f) (feature) rule list can be preloaded from a Map layout or Edit mapping link via TargetType/TargetValue and RuleID query parameters.
- 2026-08-28 [`2e016af`](https://github.com/se7enxweb/explayouts_ui/commit/2e016af) (feature) rule list and rule detail "Edit layout" links now open the modern layout SPA.
- 2026-08-28 [`1e807eb`](https://github.com/se7enxweb/explayouts_ui/commit/1e807eb) (upgrade note) top admin tab from "Exponential Layouts UI" to "Layouts".
- 2026-08-28 [`036f4cb`](https://github.com/se7enxweb/explayouts_ui/commit/036f4cb) (feature) dashboard and layout lists now use modern SPA editor links and a labeled-button UI.
- 2026-08-28 [`f902903`](https://github.com/se7enxweb/explayouts_ui/commit/f902903) (upgrade note) condition type "content_type" now uses the Exponential 4 term "class" in rule list UI.
- 2026-08-28 [`9d50fe4`](https://github.com/se7enxweb/explayouts_ui/commit/9d50fe4) (feature) layout list grid is now responsive so cards no longer overflow the admin container.
- 2026-08-28 [`d865269`](https://github.com/se7enxweb/explayouts_ui/commit/d865269) (feature) layout list sorting controls are now a single bar with the same height as the New layout button.
- 2026-08-28 [`c222fd2`](https://github.com/se7enxweb/explayouts_ui/commit/c222fd2) (feature) layout list header now places sorting bar on the left and the New layout button on the right.
- 2026-08-28 [`ff3ba57`](https://github.com/se7enxweb/explayouts_ui/commit/ff3ba57) (feature) sorting dropdowns on layout list are now capped at 120px and the sorting bar padding is reduced so it sits compactly next to the New layout button.
- 2026-08-28 [`dc94dd8`](https://github.com/se7enxweb/explayouts_ui/commit/dc94dd8) (feature) added 4px left padding to the sort icon label in the layout list sorting bar.
- 2026-08-28 [`15e6a2d`](https://github.com/se7enxweb/explayouts_ui/commit/15e6a2d) (feature) sort icon label now has equal left and right padding inside the sorting bar.

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/explayouts_ui.md#2026-09-26-changes).

- 2026-09-02 [`6c36efd`](https://github.com/se7enxweb/explayouts_ui/commit/6c36efd) (feature) explayouts_ui components list view and template.
- 2026-09-02 [`12a86b4`](https://github.com/se7enxweb/explayouts_ui/commit/12a86b4) (feature) register components view and add side nav link.
- 2026-09-02 [`19cd484`](https://github.com/se7enxweb/explayouts_ui/commit/19cd484) (feature) components list CSS padding, column widths and responsive card layout.
- 2026-09-02 [`e844193`](https://github.com/se7enxweb/explayouts_ui/commit/e844193) (feature) components list name links to view, add traditional edit icon column.
- 2026-09-02 [`af712d6`](https://github.com/se7enxweb/explayouts_ui/commit/af712d6) (feature) component name links use nice URL alias instead of content/view/full.
- 2026-09-02 [`9573f09`](https://github.com/se7enxweb/explayouts_ui/commit/9573f09) (feature) rule list View in CMS link uses system URL content/view/full for node targets.
- 2026-09-02 [`a633075`](https://github.com/se7enxweb/explayouts_ui/commit/a633075) (feature) Set content_info viewmode to 'layout_preview' in layout preview.
- 2026-09-07 [`63a53ba`](https://github.com/se7enxweb/explayouts_ui/commit/63a53ba) (feature) Added layout preview rendering against a representative content node, resolved from the mapping's own target, so content driven blocks show real content instead of rendering empty. A dedicated full view template breaks the circular reference the module result would otherwise produce.
- 2026-09-07 [`9673451`](https://github.com/se7enxweb/explayouts_ui/commit/9673451) (feature) Renamed the rule administration to layout mappings throughout the list and detail screens, and rebuilt the forms to offer content classes and siteaccesses as dropdowns instead of free text, add a per mapping cache clear, and scaffold a new mapping with a priority that actually resolves. Drops the obsolete legacy condition typ... **Release v1.1.0.**
- 2026-09-15 [`bdb8b44`](https://github.com/se7enxweb/explayouts_ui/commit/bdb8b44) (feature) Added paging to the layout, shared layout, component and rule lists, which drew every row they had on one screen. **Release v1.2.0.**
- 2026-09-17 [`cf1e932`](https://github.com/se7enxweb/explayouts_ui/commit/cf1e932) (feature) Updated the shared layouts list to read the shared flag rather than infer it from what links to a layout, so a shared layout nothing links to yet is listed. **Release v1.2.1.**
- 2026-09-19 [`60de39e`](https://github.com/se7enxweb/explayouts_ui/commit/60de39e) (fix) Fixed: Fixed the component usage and shared layout listings showing nothing and no reference counts on MongoDB, caused by a JOIN and a GROUP BY the driver cannot translate, so both admin screens report what is actually there. **Release v1.2.2.**
- 2026-09-20 [`1a1bc79`](https://github.com/se7enxweb/explayouts_ui/commit/1a1bc79) (fix) Fixed: Fixed the New layout button and the sorting controls running off the edge of the layout list on a phone, caused by a header row that could not wrap around selects that could not shrink, so the whole header now stays inside the screen. **Release v1.2.3.**
- 2026-09-22 [`6ec73b2`](https://github.com/se7enxweb/explayouts_ui/commit/6ec73b2) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.3.0.**
- 2026-09-27 [`35bae6c`](https://github.com/se7enxweb/explayouts_ui/commit/35bae6c) (fix) Fixed: Load more works in the layout preview **Release v1.3.1.**
- 2026-09-27 [`55379a7`](https://github.com/se7enxweb/explayouts_ui/commit/55379a7) (fix) Fixed: The ezinfo.php declares its keys as strings, so the about page shows the extension's name, version and license **Release v1.3.2.**
- 2026-09-28 [`9edfda2`](https://github.com/se7enxweb/explayouts_ui/commit/9edfda2) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`12003cb`](https://github.com/se7enxweb/explayouts_ui/commit/12003cb) (release) Version 1.3.3 **Release v1.3.3.**
- 2026-09-28 [`869e68e`](https://github.com/se7enxweb/explayouts_ui/commit/869e68e) (no user benefit) GitHub funding metadata, the same as the other se7enxweb packages
- 2026-09-29 [`c3ecea9`](https://github.com/se7enxweb/explayouts_ui/commit/c3ecea9) (feature) The top menu tab and its tooltip have German translations
- 2026-09-29 [`7fc0aaf`](https://github.com/se7enxweb/explayouts_ui/commit/7fc0aaf) (release) Version 1.3.4 **Release v1.3.4.**
- 2026-09-30 [`b838c3b`](https://github.com/se7enxweb/explayouts_ui/commit/b838c3b) (docs) The about page names the extension Exponential Layouts UI
- 2026-09-30 [`40084e1`](https://github.com/se7enxweb/explayouts_ui/commit/40084e1) (release) Version 1.3.5 **Release v1.3.5.**
- 2026-09-30 [`379fe88`](https://github.com/se7enxweb/explayouts_ui/commit/379fe88) (fix) Fixed: The admin Layouts pages load their stylesheets in the page head, so the sidebar is painted styled at once
- 2026-09-30 [`107ccf2`](https://github.com/se7enxweb/explayouts_ui/commit/107ccf2) (fix) Fixed: The dark sidebar and hidden scrollbars of the Layouts pages no longer reach the admin's node views
- 2026-09-30 [`3b98c33`](https://github.com/se7enxweb/explayouts_ui/commit/3b98c33) (release) Version 1.3.6 **Release v1.3.6.**

### Added after the ledger was cut (1 to 2 October 2026)

These commits were pushed after the machine-made ledger of this repository was extracted; they are listed here so the page stays complete.

- 2026-10-01 `0c6083f` (fix) The rule list's layout actions condition no longer logs a parser error on every view (v1.3.7)
- 2026-10-01 `3e56537` (fix) The Roboto fonts ship with the extension (v1.3.7)
- 2026-10-01 `667e862` (feature) The Components page follows the renamed `exp_component_<type>` blocks (v1.3.7)
- 2026-10-01 `e3d2723` (feature) The layouts admin pages run on jQuery 4 and carry Exponential file names (v1.3.7)
- 2026-10-02 `d21be9c` (fix) The stylesheet's page-wide rules no longer reset node views (v1.3.8)
- Releases v1.3.7 (1 October) and v1.3.8 (2 October)

## Related pages

- [Feature page](../../features/6.0/extensions/explayouts_ui.md)
- [Release notes](../../changelogs/extensions/explayouts_ui.md)
- [Change ledger](../ledger/explayouts_ui.md)
- [Specification](../../specifications/6.0/explayouts-ui-api.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
