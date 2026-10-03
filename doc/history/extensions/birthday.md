# birthday (birthday datatype): chronicle

The birthday datatype changed vendor in January 2024; in September 2026 its export handler was fixed so that it no longer ends a request, and on 2 October it gained the missing `ezinfo.php`. See the [feature page](../../features/6.0/extensions/birthday.md).

This page lists **every one of the 7 changes** of the repository `birthday` between 2024-01-28 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/birthday.md); what each release contains is in the [release notes](../../changelogs/extensions/birthday.md); how to use the extension is on its [feature page](../../features/6.0/extensions/birthday.md).

| Kind | Changes |
|---|---|
| fix | 2 |
| tooling | 1 |
| release | 2 |
| no user benefit | 2 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-01-28 | 1.3.0 | [`ecc405e`](https://github.com/se7enxweb/birthday/commit/ecc405e) |
| 2026-09-14 | 1.3.1 | [`69f435b`](https://github.com/se7enxweb/birthday/commit/69f435b) |
| 2026-10-02 | 1.3.2 | [`3132895`](https://github.com/se7enxweb/birthday/commit/3132895) |

## Timeline

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/birthday.md#2024-01-3-changes).

- 2024-01-28 [`8fbe590`](https://github.com/se7enxweb/birthday/commit/8fbe590) (no user benefit) Added github funding information
- 2024-01-28 [`ecc405e`](https://github.com/se7enxweb/birthday/commit/ecc405e) (tooling) Update composer.json changed package vendor name **Release 1.3.0.**
- 2024-01-28 [`d1f9e9c`](https://github.com/se7enxweb/birthday/commit/d1f9e9c) (release) Update extension.xml upated version number

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/birthday.md#2026-03-1-changes).

- 2026-03-02 [`a81888a`](https://github.com/se7enxweb/birthday/commit/a81888a) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/birthday.md#2026-09-1-changes).

- 2026-09-14 [`69f435b`](https://github.com/se7enxweb/birthday/commit/69f435b) (fix) Fixed: Fixed the csv export handler, which ended any request that loaded it. **Release 1.3.1.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/birthday.md#2026-10-2-changes).

- 2026-10-02 [`a467a63`](https://github.com/se7enxweb/birthday/commit/a467a63) (fix) Fixed: Added ezinfo.php, which the extension lacked, and corrected extension.xml, which named the product by its old name, gave the license as GPL 2.0 and pointed to a retired website, so that the about page and the upgrade checks show the extension's name, version, license and website.
- 2026-10-02 [`3132895`](https://github.com/se7enxweb/birthday/commit/3132895) (release) Version 1.3.2 **Release 1.3.2.**

## Related

* [Feature page](../../features/6.0/extensions/birthday.md)
* [Release notes](../../changelogs/extensions/birthday.md)
* [Change ledger](../ledger/birthday.md)
