---
title: Composer and Packagist in the ecosystem
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 22
summary: How Composer and Packagist carry the Exponential ecosystem - installing the kernel and extensions, the se7enxweb vendor, Git clones as an alternative, the Packagist webhook that keeps versions current, and Updates and packages in the administration.
keywords: Composer, Packagist, composer install, composer require, se7enxweb vendor, Git clone, webhook, packages, Updates and packages, ezupdate
type: help
---
# Composer and Packagist in the ecosystem

## Contents

1. [Why Composer](#why-composer)
2. [The se7enxweb vendor](#the-se7enxweb-vendor)
3. [Composer or Git clones](#composer-or-git-clones)
4. [Publishing on Packagist](#publishing-on-packagist)
5. [Packages in the administration](#packages-in-the-administration)

## Why Composer

"Exponential 6.0.x specifically requires the php package manager called Composer" (`README.md`). Composer "installs
the required Zeta Components php libraries and the extensions listed in `composer.json`" (`README.md`,
"Requirements"). Without `composer install`, the software does not run.

## The se7enxweb vendor

- The kernel is `se7enxweb/exponential`; extensions, themes, servers and forks are `se7enxweb/<name>`.
- "On Packagist the vendor se7enxweb has 219 packages", counted on 18 October 2026
  (<https://packagist.org/packages/list.json?vendor=se7enxweb>,
  [The extension ecosystem](../../who-is-the-exponential-foundation.md#the-extension-ecosystem)).
- Third-party packages also take part: for example Nexus requires `netgen/layouts-core`
  ([The Platform line](../products/the-platform-line.md)).

## Composer or Git clones

"Every package is a GitHub repository under se7enxweb and a Composer package; `composer require` or a Git clone installs
it" ([Releases and version numbers](../../who-is-the-exponential-foundation.md#releases-and-version-numbers)). Git clones
make it easy to update and to contribute fixes ([Develop with Git](../../develop-with-git.md),
[Getting the code](../../../install/03-getting-the-code.md)).

## Publishing on Packagist

For your own package: a `composer.json`, a public repository, a Packagist entry and a webhook so that Packagist sees
new tags ([Composer and Packagist](../../extension-development/part-7-releasing/composer-and-packagist.md),
[The Packagist webhook](../../extension-development/part-7-releasing/the-packagist-webhook.md)). Not every extension
has a `composer.json` yet; adding them is one of the roadmap's calls for volunteers ([Roadmap](../../project/roadmap.md)).

## Packages in the administration

"**Setup > Updates and packages**, from the `ezupdate` extension, finds, checks and installs Composer packages from
package servers" ([Roadmap](../../project/roadmap.md)); see [Update with Git](../../update-with-git.md).

## Related pages

- [Packages](README.md)
- [How packages are versioned](how-packages-are-versioned.md)
- [Composer and Git on servers](../../hosting/basics/composer-and-git-on-servers.md)
- [PHP and Composer standards](../../../standards/php-and-composer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
