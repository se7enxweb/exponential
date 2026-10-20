---
title: "Lesson 2.3: Overrides with override.ini"
audience: trainer, designer, developer
category: training
order: 23
summary: Learners read an override rule (Source, MatchFile, Subdir, Match), see the wizard's article override answer in the page source, write a second override for the line view of articles, and find both in the Template Editor. 60 minutes.
keywords: theme design, override, override.ini, Source, MatchFile, Subdir, Match, class_identifier, node, section, viewmode, Priority, template-override cache, Template Editor, templateview
type: help
---
# Lesson 2.3: Overrides with override.ini

A file in `templates/` replaces a template for every page. An **override** replaces it only where a rule matches: one
class, one node, one section. Overrides are how a theme gives articles, folders and the front page their own look
without touching anything else. In this lesson learners read the example override the wizard wrote, write one of
their own and find both in the administration.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 2: Templates, overrides and the pagelayout](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 2.2](the-pagelayout.md); published articles in a folder of the training site |

## Learning objectives

After this lesson, learners can:

- read and write an override rule with `Source`, `MatchFile`, `Subdir` and `Match[...]`;
- put the override template in the right folder of their design;
- clear the caches a new template path needs;
- prove with template path comments which override answered;
- find the overrides of a template in the **Template Editor**.

## Trainer's notes

### How to present it

