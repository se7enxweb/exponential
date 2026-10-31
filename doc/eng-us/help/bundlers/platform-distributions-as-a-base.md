---
title: Platform distributions as the base of a bundle
audience: bundler, developer
category: bundling
order: 50
summary: What bundlers need to know when they build on the Exponential Platform Legacy or Nexus distributions - which lines carry Exponential 6, how they install, the version traps on Packagist, the default account in the demo data, and how to keep a distribution-based bundle current.
keywords: Legacy, Nexus, Exponential Platform, distribution, legacy bridge, Symfony, exponential:install, exponential-media, Packagist, versions, create-project, demo account
type: help
---
# Platform distributions as the base of a bundle

This page is for bundlers who build on a Symfony-based Exponential Platform distribution that carries Exponential 6:
**Legacy** or **Nexus**. It collects what the documentation states about them and points to the repository READMEs
for everything else. If you build on plain Exponential 6, read [Exponential 6 as a base](exponential-6-as-a-base.md)
instead.

## Contents

1. [Legacy and Nexus in short](#legacy-and-nexus-in-short)
2. [Which Nexus line carries Exponential 6](#which-nexus-line-carries-exponential-6)
3. [Version traps](#version-traps)
4. [Installing a Nexus-based bundle](#installing-a-nexus-based-bundle)
5. [The demo account](#the-demo-account)
6. [Keeping it current](#keeping-it-current)

## Legacy and Nexus in short

| | Legacy | Nexus |
|---|---|---|
| What | "Dual Kernel Symfony CMS which also includes a full copy of Exponential 6" | a ready-made website project with demo content, Exponential Layouts, a Twig front end, an administration, REST and GraphQL |
| Requires | `se7enxweb/exponential` and `se7enxweb/legacy-bridge` | per line; see below |
| For | existing Exponential sites that want Symfony beside them | agencies and teams who want a complete site fast |
| Source | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-platform-legacy) | [Exponential Platform Nexus](../../features/6.0/platform-nexus-starter.md) |

## Which Nexus line carries Exponential 6

From the `composer.json` and `composer.lock` of each branch and tag
([Nexus](../../features/6.0/platform-nexus-starter.md#what-it-is)):

| Line | Runs on | Legacy kernel |
|---|---|---|
| 1.0.0.x | eZ Platform 2.5 stack, Symfony 3.4, PHP 7.1.3 to 8.5 | yes |
| 1.1.0.x | 3.3 generation, Symfony 5.4, PHP `^8.0` | yes |
| 1.2.0.x | 4.6 generation, Symfony 5.4, PHP 8.2 or newer | yes |
| 1.3.0.x and the v5 starter | Platform v5, Symfony 7.4, PHP 8.4 or newer | **no** |
| 2.5 (`v2.5.0.0` to `v2.5.0.6`) | the Legacy 2.5 distribution packaged as Nexus | yes |

A bundle that promises "Exponential 6 inside" must not be built on 1.3.0.x or the v5 starter.

## Version traps

- **Five-part tags are not on Packagist.** `v1.0.0.0.1` to `v1.0.0.0.3` and `1.3.0.0.0` "exist only as git tags".
- **Always name a version.** `composer create-project se7enxweb/exponential-platform-nexus` without a version picks
  `3.1.6`, the upstream media site, "not a Nexus release". The default branch, `master`, is the 2.5 line.
- **Pin a tag in your bundle**, never a branch. The same rule applies as for the kernel: a published release is
  permanent ([Versions, tags and pinning](versions-tags-and-pinning.md)).

## Installing a Nexus-based bundle

The documented commands for the v5 starter end with `php bin/console exponential:install exponential-media
--no-interaction`; the 1.1.0.x and 1.2.0.x branches "install the same way (`exponential:install exponential-media`) on
their own generation". `php bin/console help exponential:install` shows the install types of your checkout. The
1.0.0.x line installs from SQL dumps and needs extra symlinks; the steps are on
[Install Nexus 1.0.0.x](../../features/6.0/platform-nexus-starter.md#install-nexus-100x-the-25-line).

For a bundle, run these steps at build time or at first start exactly as the README of your line gives them, then
treat the result like any other bundle: permissions, caches, the demo account, the release checklist.

## The demo account

"The public installation guides in the repositories contain a default administrator account for the demo data. Treat
it as public knowledge and change it" ([Nexus, Limits](../../features/6.0/platform-nexus-starter.md#limits)). A bundle
must not ship that account unchanged to anyone who will put the site on a network, unless it is a public demo by
design ([Default credentials and first sign-in](default-credentials-and-first-sign-in.md)).

## Keeping it current

- A distribution has two update streams: the Symfony side (Composer) and the Exponential 6 inside it. Both arrive
  through Composer in these distributions.
- Read the distribution's changelog before every update ([Platform changelog](../../../changelogs/extensions/exponential-platform.md)).
- Test the update on a copy of a bundled installation before you publish it ([Testing a bundle](testing-a-bundle.md)).

## Related pages

- [Choosing the base](choosing-the-base.md)
- [Legacy bridge](../../features/6.0/legacy-bridge.md)
- [Site bundles, metadata and the Twig tools of the platform](../../features/6.0/platform-site-bundles.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
