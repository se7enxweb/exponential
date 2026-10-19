---
title: "Lesson 4.4: Theme designers, practical task"
audience: trainer, designer
category: training
order: 44
summary: A 75-minute practical task for the end of the Theme design course, build a theme extension with its own pagelayout, register the design, use it for one siteaccess only, load a stylesheet without inline CSS, override one template and prove the result with template path comments, scored with a six-criterion rubric. 100 minutes with scoring.
keywords: assessments and practical exercises, theme design, practical task, rubric, theme extension, pagelayout.tpl, DesignExtensions, SiteDesign, stylesheet, no inline CSS, override, template path comments, ezinfo.php, extension.xml
type: help
---
# Lesson 4.4: Theme designers, practical task

The learner builds a small theme extension from an empty folder and switches it on for the public siteaccess only.
The result must keep the administration untouched, carry no inline styles or scripts, and be traceable with template
path comments.

> **Note**
> This is the trainer's own assessment, not an official certification. Each learner works on their own training
> installation; settings are changed for one siteaccess, never globally.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 4: Assessing the technical courses](README.md) |
| Duration | 100 minutes (5 briefing, 75 task, 20 scoring) |
| Level | Intermediate |
| Audience | Learners at the end of the Theme design course |
| Prerequisites | [Lesson 4.3](theme-designers-written-test.md); a shell and an editor on the learner's own training installation |

## Learning objectives

Learners who pass this task have shown that they can:

- create a theme extension with the required files;
- register its design and choose it for one siteaccess;
- load a stylesheet from the design without inline CSS;
- override one template for one content type;
- prove which file answered with template path comments, and switch them off again.

## Trainer's notes

### How to run it

1. **Before**: one training installation per learner, served on a local port, with a site package that has articles.
   Learners know the name of the public siteaccess.
2. **Briefing**: hand out the steps and the rubric; 75 minutes. The theme is called `<account>theme`, its design
   `<account>`.
3. **Scoring**: open the public site and the administration, view the page source, read the files, and run the grep
   from step 9 yourself.

### Common questions

**May learners start from the design wizard?** If your course taught it, yes; the files must still be complete and
the rubric applies to the result
([Design extension wizard](../../../designers/extensions/design-extension-wizard.md)).

**Which stylesheet setting does the pagelayout read?** That depends on the pagelayout the learner writes. Accept any
documented way to load a file from the design's `stylesheets/` folder without a `style` block.

### Pitfalls

- **`SiteDesign` set globally.** The administration turns into the theme. Scored under **Scope**; recover with
  [Resetting the lab](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md).
- **Path comments left on.** Scored; they must be off at the end.

## Hands-on exercise

The task, as handed to learners. Work in the installation root of your own training installation.

1. Create `extension/<account>theme/` with `ezinfo.php`, `extension.xml` (version `0.1.0` in both), `LICENSE` and a
   short `README.md` that documents the `SiteDesign` line an installer must set.
2. Create `design/<account>/templates/pagelayout.tpl` in the extension: a simple page with a header showing the site
   name, the module result, and a footer with your theme's name.
3. Create `design/<account>/stylesheets/theme.css` with at least a header and footer style, and load it from the
   pagelayout through the design (no `<style>` block, no `style=""`).
4. Add `settings/design.ini.append.php` with `DesignExtensions[]=<account>theme`.
5. Activate the extension, then set `SiteDesign` for the public siteaccess only:

   ```bash
   ./console exp:ini set site.ini/DesignSettings/SiteDesign <account> siteaccess:<public siteaccess> --dry-run --allow-root-user
   ```

   Read the output, then run it without `--dry-run`. Clear the INI and template caches.
6. Override `node/view/full.tpl` for the class `article` in your design (rule in the extension's
   `settings/override.ini.append.php`), showing the title and the intro with a class from your stylesheet. Clear the
   override and template caches.
7. Check your templates with `php bin/php/eztemplatecheck.php -s<public siteaccess> extension/<account>theme/design/
   --allow-root-user`.
8. Switch on template path comments for the public siteaccess, open an article, and copy the `START` line that shows
   your override answering `node/view/full.tpl`. Switch the comments off again and clear the template cache.
9. Run this from the installation root and keep the output (it should print nothing):

   ```bash
   grep -rn -E "<style|style=\"|<script[^>]*>[^<]|on(click|change|load|submit)=" extension/<account>theme/design
   ```

10. Open the administration and check that it still uses its own design.

## Expected result

- The public site shows the learner's header and footer, styled from `theme.css`; articles use the override.
- The administration looks as before.
- The template check reports the templates valid; the grep prints nothing.
- The learner's notes hold the `START ... (node/view/full.tpl)` line; the comments are off at the end.
- **Setup > Extensions** lists the theme with version 0.1.0.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Theme extension files | Missing | Design only, no metadata | Metadata present, LICENSE or README missing | All files, versions agree, README documents `SiteDesign` | The files, **Setup > Extensions** |
| Registration and scope (must) | Theme not found | Found, but `SiteDesign` set globally | Public only, caches not cleared | Registered, public siteaccess only, admin unchanged | Both sites, the settings files |
| Pagelayout and stylesheet | Default look | Pagelayout, no stylesheet | Stylesheet loaded via inline block | Stylesheet loaded from the design, page styled | Page source |
| Override | None | Rule or file missing | Applies to all classes | Articles only, title and intro shown | An article and a folder |
| No inline code | Inline styles and scripts | Several found | One found | The grep prints nothing | The grep output |
| Tracing and clean-up | Not done | Comments on, not used | Line copied, comments left on | Line copied, comments off, template check clean | Notes, page source |

**Pass**: 12 of 18 points or more and **Registration and scope** at 2 or 3.

## Quiz

Debrief questions to ask after scoring.

1. Why does the README document `SiteDesign` instead of the theme setting it?
2. What would `DesignExtensions[]=<account>` (the design name) have done?
3. What does the line `START ... (node/view/full.tpl)` tell you?
4. Why does it matter that the grep printed nothing?

<details>
<summary>Answers</summary>

1. A global `SiteDesign` would change the administration and every other site
   ([The design settings a theme ships](../../../designers/extensions/design-settings-a-theme-ships.md)).
2. Nothing found: the value is the extension name
   ([Registering the design](../../../designers/extensions/registering-the-design.md)).
3. Your file answered the request for `node/view/full.tpl`
   ([Templates and design](../../../../guides/templates-and-design.md#3-see-which-template-wrote-which-markup)).
4. A site can switch on a Content Security Policy without the theme breaking
   ([Removing inline scripts and styles](../../../designers/lifecycle/removing-inline-scripts-and-styles.md)).

</details>

## Further reading

- [The folder structure of a theme extension](../../../designers/extensions/extension-folder-structure.md)
- [The design settings a theme ships](../../../designers/extensions/design-settings-a-theme-ships.md)
- [Checklist: theme extension ready](../../../designers/extensions/checklist-theme-extension-ready.md)

## Related pages

- [Lesson 4.3: Theme designers, written test](theme-designers-written-test.md)
- [Module 4: Assessing the technical courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
