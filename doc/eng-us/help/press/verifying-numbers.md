---
title: Verifying numbers
audience: press
category: press-media
order: 132
summary: How to check any number about Exponential before you print it - find the method behind it, recount it in the repository or on Packagist and GitHub, keep the date and the conditions, and recognize the numbers that have no source at all.
keywords: numbers, figures, statistics, verify, recount, commits, releases, packages, Packagist, GitHub, benchmark, requests per second, languages, data journalism
type: help
---
# Verifying numbers

This page is for reporters and data journalists. A number about Exponential is only printable when you know how it
was counted, when, and under which conditions. Most numbers in this documentation come with a method you can repeat.

## Contents

1. [Three questions for every number](#three-questions-for-every-number)
2. [Recounting the common figures](#recounting-the-common-figures)
3. [Performance figures](#performance-figures)
4. [Numbers without a method](#numbers-without-a-method)
5. [Writing the number](#writing-the-number)

## Three questions for every number

1. **How was it counted?** A command, a page, a measurement script. No method, no print.
2. **When?** Counts change with every commit and release. Keep the date.
3. **Under which conditions?** Especially for speed: which machine, which page, cached or not.

The counted figures, with methods and dates, are on [Exponential by the numbers](exponential-by-the-numbers.md).

## Recounting the common figures

| Figure | Recount with |
|---|---|
| Commits on main | `git rev-list --count HEAD` in a fresh clone of `se7enxweb/exponential` |
| 6.0.x releases | `git tag -l 'v6.0.*' --sort=version:refname` (count the lines) |
| Merged pull requests in the history | `git log --grep="Merge pull request" --oneline \| wc -l` |
| Required se7enxweb packages | Count the `se7enxweb/` lines in the `require` section of `composer.json` |
| se7enxweb packages on Packagist | <https://packagist.org/packages/list.json?vendor=se7enxweb> |
| Public repositories of se7enxweb | The repository count on <https://github.com/se7enxweb> |
| Locale files and translations | [Languages in Exponential](../../languages.md) |

If your count differs from the documented one, the repository has probably moved on. Print yours, with your date.

## Performance figures

The README documents figures for Exponential Velocity: 0.36 ms per cached page, 8,941 requests per second sustained
on a 12-core machine, zero errors up to 384 connections, with the method written down and scripts in the project
([Story: how the Velocity figures are measured](story-how-velocity-figures-are-measured.md)).

- Print them with their conditions: "on a 12-core machine, for a cached page".
- Do not turn them into a comparison with another product; none is published.
- If you measure yourself, publish your hardware, versions, page, method and dates.

## Numbers without a method

| Number | Status |
|---|---|
| Installations, sites, users | Not counted anywhere in the sources |
| Customers, revenue, staff | Not published |
| Market share, rank | Not published |
| "Active Properties", "Forge Projects" in the press kit | Listed without a method ([The press kit](the-press-kit.md#facts-in-the-kit-that-are-out-of-date)); ask before printing |
| Download counts | Packagist shows Composer install statistics per package; they are not counts of sites |

## Writing the number

```text
Good:  "more than 27,000 commits (count of 18 October 2026)"
Good:  "15 releases of the 6.0 line between January 2024 and June 2026"
Good:  "8,941 requests per second, measured by the project on a 12-core machine"
Avoid: "about 30,000 commits" when you counted 27,242 (round down, not up)
Avoid: "thousands of users"
```

## Related pages

- [Exponential by the numbers](exponential-by-the-numbers.md)
- [Fact-checking guide](fact-checking-guide.md)
- [What the sources do not say](what-the-sources-do-not-say.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
