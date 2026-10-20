---
title: "Lesson 4.4: Module and extension stylesheets"
audience: trainer, designer, developer
category: training
order: 44
summary: Learners decide where administration CSS belongs (core module stylesheet or extension stylesheet), name classes with one prefix, give a state an outline for forced colors, use logical properties and stacking tokens, and check their block with forced colors emulated. 45 minutes.
keywords: theme design, admin4, module stylesheet, stylesheets/modules, AdminComponentCSSFileList, BackendCSSFileList, a4- prefix, extension prefix, forced colors, Highlight, logical properties, z-index tokens, --a4-z, is-current, aria-pressed
type: help
---
# Lesson 4.4: Module and extension stylesheets

Some things the shared components cannot do: a meter with a module's own meaning, a layout that only one page has.
That CSS is allowed, but it has a fixed place, a fixed naming scheme and three more duties: forced colors, right to
left, and stacking. This lesson covers where the CSS goes and how it is written so it survives the design tests and
every user's settings.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 4: Styling the administration](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Designers and developers who style administration pages |
| Prerequisites | [Lesson 4.3](tokens-and-dark-mode.md): `training_admin.css` loaded through `BackendCSSFileList[]` |

## Learning objectives

After this lesson, learners can:

- choose between a core module stylesheet and an extension stylesheet, and name the setting that loads each;
- name classes with one prefix per stylesheet (`.a4-<module>-*` in the core, the extension's own prefix outside);
- give a state shown only by border, background or shadow an outline for forced colors;
- write layout with logical properties, and stacking with the `--a4-z-*` tokens.

## Trainer's notes

### How to present it

Source: [Styling the administration](../../../../bc/6.0/admin4-styling.md), sections "How the stylesheets are
loaded", "Stacking layers" and "Checked in CI"; [Right-to-left languages](../../../../features/6.0/right-to-left.md).

1. **Two places** (5 minutes).

   | CSS for | Goes in | Loaded by | Class names |
   |---|---|---|---|
   | a core module page | `stylesheets/modules/<group>.css` of `design/admin4` and `design/admin` (setup, content, people, structure, workflow) | `design.ini [StylesheetSettings] AdminComponentCSSFileList[]` | `.a4-<module>-*` |
   | an extension's page | the extension's own design folder | `BackendCSSFileList[]` in the extension's `design.ini.append.php` | the extension's own prefix |

   Every admin page gets the same component bundle, packed once; extension files come after it, so an extension has the
   last word on its own markup. No per-module scope classes such as `.exp-roles` or `.nf-`.
2. **Forced colors** (5 minutes). Windows high contrast replaces colors and drops borders' colors, backgrounds and
   shadows. A state shown only by those (`.is-current`, `.is-selected`, `[aria-pressed="true"]`, `:checked`) gets
   `outline: 2px solid Highlight` in the file's `@media (forced-colors: active)` block. Show the block in
   `design/admin4/stylesheets/modules/setup.css`.
3. **Right to left** (4 minutes). Use logical properties: `margin-inline-start`, `padding-inline-end`,
   `border-inline-start`, `inset-inline-start`, `text-align: start`. Left to right they are the same; right to left
   they swap by themselves. Lesson 5.4 goes deeper.
4. **Stacking** (3 minutes). A `z-index` is a `--a4-z-*` token (`--a4-z-raised`, `--a4-z-sticky`, `--a4-z-popover` ...),
   never a number other than 0, 1 or -1 inside one component.
5. **The focus ring** (3 minutes). Never remove the outline on `:focus` or `:focus-visible`; the global ring
   (`3px solid var(--a4-ring)`) is the focus indicator.

### Common questions

**Can an extension write into `stylesheets/modules/`?** No; those files are the core's. An extension's CSS stays in the
extension, so it is installed and removed with it.

**Do the CI tests check my extension's stylesheet?** The styling test reads the core designs' templates and module
stylesheets. Extension stylesheets follow the same rules by review (lesson 4.5).

**Why an outline and not a thicker border?** Forced colors repaint borders in the system color too, so a border
change is not visible as a state; the outline in `Highlight` is.

### Pitfalls

- **Two prefixes in one file.** Pick one (`training-` in this course) and keep it.
- **`left` and `right` out of habit.** Search the file for them before you finish.
- **A big z-index "to be safe".** It lands above dialogs and menus. Use the token for what the element is.

## Hands-on exercise

On your training installation, in `training_admin.css` and `training_notes.tpl` from lessons 4.2 and 4.3. Use a
Chromium-based browser for step 8 (its developer tools can emulate forced colors).

1. Read `design/admin4/stylesheets/modules/setup.css`: find one `.a4-setup-*` class and the file's
   `@media (forced-colors: active)` block.
2. In `training_notes.tpl`, give the first table row a "current" marker: in the `{foreach}`, write
   `<tr{if $training_item.node_id|eq( $training_items[0].node_id )} class="is-current"{/if}>`.
3. In `training_admin.css`, add a state that is shown only by background and an edge on the start side:

   ```css
   .training-notes-table tr.is-current > td {
       background: var(--a4-accent-soft);
       border-inline-start: 3px solid var(--a4-accent-strong);
   }
   ```

4. Give the table the class: `<table class="a4-table training-notes-table">`.
5. Add the forced-colors outline for the state at the end of the file:

   ```css
   @media (forced-colors: active) {
       .training-notes-table tr.is-current > td { outline: 2px solid Highlight; }
   }
   ```

6. Search the file for `left`, `right`, `#`, `px` and `z-index`. Every `px` left must be `1px`, `2px` or `3px` of an
   edge or outline; change anything else to a token.
7. Run the routine of lesson 3.3 and hard reload the dashboard in light and in dark mode.
8. In the developer tools, open the rendering panel and emulate `forced-colors: active`. Find the current row.
9. Turn the emulation off again.

## Expected result

- Step 7: the first row of the block is tinted in the accent with an accent edge at its start, in both modes.
- Step 8: with forced colors emulated, the tint disappears but the row keeps a visible outline in the system highlight
  color.
- Step 6: the file has no `left`, `right`, color literal or numeric `z-index`.
- The trainer checks one learner's page with forced colors emulated on the projector.

## Quiz

1. Where does CSS for an extension's administration page go, and which setting loads it?
2. A row is "current" only by its background. What does forced-colors mode need for it?
3. Which property replaces `margin-left` so that a right-to-left page swaps it by itself?
   A. `margin-start` B. `margin-inline-start` C. `margin-block-start` D. `margin-left-rtl`
4. How is a `z-index` written in admin4 stylesheets?
5. True or false: a module stylesheet in the core may use the class `.exp-roles-list`.

<details>
<summary>Answers</summary>

1. In the extension's own design folder, listed in `BackendCSSFileList[]`
   ([Styling the administration](../../../../bc/6.0/admin4-styling.md#how-the-stylesheets-are-loaded)).
2. `outline: 2px solid Highlight` in the file's `@media (forced-colors: active)` block.
3. **B.**
4. As a `--a4-z-*` token, `z-index: var(--a4-z-...)`; only 0, 1 and -1 inside one component.
5. **False.** Core module classes are `.a4-<module>-*`; per-module scope prefixes are not used.

</details>

## Further reading

- [Styling the administration](../../../../bc/6.0/admin4-styling.md#stacking-layers): the stacking scale and the checks
  run in CI.
- [Right-to-left languages in the administration](../../../../features/6.0/right-to-left.md): logical properties and
  mirror rules.
- [Accessibility audit per release](../../../designers/lifecycle/accessibility-audit-per-release.md): keeping a theme's
  states visible.

## Related pages

- [Module 4: Styling the administration](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
