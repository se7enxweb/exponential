# Änderungsverzeichnis: explayouts_ui

Jede Änderung an `explayouts_ui` seit Beginn der se7enxweb-Ära, die älteste zuerst: 55 Änderungen an 186 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 43 |
| Hinzugefügt | 9 |
| Umbenannt | 3 |

## 2026-07 (5 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-30 | `392dff9` | Hinzugefügt | Added: Initial import. Import of explayouts_ui extension providing exponential Layouts UI - Admin user interface for Exponential Layouts. | 47 | +3268 / −0 |  |
| 2026-07-30 | `54b3bae` | Aktualisiert | Updated: Updated README.md to the classic 7x documentation standard with a doc/ index and expanded usage information. | 1 | +149 / −14 |  |
| 2026-07-30 | `aa294a9` | Aktualisiert | Fixed: Fixed the composer.json license to the SPDX identifier GPL-2.0-or-later required by Packagist. | 1 | +1 / −1 |  |
| 2026-07-30 | `f7d4c04` | Hinzugefügt | Added: Added .gitattributes with export-ignore rules to keep composer dist archives lean. | 1 | +5 / −0 |  |
| 2026-07-30 | `497ecd8` | Aktualisiert | Updated: Set white leftmenu background for section_id_7 dashboard | 1 | +7 / −0 | v1.0.0 |

## 2026-08 (24 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-08-27 | `bd10b7c` | Hinzugefügt | Added: Material Icons font assets for admin layout UI | 5 | +2373 / −0 |  |
| 2026-08-28 | `7dcd433` | Aktualisiert | Updated: rule list with layout/target/condition details and inline save panel | 2 | +273 / −32 |  |
| 2026-08-28 | `2dac252` | Aktualisiert | Updated: rule list UI to match Nexus layout resolver rule list | 2 | +488 / −168 |  |
| 2026-08-28 | `82d2c02` | Aktualisiert | Updated: rule list template visibility, layout wrapper, and icon font overrides. | 1 | +9 / −5 |  |
| 2026-08-28 | `dc2bb42` | Aktualisiert | Updated: rule condition type dropdown options. | 1 | +7 / −1 |  |
| 2026-08-28 | `d9b2962` | Aktualisiert | Updated: rule detail layout icon and target content browser. | 1 | +59 / −3 |  |
| 2026-08-28 | `b4098b1` | Aktualisiert | Fixed: rule detail layout icon src and content browser URL escaping. | 1 | +5 / −5 |  |
| 2026-08-28 | `b715050` | Aktualisiert | Fixed: layout detail image height so SVG icon is visible. | 1 | +1 / −1 |  |
| 2026-08-28 | `3ffe820` | Aktualisiert | Updated: condition dropdown keeps used ibexa condition types. | 1 | +12 / −0 |  |
| 2026-08-28 | `ab3866d` | Hinzugefügt | Added: styled enabled/disabled toggle in rule details. | 1 | +36 / −1 |  |
| 2026-08-28 | `029af55` | Aktualisiert | Updated: remove ibexa-prefixed condition options from rule UI. | 2 | +8 / −13 |  |
| 2026-08-28 | `678ca69` | Aktualisiert | Updated: New rule opens the modern rule details popin. | 3 | +265 / −187 |  |
| 2026-08-28 | `079ccae` | Umbenannt | Renamed: content_node target type to node in rule list and detail templates. | 3 | +3 / −3 |  |
| 2026-08-28 | `f8b489f` | Hinzugefügt | Added: rule list can be preloaded from a Map layout or Edit mapping link via TargetType/TargetValue and RuleID query parameters. | 2 | +34 / −2 |  |
| 2026-08-28 | `2e016af` | Aktualisiert | Updated: rule list and rule detail "Edit layout" links now open the modern layout SPA. | 2 | +2 / −2 |  |
| 2026-08-28 | `1e807eb` | Umbenannt | Renamed: top admin tab from "Exponential Layouts UI" to "Layouts". | 1 | +4 / −4 |  |
| 2026-08-28 | `036f4cb` | Aktualisiert | Updated: dashboard and layout lists now use modern SPA editor links and a labeled-button UI. | 5 | +101 / −55 |  |
| 2026-08-28 | `f902903` | Umbenannt | Renamed: condition type "content_type" now uses the Exponential term "class" in rule list UI. | 3 | +3 / −3 |  |
| 2026-08-28 | `9d50fe4` | Aktualisiert | Updated: layout list grid is now responsive so cards no longer overflow the admin container. | 2 | +16 / −0 |  |
| 2026-08-28 | `d865269` | Aktualisiert | Updated: layout list sorting controls are now a single bar with the same height as the New layout button. | 1 | +7 / −0 |  |
| 2026-08-28 | `c222fd2` | Aktualisiert | Updated: layout list header now places sorting bar on the left and the New layout button on the right. | 1 | +7 / −4 |  |
| 2026-08-28 | `ff3ba57` | Aktualisiert | Updated: sorting dropdowns on layout list are now capped at 120px and the sorting bar padding is reduced so it sits compactly next to the New layout button. | 1 | +3 / −4 |  |
| 2026-08-28 | `dc94dd8` | Aktualisiert | Updated: added 4px left padding to the sort icon label in the layout list sorting bar. | 1 | +1 / −1 |  |
| 2026-08-28 | `15e6a2d` | Aktualisiert | Updated: sort icon label now has equal left and right padding inside the sorting bar. | 1 | +1 / −1 |  |

