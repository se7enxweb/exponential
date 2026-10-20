---
title: "Lesson 5.2: An accessible site design"
audience: trainer, designer, developer
category: training
order: 52
summary: Learners make their theme's frame accessible - page language, named landmarks, the current page in the menu, one h1 per page, a visible focus ring, reduced motion - and check each change in the page source and with the keyboard. 60 minutes.
keywords: theme design, accessibility, lang, landmarks, nav aria-label, aria-current, skip link, h1, headings, focus-visible, outline, prefers-reduced-motion, contrast, alt text, pagelayout
type: help
---
# Lesson 5.2: An accessible site design

Most of what makes a public site accessible is decided once, in the theme: the page language, the landmarks, the skip
link, the heading order, the focus ring, contrast and motion. In this lesson learners go through the template
author's list and apply each item to `training_theme`.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 5: Accessibility and right to left](README.md) |
| Duration | 60 minutes (15 presenting, 45 exercise) |
| Level | Intermediate |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 5.1](what-exponential-promises.md); `training_theme` with the skip link and breadcrumb of lesson 2.2 |

## Learning objectives

After this lesson, learners can:

- print the page language from the siteaccess and mark the landmarks of the frame;
- name several navigation landmarks with `aria-label` and mark the current page with `aria-current="page"`;
- keep one `h1` per page and headings in order;
- give every link and control a visible focus style and respect `prefers-reduced-motion`.

## Trainer's notes

### How to present it

Source: [Accessibility for site builders, what the template author controls](../../../creators/website-creators/accessibility-for-site-builders.md#what-the-template-author-controls).

1. **The list** (6 minutes). Go through the table: language (`<html lang="...">` from
   `$site.http_equiv.Content-language`), landmarks (`header`, `nav` with `aria-label` when there are several, `main`,
   `footer`), skip link, one `h1` per page, current page with `aria-current="page"`, visible focus (never
   `outline: none` without a replacement), contrast 4.5:1 for text and 3:1 for large text, images with `alt`, reduced
   motion, 200% zoom without horizontal scrolling.
2. **Where each one goes in the theme** (5 minutes). `pagelayout.tpl` (language, landmarks, skip link), the header part
   (menu, current page), the view templates (headings), `training.css` (focus, motion, contrast).
3. **The administration as an example** (4 minutes). admin4 has one shared focus ring (3 px outline in its ring
   color) and a `prefers-reduced-motion` rule; show them as a model for the public theme.

### Common questions

**Which language code goes in `lang`?** `$site.http_equiv.Content-language` gives the siteaccess's content language
code, as the standard pagelayout uses. The wizard's pagelayout builds it from `ContentObjectLocale`; both work, the
first follows the siteaccess's HTTP settings.

**Is the site name in the header an `h1`?** No: the page's own name is the `h1` (the article title, the folder name).
The site name is a link.

**Do I need ARIA everywhere?** No. Native elements first (`<nav>`, `<main>`, `<button>`); ARIA where HTML has nothing,
such as `aria-current`.

### Pitfalls

- **Two `h1` elements.** One from the pagelayout and one from the view. Check with the outline in step 6.
- **A focus style removed by a reset stylesheet.** Search the theme's CSS for `outline: none` and `outline: 0`.
- **`aria-current` on every item.** Only on the item of the current page (`"page"`) or of the section the page is in (`"true"`).

## Hands-on exercise

On your training installation. `D` is `extension/training_theme/design/training_theme`.

1. In `D/templates/pagelayout.tpl`, change the `<html>` element to
   `<html lang="{$site.http_equiv.Content-language|wash}">`.
2. In `D/templates/page_header.tpl`, give the `<nav>` a name:
   `<nav class="training_theme-menu" aria-label="{'Main menu'|i18n( 'design/training_theme' )}">`.
3. In the menu loop, mark the current section. The path of the page is in `$module_result.path`; its second item (index 1) is the
   top-level item when the page is below the site's top:

   ```
   <li><a href={$training_theme_item.url_alias|ezurl}{if and( is_set( $module_result.path[1].node_id ), $module_result.path[1].node_id|eq( $training_theme_item.node_id ) )} aria-current="{if $module_result.path|count|eq( 2 )}page{else}true{/if}"{/if}>{$training_theme_item.name|wash}</a></li>
   ```

   The menu now differs per page: if you wrapped it in a cache block in lesson 2.4, add `$module_result.uri` to its
   keys.
4. In `D/stylesheets/training.css`, add a focus style and a reduced-motion rule:

   ```css
   a:focus-visible, button:focus-visible, input:focus-visible { outline: 3px solid #1f4fa8; outline-offset: 2px; }
   .training_theme-menu [aria-current] { font-weight: bold; text-decoration: underline; }
   @media (prefers-reduced-motion: reduce) {
       *, *::before, *::after { transition: none !important; animation: none !important; }
   }
   ```

5. Run `eztemplatecheck.php` on the two templates, then the routine of lesson 3.3 and a hard reload.
6. Open an article. In the developer tools' accessibility panel (or by searching the source), list the headings: one
   `h1`, the article title.
7. Press Tab from the top of the page: skip link, site name, menu items, breadcrumb links, content links. Check that the
   focus is visible at every stop.
8. Open a page inside a top-level folder. Check that its menu item carries `aria-current="true"` in the source and
   looks different.
9. Check the contrast of your focus color and of the menu text against their backgrounds with the developer tools'
   color picker (it shows the contrast ratio).

## Expected result

- Step 1: the source shows `<html lang="en">` or the language code of the siteaccess.
- Step 6: exactly one `h1` on the article page.
- Step 7: every focus stop shows a visible ring.
- Step 8: the current top-level item is marked in the source and visibly.
- Step 9: contrast ratios of at least 4.5:1 for text and 3:1 for the focus ring.
- The trainer checks one learner's page with the keyboard only, on the projector.

## Quiz

1. Which variable gives the page language for `<html lang>`?
2. The page has two `<nav>` elements. What does each need?
3. Which attribute marks the current page in a menu?
   A. `aria-selected` B. `aria-current="page"` C. `aria-pressed` D. `class="active"` alone
4. What is wrong with `a:focus { outline: none; }` in a theme?
5. What should a theme do when the user's system asks for reduced motion?

<details>
<summary>Answers</summary>

1. `$site.http_equiv.Content-language`
   ([Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md#what-the-template-author-controls)).
2. A name, with `aria-label`, so screen reader users can tell them apart.
3. **B.**
4. It removes the visible focus, so keyboard users cannot see where they are; never remove it without a replacement.
5. Stop transitions and animations with a `@media (prefers-reduced-motion: reduce)` rule.

</details>

## Further reading

- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md): the full
  list, forms and the checklist.
- [Menus and navigation](../../../creators/website-creators/menus-and-navigation.md): menus that mark the current page.
- [Accessibility standards](../../../../standards/accessibility.md): how the administration does it.

## Related pages

- [Module 5: Accessibility and right to left](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
