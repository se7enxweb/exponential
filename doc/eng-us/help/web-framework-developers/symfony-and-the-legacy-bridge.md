---
title: The Symfony legacy bridge for framework developers
audience: web-framework-developer, developer
category: web-frameworks
order: 410
summary: How Exponential 6 and Symfony run as one application - the legacy bridge bundle that loads the Exponential 6 kernel inside a Symfony platform installation, what each side handles, the legacy_admin siteaccess, the console commands, calling Twig from legacy templates with ngsymfonytools, which bridge line fits which Symfony version, and the limits.
keywords: Symfony, legacy bridge, legacy-bridge, EzPublishLegacyBundle, legacy_admin, legacy_mode, exponential:legacy:script, ngsymfonytools, site-legacy-bundle, Twig, dual kernel, Exponential Platform Legacy, Nexus, web framework developer
type: help
---
# The Symfony legacy bridge for framework developers

This page is for Symfony developers who want Exponential 6 and their Symfony code in one application. The **legacy
bridge** is a Symfony bundle that loads the Exponential 6 kernel inside a Symfony platform installation; one web
application then serves the legacy administration and templates and the Symfony stack, on the same database
([Legacy bridge](../../features/6.0/legacy-bridge.md), [Symfony and the legacy bridge](../creators/app-creators/symfony-and-the-legacy-bridge.md)).
It is not a way to add Symfony to a stand-alone Exponential 6 installation: the host is a Symfony platform project.

## Contents

1. [The pieces](#the-pieces)
2. [Which side does what](#which-side-does-what)
3. [Which line for which Symfony](#which-line-for-which-symfony)
4. [Working across the boundary](#working-across-the-boundary)
5. [Limits](#limits)

## The pieces

| Package | Role |
|---|---|
| `se7enxweb/legacy-bridge` | the bundle (`eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle`) that boots the legacy kernel |
| `se7enxweb/ibexa-legacy-bridge` | the port for the Ibexa 4.6 generation |
| `se7enxweb/site-legacy-bundle` | glue for Netgen-style sites: legacy mapper, templating, event listeners |
| `se7enxweb/ngsymfonytools` | a legacy extension with template operators that let a `.tpl` include Twig or render a Symfony controller |
| `se7enxweb/exponential-legacy-installer` | the Composer installer for the legacy side |

The legacy side is reached through a siteaccess named `legacy_admin`, configured with `legacy_mode: true`
([Legacy bridge bundle specification](../../specifications/6.0/legacy-bridge-bundle.md)).

## Which side does what

| Kept in Exponential 6 (legacy) | Done by Symfony |
|---|---|
| content classes, roles and policies, the legacy administration | public rendering with Twig, routes, controllers |
| `.tpl` templates and legacy extensions | bundles, services, dependency injection |
| workflows, cronjob parts, legacy modules | REST and GraphQL of the platform, Symfony console |

Both read the same content: what editors create in the legacy administration shows up on the Twig front end. Inside
the bridge, the legacy web kernel can receive Symfony's service container (`ezpKernelWeb::hasServiceContainer()`).

## Which line for which Symfony

| Bridge line | Platform | Symfony | PHP |
|---|---|---|---|
| `master` (`v2.1.x`) | eZ Platform 2.5 LTS | 3.4 | 8.x with fixes |
| `3.x` | eZ Platform 3.3 | 5.4 | `^8.0` |
| `4.x` | Ibexa 4.6 compatible installs | 5.4 | `^8.0` |
| `5.x` | Platform v5 | 7.4 | `^8.4` |

Details, tags and requirements: [Which version for which platform](../../features/6.0/legacy-bridge.md#which-version-for-which-platform).

## Working across the boundary

- **Run a legacy script with the platform's settings:**
  `php bin/console exponential:legacy:script bin/php/ezcache.php --clear-id=content --siteaccess=legacy_admin`
  (the `exponential:legacy:*` names exist from the lines and tags listed on the feature page; older lines have only the
  `ezpublish:*` names) ([Console commands](../../features/6.0/legacy-bridge.md#console-commands)).
- **Twig from a legacy template:** activate `ngsymfonytools` and use its operators
  ([Call Twig from a legacy template](../../features/6.0/platform-site-bundles.md#call-twig-from-a-legacy-template)).
- **Caches:** by default the bridge clears the platform's persistence cache when the legacy content view cache is
  cleared, and on `cache:clear` (`ez_publish_legacy.clear_all_spi_cache_from_legacy` and
  `clear_all_spi_cache_on_symfony_clear_cache`).
- **Shared logic:** keep business rules in plain PHP classes both sides can call.

## Limits

- The upstream project stated that the bridge is not supported on eZ Platform 3.x because the schema would diverge;
  the se7enxweb branches keep the legacy schema for the platform kernels listed above.
- Two stacks mean two sets of caches, templates and deployments to run and test on every release.
- Not every platform product carries the legacy kernel: Exponential Platform DXP does not
  ([The platform products](the-platform-products.md)).

## Related pages

- [Mixing Exponential and a framework](mixing-exponential-and-a-framework.md)
- [Migrating from eZ Platform and Ibexa](../../install/16-migrating-from-ez-platform-and-ibexa.md)
- [Platform package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
