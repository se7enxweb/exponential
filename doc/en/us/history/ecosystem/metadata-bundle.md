# metadata-bundle: platform repository history

The history of `metadata-bundle`, one of the platform repositories around Exponential (group: Field types). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 4 changes from 2026-03-02 to 2026-04-12, all made by the se7enxweb team.

## What it is

Metadata field type bundle, compatible with the xrowmetadata legacy extension.

## How it relates to Exponential

Renamed to se7enxweb/metadata-bundle with Ibexa 5.x / PHP 8.4 support.

## What a user gets

The same SEO metadata field works on the legacy kernel and on the new stack.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/metadata-bundle
```

## Counts by kind

| Kind | Changes |
|---|---|
| Fixes | 1 |
| Behaviour and upgrade changes | 2 |
| No user benefit | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-03-26 | v3.0.1 | `1de801a` | Rename to se7enxweb/metadata-bundle, switch to ezsystems/ezplatform-kernel, add PHP 8.5 support |
| 2026-03-26 | v3.0.2 | `070db38` | Add replace for netgen/metadata-bundle |
| 2026-04-12 | v5.0.0 | `8e526bd` | feat(5.0.x): Ibexa 5.x / PHP 8.4 support |

## Changes made by the se7enxweb team, by theme

### Package renamed to the se7enxweb vendor (1)

- 2026-03-26 `1de801a` bc: Rename to se7enxweb/metadata-bundle, switch to ezsystems/ezplatform-kernel, add PHP 8.5 support

### Replace declarations for the upstream package (1)

- 2026-03-26 `070db38` bc: Add replace for netgen/metadata-bundle

### PHP 8.x compatibility (1)

- 2026-04-12 `8e526bd` fix: feat(5.0.x): Ibexa 5.x / PHP 8.4 support

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Related pages

- [Site bundles](../../features/6.0/platform-site-bundles.md)
- [xrowmetadata extension](../../features/6.0/extensions/xrowmetadata.md)
- [Release changelog](../../changelogs/extensions/metadata-bundle.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/metadata-bundle.md)
- Platform ecosystem by month: [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
