# Change ledger: eztags

Every change made to `eztags` since the se7enxweb era began, oldest first: 37 changes touching 174 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 32 |
| Other | 2 |
| Added | 2 |
| Removed | 1 |

## 2023-12 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2023-12-23 | `01a07a0` | Updated | Update composer.json changed package vendor | 1 | +1 / −1 |  |
| 2023-12-24 | `8a094be` | Other | Create FUNDING.yml | 1 | +3 / −0 |  |

## 2024-01 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-28 | `38f2f9c` | Updated | Updated: Updated github funding information | 1 | +1 / −1 |  |
| 2024-01-29 | `04315e7` | Updated | Update composer.json switched package name | 1 | +11 / −4 |  |
| 2024-01-29 | `2830721` | Updated | Update composer.json switched vendor name | 1 | +1 / −1 | v2.3.1 |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `e7403b4` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-04 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-22 | `65dc524` | Updated | fix: replace MOD() with % operator and use positional ORDER BY for SQLite compatibility | 2 | +3 / −3 | v2.3.2 |

## 2026-08 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-08-07 | `92ab004` | Added | Added: SQLite schema for eztags | 1 | +41 / −0 | v2.3.3 |

## 2026-09 (15 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-19 | `80c42c1` | Updated | Fixed: Fixed tags rendering as nothing on MongoDB installations, caused by joins with eztags_keyword the MongoDB driver will not translate, so tagged content lists its tags again and each tag links to its own page. | 2 | +315 / −0 | v2.3.4 |
| 2026-09-20 | `10872e9` | Updated | Updated: Updated the top menu entry from "eZ Tags" to "Tags", a shorter name that does not carry the old product's branding now that the extension ships as part of Exponential 6. | 1 | +1 / −1 | v2.3.5 |
| 2026-09-22 | `75ab1a1` | Updated | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 1 | +4 / −0 | v2.4.0 |
| 2026-09-27 | `1f1833b` | Updated | Updated: The admin tab, its title and the top menu tooltip call the extension Tags | 3 | +5 / −5 |  |
| 2026-09-27 | `b7a2860` | Updated | Updated: The extension states its version, license and website | 1 | +3 / −3 | v2.4.1 |
| 2026-09-28 | `45a97a7` | Updated | Updated: Every visible text of the extension is a translation string, with German | 7 | +920 / −8 |  |
| 2026-09-28 | `8b91c09` | Updated | Updated: Version 2.4.2 | 1 | +1 / −1 | v2.4.2 |
| 2026-09-29 | `9673498` | Updated | Updated: The top menu tab and its tooltip have German translations | 3 | +54 / −0 |  |
| 2026-09-29 | `9c0b533` | Updated | Updated: Version 2.4.3 | 1 | +1 / −1 | v2.4.3 |
| 2026-09-30 | `9f61ddd` | Updated | Updated: The description calls the product Exponential | 1 | +1 / −1 |  |
| 2026-09-30 | `ac660d6` | Updated | Updated: Version 2.4.4 | 1 | +1 / −1 | v2.4.4 |
| 2026-09-29 | `ec57870` | Updated | Fixed: Tag lookups by main translation work on every database, Oracle included | 2 | +3 / −2 |  |
| 2026-09-30 | `5c9efce` | Updated | Fixed: The tag tree filter and the tag search by subtree work on every database | 2 | +3 / −3 |  |
| 2026-09-30 | `4afd43a` | Updated | Updated: Version 2.4.5 | 1 | +1 / −1 | v2.4.5 |
| 2026-09-30 | `02672e3` | Added | Added: ezinfo.php, with Version 2.4.6 | 2 | +23 / −1 | v2.4.6 |

## 2026-10 (14 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-01 | `d3dc766` | Updated | Updated: Updated eztags for jQuery 4, with jsTree 3.3.17, so that the tag tree, the tag fields and the admin tag pages work on jQuery 4 without jQuery Migrate warnings. | 7 | +36 / −38 |  |
| 2026-10-01 | `3e200a1` | Updated | Updated: Version 2.4.7 | 2 | +2 / −2 | v2.4.7 |
| 2026-10-01 | `6059fa5` | Updated | Updated: The children table of a tag runs on Exponential UI's exp::datatable when expui is active, with $.fn.eZTagsChildrenExp, and keeps its YUI 2 DataTable as the fallback, so that the tags admin needs no YUI for it. | 2 | +166 / −2 |  |
| 2026-10-01 | `56e2988` | Updated | Fixed: Fixed the tags field's edit page in designs that do not load eztags' FrontendJavaScriptList, where it stopped with TagsStructureMenu and $.EzTags not defined, by having the field's template require its own scripts and styles, so that tags can be edited on every site design. | 1 | +5 / −0 |  |
| 2026-10-01 | `ad9e989` | Updated | Updated: Requires se7enxweb/expui ^1.0.0.1, the Exponential UI modules its templates use when they are active, so that installing it brings the jQuery 4 versions of its features; the YUI versions stay as the fallback. | 1 | +2 / −1 |  |
| 2026-10-01 | `f218ff7` | Updated | Fixed: Fixed the tags administration in the admin design, where the dashboard, tag pages, forms, left menu and node tab were blank because eZ Tags ships its admin templates in design/admin2 only, so that it works in every admin design without a design setting. | 38 | +79 / −8 |  |
| 2026-10-01 | `a275e6d` | Updated | Updated: Version 2.4.8 | 2 | +2 / −2 | v2.4.8 |
| 2026-10-02 | `ca15834` | Removed | Removed: YUI from eZ Tags; the children list of a tag runs on Exponential UI alone | 10 | +16 / −449 | v2.4.9 |
| 2026-10-02 | `f2e8f1a` | Updated | Updated: The command line scripts, cronjob parts and module views are classes the files call | 36 | +2111 / −1628 |  |
| 2026-10-02 | `9a2af2d` | Updated | Updated: The entry point files carry a header of 7x and the Exponential Foundation; the original headers move to the classes | 30 | +180 / −15 |  |
| 2026-10-02 | `bbc419d` | Updated | Updated: The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices | 2 | +3 / −2 |  |
| 2026-10-02 | `4b3fc16` | Updated | Updated: Version 2.4.10 | 2 | +2 / −2 | v2.4.10 |
| 2026-10-02 | `a3bb010` | Updated | Updated: The commands start through the shared command helpers | 1 | +3 / −6 |  |
| 2026-10-02 | `0ac2999` | Updated | Updated: Version 2.4.11 | 2 | +2 / −2 | v2.4.11 origin/master origin/HEAD |
