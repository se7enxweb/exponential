# ezie (image editor): chronicle

The image editor was untouched between a vendor switch in early 2024 and September 2026, when it was brought to PHP 8.5, WebP sites, jQuery 3 and 4 and a proper permission check. Releases 6.0.3 to 6.0.8. See the [feature page](../../features/6.0/extensions/ezie.md).

This page lists **every one of the 41 changes** of the repository `ezie` between 2023-12-23 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/ezie.md); what each release contains is in the [release notes](../../changelogs/extensions/ezie.md); how to use the extension is on its [feature page](../../features/6.0/extensions/ezie.md).

| Kind | Changes |
|---|---|
| feature | 2 |
| fix | 15 |
| security | 3 |
| upgrade note | 4 |
| docs | 4 |
| tooling | 5 |
| release | 5 |
| no user benefit | 3 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-01-28 | v6.0.0 | [`5014666`](https://github.com/se7enxweb/ezie/commit/5014666) |
| 2024-01-29 | v6.0.1 | [`7d035ee`](https://github.com/se7enxweb/ezie/commit/7d035ee) |
| 2024-03-05 | v6.0.2 | [`b4d4639`](https://github.com/se7enxweb/ezie/commit/b4d4639) |
| 2026-09-27 | v6.0.3 | [`e5fa60d`](https://github.com/se7enxweb/ezie/commit/e5fa60d) |
| 2026-09-28 | v6.0.4 | [`1ee24ac`](https://github.com/se7enxweb/ezie/commit/1ee24ac) |
| 2026-09-30 | v6.0.5 | [`cbe88f2`](https://github.com/se7enxweb/ezie/commit/cbe88f2) |
| 2026-09-30 | v6.0.6 | [`c2f9606`](https://github.com/se7enxweb/ezie/commit/c2f9606) |
| 2026-10-01 | v6.0.7 | [`e8db729`](https://github.com/se7enxweb/ezie/commit/e8db729) |
| 2026-10-02 | v6.0.8 | [`0f918f9`](https://github.com/se7enxweb/ezie/commit/0f918f9) |

## Timeline

### 2023-12

The month across all extensions: [December 2023](months/2023-12.md). [Ledger of this month](../ledger/ezie.md#2023-12-2-changes).

- 2023-12-23 [`3fcc409`](https://github.com/se7enxweb/ezie/commit/3fcc409) (tooling) Update composer.json switched package vendor
- 2023-12-24 [`0ba80c8`](https://github.com/se7enxweb/ezie/commit/0ba80c8) (no user benefit) Create FUNDING.yml

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/ezie.md#2024-01-4-changes).

- 2024-01-28 [`b61bcd2`](https://github.com/se7enxweb/ezie/commit/b61bcd2) (no user benefit) Updated github funding information
- 2024-01-28 [`5014666`](https://github.com/se7enxweb/ezie/commit/5014666) (tooling) Update composer.json replaced vendor name and package version **Release v6.0.0.**
- 2024-01-28 [`cc2d7f5`](https://github.com/se7enxweb/ezie/commit/cc2d7f5) (tooling) Update composer.json license value with valid text
- 2024-01-29 [`7d035ee`](https://github.com/se7enxweb/ezie/commit/7d035ee) (tooling) Update composer.json update homepage url **Release v6.0.1.**

### 2024-03

The month across all extensions: [March 2024](months/2024-03.md). [Ledger of this month](../ledger/ezie.md#2024-03-1-changes).

- 2024-03-05 [`b4d4639`](https://github.com/se7enxweb/ezie/commit/b4d4639) (fix) Bugfix for missing closing tag on version **Release v6.0.2.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/ezie.md#2026-03-1-changes).

- 2026-03-02 [`4aa1d94`](https://github.com/se7enxweb/ezie/commit/4aa1d94) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/ezie.md#2026-09-28-changes).

- 2026-09-27 [`38a6cfa`](https://github.com/se7enxweb/ezie/commit/38a6cfa) (feature) The image editor no longer requests jquery-migrate-1.1.1.min.js
- 2026-09-27 [`e5fa60d`](https://github.com/se7enxweb/ezie/commit/e5fa60d) (docs) The extension states its version, license and website **Release v6.0.3.**
- 2026-09-28 [`407daf8`](https://github.com/se7enxweb/ezie/commit/407daf8) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`1ee24ac`](https://github.com/se7enxweb/ezie/commit/1ee24ac) (release) Version 6.0.4 **Release v6.0.4.**
- 2026-09-30 [`c5ce8e2`](https://github.com/se7enxweb/ezie/commit/c5ce8e2) (docs) The description calls the product Exponential
- 2026-09-30 [`cbe88f2`](https://github.com/se7enxweb/ezie/commit/cbe88f2) (release) Version 6.0.5 **Release v6.0.5.**
- 2026-09-30 [`0e69bbf`](https://github.com/se7enxweb/ezie/commit/0e69bbf) (fix) Fixed: The image editor no longer fails on every image when the site allows WebP output
- 2026-09-30 [`d0757cd`](https://github.com/se7enxweb/ezie/commit/d0757cd) (fix) Fixed: The GD handler of the image editor runs on PHP 8.5 without deprecations
- 2026-09-30 [`169df3d`](https://github.com/se7enxweb/ezie/commit/169df3d) (fix) Fixed: A horizontal flip with ImageMagick flips only the selection
- 2026-09-30 [`abe9847`](https://github.com/se7enxweb/ezie/commit/abe9847) (security) Fixed: The watermark tool only reads images from the watermark folder
- 2026-09-30 [`4b2632e`](https://github.com/se7enxweb/ezie/commit/4b2632e) (security) Fixed: The image editor checks who edits which image and answers errors as JSON
- 2026-09-30 [`98dea8f`](https://github.com/se7enxweb/ezie/commit/98dea8f) (upgrade note) The blur, levels and saturation views of the image editor, which never worked
- 2026-09-30 [`d592cb7`](https://github.com/se7enxweb/ezie/commit/d592cb7) (fix) Fixed: Working folders the image editor left behind are removed
- 2026-09-30 [`1a141f5`](https://github.com/se7enxweb/ezie/commit/1a141f5) (docs) The known issues describe WebP images, the draft check and the JSON errors
- 2026-09-30 [`9e33a9a`](https://github.com/se7enxweb/ezie/commit/9e33a9a) (upgrade note) Fixed: The image editor loads the image again with jQuery 3
- 2026-09-30 [`34d8504`](https://github.com/se7enxweb/ezie/commit/34d8504) (upgrade note) Fixed: Selecting, cropping and placing a watermark work with jQuery 3
- 2026-09-30 [`4cbd5e6`](https://github.com/se7enxweb/ezie/commit/4cbd5e6) (fix) Fixed: The thumbnail box of the image editor can be detached and attached again
- 2026-09-30 [`d85455a`](https://github.com/se7enxweb/ezie/commit/d85455a) (fix) Fixed: The image editor opens above the admin columns and fits the window
- 2026-09-30 [`810ad16`](https://github.com/se7enxweb/ezie/commit/810ad16) (fix) Fixed: Long option panels of the image editor scroll instead of running out of the window
- 2026-09-30 [`8e72ba7`](https://github.com/se7enxweb/ezie/commit/8e72ba7) (fix) Fixed: The slider styles of the image editor no longer request missing images
- 2026-09-30 [`1b9a8d8`](https://github.com/se7enxweb/ezie/commit/1b9a8d8) (fix) Fixed: The image editor's tool handlers are not added again each time it opens
- 2026-09-30 [`69f1402`](https://github.com/se7enxweb/ezie/commit/69f1402) (fix) Fixed: The selection options of the image editor work without throwing an error
- 2026-09-30 [`2ea46af`](https://github.com/se7enxweb/ezie/commit/2ea46af) (security) The image editor sends the form token as a header as well
- 2026-09-30 [`f0c3872`](https://github.com/se7enxweb/ezie/commit/f0c3872) (fix) Fixed: A failed image editor action shows the server's reason and keeps the editor open
- 2026-09-30 [`d9f20e8`](https://github.com/se7enxweb/ezie/commit/d9f20e8) (fix) Fixed: Quitting the image editor asks a translated question, and only when there are changes
- 2026-09-30 [`0b8eab0`](https://github.com/se7enxweb/ezie/commit/0b8eab0) (fix) Fixed: After Save & Close the edit form shows the saved image wherever the attribute is
- 2026-09-30 [`e828a1e`](https://github.com/se7enxweb/ezie/commit/e828a1e) (fix) Fixed: The image editor's error dialog grows with the server's message
- 2026-09-30 [`c2f9606`](https://github.com/se7enxweb/ezie/commit/c2f9606) (release) Version 6.0.6 **Release v6.0.6.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/ezie.md#2026-10-5-changes).

- 2026-10-01 [`6292484`](https://github.com/se7enxweb/ezie/commit/6292484) (upgrade note) Updated the image editor for jQuery 4 and jQuery UI 1.14, so that opening it, its tools, undo, the selection tool and quitting without saving work without jQuery Migrate warnings.
- 2026-10-01 [`e8db729`](https://github.com/se7enxweb/ezie/commit/e8db729) (release) Version 6.0.7 **Release v6.0.7.**
- 2026-10-02 [`5447227`](https://github.com/se7enxweb/ezie/commit/5447227) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`ce35086`](https://github.com/se7enxweb/ezie/commit/ce35086) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`0f918f9`](https://github.com/se7enxweb/ezie/commit/0f918f9) (release) Version 6.0.8 **Release v6.0.8.**

## Related

* [Feature page](../../features/6.0/extensions/ezie.md)
* [Release notes](../../changelogs/extensions/ezie.md)
* [Change ledger](../ledger/ezie.md)
