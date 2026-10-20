# ezjscore (JavaScript core and packer): chronicle

The standalone ezjscore repository had been empty since 2013. On 1 October 2026 it was brought back in step with the kernel copy (1.4.0 with jQuery 4), and on 2 October YUI was removed (1.5.0) and the sub items table options functions were added. See the [feature page](../../features/6.0/extensions/ezjscore.md).

This page lists **every one of the 15 changes** of the repository `ezjscore` between 2024-01-28 and 2026-10-02, by month, with what kind of change each is. Read it to find out when a behaviour arrived and which release you need for it. The complete machine-made record, with sizes, is the [change ledger](../ledger/ezjscore.md); what each release contains is in the [release notes](../../changelogs/extensions/ezjscore.md); how to use the extension is on its [feature page](../../features/6.0/extensions/ezjscore.md).

| Kind | Changes |
|---|---|
| feature | 3 |
| fix | 1 |
| upgrade note | 2 |
| docs | 1 |
| tooling | 1 |
| release | 5 |
| no user benefit | 2 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2026-10-01 | 1.4.0 | [`66d2310`](https://github.com/se7enxweb/ezjscore/commit/66d2310) |
| 2026-10-02 | 1.4.1 | [`5ee94f6`](https://github.com/se7enxweb/ezjscore/commit/5ee94f6) |
| 2026-10-02 | 1.5.0 | [`0544db8`](https://github.com/se7enxweb/ezjscore/commit/0544db8) |
| 2026-10-02 | 1.5.1 | [`eae7434`](https://github.com/se7enxweb/ezjscore/commit/eae7434) |
| 2026-10-02 | 1.5.2 | [`e2ea4db`](https://github.com/se7enxweb/ezjscore/commit/e2ea4db) |
| 2026-10-02 | 1.5.3 | [`9e8cfd0`](https://github.com/se7enxweb/ezjscore/commit/9e8cfd0) |
| 2026-10-02 | 1.5.4 | [`4eafcf0`](https://github.com/se7enxweb/ezjscore/commit/4eafcf0) |
| 2026-10-02 | 1.5.5 | [`1d48bd8`](https://github.com/se7enxweb/ezjscore/commit/1d48bd8) |

## Timeline

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/ezjscore.md#2024-01-1-changes).

- 2024-01-28 [`61006a2`](https://github.com/se7enxweb/ezjscore/commit/61006a2) (no user benefit) Added github funding information

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/ezjscore.md#2026-03-1-changes).

- 2026-03-02 [`031a161`](https://github.com/se7enxweb/ezjscore/commit/031a161) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/ezjscore.md#2026-10-13-changes).

- 2026-10-01 [`66d2310`](https://github.com/se7enxweb/ezjscore/commit/66d2310) (upgrade note) Updated the repository to ezjscore 1.4.0, the version Exponential 6 ships in its kernel, with jQuery 4.0.0, jQuery Migrate 4.0.2 and jQuery UI 1.14.2, so that ezjscore can again be installed and followed on its own. **Release 1.4.0.**
- 2026-10-02 [`33c9efb`](https://github.com/se7enxweb/ezjscore/commit/33c9efb) (fix) Fixed: The CSS packer keeps the space before a colon in selectors, where it is the descendant combinator
- 2026-10-02 [`5ee94f6`](https://github.com/se7enxweb/ezjscore/commit/5ee94f6) (release) Version 1.4.1 **Release 1.4.1.**
- 2026-10-02 [`0544db8`](https://github.com/se7enxweb/ezjscore/commit/0544db8) (upgrade note) YUI; ezjscore loads jQuery and jQuery UI only **Release 1.5.0.**
- 2026-10-02 [`8a19de6`](https://github.com/se7enxweb/ezjscore/commit/8a19de6) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`eae7434`](https://github.com/se7enxweb/ezjscore/commit/eae7434) (release) Version 1.5.1 **Release 1.5.1.**
- 2026-10-02 [`f24bcfb`](https://github.com/se7enxweb/ezjscore/commit/f24bcfb) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`e2ea4db`](https://github.com/se7enxweb/ezjscore/commit/e2ea4db) (release) Version 1.5.2 **Release 1.5.2.**
- 2026-10-02 [`9e8cfd0`](https://github.com/se7enxweb/ezjscore/commit/9e8cfd0) (feature) The expsubitems server functions that serve the admin subitems list's Table options **Release 1.5.3.**
- 2026-10-02 [`f68a5a6`](https://github.com/se7enxweb/ezjscore/commit/f68a5a6) (feature) Server function blocks in the settings for the subitems columns
- 2026-10-02 [`4eafcf0`](https://github.com/se7enxweb/ezjscore/commit/4eafcf0) (release) Version 1.5.4 **Release 1.5.4.**
- 2026-10-02 [`eb438e1`](https://github.com/se7enxweb/ezjscore/commit/eb438e1) (feature) The server router lets expservices classes answer in their own envelope, errors included
- 2026-10-02 [`1d48bd8`](https://github.com/se7enxweb/ezjscore/commit/1d48bd8) (release) Version 1.5.5 **Release 1.5.5.**

## Related pages

- [Feature page](../../features/6.0/extensions/ezjscore.md)
- [Release notes](../../changelogs/extensions/ezjscore.md)
- [Change ledger](../ledger/ezjscore.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
