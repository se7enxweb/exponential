---
title: "Lesson 4.1: The administration styling rules"
audience: trainer, designer, developer
category: training
order: 41
summary: Learners explain why every administration design forbids inline CSS, list the seven styling rules and their documented exceptions, and find rule breaks in a sample template. 45 minutes.
keywords: theme design, admin4, administration design, styling rules, no inline CSS, style block, style attribute, components.css, tokens, dark mode, packer, AdminStylingRulesTest, extension admin design
type: help
---
# Lesson 4.1: The administration styling rules

Public themes are the designer's own; the administration is shared by every extension and every editor, and it has
strict rules. They came from experience: the admin4 module pages had grown 55 inline `<style>` blocks with four
different accent colors and white cards in dark mode. This lesson teaches the rules and the reasons, before learners
write any administration markup in the next lessons.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 4: Styling the administration](README.md) |
| Duration | 45 minutes (25 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who build administration pages or extensions with admin pages |
| Prerequisites | [Module 3](../module-03-stylesheets-and-scripts/README.md); [Lesson 1.4](../module-01-designs-and-the-fallback-chain/site-designs-and-admin-designs.md) |

## Learning objectives

After this lesson, learners can:

- state the seven styling rules of the administration designs;
- explain for each rule the problem it prevents;
- name the designs and extension designs the rules apply to;
- list the documented exceptions and why each one stays;
- find rule breaks in a template and say where the CSS should go instead.

## Trainer's notes

### How to present it

Source: [Styling the administration](../../../../bc/6.0/admin4-styling.md), sections "Why", "The rules" and
"Exceptions".

1. **The story** (5 minutes). Tell the numbers from the "Why" section: one `<style>` block in each of 55 admin4
   templates (7,831 lines), utilities copied up to 41 times, 35 private custom-property prefixes, four accent colors,
   46 blocks that hard-coded white with no dark rules. Inline CSS is not packed or cached, and extensions had no shared
   classes to build on.
2. **The seven rules** (12 minutes). Put them on one slide:
   1. No `<style>` block and no `style=""` attribute in a template of an administration design. Per-element values are
      a class (`.a4-pct-0` to `.a4-pct-100`) or a `data-*` attribute.
   2. Use the shared component classes first (`components.css`, prefix `.a4-`); never copy one into a module or an
      extension.
   3. Colors, radius, spacing and fonts come only from the `--a4-*` tokens; no literal values, no local aliases.
   4. Every color token has a value for `html[data-a4-theme="dark"]`, added in the same change.
   5. Module CSS is the exception: only what components cannot do, in `stylesheets/modules/<group>.css`, as
      `.a4-<module>-*` classes built from tokens.
   6. Check every new or changed page in light and dark mode, at 1440 px and at 960 px with scale 2 (a browser at 200%
      zoom).
   7. CSS reaches the browser through the packer; never a `?v=` query on a link.
3. **Scope** (3 minutes). Every administration design (`admin`, `admin3`, `admin4`, `admin4l`) and the admin designs
   of extensions (`extension/*/design/admin*`, `exp_adminui` and the others). Extensions keep their own stylesheet,
   loaded through `BackendCSSFileList[]`, and follow the same rules with their own prefix.
4. **Exceptions** (5 minutes). Three, each documented where it is: the inline script at the top of admin4's
   `page_head_style.tpl` (sets the color mode before the page is drawn, so it does not flash), a two-line script in
   admin3's `pagelayout.tpl` (the dragged column width), and one `<style>` block in `design/admin/templates/pagelayout.tpl`
   (column widths from `menu.ini` that a stylesheet cannot know). Stress: two of them are scripts, not CSS.

### Common questions

**Do these rules apply to my public theme?** The rules are written for administration designs. The first rule is good
practice for any theme (module 3: a Content Security Policy blocks inline styles), and the token idea works on a public
site too, but colors and radii of a public theme are the designer's choice.

**Who checks them?** A test, `tests/tests/design/AdminStylingRulesTest.php`, fails on inline CSS in the core
administration designs and on literal colors in the module stylesheets (lesson 4.5). Reviewers check the rest with
the checklist.

**What about an old template that still has a `<style>` block?** It is on the test's allowlist of templates being
migrated. The list only shrinks; nobody adds to it to make a change pass.

### Pitfalls

- **"Just this once" inline styles.** Every block in the story started that way. Hold the line in class.
- **Confusing rule 2 with "never write CSS".** Module and extension CSS exist; they come after the components and use
  tokens.
- **Forgetting dark mode.** Lesson 4.3 shows the effect; mention it here.

## Hands-on exercise

Work in pairs, on paper or in an editor; nothing is changed on the installation. The trainer hands out the sample
template below (or projects it).

```
<div class="my-report" style="padding: 12px; background: #fff;">
  <style>
    .my-report h2 { color: #c2410c; border-radius: 6px; }
    .my-report .sr { position: absolute; left: -999em; }
  </style>
  <h2>Report</h2>
  <span class="sr">Status:</span>
  <div class="bar" style="width: {$percent}%"></div>
  <a class="my-button" href={'report/run'|ezurl} onclick="return confirm('Run?')">Run</a>
</div>
```

1. Mark every place that breaks rule 1.
2. Mark every literal color, radius and spacing value (rule 3). For each, write which kind of token should replace it
   (color, radius, space).
3. Find the utility that is copied instead of reused (rule 2). Name the shared class that already does it, using the
   component table of [Styling the administration](../../../../bc/6.0/admin4-styling.md#components).
4. For `style="width: {$percent}%"`, write how rule 1 says to express a per-element value.
5. Find the `onclick`. Which other rule from module 3 does it break, and where would the code go?
6. For the `background: #fff`, describe what an editor in dark mode would see.
7. Decide where the remaining CSS belongs: a module stylesheet of the core, or an extension stylesheet listed in
   `BackendCSSFileList[]`. Write the file name you would use if this template were in your own extension.
8. Compare with another pair.

## Expected result

- Steps 1 and 2: the `<style>` block, the two `style=""` attributes, and the literals `12px`, `#fff`, `#c2410c`, `6px` are marked.
- Step 3: the screen-reader utility `.sr` is a copy of `.a4-sr-only`.
- Step 4: one of the classes `.a4-pct-0` to `.a4-pct-100` (with `.a4-meter` or `.a4-bars`), or a `data-*` attribute.
- Step 5: the inline handler breaks the Content Security Policy (`script-src 'self'`); the code goes into a script
  file of the design.
- Step 6: a white box on the dark page.
- The trainer collects one sheet per pair and goes through the answers on the projector.

## Quiz

1. Name the two kinds of inline CSS that rule 1 forbids in administration templates.
2. Which file holds the shared `.a4-*` components and the tokens?
   A. `admin4.css` B. `components.css` C. `core.css` D. `site.css`
3. What must come with every new color token?
4. At which two window sizes is a changed administration page checked?
5. Why does the inline script at the top of admin4's `page_head_style.tpl` stay?

<details>
<summary>Answers</summary>

1. A `<style>` block and a `style=""` attribute
   ([Styling the administration](../../../../bc/6.0/admin4-styling.md#the-rules)).
2. **B.** `design/admin4/stylesheets/components.css` (with an identical copy in `design/admin`).
3. A value for `html[data-a4-theme="dark"]`, in the same change.
4. 1440 px at scale 1, and 960 px at scale 2 (200% zoom), each in light and dark mode.
5. It sets the color mode before anything is drawn, so the page does not flash light and then dark; it is a
   script, not CSS.

</details>

## Further reading

- [Styling the administration: one component stylesheet, tokens, no inline CSS](../../../../bc/6.0/admin4-styling.md):
  the reference for this whole module.
- [The admin4 design](../../../../features/6.0/admin4-design.md): the design the rules were made for.
- [Removing inline scripts and styles](../../../designers/lifecycle/removing-inline-scripts-and-styles.md): migrating
  an older template.

## Related pages

- [Module 4: Styling the administration](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
