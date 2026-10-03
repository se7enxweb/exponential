# ezstarrating (star ratings): chronicle

Star rating changed vendor in early 2024 and was fixed for PHP 8 and persistent workers on 27 September 2026, then moved to jQuery 4 and lost its YUI script in October. See the [feature page](../../features/6.0/extensions/ezstarrating.md).

This page lists **every one of the 18 changes** of the repository `ezstarrating` between 2023-12-23 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/ezstarrating.md); what each release contains is in the [release notes](../../changelogs/extensions/ezstarrating.md); how to use the extension is on its [feature page](../../features/6.0/extensions/ezstarrating.md).

| Kind | Changes |
|---|---|
| feature | 3 |
| upgrade note | 2 |
| docs | 3 |
| tooling | 3 |
| release | 4 |
| no user benefit | 3 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2023-12-23 | 6.0 | [`ee6f3d5`](https://github.com/se7enxweb/ezstarrating/commit/ee6f3d5) |
| 2024-01-29 | v6.0.1 | [`9d48709`](https://github.com/se7enxweb/ezstarrating/commit/9d48709) |
| 2024-01-29 | v6.0.2 | [`24aecf1`](https://github.com/se7enxweb/ezstarrating/commit/24aecf1) |
| 2026-09-27 | v6.0.3 | [`7d12e86`](https://github.com/se7enxweb/ezstarrating/commit/7d12e86) |
| 2026-09-30 | v6.0.4 | [`a85a8a2`](https://github.com/se7enxweb/ezstarrating/commit/a85a8a2) |
| 2026-10-01 | v6.0.5 | [`2403cc9`](https://github.com/se7enxweb/ezstarrating/commit/2403cc9) |
| 2026-10-02 | v6.0.6 | [`055e15d`](https://github.com/se7enxweb/ezstarrating/commit/055e15d) |
| 2026-10-02 | v6.0.7 | [`953918f`](https://github.com/se7enxweb/ezstarrating/commit/953918f) |
| 2026-10-02 | v6.0.8 | [`662636e`](https://github.com/se7enxweb/ezstarrating/commit/662636e) |

## Timeline

### 2023-12

The month across all extensions: [December 2023](months/2023-12.md). [Ledger of this month](../ledger/ezstarrating.md#2023-12-2-changes).

- 2023-12-23 [`ee6f3d5`](https://github.com/se7enxweb/ezstarrating/commit/ee6f3d5) (tooling) Update composer.json switched package vendor **Release 6.0.**
- 2023-12-24 [`d6bf494`](https://github.com/se7enxweb/ezstarrating/commit/d6bf494) (no user benefit) Create FUNDING.yml

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/ezstarrating.md#2024-01-3-changes).

- 2024-01-28 [`4f0f6c7`](https://github.com/se7enxweb/ezstarrating/commit/4f0f6c7) (no user benefit) Updated github funding information
- 2024-01-29 [`9d48709`](https://github.com/se7enxweb/ezstarrating/commit/9d48709) (tooling) Update composer.json switched package name **Release v6.0.1.**
- 2024-01-29 [`24aecf1`](https://github.com/se7enxweb/ezstarrating/commit/24aecf1) (tooling) Update composer.json switched package vendor name **Release v6.0.2.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/ezstarrating.md#2026-03-1-changes).

- 2026-03-02 [`268106c`](https://github.com/se7enxweb/ezstarrating/commit/268106c) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/ezstarrating.md#2026-09-5-changes).

- 2026-09-27 [`f9b991a`](https://github.com/se7enxweb/ezstarrating/commit/f9b991a) (feature) Constructors are public as the parent class requires, and the rating stats cache resets between requests
- 2026-09-27 [`7374103`](https://github.com/se7enxweb/ezstarrating/commit/7374103) (feature) SQLite schema for the star rating tables
- 2026-09-27 [`7d12e86`](https://github.com/se7enxweb/ezstarrating/commit/7d12e86) (docs) The extension states its version, license and website **Release v6.0.3.**
- 2026-09-30 [`4874340`](https://github.com/se7enxweb/ezstarrating/commit/4874340) (docs) The description calls the product Exponential
- 2026-09-30 [`a85a8a2`](https://github.com/se7enxweb/ezstarrating/commit/a85a8a2) (release) Version 6.0.4 **Release v6.0.4.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/ezstarrating.md#2026-10-7-changes).

- 2026-10-01 [`ae6ef2c`](https://github.com/se7enxweb/ezstarrating/commit/ae6ef2c) (upgrade note) Updated the star rating for jQuery 4, so that rating and its click handler use .on() and .off() instead of the deprecated click shorthand and .unbind().
- 2026-10-01 [`2403cc9`](https://github.com/se7enxweb/ezstarrating/commit/2403cc9) (release) Version 6.0.5 **Release v6.0.5.**
- 2026-10-02 [`055e15d`](https://github.com/se7enxweb/ezstarrating/commit/055e15d) (upgrade note) The YUI 3 rating script; the rating runs on jQuery alone **Release v6.0.6.**
- 2026-10-02 [`57ecf29`](https://github.com/se7enxweb/ezstarrating/commit/57ecf29) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`953918f`](https://github.com/se7enxweb/ezstarrating/commit/953918f) (release) Version 6.0.7 **Release v6.0.7.**
- 2026-10-02 [`edc1ec0`](https://github.com/se7enxweb/ezstarrating/commit/edc1ec0) (feature) English and German translations for every string the admin showed untranslated
- 2026-10-02 [`662636e`](https://github.com/se7enxweb/ezstarrating/commit/662636e) (release) Version 6.0.8 **Release v6.0.8.**

## Related

* [Feature page](../../features/6.0/extensions/ezstarrating.md)
* [Release notes](../../changelogs/extensions/ezstarrating.md)
* [Change ledger](../ledger/ezstarrating.md)
