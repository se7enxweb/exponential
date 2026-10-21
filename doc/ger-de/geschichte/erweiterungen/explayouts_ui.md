# explayouts_ui (die Layouts-Seiten der Administration): Chronik

Die Layouts-Seiten der Administration wurden am 30. Juli 2026 importiert. Im August wurde aus der Regelliste die Seite der Layout-Zuordnungen mit Detailbereich und Schnellaktionen; im September wurden die Listen seitenweise angezeigt, die Vorschau zeigte echte Inhalte, und die Seiten wurden für Telefone, MongoDB und dauerhaft laufende Worker korrigiert. Siehe die [Funktionsseite](../../../eng-us/features/6.0/extensions/explayouts_ui.md).

Diese Seite führt **jede der 55 Änderungen** des Repositorys `explayouts_ui` zwischen 2026-07-30 und 2026-09-30 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/explayouts_ui.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/explayouts_ui.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../../eng-us/features/6.0/extensions/explayouts_ui.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 34 |
| Fehlerbehebung | 10 |
| Upgrade-Hinweis | 3 |
| Dokumentation | 2 |
| Werkzeuge | 1 |
| Release | 4 |
| ohne Nutzen für Anwender | 1 |

## Releases

| Datum | Release | Commit |
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

## Zeitleiste

### 2026-07

