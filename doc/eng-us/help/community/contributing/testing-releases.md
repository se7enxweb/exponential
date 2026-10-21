---
title: Testing releases as a community member
audience: community, admin, developer, hosting, supporter
category: community
order: 28
summary: How community members test the Exponential development line before a release - a safe test installation on SQLite, what to test for your own setup, how to describe a regression, and how hosts and agencies can test across many servers.
keywords: testing, release, development line, main, dev-main, regression, test installation, SQLite, Git clone, engines, databases, PHP versions
type: help
---
# Testing releases as a community member

Fixes and features are made on the `main` branch, "the 6.0.15 line, `dev-main`", and published with the next 6.0.x
release (`SECURITY.md`). Every community member who tries `main` on their own kind of setup before a release makes
that release better for everyone with the same setup.

> **Warning**
> Test only on a separate installation with invented data. Never on a production site, and never point an installer
> at a database you want to keep ([Upgrading](../../../install/11-upgrading.md)).

## Contents

1. [Set up a test installation](#set-up-a-test-installation)
2. [What to test](#what-to-test)
3. [Describing a regression](#describing-a-regression)
4. [Testing across many servers](#testing-across-many-servers)

## Set up a test installation

The simplest way is a Git clone of `main` installed on SQLite, which needs no database server:

1. Clone the repository and note the commit you test (`git log -1 --format='%h %ad %s'`).
2. Install it as [Install in one command](../../../features/6.0/install-in-one-command.md) describes.
3. Keep it current with Git ([Develop with Git](../../develop-with-git.md)).

The detailed walk-through for advocates is [Testing the development line](../../advocates/testing-the-development-line.md).

## What to test

| Area | How |
|---|---|
| Your everyday tasks | Edit, publish, upload, translate, build a page with layouts |
| What the changelog says is new | Follow the feature pages linked from the [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md) |
| Your setup | Your PHP version, database, engine (Velocity, Apache or nginx with PHP-FPM, FrankenPHP), operating system |
| Your extensions | The extensions your real sites use |
| Your language and accessibility needs | The administration in your language, with your assistive technology |
| An upgrade | A copy of a real site's structure with invented content, upgraded as [Upgrading](../../../install/11-upgrading.md) describes |

## Describing a regression

"Worked in the last release, broken on main" is the most useful finding there is, because it points to a recent
change. Name the commit:

```text
Environment: Exponential main at <commit>, PHP <version>, <engine>, <database>, <design>
Worked in <last release>: <yes / no / unknown>
Steps: ...
```

File it on the right tracker ([Filing a bug](filing-a-bug.md)).

## Testing across many servers

Hosting companies and agencies often run many PHP versions, databases and operating systems. A test on each of them
before a release is a contribution few others can make. Partners have a page of their own on this:
[Testing releases (partners)](../../partners/testing-releases.md). Requirements and supported combinations:
[Requirements](../../../install/02-requirements.md), [Database support](../../database-support.md),
[Operating system support](../../os-support.md).

## Related pages

- [Contributing as a community member](README.md)
- [Testing the development line](../../advocates/testing-the-development-line.md)
- [Staying informed](../joining/staying-informed.md)
- [How releases happen](../how-it-works/how-releases-happen.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
