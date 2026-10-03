# Change ledger: xrowmetadata

Every change made to `xrowmetadata` since the se7enxweb era began, oldest first: 24 changes touching 57 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 18 |
| Added | 4 |
| Other | 2 |

## 2023-12 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2023-12-24 | `8af4413` | Other | Create FUNDING.yml | 1 | +3 / −0 |  |

## 2024-01 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-28 | `c54b6bb` | Added | Added: Added github funding information | 1 | +1 / −1 |  |
| 2024-01-29 | `dc7ee86` | Updated | Update composer.json | 1 | +20 / −6 |  |
| 2024-01-29 | `828fcd5` | Updated | Update and rename README.txt to README.md | 1 | +13 / −10 | v1.3.5 |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `b80d85f` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-08 (7 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-08-06 | `4a26f81` | Added | Added: Open Graph image support to the xrowmetadata datatype\n\n- Extended xrowMetaData struct with og_image, width, height, alt and type.\n- xrowMetaDataType now persists og_image in data_text and falls back to class-level data_text5 default.\n- Added class and object edit templates for selecting the Open Graph image object ID. | 5 | +88 / −5 |  |
| 2026-08-06 | `fbe1865` | Updated | Updated: Object-relation style GUI for Open Graph image selection in xrowmetadata\n\n- xrowMetaDataType now supports custom HTTP actions for both object and class attributes.\n- Replaced numeric ID inputs with a browse/remove object relation interface for og_image.\n- Class-level default and per-object override both use data_int4 / data_text5 and data_int fallback. | 3 | +204 / −9 |  |
| 2026-08-06 | `737cb6f` | Updated | Updated: Show no-relation message when no Open Graph image is selected | 2 | +6 / −0 |  |
| 2026-08-06 | `23580ee` | Added | Added: class-level Open Graph image content for xrowmetadata class attributes | 2 | +47 / −36 |  |
| 2026-08-06 | `1493317` | Updated | Updated: Simplify class-level OG image display and avoid content fetches in class template | 1 | +1 / −3 |  |
| 2026-08-06 | `51ae4c6` | Updated | Updated: Open Graph image preview and class-level default fallback to v1.3.6 | 4 | +71 / −13 | v1.3.6 |
| 2026-08-07 | `603e137` | Updated | Updated: bump xrowmetadata version to 1.3.7 | 2 | +2 / −2 | v1.3.7 |

## 2026-09 (5 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-22 | `9e91560` | Updated | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 1 | +48 / −44 | v1.4.0 |
| 2026-09-27 | `3c0e57e` | Updated | Updated: The extension states its version, license and website | 2 | +6 / −5 | v1.4.1 |
| 2026-09-30 | `9154723` | Updated | Updated: The about page names the extension Xrow Meta Data | 2 | +2 / −2 |  |
| 2026-09-30 | `5c3bf97` | Updated | Updated: The description calls the product Exponential | 1 | +1 / −1 |  |
| 2026-09-30 | `7cd1a57` | Updated | Updated: Version 1.4.2 | 2 | +2 / −2 | v1.4.2 |

## 2026-10 (7 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-01 | `65de202` | Added | Added: An English translation catalogue, and German for the canonical link, Open Graph and page texts | 3 | +209 / −0 |  |
| 2026-10-01 | `d824416` | Updated | Updated: Updated the metadata field's "more" checkbox for jQuery 4, so that it uses .on() instead of the deprecated click shorthand. | 1 | +1 / −1 |  |
| 2026-10-01 | `dd5a18a` | Updated | Updated: Version 1.4.3 | 2 | +2 / −2 | v1.4.3 |
| 2026-10-02 | `86ff6bb` | Updated | Updated: The command line scripts, cronjob parts and module views are classes the files call | 12 | +443 / −287 |  |
| 2026-10-02 | `1e51a37` | Updated | Updated: The commands and cronjob parts list a description of what they do | 4 | +4 / −0 |  |
| 2026-10-02 | `d9bac19` | Updated | Updated: The sitemap cronjob prints the name of each file it wrote instead of failing on the file object, and the news sitemap stops quietly when the news subtree is empty | 1 | +6 / −1 |  |
| 2026-10-02 | `ec158f9` | Updated | Updated: Version 1.4.4 | 2 | +2 / −2 | v1.4.4 origin/master origin/HEAD |
