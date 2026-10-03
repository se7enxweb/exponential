# Ecosystem repository: ibexa-legacy-bridge---7x

**Group:** Legacy bridge and site bundles. **Period in the ledger:** 2024-02-19 to 2026-04-19. **Changes:** 16 (7 made by the se7enxweb team, 9 upstream history carried by the fork).

## What it is

Bridge for Ibexa 4 to the Exponential legacy kernel (se7enxweb/ibexa-legacy-bridge).

## How it relates to Exponential

Port of the Ibexa 3 APIs to the Ibexa 4 compatible ones; renames the script command to exponential:legacy:script.

## What a user gets

Legacy bridge for Platform 4.6.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ibexa-legacy-bridge
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 4 |
| Fixes | 4 |
| Behaviour and upgrade changes | 1 |
| Tooling | 2 |
| Releases | 1 |
| No user benefit | 4 |

## Changes made by the se7enxweb team, by theme

### Composer requirements (1)

- 2026-03-16 `8ce72f9` tooling: Updated: Refactor bundle to replace ibexa3 apis with MediataCom ibexa4 compatible apis. Added se7enxweb/mediata-ezpage-fieldtype-bundle requirement to

### Exponential branding (1)

- 2026-03-16 `5bd8c73` feature: Updated: Replacing vendor in composer.json. Rebranding.

### Version numbers (1)

- 2026-03-17 `340aedb` release: Updated: Replaced netgen/ezpublish-letgacy-installer package. Version bump to composer config. Rebranding.

### SQLite support (1)

- 2026-04-07 `efe787a` feature: SQLite: map pdo_sqlite driver + inject absolute DB path into legacy INI

### Console command names (1)

- 2026-04-07 `dc19470` bc: rename LegacyEmbedScriptCommand to exponential:legacy:script, keep ezpublish:legacy:script as deprecated alias (v4/4.x)

### Symfony and platform compatibility (1)

- 2026-04-19 `e6ba76f` fix: update mediata-ezpage-fieldtype-bundle constraint to ^4.0 for Ibexa 4.6 compat

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-02 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `5c4d2ab` Allow ibexa-support branch of netgen/ezpublish-legacy; `5aaf2a2` Remove dev-ibexa4 require |
| 2024-04 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `8ebc459` Use Ibexa 4.6 |
| 2024-05 | 4 | 0 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `ca13b11` Fix issues with non matching interfaces after Twig 3.9; `7efbc39` Fix rendering legacy Twig templates with Twig 3.9+ |
| 2024-09 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `6101698` Fix support for Twig 3.13 |

## Full record

- Every change with date, kind, size and release tag: [ledger of ibexa-legacy-bridge---7x](ledger/ibexa-legacy-bridge---7x.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
