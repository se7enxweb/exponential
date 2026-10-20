---
title: Testing the development line
audience: advocate
category: advocacy
order: 105
summary: How advocates test the current development line of Exponential (the main branch, dev-main) on a separate test installation and report what they find, so releases are better - never on production, with a test routine and report template.
keywords: testing, development line, main branch, dev-main, pre-release, Git clone, test installation, feedback, regressions, release quality
type: help
---
# Testing the development line

This page is for advocates who want to help the next release be good. Security and other fixes are made on the `main`
branch, "the 6.0.15 line, `dev-main`", and published with the next 6.0.x release
([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). Trying `main` on a test installation
finds problems before they reach a release.

> **Warning**
> Only on a separate test installation with invented data. Never on a production site, and never point an installer
> at a database you want to keep ([Upgrading](../../install/11-upgrading.md)).

## Contents

1. [Get the development line](#get-the-development-line)
2. [What to test](#what-to-test)
3. [Report what you find](#report-what-you-find)

## Get the development line

The simplest way is a Git clone, which also lets you update and contribute
([Develop with Git](../develop-with-git.md)):

```bash
git clone https://github.com/se7enxweb/exponential.git exponential-main
cd exponential-main
git log -1 --format='%h %ad %s'      # note the commit you test
```

Then install it on SQLite as [Install in one command](../../features/6.0/install-in-one-command.md) describes. To keep
it current, see [Develop with Git, Keep up to date](../develop-with-git.md#keep-up-to-date); to try one fix before it
is released, [Use a fix before it is released](../develop-with-git.md#use-a-fix-before-it-is-released).

## What to test

| Area | How |
|---|---|
| Your everyday tasks | Edit, publish, upload, translate, layouts |
| New features in the changelog | Follow their feature pages ([6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md)) |
| Your language | The administration in your language ([Reviewing a machine-assisted translation](reviewing-a-machine-assisted-translation.md)) |
| Your setup | Your database, engine, operating system |
| Accessibility | Your assistive technology ([Advocating for accessibility](advocating-for-accessibility.md)) |

## Report what you find

Use [Reporting bugs well](reporting-bugs-well.md) and name the commit:

```text
Environment: Exponential main at <commit>, PHP <version>, <engine>, <database>, <design>
Worked in <last release>: <yes/no/unknown>
```

"Worked in the last release, broken on main" is the most valuable kind of report: it points to a recent change.

Do not announce untested features as released ([Writing about a release](writing-about-a-release.md)).

## Related pages

- [Reproducing and confirming issues](reproducing-and-confirming-issues.md)
- [Develop with Git](../develop-with-git.md)
- [Release and upgrade planning](../leadership/release-and-upgrade-planning.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
