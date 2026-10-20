# Layouts on the platform: the layouts-core fork

This page is for developers of Nexus and DXP projects on the Symfony based platform who use Netgen Layouts on PHP 8.4
or 8.5. The se7enxweb fork of the Layouts core makes block templates that read parameters work on those PHP versions.

| | |
|---|---|
| Repository | `se7enxweb/layouts-core` (fork of Netgen Layouts core) |
| Newest tag | `2.0.0-se7enx.1` (2026-04-19) |
| Upstream releases in the history | `1.4.10` (2024-09-06), `1.4.11`, `1.4.12`, `1.4.13` (2025-12-17), `2.0.0` (2026-02-25) |
| History | [layouts-core](../../history/ecosystem/layouts-core.md): 450 changes, one of them by the se7enxweb team |
| Counterpart in Exponential 6 | the extensions `explayouts`, `explayouts_ui`, `explayouts_ui_api`; see [Exponential Layouts](../../bc/6.0/LAYOUTS.md) |

## What it is

Layouts is the page builder of the platform side of the family. Editors compose a page from zones and blocks in a
visual editor, and rules decide which layout applies to which request. The core package holds the model (layouts,
zones, blocks, collections, rules, targets, conditions), the parameter system, the block definitions and the
rendering. Nexus sites (see [Nexus starter](platform-nexus-starter.md)) are built on it through the Netgen Layouts
2.0.x packages: Ibexa integration, Site API integration, the standard blocks, the layouts admin at `/nglayouts/admin`
and the editor app at `/nglayouts/app`.

The concept is the same as in Exponential Layouts, which was ported to the legacy kernel: layout types with named
zones, blocks with parameters, collections of items, and rules with targets that pick the layout of a page. If you
know one, you know the other.

## Use it

You do not install it by hand in a Nexus or DXP project: the metapackage requires it. In your own project:

```bash
composer require se7enxweb/layouts-core
composer why netgen/layouts-core     # shows se7enxweb/layouts-core as the provider
```

Block templates read parameters as usual. This line is from the Nexus starter's gallery block template
(`templates/nglayouts/themes/app/block/gallery/sushi_bar.html.twig`); it is the kind of access that fails on PHP 8.4
without the fork:

```twig
{% if block.parameter('infinite_loop').value %}data-loop="true"{% endif %}
```

## What the fork changes

Version 2.0 of the core uses PHP 8.4 language features throughout. One of them breaks template access:

- PHP 8.4 added asymmetric visibility (`private(set)`). Twig reads an object's attribute by checking
  `ReflectionProperty::isInitialized()` before it looks for a getter method. For properties declared with
  `private(set)` that check fails, Twig never reaches the getter, and a page that reads a parameter in a block
  template stops with "Call to undefined method".
- The fork (commit `3ec0262b2`, 2026-04-19) adds explicit getter methods to `Netgen\Layouts\Parameters\Parameter`:
  `getName()`, `getParameterDefinition()`, `getValue()`, `isEmpty()` and `getValueObject()`, so Twig resolves them by
  ordinary method lookup. The same commit renames the package to `se7enxweb/layouts-core` and adds
  `replace: netgen/layouts-core`.

Because of the `replace` declaration, every Netgen Layouts package that requires `netgen/layouts-core` is satisfied by
the fork. The metapackage `se7enxweb/exponential-platform-dxp` requires the fork directly (commit "require
se7enxweb/layouts-core fork for PHP 8.4 private(set) Twig compatibility", 2026-04-19). Tag `2.0.0-se7enx.1` marks the
fork on top of upstream `2.0.0`.

## What upstream changed in 2.0 (carried history)

Between October 2025 and February 2026: constructor property promotion and missing typehints (2025-10-28),
deprecations removed across Symfony and Doctrine (2025-11-10), PHPUnit annotations replaced by attributes
(2025-11-11), core API values moved to property hooks (2025-11-26), view system and config values on property hooks
(2025-11-27/28), UUIDs switched to the Symfony Uid component (2025-12-13), and Uuid v7 in tests (2025-12-31). Projects
that extend layout classes should read the upstream upgrade notes for 2.0 before moving from 1.4.

## Limits

- The fork needs PHP 8.4 (`^8.4`).
- Layouts 1.4 on PHP below 8.4 does not need the fork.

## Related pages

- [Exponential Layouts](../../bc/6.0/LAYOUTS.md)
- Platform features: [administration interface](platform-admin-ui-fork.md), [DXP skeleton](platform-dxp-skeleton.md), [Nexus starter](platform-nexus-starter.md), [PHP 8.5 framework forks](platform-php85-framework-forks.md), [site bundles](platform-site-bundles.md), [SQLite for Exponential Platform](platform-sqlite-install.md), [legacy bridge](legacy-bridge.md), [AdminNeo database manager](adminneo-database-manager.md)
- Specifications: [platform console command names](../../specifications/6.0/platform-console-commands.md), [platform package map](../../specifications/6.0/platform-package-map.md), [platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md), [legacy bridge bundle](../../specifications/6.0/legacy-bridge-bundle.md)
- Upgrade notes: [package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md)
