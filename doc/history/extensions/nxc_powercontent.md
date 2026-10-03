# nxc_powercontent (content from code and REST): chronicle

Powercontent was extended for the REST extension in October 2024, fixed for PHP 8.5 in December 2025, gained copy and hide actions in June 2026 and a class list fetch in July 2026, and a PDF export fix in October 2026. See the [feature page](../../features/6.0/extensions/nxc_powercontent.md).

This page lists **every one of the 12 changes** of the repository `nxc_powercontent` between 2024-10-29 and 2026-10-02, by month, with what kind of change each is. Read it to find out when a behaviour arrived and which release you need for it. The complete machine-made record, with sizes, is the [change ledger](../ledger/nxc_powercontent.md); what each release contains is in the [release notes](../../changelogs/extensions/nxc_powercontent.md); how to use the extension is on its [feature page](../../features/6.0/extensions/nxc_powercontent.md).

| Kind | Changes |
|---|---|
| feature | 5 |
| fix | 2 |
| docs | 1 |
| release | 3 |
| no user benefit | 1 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-10-29 | v1.1.0 | [`58c1fc4`](https://github.com/se7enxweb/nxc_powercontent/commit/58c1fc4) |
| 2026-06-21 | v1.2.0 | [`85f728e`](https://github.com/se7enxweb/nxc_powercontent/commit/85f728e) |
| 2026-07-12 | v1.3.0 | [`5bbe3d2`](https://github.com/se7enxweb/nxc_powercontent/commit/5bbe3d2) |
| 2026-09-22 | v1.4.0 | [`3d0e28a`](https://github.com/se7enxweb/nxc_powercontent/commit/3d0e28a) |
| 2026-10-02 | v1.4.1 | [`c43b6af`](https://github.com/se7enxweb/nxc_powercontent/commit/c43b6af) |
| 2026-10-02 | v1.4.2 | [`739ebfd`](https://github.com/se7enxweb/nxc_powercontent/commit/739ebfd) |
| 2026-10-02 | v1.4.3 | [`038c6e9`](https://github.com/se7enxweb/nxc_powercontent/commit/038c6e9) |

## Timeline

### 2024-10

The month across all extensions: [October 2024](months/2024-10.md). [Ledger of this month](../ledger/nxc_powercontent.md#2024-10-1-changes).

- 2024-10-29 [`58c1fc4`](https://github.com/se7enxweb/nxc_powercontent/commit/58c1fc4) (feature) Extend and refactor solution for use within a rest api extension ezprestapi for v2 legacy rest crud calls functionality **Release v1.1.0.**

### 2025-12

The month across all extensions: [December 2025](months/2025-12.md). [Ledger of this month](../ledger/nxc_powercontent.md#2025-12-1-changes).

- 2025-12-29 [`e554e30`](https://github.com/se7enxweb/nxc_powercontent/commit/e554e30) (fix) Updated class method to provide nullable type bugfix required for php 8.5 support. Enhancement

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/nxc_powercontent.md#2026-03-1-changes).

- 2026-03-02 [`976044c`](https://github.com/se7enxweb/nxc_powercontent/commit/976044c) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-06

The month across all extensions: [June 2026](months/2026-06.md). [Ledger of this month](../ledger/nxc_powercontent.md#2026-06-1-changes).

- 2026-06-21 [`85f728e`](https://github.com/se7enxweb/nxc_powercontent/commit/85f728e) (feature) Add Copy and Hide/Unhide action handlers to content/action.php **Release v1.2.0.**

### 2026-07

The month across all extensions: [July 2026](months/2026-07.md). [Ledger of this month](../ledger/nxc_powercontent.md#2026-07-1-changes).

- 2026-07-12 [`5bbe3d2`](https://github.com/se7enxweb/nxc_powercontent/commit/5bbe3d2) (feature) feat(nxc_powercontent): add class_list fetch function and update related methods for broader compatibility **Release v1.3.0.**

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/nxc_powercontent.md#2026-09-1-changes).

- 2026-09-22 [`3d0e28a`](https://github.com/se7enxweb/nxc_powercontent/commit/3d0e28a) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.4.0.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/nxc_powercontent.md#2026-10-6-changes).

- 2026-10-02 [`d55466a`](https://github.com/se7enxweb/nxc_powercontent/commit/d55466a) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`c43b6af`](https://github.com/se7enxweb/nxc_powercontent/commit/c43b6af) (release) Version 1.4.1 **Release v1.4.1.**
- 2026-10-02 [`dbb4c21`](https://github.com/se7enxweb/nxc_powercontent/commit/dbb4c21) (feature) The PDF export no longer stops with a fatal error: the content cache info is read from an object instance, as PHP 8 requires
- 2026-10-02 [`739ebfd`](https://github.com/se7enxweb/nxc_powercontent/commit/739ebfd) (release) Version 1.4.2 **Release v1.4.2.**
- 2026-10-02 [`23a8bc3`](https://github.com/se7enxweb/nxc_powercontent/commit/23a8bc3) (feature) content/edit declares its redirect helper only once, so a persistent PHP worker can run it more than once
- 2026-10-02 [`038c6e9`](https://github.com/se7enxweb/nxc_powercontent/commit/038c6e9) (release) Version 1.4.3 **Release v1.4.3.**

## Related pages

- [Feature page](../../features/6.0/extensions/nxc_powercontent.md)
- [Release notes](../../changelogs/extensions/nxc_powercontent.md)
- [Change ledger](../ledger/nxc_powercontent.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
