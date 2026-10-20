# eztags (Tags): chronicle

The tag taxonomy extension was repackaged for Exponential in January 2024 and then repaired database by database: SQLite in April 2026, MongoDB in September, Oracle and PostgreSQL on 29 and 30 September. October removed YUI and made the admin render in every admin design. See the [feature page](../../features/6.0/extensions/eztags.md).

This page lists **every one of the 37 changes** of the repository `eztags` between 2023-12-23 and 2026-10-02, by month, with what kind of change each is. Read it to find out when a behaviour arrived and which release you need for it. The complete machine-made record, with sizes, is the [change ledger](../ledger/eztags.md); what each release contains is in the [release notes](../../changelogs/extensions/eztags.md); how to use the extension is on its [feature page](../../features/6.0/extensions/eztags.md).

| Kind | Changes |
|---|---|
| feature | 8 |
| fix | 6 |
| upgrade note | 2 |
| docs | 4 |
| tooling | 6 |
| release | 8 |
| no user benefit | 3 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-01-29 | v2.3.1 | [`2830721`](https://github.com/se7enxweb/eztags/commit/2830721) |
| 2026-04-22 | v2.3.2 | [`65dc524`](https://github.com/se7enxweb/eztags/commit/65dc524) |
| 2026-08-07 | v2.3.3 | [`92ab004`](https://github.com/se7enxweb/eztags/commit/92ab004) |
| 2026-09-19 | v2.3.4 | [`80c42c1`](https://github.com/se7enxweb/eztags/commit/80c42c1) |
| 2026-09-20 | v2.3.5 | [`10872e9`](https://github.com/se7enxweb/eztags/commit/10872e9) |
| 2026-09-22 | v2.4.0 | [`75ab1a1`](https://github.com/se7enxweb/eztags/commit/75ab1a1) |
| 2026-09-27 | v2.4.1 | [`b7a2860`](https://github.com/se7enxweb/eztags/commit/b7a2860) |
| 2026-09-28 | v2.4.2 | [`8b91c09`](https://github.com/se7enxweb/eztags/commit/8b91c09) |
| 2026-09-29 | v2.4.3 | [`9c0b533`](https://github.com/se7enxweb/eztags/commit/9c0b533) |
| 2026-09-30 | v2.4.4 | [`ac660d6`](https://github.com/se7enxweb/eztags/commit/ac660d6) |
| 2026-09-30 | v2.4.5 | [`4afd43a`](https://github.com/se7enxweb/eztags/commit/4afd43a) |
| 2026-09-30 | v2.4.6 | [`02672e3`](https://github.com/se7enxweb/eztags/commit/02672e3) |
| 2026-10-01 | v2.4.7 | [`3e200a1`](https://github.com/se7enxweb/eztags/commit/3e200a1) |
| 2026-10-01 | v2.4.8 | [`a275e6d`](https://github.com/se7enxweb/eztags/commit/a275e6d) |
| 2026-10-02 | v2.4.9 | [`ca15834`](https://github.com/se7enxweb/eztags/commit/ca15834) |
| 2026-10-02 | v2.4.10 | [`4b3fc16`](https://github.com/se7enxweb/eztags/commit/4b3fc16) |
| 2026-10-02 | v2.4.11 | [`0ac2999`](https://github.com/se7enxweb/eztags/commit/0ac2999) |

## Timeline

### 2023-12

The month across all extensions: [December 2023](months/2023-12.md). [Ledger of this month](../ledger/eztags.md#2023-12-2-changes).

- 2023-12-23 [`01a07a0`](https://github.com/se7enxweb/eztags/commit/01a07a0) (tooling) Update composer.json changed package vendor
- 2023-12-24 [`8a094be`](https://github.com/se7enxweb/eztags/commit/8a094be) (no user benefit) Create FUNDING.yml

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/eztags.md#2024-01-3-changes).

- 2024-01-28 [`38f2f9c`](https://github.com/se7enxweb/eztags/commit/38f2f9c) (no user benefit) Updated github funding information
- 2024-01-29 [`04315e7`](https://github.com/se7enxweb/eztags/commit/04315e7) (tooling) Update composer.json switched package name
- 2024-01-29 [`2830721`](https://github.com/se7enxweb/eztags/commit/2830721) (tooling) Update composer.json switched vendor name **Release v2.3.1.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/eztags.md#2026-03-1-changes).

- 2026-03-02 [`e7403b4`](https://github.com/se7enxweb/eztags/commit/e7403b4) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-04

The month across all extensions: [April 2026](months/2026-04.md). [Ledger of this month](../ledger/eztags.md#2026-04-1-changes).

- 2026-04-22 [`65dc524`](https://github.com/se7enxweb/eztags/commit/65dc524) (fix) fix: replace MOD() with % operator and use positional ORDER BY for SQLite compatibility **Release v2.3.2.**

### 2026-08

The month across all extensions: [August 2026](months/2026-08.md). [Ledger of this month](../ledger/eztags.md#2026-08-1-changes).

- 2026-08-07 [`92ab004`](https://github.com/se7enxweb/eztags/commit/92ab004) (feature) SQLite schema for eztags **Release v2.3.3.**

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/eztags.md#2026-09-15-changes).

- 2026-09-19 [`80c42c1`](https://github.com/se7enxweb/eztags/commit/80c42c1) (fix) Fixed: Fixed tags rendering as nothing on MongoDB installations, caused by joins with eztags_keyword the MongoDB driver will not translate, so tagged content lists its tags again and each tag links to its own page. **Release v2.3.4.**
- 2026-09-20 [`10872e9`](https://github.com/se7enxweb/eztags/commit/10872e9) (feature) Updated the top menu entry from "eZ Tags" to "Tags", a shorter name that does not carry the old product's branding now that the extension ships as part of Exponential 6. **Release v2.3.5.**
- 2026-09-22 [`75ab1a1`](https://github.com/se7enxweb/eztags/commit/75ab1a1) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v2.4.0.**
- 2026-09-27 [`1f1833b`](https://github.com/se7enxweb/eztags/commit/1f1833b) (feature) The admin tab, its title and the top menu tooltip call the extension Tags
- 2026-09-27 [`b7a2860`](https://github.com/se7enxweb/eztags/commit/b7a2860) (docs) The extension states its version, license and website **Release v2.4.1.**
- 2026-09-28 [`45a97a7`](https://github.com/se7enxweb/eztags/commit/45a97a7) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`8b91c09`](https://github.com/se7enxweb/eztags/commit/8b91c09) (release) Version 2.4.2 **Release v2.4.2.**
- 2026-09-29 [`9673498`](https://github.com/se7enxweb/eztags/commit/9673498) (feature) The top menu tab and its tooltip have German translations
- 2026-09-29 [`9c0b533`](https://github.com/se7enxweb/eztags/commit/9c0b533) (release) Version 2.4.3 **Release v2.4.3.**
- 2026-09-30 [`9f61ddd`](https://github.com/se7enxweb/eztags/commit/9f61ddd) (docs) The description calls the product Exponential
- 2026-09-30 [`ac660d6`](https://github.com/se7enxweb/eztags/commit/ac660d6) (release) Version 2.4.4 **Release v2.4.4.**
- 2026-09-29 [`ec57870`](https://github.com/se7enxweb/eztags/commit/ec57870) (feature) Fixed: Tag lookups by main translation work on every database, Oracle included
- 2026-09-30 [`5c9efce`](https://github.com/se7enxweb/eztags/commit/5c9efce) (fix) Fixed: The tag tree filter and the tag search by subtree work on every database
- 2026-09-30 [`4afd43a`](https://github.com/se7enxweb/eztags/commit/4afd43a) (release) Version 2.4.5 **Release v2.4.5.**
- 2026-09-30 [`02672e3`](https://github.com/se7enxweb/eztags/commit/02672e3) (feature) ezinfo.php, with Version 2.4.6 **Release v2.4.6.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/eztags.md#2026-10-14-changes).

- 2026-10-01 [`d3dc766`](https://github.com/se7enxweb/eztags/commit/d3dc766) (upgrade note) Updated eztags for jQuery 4, with jsTree 3.3.17, so that the tag tree, the tag fields and the admin tag pages work on jQuery 4 without jQuery Migrate warnings.
- 2026-10-01 [`3e200a1`](https://github.com/se7enxweb/eztags/commit/3e200a1) (release) Version 2.4.7 **Release v2.4.7.**
- 2026-10-01 [`6059fa5`](https://github.com/se7enxweb/eztags/commit/6059fa5) (feature) The children table of a tag runs on Exponential UI's exp::datatable when expui is active, with $.fn.eZTagsChildrenExp, and keeps its YUI 2 DataTable as the fallback, so that the tags admin needs no YUI for it.
- 2026-10-01 [`56e2988`](https://github.com/se7enxweb/eztags/commit/56e2988) (fix) Fixed: Fixed the tags field's edit page in designs that do not load eztags' FrontendJavaScriptList, where it stopped with TagsStructureMenu and $.EzTags not defined, by having the field's template require its own scripts and styles, so that tags can be edited on every site design.
- 2026-10-01 [`ad9e989`](https://github.com/se7enxweb/eztags/commit/ad9e989) (tooling) Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback.
- 2026-10-01 [`f218ff7`](https://github.com/se7enxweb/eztags/commit/f218ff7) (fix) Fixed: Fixed the tags administration in the admin design, where the dashboard, tag pages, forms, left menu and node tab were blank because eZ Tags ships its admin templates in design/admin2 only, so that it works in every admin design without a design setting.
- 2026-10-01 [`a275e6d`](https://github.com/se7enxweb/eztags/commit/a275e6d) (release) Version 2.4.8 **Release v2.4.8.**
- 2026-10-02 [`ca15834`](https://github.com/se7enxweb/eztags/commit/ca15834) (upgrade note) YUI from eZ Tags; the children list of a tag runs on Exponential UI alone **Release v2.4.9.**
- 2026-10-02 [`f2e8f1a`](https://github.com/se7enxweb/eztags/commit/f2e8f1a) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`9a2af2d`](https://github.com/se7enxweb/eztags/commit/9a2af2d) (docs) The entry point files carry a header of 7x and the Exponential Foundation; the original headers move to the classes
- 2026-10-02 [`bbc419d`](https://github.com/se7enxweb/eztags/commit/bbc419d) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`4b3fc16`](https://github.com/se7enxweb/eztags/commit/4b3fc16) (release) Version 2.4.10 **Release v2.4.10.**
- 2026-10-02 [`a3bb010`](https://github.com/se7enxweb/eztags/commit/a3bb010) (tooling) The commands start through the shared command helpers
- 2026-10-02 [`0ac2999`](https://github.com/se7enxweb/eztags/commit/0ac2999) (release) Version 2.4.11 **Release v2.4.11.**

## Related pages

- [Feature page](../../features/6.0/extensions/eztags.md)
- [Release notes](../../changelogs/extensions/eztags.md)
- [Change ledger](../ledger/eztags.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
