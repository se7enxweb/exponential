---
title: "Story: current PHP"
audience: press
category: press-media
order: 72
summary: A feature story about keeping an old PHP code base on current PHP - PHP 8.0 to 8.5 supported, PHP 7.4 sites on 6.0.7, the CI matrix, the aims beyond PHP 9, and the forks of libraries patched along the way - with dates, sources and the claims to avoid.
keywords: story, PHP, PHP 8, PHP 8.4, PHP 8.5, PHP 7.4, compatibility, modernization, CI, Zeta Components, upgrades, technical debt
type: help
---
# Story: current PHP

This page is for technical journalists. Keeping a code base that started in the PHP 4 era on PHP 8.5 is a story about
steady, visible maintenance.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

Exponential 6 supports PHP 8.0 to 8.5; PHP 8.0 is kept so that the stock PHP of Red Hat Enterprise Linux 9 runs it,
and sites on PHP 7.4 can stay on 6.0.7. "Current releases are primarily aimed at easing the requirements to support
current versions of the PHP language like PHP 8.2, 8.3, 8.4, 8.5, 8.6 and beyond php9" (README). The continuous
integration runs the test suite on PHP 8.1 to 8.5 and checks that PHP 8.0 still boots on every push and pull request
([PHP versions](../who-is-the-exponential-foundation.md#php-versions)).

## What is new, and when

| What | When | Source |
|---|---|---|
| PHP 8 in the setup wizard | December 2023 | [December 2023](../../history/2023/2023-12.md) |
| PHP 8.4 in, PHP 7 out (6.0.7 the last for 7.4) | April 2025 | [April 2025](../../history/2025/2025-04.md) |
| PHP 8.5 supported | December 2025 | [December 2025](../../history/2025/2025-12.md) |

## Facts you can print

| Fact | Source |
|---|---|
| PHP 8.0 to 8.5 supported, for Velocity too | [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| `composer.json` allows up to PHP 8.8 | same |
| CI on PHP 8.1 to 8.5, PHP 8.0 boot check | [CI](../../bc/6.0/ci.md) |
| 7x maintains patched forks of Zeta Components and submitted security pull requests upstream | [The PHP stack it stands on](../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "Supports PHP 9" | The project aims at future versions; support is documented to 8.5 |
| "Rewritten for PHP 8" | The existing code was updated release by release |
| "Runs on PHP 7" (current releases) | Only up to 6.0.7 |

## Angles and headline ideas

- "From PHP 4-era code to PHP 8.5."
- "Old code, new PHP: the maintenance nobody sees."

## Related pages

- [Story: a code base kept alive](story-longevity.md)
- [Objection: "PHP and modern technology"](../advocates/objection-php-and-modern-technology.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
