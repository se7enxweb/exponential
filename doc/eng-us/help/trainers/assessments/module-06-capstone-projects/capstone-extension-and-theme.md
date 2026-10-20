---
title: "Lesson 6.4: Capstone, an extension and a theme"
audience: trainer, developer, designer
category: training
order: 64
summary: A two-day capstone for developers and designers, build a theme extension and a functional extension for an invented organization, with a protected module view, templates and overrides, no inline code, consistent metadata and a local Git history ready for a first release, scored with a seven-criterion rubric. Two days plus presentations.
keywords: assessments and practical exercises, capstone, extension, theme extension, developers, theme design, module view, policy, override, CSP, metadata, Git, release readiness, rubric
type: help
---
# Lesson 6.4: Capstone, an extension and a theme

A developer and a designer (or one learner doing both) deliver a small product for an invented organization: a theme
for its public site, and an extension with one feature. The result must be ready for a first release, without the
release itself.

> **Note**
> This is the trainer's own assessment, not an official certification. Work stays on the training installations and
> in local Git repositories: nothing is pushed, tagged or published, because a pushed tag is permanent.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 6: Capstone projects](README.md) |
| Duration | Two days: 15 minutes briefing, about 10 hours work with three checkpoints, 15 minutes presentation per pair, 1 hour scoring |
| Level | Advanced |
| Audience | Learners at the end of Developers and Theme design |
| Prerequisites | Those courses; a training installation from a Git clone, a shell, an editor and Git per pair |

## Learning objectives

Pairs that pass this capstone have shown that they can:

- build a theme extension used by one siteaccess only, without inline code;
- build a functional extension with a protected view, templates and settings of its own;
- make the two extensions work together through loading order and overrides;
- keep metadata consistent and the history clean and readable;
- explain what a first release would take, without publishing it.

## Trainer's notes

### How to run it

1. **Before**: one training installation per pair, from a Git clone, served on a local port, with articles in the
   content tree. Agree with each pair who leads the theme and who leads the extension; both answer questions on both.
2. **Checkpoints**: day 1 midday (both extensions active, empty), end of day 1 (the feature's page works; the theme
   styles the site), day 2 midday (overrides and access control done).
3. **Presentation**: the pair shows the site, the feature as an anonymous visitor and as a permitted user, the grep for
   inline code, `php -l` and template checks, and `git log`.

### Pitfalls

- **Scope creep.** The feature is one page. Polish counts more than features.
- **Global `SiteDesign`.** The administration turns into the theme. Scored.

## Hands-on exercise

The brief, as handed to pairs.

*The Greenfield Farmers' Market (an invented market) wants a fresh look for its site and a page that lists this
week's stalls.*

1. Theme extension `<pair>theme`: its own pagelayout, stylesheet loaded from the design, registered with
   `DesignExtensions[]`, used by the public siteaccess only; `ezinfo.php`, `extension.xml`, `LICENSE`, `README.md` that
   documents the `SiteDesign` line.
2. Feature extension `<pair>market`: a module `market` with a view `stalls` that lists the children of a folder named
   **Stalls** (created by you, with five folder or article items) through a template, with every printed value washed.
3. Access: the view requires a function `read`; a role grants it to Anonymous Users only after you decide the page is
   public. Prove both states.
4. Settings: the folder's node is read from a settings file of your own (`<pair>market.ini`), not written in the code.
5. Overrides: the theme overrides `node/view/full.tpl` for articles; the feature's template uses the theme's
   stylesheet classes. The feature extension declares in its `extension.xml` that it uses the theme where that is
   needed, or explain why it does not.
6. Quality: `php -l` on every PHP file, the template check on both designs, and the inline-code grep returning
   nothing.
7. Git: one repository per extension, commits with the prefixes `Added:`, `Updated:`, `Removed:`, `Renamed:`, version
   `0.1.0` in `ezinfo.php` and `extension.xml` of each, no tag.
8. A one-page release plan: how you would choose the number from what is published, what you would check before the
   tag, and what you would do if a published version turned out wrong.

## Expected result

- The public site uses the theme; the administration does not. The stalls page works and is protected as designed.
- The checks are clean; each repository has a readable history and consistent version fields; no tags exist.
- The release plan matches the release section of the extensions guide.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Theme | Not used | Used everywhere (global) | Public only, gaps in files | Public only, complete files, README documents `SiteDesign` | Both sites, files |
| Feature page | None | Static page | Lists items, values unwashed | Lists the folder's children, washed | The page |
| Access control (must) | Unprotected | Declared, not required | Required, one state not shown | Both states proven | Anonymous and permitted views |
| Settings | Hard-coded | Settings file, unused | Used, not documented | Read from own INI, documented | Code, INI |
| Overrides and cooperation | None | Override without rule | Works, dependency not considered | Works, dependency declared or reasoned | Pages, `extension.xml` |
| Quality checks | None | Some files checked | All checked, one finding left | All clean, grep empty | Terminal output |
| Git and release readiness | None | One repo, no prefixes | Both, versions inconsistent | Both, prefixes, consistent versions, no tag, sound release plan | `git log`, files, plan |

**Pass**: 14 of 21 points or more and **Access control** at 2 or 3.

## Quiz

Debrief questions after the presentations.

1. Why is the stalls folder's node in a settings file?
2. What did the inline-code grep protect the site from?
3. In your release plan, what happens if `0.1.0` turns out to be wrong after it is published?
4. Which extension loads first, and why does it matter here?

<details>
<summary>Answers</summary>

1. So an installer can point it at their own folder without changing code
   ([Extensions](../../../../guides/extensions.md#64-settings-content-and-cronjobs-of-your-own)).
2. Breakage when a site switches on a Content Security Policy
   ([Removing inline scripts and styles](../../../designers/lifecycle/removing-inline-scripts-and-styles.md)).
3. Leave it; publish `0.1.1` with the fix and say in its notes what was wrong
   ([Extensions](../../../../guides/extensions.md#7-release-it)).
4. The one earlier in the list (or ordered by `extension.xml` dependencies); it wins for settings and templates
   ([Loading order and which theme wins](../../../designers/extensions/loading-order-for-themes.md)).

</details>

## Further reading

- [Developers, practical task](../module-04-technical-courses/developers-practical-task.md) and
  [Theme designers, practical task](../module-04-technical-courses/theme-designers-practical-task.md)
- [Checklist: theme extension ready](../../../designers/extensions/checklist-theme-extension-ready.md)
- [Extension development](../../../extension-development/README.md)

## Related pages

- [Module 6: Capstone projects](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
