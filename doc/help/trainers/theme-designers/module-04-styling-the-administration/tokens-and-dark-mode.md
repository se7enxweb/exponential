---
title: "Lesson 4.3: Tokens and dark mode"
audience: trainer, designer, developer
category: training
order: 43
summary: Learners read the --a4-* tokens in the browser, add an extension stylesheet through BackendCSSFileList, see a hard-coded color fail in dark mode, and fix it with tokens so the rule works in both modes without a dark rule of its own. 60 minutes.
keywords: theme design, admin4, tokens, custom properties, --a4-card, --a4-ink, --a4-accent, --a4-space, --a4-radius-s, dark mode, data-a4-theme, color-scheme, BackendCSSFileList, extension stylesheet, contrast
type: help
---
# Lesson 4.3: Tokens and dark mode

admin4 has a light and a dark mode. It does not achieve that with two sets of rules: every color, radius, space and
font comes from a custom property, a **token**, and the dark mode only changes the tokens' values. A rule written with
tokens works in both modes by itself; a rule with `#fff` paints a white box on the dark page. Learners see both in
this lesson.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 4: Styling the administration](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who style administration pages |
| Prerequisites | [Lesson 4.2](shared-components.md): the `training_notes` dashboard block |

## Learning objectives

After this lesson, learners can:

- name the token groups (accent, neutral, states, spacing, radius, type) and read a token's value in the browser;
- explain how `html[data-a4-theme="dark"]` switches the tokens, and why a component needs no dark rule;
- add an extension stylesheet to the administration with `BackendCSSFileList[]`;
- replace literal colors, radii and lengths with tokens, choosing the nearest spacing step.

## Trainer's notes

### How to present it

Source: [Styling the administration, tokens and dark mode](../../../../bc/6.0/admin4-styling.md#tokens) and
[The admin4 design](../../../../features/6.0/admin4-design.md).

1. **The groups** (6 minutes). From the token table: accent (`--a4-accent`, `--a4-accent-strong` for filled controls
   with white text, `--a4-accent-text` for accent-colored text, `--a4-accent-soft` for tints); neutral (`--a4-ink`,
   `--a4-muted`, `--a4-line`, `--a4-card`, `--a4-surface`, `--a4-soft`); states (`--a4-ok`, `--a4-warn`, `--a4-bad`,
   `--a4-info` with `-bg` and `-line`); spacing `--a4-space-1` to `--a4-space-7` (4, 8, 12, 16, 24, 32, 48 px); the one
   component radius `--a4-radius-s`; type `--a4-text-xs` to `--a4-text-3xl` and `--a4-font`.
2. **One accent, one radius** (2 minutes). There is one accent and one component radius; nobody adds a second.
3. **How dark mode works** (5 minutes). The mode is `html[data-a4-theme="light"|"dark"]`, set before the page is drawn
   from the user's choice (the eZPreference `admin_color_mode`). `components.css` gives every color token a second
   value for the dark mode and sets `color-scheme`, so scrollbars and form controls follow. A component or module rule
   never needs an `html[data-a4-theme="dark"]` rule of its own: the tokens switch.
4. **Extension stylesheets** (4 minutes). An extension keeps its stylesheet in its own design folder and lists it in
   `BackendCSSFileList[]`; admin4 loads that list after its own stylesheets, so an extension has the last word on its own
   markup. Its classes take the extension's own prefix, its values come from the `--a4-*` tokens.
5. **Nearest step** (3 minutes). A length between two spacing steps takes the nearer one: 3 and 5 become 4, 6 to 9
   become 8, 10 to 14 become 12, 15 to 20 become 16, 22 to 28 become 24.

### Common questions

**Can my extension define its own tokens?** Not as aliases of the core ones (`--my-ink: var(--a4-ink)` is exactly what
the rules forbid). A real new value that the whole administration needs is proposed for `components.css`, with a light
and a dark value.

**Why does `--a4-accent-strong` exist next to `--a4-accent`?** White text on the brand orange is only 3:1; filled
controls with white text use the darker shade for 5.2:1.

**What happens without JavaScript?** The admin is light, its documented default.

### Pitfalls

- **Testing only in light mode.** Make the dark check a habit: every exercise from now on ends with it.
- **Tokens outside admin4.** The `--a4-*` tokens exist where `components.css` is loaded (the administration designs).
  A public theme defines its own custom properties.
- **Forgetting the packer.** A new stylesheet in `BackendCSSFileList[]` needs the INI cache and the routine of lesson
  3.3.

## Hands-on exercise

On your training installation, administration in `admin4` or `admin4l`, with the `training_notes` block from lesson
4.2.

1. On the dashboard, open the inspector, select the `<html>` element and read its `data-a4-theme` attribute. Then
   select any card and read the computed values of `--a4-card` and `--a4-ink`.
2. Switch to dark mode with the header's color mode button and read the same two values again.
3. Add a note paragraph to `training_notes.tpl`, directly under the heading:

   ```
   <p class="training-note">{'Items are listed by last modification.'|i18n( 'design/admin/dashboard/training_notes' )}</p>
   ```

4. Create `extension/training_theme/design/admin4/stylesheets/training_admin.css` with a first version written the
   wrong way, on purpose:

   ```css
   .training-note { background: #fff; color: #333; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
   ```

5. List it in `extension/training_theme/settings/design.ini.append.php`, in `[StylesheetSettings]`:

   ```ini
   BackendCSSFileList[]=training_admin.css
   ```

6. Clear the INI cache and run the routine of lesson 3.3 (`ezjscore-packer`, `template`, `template-block`), then hard
   reload the dashboard in dark mode. Note what the note looks like.
7. Rewrite the rule with tokens:

   ```css
   .training-note {
       background: var(--a4-soft);
       color: var(--a4-ink);
       padding: var(--a4-space-3);
       border: 1px solid var(--a4-line);
       border-radius: var(--a4-radius-s);
   }
   ```

8. Run the routine again and hard reload. Check the note in dark mode, then in light mode.
9. Explain to your neighbour why `10px` became `var(--a4-space-3)`.

## Expected result

- Steps 1 and 2: `data-a4-theme` changes from `light` to `dark`, and with it the values of `--a4-card` and `--a4-ink`.
- Step 6: in dark mode the note is a white box with grey text on the dark card, the bug the rules prevent.
- Step 8: the note sits on a slightly different surface in both modes, with readable text, rounded like the other
  components, and the stylesheet holds no literal color.
- The trainer checks the final `training_admin.css` of two learners: only tokens, no `html[data-a4-theme]` rule.

## Quiz

1. Which attribute on `<html>` switches admin4 to dark mode?
2. Why does a rule written with tokens need no dark rule of its own?
3. Through which list does an extension add its stylesheet to the administration?
   A. `FrontendCSSFileList[]` B. `AdminComponentCSSFileList[]` C. `BackendCSSFileList[]` D. `CSSFileList[]`
4. A design asks for 18 px of padding. Which spacing token do you use?
5. Which token colors text in the accent color, and which fills a button that has white text?

<details>
<summary>Answers</summary>

1. `data-a4-theme`, with the value `dark` (`html[data-a4-theme="dark"]`).
2. The tokens' values change with the mode; every color token has a dark value
   ([Styling the administration, dark mode](../../../../bc/6.0/admin4-styling.md#dark-mode)).
3. **C.** `AdminComponentCSSFileList[]` is for the core's own component and module files.
4. `var(--a4-space-4)` (16 px): 15 to 20 take 16.
5. `--a4-accent-text` for text; `--a4-accent-strong` for the fill of a control with white text.

</details>

## Further reading

- [Styling the administration](../../../../bc/6.0/admin4-styling.md#tokens): every token with its purpose.
- [The admin4 design](../../../../features/6.0/admin4-design.md): light, dark and system mode and the remembered
  choices.
- [Accessibility standards](../../../../standards/accessibility.md): the contrast test that checks the tokens.

## Related pages

- [Module 4: Styling the administration](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
