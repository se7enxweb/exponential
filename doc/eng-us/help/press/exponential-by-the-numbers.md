---
title: Exponential by the numbers
audience: press, advocate
category: press-media
order: 15
summary: The figures about Exponential that the documentation has actually counted - commits, releases, packages, languages, repositories, the history ledgers and the documented Velocity measurements - each with its date, how it was counted and how to recount it, plus the numbers that do not exist.
keywords: numbers, figures, statistics, data, counts, commits, releases, packages, Packagist, languages, locales, translations, repositories, Velocity benchmark, requests per second
type: help
---
# Exponential by the numbers

This page is for reporters and data journalists. It lists only figures that the documentation has counted, with the
date and the method, so you can print them responsibly or count them again. Numbers change with every release; always
print the date with the number.

## Contents

1. [The counted figures](#the-counted-figures)
2. [The Velocity measurements](#the-velocity-measurements)
3. [Numbers that do not exist](#numbers-that-do-not-exist)
4. [Recounting](#recounting)
5. [Printing a number](#printing-a-number)

## The counted figures

Counted again on 19 October 2026 with the command in the third column; the first count, of 18 October 2026, is in
[Who is 7x?, 7x in numbers](../who-is-7x.md#7x-in-numbers).

| Figure | Value | How it was counted |
|---|---|---|
| Years in the copyright notice | 1998 to 2026 (7x and the Foundation); 1999 to 2014 (eZ Systems AS) | COPYRIGHT.md |
| Oldest commit in the repository | 2 September 2002 | `git log --reverse` |
| Commits on `main` | 27,727 | `git rev-list --count origin/main` |
| Merged pull requests in the history | 1,042 | `git log --grep="Merge pull request"` |
| 6.0.x releases | 15 (6.0.0 on 1 January 2024 to 6.0.14 on 7 June 2026); 6.0.15 in development, no tag on 19 October 2026 | `git tag -l 'v6.0.*'` |
| se7enxweb packages required by the kernel | 66 on 18 October 2026, 69 on 25 October 2026 (the count grows as extensions are added) | `composer.json` |
| Packages of the vendor se7enxweb on Packagist | 219 | <https://packagist.org/packages/list.json?vendor=se7enxweb> |
| Public repositories of the GitHub account se7enxweb | 1,025, of which 95 are not forks | `gh repo list se7enxweb --limit 1100` |
| Changes in the history since December 2023 | 6,556 in 53 repositories, 1,037 by the se7enxweb team (18 October 2026) | [Ecosystem](../../history/ecosystem.md) |
| Commit authors before 2016 | 194 different names | `git log --until=2016-01-01 --format=%an` |
| Locale files (content languages and countries) | 268, of which 40 are right to left | [Languages](../../languages.md) |
| Administration translations | 70, all of them complete (26 October 2026) | [Languages](../../languages.md) |
| Supported databases | 6 | README, "Requirements" |
| Supported PHP versions | 8.0 to 8.5 | README; [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |

## The Velocity measurements

The README documents these figures for Exponential Velocity, with the method written down and scripts that ship
with the project ([Story: how the Velocity figures are measured](story-how-velocity-figures-are-measured.md)):

| Figure | What it means, as the README explains it |
|---|---|
| 0.36 ms per cached page | The server's own work for a cached page, a median over hundreds of requests |
| 8,941 requests per second sustained | Measured on a 12-core machine serving a real page, zero errors up to 384 connections in flight |
| Repeat visits never touch the network | With the optional navigation cache (a service worker), a return visit is answered by the browser |

Print them as "documented by the project" and keep the conditions. They are not a comparison with any other product.

## Numbers that do not exist

The sources do not count these. Do not estimate them, and treat any figure you find for them elsewhere as unverified
until its author tells you how it was counted:

- installations, websites or users of Exponential;
- customers of 7x, its revenue or staff;
- the Foundation's budget or number of donors;
- developers who know Exponential;
- market share or rankings;
- downloads (Packagist shows install statistics per package; they count Composer installs, not sites).

The press kit lists two counts ("Active Properties" and "Forge Projects") without a method; see
[The press kit](the-press-kit.md#facts-in-the-kit-that-are-out-of-date).

## Recounting

On a clone of the repository:

```bash
git clone https://github.com/se7enxweb/exponential.git && cd exponential
git rev-list --count HEAD
git tag -l 'v6.0.*' --sort=version:refname | wc -l
git log --grep="Merge pull request" --oneline | wc -l
```

Your count will differ from the table if the repository has moved on. That is expected; print your own count with
your date ([Verifying numbers](verifying-numbers.md)).

## Printing a number

```text
Good:  "The repository holds more than 27,000 commits, the oldest from September 2002
        (count of 19 October 2026)."
Good:  "On 25 October 2026 the kernel's composer.json required 69 packages published by 7x."
Avoid: "Thousands of companies use Exponential."      (no source)
Avoid: "Exponential is 10 times faster than ..."       (no comparison published)
```

## Related pages

- [Fact sheet for journalists](fact-sheet-for-journalists.md)
- [What the sources do not say](what-the-sources-do-not-say.md)
- [Verifying numbers](verifying-numbers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
