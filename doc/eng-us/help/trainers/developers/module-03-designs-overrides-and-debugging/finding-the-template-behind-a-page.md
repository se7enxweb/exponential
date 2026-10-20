---
title: "Lesson 3.4: Finding the template behind a page"
audience: trainer, developer
category: training
order: 34
summary: Learners trace any piece of a page to its template with path comments and the list of used templates, find out why an override does not apply, and check templates with eztemplatecheck before reloading. 45 minutes.
keywords: developers course, template debug, ShowTemplatePathComments, ShowUsedTemplates, Exp Debug bar, Quick settings, eztemplatecheck, override not applied, exp:ini where, template debugging
type: help
---
# Lesson 3.4: Finding the template behind a page

The most common question of a template developer is "which file made this?", and the second is "why not mine?".
This lesson gives learners a routine that answers both in minutes: template path comments, the list of used
templates, `exp:ini where` for override rules, and the syntax checker.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 3: Designs, overrides and debugging](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 3.2](override-rules.md) and [Lesson 1.3](../module-01-the-development-installation/debug-output-and-logs.md) |

## Learning objectives

After this lesson, learners can:

- read `START` and `STOP` comments in the page source, including the original name in brackets;
- read the list of used templates in the debug output and follow its edit links;
- find every file that defines an override block with `exp:ini where`;
- check templates with `eztemplatecheck.php` and read its error messages;
- work through a checklist when an override does not apply.

## Trainer's notes

### How to present it

Sources: [Template path comments](../../../../features/6.0/template-path-comments.md) and
[Templates and design, part 7](../../../../guides/templates-and-design.md#7-debugging-templates).

1. **Path comments** (4 minutes). With `[TemplateSettings] ShowTemplatePathComments=enabled`, every template's
   output is wrapped in `<!-- START ... -->` and `<!-- STOP ... -->`. When an override answered, the original name
   follows in brackets: `<!-- START .../full/article.tpl (node/view/full.tpl) -->` reads "the file `article.tpl`
   answered the request `node/view/full.tpl`". Development only: the comments break JSON, XML and RSS output.
2. **Used templates** (3 minutes). With `ShowUsedTemplates=enabled` the debug output lists every template of the
   page, the requested one and the one used. In the administration, the **Quick settings** box has the switches
   **Debug output**, **Template debug**, **Inline template debug** and **List of used templates** for the
   current siteaccess.
3. **The checklist** (5 minutes). Project this table:

   | Question | Do this |
   |---|---|
   | Which file wrote it? | Path comments |
   | Why this file and not mine? | Check `Source`, `MatchFile`, `Match[...]`; `exp:ini where override.ini/<block>/MatchFile <sa>` |
   | Is the syntax right? | `php bin/php/eztemplatecheck.php <file>` |
   | Still the old output? | Clear `ini`, `template-override`, `template`, `content` |
   | Which templates, how long, how many queries? | The Exp Debug bar: templates, timing, SQL |

4. **The checker** (3 minutes). `eztemplatecheck.php` takes a file, a directory or `-s<siteaccess>` for all
   templates of a siteaccess. A wrong closing tag reads "Unterminated tag ... does not match tag ...".

### Common questions

**Can I switch path comments on for one siteaccess only?** Yes: `exp:ini set
site.ini/TemplateSettings/ShowTemplatePathComments enabled siteaccess:<sa>`.

**The edit links of the used templates open the wrong editor.** `[DebugSettings] UsedTemplateEditUrl` sets the
link as a `sprintf()` format, for example to the administration's editor
([Templates and design, part 7](../../../../guides/templates-and-design.md#edit-links-in-the-list-of-used-templates)).

**What about operator warnings?** A warning such as "Operator 'my_operator' has no default value for parameter
'length'" names the template and line; it is shown once per compile.

### Pitfalls

- **Comments in production.** Switch path comments off again after the lesson.
- **Searching the compiled cache.** Compiled templates under `var/` are not the files to edit. Always edit the
  `.tpl` named in the comment.
- **An override in a different siteaccess.** The rule was written for `site` but the page is served by another
  siteaccess. `exp:ini where` with the right siteaccess name shows it.

## Hands-on exercise

On the development installation, with the overrides of lesson 3.2 in place.

1. Switch on path comments for your public siteaccess and clear the template cache:

   ```bash
   php bin/php/console exp:ini set site.ini/TemplateSettings/ShowTemplatePathComments enabled siteaccess:<sa>
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   ```

2. Open an article on the site, view the page source, and search for `START`. Find the comment with
   `(node/view/full.tpl)` and write down which file answered.
3. Find the template of the page's header or footer the same way.
4. In the debug bar, open the list of used templates and find the same two files.
5. List the files that define your override block:
   `php bin/php/console exp:ini where override.ini/mysite_full_article/MatchFile <sa>`.
6. Break the rule on purpose: set `Match[class_identifier]` to `artcle` (a typo) with `exp:ini set`, clear the
   `ini`, `template-override` and `content` caches, and reload. Use the checklist to find the cause, then fix it.
7. Break the template on purpose: change one `{/if}` into `{/foreach}` and run
   `php bin/php/eztemplatecheck.php extension/mysite_hello/design/<design>/override/templates/full/mysite_article.tpl`.
   Fix it.
8. Check all templates of your extension at once: `php bin/php/eztemplatecheck.php extension/mysite_hello/design/`.
9. Switch path comments off again (`disabled`) and clear the template cache.

## Expected result

- Step 2: a comment such as `<!-- START extension/mysite_hello/design/<design>/override/templates/full/mysite_article.tpl (node/view/full.tpl) -->`.
- Step 5: the extension's siteaccess file and the value in effect.
- Step 6: the page falls back to another template; after the fix the course line is back.
- Step 7: `Template file invalid` with the line; step 8 ends without errors after the fix.
- The trainer checks that each learner can explain the bracket in step 2.

## Quiz

1. What does `(node/view/full.tpl)` at the end of a `START` comment tell you?
2. Which command lists every file that defines an override block, in load order?
   A. `exp:ini get` B. `exp:ini where` C. `eztemplatecheck.php` D. `ezcache.php --list-ids`
3. Name three reasons an override does not apply.
4. Why must path comments be off for a feed or a JSON view?
5. Which box of the administration switches debug output and the list of used templates for the current
   siteaccess?

<details>
<summary>Answers</summary>

1. An override answered; the file named before the bracket replaced the requested `node/view/full.tpl`.
2. **B.** `exp:ini where override.ini/<block>/MatchFile <sa>`.
3. A condition does not match (typo, wrong class); the rule is in another siteaccess; caches were not cleared; a
   rule with a higher priority matches first; the template file is not in a design of the chain.
4. They add HTML comments to the output, which breaks JSON, XML and RSS.
5. **Quick settings**, with **Debug output** and **List of used templates**.

</details>

## Further reading

- [Template path comments](../../../../features/6.0/template-path-comments.md): format, overrides and limits.
- [The Exp Debug bar](../../../../features/6.0/exp-debug-bar.md): templates, timing and SQL tabs.
- [Debug output improvements](../../../../features/6.0/debug-output-improvements.md): what changed in the debug
  output.

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
