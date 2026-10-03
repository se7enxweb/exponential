# enhancedselection2 (selection datatype): chronicle

The identifier-storing selection datatype changed vendor in 2024 and in late September and October 2026 got a PostgreSQL fix, translations and a repair of its multiple selection templates. See the [feature page](../../features/6.0/extensions/enhancedselection2.md).

This page lists **every one of the 15 changes** of the repository `enhancedselection2` between 2024-03-06 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/enhancedselection2.md); what each release contains is in the [release notes](../../changelogs/extensions/enhancedselection2.md); how to use the extension is on its [feature page](../../features/6.0/extensions/enhancedselection2.md).

| Kind | Changes |
|---|---|
| feature | 4 |
| fix | 1 |
| docs | 1 |
| tooling | 4 |
| release | 4 |
| no user benefit | 1 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-03-06 | 2.1.1 | [`9bc66e1`](https://github.com/se7enxweb/enhancedselection2/commit/9bc66e1) |
| 2024-03-06 | 2.1.2 | [`4a50d0e`](https://github.com/se7enxweb/enhancedselection2/commit/4a50d0e) |
| 2026-09-27 | 2.1.3 | [`98febe4`](https://github.com/se7enxweb/enhancedselection2/commit/98febe4) |
| 2026-09-28 | 2.1.4 | [`a9a3342`](https://github.com/se7enxweb/enhancedselection2/commit/a9a3342) |
| 2026-09-30 | 2.1.5 | [`c434950`](https://github.com/se7enxweb/enhancedselection2/commit/c434950) |
| 2026-10-02 | 2.1.6 | [`2fdaddf`](https://github.com/se7enxweb/enhancedselection2/commit/2fdaddf) |
| 2026-10-02 | 2.1.7 | [`ec64b8b`](https://github.com/se7enxweb/enhancedselection2/commit/ec64b8b) |

## Timeline

### 2024-03

The month across all extensions: [March 2024](months/2024-03.md). [Ledger of this month](../ledger/enhancedselection2.md#2024-03-2-changes).

- 2024-03-06 [`9bc66e1`](https://github.com/se7enxweb/enhancedselection2/commit/9bc66e1) (tooling) Update composer.json switched package vendor **Release 2.1.1.**
- 2024-03-06 [`4a50d0e`](https://github.com/se7enxweb/enhancedselection2/commit/4a50d0e) (tooling) Update composer.json switched installer package vendor **Release 2.1.2.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/enhancedselection2.md#2026-03-1-changes).

- 2026-03-02 [`92b30f8`](https://github.com/se7enxweb/enhancedselection2/commit/92b30f8) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/enhancedselection2.md#2026-09-6-changes).

- 2026-09-27 [`98febe4`](https://github.com/se7enxweb/enhancedselection2/commit/98febe4) (feature) ezinfo.php states the extension's name, version, copyright, license and website **Release 2.1.3.**
- 2026-09-28 [`e6afa97`](https://github.com/se7enxweb/enhancedselection2/commit/e6afa97) (fix) Fixed: Selections of an attribute that has no id yet are not looked up, and ids are compared as integers, so PostgreSQL installs the content
- 2026-09-28 [`1e4df6b`](https://github.com/se7enxweb/enhancedselection2/commit/1e4df6b) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`a9a3342`](https://github.com/se7enxweb/enhancedselection2/commit/a9a3342) (release) Version 2.1.4 **Release 2.1.4.**
- 2026-09-30 [`fecc9b7`](https://github.com/se7enxweb/enhancedselection2/commit/fecc9b7) (docs) The description calls the product Exponential
- 2026-09-30 [`c434950`](https://github.com/se7enxweb/enhancedselection2/commit/c434950) (release) Version 2.1.5 **Release 2.1.5.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/enhancedselection2.md#2026-10-6-changes).

- 2026-10-02 [`998dc8f`](https://github.com/se7enxweb/enhancedselection2/commit/998dc8f) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`23745c4`](https://github.com/se7enxweb/enhancedselection2/commit/23745c4) (feature) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`c32095c`](https://github.com/se7enxweb/enhancedselection2/commit/c32095c) (feature) The edit and collect templates of a multiple selection parse again: a stray closing parenthesis made the template parser reject the checkbox list, so the edit form of a class using it showed no fields
- 2026-10-02 [`2fdaddf`](https://github.com/se7enxweb/enhancedselection2/commit/2fdaddf) (release) Version 2.1.6 **Release 2.1.6.**
- 2026-10-02 [`16a2231`](https://github.com/se7enxweb/enhancedselection2/commit/16a2231) (tooling) The commands start through the shared command helpers
- 2026-10-02 [`ec64b8b`](https://github.com/se7enxweb/enhancedselection2/commit/ec64b8b) (release) Version 2.1.7 **Release 2.1.7.**

## Related

* [Feature page](../../features/6.0/extensions/enhancedselection2.md)
* [Release notes](../../changelogs/extensions/enhancedselection2.md)
* [Change ledger](../ledger/enhancedselection2.md)
