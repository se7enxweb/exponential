# Ecosystem repository: ezplatform-xmltext-fieldtype

**Group:** Field types. **Period in the ledger:** 2026-03-25 to 2026-04-17. **Changes:** 7 (7 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

XmlText field type for the Symfony platform.

## How it relates to Exponential

Lets the platform read XML text content created in the Exponential legacy kernel; updated to Ibexa 4.6 service naming.

## What a user gets

Existing XML text fields from legacy content display on the new stack.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-xmltext-fieldtype
```

## Where to read more

- [Release changelog](../../changelogs/extensions/ezplatform-xmltext-fieldtype.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Fixes | 6 |
| Behaviour and upgrade changes | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-03-25 | v2.0.3 | `26123b1` | ezsystems/ -> se7enxweb/; expand PHP to ^8.5; add replace |

## Changes made by the se7enxweb team, by theme

### Bug fixes (4)

- 2026-04-17 `f7680a9` fix: update persistence converter DI tag to ibexa.field_type.storage.legacy.converter
- 2026-04-17 `ee23086` fix: update all DI service tags from ezplatform. to ibexa. namespace
- 2026-04-17 `9da623f` fix: update ezurl storage gateway service ID to Ibexa FQCN
- 2026-04-17 `d28c30d` fix: use single quotes for FQCN service ID in YAML (backslash escape)

### Symfony and platform compatibility (2)

- 2026-04-04 `7a26416` fix: Ibexa 4.6 compat - FieldTypeParentCompatibilityPass, dual extension support, ezrichtext aliases
- 2026-04-17 `2730518` fix: update all service IDs to Ibexa 4.6 naming conventions

### Replace declarations for the upstream package (1)

- 2026-03-25 `26123b1` bc: ezsystems/ -> se7enxweb/; expand PHP to ^8.5; add replace

## Full record

- Every change with date, kind, size and release tag: [ledger of ezplatform-xmltext-fieldtype](../ledger/ezplatform-xmltext-fieldtype.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).

<!-- rev2-see-also:start -->
## See also

- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ezplatform-xmltext-fieldtype.md)
- Platform ecosystem by month: [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)

<!-- rev2-see-also:end -->
