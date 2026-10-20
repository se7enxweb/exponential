---
title: Commit messages the project can build a changelog from
audience: developer, designer, app-creator
category: extension-development
order: 110
summary: The commit message format of Exponential - Added, Updated, Fixed, Removed, Renamed - why it exists, how the kernel's commit-msg hook checks it, and how to write a subject and body that explain what changed and why.
keywords: commit message, Added, Updated, Fixed, Removed, Renamed, changelog, commit-msg hook, make hooks, subject, body, git
type: help
---
# Commit messages the project can build a changelog from

Part 5, chapter 11. A commit message is written once and read many times: in reviews, in `git log`, in `git blame`
years later, and in the changelog of the next release. Exponential uses a simple format that makes all of those
work. Use it in the kernel (it is checked there) and in your own extensions.

## The format

The subject starts with one of five words and a colon:

| Prefix | For |
|---|---|
| `Added: ` | something new: a feature, a file, a class, a setting, a page |
| `Updated: ` | changed or improved behavior, templates, texts, settings |
| `Fixed: ` | a bug fixed |
| `Removed: ` | something deleted: a feature, a file, a dependency |
| `Renamed: ` | a file, class, setting or view renamed |

The rest of the subject is a sentence that says what is true **after** the commit:

```text
Fixed: The redirect keeps the query string on the own host
Updated: The context menu entries of expchangeclass carry no inline handlers
Added: German translation of the sign-in and setup texts
```

That example of a `Fixed:` subject is the one `CONTRIBUTING.md` gives.

## Why this format

- **The changelog writes itself.** At release time, the commits since the last tag sort into the four or five groups
  of a changelog by their first word ([Changelogs](../part-7-releasing/changelogs.md)).
- **Reviewers know what to expect.** A `Fixed:` commit should not add features; a `Renamed:` commit should not change
  behavior.
- **It keeps commits small.** If a subject needs "and" between two prefixes, it is two commits.

## The kernel checks it

The kernel repository ships Git hooks in `.githooks/`. Run once in your clone:

```bash
make hooks
```

From then on, `git commit` refuses a subject without one of the five prefixes:

```text
commit-msg: the subject must start with 'Added: ', 'Updated: ', 'Fixed: ', 'Removed: ' or 'Renamed: ', e.g.
    Fixed: The redirect keeps the query string on the own host
```

Merge commits and `Revert`, `fixup!` and `squash!` subjects made by Git are allowed. CI checks the commits of a push
or pull request with `.githooks/check-commit-range` ([Quality checks](../../../features/6.0/quality-checks.md));
according to that page a contributor's commit only warns there.

You can copy the `commit-msg` hook into your extension's repository: it is a short shell script.

## The body

After an empty line, the body explains what the subject cannot: why the change was needed, what was wrong before,
and how it was checked. Real examples from the extensions this book cites:

```text
Updated: Version 1.0.6: installs and runs on PHP 8.0, the oldest PHP Exponential 6 supports

PHP 8.0 is the stock PHP of Red Hat Enterprise Linux 9 and the oldest version Exponential 6 supports. The code of
this extension was checked with PHPCompatibility for PHP 8.0, linted and loaded with PHP 8.0.30, and served by
PHP 8.0 on a full installation.
```

(se7enxweb/expchangeclass, commit 32196f0.) The body names the reason, the checks and the result. Someone reading it
in five years knows whether to trust the change.

## Good and bad subjects

| Bad | Better |
|---|---|
| `fix` | `Fixed: The batch script stops when no object of a batch converts` |
| `Updated stuff` | `Updated: The result boxes use the shared admin notice instead of style attributes` |
| `Fixed: bug #12` | `Fixed: The parameter file shown after a conversion is escaped` (and "Closes #12" in the body) |
| `Added: tests and fixed the menu and new translations` | three commits |
| `WIP` | do not push it; or squash it before the pull request |

## What does not belong in a commit message

- Secrets, passwords, personal data: a commit is public the moment it is pushed and cannot be taken back.
- Private paths of your machine or names of internal servers.
- Credit lines for tools you used to write the change. The author of the commit is the person responsible for it.

## Related pages

- Previous: [Writing good issues](writing-good-issues.md)
- Next: [Reviewing other people's work](reviewing-other-peoples-work.md)
- [Changelogs](../part-7-releasing/changelogs.md)
- [Quality checks](../../../features/6.0/quality-checks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
