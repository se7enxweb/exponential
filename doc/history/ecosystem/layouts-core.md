# Ecosystem repository: layouts-core

**Group:** Layouts and recipes. **Period in the ledger:** 2024-09-06 to 2026-04-19. **Changes:** 450 (1 made by the se7enxweb team, 449 upstream history carried by the fork).

## What it is

Netgen Layouts core (PHP/Symfony page builder engine), fork se7enxweb/layouts-core.

## How it relates to Exponential

The engine behind the layouts of Nexus on Platform v5. explayouts is the Exponential legacy counterpart that follows the same model.

## What a user gets

Layout page builder on PHP 8.4: getter shims make Twig read parameters.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/layouts-core
```

## Where to read more

- [Layouts core fork](../../features/6.0/platform-layouts-core-fork.md)
- [Exponential Layouts](../../bc/6.0/LAYOUTS.md)
- [Release changelog](../../changelogs/extensions/layouts-core.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

<!-- rev2-listed-rows:start -->
## Security, performance and upgrade changes in the history

These changes are classified in the coverage record and are not named elsewhere on this page. Most are upstream history that the fork carries; the date and the commit subject are the ledger entry (see the [full ledger](../ledger/README.md)). Read the subject for what changed; for the exact effect, open the commit in the repository.

### Security (4)

- 2025-11-11 `1d5032714` Replace usage of ezyang html purifier with Symfony Html Sanitizer component
- 2025-12-15 `e5cdcd61e` Remove custom wrapper around Symfonys HtmlSanitizer
- 2025-12-16 `932903874` Rename ApiCsrfValidationListener to AppCsrfValidationListener
- 2025-12-17 `1a35168f3` Do not run CSRF validation on app forms

### Performance (1)

- 2025-10-28 `08e55d125` Various little code optimizations

### Behaviour and upgrade (9)

- 2025-07-15 `bb12b1e1f` Move SYMFONY_DEPRECATIONS_HELPER variable to Composer config
- 2025-07-15 `3c95d79a2` Remove support for Symfony 6.2 and 6.3
- 2025-11-10 `3b06316da` Remove deprecations across the board: symfony, doctrine and so on
- 2025-11-11 `f3c9d6625` Use DsnParser in Doctrine, fix more deprecations
- 2025-11-11 `29fb2386b` Fix some more deprecations
- 2025-11-12 `ea6574da1` Remove wrong deprecation
- 2025-11-17 `e004a96d7` Use PHPUnit deprecations reporter
- 2025-11-20 `49bcbc946` Remove deprecations
- 2025-12-03 `1bfea6fdd` Remove usage of deprecated DI Extension class

<!-- rev2-listed-rows:end -->

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 164 |
| Fixes | 32 |
| Behaviour and upgrade changes | 11 |
| Security | 4 |
| Performance | 1 |
| Documentation | 11 |
| Tooling | 196 |
| Releases | 3 |
| No user benefit | 28 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-04-19 | 2.0.0-se7enx.1 | `3ec0262b2` | add Twig/PHP 8.4 private(set) getter shims to Parameter class |

## Changes made by the se7enxweb team, by theme

### PHP 8.x compatibility (1)

- 2026-04-19 `3ec0262b2` fix: add Twig/PHP 8.4 private(set) getter shims to Parameter class

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-09 | 9 | 0 | 0 | 1 | 0 | 0 | 0 | 7 | 0 | 1 | `03acf1f33` Fix CS; `40987764e` Fix deprecation in Twig 3.12 |
| 2024-05 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `79b509c4a` Support yield rendering strategy in Twig 3.9+ |
| 2025-02 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `0206f2477` Expose block definition plugin classes; `21ea622d7` Add a DynamicParameter class to declare which dynamic parameters a han |
| 2025-07 | 19 | 2 | 1 | 2 | 0 | 0 | 0 | 12 | 0 | 2 | `14f39b6ea` Upgrade PHPStan to 2.x; `3c525cd18` Make LinkValue internal properties only accept strings |
| 2025-09 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `d89ff1fd2` Use include Twig function instead of tag; `5a02c8595` Remove obsolete  tag |
| 2025-10 | 25 | 16 | 1 | 0 | 0 | 1 | 0 | 4 | 0 | 3 | `493bc5ea5` Add missing parameter and return typehints; `4a0824a52` Add constructor property promotion |
| 2025-11 | 165 | 64 | 17 | 6 | 1 | 0 | 2 | 67 | 0 | 8 | `501549e21` Replace PHPUnit annotations with attributes; `e88254df7` Update core API values to use property hooks |
| 2025-12 | 197 | 71 | 11 | 1 | 3 | 0 | 8 | 94 | 2 | 7 | `02befe412` Switch uuids to Symfony Uid component; `1dfede980` Switch mocks to stubs in tests |
| 2026-01 | 21 | 6 | 1 | 1 | 0 | 0 | 1 | 6 | 0 | 6 | `0de13279b` Remove usage of friends-of-behat/suite-settings-extension; `e17483b3f` Remove usage of default_index_method, it is deprecated in Symfony 8.1 |
| 2026-02 | 6 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | 1 | 1 | `e8a47e536` Remove unneeded with() calls for stubs; `f65d58795` Limit node to v14 |

## Full record

- Every change with date, kind, size and release tag: [ledger of layouts-core](../ledger/layouts-core.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).

<!-- rev2-see-also:start -->
## See also

- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/layouts-core.md)
- [Layouts core fork](../../features/6.0/platform-layouts-core-fork.md)
- Platform ecosystem by month: [2024-05](months/2024-05.md), [2024-09](months/2024-09.md), [2025-02](months/2025-02.md), [2025-07](months/2025-07.md), [2025-09](months/2025-09.md), [2025-10](months/2025-10.md), [2025-11](months/2025-11.md), [2025-12](months/2025-12.md), [2026-01](months/2026-01.md), [2026-02](months/2026-02.md), [2026-04](months/2026-04.md)

<!-- rev2-see-also:end -->