Source: [Templates and design, part 4](../../../../guides/templates-and-design.md#4-make-a-change-with-an-override),
[Template overrides for site builders](../../../creators/website-creators/template-overrides.md) and
[Template editor](../../../../features/6.0/template-editor-overrides.md).

1. **The rule** (6 minutes). Open `extension/training_theme/settings/override.ini.append.php`:

   ```ini
   [full_article_training_theme]
   Source=node/view/full.tpl
   MatchFile=full/article.tpl
   Subdir=templates
   Match[class_identifier]=article
   ```

   `Source` is the template being replaced, `MatchFile` the replacement relative to `override/templates/`, `Subdir` is
   always `templates`, and every `Match[...]` line must hold (`class_identifier`, `node`, `object`, `section`,
   `parent_node`, `viewmode` and more). The block name only has to be unique.
2. **Order** (3 minutes). Rules with a `Priority` come first, the lowest number first; rules without one follow. The
   first rule whose conditions all hold wins.
3. **The caches** (3 minutes). A new template path needs the `template-override` and template caches cleared once,
   and the content view cache so cached pages are drawn again. Later edits of the same file need no clear while the
   template cache is disabled.
4. **The Template Editor** (5 minutes). In the administration, open the **Template Editor** (Design menu,
   `/visual/templatelist`) and the template `node/view/full.tpl`. The page "Overrides for ... template in ...
   siteaccess" lists the overrides in the order they are tried, with their conditions, arrows to reorder, **Edit**,
   **New override**, **Save conditions** and **Remove selected**. Show it; do not change anything on a shared site.
5. **Where overrides belong** (3 minutes). In a theme: the rule in the extension's `settings/override.ini.append.php`
   and the file in the extension's design. Rules written by the Template Editor land in
   `settings/siteaccess/<site>/override.ini.append.php` of the installation; a theme author moves them into the
   extension.

### Common questions

**What is the difference between `node` and `object` in `Match`?** `node` is one place in the tree, `object` the
content item in all its places.

**Can one override serve several classes?** No; one `Match[class_identifier]` per rule. Write one rule per class, all
pointing at the same `MatchFile` if the look is the same.

**My override is ignored.** Check `Source` (the exact template name), `MatchFile` (relative to
`override/templates/`), the conditions, then clear `template-override` and `template`.
`php bin/php/console exp:ini where override/<block>/MatchFile <site>` lists the files that define the block.

### Pitfalls

- **The file in `templates/` instead of `override/templates/`.** It is never found by the rule.
- **A typo in the class identifier.** The rule silently never matches; read the identifier under **Setup > Classes**
  or in the class's line of the content view.
- **Testing on a cached page.** Clear the content view cache after a new override, or the old page is served.

## Hands-on exercise

On your training installation, with template path comments on for `<site>`. The design folder is
`extension/training_theme/design/training_theme/`.

1. Read `extension/training_theme/settings/override.ini.append.php` and the template it names,
   `override/templates/full/article.tpl`.
2. Open an article on `<site>`. In the page source, find the comment
   `<!-- START .../full/article.tpl (node/view/full.tpl) -->`.
3. Open a folder that lists articles. Find the `START` comment of the template that draws each article in the list
   (`node/view/line.tpl`, from the standard design).
4. Create the line override template:

   ```bash
   mkdir -p extension/training_theme/design/training_theme/override/templates/line
   ```

   and save as `.../override/templates/line/article.tpl`:

   ```
   <div class="training_theme-line">
       <h2><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h2>
       {if $node.data_map.intro.has_content}
           {attribute_view_gui attribute=$node.data_map.intro}
       {/if}
   </div>
   ```

5. Add the rule to `extension/training_theme/settings/override.ini.append.php`, before the closing `*/ ?>`:

   ```ini
   [line_article_training_theme]
   Source=node/view/line.tpl
   MatchFile=line/article.tpl
   Subdir=templates
   Match[class_identifier]=article
   ```

6. Check the template: `php bin/php/eztemplatecheck.php -s<site> extension/training_theme/design/training_theme/override/templates/line/article.tpl`.
7. Clear the caches a new template path needs:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=template-override
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-tag=content
   ```

8. Reload the folder. Find `<!-- START .../line/article.tpl (node/view/line.tpl) -->` in the source.
9. In the administration, open the **Template Editor**, filter for `node/view/line.tpl`, open it, choose your siteaccess
   in **Siteaccess**, and find your override in the list. Do not press **Save conditions** or **Remove selected**.

## Expected result

- Step 2 shows the wizard's override answering `node/view/full.tpl` for articles.
- Step 8: the folder lists each article with its linked name and intro, and the comment names the learner's
  `line/article.tpl` with `(node/view/line.tpl)` in brackets. Other classes in the folder still use the standard line
  template.
- Step 9: the override appears in the list for the learner's siteaccess with the condition `class_identifier`.
- The trainer checks one learner's folder page and its source on the projector.

## Quiz

1. In an override rule, what is `Source`, and what is `MatchFile` relative to?
2. A rule has `Match[class_identifier]=article` and `Match[section]=1`. When does it apply?
   A. To every article B. To every item in section 1 C. To articles in section 1 D. Never
3. Which caches does a new override path need cleared?
4. Two rules match the same page; neither has a `Priority`. How do you decide which is used, and how do you change it?
5. Where does a theme keep its override rules?

<details>
<summary>Answers</summary>

1. `Source` is the template being replaced; `MatchFile` is relative to `override/templates/` of the design
   ([Templates and design, part 4](../../../../guides/templates-and-design.md#4-make-a-change-with-an-override)).
2. **C.** All `Match` lines must hold.
3. `template-override` and the template cache, plus the content view cache so cached pages are drawn again (and the
   INI cache when the rule is new).
4. The first rule tried wins; check the order in the **Template Editor** and give the rules a `Priority` (lowest
   first), or reorder them there
   ([Template editor](../../../../features/6.0/template-editor-overrides.md)).
5. In the extension's `settings/override.ini.append.php`, with the templates in its design's `override/templates/`.

</details>

## Further reading

- [Template overrides for site builders](../../../creators/website-creators/template-overrides.md): conditions and
  common rules.
- [Template editor](../../../../features/6.0/template-editor-overrides.md): the click path, reordering, backups.
- [Template override ordering](../../../../features/6.0/template-override-ordering.md): how the order is decided.
- [Comparing overrides with the standard](../../../designers/lifecycle/comparing-overrides-with-the-standard.md):
  keeping overrides current after an upgrade.

## Related pages

- [Module 2: Templates, overrides and the pagelayout](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
