# Change ledger: ezie

Every change made to `ezie` since the se7enxweb era began, oldest first: 41 changes touching 189 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 38 |
| Other | 2 |
| Removed | 1 |

## 2023-12 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2023-12-23 | `3fcc409` | Updated | Update composer.json switched package vendor | 1 | +1 / −1 |  |
| 2023-12-24 | `0ba80c8` | Other | Create FUNDING.yml | 1 | +3 / −0 |  |

## 2024-01 (4 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-28 | `b61bcd2` | Updated | Updated: Updated github funding information | 1 | +1 / −1 |  |
| 2024-01-28 | `5014666` | Updated | Update composer.json replaced vendor name and package version | 1 | +2 / −2 | v6.0.0 |
| 2024-01-28 | `cc2d7f5` | Updated | Update composer.json license value with valid text | 1 | +1 / −1 |  |
| 2024-01-29 | `7d035ee` | Updated | Update composer.json update homepage url | 1 | +1 / −1 | v6.0.1 |

## 2024-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-03-05 | `b4d4639` | Updated | Updated: Bugfix for missing closing tag on version | 1 | +1 / −1 | v6.0.2 |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `4aa1d94` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-09 (28 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-27 | `38a6cfa` | Updated | Updated: The image editor no longer requests jquery-migrate-1.1.1.min.js | 1 | +0 / −1 |  |
| 2026-09-27 | `e5fa60d` | Updated | Updated: The extension states its version, license and website | 1 | +3 / −3 | v6.0.3 |
| 2026-09-28 | `407daf8` | Updated | Updated: Every visible text of the extension is a translation string, with German | 3 | +197 / −3 |  |
| 2026-09-28 | `1ee24ac` | Updated | Updated: Version 6.0.4 | 1 | +1 / −1 | v6.0.4 |
| 2026-09-30 | `c5ce8e2` | Updated | Updated: The description calls the product Exponential | 1 | +1 / −1 |  |
| 2026-09-30 | `cbe88f2` | Updated | Updated: Version 6.0.5 | 1 | +1 / −1 | v6.0.5 |
| 2026-09-30 | `0e69bbf` | Updated | Fixed: The image editor no longer fails on every image when the site allows WebP output | 1 | +21 / −3 |  |
| 2026-09-30 | `d0757cd` | Updated | Fixed: The GD handler of the image editor runs on PHP 8.5 without deprecations | 1 | +16 / −10 |  |
| 2026-09-30 | `169df3d` | Updated | Fixed: A horizontal flip with ImageMagick flips only the selection | 1 | +19 / −2 |  |
| 2026-09-30 | `abe9847` | Updated | Fixed: The watermark tool only reads images from the watermark folder | 1 | +26 / −9 |  |
| 2026-09-30 | `4b2632e` | Updated | Fixed: The image editor checks who edits which image and answers errors as JSON | 14 | +378 / −309 |  |
| 2026-09-30 | `98dea8f` | Removed | Removed: The blur, levels and saturation views of the image editor, which never worked | 3 | +0 / −65 |  |
| 2026-09-30 | `d592cb7` | Updated | Fixed: Working folders the image editor left behind are removed | 1 | +14 / −0 |  |
| 2026-09-30 | `1a141f5` | Updated | Updated: The known issues describe WebP images, the draft check and the JSON errors | 1 | +20 / −0 |  |
| 2026-09-30 | `9e33a9a` | Updated | Fixed: The image editor loads the image again with jQuery 3 | 1 | +2 / −2 |  |
| 2026-09-30 | `34d8504` | Updated | Fixed: Selecting, cropping and placing a watermark work with jQuery 3 | 2 | +22 / −26 |  |
| 2026-09-30 | `4cbd5e6` | Updated | Fixed: The thumbnail box of the image editor can be detached and attached again | 1 | +2 / −2 |  |
| 2026-09-30 | `d85455a` | Updated | Fixed: The image editor opens above the admin columns and fits the window | 4 | +24 / −3 |  |
| 2026-09-30 | `810ad16` | Updated | Fixed: Long option panels of the image editor scroll instead of running out of the window | 1 | +2 / −0 |  |
| 2026-09-30 | `8e72ba7` | Updated | Fixed: The slider styles of the image editor no longer request missing images | 1 | +16 / −16 |  |
| 2026-09-30 | `1b9a8d8` | Updated | Fixed: The image editor's tool handlers are not added again each time it opens | 2 | +15 / −19 |  |
| 2026-09-30 | `69f1402` | Updated | Fixed: The selection options of the image editor work without throwing an error | 2 | +10 / −12 |  |
| 2026-09-30 | `2ea46af` | Updated | Updated: The image editor sends the form token as a header as well | 1 | +17 / −1 |  |
| 2026-09-30 | `f0c3872` | Updated | Fixed: A failed image editor action shows the server's reason and keeps the editor open | 2 | +37 / −14 |  |
| 2026-09-30 | `d9f20e8` | Updated | Fixed: Quitting the image editor asks a translated question, and only when there are changes | 13 | +63 / −7 |  |
| 2026-09-30 | `0b8eab0` | Updated | Fixed: After Save & Close the edit form shows the saved image wherever the attribute is | 1 | +11 / −8 |  |
| 2026-09-30 | `e828a1e` | Updated | Fixed: The image editor's error dialog grows with the server's message | 1 | +9 / −2 |  |
| 2026-09-30 | `c2f9606` | Updated | Updated: Version 6.0.6 | 2 | +35 / −1 | v6.0.6 |

## 2026-10 (5 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-01 | `6292484` | Updated | Updated: Updated the image editor for jQuery 4 and jQuery UI 1.14, so that opening it, its tools, undo, the selection tool and quitting without saving work without jQuery Migrate warnings. | 9 | +55 / −55 |  |
| 2026-10-01 | `e8db729` | Updated | Updated: Version 6.0.7 | 2 | +2 / −2 | v6.0.7 |
| 2026-10-02 | `5447227` | Updated | Updated: The command line scripts, cronjob parts and module views are classes the files call | 26 | +826 / −327 |  |
| 2026-10-02 | `ce35086` | Updated | Updated: The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices | 77 | +78 / −2 |  |
| 2026-10-02 | `0f918f9` | Updated | Updated: Version 6.0.8 | 2 | +2 / −2 | v6.0.8 origin/master origin/HEAD |