Der Monat über alle Erweiterungen: [Juli 2026](monate/2026-07.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/explayouts_ui.md#2026-07-5-änderungen).

- 2026-07-30 [`392dff9`](https://github.com/se7enxweb/explayouts_ui/commit/392dff9) (Funktion) Initial import. Import of explayouts_ui extension providing exponential Layouts UI - Admin user interface for Exponential Layouts.
- 2026-07-30 [`54b3bae`](https://github.com/se7enxweb/explayouts_ui/commit/54b3bae) (Dokumentation) Updated README.md to the classic 7x documentation standard with a doc/ index and expanded usage information.
- 2026-07-30 [`aa294a9`](https://github.com/se7enxweb/explayouts_ui/commit/aa294a9) (Fehlerbehebung) Fixed: Fixed the composer.json license to the SPDX identifier GPL-2.0-or-later required by Packagist.
- 2026-07-30 [`f7d4c04`](https://github.com/se7enxweb/explayouts_ui/commit/f7d4c04) (Werkzeuge) Added .gitattributes with export-ignore rules to keep composer dist archives lean.
- 2026-07-30 [`497ecd8`](https://github.com/se7enxweb/explayouts_ui/commit/497ecd8) (Funktion) Set white leftmenu background for section_id_7 dashboard **Release v1.0.0.**

### 2026-08

Der Monat über alle Erweiterungen: [August 2026](monate/2026-08.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/explayouts_ui.md#2026-08-24-änderungen).

- 2026-08-27 [`bd10b7c`](https://github.com/se7enxweb/explayouts_ui/commit/bd10b7c) (Funktion) Material Icons font assets for admin layout UI
- 2026-08-28 [`7dcd433`](https://github.com/se7enxweb/explayouts_ui/commit/7dcd433) (Funktion) rule list with layout/target/condition details and inline save panel
- 2026-08-28 [`2dac252`](https://github.com/se7enxweb/explayouts_ui/commit/2dac252) (Funktion) rule list UI to match Nexus layout resolver rule list
- 2026-08-28 [`82d2c02`](https://github.com/se7enxweb/explayouts_ui/commit/82d2c02) (Funktion) rule list template visibility, layout wrapper, and icon font overrides.
- 2026-08-28 [`dc2bb42`](https://github.com/se7enxweb/explayouts_ui/commit/dc2bb42) (Funktion) rule condition type dropdown options.
- 2026-08-28 [`d9b2962`](https://github.com/se7enxweb/explayouts_ui/commit/d9b2962) (Funktion) rule detail layout icon and target content browser.
- 2026-08-28 [`b4098b1`](https://github.com/se7enxweb/explayouts_ui/commit/b4098b1) (Fehlerbehebung) Fixed: rule detail layout icon src and content browser URL escaping.
- 2026-08-28 [`b715050`](https://github.com/se7enxweb/explayouts_ui/commit/b715050) (Fehlerbehebung) Fixed: layout detail image height so SVG icon is visible.
- 2026-08-28 [`3ffe820`](https://github.com/se7enxweb/explayouts_ui/commit/3ffe820) (Funktion) condition dropdown keeps used ibexa condition types.
- 2026-08-28 [`ab3866d`](https://github.com/se7enxweb/explayouts_ui/commit/ab3866d) (Funktion) styled enabled/disabled toggle in rule details.
- 2026-08-28 [`029af55`](https://github.com/se7enxweb/explayouts_ui/commit/029af55) (Funktion) remove ibexa-prefixed condition options from rule UI.
- 2026-08-28 [`678ca69`](https://github.com/se7enxweb/explayouts_ui/commit/678ca69) (Funktion) New rule opens the modern rule details popin.
- 2026-08-28 [`079ccae`](https://github.com/se7enxweb/explayouts_ui/commit/079ccae) (Upgrade-Hinweis) content_node target type to node in rule list and detail templates.
- 2026-08-28 [`f8b489f`](https://github.com/se7enxweb/explayouts_ui/commit/f8b489f) (Funktion) rule list can be preloaded from a Map layout or Edit mapping link via TargetType/TargetValue and RuleID query parameters.
- 2026-08-28 [`2e016af`](https://github.com/se7enxweb/explayouts_ui/commit/2e016af) (Funktion) rule list and rule detail "Edit layout" links now open the modern layout SPA.
- 2026-08-28 [`1e807eb`](https://github.com/se7enxweb/explayouts_ui/commit/1e807eb) (Upgrade-Hinweis) top admin tab from "Exponential Layouts UI" to "Layouts".
- 2026-08-28 [`036f4cb`](https://github.com/se7enxweb/explayouts_ui/commit/036f4cb) (Funktion) dashboard and layout lists now use modern SPA editor links and a labeled-button UI.
- 2026-08-28 [`f902903`](https://github.com/se7enxweb/explayouts_ui/commit/f902903) (Upgrade-Hinweis) condition type "content_type" now uses the Exponential 4 term "class" in rule list UI.
- 2026-08-28 [`9d50fe4`](https://github.com/se7enxweb/explayouts_ui/commit/9d50fe4) (Funktion) layout list grid is now responsive so cards no longer overflow the admin container.
- 2026-08-28 [`d865269`](https://github.com/se7enxweb/explayouts_ui/commit/d865269) (Funktion) layout list sorting controls are now a single bar with the same height as the New layout button.
- 2026-08-28 [`c222fd2`](https://github.com/se7enxweb/explayouts_ui/commit/c222fd2) (Funktion) layout list header now places sorting bar on the left and the New layout button on the right.
- 2026-08-28 [`ff3ba57`](https://github.com/se7enxweb/explayouts_ui/commit/ff3ba57) (Funktion) sorting dropdowns on layout list are now capped at 120px and the sorting bar padding is reduced so it sits compactly next to the New layout button.
- 2026-08-28 [`dc94dd8`](https://github.com/se7enxweb/explayouts_ui/commit/dc94dd8) (Funktion) added 4px left padding to the sort icon label in the layout list sorting bar.
- 2026-08-28 [`15e6a2d`](https://github.com/se7enxweb/explayouts_ui/commit/15e6a2d) (Funktion) sort icon label now has equal left and right padding inside the sorting bar.

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/explayouts_ui.md#2026-09-26-änderungen).

- 2026-09-02 [`6c36efd`](https://github.com/se7enxweb/explayouts_ui/commit/6c36efd) (Funktion) explayouts_ui components list view and template.
- 2026-09-02 [`12a86b4`](https://github.com/se7enxweb/explayouts_ui/commit/12a86b4) (Funktion) register components view and add side nav link.
- 2026-09-02 [`19cd484`](https://github.com/se7enxweb/explayouts_ui/commit/19cd484) (Funktion) components list CSS padding, column widths and responsive card layout.
- 2026-09-02 [`e844193`](https://github.com/se7enxweb/explayouts_ui/commit/e844193) (Funktion) components list name links to view, add traditional edit icon column.
- 2026-09-02 [`af712d6`](https://github.com/se7enxweb/explayouts_ui/commit/af712d6) (Funktion) component name links use nice URL alias instead of content/view/full.
- 2026-09-02 [`9573f09`](https://github.com/se7enxweb/explayouts_ui/commit/9573f09) (Funktion) rule list View in CMS link uses system URL content/view/full for node targets.
- 2026-09-02 [`a633075`](https://github.com/se7enxweb/explayouts_ui/commit/a633075) (Funktion) Set content_info viewmode to 'layout_preview' in layout preview.
- 2026-09-07 [`63a53ba`](https://github.com/se7enxweb/explayouts_ui/commit/63a53ba) (Funktion) Added layout preview rendering against a representative content node, resolved from the mapping's own target, so content driven blocks show real content instead of rendering empty. A dedicated full view template breaks the circular reference the module result would otherwise produce.
- 2026-09-07 [`9673451`](https://github.com/se7enxweb/explayouts_ui/commit/9673451) (Funktion) Renamed the rule administration to layout mappings throughout the list and detail screens, and rebuilt the forms to offer content classes and siteaccesses as dropdowns instead of free text, add a per mapping cache clear, and scaffold a new mapping with a priority that actually resolves. Drops the obsolete legacy condition typ... **Release v1.1.0.**
- 2026-09-15 [`bdb8b44`](https://github.com/se7enxweb/explayouts_ui/commit/bdb8b44) (Funktion) Added paging to the layout, shared layout, component and rule lists, which drew every row they had on one screen. **Release v1.2.0.**
- 2026-09-17 [`cf1e932`](https://github.com/se7enxweb/explayouts_ui/commit/cf1e932) (Funktion) Updated the shared layouts list to read the shared flag rather than infer it from what links to a layout, so a shared layout nothing links to yet is listed. **Release v1.2.1.**
- 2026-09-19 [`60de39e`](https://github.com/se7enxweb/explayouts_ui/commit/60de39e) (Fehlerbehebung) Fixed: Fixed the component usage and shared layout listings showing nothing and no reference counts on MongoDB, caused by a JOIN and a GROUP BY the driver cannot translate, so both admin screens report what is actually there. **Release v1.2.2.**
- 2026-09-20 [`1a1bc79`](https://github.com/se7enxweb/explayouts_ui/commit/1a1bc79) (Fehlerbehebung) Fixed: Fixed the New layout button and the sorting controls running off the edge of the layout list on a phone, caused by a header row that could not wrap around selects that could not shrink, so the whole header now stays inside the screen. **Release v1.2.3.**
- 2026-09-22 [`6ec73b2`](https://github.com/se7enxweb/explayouts_ui/commit/6ec73b2) (Fehlerbehebung) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.3.0.**
- 2026-09-27 [`35bae6c`](https://github.com/se7enxweb/explayouts_ui/commit/35bae6c) (Fehlerbehebung) Fixed: Load more works in the layout preview **Release v1.3.1.**
- 2026-09-27 [`55379a7`](https://github.com/se7enxweb/explayouts_ui/commit/55379a7) (Fehlerbehebung) Fixed: The ezinfo.php declares its keys as strings, so the about page shows the extension's name, version and license **Release v1.3.2.**
- 2026-09-28 [`9edfda2`](https://github.com/se7enxweb/explayouts_ui/commit/9edfda2) (Funktion) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`12003cb`](https://github.com/se7enxweb/explayouts_ui/commit/12003cb) (Release) Version 1.3.3 **Release v1.3.3.**
- 2026-09-28 [`869e68e`](https://github.com/se7enxweb/explayouts_ui/commit/869e68e) (ohne Nutzen für Anwender) GitHub funding metadata, the same as the other se7enxweb packages
- 2026-09-29 [`c3ecea9`](https://github.com/se7enxweb/explayouts_ui/commit/c3ecea9) (Funktion) The top menu tab and its tooltip have German translations
- 2026-09-29 [`7fc0aaf`](https://github.com/se7enxweb/explayouts_ui/commit/7fc0aaf) (Release) Version 1.3.4 **Release v1.3.4.**
- 2026-09-30 [`b838c3b`](https://github.com/se7enxweb/explayouts_ui/commit/b838c3b) (Dokumentation) The about page names the extension Exponential Layouts UI
- 2026-09-30 [`40084e1`](https://github.com/se7enxweb/explayouts_ui/commit/40084e1) (Release) Version 1.3.5 **Release v1.3.5.**
- 2026-09-30 [`379fe88`](https://github.com/se7enxweb/explayouts_ui/commit/379fe88) (Fehlerbehebung) Fixed: The admin Layouts pages load their stylesheets in the page head, so the sidebar is painted styled at once
- 2026-09-30 [`107ccf2`](https://github.com/se7enxweb/explayouts_ui/commit/107ccf2) (Fehlerbehebung) Fixed: The dark sidebar and hidden scrollbars of the Layouts pages no longer reach the admin's node views
- 2026-09-30 [`3b98c33`](https://github.com/se7enxweb/explayouts_ui/commit/3b98c33) (Release) Version 1.3.6 **Release v1.3.6.**

### Nach dem Abschluss des Änderungsverzeichnisses hinzugekommen (1. bis 2. Oktober 2026)

Diese Commits wurden nach dem Erstellen des maschinell erzeugten Änderungsverzeichnisses dieses Repositorys gepusht; sie stehen hier, damit die Seite vollständig bleibt.

- 2026-10-01 `0c6083f` (Fehlerbehebung) The rule list's layout actions condition no longer logs a parser error on every view (v1.3.7)
- 2026-10-01 `3e56537` (Fehlerbehebung) The Roboto fonts ship with the extension (v1.3.7)
- 2026-10-01 `667e862` (Funktion) The Components page follows the renamed `exp_component_<type>` blocks (v1.3.7)
- 2026-10-01 `e3d2723` (Funktion) The layouts admin pages run on jQuery 4 and carry Exponential file names (v1.3.7)
- 2026-10-02 `d21be9c` (Fehlerbehebung) The stylesheet's page-wide rules no longer reset node views (v1.3.8)
- Releases v1.3.7 (1. Oktober) und v1.3.8 (2. Oktober)

## Verwandte Seiten

- [Funktionsseite](../../../eng-us/features/6.0/extensions/explayouts_ui.md)
- [Release Notes](../../../changelogs/extensions/explayouts_ui.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/explayouts_ui.md)
- [Spezifikation](../../spezifikationen/6.0/explayouts-ui-api.md)
- [Verhaltensänderungen der Erweiterungen](../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
