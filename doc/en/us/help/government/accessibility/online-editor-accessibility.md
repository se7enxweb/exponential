---
title: The online editor and accessibility
audience: government, admin, editor, project-manager
category: public-sector
order: 110
summary: For public bodies whose staff write rich text - which online editor engines Exponential ships (TinyMCE 3 by default, TinyMCE 8 opt-in), why their accessibility is Unverified, and how to test the editor and its output for your own statement.
keywords: online editor, rich text, TinyMCE, TinyMCE 3, TinyMCE 8, ezoe, engine switch, accessibility, keyboard, screen reader, unverified, authoring tool, rich text output, headings, tables
type: help
---
# The online editor and accessibility

This page is for administrators who choose the rich text editor, accessibility coordinators who evaluate the
authoring environment, and editors with disabilities who use it. The online editor is the part of the administration
the project has **not** audited: its status is **Unverified**, and the editor engine's own accessibility is that of
the TinyMCE project ([Accessibility standards: Known gaps](../../../standards/accessibility.md#known-gaps)).

## Contents

1. [Which editor your staff get](#which-editor-your-staff-get)
2. [What the project says](#what-the-project-says)
3. [Testing the editor](#testing-the-editor)
4. [Testing the output](#testing-the-output)
5. [Choosing and documenting](#choosing-and-documenting)
6. [Related pages](#related-pages)

## Which editor your staff get

The eZ Online Editor extension (`extension/ezoe`) ships two engines
([The online editor on TinyMCE 8](../../../features/6.0/online-editor-tinymce8.md)):

| Engine | Status in a new installation | How to choose it |
|---|---|---|
| TinyMCE 3.5.12 | the default | nothing to do |
| TinyMCE 8.9.2 | opt-in | per user: `/user/preferences/set/ezoe_engine/tinymce8` or the page `/ezoe/engine`; per siteaccess: `ezoe.ini [EditorSettings] EditorEngine=tinymce8` |

With `EngineSwitch=enabled` the editor shows an engine switch below it, a group of toggle buttons with `aria-pressed`.
Both engines store the same XML, so editors can switch.

Where the Content Security Policy is enforced without `'unsafe-inline'` in `script-src`, TinyMCE 3 is not offered and
the editor falls back to TinyMCE 8 ([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## What the project says

- The TinyMCE integrations were not audited for WCAG. **Unverified.**
- The engine switch is a toggle group with `aria-pressed` (documented in the TinyMCE 8 page).
- EN 301 549 clause 11.8 (authoring tools) has not been reviewed.

There is no further evidence in the repository. Do not describe the editor as accessible in your statement without
your own test.

## Testing the editor

Test with the keyboard alone, then with a screen reader, on the engine you deploy:

- [ ] Tab reaches the editor area and leaves it again (no keyboard trap).
- [ ] The toolbar can be reached and operated with the keyboard.
- [ ] Headings, lists, bold and links can be applied without the mouse.
- [ ] The link and embed dialogs open, can be completed and closed with Escape, and return the focus.
- [ ] A table with a header row and a caption can be created.
- [ ] An image can be embedded and its alternative text entered or checked.
- [ ] The screen reader announces the editor's name and the toolbar buttons.
- [ ] At 200% zoom the toolbar and dialogs stay usable.

## Testing the output

What matters most to the public is the HTML the editor's content produces in your design:

1. Create a test article with two heading levels, a list, a link with a meaningful text, a table with header cells
   and a caption, and an embedded image with alternative text.
2. Publish it in a hidden test folder and open it on the public siteaccess.
3. Inspect the HTML: real `h2`/`h3` elements, `th` cells with `scope`, a `caption`, `alt` on the image. The standard
   design's table templates write `caption`, `scope` and `abbr` when they are set
   (`design/standard/templates/content/datatype/view/ezxmltags/table.tpl`, `th.tpl`).
4. Run an automated checker on the page and do a keyboard pass.

## Choosing and documenting

- Pick one engine for the organization and write it into your editor guidelines.
- Record your test results with the engine and its version.
- Re-test after upgrades: the [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md) lists editor changes.
- In your accessibility statement, describe the editing environment only if your law asks for it, and only with what
  you tested.

## Related pages

- [The online editor on TinyMCE 8](../../../features/6.0/online-editor-tinymce8.md)
- [Accessibility standards](../../../standards/accessibility.md)
- [Editing content](../../editing-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
