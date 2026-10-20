# ezdemo (demo design): chronicle

The demo design followed the same path as ezwebin: package updates in early 2024, then in September and October 2026 translations, the forgot password and error page fixes, jQuery 4 and the removal of YUI from its galleries and flyouts. See the [feature page](../../features/6.0/extensions/ezdemo.md).

This page lists **every one of the 23 changes** of the repository `ezdemo` between 2023-12-22 and 2026-10-02, by month, with what kind of change each is. Read it to find out when a behaviour arrived and which release you need for it. The complete machine-made record, with sizes, is the [change ledger](../ledger/ezdemo.md); what each release contains is in the [release notes](../../changelogs/extensions/ezdemo.md); how to use the extension is on its [feature page](../../features/6.0/extensions/ezdemo.md).

| Kind | Changes |
|---|---|
| feature | 6 |
| fix | 1 |
| security | 1 |
| upgrade note | 2 |
| docs | 2 |
| tooling | 1 |
| release | 7 |
| no user benefit | 3 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-01-28 | v6.0.0 | [`63203d2`](https://github.com/se7enxweb/ezdemo/commit/63203d2) |
| 2024-01-28 | v6.0.1 | [`b30d24d`](https://github.com/se7enxweb/ezdemo/commit/b30d24d) |
| 2026-09-27 | v6.0.2 | [`c0caec8`](https://github.com/se7enxweb/ezdemo/commit/c0caec8) |
| 2026-09-27 | v6.0.3 | [`9b3554c`](https://github.com/se7enxweb/ezdemo/commit/9b3554c) |
| 2026-09-27 | v6.0.4 | [`037f5af`](https://github.com/se7enxweb/ezdemo/commit/037f5af) |
| 2026-09-27 | v6.0.5 | [`e7f4c0c`](https://github.com/se7enxweb/ezdemo/commit/e7f4c0c) |
| 2026-09-30 | v6.0.6 | [`1fde2c8`](https://github.com/se7enxweb/ezdemo/commit/1fde2c8) |
| 2026-10-01 | v6.0.7 | [`72eaecc`](https://github.com/se7enxweb/ezdemo/commit/72eaecc) |
| 2026-10-02 | v6.0.8 | [`dc2826d`](https://github.com/se7enxweb/ezdemo/commit/dc2826d) |
| 2026-10-02 | v6.0.9 | [`2a74806`](https://github.com/se7enxweb/ezdemo/commit/2a74806) |

## Timeline

### 2023-12

The month across all extensions: [December 2023](months/2023-12.md). [Ledger of this month](../ledger/ezdemo.md#2023-12-2-changes).

- 2023-12-22 [`e1f3faa`](https://github.com/se7enxweb/ezdemo/commit/e1f3faa) (tooling) Update composer.json switched package vendor
- 2023-12-24 [`f0911ba`](https://github.com/se7enxweb/ezdemo/commit/f0911ba) (no user benefit) Create FUNDING.yml

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/ezdemo.md#2024-01-2-changes).

- 2024-01-28 [`63203d2`](https://github.com/se7enxweb/ezdemo/commit/63203d2) (no user benefit) Updated github funding information **Release v6.0.0.**
- 2024-01-28 [`b30d24d`](https://github.com/se7enxweb/ezdemo/commit/b30d24d) (feature) Mass updates from parent repository ezdemo-ezpackage **Release v6.0.1.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/ezdemo.md#2026-03-1-changes).

- 2026-03-02 [`164cd38`](https://github.com/se7enxweb/ezdemo/commit/164cd38) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/ezdemo.md#2026-09-13-changes).

- 2026-09-27 [`890e6e3`](https://github.com/se7enxweb/ezdemo/commit/890e6e3) (feature) Visible texts and their translations name Exponential
- 2026-09-27 [`6a34a5b`](https://github.com/se7enxweb/ezdemo/commit/6a34a5b) (feature) Templates write <br> as HTML5 does
- 2026-09-27 [`36f0c83`](https://github.com/se7enxweb/ezdemo/commit/36f0c83) (feature) The extension names itself Exponential Demo Design LS
- 2026-09-27 [`8b38bc8`](https://github.com/se7enxweb/ezdemo/commit/8b38bc8) (feature) ezinfo.php reports the extension's name, version, copyright and license
- 2026-09-27 [`c0caec8`](https://github.com/se7enxweb/ezdemo/commit/c0caec8) (release) Version 6.0.2 **Release v6.0.2.**
- 2026-09-27 [`339b0fd`](https://github.com/se7enxweb/ezdemo/commit/339b0fd) (docs) extension.xml and ezinfo.php name the license in full, GNU General Public License v2.0 (or any later version)
- 2026-09-27 [`9b3554c`](https://github.com/se7enxweb/ezdemo/commit/9b3554c) (release) Version 6.0.3 **Release v6.0.3.**
- 2026-09-27 [`38d9303`](https://github.com/se7enxweb/ezdemo/commit/38d9303) (security) Fixed: The forgot password page no longer tells whether an address has an account and escapes what it prints
- 2026-09-27 [`037f5af`](https://github.com/se7enxweb/ezdemo/commit/037f5af) (release) Version 6.0.4 **Release v6.0.4.**
- 2026-09-27 [`c421667`](https://github.com/se7enxweb/ezdemo/commit/c421667) (fix) Fixed: Error pages show their own error in the page title, because the page layout keys its per-URI caches by error type and number as well
- 2026-09-27 [`e7f4c0c`](https://github.com/se7enxweb/ezdemo/commit/e7f4c0c) (release) Version 6.0.5 **Release v6.0.5.**
- 2026-09-29 [`0328e9b`](https://github.com/se7enxweb/ezdemo/commit/0328e9b) (feature) The German translation covers every interface text of the templates
- 2026-09-30 [`1fde2c8`](https://github.com/se7enxweb/ezdemo/commit/1fde2c8) (release) Version 6.0.6 **Release v6.0.6.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/ezdemo.md#2026-10-5-changes).

- 2026-10-01 [`08ebe51`](https://github.com/se7enxweb/ezdemo/commit/08ebe51) (upgrade note) Updated the edit page's collapsible attribute groups for jQuery 4, so that they open and close with .on() instead of the deprecated click shorthand.
- 2026-10-01 [`72eaecc`](https://github.com/se7enxweb/ezdemo/commit/72eaecc) (release) Version 6.0.7 **Release v6.0.7.**
- 2026-10-02 [`dc2826d`](https://github.com/se7enxweb/ezdemo/commit/dc2826d) (upgrade note) YUI from the ezdemo design; its galleries, flyouts and campaign block run on jQuery **Release v6.0.8.**
- 2026-10-02 [`84c3816`](https://github.com/se7enxweb/ezdemo/commit/84c3816) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`2a74806`](https://github.com/se7enxweb/ezdemo/commit/2a74806) (release) Version 6.0.9 **Release v6.0.9.**

## Related pages

- [Feature page](../../features/6.0/extensions/ezdemo.md)
- [Release notes](../../changelogs/extensions/ezdemo.md)
- [Change ledger](../ledger/ezdemo.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
