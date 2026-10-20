---
title: "Lesson 8.3: Changelog, release notes and the release checklist"
audience: trainer, designer, developer
category: training
order: 83
summary: Learners turn their commit history into CHANGELOG.md entries, write release notes with a headline and an "after updating" section, and walk the distribution checklist for their theme as the course's closing exercise. 60 minutes.
keywords: theme design, CHANGELOG.md, changelog entries, Added, Updated, Removed, Renamed, Fixed, release notes, after updating, distribution checklist, theme extension ready checklist, git log, git describe, course wrap-up
type: help
---
# Lesson 8.3: Changelog, release notes and the release checklist

The last lesson of the course brings everything together. Learners describe their theme's changes in the form users
read them, a changelog and release notes, and then check their theme against the published checklists. What remains
open on the checklist is the honest list of what a learner would still do before publishing a real theme.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 8: Licensing and releasing](README.md) |
| Duration | 60 minutes (15 presenting, 45 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who publish themes |
| Prerequisites | [Lesson 8.2](versions-and-permanent-tags.md): the tag `v0.1.1` in the local repository |

## Learning objectives

After this lesson, learners can:

- collect the commits since the last tag and rewrite them as changelog entries from the user's view;
- keep `CHANGELOG.md` with the newest release at the top;
- write release notes with a headline, the changes, what to do after updating, and compatibility;
- check a theme against the "theme extension ready" and "before you publish" checklists.

## Trainer's notes

### How to present it

Sources: [Keeping a CHANGELOG.md](../../../designers/distribution/keeping-a-changelog-file.md),
[Writing release notes](../../../designers/distribution/writing-release-notes.md),
[Checklist: the theme extension is ready](../../../designers/extensions/checklist-theme-extension-ready.md) and
[Checklist: before you publish](../../../designers/distribution/distribution-checklist.md).

1. **Changelog** (4 minutes). The file travels with the theme into every installation; a GitHub release does not.
   Keep both. Collect with `git log --oneline "$(git describe --tags --abbrev=0)"..HEAD`, rewrite with prefixes, merge
   what belongs together, drop internal commits, newest release first, never rewrite published entries except to
   correct a fact, and then say so.
2. **Release notes** (4 minutes). A headline with the benefit; the changes with prefixes; what to do after updating
   (caches to clear, settings to add), or "Nothing to do after updating."; compatibility; credits with consent; a
   correction when the release repairs an earlier one. Remove anything private: internal paths, host names, customer
   names, credentials.
3. **The checklists** (4 minutes). Show the groups of the publishing checklist: repository, licenses, version and tag,
   release and Composer, being found and supported.
4. **Close the course** (3 minutes). Recap the chain from module 1 to this lesson: design chain, own extension,
   packer, administration rules, accessibility, icons, packaging, licensing, releasing.

### Common questions

**Do I need a changelog for a theme only my company uses?** Yes; the next developer on the project is its reader.

**What goes into "after updating" for a theme?** Typically the caches: the template-override cache for new templates,
the packer and template-block caches for changed stylesheets, the INI cache for new settings.

**Where do I publish?** That is a decision for a real theme: see
[Choosing where to publish](../../../designers/distribution/choosing-where-to-publish.md).

### Pitfalls

- **Copying commit messages one to one.** Users need the effect, not the steps.
- **Leaving the "after updating" section out.** A theme with a new template needs a cache clear; say so.
- **Ticking checklist items that do not apply as done.** Mark them "not applicable" with a reason.

## Hands-on exercise

In `extension/training_theme`, the local repository from lessons 7.4 and 8.2.

1. Make one more change for the next release: in `training.css`, give the footer more space at the top. Commit it with
   a prefixed message.
2. Collect the commits since the last tag:

   ```bash
   git log --oneline "$(git describe --tags --abbrev=0)"..HEAD
   ```

3. Create `CHANGELOG.md` with a heading `# Changelog`, a section `## 0.1.2` with the entry for step 1, and a section
   `## 0.1.1` and `## 0.1.0` with one or two entries each from your memory of the course (the breadcrumb, the dashboard
   block, the icons).
4. Write release notes for `0.1.2` in a file `var/tmp/training_theme-0.1.2-notes.md` of the installation (not in the
   theme): headline, changes, "after updating" (which caches?), compatibility (the Exponential version of your lab,
   from the About page).
5. Read your notes as a user who skipped two releases. Remove anything private.
6. Open [Checklist: the theme extension is ready](../../../designers/extensions/checklist-theme-extension-ready.md) and
   go through it for `training_theme`. Mark each item done, open or not applicable.
7. Do the same with [Checklist: before you publish](../../../designers/distribution/distribution-checklist.md), up to
   the "Version and tag" group.
8. Write the three most important open items on a card. Share them with the group.

## Expected result

- `CHANGELOG.md` has three releases, newest at the top, with prefixed entries written for users.
- The release notes have a headline, the changes, an "after updating" section naming the packer and template-block
  caches for the stylesheet change, and the tested version.
- Each learner has a marked checklist and three open items, such as a screenshot, a `SECURITY.md` or a real
  repository.
- The trainer collects the open items on the board; they are the learners' next steps after the course.

## Quiz

1. Why keep a `CHANGELOG.md` when the repository has release notes?
2. Which command lists the commits since the newest tag?
3. Which section of the release notes tells users which caches to clear?
   A. The headline B. Credits C. What to do after updating D. Compatibility
4. May you rewrite a changelog entry of a published release?
5. Name two things to remove from release notes before publishing.

<details>
<summary>Answers</summary>

1. The file travels with the theme into every installation; a release on a hosting site does not
   ([Keeping a CHANGELOG.md](../../../designers/distribution/keeping-a-changelog-file.md)).
2. `git log --oneline "$(git describe --tags --abbrev=0)"..HEAD`.
3. **C.**
4. Only to correct a factual mistake, and then say so.
5. Any two: internal paths, host names, customer names, credentials
   ([Writing release notes](../../../designers/distribution/writing-release-notes.md)).

</details>

## Further reading

- [Changelog entries](../../../designers/distribution/changelog-prefixes.md): the prefixes.
- [INSTALL.md and CHANGELOG.md](../../../designers/extensions/install-and-changelog-files.md).
- [Distributing a theme: start here](../../../designers/distribution/distribution-start-here.md): the whole publishing
  path.
- [The life cycle of a theme](../../../designers/lifecycle/the-life-cycle-of-a-theme.md): after the first release.

## Related pages

- [Module 8: Licensing and releasing](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
