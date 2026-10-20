---
title: "Lesson 1.3: Which template wrote this markup?"
audience: trainer, designer, developer
category: training
order: 13
summary: Learners switch on template path comments for one siteaccess, find the template behind a piece of a page in the page source, read the override note in brackets, and switch the comments off again. 45 minutes.
keywords: theme design, template path comments, ShowTemplatePathComments, START, STOP, view source, debug bar, used templates, template cache, exp:ini set
type: help
---
# Lesson 1.3: Which template wrote this markup?

The fastest way to learn a design is to ask a page which files made it. With template path comments switched on,
Exponential wraps the output of every template in two HTML comments that name the file. This lesson makes that the
learners' first reflex whenever a page does not look as expected.

> **Note** Template path comments are for development only. They add markup to every template and break feeds, JSON
> and XML. Never leave them on for a live siteaccess.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 1: Designs and the fallback chain](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 1.2](reading-the-chain-of-a-siteaccess.md); a browser that can show the page source |

## Learning objectives

After this lesson, learners can:

- switch template path comments on and off for one siteaccess with `exp:ini set`;
- clear the template cache so that the change takes effect;
- find the template behind a piece of markup with the `START` and `STOP` comments;
- read the name in brackets as "this file answered the request for that template".

## Trainer's notes

### How to present it

Source: [Template path comments](../../../../features/6.0/template-path-comments.md) and
[Templates and design, part 3](../../../../guides/templates-and-design.md#3-see-which-template-wrote-which-markup).

1. **The setting** (3 minutes). `site.ini [TemplateSettings] ShowTemplatePathComments`, default `disabled`. Any value
   other than `enabled` is off. Set it for one siteaccess only:

   ```bash
   php bin/php/console exp:ini set site/TemplateSettings/ShowTemplatePathComments enabled siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=template
   ```

   The cache clear is needed because compiled templates carry the check.
2. **Reading the comments** (5 minutes). Open a page of the siteaccess, then the page source, and search for
   `START`. Each piece of output is wrapped:

   ```html
   <!-- START design/plain/templates/pagelayout.tpl -->
   ...
   <!-- STOP design/plain/templates/pagelayout.tpl -->
   ```

   Nested comments show the include structure: the pagelayout, inside it the parts it includes, inside the content
   area the view template.
3. **The bracket** (3 minutes). When an override answered, the requested name follows in brackets:
   `<!-- START .../full/article.tpl (node/view/full.tpl) -->`. Read it aloud: "the file `article.tpl` answered the
   request `node/view/full.tpl`". Lesson 2.3 writes such an override.
4. **Other tools** (4 minutes). The same switch is in the Exp Debug bar (settings group templates). In the right
   column of the administration, **Quick settings** has **Template debug** and **List of used templates**, which list
   the templates of a page in the debug output. Show where they are; this course uses the comments because they work
   on any siteaccess.

### Common questions

**The comments do not appear.** The template cache still holds compiled templates from before; clear it again. Check
with `exp:ini where site/TemplateSettings/ShowTemplatePathComments <site>` that the value is in effect for the right
siteaccess.

**Can visitors see the comments?** Yes, anyone who views the source. That is one more reason to keep them on training
and development siteaccesses only.

**Do they slow the site?** A little, and the output is larger. Irrelevant on a training site; one more reason to switch
them off afterwards.

### Pitfalls

- **Switching it on in `settings/override/`.** Every siteaccess gets the comments, including feeds and the
  administration. Use `siteaccess:<site>`.
- **Forgetting to switch off.** End the exercise with the off step and check it, as below.
- **Searching the rendered page instead of the source.** Comments are invisible on the page; use "view source", not
  the browser's element inspector, which may hide comments depending on its settings.

## Hands-on exercise

On your training installation, with the public siteaccess `<site>` from lesson 1.2 served in a browser (for example
with `php bin/php/console exp:velocity start`, see
[Lesson 2.4 of Train the trainer](../../train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md)).

1. Switch the comments on for your siteaccess:

   ```bash
   php bin/php/console exp:ini set site/TemplateSettings/ShowTemplatePathComments enabled siteaccess:<site>
   ```

2. Confirm it: `php bin/php/console exp:ini where site/TemplateSettings/ShowTemplatePathComments <site>`. The last
   line after `In effect:` reads `ShowTemplatePathComments=enabled`.
3. Clear the template cache: `php bin/php/ezcache.php --clear-tag=template`.
4. Open the front page of the siteaccess in the browser and open the page source.
5. Search for the first `START`. Note the file: it is the pagelayout of the site design.
6. Find the footer of the page in the source and the nearest `START` above it. Note the file.
7. Open any content page (an article or a folder) and find the template of the main content area. Is there a name in
   brackets?
8. Compare the files from steps 5 to 7 with the chain you drew in lesson 1.2: which design did each come from?
9. Switch the comments off again:

   ```bash
   php bin/php/console exp:ini set site/TemplateSettings/ShowTemplatePathComments disabled siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=template
   ```

10. Reload the page source and check that no `START` comment is left.

## Expected result

- Steps 5 to 7: each learner names three template files with their full path, for example the pagelayout of their
  site design and a view template.
- Step 8: each file sits in a design of the chain from lesson 1.2, and learners can say why that design won.
- Step 10: the source has no `START` comments.
- The trainer checks by asking two learners to read one of their comments aloud and point to the file on disk.

## Quiz

1. Which setting wraps every template's output in `START` and `STOP` comments?
2. Why is the template cache cleared after switching the setting?
3. What does `<!-- START design/media/templates/full/article.tpl (node/view/full.tpl) -->` tell you?
   A. Two templates were both rendered B. `article.tpl` answered a request for `node/view/full.tpl` C. The override
   failed D. `node/view/full.tpl` includes `article.tpl`
4. Name two kinds of output that break when the comments are on.
5. In which scope do you switch the comments on for a training siteaccess, and why?

<details>
<summary>Answers</summary>

1. `site.ini [TemplateSettings] ShowTemplatePathComments=enabled`
   ([Template path comments](../../../../features/6.0/template-path-comments.md)).
2. Compiled templates carry the check; without the clear they keep running without comments.
3. **B.** The bracket names the `Source` of the matching override rule.
4. Any two of: RSS and other XML feeds, JSON output, anything that parses the HTML strictly.
5. `siteaccess:<site>`, so that only that siteaccess gets them, not feeds or the administration.

</details>

## Further reading

- [Template path comments](../../../../features/6.0/template-path-comments.md): details, limits and the debug bar
  switch.
- [Exp Debug bar](../../../../features/6.0/exp-debug-bar.md): the templates, timing and SQL tabs.
- [Templates and design, part 7: debugging templates](../../../../guides/templates-and-design.md#7-debugging-templates):
  the checklist when a page is wrong.

## Related pages

- [Module 1: Designs and the fallback chain](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
