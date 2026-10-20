---
title: "The checklist after a migration"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1533
summary: Pages by markup, menus, mappings, drafts, caches, Velocity: the checks after moving.
keywords: checklist, after migration, markup, menus, mappings, caches
type: help
---
# The checklist after a migration

The migrated site is complete.

*Life cycle: Migrate, example 34 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. `exp:layouts list` (mappings, links) and `--drafts` (none left from the import).

## On the command line

1. Page markers (`zone zone-...`, `ngl-block`, menu item IDs) on each page type; `exp:ini get` of the menu lists; **Template & content** cleared; Velocity restarted.

## Which to choose

Run it right after the migration and again the next day.

## Check

- All checks pass.

## Related pages

- [Why doesn't my change appear?](../../why-doesnt-my-change-appear.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
