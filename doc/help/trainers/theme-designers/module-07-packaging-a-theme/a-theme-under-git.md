---
title: "Lesson 7.4: A theme under Git, with a composer.json"
audience: trainer, designer, developer
category: training
order: 74
summary: Learners put their theme in a repository of its own with a .gitignore, make a first commit with a changelog prefix, and add a composer.json that installs the theme into the right extension folder. 45 minutes.
keywords: theme design, git, git init, repository per theme, gitignore, commit message, Added, Updated, Removed, Renamed, Fixed, composer.json, ezpublish-legacy-extension, ezpublish-legacy-extension-name, composer validate, SPDX license
type: help
---
# Lesson 7.4: A theme under Git, with a composer.json

A theme with a future lives in a repository of its own, whose root is the extension folder. That gives it its own
history, tags and releases, and lets any site install it with Composer. In this lesson learners turn `training_theme`
into such a repository and give it the `composer.json` that makes `composer require` put it in the right place.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 7: Packaging a theme](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who publish themes |
| Prerequisites | [Lesson 7.1](theme-extension-metadata.md); Git installed and a Git name and e-mail set (any address at `example.invalid` for the course) |

## Learning objectives

After this lesson, learners can:

- explain why a theme has a repository of its own rather than living in a site's repository;
- start the repository with a `.gitignore` that keeps editor leftovers, build output and secrets out;
- write commit messages with the prefixes `Added:`, `Updated:`, `Removed:`, `Renamed:`, `Fixed:`;
- write a `composer.json` with the right type, license and extension folder name, and validate it.

## Trainer's notes

### How to present it

Sources: [Putting a theme under Git](../../../designers/extensions/putting-a-theme-under-git.md),
[A .gitignore for themes](../../../designers/extensions/gitignore-for-themes.md) and
[composer.json for a theme](../../../designers/extensions/composer-json-for-a-theme.md).

1. **One repository per theme** (4 minutes). Its own history and releases; installable with Composer by any site;
   contributors see only the theme; it can be public while the site stays private.
2. **The .gitignore** (2 minutes). The wizard wrote one: editor leftovers, operating system files, `/vendor/`,
   `/node_modules/`, logs.
3. **Commit messages** (3 minutes). What changed for the user and why, with a prefix, so the changelog can be built
   from the history. Example: `Fixed: The map popup escapes the address and the title`.
4. **composer.json** (6 minutes). The fields that matter: `name` (`vendor/package`, lower case), `description`,
   `type: ezpublish-legacy-extension`, `license` as an SPDX identifier equal to `LICENSE`, `authors`, `homepage`,
   `support`, `require` (PHP and the installer plugin), and `extra.ezpublish-legacy-extension-name` (the folder name
   below `extension/`). `version` is usually left out: Packagist reads it from the tag.

### Common questions

**Do I need GitHub for this lesson?** No. The repository stays local; publishing is a decision for module 8 and for
real themes.

**Which vendor name?** Your organization's Packagist vendor. For the course, a made-up lower-case name is fine.

**Can I keep the theme inside the site's repository and still publish it later?** You can split it out later, but it
is easier to start separate.

### Pitfalls

- **Committing in the installation's repository.** `cd extension/training_theme` first; check with `git rev-parse
  --show-toplevel`.
- **A personal address in commits.** Use a course address at `example.invalid` in the lab.
- **A license that differs between `composer.json` and `LICENSE`.** Write the SPDX form of the same license (for GPL
  version 2 or later: `GPL-2.0-or-later`).

## Hands-on exercise

On your own training installation. Nothing is pushed anywhere.

1. `cd extension/training_theme` and check that it is not already inside a repository: `git rev-parse
   --show-toplevel` (an error, or the installation's root, means it is not a repository of its own).
2. Read the `.gitignore` the wizard wrote. Add a line `*.ezpkg` so exported packages are not committed by accident.
3. Start the repository: `git init -b main`.
4. Set a course identity for this repository only:
   `git config user.name "Course Learner"` and `git config user.email "learner@example.invalid"`.
5. Check what would be committed: `git status --short`. Every file should be one you mean to publish.
6. Commit: `git add . && git commit -m "Added: The training_theme theme extension"`.
7. Write `composer.json` at the theme's root:

   ```json
   {
       "name": "training/training_theme",
       "description": "Training theme built in the Theme design course",
       "type": "ezpublish-legacy-extension",
       "license": "GPL-2.0-or-later",
       "require": {
           "php": ">=8.0",
           "se7enxweb/ezpublish-legacy-installer": "*"
       },
       "extra": {
           "ezpublish-legacy-extension-name": "training_theme"
       }
   }
   ```

   Change `license` to the SPDX form of the license in your `LICENSE` file.
8. If Composer is available, run `composer validate` in the theme folder and read the result.
9. Commit it: `git add composer.json && git commit -m "Added: composer.json so the theme can be required by name"`.
10. Read the history: `git log --oneline`.

## Expected result

- Step 5 lists only the theme's own files (no `.bak`, no `.ezpkg`, no editor files).
- Step 8: `composer validate` reports the file valid; warnings about missing optional fields (such as `authors`) are
  read and noted.
- Step 10 shows two commits with prefixed messages.
- The trainer checks that the commits are in `extension/training_theme/.git`, not in the installation's repository.

## Quiz

1. Give two reasons for a repository per theme.
2. Which `composer.json` field makes the installer place the package below `extension/`?
   A. `name` B. `type: ezpublish-legacy-extension` C. `homepage` D. `version`
3. Which field sets the folder name below `extension/`?
4. Why is `version` usually left out of a theme's `composer.json`?
5. Write a commit message for a change that makes the breadcrumb wrap on phones.

<details>
<summary>Answers</summary>

1. Any two: its own history and releases, installable with Composer by any site, contributors see only the theme, it
   can be public while the site stays private
   ([Putting a theme under Git](../../../designers/extensions/putting-a-theme-under-git.md)).
2. **B.**
3. `extra.ezpublish-legacy-extension-name`.
4. Packagist reads the version from the Git tag; a field in the file would have to be kept in sync by hand.
5. For example `Updated: The breadcrumb wraps on narrow screens instead of overflowing`.

</details>

## Further reading

- [From the wizard's output to the first commit](../../../designers/extensions/from-wizard-output-to-first-commit.md).
- [The Composer installer and the folder name](../../../designers/extensions/composer-installer-and-extension-name.md).
- [Testing a Composer install before publishing](../../../designers/extensions/testing-a-composer-install-locally.md).
- [Develop with Git](../../../develop-with-git.md): working on extensions inside an installation.

## Related pages

- [Module 7: Packaging a theme](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
