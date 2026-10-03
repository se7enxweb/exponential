# ezmultiupload (multiple file upload): chronicle

Multiple upload changed vendor in early 2024 and in October 2026 moved from the YUI 3 uploader to Exponential UI's `exp::upload` (6.0.5, 6.0.6). See the [feature page](../../features/6.0/extensions/ezmultiupload.md).

This page lists **every one of the 21 changes** of the repository `ezmultiupload` between 2023-12-22 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/ezmultiupload.md); what each release contains is in the [release notes](../../changelogs/extensions/ezmultiupload.md); how to use the extension is on its [feature page](../../features/6.0/extensions/ezmultiupload.md).

| Kind | Changes |
|---|---|
| feature | 2 |
| upgrade note | 1 |
| docs | 4 |
| tooling | 6 |
| release | 4 |
| no user benefit | 4 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2023-12-24 | v6.0.0 | [`db64241`](https://github.com/se7enxweb/ezmultiupload/commit/db64241) |
| 2024-01-23 | v6.0.1 | [`a95719f`](https://github.com/se7enxweb/ezmultiupload/commit/a95719f) |
| 2024-01-29 | v6.0.2 | [`749fb36`](https://github.com/se7enxweb/ezmultiupload/commit/749fb36) |
| 2026-09-27 | v6.0.3 | [`f7ae806`](https://github.com/se7enxweb/ezmultiupload/commit/f7ae806) |
| 2026-09-30 | v6.0.4 | [`b40360a`](https://github.com/se7enxweb/ezmultiupload/commit/b40360a) |
| 2026-10-01 | v6.0.5 | [`bbbeddc`](https://github.com/se7enxweb/ezmultiupload/commit/bbbeddc) |
| 2026-10-02 | v6.0.6 | [`ebfb9e0`](https://github.com/se7enxweb/ezmultiupload/commit/ebfb9e0) |
| 2026-10-02 | v6.0.7 | [`4f2dc57`](https://github.com/se7enxweb/ezmultiupload/commit/4f2dc57) |
| 2026-10-02 | v6.0.8 | [`dfd1034`](https://github.com/se7enxweb/ezmultiupload/commit/dfd1034) |

## Timeline

### 2023-12

The month across all extensions: [December 2023](months/2023-12.md). [Ledger of this month](../ledger/ezmultiupload.md#2023-12-2-changes).

- 2023-12-22 [`81294d2`](https://github.com/se7enxweb/ezmultiupload/commit/81294d2) (tooling) Update composer.json switched to se7enxweb package name
- 2023-12-24 [`db64241`](https://github.com/se7enxweb/ezmultiupload/commit/db64241) (no user benefit) Create FUNDING.yml **Release v6.0.0.**

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/ezmultiupload.md#2024-01-5-changes).

- 2024-01-07 [`18619d3`](https://github.com/se7enxweb/ezmultiupload/commit/18619d3) (tooling) Update composer.json replaced dependency on ezsystems repository.
- 2024-01-07 [`2a81173`](https://github.com/se7enxweb/ezmultiupload/commit/2a81173) (no user benefit) Merge pull request #2 from a contributor
- 2024-01-23 [`a95719f`](https://github.com/se7enxweb/ezmultiupload/commit/a95719f) (tooling) Update composer.json renaming package for default installation inclusion **Release v6.0.1.**
- 2024-01-28 [`93c836d`](https://github.com/se7enxweb/ezmultiupload/commit/93c836d) (no user benefit) Updated github funding information
- 2024-01-29 [`749fb36`](https://github.com/se7enxweb/ezmultiupload/commit/749fb36) (tooling) Update composer.json license field value to valid value **Release v6.0.2.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/ezmultiupload.md#2026-03-1-changes).

- 2026-03-02 [`008b3a5`](https://github.com/se7enxweb/ezmultiupload/commit/008b3a5) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/ezmultiupload.md#2026-09-3-changes).

- 2026-09-27 [`f7ae806`](https://github.com/se7enxweb/ezmultiupload/commit/f7ae806) (docs) The extension states its version, license and website **Release v6.0.3.**
- 2026-09-30 [`366120d`](https://github.com/se7enxweb/ezmultiupload/commit/366120d) (docs) The description calls the product Exponential
- 2026-09-30 [`b40360a`](https://github.com/se7enxweb/ezmultiupload/commit/b40360a) (release) Version 6.0.4 **Release v6.0.4.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/ezmultiupload.md#2026-10-10-changes).

- 2026-10-01 [`33744bd`](https://github.com/se7enxweb/ezmultiupload/commit/33744bd) (feature) The multiple upload page runs on Exponential UI's exp::upload when expui is active, with its messages in Exp.dialog, and keeps YUI's uploader as the fallback, so that uploading several files needs no YUI.
- 2026-10-01 [`6208f48`](https://github.com/se7enxweb/ezmultiupload/commit/6208f48) (tooling) Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback.
- 2026-10-01 [`bbbeddc`](https://github.com/se7enxweb/ezmultiupload/commit/bbbeddc) (release) Version 6.0.5 **Release v6.0.5.**
- 2026-10-02 [`ebfb9e0`](https://github.com/se7enxweb/ezmultiupload/commit/ebfb9e0) (upgrade note) The YUI 3 uploader; several files upload on Exponential UI alone **Release v6.0.6.**
- 2026-10-02 [`10037c6`](https://github.com/se7enxweb/ezmultiupload/commit/10037c6) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`79450ce`](https://github.com/se7enxweb/ezmultiupload/commit/79450ce) (docs) The entry point files carry a header of 7x and the Exponential Foundation; the original headers move to the classes
- 2026-10-02 [`5dc1770`](https://github.com/se7enxweb/ezmultiupload/commit/5dc1770) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`4f2dc57`](https://github.com/se7enxweb/ezmultiupload/commit/4f2dc57) (release) Version 6.0.7 **Release v6.0.7.**
- 2026-10-02 [`a6dcdb9`](https://github.com/se7enxweb/ezmultiupload/commit/a6dcdb9) (feature) English and German translations for every string the admin showed untranslated
- 2026-10-02 [`dfd1034`](https://github.com/se7enxweb/ezmultiupload/commit/dfd1034) (release) Version 6.0.8 **Release v6.0.8.**

## Related

* [Feature page](../../features/6.0/extensions/ezmultiupload.md)
* [Release notes](../../changelogs/extensions/ezmultiupload.md)
* [Change ledger](../ledger/ezmultiupload.md)
