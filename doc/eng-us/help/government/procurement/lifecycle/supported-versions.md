---
title: Supported versions
audience: government, project-manager, admin, hosting
category: public-sector
order: 30
summary: Which Exponential versions receive security fixes according to SECURITY.md, what "supported" means for a public body, what happens to older lines, and how to require a supported version in a contract.
keywords: supported versions, security support, end of life, 6.0.x, 5.x, 4.x, SECURITY.md, maintained line, upgrade, contract, public sector
type: help
---
# Supported versions

This page is for project managers, contract managers and security officers in a public body. It answers: which
versions of Exponential get security fixes, what "supported" means in practice, and how to keep a site on a supported
version for the term of a contract. The source is [`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)
in the repository root.

## Contents

1. [The table](#the-table)
2. [What "supported" means](#what-supported-means)
3. [Older lines](#older-lines)
4. [Extensions](#extensions)
5. [In a contract](#in-a-contract)

## The table

From `SECURITY.md`:

| Version | Supported |
|---|---|
| 6.0.x, current line: `main` and the next tag cut from it | yes |
| 6.0.x released tags (`v6.0.0` to `v6.0.14`) | fixed by upgrading to the newest 6.0.x |
| 5.x, 4.x, 3.x and older | no |

"Exponential 6.0 is the maintained line. Security fixes are made on the `main` branch (the 6.0.15 line, `dev-main`)
and published with the next 6.0.x release."

## What "supported" means

- **A fix is a new release.** There are no back-ported patches for older 6.0.x tags. To receive a fix, the site moves
  to the newest 6.0.x. Plan for that: every site must be able to upgrade within the 6.0 line without a project.
- **Reports are answered.** A private report to the address in `SECURITY.md` gets "a first answer within one business
  day". `SECURITY.md` does not promise a time to fix.
- **No end-of-life dates are published.** `SECURITY.md` states which lines are supported today; it does not say for
  how long. Ask your supplier how they will keep you informed.

The project's wider support commitments (PHP versions, releases, documentation) are collected in
[Who is the Exponential Foundation?](../../../who-is-the-exponential-foundation.md#the-support-commitment).

## Older lines

A site on 5.x, 4.x or older receives no fixes. The migration chapters of the installation book bring such sites to
6.0: [14. Migrating from the 4.x line](../../../../install/14-migrating-from-4x.md),
[15. Migrating from the 5.x legacy stack](../../../../install/15-migrating-from-5x-legacy.md) and
[16. Migrating from eZ Platform and Ibexa](../../../../install/16-migrating-from-ez-platform-and-ibexa.md). Sites of
that era ran on the code base of eZ Publish, which its creator discontinued after almost eighteen years; Exponential
continues that code base ([Who is 7x?](../../../who-is-7x.md)).

A site that must stay on PHP 7.4 can only run up to 6.0.7, which is no longer the maintained line
([2. Requirements](../../../../install/02-requirements.md#versions)).

## Extensions

Extensions are separate packages with their own versions. Each records its version in `ezinfo.php` and `extension.xml`
([Source control and releases](../../../../standards/source-control-and-releases.md#extension-releases)), and the
about page lists them. `SECURITY.md` covers the kernel repository; for each extension your site uses, check its own
repository and changelog in `doc/changelogs/extensions/`. Extensions written for you by a supplier are supported only
as the contract says.

## In a contract

```text
Supported versions
1. The Supplier keeps the Site on the newest released 6.0.x version of Exponential, or on a version
   no older than <n> releases behind it, as listed in SECURITY.md of the Exponential repository.
2. When a release fixes a security issue, the Supplier upgrades the Site within <time> of the release
   (see the security update clause).
3. The Supplier reports the kernel version and every extension version in each <period> report,
   as shown by Setup > System information and the about page.
4. If the Site uses a line that SECURITY.md no longer lists as supported, the Supplier proposes a
   migration plan within <time>.
```

Fill in the times yourself; nothing in the project sets them. The tender templates part of this procurement section
has longer clauses.

## Related pages

- [The product lifecycle](README.md)
- [Release policy and version numbers](release-policy-and-version-numbers.md)
- [Security updates and patching cadence](security-updates-and-patching-cadence.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
