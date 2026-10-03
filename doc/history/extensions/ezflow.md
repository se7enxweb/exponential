# ezflow (pages, zones and blocks): chronicle

eZ Flow received a PHP 8.2 bugfix in December 2023, then in September and October 2026 Oracle fixes for block pools and page blocks, and the removal of YUI from the page editor in 6.1.4. See the [feature page](../../features/6.0/extensions/ezflow.md).

This page lists **every one of the 22 changes** of the repository `ezflow` between 2023-12-23 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/ezflow.md); what each release contains is in the [release notes](../../changelogs/extensions/ezflow.md); how to use the extension is on its [feature page](../../features/6.0/extensions/ezflow.md).

| Kind | Changes |
|---|---|
| feature | 6 |
| fix | 4 |
| upgrade note | 1 |
| docs | 2 |
| tooling | 2 |
| release | 4 |
| no user benefit | 3 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-01-28 | v6.0.0 | [`b13c0cb`](https://github.com/se7enxweb/ezflow/commit/b13c0cb) |
| 2024-01-28 | v6.0.1 | [`48be22e`](https://github.com/se7enxweb/ezflow/commit/48be22e) |
| 2024-01-29 | v6.0.2 | [`56db68c`](https://github.com/se7enxweb/ezflow/commit/56db68c) |
| 2026-09-22 | v6.1.0 | [`b7ac2eb`](https://github.com/se7enxweb/ezflow/commit/b7ac2eb) |
| 2026-09-27 | v6.1.1 | [`e8a191f`](https://github.com/se7enxweb/ezflow/commit/e8a191f) |
| 2026-09-27 | v6.1.2 | [`40be4b8`](https://github.com/se7enxweb/ezflow/commit/40be4b8) |
| 2026-09-30 | v6.1.3 | [`1424c02`](https://github.com/se7enxweb/ezflow/commit/1424c02) |
| 2026-10-02 | v6.1.4 | [`d78a00d`](https://github.com/se7enxweb/ezflow/commit/d78a00d) |
| 2026-10-02 | v6.1.5 | [`4f35239`](https://github.com/se7enxweb/ezflow/commit/4f35239) |

## Timeline

### 2023-12

The month across all extensions: [December 2023](months/2023-12.md). [Ledger of this month](../ledger/ezflow.md#2023-12-3-changes).

- 2023-12-23 [`e593c90`](https://github.com/se7enxweb/ezflow/commit/e593c90) (tooling) Update composer.json switched package vendor
- 2023-12-24 [`8a05b14`](https://github.com/se7enxweb/ezflow/commit/8a05b14) (no user benefit) Create FUNDING.yml
- 2023-12-24 [`7470247`](https://github.com/se7enxweb/ezflow/commit/7470247) (fix) Bugfix release of ezezflow composer to fix PHP 8.2 bugs

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/ezflow.md#2024-01-4-changes).

- 2024-01-28 [`b13c0cb`](https://github.com/se7enxweb/ezflow/commit/b13c0cb) (no user benefit) Updated github funding information **Release v6.0.0.**
- 2024-01-28 [`d04d2d7`](https://github.com/se7enxweb/ezflow/commit/d04d2d7) (feature) Added documentation to repository. Mass update from parent repository ezflow-ezpackage
- 2024-01-28 [`48be22e`](https://github.com/se7enxweb/ezflow/commit/48be22e) (feature) Switched package vendor name **Release v6.0.1.**
- 2024-01-29 [`56db68c`](https://github.com/se7enxweb/ezflow/commit/56db68c) (tooling) Update composer.json switched homepage url **Release v6.0.2.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/ezflow.md#2026-03-1-changes).

- 2026-03-02 [`b736ed2`](https://github.com/se7enxweb/ezflow/commit/b736ed2) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/ezflow.md#2026-09-10-changes).

- 2026-09-22 [`b7ac2eb`](https://github.com/se7enxweb/ezflow/commit/b7ac2eb) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v6.1.0.**
- 2026-09-27 [`ad3123d`](https://github.com/se7enxweb/ezflow/commit/ad3123d) (feature) Visible texts and their translations name Exponential
- 2026-09-27 [`4d45ca4`](https://github.com/se7enxweb/ezflow/commit/4d45ca4) (feature) Templates write <br> as HTML5 does
- 2026-09-27 [`a89d5a2`](https://github.com/se7enxweb/ezflow/commit/a89d5a2) (feature) ezinfo.php reports the extension's name, version, copyright and license
- 2026-09-27 [`e8a191f`](https://github.com/se7enxweb/ezflow/commit/e8a191f) (release) Version 6.1.1 **Release v6.1.1.**
- 2026-09-27 [`06d8fd8`](https://github.com/se7enxweb/ezflow/commit/06d8fd8) (docs) extension.xml and ezinfo.php name the license in full, GNU General Public License v2.0 (or any later version)
- 2026-09-27 [`40be4b8`](https://github.com/se7enxweb/ezflow/commit/40be4b8) (release) Version 6.1.2 **Release v6.1.2.**
- 2026-09-30 [`29077fe`](https://github.com/se7enxweb/ezflow/commit/29077fe) (fix) Fixed: A block pool over its limit is trimmed on every database, Oracle included
- 2026-09-30 [`1f32a4d`](https://github.com/se7enxweb/ezflow/commit/1f32a4d) (fix) Fixed: A page block is found in its page on every database, Oracle included
- 2026-09-30 [`1424c02`](https://github.com/se7enxweb/ezflow/commit/1424c02) (release) Version 6.1.3 **Release v6.1.3.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/ezflow.md#2026-10-4-changes).

- 2026-10-02 [`d78a00d`](https://github.com/se7enxweb/ezflow/commit/d78a00d) (upgrade note) YUI from ezflow; the page editor, block tools, push to block, schedule dialog and timeline run on jQuery **Release v6.1.4.**
- 2026-10-02 [`adf61c1`](https://github.com/se7enxweb/ezflow/commit/adf61c1) (feature) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`8cce337`](https://github.com/se7enxweb/ezflow/commit/8cce337) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`4f35239`](https://github.com/se7enxweb/ezflow/commit/4f35239) (release) Version 6.1.5 **Release v6.1.5.**

## Related

* [Feature page](../../features/6.0/extensions/ezflow.md)
* [Release notes](../../changelogs/extensions/ezflow.md)
* [Change ledger](../ledger/ezflow.md)
