---
title: "Velocity after a migration"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1530
summary: On a site served by Exponential Velocity, settings changes need exp:velocity restart, as exp:ini says.
keywords: Velocity, restart, migration, settings
type: help
---
# Velocity after a migration

Migrated settings take effect on Velocity.

*Life cycle: Migrate, example 31 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Layout imports need neither a restart nor a cache clear: an import with `--publish` purges the page cache itself.

## On the command line

1. After the `exp:ini` moves and copies: `./console exp:velocity restart`; then the caches.

## Which to choose

Restart once at the end.

## Check

- The Velocity address shows the migrated menus.

## Related pages

- [Caches after a change](../../caches-after-a-change.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
