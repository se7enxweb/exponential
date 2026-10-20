---
title: Accessibility standards
audience: developer, admin
category: standards
order: 20
summary: How Exponential measures up against WCAG 2.2 AA, WAI-ARIA 1.2, EN 301 549 and Section 508, with the templates, stylesheets and tests that prove it and the gaps that remain.
keywords: accessibility, a11y, WCAG, WCAG 2.2, AA, WAI-ARIA, ARIA 1.2, APG, ARIA Authoring Practices, EN 301 549, Section 508, VPAT, ACR, screen reader, keyboard, focus, contrast, skip link, landmarks, right-to-left, RTL, dark mode, forced colors, axe
type: reference
---
# Accessibility standards

This page is for developers who build or review templates, and for administrators who must answer the question
"is Exponential accessible?" for a procurement, an audit or their own users. It lists the accessibility standards
that apply to a web content management system, says for each one what Exponential 6.0.x does, points to the files
and tests that show it, and names the gaps honestly.

> **Note** Exponential is software. Whether a *site* built with it is accessible also depends on its own design, its
> templates and the content its editors publish. The statements below are about the parts that ship with
> Exponential.

## Summary

| Standard | Version | Status | Evidence (short) |
|---|---|---|---|
| Web Content Accessibility Guidelines (WCAG) | 2.2, level AA | **Partly complies** | admin4 / admin4l administration built and tested against AA criteria; classic admin and standard edit form partly fixed; public designs and online editor not audited |
| WAI-ARIA and the ARIA Authoring Practices Guide (APG) | WAI-ARIA 1.2 | **Partly complies** | landmarks, tabs, disclosure and menu patterns in admin4, held by `tests/tests/design/AdminKeyboardTest.php`, `AdminLandmarksTest.php`, `AdminContextMenuTest.php` |
| EN 301 549 (European accessibility requirements for ICT) | v3.2.1 (2021-03) | **Unverified** | its web clause 9 is WCAG 2.1 AA, which the admin4 work targets; no conformance report against the standard |
| Section 508 (US Rehabilitation Act, Revised 508 Standards) | 2017 refresh (WCAG 2.0 AA) | **Unverified** | covered in substance by the WCAG work for admin4; no VPAT / Accessibility Conformance Report |

## WCAG 2.2

### What it is

The Web Content Accessibility Guidelines 2.2 are a W3C Recommendation (first published October 2023), published by
the W3C Web Accessibility Initiative. Level AA is the level that laws and procurement rules usually require. WCAG
2.2 adds nine success criteria to WCAG 2.1, among them 2.4.11 Focus Not Obscured, 2.5.7 Dragging Movements and 2.5.8
Target Size (Minimum).

### How Exponential complies

The work concentrates on the **admin4** administration design (`design/admin4/`) and its layout variant **admin4l**
(`design/admin4l/`). It is described in [the admin4 design](../features/6.0/admin4-design.md) and in the
contributor reference [admin4 styling](../bc/6.0/admin4-styling.md). The design tests run without a database in
the `design` test suite of `phpunit.xml` (`tests/tests/design/`), which the PHPUnit workflow
(`.github/workflows/phpunit.yml`) runs with every suite of `phpunit.xml`.

