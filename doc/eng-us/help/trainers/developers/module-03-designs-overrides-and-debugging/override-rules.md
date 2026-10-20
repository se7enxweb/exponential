---
title: "Lesson 3.2: Override rules in override.ini"
audience: trainer, developer
category: training
order: 32
summary: Learners write an override rule by hand in override.ini with Source, MatchFile, Subdir and Match conditions, put the template in their extension, clear the right caches and check that their file answers. 60 minutes.
keywords: developers course, override, override.ini, Source, MatchFile, Subdir, Match, class_identifier, node, object, section, viewmode, Priority, template-override cache, full view
type: help
---
# Lesson 3.2: Override rules in override.ini

An override says: "for this class, this node or this section, use my template instead of the default". It has two
parts, a template file and a rule in `override.ini`. In this lesson learners write both by hand, inside their
extension, so the change is under version control and can be undone by removing two files.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 3: Designs, overrides and debugging](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 3.1](the-design-chain.md): `mysite_hello` is active and has a design folder |

## Learning objectives

After this lesson, learners can:

- write an override block with `Source`, `MatchFile`, `Subdir` and `Match[...]`;
- choose a match key: `class_identifier`, `node`, `object`, `section`, `parent_node`, `viewmode` and others;
- place the template in `design/<design>/override/templates/`;
- clear the `template-override`, template and content caches after adding a rule;
- explain how `Priority` decides between several matching rules.

## Trainer's notes

### How to present it

Source: [Templates and design, part 4](../../../../guides/templates-and-design.md#4-make-a-change-with-an-override).

1. **An example rule** (5 minutes). Project this block and read it line by line:

   ```ini
   [full_article_course]
   Source=node/view/full.tpl
   MatchFile=full/article_course.tpl
   Subdir=templates
   Match[class_identifier]=article
   ```

   `Source` is the template being replaced, `MatchFile` the new file relative to `override/templates/`, `Subdir`
   is always `templates`, and every `Match[...]` must hold.
2. **Match keys** (5 minutes). The template editor offers `class_identifier`, `class`, `node`, `object`, `section`,
   `section_identifier`, `remote_id`, `node_remote_id`, `parent_node`, `class_group`, `depth`, `url_alias`,
   `viewmode`, `navigation_part_identifier`, `persistent_variable`, `state` and `state_identifier`. Since 6.0.15 a
   node view also has the key `layout` (empty unless `layout/set` is used).
3. **Where the rule lives** (4 minutes). In `settings/siteaccess/<sa>/override.ini.append.php` for one siteaccess,
   or in an extension's `settings/siteaccess/<sa>/override.ini.append.php`. Keeping it in the extension ships the
   rule with its template.
4. **Order** (3 minutes). Rules with a `Priority` are tried from the lowest number up; rules without a `Priority`
   come after them. The first rule whose conditions all hold wins.
5. **Caches** (3 minutes). A new rule or a new template path needs `template-override` and the template caches
   cleared; pages already in the content view cache need `--clear-tag=content`.

### Common questions

**How do I override for two classes?** Two blocks, one per class. Conditions within one block are combined with
"and".

**Can I override a line view only inside one folder?** Yes: `Source=node/view/line.tpl` with
`Match[parent_node]=<node ID>`.

**What is in the template?** The same variables as the original: in a node view, `$node` and the view parameters.
Start from a copy of the original.

### Pitfalls

- **`MatchFile` with the `override/templates/` prefix.** It is relative to `override/templates/`; write
  `full/article_course.tpl`.
- **A block name that exists elsewhere.** Block names must be unique across all `override.ini` files of the
  siteaccess; a duplicate name merges with the other block. Prefix with your extension's name.
- **Forgetting the siteaccess folder.** A rule in the extension's `settings/override.ini.append.php`, outside a
  siteaccess folder, is meant for every siteaccess, the administration included. Put a site's rules in the
  extension's `settings/siteaccess/<sa>/` folder, as in the exercise, and check with
  `exp:ini where override.ini/<block>/MatchFile <sa>` which files define a block.

## Hands-on exercise

On the development installation, with `mysite_hello` active from lesson 3.1. `<sa>` is your public siteaccess,
`<design>` its site design.

1. Copy the kernel's full view as a starting point:

   ```bash
   mkdir -p extension/mysite_hello/design/<design>/override/templates/full
   cp design/standard/templates/node/view/full.tpl extension/mysite_hello/design/<design>/override/templates/full/mysite_article.tpl
   ```

2. Add a visible line at the top of the copy: `<p class="mysite-note">Course override: {$node.name|wash}</p>`.
3. Create the rule with `exp:ini` in the extension's siteaccess scope:

   ```bash
   php bin/php/console exp:ini set override.ini/mysite_full_article/Source node/view/full.tpl extension:mysite_hello:siteaccess:<sa>
   php bin/php/console exp:ini set override.ini/mysite_full_article/MatchFile full/mysite_article.tpl extension:mysite_hello:siteaccess:<sa>
   php bin/php/console exp:ini set override.ini/mysite_full_article/Subdir templates extension:mysite_hello:siteaccess:<sa>
   php bin/php/console exp:ini set "override.ini/mysite_full_article/Match[class_identifier]" article extension:mysite_hello:siteaccess:<sa>
   ```

4. Open the file that was written, `extension/mysite_hello/settings/siteaccess/<sa>/override.ini.append.php`, and
   compare it with the example block above.
5. Check the syntax of the template: `php bin/php/eztemplatecheck.php extension/mysite_hello/design/<design>/override/templates/full/mysite_article.tpl`.
6. Clear the caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=template-override
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-tag=content
   ```

7. Open an article on the site. The line from step 2 appears.
8. Open a folder: the line does not appear.
9. Narrow the rule to one article: add `Match[node]` with that article's node ID the same way, clear the
   `ini`, `template-override` and `content` caches, and check another article.
10. Commit the template and the settings file.

## Expected result

- Step 4: a block `[mysite_full_article]` with the four lines, inside the `<?php /* ... */ ?>` wrapper of an
  `.ini.append.php` file.
- Step 7: the article page starts with "Course override:" and its name; step 8: folders are unchanged.
- Step 9: only the chosen article shows the line.
- The trainer checks by opening one learner's article with template path comments on (lesson 3.4).

## Quiz

1. What does `Source` name in an override block?
2. `MatchFile=full/article_course.tpl` is relative to which folder?
   A. `templates/` B. `override/templates/` C. `design/` D. `settings/`
3. Two conditions in one block: are they combined with "and" or "or"?
4. Which caches must be cleared after adding a new rule and template?
5. Two rules match; one has `Priority=10`, the other none. Which wins?

<details>
<summary>Answers</summary>

1. The template being replaced, for example `node/view/full.tpl`.
2. **B.** `override/templates/` of a design in the chain.
3. "And": every `Match[...]` must hold.
4. `ini` (for the new rule), `template-override`, `template`, and `content` for pages already cached.
5. The rule with `Priority=10`: rules without a priority sort last
   ([Template editor](../../../../features/6.0/template-editor-overrides.md)).

</details>

## Further reading

- [Templates and design](../../../../guides/templates-and-design.md): overrides by click and by file, the `layout`
  key.
- [Template editor](../../../../features/6.0/template-editor-overrides.md): match keys, priorities and how the editor
  writes rules.
- [Extension module override](../../../../features/6.0/extension-module-override.md): overriding a module's templates
  from an extension.

## Related pages

- [Module 3: Designs, overrides and debugging](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
