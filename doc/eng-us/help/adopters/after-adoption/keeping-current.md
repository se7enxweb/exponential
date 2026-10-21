---
title: Keeping Exponential current after adoption
audience: adopter, customer, admin, hosting
category: adoption
order: 62
summary: How adopters keep their Exponential installation current - why updates matter, the 6.0 line and immutable releases, a routine for checking and applying updates, extensions, PHP versions, and who does what.
keywords: updates, upgrades, keeping current, 6.0.x, security releases, routine, extensions, PHP version, Git, Composer, update day
type: help
---
# Keeping Exponential current after adoption

## Contents

1. [Why it matters](#why-it-matters)
2. [How releases reach you](#how-releases-reach-you)
3. [A routine](#a-routine)
4. [Extensions and PHP](#extensions-and-php)

## Why it matters

Security fixes are made on `main` and "published with the next 6.0.x release"; "a fix for a problem in `v6.0.14` or an
earlier tag reaches you as the next release, not as a corrected copy of the old one" (`SECURITY.md`). A site that
does not update does not get fixes.

## How releases reach you

- Through Composer or Git, depending on how the site was installed ([Update with Git](../../update-with-git.md)).
- **Setup > Updates and packages** in the administration finds and installs Composer packages
  ([Roadmap](../../project/roadmap.md)).
- What changed: the changelogs ([Staying informed](../../community/joining/staying-informed.md)).

## A routine

| When | Do |
|---|---|
| Monthly | Check for new releases and read their notes |
| Within days of a security release | Apply it, first on staging |
| Each update | Follow the [upgrade day checklist](../../customers/healthy/upgrade-day-checklist.md) |
| Yearly | Check the PHP version and the extensions |

How updates work for owners: [How updates work](../../customers/healthy/how-updates-work.md). With a provider:
[Upgrades with your provider](../../customers/healthy/upgrades-with-your-provider.md).

## Extensions and PHP

- Each extension has its own releases ([How packages are versioned](../../ecosystem/packages/how-packages-are-versioned.md)).
- Keep PHP on a supported version: Exponential runs on 8.0 to 8.5 (`README.md`).
- Custom code kept in extensions makes updates easier ([Extending without forking](../../extension-development/part-5-extend-fork-contribute/extending-without-forking.md)).

## Related pages

- [After adoption](README.md)
- [Release line and support](../../customers/healthy/release-line-and-support.md)
- [Upgrades without surprises](../../businesses/reliability/upgrades-without-surprises.md)
- [Upgrading](../../../install/11-upgrading.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