| WCAG area | What ships | Evidence |
|---|---|---|
| 1.1.1 Non-text content, 4.1.2 Name, role, value | Every form control in admin4 templates has an accessible name (`<label for>`, a wrapping `<label>`, `aria-label` or `aria-labelledby`; a `title` or `placeholder` does not count). The standard design's content edit form names its sort, location, translation and relation controls | `tests/tests/design/AdminFormLabelsTest.php`, `tests/tests/design/StandardContentEditAccessibleNamesTest.php`, `tests/tests/design/AdminRelationListLabelsTest.php` |
| 1.3.1 Info and relationships, 2.4.1 Bypass blocks | A skip link is the first Tab stop (`.a4-skip-link` in `design/admin4/templates/pagelayout.tpl`, "Skip to main content"); banner, navigation, main and footer landmarks; one main landmark per page; table header cells with `scope="col"` | `tests/tests/design/AdminLandmarksTest.php` |
| 1.3.1, 3.3.1 Error identification, 3.3.2 Labels or instructions | Required fields carry a visible mark and the word for screen readers (`.a4-sr-only`); a failed store shows an error summary that takes the focus and links to the fields; the script sets `aria-required` / `aria-invalid` and points `aria-describedby` at the message | `tests/tests/design/AdminFormErrorsTest.php` |
| 1.4.1 Use of color | Notices show their state with an icon as well as a color; module messages carry `role="alert"` or a status role; one pair of live regions for toasts | `tests/tests/design/AdminNoticesTest.php` |
| 1.4.3 Contrast (minimum), 1.4.11 Non-text contrast | The color tokens of `design/admin4/stylesheets/components.css` (`--a4-*`) are checked with the WCAG 2 formula in light **and** dark mode: text 4.5:1 on every surface, focus ring and field edges 3:1, chart colors 3:1 | `tests/tests/design/AdminTokenContrastTest.php` |
| 1.4.4 Resize text, 1.4.10 Reflow | admin4 is checked at phone (390 px), tablet (820 px), small and wide desktop widths; the project checks pages at 960 px with 200% zoom. The classic admin stacks its columns below 760 px | `tests/tests/design/AdminResponsiveTest.php`, `tests/tests/design/ClassicAdminNarrowWindowTest.php` |
| 1.4.13 Content on hover or focus | Tooltips are hoverable, persistent and dismissible with Escape | [admin4 design](../features/6.0/admin4-design.md) |
| 2.1.1 Keyboard, 2.4.3 Focus order | Node tabs follow the APG tabs pattern (arrow keys, Home, End); the content tree toggle is a named button with `aria-expanded`; context menus work from the keyboard; the header has the same Tab order in admin4 and admin4l | `tests/tests/design/AdminKeyboardTest.php`, `AdminContextMenuTest.php`, `AdminHeaderTabOrderTest.php`; [Keyboard shortcuts](../help/keyboard-shortcuts.md) |
| 2.4.7 Focus visible | One shared focus ring: `.a4-page :focus-visible`, `.a4-focus:focus-visible` and `.a4-btn:focus-visible` in `components.css` (3 px outline in `--a4-ring`) | `design/admin4/stylesheets/components.css` |
| 2.5.8 Target size (minimum) | Targets are at least 24 x 24 px; where the drawn control is smaller an invisible `::after` enlarges the target | [admin4 styling](../bc/6.0/admin4-styling.md), [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) |
| 3.1.1 Language of page | `<html lang="...">` from the siteaccess locale in every admin pagelayout | `design/admin4/templates/pagelayout.tpl` |
| 1.3.2 Meaningful sequence (bidirectional text) | Right-to-left interface languages set `dir="rtl"` on `<html>`; names are isolated with `<bdi>`; values read left to right keep their order | [Right-to-left languages](../features/6.0/right-to-left.md), `tests/tests/design/AdminRightToLeftTest.php` |
| User preferences (not a WCAG criterion, but supports 1.4.3 and 2.3.3) | Light, dark and "system" mode (`prefers-color-scheme`, `design/admin4/javascript/a4_head.js`); `@media (forced-colors: active)` and `@media (prefers-reduced-motion: reduce)` rules in `design/admin4/stylesheets/admin4.css` | `tests/tests/design/AdminThemeSwitchTest.php` |

