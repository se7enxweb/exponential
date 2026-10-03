# Change ledger: ezplatform-kernel

Every change made to `ezplatform-kernel` since the se7enxweb era began, oldest first: 38 changes touching 106 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Other | 18 |
| Updated | 11 |
| Merged | 4 |
| Added | 4 |
| Removed | 1 |

## 2023-12 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2023-12-22 | `4abba9267` | Other | IBX-6880: Skipped normalizing directories in the `normalizePath` method | 1 | +3 / −2 |  |
| 2023-12-27 | `29489e4fc` | Other | IBX-7346: Reindexed reverse-related content after deleting source content (#396) | 6 | +174 / −62 |  |

## 2024-01 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-31 | `6e5fa6ff0` | Other | IBX-7485: Skipped files with corrupted filenames when loading and deleting content | 3 | +114 / −3 |  |

## 2024-02 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-02-19 | `aa31bb228` | Other | IBX-7172: Fixed Repository Filtering by multiple ObjectStateId criteria | 2 | +78 / −4 |  |

## 2024-03 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-03-08 | `8ef0e70c4` | Other | IBX-7809: Fixed creating `UserMetadata` criterion from `UserGroupLimitationType` | 2 | +103 / −4 |  |
| 2024-03-20 | `7e472317f` | Merged | Merge pull request from GHSA-mwvh-p3hx-x4gg | 12 | +134 / −34 |  |

## 2024-04 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-04-17 | `81464c68c` | Updated | Fixed missing return types for DebugTemplate class | 1 | +8 / −16 |  |
| 2024-04-24 | `245d02e15` | Other | IBX-6592: Removed unusable location/subtree limitations from `state/assign` policy | 1 | +1 / −1 |  |

## 2024-05 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-05-07 | `b5fe9ad82` | Other | IBX-6494: Fixed copying of non-translatable fields to later versions | 2 | +122 / −21 |  |
| 2024-05-10 | `98b7b50e6` | Other | IBX-5388: Fixed performance issues of content updates after field changes | 26 | +1164 / −241 |  |

## 2024-06 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-06-07 | `c88c39759` | Other | IBX-8019: Added performance consideration notice to `LocationService::loadLocationChildren` (#407) | 1 | +4 / −0 |  |
| 2024-06-24 | `17e78be8a` | Other | IBX-6833: Fixed copying empty fields from a published version | 2 | +122 / −3 |  |

## 2024-08 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-08-07 | `9a6241085` | Other | IBX-8562: Fixed flooding content attributes table with duplicates | 6 | +87 / −25 |  |

## 2024-10 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-10-22 | `b19be8dbe` | Other | IBX-8562: Command to remove duplicated entries after faulty IBX-5388 fix | 3 | +268 / −2 |  |

## 2025-02 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-02-11 | `553e0dd0c` | Other | IBX-9455: Upgraded Twig to ^3.19.0 (#411) | 1 | +1 / −1 |  |

## 2025-09 (11 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-09-27 | `a613f1c18` | Updated | Change 'eZ Platform' to 'Exponential Platform'. Rebranding | 1 | +1 / −1 |  |
| 2025-09-27 | `57d8a9174` | Other | Configure funding sources in FUNDING.yml | 1 | +3 / −0 |  |
| 2025-09-27 | `f54fd6f66` | Updated | Update package details for se7enxweb integration | 1 | +6 / −6 |  |
| 2025-09-27 | `9621fd420` | Updated | Update PHP version requirements in composer.json | 1 | +1 / −1 |  |
| 2025-09-27 | `36a12daf1` | Other | Replace 'Ibexa Platform' with 'Exponential Platform'. Rebranding. | 1 | +5 / −5 |  |
| 2025-09-27 | `a7016bf01` | Other | Replace 'Ibexa Platform' with 'Exponential Platform'. Rebranding. | 1 | +5 / −5 |  |
| 2025-09-27 | `c61b18361` | Other | Correct spelling of 'ibexa' to 'exponential' in SQL. Rebranding. | 1 | +2 / −2 | v1.3.40 |
| 2025-09-28 | `9ab6f7357` | Added | Add missing configuration parsers for Content and User Settings views | 4 | +52 / −0 |  |
| 2025-09-28 | `8c070ac8f` | Merged | Merge pull request #5 from se7enxweb/fix/missing-configuration-parsers | 0 | +0 / −0 | v1.3.41 |
| 2025-09-28 | `933f42eaf` | Updated | Fix vendor paths: Replace ezsystems with se7enxweb paths | 2 | +6 / −6 |  |
| 2025-09-28 | `43712cafc` | Merged | Merge pull request #7 from se7enxweb/fix/update-vendor-paths-from-ezsystems-to-se7enxweb | 0 | +0 / −0 | v1.3.42 |

## 2026-03 (5 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `081567820` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |
| 2026-03-26 | `021397cff` | Added | Add replace for ezsystems/ezplatform-kernel + ibexa/core, add PHP 8.5 support | 1 | +4 / −2 | v1.3.43 origin/1.3 |
| 2026-03-26 | `9b341b163` | Added | Add branch-alias for 1.3-se7enx | 1 | +2 / −1 |  |
| 2026-03-26 | `7c7b12577` | Removed | Remove se7enx branch-alias: prevent auto-resolution by external projects | 1 | +1 / −2 |  |
| 2026-03-29 | `efaf3e785` | Updated | fix: append 73 legacy Exponential schema tables to ibexa-oss cleandata.sql | 1 | +815 / −0 | v1.3.44 |

## 2026-04 (7 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-07 | `4b90bbf4d` | Added | feat: rename commands to exponential:* prefix, keep ibexa:* and ezplatform:* as deprecated aliases | 14 | +40 / −25 |  |
| 2026-04-07 | `f1970779c` | Updated | fix: unescape backslash-escaped quotes and newlines in SQLite cleandata.sql | 1 | +1174 / −0 |  |
| 2026-04-07 | `eac6c8b77` | Updated | fix: remove autoincrement from ezcontentobject_attribute.id for SQLite composite PK compatibility | 1 | +1 / −1 |  |
| 2026-04-11 | `b32c2d6ae` | Updated | fix: skip doctrine:database:create for SQLite in checkCreateDatabase() | 1 | +15 / −0 |  |
| 2026-04-11 | `1e96f2345` | Merged | Merge pull request #10 from se7enxweb/1.3-se7enx | 0 | +0 / −0 |  |
| 2026-04-11 | `a5a32b66f` | Updated | fix(sqlite): recreate composite-PK tables after Doctrine schema generation | 1 | +96 / −0 |  |
| 2026-04-12 | `77524ca4e` | Updated | fix(deps): switch ezsystems/doctrine-dbal-schema to se7enxweb/doctrine-dbal-schema | 1 | +1 / −1 | v1.3.45 origin/HEAD origin/1.3.x |
