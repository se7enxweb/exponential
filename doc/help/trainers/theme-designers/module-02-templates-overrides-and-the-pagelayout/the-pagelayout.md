---
title: "Lesson 2.2: The pagelayout, your site's frame"
audience: trainer, designer, developer
category: training
order: 22
summary: Learners read how a page is assembled, use the variables the pagelayout gets ($module_result, $site, $current_user), add a skip link and a breadcrumb to their own pagelayout, and check the file with eztemplatecheck. 60 minutes.
keywords: theme design, pagelayout, pagelayout.tpl, module_result, site, current_user, breadcrumb, skip link, landmarks, include, design:, eztemplatecheck, wash
type: help
---
# Lesson 2.2: The pagelayout, your site's frame

Every page of a siteaccess is the output of a module view wrapped in one template, `pagelayout.tpl`. The pagelayout
is where a designer spends most of the first days: the head, the header, the menus, the footer. In this lesson learners
learn what the pagelayout receives and extend the one the wizard wrote for them.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 2: Templates, overrides and the pagelayout](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Beginner |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 2.1](your-own-design-extension.md): `training_theme` draws the learner's public siteaccess |

## Learning objectives

After this lesson, learners can:

- describe the three steps from a request to a finished page;
- use `$module_result.content`, `$module_result.path`, `$site` and `$current_user` in a pagelayout;
- split the pagelayout into parts with `{include uri='design:...'}`;
- add a skip link, landmarks and a breadcrumb, and print user text safely with `wash`;
- check a template's syntax with `eztemplatecheck.php` before opening the browser.

## Trainer's notes

### How to present it

