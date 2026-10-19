---
title: "Lesson 6.2: Icons in admin4"
audience: trainer, designer, developer
category: training
order: 62
summary: Learners draw admin4 icons with .a4-icon and .a4-bi-<name>, give icon-only controls an accessible name, and ship an icon of their own from their extension (an SVG, a mask rule in their stylesheet and an IconList entry). 45 minutes.
keywords: theme design, admin4, icons, a4-icon, a4-bi, Bootstrap Icons, a4-icon-btn, currentColor, mask, aria-label, aria-hidden, IconList, AdminDesignSettings, TopMenuIcons, menu.ini Icon, SVG
type: help
---
# Lesson 6.2: Icons in admin4

admin4 draws its interface icons from Bootstrap Icons, shipped as SVG files in the installation, as CSS masks in the
text color. So an icon follows the color of its link or button, its hover state and the dark mode by itself. In this
lesson learners use the shared icon classes, name icon-only controls correctly and add an icon of their own.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 6: Icons](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who build administration pages |
| Prerequisites | [Module 4](../module-04-styling-the-administration/README.md): the `training_notes` block and `training_admin.css` |

## Learning objectives

After this lesson, learners can:

- write an admin4 icon with `.a4-icon` and one `.a4-bi-<name>` class, in one of its sizes;
- give a control that is only an icon a name with `.a4-icon-btn` and `aria-label`/`title`, and hide decorative icons
  from screen readers;
- explain what `design.ini [AdminDesignSettings] IconList[]` is for;
- add an icon of their own: an SVG, a mask rule in an extension stylesheet and an `IconList[]` entry.

## Trainer's notes

### How to present it

Source: [The admin4 design, icons](../../../../features/6.0/admin4-design.md#icons) and the icon component in
[Styling the administration](../../../../bc/6.0/admin4-styling.md#components).

1. **The icon** (4 minutes). `<span class="a4-icon a4-bi-pencil" aria-hidden="true"></span>`: a 1em square that uses
   the SVG `share/icons/bootstrap-icons/svg/pencil.svg` as a mask over `currentColor`. Sizes: `.is-small` (12 px),
   `.is-medium` (16 px), `.is-large` (22 px). Bootstrap Icons 1.13.2, MIT license, served from the installation; nothing
   comes from a CDN.
2. **Names** (4 minutes). A decorative icon beside text is `aria-hidden="true"`. A control that is only an icon is a
   `<button>` or link with `.a4-icon-btn` and its name in `title` and `aria-label` (or `.a4-sr-only` text inside). An
   icon that stands for a word inside a link carries `role="img"` and `aria-label`.
3. **The list** (3 minutes). Only the icons that have a `.a4-bi-<name>` rule exist; `design.ini [AdminDesignSettings]
   IconList[]` lists them for settings that name an icon (a top menu tab's `Icon` in `menu.ini`, tiles of the Setup
   overview). A name not listed draws the plug icon; a tab whose icon is not listed shows its name alone.
4. **Your own icon** (4 minutes). The comment in `settings/design.ini` says how: add the SVG, a rule `.a4-bi-<name>` in
   a stylesheet of `BackendCSSFileList[]`, and `IconList[]=<name>` in the extension's `design.ini.append.php`.

### Common questions

**Why masks and not `<img>`?** A mask takes `currentColor`, so the icon follows hover, focus and the dark mode. An SVG in
an `<img>` cannot take the page's colors.

**Which icons are available?** The ones with a `.a4-bi-` rule in `components.css`; the shipped SVG folder holds only
the icons in use, not the whole Bootstrap set.

**Can I use a GIF icon through `ezimage` in admin4?** No; the code review checklist forbids GIF and PNG icons, inline
`<svg>` and `<input type="image">` in admin4.

### Pitfalls

- **An icon-only link without a name.** Screen readers say "link" and nothing else. Always `aria-label` and `title`.
- **A color on the icon.** It takes the text color; color the control, not the icon.
- **A third-party icon without its license.** Your own drawing, or an icon whose license allows it, with the license
  shipped (module 8).

## Hands-on exercise

On your training installation, administration in admin4, files from module 4.

1. In `training_notes.tpl`, put an icon before the button's text:

   ```
   <a class="a4-btn" href=...><span class="a4-icon a4-bi-diagram-3" aria-hidden="true"></span> {'Open the content tree'|i18n( 'design/admin/dashboard/training_notes' )}</a>
   ```

   (keep the `href` you had).
2. After the heading, add an icon-only link to the dashboard itself, to reload it:

   ```
   <a class="a4-icon-btn" href={'content/dashboard'|ezurl} title="{'Reload the dashboard'|i18n( 'design/admin/dashboard/training_notes' )}" aria-label="{'Reload the dashboard'|i18n( 'design/admin/dashboard/training_notes' )}"><span class="a4-icon a4-bi-arrow-clockwise" aria-hidden="true"></span></a>
   ```

3. Check the template with `eztemplatecheck.php`, clear `--clear-tag=template` and reload the dashboard. Hover the
   icon link, then reach it with Tab.
4. Switch to dark mode and back: both icons follow the text color.
5. Draw an icon of your own: save as `extension/training_theme/design/admin4/images/training-flag.svg`:

   ```xml
   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M3 1h1v14H3zM5 2h8l-2 3 2 3H5z"/></svg>
   ```

6. Add its mask rule to `training_admin.css`:

   ```css
   .a4-bi-training-flag {
       -webkit-mask-image: url(../images/training-flag.svg);
       mask-image: url(../images/training-flag.svg);
   }
   ```

7. Add `IconList[]=training-flag` under `[AdminDesignSettings]` in `extension/training_theme/settings/design.ini.append.php`.
8. Use it in the block's heading: `<span class="a4-icon a4-bi-training-flag" aria-hidden="true"></span>` before the text.
9. Run the routine of lesson 3.3 (the packer rewrites the relative `url()` in the packed file), clear the INI cache, and
   hard reload. Check light and dark mode.

## Expected result

- Steps 3 and 4: the button shows the tree icon, the icon link shows a circular arrow, both in the text color, the
  link with its ring on focus, in both modes.
- Step 9: the heading shows the learner's flag in the text color.
- A screen reader (or the accessibility panel) names the icon link "Reload the dashboard"; the decorative icons are
  hidden.
- The trainer checks one learner's block in both modes and the accessible name of the icon link.

## Quiz

1. Write the markup of a decorative pencil icon in admin4.
2. A link holds only an icon. What does it need?
   A. Nothing B. `title` and `aria-label` (or `.a4-sr-only` text) C. An `alt` attribute on the span D. A tooltip script
3. Why does an `.a4-icon` change color in dark mode by itself?
4. What happens when a menu tab names an icon that is not in `IconList[]`?
5. Name the three things an extension adds to ship an icon of its own.

<details>
<summary>Answers</summary>

1. `<span class="a4-icon a4-bi-pencil" aria-hidden="true"></span>`
   ([The admin4 design, icons](../../../../features/6.0/admin4-design.md#icons)).
2. **B.** With `.a4-icon-btn` for its look.
3. It is a mask over `currentColor`, which follows the text color and so the tokens.
4. The tab shows its name alone, without an icon.
5. The SVG file, a `.a4-bi-<name>` mask rule in a stylesheet of `BackendCSSFileList[]`, and `IconList[]=<name>` in its
   `design.ini.append.php`.

</details>

## Further reading

- [The admin4 design, icons](../../../../features/6.0/admin4-design.md#icons): every place admin4 draws icons.
- [Styling the administration](../../../../bc/6.0/admin4-styling.md#code-review-checklist): the icon item of the
  checklist.
- [Third-party fonts, icons and libraries](../../../designers/distribution/third-party-fonts-icons-and-libraries.md):
  licensing icons you did not draw.

## Related pages

- [Module 6: Icons](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
