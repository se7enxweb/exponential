---
title: Mapping WCAG 2.2 AA to Exponential
audience: government, project-manager, developer, designer
category: public-sector
order: 70
summary: A criterion-by-criterion map of WCAG 2.2 level AA for public bodies - what the admin4 administration provides and tests, and who answers for the public site and its content.
keywords: WCAG 2.2, success criteria, level AA, mapping, admin4, public site, editors, designers, developers, 1.1.1, 1.4.3, 2.1.1, 2.4.7, 2.5.8, 3.3.1, 4.1.2, accessibility matrix
type: reference
---
# Mapping WCAG 2.2 AA to Exponential

This page is for accessibility coordinators, designers, developers and project managers who plan or audit a site. It
lists the WCAG 2.2 success criteria the project's statement covers, says what the admin4 administration does for each
and which test holds it, and says who answers for the same criterion on your public site. Every entry for the
administration comes from [Accessibility standards](../../../standards/accessibility.md#wcag-22); WCAG itself is at
<https://www.w3.org/TR/WCAG22/>.

## Contents

1. [How to read the table](#how-to-read-the-table)
2. [The map](#the-map)
3. [Criteria the statement does not mention](#criteria-the-statement-does-not-mention)
4. [Related pages](#related-pages)

## How to read the table

- **Admin4** is the administration editors use. The status of the whole is **Partly complies**: designed and
  regression-tested, not audited page by page.
- **Public site** is your design. The project has not audited the public designs, so every row there is your work;
  the column names who usually does it.
- A test file name means a test in `tests/tests/design/` that reads the template or stylesheet sources.

## The map

| Criterion | Admin4 (editing tool) | Evidence | Public site: who does it |
|---|---|---|---|
| 1.1.1 Non-text content | every form control has an accessible name | `AdminFormLabelsTest.php` | editors write alternative text; administrators can make it required on image attributes ("Alternative text is required"); designers handle decorative images |
| 1.3.1 Info and relationships | landmarks, `scope="col"` on header cells, required fields marked for screen readers | `AdminLandmarksTest.php`, `AdminFormErrorsTest.php` | developers: semantic templates; editors: real headings, table header cells |
| 1.3.2 Meaningful sequence | right-to-left languages set `dir="rtl"`; names isolated with `<bdi>` | `AdminRightToLeftTest.php` | developers: source order equals reading order; `lang`/`dir` on content in another language |
| 1.4.1 Use of color | notices carry an icon as well as a color | `AdminNoticesTest.php` | designers |
| 1.4.3 Contrast (minimum) | `--a4-*` tokens checked: text 4.5:1 in light and dark mode | `AdminTokenContrastTest.php` | designers: color tokens of your theme |
| 1.4.4 Resize text, 1.4.10 Reflow | checked at phone, tablet and desktop widths; pages checked at 960 px with 200% zoom | `AdminResponsiveTest.php` | designers and developers: responsive templates |
| 1.4.11 Non-text contrast | focus ring and field edges 3:1, chart colors 3:1 | `AdminTokenContrastTest.php` | designers |
| 1.4.13 Content on hover or focus | tooltips hoverable, persistent, dismissible with Escape | [admin4 design](../../../features/6.0/admin4-design.md) | developers |
| 2.1.1 Keyboard | node tabs (APG tabs pattern), content tree, context menus by keyboard | `AdminKeyboardTest.php`, `AdminContextMenuTest.php` | developers: menus, sliders, embedded widgets |
| 2.4.1 Bypass blocks | "Skip to main content" is the first Tab stop | `AdminLandmarksTest.php` | developers: a skip link in your pagelayout |
| 2.4.3 Focus order | the same header Tab order in admin4 and admin4l | `AdminHeaderTabOrderTest.php` | developers |
| 2.4.7 Focus visible | one shared focus ring (3 px outline in `--a4-ring`) | `design/admin4/stylesheets/components.css` | designers: never remove the outline without a replacement |
| 2.5.8 Target size (minimum) | targets at least 24 x 24 px | [admin4 styling](../../../bc/6.0/admin4-styling.md) | designers |
| 3.1.1 Language of page | `<html lang>` from the siteaccess locale | `design/admin4/templates/pagelayout.tpl` | developers: `lang` in your pagelayout; per-language siteaccesses |
| 3.3.1 Error identification, 3.3.2 Labels or instructions | error summary that takes the focus and links to fields; `aria-invalid`, `aria-describedby` | `AdminFormErrorsTest.php` | developers: your forms (information collector, registration) |
| 4.1.2 Name, role, value | accessible names; tabs, tree and menu roles and states | `AdminFormLabelsTest.php`, `AdminKeyboardTest.php` | developers |

User preferences that support 1.4.3 and 2.3.3: light, dark and system color modes, and `forced-colors` and
`prefers-reduced-motion` rules in `design/admin4/stylesheets/admin4.css` (`AdminThemeSwitchTest.php`).

## Criteria the statement does not mention

The project's statement lists the criteria above. For any other AA criterion (for example 1.2.x captions and audio
description, 1.4.5 images of text, 2.2.1 timing adjustable, 2.4.2 page titled, 2.4.4 link purpose, 2.4.6 headings and
labels, 2.4.11 focus not obscured, 2.5.7 dragging movements, 3.2.x predictable, 3.3.7 redundant entry, 3.3.8
accessible authentication) there is **no documented evidence** for the administration. Treat them as untested: put
them in your own audit for both the administration and the public site.

Some facts that matter for those criteria and can be verified in the repository:

- **2.5.7 Dragging movements**: sub items can be reordered by drag and drop in admin4. The grip is also a button
  that moves the row with Space, Enter and the arrow keys, and the priority cells can still be edited by hand
  ([Reordering sub items by drag and drop](../../../features/6.0/admin4-design.md#reordering-sub-items-by-drag-and-drop)).
  Include other drag interactions of your extensions (for example the layouts editor) in your audit.
- **2.2.1 Timing adjustable**: sessions end after `site.ini [Session] SessionTimeout` (259200 seconds by default);
  a shorter value chosen for security can affect editors who work slowly
  ([13.7 Sessions and cookies](../../../install/13-security-hardening.md#137-sessions-and-cookies)).
- **3.3.8 Accessible authentication**: the sign-in is a username and password form; two-step sign-in, when enabled,
  adds a 6-digit code ([Two-step sign-in](../../../guides/two-factor-login.md)). Check that password managers and
  pasting work in your configuration.

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [The admin4 design](../../../features/6.0/admin4-design.md)
- [admin4 styling](../../../bc/6.0/admin4-styling.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
