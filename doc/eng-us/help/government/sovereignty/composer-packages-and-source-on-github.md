---
title: Composer packages and source on GitHub
audience: government, admin, developer
category: public-sector
order: 150
summary: How Exponential's code reaches a public body (GitHub repositories and Composer packages on Packagist), why published versions never change, how to keep your own copy of every source you depend on, and how to check what an installation contains.
keywords: Composer, Packagist, GitHub, source code, packages, composer.lock, mirror, releases, tags, immutable versions, supply chain, sovereignty
type: help
---
# Composer packages and source on GitHub

This page is for administrators and developers who must know where the code comes from and how to keep it available
even if an outside service is not. Every package of the project is a GitHub repository under the se7enxweb
organization and a Composer package; `composer require` or a Git clone installs it
([Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#the-support-commitment),
[Getting the code](../../../install/03-getting-the-code.md)).

## Contents

1. [Where the code is](#where-the-code-is)
2. [Published versions never change](#published-versions-never-change)
3. [Knowing what you run](#knowing-what-you-run)
4. [Keeping your own copy](#keeping-your-own-copy)
5. [Updating](#updating)

## Where the code is

| Part | Where |
|---|---|
| The kernel | GitHub `se7enxweb/exponential`, Composer package `se7enxweb/exponential` |
| Extensions required by the kernel | dozens of `se7enxweb/*` packages listed in the root `composer.json` |
| Velocity | `se7enxweb/exponential-velocity` |
| Third-party libraries | their own Composer packages |

## Published versions never change

The project's security policy says: "A published tag is never changed: a fix for a problem in `v6.0.14` or an
earlier tag reaches you as the next release, not as a corrected copy of the old one" (SECURITY.md, quoted in
[Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#the-support-commitment)). For a public
body this means a version number identifies exactly one content, which helps audits and reproducible builds.

## Knowing what you run

- `composer.lock` of your installation records the exact version and commit of every package.
- **Setup > Updates and packages** (`ezupdate`) shows installed packages, how each was fetched and, for Git working
  copies, the branch and commit ([ezupdate](../../../features/6.0/extensions/ezupdate.md)).
- The about page lists every extension with its version and license
  ([About and package pages](../../../features/6.0/about-and-package-pages.md)).
- **Setup > Upgrade check** compares files with the shipped checksums ([Upgrade check](../../../guides/upgrade-check.md)).

## Keeping your own copy

To stay independent of GitHub and Packagist being reachable:

1. Keep your installation's code, including `vendor/` or the extension directories it installed, in your own
   repository or artifact store, together with `composer.lock`.
2. Optionally mirror the GitHub repositories you depend on into your own Git server.
3. Composer can use your own package repositories; `ezupdate` lists the `repositories` of `composer.json`, and
   packagist.org can be switched off there.
4. Test that a fresh installation builds from your copies alone.

## Updating

Updates are Composer operations: through **Setup > Updates and packages** (a check that changes nothing, a dry run, a
backup confirmation) or on the command line ([Upgrading](../../../guides/upgrading.md),
[Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md)). Read the changelog of
each release before updating a production site.

## Related pages

- [Getting the code](../../../install/03-getting-the-code.md)
- [Composer and Git on servers](../../hosting/basics/composer-and-git-on-servers.md)
- [Update with Git](../../update-with-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
