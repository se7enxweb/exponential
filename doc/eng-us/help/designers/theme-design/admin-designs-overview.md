---
title: Admin designs overview
audience: designer, admin
category: theme-design
order: 150
summary: The administration designs (admin, admin2, admin3, admin4, admin4l, editor, adminui), how they stack, what admin4 adds (tokens, components, dark mode) and the rules every template change in an admin design must follow.
keywords: admin design, administration design, admin4, admin4l, admin3, admin2, editor design, adminui, admin theme, admin look, admin styling rules, backend design
type: help
---
# Admin designs overview

This page is for designers who change the look of the administration, or style an extension's admin pages. It answers:
which admin designs exist, how they stack on each other, and which rules apply to any template or stylesheet you add to
them.

## Contents

1. [The designs](#the-designs)
2. [How they stack](#how-they-stack)
3. [What admin4 brings](#what-admin4-brings)
4. [admin4l: the admin drawn with layouts](#admin4l-the-admin-drawn-with-layouts)
5. [The editor design](#the-editor-design)
6. [Rules for every admin template](#rules-for-every-admin-template)
7. [Where to go next](#where-to-go-next)

## The designs

| Design | Folder | What it is |
|---|---|---|
| `admin` | `design/admin` | the classic administration design; also the last fallback of the newer ones |
| `admin2`, `admin3` | `design/admin2`, `design/admin3` | intermediate designs; admin3 brought the responsive frame |
| `admin4` | `design/admin4` | the current design: complete and self-contained, with tokens, shared components and a dark mode ([The admin4 design](../../../features/6.0/admin4-design.md)) |
| `admin4l` | `design/admin4l` | a thin design on top of admin4 whose frame is drawn by layouts blocks |
| `editor` | `design/editor` | the editor siteaccess: almost empty, the difference lies in its settings |
| `adminui` | `extension/exp_adminui/design/adminui` | the AdminUI administration, with its own stylesheet and a right-to-left copy |

## How they stack

On the demonstration installation:

![The Interface box in the right column with the Administration design list at the bottom, where a user picks the admin design](../../images/designers/admin-design-switch.webp)

```
admin    siteaccess: admin4l > admin4 > admin3 > admin2 > admin > standard
editor   siteaccess: editor > admin4l > admin4 > admin3 > admin2 > admin > standard
adminui  siteaccess: adminui > admin4l > admin4 > admin3 > admin2 > admin > standard
```

A signed-in user can also switch the administration design for themselves with the interface switch
([Interface switch: design](../../../features/6.0/ui-switch-design.md)), so test admin changes in each design a user
may pick.

## What admin4 brings

- **Tokens.** Colors, spacing, sizes, radius and fonts are CSS custom properties `--a4-*` in
  `design/admin4/stylesheets/components.css`, with dark values under `html[data-a4-theme="dark"]`
  ([admin4 tokens](admin4-tokens.md)).
- **Shared components.** `.a4-card`, `.a4-btn`, `.a4-table`, `.a4-toolbar`, `.a4-filterbar`, `.a4-tag` /
  `.a4-badge`, `.a4-sr-only`, `.a4-skip-link` and more, in the same file ([admin4 components](admin4-components.md)).
- **Module stylesheets.** One file per area in `stylesheets/modules/` (`setup.css`, `content.css`, `people.css`,
  `structure.css`, `workflow.css`, `help.css`), loaded through `design.ini [StylesheetSettings]
  AdminComponentCSSFileList[]` ([admin4 module stylesheets](admin4-module-stylesheets.md)).
- **Light, dark and system modes.** A pre-paint script (`javascript/a4_head.js`, loaded as a file by
  `page_head_style.tpl`) sets `data-a4-theme` before the first paint ([The theme toggle and the pre-paint guard](theme-toggle-pre-paint-guard.md)).
- **Right to left.** Logical CSS properties throughout ([Right-to-left designs](right-to-left-designs.md)).
- **Content Security Policy ready.** No inline scripts or styles in its templates.

## admin4l: the admin drawn with layouts

`design/admin4l` holds a `pagelayout.tpl` and about two dozen block templates under `templates/explayouts/block/`
(`admin_logo.tpl`, `admin_tab_menu.tpl`, `admin_left_menu.tpl`, `admin_breadcrumb.tpl`, `admin_module_result.tpl`,
`admin_footer.tpl`, `admin_theme_switch.tpl` ...). The frame of the admin is an admin layout (layout types `admin_3col`,
`admin_2col`, `admin_full` in `explayouts.ini`) whose zones hold these blocks, so the frame can be rearranged with the
layouts editor. The pagelayout asks `fetch( 'explayouts', 'resolve_admin_layout', hash( 'module', ..., 'view', ... ) )`
for the layout of the current view, behind the safety switch `explayouts.ini [AdminLayoutSettings] Enabled`.
Everything else comes from admin4.

## The editor design

`design/editor` contains only a README and empty folders. Its README explains that what makes the editor's
administration different is in the siteaccess settings (`settings/siteaccess/editor`): fewer menu tabs, modules that
answer 404, no developer toolbar. Add templates there only where the editor's administration must look different.

## Rules for every admin template

These are the project's rules for every admin design (admin, admin3, admin4, admin4l and new ones) and extension
admin templates; `tests/tests/design/AdminStylingRulesTest.php` enforces them:

- **No `<style>` block and no `style=""` attribute in a template.** CSS lives in the design's `stylesheets/`.
- **Reuse the shared components** before writing CSS; add a new shared component centrally only if nothing fits.
- **Only tokens** for colors, radius, spacing and fonts; no hard-coded values, no local token aliases. One accent,
  one radius.
- **Every new color token gets a dark value** for `html[data-a4-theme="dark"]` in the same change.
- **Module CSS** goes in `stylesheets/modules/<group>.css` with `.a4-<module>-*` class names.
- **Check light and dark**, at 1440 px (scale 1) and 960 px at scale 2 (200% zoom): no white card on a dark
  background, contrast at least 4.5:1, no horizontal overflow, no console errors.
- **CSS reaches the browser through the packer**: never add `?v=` version queries to links.

The contributor documentation is [Admin styling](../../../bc/6.0/admin4-styling.md).

## Where to go next

- Styling an extension's admin page: [Tutorial: add an admin module stylesheet](tutorial-add-an-admin-module-stylesheet.md).
- Checking a page: [The admin styling rules test](admin-styling-rules-test.md).
- Colors: [admin4 tokens](admin4-tokens.md).

## Related pages

- [Designs and siteaccesses](designs-and-siteaccesses.md)
- [Dark mode overview](dark-mode-overview.md)
- [CSP-safe design](csp-safe-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
