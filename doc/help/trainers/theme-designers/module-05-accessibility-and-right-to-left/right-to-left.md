---
title: "Lesson 5.4: Right to left"
audience: trainer, designer, developer
category: training
order: 54
summary: Learners print the text direction on their pagelayout, mark right-to-left content with exp_content_direction.tpl, write direction-safe CSS with logical properties and mirror rules, and test their dashboard block in the administration with Hebrew as the interface language. 60 minutes.
keywords: theme design, right to left, RTL, dir, text_direction, TextDirection, exp_content_direction.tpl, logical properties, inline-start, mirror rule, where dir rtl, bdi, dir auto, Hebrew, Arabic, interface language
type: help
---
# Lesson 5.4: Right to left

Hebrew, Arabic, Persian, Urdu and other languages are written right to left. A theme that hard-codes `left` and
`right` breaks for them; a theme written with logical properties serves both directions with one stylesheet.
Exponential's administration turns around completely for a right-to-left interface language. This lesson teaches
learners to do the same in their theme and to test it.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 5: Accessibility and right to left](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Advanced |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 5.2](an-accessible-site-design.md); [Lesson 4.4](../module-04-styling-the-administration/module-and-extension-stylesheets.md) for the administration part |

## Learning objectives

After this lesson, learners can:

- explain where the text direction comes from (the locale file) and how a template reads it;
- print `dir="rtl"` on the page and on right-to-left content;
- replace physical properties with logical ones, and write a mirror rule where no logical property exists;
- keep a typed name in its own direction with `dir="auto"` or `<bdi>`;
- test an administration page with a right-to-left interface language and a public page with `dir="rtl"` set by hand.

## Trainer's notes

### How to present it

Sources: [Right-to-left languages in the administration](../../../../features/6.0/right-to-left.md) and
[Right-to-left sites](../../../creators/website-creators/right-to-left-sites.md).

1. **The source** (4 minutes). `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl`; 28 locale files
   carry it. In templates: `$site.text_direction` for the interface locale of the siteaccess, and
   `$code|locale().is_right_to_left` for any locale code.
2. **The page** (3 minutes). The administration pagelayouts print:

   ```
   <html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
   ```

   A left-to-right page gets nothing new.
3. **Content** (3 minutes). Content in a right-to-left language on any page is marked with
   `design:parts/exp_content_direction.tpl`, which prints ` lang="..." dir="rtl"` for a right-to-left content locale
   and nothing for a left-to-right one; it takes `exp_locale_code`.
4. **CSS** (6 minutes). The four rules of the administration: logical properties for layout (`margin-inline-start`,
   `padding-inline-end`, `inset-inline-start`, `text-align: start`); a mirror rule where a logical property cannot
   say it, written as `:where([dir="rtl"]) .x { ... }` right after the rule it mirrors; mirror only icons that point
   along the line; never mirror alignment an editor chose.
5. **Names** (4 minutes). A name an editor typed is its own direction run: `dir="auto"` on an inline element that holds
   only the name, `<bdi>` inside a block or around a name in a translated sentence.

### Common questions

**Why `:where()` in the mirror rule?** It adds no specificity, so right to left the mirror wins and loses exactly where
the original does.

**Do I need a separate right-to-left stylesheet?** No. With logical properties one stylesheet serves both directions.

**How do I test a public right-to-left site without one?** Set `dir="rtl"` on `<html>` by hand in the developer
tools; that is how the administration's login page was checked.

### Pitfalls

- **`float: left`.** There is no logical float value that every rule set uses here; prefer flex or grid, which follow
  the direction.
- **Mirroring everything.** Logos, media controls and charts keep their direction; only icons that point along the line
  turn.
- **The shared administrator account.** Changing the interface language changes that user's preference. Use your own
  training administrator.

## Hands-on exercise

On your training installation. `D` is `extension/training_theme/design/training_theme`.

1. In `D/templates/pagelayout.tpl`, extend the `<html>` element:

   ```
   <html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
   ```

2. In `D/override/templates/full/article.tpl`, mark the article with its language's direction:

   ```
   <article class="training_theme-article"{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$node.object.current_language}>
   ```

3. Check both templates with `eztemplatecheck.php`.
4. Search `D/stylesheets/` for `left`, `right`, `float` and `text-align`. Rewrite each with a logical property, or add a
   `:where([dir="rtl"])` mirror rule right after it where none exists.
5. Add a separator that points along the line to `training.css`:

   ```css
   .training_theme-path a::after { content: " \203A"; }
   ```

   U+203A is a character that Unicode marks as mirrored: browsers turn it by themselves in right-to-left text. An arrow
   drawn by CSS (a background image, or a shape made of borders) does not turn; it would need a mirror rule such as
   `:where([dir="rtl"]) .training_theme-next::after { scale: -1 1; }`. Check the difference in step 7.
6. Run the routine of lesson 3.3 and hard reload a page of `<site>` two levels down.
7. In the developer tools, add `dir="rtl"` to the `<html>` element by hand. Check the header, menu, breadcrumb and
   content: do they read from the right? Is there a horizontal scroll bar? Remove the attribute again.
8. In the administration, signed in as your own training administrator, open the right column's **Interface** tool,
   choose `עברית — Hebrew (Israel)` in **Interface language** and press **Apply**.
9. On the dashboard, check your `training_notes` block from module 4: the table reads from the right, the current-row
   edge is on the right, the names keep their own order. Check light and dark mode.
10. Choose English again in **Interface language** and press **Apply**.

## Expected result

- Step 1: a left-to-right page has no `dir` attribute, as before.
- Step 4: the theme's stylesheets hold no physical left/right property without a mirror.
- Step 7: with `dir="rtl"` set by hand, the frame mirrors without a horizontal scroll bar; the separator of step 5
  points to the left by itself, without a rule of the learner's own.
- Step 9: `<html dir="rtl" lang="he-IL">` in the source; the block's accent edge (`border-inline-start`) is on the right
  without any change to `training_admin.css`.
- The trainer checks that every learner is back on English at the end.

## Quiz

1. Where does Exponential read whether a language is written right to left?
2. Which template marks right-to-left content with `lang` and `dir`, and which parameter does it need?
3. Which replaces `padding-right: 1rem` in a direction-safe stylesheet?
   A. `padding-end: 1rem` B. `padding-inline-end: 1rem` C. `padding-block-end: 1rem` D. `padding-right-rtl: 1rem`
4. Why is a mirror rule written with `:where([dir="rtl"])`?
5. A name typed by an editor sits inside a translated sentence. How is it isolated?

<details>
<summary>Answers</summary>

1. In the locale file, `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl`
   ([Right-to-left languages](../../../../features/6.0/right-to-left.md#where-the-direction-comes-from)).
2. `design:parts/exp_content_direction.tpl`, with `exp_locale_code`.
3. **B.**
4. `:where()` adds no specificity, so the mirror wins and loses exactly where the original rule does.
5. With `<bdi>` around the name.

</details>

## Further reading

- [Right-to-left languages in the administration](../../../../features/6.0/right-to-left.md): every rule, the names
  inside interface text and how to test.
- [Right-to-left sites](../../../creators/website-creators/right-to-left-sites.md): the public site's side.
- [Content languages](../../../../guides/content-languages.md): content in several languages.
- [Languages and right to left in class](../../train-the-trainer/module-05-accessibility-and-inclusion/languages-and-right-to-left-in-class.md):
  teaching with right-to-left languages.

## Related pages

- [Module 5: Accessibility and right to left](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
