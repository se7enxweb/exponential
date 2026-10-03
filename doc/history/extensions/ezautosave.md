# ezautosave (draft autosave): chronicle

Autosave changed vendor in early 2024 and in October 2026 moved from YUI to Exponential UI's `exp::autosave` (6.0.5 to 6.0.7). See the [feature page](../../features/6.0/extensions/ezautosave.md).

This page lists **every one of the 18 changes** of the repository `ezautosave` between 2023-12-23 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/ezautosave.md); what each release contains is in the [release notes](../../changelogs/extensions/ezautosave.md); how to use the extension is on its [feature page](../../features/6.0/extensions/ezautosave.md).

| Kind | Changes |
|---|---|
| feature | 1 |
| upgrade note | 3 |
| docs | 3 |
| tooling | 4 |
| release | 4 |
| no user benefit | 3 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-01-28 | v6.0.0 | [`a30f09d`](https://github.com/se7enxweb/ezautosave/commit/a30f09d) |
| 2026-09-27 | v6.0.1 | [`fc1d28d`](https://github.com/se7enxweb/ezautosave/commit/fc1d28d) |
| 2026-09-27 | v6.0.2 | [`aff1aed`](https://github.com/se7enxweb/ezautosave/commit/aff1aed) |
| 2026-09-28 | v6.0.3 | [`b2386fe`](https://github.com/se7enxweb/ezautosave/commit/b2386fe) |
| 2026-09-30 | v6.0.4 | [`a38d261`](https://github.com/se7enxweb/ezautosave/commit/a38d261) |
| 2026-10-01 | v6.0.5 | [`43feb7b`](https://github.com/se7enxweb/ezautosave/commit/43feb7b) |
| 2026-10-02 | v6.0.6 | [`2bc1ef1`](https://github.com/se7enxweb/ezautosave/commit/2bc1ef1) |
| 2026-10-02 | v6.0.7 | [`b7febda`](https://github.com/se7enxweb/ezautosave/commit/b7febda) |
| 2026-10-02 | v6.0.8 | [`447d480`](https://github.com/se7enxweb/ezautosave/commit/447d480) |

## Timeline

### 2023-12

The month across all extensions: [December 2023](months/2023-12.md). [Ledger of this month](../ledger/ezautosave.md#2023-12-2-changes).

- 2023-12-23 [`50b4386`](https://github.com/se7enxweb/ezautosave/commit/50b4386) (tooling) Update composer.json switched package vendor
- 2023-12-24 [`f80a853`](https://github.com/se7enxweb/ezautosave/commit/f80a853) (no user benefit) Create FUNDING.yml

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/ezautosave.md#2024-01-2-changes).

- 2024-01-28 [`f83984e`](https://github.com/se7enxweb/ezautosave/commit/f83984e) (no user benefit) Updated github funding information
- 2024-01-28 [`a30f09d`](https://github.com/se7enxweb/ezautosave/commit/a30f09d) (tooling) Update composer.json switched package vendor and update package name to remove deprecated -ls switch **Release v6.0.0.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/ezautosave.md#2026-03-1-changes).

- 2026-03-02 [`c3bf825`](https://github.com/se7enxweb/ezautosave/commit/c3bf825) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/ezautosave.md#2026-09-6-changes).

- 2026-09-27 [`fc1d28d`](https://github.com/se7enxweb/ezautosave/commit/fc1d28d) (docs) The extension states its version, license and website **Release v6.0.1.**
- 2026-09-27 [`aff1aed`](https://github.com/se7enxweb/ezautosave/commit/aff1aed) (tooling) The Composer package declares GPL-2.0-or-later, as the extension's own metadata does, in version 6.0.2 **Release v6.0.2.**
- 2026-09-28 [`f7ac3a5`](https://github.com/se7enxweb/ezautosave/commit/f7ac3a5) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`b2386fe`](https://github.com/se7enxweb/ezautosave/commit/b2386fe) (release) Version 6.0.3 **Release v6.0.3.**
- 2026-09-30 [`0fbf2b7`](https://github.com/se7enxweb/ezautosave/commit/0fbf2b7) (docs) The description calls the product Exponential
- 2026-09-30 [`a38d261`](https://github.com/se7enxweb/ezautosave/commit/a38d261) (release) Version 6.0.4 **Release v6.0.4.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/ezautosave.md#2026-10-7-changes).

- 2026-10-01 [`f4e9bee`](https://github.com/se7enxweb/ezautosave/commit/f4e9bee) (upgrade note) Autosave and the draft preview run on Exponential UI's exp::autosave when expui is active, in the admin and the ezwebin templates, and keep the YUI version as the fallback, so that drafts are saved on jQuery 4.
- 2026-10-01 [`abc3c4c`](https://github.com/se7enxweb/ezautosave/commit/abc3c4c) (tooling) Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback.
- 2026-10-01 [`43feb7b`](https://github.com/se7enxweb/ezautosave/commit/43feb7b) (release) Version 6.0.5 **Release v6.0.5.**
- 2026-10-02 [`2bc1ef1`](https://github.com/se7enxweb/ezautosave/commit/2bc1ef1) (upgrade note) The admin edit form's YUI autosave and preview; it autosaves and previews on Exponential UI alone **Release v6.0.6.**
- 2026-10-02 [`b7febda`](https://github.com/se7enxweb/ezautosave/commit/b7febda) (upgrade note) The front-end YUI autosave; ezautosave runs on Exponential UI alone **Release v6.0.7.**
- 2026-10-02 [`1abe15d`](https://github.com/se7enxweb/ezautosave/commit/1abe15d) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`447d480`](https://github.com/se7enxweb/ezautosave/commit/447d480) (release) Version 6.0.8 **Release v6.0.8.**

## Related

* [Feature page](../../features/6.0/extensions/ezautosave.md)
* [Release notes](../../changelogs/extensions/ezautosave.md)
* [Change ledger](../ledger/ezautosave.md)