The help system follows the same rules: one `h1` per page, named navigation landmarks, labeled search fields, a
visible focus ring, a note on links that open a new window, and search results announced to screen readers
([Help system: Languages and accessibility](../features/6.0/help-system.md#languages-and-accessibility)).

A count of the ARIA attributes in `design/admin4/templates/` (October 2026) gives an idea of the coverage:
about 400 `aria-labelledby`, 390 `aria-hidden`, 380 `aria-label`, 215 `aria-describedby`, 110 `aria-current` and
65 `aria-live` uses.

Automated checks with the axe engine are run by hand during reviews, not in CI. Their results are recorded in the
[6.0.15 changelog](../../../changelogs/6.0/6.0.15.md): for example "axe reports no serious finding in the page frame on 10
classic pages at 1440 px and at 960 px with 200 % zoom" (classic admin, #522), 13 serious findings on the standard
content edit form fixed, a target-size failure and a forced-colors contrast failure fixed.

### Status

**Partly complies.** The admin4 and admin4l administration is designed and regression-tested against WCAG 2.2 AA
criteria. That is not the same as a conformance claim: there is no full audit of every page, no assistive
technology test log, and several parts of the product were not reviewed (see below).

### Known gaps

- **Public site designs are not audited.** `design/standard`, `design/base`, `design/plain`, `design/mysite` and
  the site designs that come as packages (ezwebin, the media themes, starter sites) have had only targeted fixes
  (the standard content edit form). Their accessibility is the site builder's responsibility.
- **Classic administration (`design/admin`) and admin3** received label and narrow-window fixes, but they are not
  covered by the contrast, landmark and form-error tests that hold admin4.
- **Online editor.** The TinyMCE 8 and TinyMCE 3 integrations (`extension/ezoe`) were not audited for WCAG; the
  editor engine's own accessibility is that of the TinyMCE project. **Unverified.**
- **Extensions** shipped as separate packages (layouts editor, newsletter, shop) are reviewed one by one; some fixes
  are recorded in the changelog, there is no overall statement.
- **No automated accessibility engine in CI.** The tests read template and stylesheet sources; they do not render
  pages in a browser. axe runs are manual.
- **No published conformance report** (WCAG-EM evaluation, VPAT or Accessibility Conformance Report).
- **Content.** Alternative text, heading order and link text in published content depend on editors. The image
  datatypes offer an alternative text field; nothing forces editors to fill it.

## WAI-ARIA 1.2 and the ARIA Authoring Practices

### What it is

Accessible Rich Internet Applications (WAI-ARIA) 1.2 is a W3C Recommendation (June 2023) that defines roles, states
and properties for assistive technologies. The ARIA Authoring Practices Guide (APG), a W3C WAI note, shows how to
build common widgets (tabs, menus, disclosures, dialogs) with them.

### How Exponential complies

- **Tabs pattern**: node tabs in admin4 (`design/admin4/javascript/node_tabs.js`), held by
  `tests/tests/design/AdminKeyboardTest.php`.
- **Disclosure navigation**: the opt-in mega menu (`menu.ini [TopAdminMenu] MegaMenuTabs[]`) keeps the tab a link
  and adds a button with `aria-expanded` beside it (`tests/tests/design/AdminMegaMenuTest.php`).
- **Menu**: the context menus (`ezpopupmenu.js`) have roles and keyboard support
  (`tests/tests/design/AdminContextMenuTest.php`).
- **Toggle buttons**: `aria-pressed` for the editor engine switch and segmented controls
  ([TinyMCE 8](../features/6.0/online-editor-tinymce8.md)).
- **Live regions**: toasts and help search results (`aria-live`).
- **First rule of ARIA**: native elements are preferred. The form-label test does not accept a `title` as a name,
  and `aria-label` must not replace a visible label that has text (`AdminFormLabelsTest.php`).

### Status

**Partly complies** for admin4 and admin4l, where the patterns are tested on the template sources. **Unverified**
elsewhere; classic designs still contain older JavaScript widgets.

### Known gaps

- The tests check markup and the hooks the scripts use, not the runtime behavior in a screen reader.
- No test lists every widget against its APG pattern.

## EN 301 549

### What it is

EN 301 549 "Accessibility requirements for ICT products and services", v3.2.1 (2021-03), published by ETSI,
CEN and CENELEC. It is the harmonized standard of the EU Web Accessibility Directive (2016/2102) and the reference
for the European Accessibility Act (2019/882). Its clause 9 (web) incorporates WCAG 2.1 level AA; clause 11
(software) and clause 12 (documentation and support) apply to authoring tools as well.

### How Exponential complies

The WCAG 2.2 AA work on admin4 covers the WCAG 2.1 AA criteria that clause 9 requires (WCAG 2.2 is a superset,
except for 4.1.1 Parsing, which 2.2 removed). The documentation of the help system follows the same rules.

### Status

**Unverified.** No evaluation against EN 301 549 has been made, and clause 11.8 (authoring tools, for example that
the tool helps authors produce accessible content) has not been reviewed.

### Known gaps

- No accessibility statement template for sites, as the Web Accessibility Directive requires of public bodies.
- Clause 11.8 (authoring tool support: prompts for alternative text, accessible output of rich text) not assessed.

## Section 508

### What it is

Section 508 of the US Rehabilitation Act requires federal agencies to procure accessible ICT. The Revised 508
Standards (US Access Board, 2017) incorporate WCAG 2.0 level AA by reference. Vendors usually document conformance
in a VPAT (Voluntary Product Accessibility Template), which produces an Accessibility Conformance Report.

### How Exponential complies

In substance through the WCAG work above; WCAG 2.0 AA is a subset of what admin4 is tested against.

### Status

**Unverified.** There is no VPAT or Accessibility Conformance Report for Exponential.

## For template authors

- Follow [admin4 styling](../bc/6.0/admin4-styling.md): shared components (`.a4-btn`, `.a4-card`, `.a4-sr-only`),
  tokens only, a dark value for each new color token, no inline CSS.
- Run the design tests after a template change:

  ```bash
  php vendor/bin/phpunit --testsuite design
  ```

- Check a changed page in light and dark mode, at 1440 px and at 960 px with 200% zoom, and with the keyboard alone.

## Related pages

- [Security standards](security.md), [Privacy standards](privacy.md), [Web platform standards](web-platform.md)
- [The admin4 design](../features/6.0/admin4-design.md)
- [Right-to-left languages](../features/6.0/right-to-left.md)
- [Keyboard shortcuts](../help/keyboard-shortcuts.md)
