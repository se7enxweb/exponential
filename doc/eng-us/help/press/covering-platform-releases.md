---
title: Covering Exponential Platform releases
audience: press
category: press-media
order: 109
summary: How to report on releases of the Symfony based Exponential Platform family - Platform 3.2.9, Legacy, Nexus, the Nexus starter and DXP - what each one is, their latest tagged releases, where the notes live and the mix-ups to avoid.
keywords: Exponential Platform, Platform Legacy, Platform Nexus, Nexus starter, Platform DXP, Symfony, release, release notes, dual kernel, layouts editor, Ibexa, eZ Platform
type: help
---
# Covering Exponential Platform releases

This page is for technical and trade press who report on the Exponential Platform family. These are distributions
built on the Symfony framework, separate from the Exponential 6 kernel and released on their own schedule. They are
easy to confuse with each other, so check which product a release belongs to before you write.

## Contents

1. [The family in one table](#the-family-in-one-table)
2. [Latest tagged releases](#latest-tagged-releases)
3. [Where the notes live](#where-the-notes-live)
4. [Mix-ups to avoid](#mix-ups-to-avoid)
5. [Naming and other companies](#naming-and-other-companies)

## The family in one table

Descriptions from [Who is the Exponential Foundation?, The products one by one](../who-is-the-exponential-foundation.md#the-products-one-by-one);
the Nexus lines from [Exponential Platform Nexus: a finished site in minutes](../../features/6.0/platform-nexus-starter.md).

| Product | What it is | Contains Exponential 6? |
|---|---|---|
| Exponential Platform 3.2.9 | "CMS / DXP based on the last free versions of eZ Platform. Symfony / New Stack Only" | No ("No Exponential 6.x Legacy Support") |
| Exponential Platform Legacy | A distribution of Exponential Platform "with Exponential (Legacy) Included"; "Dual Kernel" | Yes |
| Exponential Platform Nexus | Platform plus Exponential 6, Netgen Media Site and Netgen Layouts, one line per platform generation | In the lines 1.0.0.x to 1.2.0.x and 2.5; not in 1.3.0.x (Platform v5) |
| Nexus starter | The v5 Nexus site as a separate repository, "Currently In Public Beta" in its description | No |
| Exponential Platform DXP | "a free, open-source Digital Experience Platform built on the Symfony framework" | No |

## Latest tagged releases

From the [release notes index](../../../changelogs/extensions/README.md#platform-packages) and each package's page, as of
18 October 2026:

| Package | Latest release | Date | Notes |
|---|---|---|---|
| exponential-platform | v3.2.9 | 2025-09-28 | [release notes](../../../changelogs/extensions/exponential-platform.md) |
| exponential-platform-legacy | v2.5.0.3 | 2026-04-21 | [release notes](../../../changelogs/extensions/exponential-platform-legacy.md) |
| exponential-platform-nexus | 1.0.0.6 | 2026-04-21 | [release notes](../../../changelogs/extensions/exponential-platform-nexus.md) |
| exponential-platform-nexus-starter | 1.0.0.0 | 2026-04-26 | [release notes](../../../changelogs/extensions/exponential-platform-nexus-starter.md) |
| exponential-platform-dxp | v0.0.0.2 | 2026-03-21 | [release notes](../../../changelogs/extensions/exponential-platform-dxp.md) |

Some tags carry a `v`, some do not (`1.0.0.6`); print the number without it in running text
([How version numbers work](how-version-numbers-work.md)). Check the tags again before you publish
([Verifying a release yourself](verifying-a-release-yourself.md)).

## Where the notes live

- The platform release notes list "Releases made by the se7enxweb team"; "Upstream history carried by the fork is
  not repeated here" (for example the [Nexus release notes](../../../changelogs/extensions/exponential-platform-nexus.md)).
  The upstream story is told on the repository pages of the [platform ecosystem](../../history/ecosystem.md).
- How the platform repositories relate to Exponential: [The platform ecosystem](../../history/ecosystem.md).
- How to start a project: [Nexus starter](../../features/6.0/platform-nexus-starter.md),
  [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md).

## Mix-ups to avoid

| Wrong | Right | Source |
|---|---|---|
| "DXP includes the Exponential Layouts editor" | DXP ships no layouts editor, no approval workflow, no scheduler and no legacy kernel; "For Exponential 6 beside Symfony, choose Legacy or Nexus" | [Exponential Platform DXP](../who-is-the-exponential-foundation.md#exponential-platform-dxp) |
| "A Platform release is an Exponential 6 release" | The kernel has its own versions (6.0.x); the platform packages count separately | [Where changelogs and release notes live](where-changelogs-and-release-notes-live.md) |
| "Nexus is the same as DXP" | Nexus is a complete site with demo content and Netgen Layouts, and most of its lines include Exponential 6; DXP is a Symfony-only platform | [Exponential Platform Nexus](../who-is-the-exponential-foundation.md#exponential-platform-nexus), [Nexus starter](../../features/6.0/platform-nexus-starter.md) |
| "Every Nexus release includes Exponential 6" | The 1.3.0.x line and the v5 starter have no legacy kernel | [Nexus starter](../../features/6.0/platform-nexus-starter.md) |
| "The starter is final" | Its description says "Currently In Public Beta" | Same section |
| "DXP is licensed GPL only" | Its `composer.json` says "GPL-2.0-or-later or proprietary" | [Exponential Platform DXP](../who-is-the-exponential-foundation.md#exponential-platform-dxp) |

## Naming and other companies

- DXP's README calls it "the community continuation of the eZ Platform and Ibexa OSS line maintained by 7x". Quote
  that as the README's wording. Do not turn it into a partnership or endorsement: Exponential "is not associated with
  the original eZ Publish software or its original developer, eZ Systems or Ibexa"
  ([Who is 7x?, In short](../who-is-7x.md#in-short)).
- Nexus is built on the open source work of Netgen; name Netgen when you describe it
  ([Who is 7x?, Contributors and friends](../who-is-7x.md#contributors-and-friends-of-the-project)).
- Package names such as `ezplatform-kernel` or `ezpublish-kernel` in the index are names of forks and stay as they
  are; they are identifiers, not product names.

## Related pages

- [Covering a release](covering-a-release.md)
- [Knowing the product family](../advocates/knowing-the-product-family.md)
- [Briefing for technical press](briefing-for-technical-press.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
