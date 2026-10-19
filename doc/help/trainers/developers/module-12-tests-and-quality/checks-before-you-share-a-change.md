---
title: "Lesson 12.3: Checks before you share a change"
audience: trainer, developer
category: training
order: 123
summary: Learners run the checks a kernel change must pass before it is pushed (syntax, file manifest, commit messages, code style, PHPStan and the tests of the change), install the git hooks, and apply the same habits to their extension. 45 minutes.
keywords: developers course, quality checks, php -l, checkmanifest, filelist.md5, commit message prefixes, Added Updated Fixed Removed Renamed, git hooks, make hooks, make devtools, phpcs, checkcodestyle, phpstan, test-map, CI gates
type: help
---
# Lesson 12.3: Checks before you share a change

A change that breaks the build costs everyone time. The project's continuous integration runs a fixed list of checks
on every push and pull request; the same checks run on a developer's machine in minutes. In this lesson learners
run them, install the git hooks that run some of them automatically, and adopt the same routine for their own
extension.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 12: Tests and quality](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 12.2](writing-a-test.md); the kernel as a Git clone |

## Learning objectives

After this lesson, learners can:

- check the syntax of changed PHP files with `php -l` and templates with `eztemplatecheck.php`;
- check and fix the file manifest with `bin/php/checkmanifest.php`;
- write commit messages with the prefixes `Added:`, `Updated:`, `Fixed:`, `Removed:` and `Renamed:`;
- install the git hooks with `make hooks` and the development tools with `make devtools`;
- run the tests of a change and explain which checks block in CI.

## Trainer's notes

### How to present it

Sources: [Quality checks](../../../../features/6.0/quality-checks.md) and
[Testing and continuous integration](../../../../standards/testing-and-ci.md).

1. **What CI checks** (4 minutes). The Quality workflow: PHP syntax of changed files, JavaScript syntax, the file
   manifest, commit messages, PHPStan (blocking), code style (warning). The PHPUnit workflow: the suites on PHP 8.1
   to 8.5 and a database job.
2. **The manifest** (3 minutes). `share/filelist.md5` lists a checksum per file of the distribution; the upgrade
   check compares installations against it. A kernel change updates it in the same commit: `php
   bin/php/checkmanifest.php --staged` checks, `--fix` rewrites.
3. **Commit messages** (2 minutes). The subject starts with `Added: `, `Updated: `, `Fixed: `, `Removed: ` or
   `Renamed: ` and says what the change does.
4. **Hooks and tools** (3 minutes). `make hooks` sets `core.hooksPath` to `.githooks`: the pre-commit hook runs
   `php -l`, the manifest and code style checks on staged files; commit-msg checks the subject; pre-push runs PHPStan
   when the tools are installed. `make devtools` installs PHP_CodeSniffer and PHPStan into `.devtools/`.
5. **Tests of a change** (3 minutes). The commands in [Quality checks](../../../../features/6.0/quality-checks.md)
   select the test files that cover the changed files and run each in its own process.

### Common questions

**Does my extension need a manifest?** Only if it ships one, as some extensions do for the upgrade check. The
kernel's manifest covers the kernel's files.

**Why is code style only a warning?** Old code does not follow the style everywhere; only new violations in changed
files are reported.

**Do I have to install the hooks?** They are opt-in per clone, and recommended: they catch mistakes before a commit
exists.

### Pitfalls

- **Pushing without running the checks.** CI fails and the change waits. Run the "before you push" commands.
- **Fixing the manifest by hand.** Use `--fix`; hand edits are easy to get wrong.
- **A commit message about tools.** Describe what changed in the product and why, not which tool was used.

## Hands-on exercise

In the kernel clone of your development installation, on your branch `my-dev`.

1. Install the hooks: `make hooks`, and check: `git config core.hooksPath`.
2. Make a small change to a documentation page in `doc/` (fix a typo you found during the course) and stage it.
3. Check the manifest for the staged files: `php bin/php/checkmanifest.php --staged`.
4. Commit with a wrong subject first: `git commit -m "typo"`. Read the hook's refusal.
5. Commit with a correct subject: `git commit -m "Fixed: Typo in <page name>"`.
6. Check the syntax of the PHP files changed since `origin/main`:
   `git diff --name-only --diff-filter=AM origin/main..HEAD -- '*.php' | xargs -r -n1 php -l`.
7. Check the commit messages of your branch: `sh .githooks/check-commit-range origin/main HEAD`.
8. For your extension, write the same routine down as a short checklist: `php -l` of changed PHP files,
   `eztemplatecheck.php` of the design folder, the extension's tests, a commit subject with a prefix.

## Expected result

- Step 1 prints `.githooks`.
- Step 3 reports the documentation file as stale or missing in the manifest, or passes if documentation is not
  listed; with `--fix` the manifest is updated.
- Step 4 is refused with a message about the subject; step 5 is accepted.
- Step 7 passes for the commit of step 5.
- Step 8: each learner has a written checklist for `mysite_hello`.

## Quiz

1. Which five prefixes may a commit subject start with?
2. Which check is only a warning in CI?
   A. PHPStan B. Code style C. Commit messages D. PHP syntax
3. Which command rewrites stale checksums of the file manifest?
4. What does `make hooks` do?
5. Why run the tests of a change before the full run?

<details>
<summary>Answers</summary>

1. `Added:`, `Updated:`, `Fixed:`, `Removed:` and `Renamed:`.
2. **B.** Code style; only new violations in changed files are reported.
3. `php bin/php/checkmanifest.php --fix` (or `make manifest-fix`).
4. It sets `git config core.hooksPath .githooks`, so the project's hooks run on commit and push.
5. They finish in minutes and find most problems; the full run stays the check before a release or a large change.

</details>

## Further reading

- [Quality checks](../../../../features/6.0/quality-checks.md).
- [File consistency check](../../../../features/6.0/file-consistency-check.md): what the manifest is for.
- [Source control and releases](../../../../standards/source-control-and-releases.md).

## Related pages

- [Module 12: Tests and quality](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
