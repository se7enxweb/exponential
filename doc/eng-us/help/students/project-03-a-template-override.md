---
title: "Project 3: a template override"
audience: student
category: learning
order: 27
summary: Study project 3 - find the design of your public siteaccess, see which template renders a page, make an override for one content class with the template editor and then as files with an override.ini rule, write the template in eZ TPL with escaped output, check it, and hand in the files and evidence, graded with a rubric.
keywords: study project, template, override, override.ini, design, template editor, path comments, eztemplatecheck, wash, fetch, node/view/full.tpl, class_identifier, learner
type: help
---
# Project 3: a template override

In this project you change how one kind of content looks on the site, without touching any file you do not own. You
do it twice: once with the template editor in the administration, once as files you keep in Git. The steps follow
[Templates and design](../../guides/templates-and-design.md).

## Contents

1. [At a glance](#at-a-glance)
2. [Goal](#goal)
3. [Before you start](#before-you-start)
4. [Tasks](#tasks)
5. [Hand in](#hand-in)
6. [Rubric](#rubric)
7. [Stretch goals](#stretch-goals)

## At a glance

| | |
|---|---|
| Level | Intermediate |
| Time | About 3 hours |
| Paths | Developer, designer |
| Prerequisites | A terminal on your study site; [Learning the template language](learning-the-template-language.md); [project 1](project-01-a-content-class-of-your-own.md) is useful |

## Goal

A full view for one content class (your "Club event" from project 1, or Article) that shows the title, one or more
attributes and a list of the item's siblings, applied only to that class, kept as files in your own Git repository.

## Before you start

Read [Templates and design](../../guides/templates-and-design.md), parts 1 to 6, and
[Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md).

## Tasks

1. **Find your design.** Run `php bin/php/console exp:ini where site/DesignSettings/SiteDesign site` (use the siteaccess
   of the page you look at). Note the design name from the `In effect:` line
   ([Templates and design](../../guides/templates-and-design.md#2-find-out-which-design-you-use)).
2. **See which template answers.** Switch on the template path comments for your public siteaccess, clear the
   template cache, open an item of your class, and search the page source for `START`
   ([Templates and design](../../guides/templates-and-design.md#3-see-which-template-wrote-which-markup)).
   Note the template that renders the full view.
3. **Click path.** In **Design > Template Editor**, open `node/view/full.tpl` of your design, press **New override**,
   choose *Default copy*, enter the object ID of one item, and **Create**. Add a visible line, save, reload the item.
   Then remove the override again with **Remove selected** ([Templates and design](../../guides/templates-and-design.md#4-make-a-change-with-an-override)).
4. **File path.** Create the template in the override directory of your design, or better, in a design extension of
   your own ([Learning path: designer](learning-path-designer.md), step 7). Write an override rule in
   `override.ini.append.php` with `Source=node/view/full.tpl`, your `MatchFile`, `Subdir=templates`, and
   `Match[class_identifier]=<your class identifier>` instead of a single object.
5. **Write the template.** Show the title with `{$node.name|wash}`, at least one attribute with
   `attribute_view_gui`, and the list of siblings from the guide's example. Every printed value is escaped.
6. **Check the syntax**: `php bin/php/eztemplatecheck.php -s<siteaccess> <your file> --allow-root-user`. Expected:
   `Template file valid: <your file>` ([Templates and design](../../guides/templates-and-design.md#5-check-the-syntax-before-you-look-at-the-browser)).
7. **Clear the caches once** for the new template path (`template-override`, the template tag and the content tag, as
   in the guide), and reload two items of your class and one item of another class. Only your class should change.
8. **Switch the path comments off** again and clear the template cache. They add markup to every template and must
   never stay on.
9. **Commit** the template and the rule to your own repository.

## Hand in

- The template file and the override rule.
- Screenshots: the page source with the `START ... (node/view/full.tpl)` comment of your file; two items of your class
  and one of another class.
- The output of the syntax check.
- A short report: why you matched by class and not by object, and what each line of the template does.

## Rubric

Score the four common criteria of [Grading your own work](grading-your-own-work.md), and these checks (8):

1. The design name was found with `exp:ini where`, not guessed.
2. No file in `design/standard/` was changed.
3. The override applies to the whole class and only to it.
4. Every printed value uses `wash` or a view function.
5. The syntax check passes.
6. The path comments are switched off at the end.
7. The files are in the learner's own Git repository, without `settings/override/` secrets.
8. The report explains `Source`, `MatchFile`, `Subdir` and `Match`.

## Stretch goals

- Add a `line` view override for the same class and use it in a list with `node_view_gui ... view='line'`.
- Order two matching overrides with `Priority` and explain which wins
  ([Template editor](../../features/6.0/template-editor-overrides.md)).
- Read [Template resources and overrides](../template-language/resources-and-overrides.md) and draw the resolution of
  your template.

## Related pages

- [Study projects: overview](study-projects-overview.md)
- [Learning the template language](learning-the-template-language.md)
- [Override basics](../designers/templates/override-ini-basics.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
