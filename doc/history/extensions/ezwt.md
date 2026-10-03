# ezwt (website toolbar): chronicle

The website toolbar was repackaged in early 2024, cleaned for HTML5 in July 2026, and in October 2026 its sort page moved from YUI to jQuery (6.0.8). See the [feature page](../../features/6.0/extensions/ezwt.md).

This page lists **every one of the 20 changes** of the repository `ezwt` between 2023-12-23 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/ezwt.md); what each release contains is in the [release notes](../../changelogs/extensions/ezwt.md); how to use the extension is on its [feature page](../../features/6.0/extensions/ezwt.md).

| Kind | Changes |
|---|---|
| feature | 1 |
| upgrade note | 3 |
| docs | 4 |
| tooling | 5 |
| release | 4 |
| no user benefit | 3 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2023-12-23 | 6.0 | [`f6e1b2e`](https://github.com/se7enxweb/ezwt/commit/f6e1b2e) |
| 2024-01-29 | v6.0.1 | [`5ad931c`](https://github.com/se7enxweb/ezwt/commit/5ad931c) |
| 2024-01-29 | v6.0.2 | [`fb70415`](https://github.com/se7enxweb/ezwt/commit/fb70415) |
| 2026-07-18 | v6.0.3 | [`cae2638`](https://github.com/se7enxweb/ezwt/commit/cae2638) |
| 2026-09-27 | v6.0.4 | [`513a123`](https://github.com/se7enxweb/ezwt/commit/513a123) |
| 2026-09-28 | v6.0.5 | [`407b25f`](https://github.com/se7enxweb/ezwt/commit/407b25f) |
| 2026-09-30 | v6.0.6 | [`af54c77`](https://github.com/se7enxweb/ezwt/commit/af54c77) |
| 2026-10-01 | v6.0.7 | [`2e99d32`](https://github.com/se7enxweb/ezwt/commit/2e99d32) |
| 2026-10-02 | v6.0.8 | [`86b2816`](https://github.com/se7enxweb/ezwt/commit/86b2816) |
| 2026-10-02 | v6.0.9 | [`4eb1d82`](https://github.com/se7enxweb/ezwt/commit/4eb1d82) |

## Timeline

### 2023-12

The month across all extensions: [December 2023](months/2023-12.md). [Ledger of this month](../ledger/ezwt.md#2023-12-2-changes).

- 2023-12-23 [`f6e1b2e`](https://github.com/se7enxweb/ezwt/commit/f6e1b2e) (tooling) Update composer.json switched package vendor **Release 6.0.**
- 2023-12-24 [`4cdc4b0`](https://github.com/se7enxweb/ezwt/commit/4cdc4b0) (no user benefit) Create FUNDING.yml

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/ezwt.md#2024-01-3-changes).

- 2024-01-28 [`674454e`](https://github.com/se7enxweb/ezwt/commit/674454e) (no user benefit) Updated github funding information
- 2024-01-29 [`5ad931c`](https://github.com/se7enxweb/ezwt/commit/5ad931c) (tooling) Update composer.json switched package vendor name **Release v6.0.1.**
- 2024-01-29 [`fb70415`](https://github.com/se7enxweb/ezwt/commit/fb70415) (tooling) Update composer.json changed homepage url **Release v6.0.2.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/ezwt.md#2026-03-1-changes).

- 2026-03-02 [`a82eeba`](https://github.com/se7enxweb/ezwt/commit/a82eeba) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-07

The month across all extensions: [July 2026](months/2026-07.md). [Ledger of this month](../ledger/ezwt.md#2026-07-1-changes).

- 2026-07-18 [`cae2638`](https://github.com/se7enxweb/ezwt/commit/cae2638) (upgrade note) Remove XHTML trailing slashes and obsolete vendor prefixes **Release v6.0.3.**

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/ezwt.md#2026-09-5-changes).

- 2026-09-27 [`513a123`](https://github.com/se7enxweb/ezwt/commit/513a123) (docs) The extension states its version, license and website **Release v6.0.4.**
- 2026-09-28 [`b8bb553`](https://github.com/se7enxweb/ezwt/commit/b8bb553) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`407b25f`](https://github.com/se7enxweb/ezwt/commit/407b25f) (release) Version 6.0.5 **Release v6.0.5.**
- 2026-09-30 [`93afd60`](https://github.com/se7enxweb/ezwt/commit/93afd60) (docs) The description calls the product Exponential
- 2026-09-30 [`af54c77`](https://github.com/se7enxweb/ezwt/commit/af54c77) (release) Version 6.0.6 **Release v6.0.6.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/ezwt.md#2026-10-8-changes).

- 2026-10-01 [`f29c572`](https://github.com/se7enxweb/ezwt/commit/f29c572) (upgrade note) The date and date/time fields of the ezdemo design use Exponential UI's calendar, exp::datepicker, when expui is active and load YUI's calendar only without it, so that editing runs on jQuery 4.
- 2026-10-01 [`deffdd1`](https://github.com/se7enxweb/ezwt/commit/deffdd1) (tooling) Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback.
- 2026-10-01 [`2e99d32`](https://github.com/se7enxweb/ezwt/commit/2e99d32) (release) Version 6.0.7 **Release v6.0.7.**
- 2026-10-02 [`86b2816`](https://github.com/se7enxweb/ezwt/commit/86b2816) (upgrade note) YUI from the website toolbar; sorting and date fields run on jQuery and Exponential UI **Release v6.0.8.**
- 2026-10-02 [`d33a8e0`](https://github.com/se7enxweb/ezwt/commit/d33a8e0) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`3508df1`](https://github.com/se7enxweb/ezwt/commit/3508df1) (docs) The entry point files carry a header of 7x and the Exponential Foundation; the original headers move to the classes
- 2026-10-02 [`7f8296a`](https://github.com/se7enxweb/ezwt/commit/7f8296a) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`4eb1d82`](https://github.com/se7enxweb/ezwt/commit/4eb1d82) (release) Version 6.0.9 **Release v6.0.9.**

## Related

* [Feature page](../../features/6.0/extensions/ezwt.md)
* [Release notes](../../changelogs/extensions/ezwt.md)
* [Change ledger](../ledger/ezwt.md)
* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