## 2026-09 (26 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-02 | `6c36efd` | Hinzugefügt | Added: explayouts_ui components list view and template. | 2 | +319 / −0 |  |
| 2026-09-02 | `12a86b4` | Aktualisiert | Updated: register components view and add side nav link. | 2 | +11 / −0 |  |
| 2026-09-02 | `19cd484` | Aktualisiert | Updated: components list CSS padding, column widths and responsive card layout. | 1 | +28 / −6 |  |
| 2026-09-02 | `e844193` | Aktualisiert | Updated: components list name links to view, add traditional edit icon column. | 2 | +20 / −4 |  |
| 2026-09-02 | `af712d6` | Aktualisiert | Updated: component name links use nice URL alias instead of content/view/full. | 1 | +11 / −4 |  |
| 2026-09-02 | `9573f09` | Aktualisiert | Updated: rule list View in CMS link uses system URL content/view/full for node targets. | 2 | +4 / −4 |  |
| 2026-09-02 | `a633075` | Aktualisiert | Updated: Set content_info viewmode to 'layout_preview' in layout preview. | 1 | +1 / −0 |  |
| 2026-09-07 | `63a53ba` | Hinzugefügt | Added: Added layout preview rendering against a representative content node, resolved from the mapping's own target, so content driven blocks show real content instead of rendering empty. A dedicated full view template breaks the circular reference the module result would otherwise produce. | 2 | +128 / −1 |  |
| 2026-09-07 | `9673451` | Aktualisiert | Updated: Renamed the rule administration to layout mappings throughout the list and detail screens, and rebuilt the forms to offer content classes and siteaccesses as dropdowns instead of free text, add a per mapping cache clear, and scaffold a new mapping with a priority that actually resolves. Drops the obsolete legacy condition type mapping and the browse button that never applied to the target field. | 3 | +513 / −155 | v1.1.0 |
| 2026-09-15 | `bdb8b44` | Hinzugefügt | Added: Added paging to the layout, shared layout, component and rule lists, which drew every row they had on one screen. | 9 | +132 / −0 | v1.2.0 |
| 2026-09-17 | `cf1e932` | Aktualisiert | Updated: Updated the shared layouts list to read the shared flag rather than infer it from what links to a layout, so a shared layout nothing links to yet is listed. | 1 | +9 / −9 | v1.2.1 |
| 2026-09-19 | `60de39e` | Aktualisiert | Fixed: Fixed the component usage and shared layout listings showing nothing and no reference counts on MongoDB, caused by a JOIN and a GROUP BY the driver cannot translate, so both admin screens report what is actually there. | 2 | +66 / −10 | v1.2.2 |
| 2026-09-20 | `1a1bc79` | Aktualisiert | Fixed: Fixed the New layout button and the sorting controls running off the edge of the layout list on a phone, caused by a header row that could not wrap around selects that could not shrink, so the whole header now stays inside the screen. | 1 | +26 / −2 | v1.2.3 |
| 2026-09-22 | `6ec73b2` | Aktualisiert | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 7 | +253 / −216 | v1.3.0 |
| 2026-09-27 | `35bae6c` | Aktualisiert | Fixed: Load more works in the layout preview | 1 | +5 / −0 | v1.3.1 |
| 2026-09-27 | `55379a7` | Aktualisiert | Fixed: The ezinfo.php declares its keys as strings, so the about page shows the extension's name, version and license | 2 | +6 / −6 | v1.3.2 |
| 2026-09-28 | `9edfda2` | Aktualisiert | Updated: Every visible text of the extension is a translation string, with German | 28 | +3907 / −290 |  |
| 2026-09-28 | `12003cb` | Aktualisiert | Updated: Version 1.3.3 | 2 | +2 / −2 | v1.3.3 |
| 2026-09-28 | `869e68e` | Hinzugefügt | Added: GitHub funding metadata, the same as the other se7enxweb packages | 1 | +3 / −0 |  |
| 2026-09-29 | `c3ecea9` | Aktualisiert | Updated: The top menu tab and its tooltip have German translations | 3 | +54 / −0 |  |
| 2026-09-29 | `7fc0aaf` | Aktualisiert | Updated: Version 1.3.4 | 2 | +2 / −2 | v1.3.4 |
| 2026-09-30 | `b838c3b` | Aktualisiert | Updated: The about page names the extension Exponential Layouts UI | 2 | +2 / −2 |  |
| 2026-09-30 | `40084e1` | Aktualisiert | Updated: Version 1.3.5 | 2 | +2 / −2 | v1.3.5 |
| 2026-09-30 | `379fe88` | Aktualisiert | Fixed: The admin Layouts pages load their stylesheets in the page head, so the sidebar is painted styled at once | 9 | +9 / −27 |  |
| 2026-09-30 | `107ccf2` | Aktualisiert | Fixed: The dark sidebar and hidden scrollbars of the Layouts pages no longer reach the admin's node views | 1 | +12 / −3 |  |
| 2026-09-30 | `3b98c33` | Aktualisiert | Updated: Version 1.3.6 | 2 | +2 / −2 | v1.3.6 |