Source: [The pagelayout: your site's frame](../../../creators/website-creators/the-pagelayout.md) and
[Templates and design, part 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes).

1. **How a page is assembled** (4 minutes). The request reaches `index.php` and the siteaccess is chosen; the module
   view runs (for content, `content/view/full/<node>` renders `node/view/full.tpl` and puts the HTML in
   `$module_result.content`); then the pagelayout of the design chain renders around it.
2. **What the pagelayout gets** (6 minutes). Show the table on the projector:

   | Variable | Holds |
   |---|---|
   | `$module_result.content` | the HTML of the view, printed where the main content goes; never `wash` it |
   | `$module_result.path` | the breadcrumb items, each with `text`, `url` and, for content, `url_alias` |
   | `$module_result.node_id` | the node of a content page |
   | `$module_result.uri` | the address of the view |
   | `$site.title` | the page title the kernel built |
   | `$current_user` | the user viewing the page (`is_logged_in`, `contentobject_id` ...) |

3. **The language in five lines** (5 minutes). `{$x|wash}` prints and escapes; `{def}` and `{undef}` declare;
   `{foreach}` loops, `{delimiter}` separates; `{if}` with `eq`, `gt` and friends; `|ezurl` makes a link that respects
   the siteaccess and is written without quotes in an attribute. Developers get the full language in the developers
   course; designers need these.
4. **Parts** (3 minutes). Open the wizard's `pagelayout.tpl`: it includes `design:page_head.tpl`,
   `design:page_header.tpl` and `design:page_footer.tpl`. `design:` means "find this path along the chain", so a part
   can be replaced on its own.
5. **Check before you look** (2 minutes). `php bin/php/eztemplatecheck.php -s<site> <file>` prints
   `Template file valid: <file>` or `Template file invalid: <file>` with the reason.

### Common questions

**Why does `$module_result.content` not get `wash`?** It is HTML that the view already produced (and escaped where
needed). Washing it would print the tags as text.

**Where does the page title come from?** The wizard's `page_head.tpl` builds it from `$module_result.path`, last item
first, followed by the site name. `$site.title` is the kernel's version of the same.

**Can I put a `<style>` block in the pagelayout for a quick test?** Do not: styles belong in the design's
stylesheets (module 3), and a site under a Content Security Policy blocks inline styles and scripts.

### Pitfalls

- **A pagelayout error gives a blank page.** Run `eztemplatecheck.php` first; the debug output names template and
  line.
- **Fetches without a cache block.** The wizard's header fetches the top-level items on every request. Fine for class;
  lesson 2.4 puts it in a cache block.
- **Forgetting `wash`.** Every value a user typed (names, titles) is printed with `|wash`.

## Hands-on exercise

On your training installation, with template path comments on for `<site>` (lesson 1.3). All files are below
`extension/training_theme/design/training_theme/templates/`.

1. Open `pagelayout.tpl` and find the `<main>` element and `{$module_result.content}` inside it.
2. Directly after `<body ...>`, add a skip link:

   ```
   <a class="skip-link" href="#content">{'Skip to content'|i18n( 'design/training_theme' )}</a>
   ```

3. Between the header include and `<main>`, add a breadcrumb:

   ```
   <nav class="training_theme-path" aria-label="{'You are here'|i18n( 'design/training_theme' )}">
   {foreach $module_result.path as $item}
       {if $item.url}<a href={cond( is_set( $item.url_alias ), $item.url_alias, $item.url )|ezurl}>{$item.text|wash}</a>{else}<span aria-current="page">{$item.text|wash}</span>{/if}
       {delimiter} / {/delimiter}
   {/foreach}
   </nav>
   ```

4. Save, then check the syntax:

   ```bash
   php bin/php/eztemplatecheck.php -s<site> extension/training_theme/design/training_theme/templates/pagelayout.tpl
   ```

5. Open a page two levels down in the site. Check the breadcrumb and the skip link (press Tab once after loading).
6. Open `page_footer.tpl` and add a second paragraph that greets a signed-in user:

   ```
   {if $current_user.is_logged_in}<p>{'Signed in as %name'|i18n( 'design/training_theme', '', hash( '%name', $current_user.contentobject.name|wash ) )}</p>{/if}
   ```

7. Check the file with `eztemplatecheck.php`, then reload a page signed out and signed in (sign in through `/user/login`
   on the siteaccess).
8. Break the pagelayout on purpose: delete one `{/foreach}`. Run `eztemplatecheck.php` and read the message. Put the
   line back and check again.
9. In the page source, find the `START` comments of `page_header.tpl` and `page_footer.tpl` inside the pagelayout's
   comments.

## Expected result

- Step 4 and step 7 print `Template file valid: ...`.
- Step 5: the breadcrumb lists the path, the current page not linked; the first Tab shows the skip link, and following
  it moves to the content.
- Step 7: the greeting appears only when signed in.
- Step 8: the check reports the template as invalid until the line is restored.
- The trainer checks one learner's breadcrumb and skip link on the projector.

## Quiz

1. Which variable holds the HTML of the module view, and why is it printed without `wash`?
2. What does `design:` mean in `{include uri='design:page_footer.tpl'}`?
3. Which command checks a template's syntax, and what does it print for a good file?
4. Why is `$item.text` printed with `|wash`?
5. Which of these belongs in the pagelayout?
   A. A `<style>` block B. `onclick` attributes C. Landmarks such as `<header>`, `<nav>`, `<main>`, `<footer>`
   D. A `<script>` block with code

<details>
<summary>Answers</summary>

1. `$module_result.content`; it is already HTML produced by the view
   ([The pagelayout](../../../creators/website-creators/the-pagelayout.md#what-the-pagelayout-gets)).
2. Find the template along the siteaccess's design chain.
3. `php bin/php/eztemplatecheck.php -s<site> <file>`; it prints `Template file valid: <file>`.
4. It is text an editor typed; `wash` escapes HTML so it cannot inject markup or script.
5. **C.** Styles and scripts go into the design's files (module 3).

</details>

## Further reading

- [The pagelayout: your site's frame](../../../creators/website-creators/the-pagelayout.md): a minimal pagelayout,
  cache blocks and mistakes to avoid.
- [Templates and design, part 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes):
  the template language in ten minutes.
- [Menus and navigation](../../../creators/website-creators/menus-and-navigation.md): menus beyond the top level.
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md): why the
  skip link and landmarks matter (module 5).

## Related pages

- [Module 2: Templates, overrides and the pagelayout](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
