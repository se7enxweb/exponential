# Change ledger: core

Every change made to `core` since the se7enxweb era began, oldest first: 655 changes touching 9672 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Other | 471 |
| Merged | 117 |
| Updated | 27 |
| Removed | 20 |
| Added | 19 |
| Released | 1 |

## 2023-12 (18 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2023-12-07 | `e8a5aac3e9` | Other | IBX-5827: Adapted code to Ibexa coding standards | 2 | +2 / −3 |  |
| 2023-12-11 | `488c86d6d5` | Other | Coding Standards: Update PHPStan baseline | 1 | +5 / −5 |  |
| 2023-12-11 | `31b544dc30` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.5 (#306) | 0 | +0 / −0 |  |
| 2023-12-11 | `51d0515459` | Merged | Merge remote-tracking branch 'origin/4.5' | 0 | +0 / −0 |  |
| 2023-12-12 | `9111b08736` | Other | IBX-6827: Aggregation API improvements  (#287) | 18 | +909 / −1 |  |
| 2023-12-13 | `ce348cb32d` | Other | IBX-7276: Renamed ibexa_get_current_user Twig function to ibexa_current_user (#307) | 3 | +9 / −9 |  |
| 2023-12-13 | `8ee2fb7148` | Other | [PHPStan] Updated PHPStan baseline (#310) | 1 | +0 / −15 |  |
| 2023-12-13 | `50b643f214` | Other | Merged branch '4.5' | 0 | +0 / −0 |  |
| 2023-12-13 | `3827b5c622` | Added | Added Ibexa\Core\FieldType\User\Type::FIELD_TYPE_IDENTIFIER const (#309) | 1 | +3 / −1 |  |
| 2023-12-14 | `3fd56c68cc` | Other | IBX-6856: Added mime types limitation for ezimage field type (#300) | 15 | +334 / −144 |  |
| 2023-12-18 | `ee3aaf7a29` | Other | IBX-7318: Set image field from Image CT to is_searchable set to true (#305) | 2 | +2 / −2 |  |
| 2023-12-22 | `4abba9267d` | Other | IBX-6880: Skipped normalizing directories in the `normalizePath` method | 1 | +3 / −2 |  |
| 2023-12-22 | `843d70977e` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.5 | 0 | +0 / −0 |  |
| 2023-12-22 | `182651e728` | Other | Merged branch '4.5' | 0 | +0 / −0 |  |
| 2023-12-27 | `29489e4fc0` | Other | IBX-7346: Reindexed reverse-related content after deleting source content (#396) | 6 | +174 / −62 |  |
| 2023-12-27 | `8c218824d5` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.5 | 0 | +0 / −0 |  |
| 2023-12-27 | `67811a0ac7` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.5 (#315) | 0 | +0 / −0 |  |
| 2023-12-27 | `1f34f08d82` | Merged | Merge branch '4.5' | 0 | +0 / −0 |  |

## 2024-01 (16 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-05 | `fb6e4500ae` | Other | IBX-6937: Changed expected min and max value types to numeric instead of int (#308) | 7 | +87 / −87 |  |
| 2024-01-08 | `b65033375b` | Other | [PHPStan] Fixed lambda return type in IbexaCoreExtension (#317) | 1 | +1 / −1 |  |
| 2024-01-08 | `aa17da9d9c` | Merged | Merge remote-tracking branch 'origin/4.5' | 0 | +0 / −0 |  |
| 2024-01-09 | `a94014f909` | Other | IBX-7337: Added twig functions to get user preference (#313) | 6 | +195 / −0 |  |
| 2024-01-10 | `025412e6cd` | Updated | Fixed list of excluded dirs for JMS translation extraction (#318) | 2 | +6 / −1 |  |
| 2024-01-10 | `c249ab4629` | Other | Merged branch '4.5' | 0 | +0 / −0 |  |
| 2024-01-16 | `ed512f12b8` | Other | IBX-7502: Added file size validation for image asset field type (#320) | 4 | +126 / −62 |  |
| 2024-01-17 | `273aa8c61c` | Other | IBX-7418: Added ContentName Criterion (#312) | 4 | +433 / −0 |  |
| 2024-01-19 | `a7ebd0d357` | Other | Unified Image Criteria argument names (#321) | 4 | +38 / −44 |  |
| 2024-01-22 | `2ac0538e7d` | Other | IBX-7409: Changed Content Type to content type (#316) | 68 | +209 / −209 |  |
| 2024-01-24 | `dcb200bd7e` | Other | [PHPStan] Regenerated baseline after PHPStan release (#324) | 2 | +5 / −25 |  |
| 2024-01-24 | `46e3884c22` | Other | Merged branch '4.5' | 0 | +0 / −0 |  |
| 2024-01-30 | `9150cc67ab` | Other | IBX-5821: Fixed an issue where incomplete request object was passed over to route Matcher (#319) | 3 | +62 / −57 |  |
| 2024-01-30 | `e32cd2990c` | Merged | Merge branch '4.5' | 0 | +0 / −0 |  |
| 2024-01-31 | `6e5fa6ff09` | Other | IBX-7485: Skipped files with corrupted filenames when loading and deleting content | 3 | +114 / −3 |  |
| 2024-01-31 | `408552ff74` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.5 | 0 | +0 / −0 |  |

## 2024-02 (25 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-02-01 | `11c8c52043` | Other | IBX-7278: Set proper route defaults values (#327) | 1 | +2 / −0 |  |
| 2024-02-01 | `087ad78ebe` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.5 (#326) | 0 | +0 / −0 |  |
| 2024-02-01 | `7f24942e32` | Other | Merged branch '4.5' | 0 | +0 / −0 |  |
| 2024-02-05 | `2c8459dfa4` | Other | IBX-6906: [DX] Introduced identifier-based view matchers (#322) | 11 | +191 / −151 |  |
| 2024-02-05 | `12b3b45a4c` | Other | Specified return type for PermissionResolver | 2 | +7 / −40 |  |
| 2024-02-06 | `855d3fde49` | Other | IBX-7689: Modified code to generate hyperlinks only for non-drafts (#329) | 1 | +3 / −1 |  |
| 2024-02-06 | `f8cfd72a98` | Merged | Merge branch '4.5' | 0 | +0 / −0 |  |
| 2024-02-08 | `565bb779b9` | Other | [CI] Improved publishing of Solr Docker image for tests (#304) | 2 | +74 / −26 |  |
| 2024-02-09 | `a06df7b8b8` | Other | IBX-7744: Updated Ibexa logo (#332) | 1 | +6 / −10 |  |
| 2024-02-12 | `8f016488a3` | Other | IBX-7364: Ensured independent property assignment in setPreviewActive (#323) | 2 | +6 / −0 |  |
| 2024-02-13 | `bc88c573e5` | Other | Made focus mode disabled for clean installation admin user (#334) | 2 | +6 / −0 |  |
| 2024-02-15 | `9e31c3a25a` | Updated | Fixed typo in ibexa:install option description (#335) | 1 | +1 / −1 |  |
| 2024-02-19 | `aa31bb2285` | Other | IBX-7172: Fixed Repository Filtering by multiple ObjectStateId criteria | 2 | +78 / −4 |  |
| 2024-02-19 | `4e5b883570` | Other | IBX-7636: Prevented caching content preview response (#331) | 1 | +1 / −0 |  |
| 2024-02-19 | `239fae7fee` | Merged | Merge remote-tracking branch 'origin/4.5' | 0 | +0 / −0 |  |
| 2024-02-19 | `58ffa8ca91` | Other | [Composer] Dropped obsolete symfony/thanks config (#338) | 1 | +0 / −4 |  |
| 2024-02-19 | `fc37ddbae6` | Other | [Tests] Fixed failing PreviewControllerTest::testPreview after merge-up | 1 | +1 / −1 |  |
| 2024-02-21 | `788f204ded` | Other | [PHPDoc] Removed incorrect type hint of GenericType::getSortInfo (#339) | 2 | +0 / −7 |  |
| 2024-02-21 | `682ce8b5bc` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.5 | 0 | +0 / −0 |  |
| 2024-02-21 | `73817e0fd7` | Other | Merged branch '4.5' | 0 | +0 / −0 |  |
| 2024-02-26 | `bd0e500fa5` | Other | Bumped Ibexa Fast Track version to v4.5.6 | 1 | +1 / −1 |  |
| 2024-02-26 | `e7e61f84fd` | Other | Merged branch '4.5' | 0 | +0 / −0 |  |
| 2024-02-26 | `61d0381b23` | Other | Bumped Ibexa LTS version to v4.6.1 | 1 | +1 / −1 |  |
| 2024-02-28 | `83c4e508e4` | Other | IBX-7838: Corrected message an error for InvalidArgumentType (#341) | 2 | +1 / −6 |  |
| 2024-02-28 | `453fd988be` | Other | Merged branch '4.5' | 0 | +0 / −0 |  |

## 2024-03 (27 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-03-01 | `6e64a8f046` | Other | Bumped Ibexa LTS version to v4.6.2 | 1 | +1 / −1 |  |
| 2024-03-07 | `a321523049` | Other | IBX-7769: Added safety check to ibexa:install command when tables in database exist (#336) | 1 | +9 / −0 |  |
| 2024-03-07 | `ca4472ab67` | Merged | Merge remote-tracking branch 'origin/4.5' | 0 | +0 / −0 |  |
| 2024-03-07 | `9ab64b56fb` | Other | IBX-7836: [Tests] Changed My Content to My content (#340) | 1 | +1 / −1 |  |
| 2024-03-08 | `8ef0e70c49` | Other | IBX-7809: Fixed creating `UserMetadata` criterion from `UserGroupLimitationType` | 2 | +103 / −4 |  |
| 2024-03-08 | `40b0fd0b44` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.5 | 0 | +0 / −0 |  |
| 2024-03-08 | `926bcc3a9e` | Merged | Merge branch '4.5' | 0 | +0 / −0 |  |
| 2024-03-14 | `86e66490bf` | Other | Set up branch to become 5.0 in the future | 1 | +2 / −2 |  |
| 2024-03-14 | `638d420c44` | Other | Bumped Ibexa Fast Track version to v5.0.0 | 1 | +1 / −1 |  |
| 2024-03-19 | `1b543804f9` | Other | IBX-7959: Added `ContentInfo::getSectionId` strict getter (#348) | 10 | +62 / −53 |  |
| 2024-03-19 | `2c2cd9c64f` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-03-20 | `7e472317f7` | Merged | Merge pull request from GHSA-mwvh-p3hx-x4gg | 12 | +134 / −34 |  |
| 2024-03-20 | `e877744d0a` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.5 | 0 | +0 / −0 |  |
| 2024-03-20 | `1fd57b27f0` | Other | IBX-7149: Refactored content type-based indexing to rely on a dedicated strategy (#296) | 12 | +433 / −93 |  |
| 2024-03-20 | `b934a7e8e1` | Other | Merged branch '4.5' into 4.6 | 0 | +0 / −0 |  |
| 2024-03-20 | `3d320062ec` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-03-20 | `862a2b48a2` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.5 (#351) | 0 | +0 / −0 |  |
| 2024-03-20 | `51fb8792ec` | Other | Merged branch '4.5' into 4.6 | 0 | +0 / −0 |  |
| 2024-03-20 | `3a51430297` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-03-22 | `7226b5fb6a` | Other | Bumped Ibexa Fast Track version to v4.5.7 | 1 | +1 / −1 |  |
| 2024-03-22 | `9e107b6fd8` | Other | Merged branch '4.5' into 4.6 | 0 | +0 / −0 |  |
| 2024-03-22 | `6e01aeb62c` | Other | Bumped Ibexa LTS version to v4.6.3 | 1 | +1 / −1 |  |
| 2024-03-22 | `170fc21297` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-03-27 | `0334d52327` | Other | IBX-3740: Prepended default Core settings (#182) | 4 | +11 / −21 |  |
| 2024-03-27 | `86d44648ac` | Other | Merged branch '4.5' into 4.6 | 0 | +0 / −0 |  |
| 2024-03-27 | `44403a6519` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-03-27 | `82540f61f9` | Removed | Removed repository-wide pull request template | 1 | +0 / −16 |  |

## 2024-04 (28 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-04-10 | `26f59d8433` | Other | IBX-7717: Introduced strict getters for LookupLimitationResult and VersionInfo Value Object (#349) | 2 | +33 / −5 |  |
| 2024-04-10 | `00e8a3fc2d` | Merged | Merge remote-tracking branch 'origin/4.5' into 4.6 | 0 | +0 / −0 |  |
| 2024-04-10 | `bde353833e` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-04-11 | `20717d9c3d` | Added | Added order by limitation value to loadRole and loadRoleByIdentifier methods (#353) | 1 | +4 / −2 |  |
| 2024-04-11 | `3126cd50bf` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-04-12 | `28ca460ca4` | Other | Bumped Ibexa LTS version to v4.6.4 | 1 | +1 / −1 |  |
| 2024-04-12 | `6b9e4de44f` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-04-16 | `f1ce2b27e5` | Updated | Fixed PHPDoc syntax (#311) | 5 | +9 / −34 |  |
| 2024-04-17 | `81464c68ca` | Updated | Fixed missing return types for DebugTemplate class | 1 | +8 / −16 |  |
| 2024-04-17 | `64038840d0` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.6 | 0 | +0 / −0 |  |
| 2024-04-18 | `20cf5e198d` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-04-18 | `65cf2fdecd` | Other | Dropped obsolete Debug Bundle DebugTemplate class (#357) | 5 | +9 / −175 |  |
| 2024-04-18 | `9a396e3884` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-04-22 | `045cb9e603` | Other | IBX-8032: Added proper casting to int for width/height in image search field (#354) | 1 | +7 / −2 |  |
| 2024-04-22 | `a36ba335e8` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-04-22 | `83fe51913c` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.5 | 0 | +0 / −0 |  |
| 2024-04-22 | `3ea7b6d2d7` | Other | [PHPStan] Aligned baseline after the merge up | 1 | +0 / −20 |  |
| 2024-04-22 | `4ccc0fad65` | Other | IBX-7833: [PAPI] Implemented loading paginated relation list (#343) | 15 | +543 / −40 |  |
| 2024-04-23 | `b6d3e709f2` | Merged | Merge remote-tracking branch 'origin/4.5' into 4.6 | 0 | +0 / −0 |  |
| 2024-04-23 | `4b0a3e58a6` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-04-24 | `245d02e150` | Other | IBX-6592: Removed unusable location/subtree limitations from `state/assign` policy | 1 | +1 / −1 |  |
| 2024-04-24 | `fec637d610` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.6 | 0 | +0 / −0 |  |
| 2024-04-25 | `669c3002c5` | Other | [BC break] Added `implements \Stringable` to `Translation` Value Object (#344) | 4 | +14 / −21 |  |
| 2024-04-25 | `c97c8e00a7` | Other | IBX-8119: Upgraded minimum PHP version to 8.3 | 3 | +75 / −83 |  |
| 2024-04-29 | `79794e8d58` | Other | IBX-8121: Fixed code style for 5.0 - Upgrade dependencies | 1 | +2 / −2 |  |
| 2024-04-29 | `038e5ea7b3` | Other | IBX-8121: Fixed code style for 5.0 - Run code style fixer | 1643 | +11682 / −9184 |  |
| 2024-04-29 | `1e42f58a6d` | Other | IBX-8121: Fixed PHPStan | 2 | +16 / −16 |  |
| 2024-04-29 | `75b7723a75` | Other | IBX-8121: Fixed PHPUnit configuration | 1 | +2 / −2 |  |

## 2024-05 (28 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-05-06 | `a93fbd31d6` | Other | [Tests] Dropped providing fallback Kernel if not defined (#345) | 2 | +2 / −12 |  |
| 2024-05-07 | `987f6af4d7` | Other | IBX-8119: [GHA] Bumped PHP to 8.3 in gha-docker-solr.yaml workflow (#364) | 2 | +4 / −1 |  |
| 2024-05-06 | `72cf197352` | Updated | Fixed access to non-existent properties | 8 | +6 / −53 |  |
| 2024-05-07 | `b5fe9ad828` | Other | IBX-6494: Fixed copying of non-translatable fields to later versions | 2 | +122 / −21 |  |
| 2024-05-07 | `9c46427b39` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.6 | 0 | +0 / −0 |  |
| 2024-05-07 | `2be7d15425` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-05-10 | `2f0f96c599` | Other | IBX-8119: Fixed tests for 5.0 | 17 | +17 / −27 |  |
| 2024-05-10 | `98b7b50e60` | Other | IBX-5388: Fixed performance issues of content updates after field changes | 26 | +1164 / −241 |  |
| 2024-05-14 | `055e4cf547` | Other | Bumped Ibexa LTS version to v4.6.5 | 1 | +1 / −1 |  |
| 2024-05-14 | `394d81529e` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-05-14 | `e22466cb4a` | Other | IBX-8012: Fixed handling languages by `UrlAliasGenerator::loadLocation` (#361) | 3 | +86 / −10 |  |
| 2024-05-14 | `77bbf78f6b` | Merged | Merge branch '4.6' into main | 0 | +0 / −0 |  |
| 2024-05-14 | `63492be94b` | Other | IBX-8283: Added missing policies' UI translations (#370) | 2 | +43 / −0 |  |
| 2024-05-14 | `62df3d649c` | Merged | Merge remote-tracking branch 'ibexa/4.6' | 0 | +0 / −0 |  |
| 2024-05-15 | `868c47533d` | Other | [PHPStan] Fixed PHPStan baseline (#371) | 6 | +60 / −6 |  |
| 2024-05-15 | `729c2b5277` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-05-16 | `4bcf0c5142` | Other | IBX-7653: IsContainer criterion added (#333) | 9 | +293 / −1 |  |
| 2024-05-16 | `3a97d9c426` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-05-17 | `b8ab5efb58` | Other | Bumped Ibexa LTS version to v4.6.6 | 1 | +1 / −1 |  |
| 2024-05-17 | `721ca78f04` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-05-17 | `327864179d` | Other | Wrapped `iterable`s used as arrays with `iterator_to_array` (#372) | 25 | +105 / −330 |  |
| 2024-05-17 | `2964324e27` | Other | IBX-3957: Made NOP URL aliases not reusable and original (#350) | 3 | +52 / −5 |  |
| 2024-05-20 | `5d4ffc6f77` | Other | IBX-8139: Dropped `class_alias` BC layer statements from all classes (#366) | 2746 | +1 / −5504 |  |
| 2024-05-20 | `b2f9e93d14` | Other | Bumped Ibexa LTS version to v4.6.7 | 1 | +1 / −1 |  |
| 2024-05-20 | `1ed6afa327` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-05-23 | `eeb08b493e` | Other | IBX-8140: Enabled authenticator manager-based security (#368) | 19 | +149 / −1233 |  |
| 2024-05-29 | `25100f09a9` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.6 | 0 | +0 / −0 |  |
| 2024-05-31 | `216c669161` | Updated | Updated copyright year to 2024 | 2 | +2 / −2 |  |

## 2024-06 (24 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-06-05 | `ac9beddbbb` | Merged | Merge remote-tracking branch 'origin/4.6' into HEAD | 0 | +0 / −0 |  |
| 2024-06-05 | `2944bb7c57` | Other | Coding standards | 3 | +16 / −9 |  |
| 2024-06-05 | `15c129d4ec` | Updated | Update src/lib/Persistence/Legacy/Content/Mapper/ResolveVirtualFieldSubscriber.php | 1 | +1 / −1 |  |
| 2024-06-05 | `9654942f51` | Other | Merged branch '4.6' into 'main' (#376) | 0 | +0 / −0 |  |
| 2024-06-05 | `5848b1e80e` | Other | IBX-7911: Used strict getters for content tree loading code paths (#347) | 37 | +716 / −317 |  |
| 2024-06-05 | `ef76f23dca` | Other | [PHPStan] Aligned baseline after PHPStan update | 1 | +5 / −0 |  |
| 2024-06-05 | `38ccd4f761` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-06-07 | `c88c39759c` | Other | IBX-8019: Added performance consideration notice to `LocationService::loadLocationChildren` (#407) | 1 | +4 / −0 |  |
| 2024-06-07 | `39aed20d9c` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.6 | 0 | +0 / −0 |  |
| 2024-06-07 | `439d1d8cce` | Other | [PHPStan] Aligned baseline with PHPStan update | 4 | +4 / −114 |  |
| 2024-06-07 | `88addb4394` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-06-11 | `915df966c3` | Other | [PHPStan] Dropped baseline files for unsupported PHP versions (#381) | 7 | +440 / −987 |  |
| 2024-06-18 | `5daf2a1006` | Other | IBX-8399: Moved RepositoryConfigurationProvider to Repository layer (#383) | 33 | +544 / −519 |  |
| 2024-06-18 | `f8ff4f6561` | Other | IBX-8400: Fixed redundancy in RepositoryFactory implementations (#384) | 10 | +106 / −130 |  |
| 2024-06-19 | `d369ebe6ee` | Other | IBX-8356: Deprecated `Ibexa\Core\MVC\Symfony\Security\Authentication\AuthenticatorInterface` to be replaced with Symfony-based authorization in 5.0 (#387) | 2 | +3 / −1 |  |
| 2024-06-19 | `77e36ffd85` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-06-24 | `17e78be8a1` | Other | IBX-6833: Fixed copying empty fields from a published version | 2 | +122 / −3 |  |
| 2024-06-25 | `49a2330fb8` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.6 | 0 | +0 / −0 |  |
| 2024-06-25 | `c93fddd325` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-06-25 | `52beb2fe22` | Other | Bumped Ibexa LTS version to v4.6.8 | 1 | +1 / −1 |  |
| 2024-06-27 | `2c706aef2f` | Other | IBX-8224: Dropped BackwardCompatibleCommand usage (#386) | 12 | +14 / −91 |  |
| 2024-06-27 | `ca8e4272bc` | Other | IBX-8136: Dropped "guzzlehttp/guzzle" and "php-http/guzzle6-adapter" dependencies (#392) | 1 | +0 / −2 |  |
| 2024-06-28 | `7d40748af7` | Other | [PHPDoc] Fixed LocationService::loadLocationChildren doc (#395) | 1 | +2 / −5 |  |
| 2024-06-28 | `232ee8a4a3` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |

## 2024-07 (22 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-07-01 | `62e04b2fcc` | Other | IBX-8356: Removed `Ibexa\Core\MVC\Symfony\Security\Authentication\AuthenticatorInterface` to be replaced with Symfony-based authentication | 6 | +128 / −70 |  |
| 2024-07-01 | `2069cc3491` | Other | IBX-8452: Fixed result of casting to string of Plural Value Object (#394) | 5 | +156 / −60 |  |
| 2024-07-01 | `bc01e2eb0b` | Other | IBX-8322: Fixed lack of redirections to last visited pages after successful authentication (#389) | 2 | +0 / −50 |  |
| 2024-07-01 | `6069e71912` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-07-02 | `4ef331b0ae` | Other | Regenerated PHPStan baseline (#399) | 2 | +9 / −11 |  |
| 2024-07-02 | `a3fbb29c44` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-07-04 | `e710a6e2d1` | Other | [PHPDoc] Fixed erroneous annotations for PHP API reference (#397) | 22 | +65 / −79 |  |
| 2024-07-04 | `00eb98a558` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-07-05 | `f44f605b25` | Other | Deprecated `CONSTANT_AUTH_TIME_SETTING` (#401) | 1 | +3 / −0 |  |
| 2024-07-05 | `d6b1921454` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-07-09 | `fe4c34ac62` | Other | IBX-8323: Reworked RepositoryAuthenticationProvider and moved its logic to a dedicated subscriber (#396) | 10 | +272 / −687 |  |
| 2024-07-09 | `b692e9babf` | Other | [PHPStan] Aligned baseline after PHPStan release (#402) | 1 | +5 / −5 |  |
| 2024-07-09 | `c59e25f83f` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-07-10 | `3d3588b3de` | Other | IBX-8378: [Tests] Fixed setting image field as searchable for tests (#403) | 1 | +6 / −0 |  |
| 2024-07-10 | `ace51ecfb4` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-07-15 | `bdab9eab46` | Other | IBX-8426: Fixed duplicating relations when updating content (#390) | 4 | +195 / −6 |  |
| 2024-07-15 | `40698b3620` | Merged | Merge branch '4.6' into main | 0 | +0 / −0 |  |
| 2024-07-16 | `8cd53fca4d` | Other | [CS] Fixed outstanding CS issue in ContentTest | 1 | +1 / −1 |  |
| 2024-07-23 | `da23f87c5f` | Other | IBX-8138: Refactored deprecated `loadUserByUsername` method (#400) | 7 | +243 / −478 |  |
| 2024-07-30 | `573af4849f` | Other | Bumped Ibexa LTS version to v4.6.9 | 1 | +1 / −1 |  |
| 2024-07-31 | `e7d917c109` | Other | IBX-8644: Added missing country Curaçao in ezcountry fieldtype | 1 | +1 / −0 |  |
| 2024-07-31 | `de74408d61` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |

## 2024-08 (24 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-08-01 | `b4a44ccc2d` | Other | IBX-8558: Removed `GuardRepositoryAuthenticationProvider` due to Symfony security deprecations (#405) | 5 | +1 / −190 |  |
| 2024-08-01 | `8a676dc713` | Other | IBX-4000: Changed the method name creation for logCall in callable function (#410) | 2 | +12 / −9 |  |
| 2024-08-01 | `b3755c3d8b` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-08-07 | `9a6241085f` | Other | IBX-8562: Fixed flooding content attributes table with duplicates | 6 | +87 / −25 |  |
| 2024-08-09 | `1208a1ea38` | Other | [Tests] Fixed breaking changes introduced by twig/twig v3.11.0 (#415) | 5 | +52 / −110 |  |
| 2024-08-09 | `2715e7daac` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-08-13 | `4e7be1e52a` | Merged | Merge remote-tracking branch 'ezsystems/1.3' into temp_1.3_to_4.6 | 0 | +0 / −0 |  |
| 2024-08-14 | `7cf00454c6` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.6 (#413) | 0 | +0 / −0 |  |
| 2024-08-14 | `e6bbd52249` | Other | Bumped Ibexa LTS version to v4.6.10 | 1 | +1 / −1 |  |
| 2024-08-14 | `f851d43941` | Other | IBX-8656: Skipped credentials check for `SelfValidatingPassport` (#411) | 6 | +41 / −130 |  |
| 2024-08-20 | `078d9d05bf` | Other | [PHPStan] Aligned baseline after PHPStan release (#418) | 1 | +3 / −3 |  |
| 2024-08-20 | `a00f5f6abd` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-08-21 | `e8e81c8243` | Other | IBX-8469: Fixed image filtering by file size float value (#414) | 3 | +84 / −22 |  |
| 2024-08-21 | `04b97ef620` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-08-26 | `b106a2a3e0` | Other | [Tests] Truncated SQLite last insert ID to real DB integer value (#421) | 1 | +3 / −1 |  |
| 2024-08-26 | `ae5c80d151` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-08-26 | `f15e57c387` | Added | Add missing variable name in var tag in PermissionResolver (#423) | 1 | +1 / −1 |  |
| 2024-08-26 | `797bb14895` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-08-27 | `13a0cf1627` | Other | Extracted common base for TextBlock and TextLine field types (#406) | 10 | +261 / −949 |  |
| 2024-08-27 | `0561171f2c` | Other | Extracted common code for Binary and Media field type (#407) | 7 | +381 / −901 |  |
| 2024-08-27 | `0b6ceefaea` | Other | Extracted redundant TextLine and ISBN SearchField into a common base (#422) | 3 | +62 / −110 |  |
| 2024-08-28 | `4bed131552` | Other | Extracted abstract for redundant Host & URI text matchers (#424) | 4 | +63 / −141 |  |
| 2024-08-28 | `7c398f219b` | Other | Refactored Float and Integer field types to use external validators (#425) | 14 | +887 / −1720 |  |
| 2024-08-28 | `7e4312d81f` | Other | IBX-8138: [Rector] Applied rules from Symfony 5 Rector set lists (#385) | 458 | +1047 / −1301 |  |

## 2024-09 (10 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-09-03 | `81784d04db` | Other | IBX-8804: Moved `PASSWORD_HASH_OAUTH2` from `ibexa/oauth2-client` (#419) | 7 | +37 / −154 |  |
| 2024-09-04 | `0ba8cd18d5` | Other | IBX-8726: Added IsBookmarked criterion (#417) | 17 | +598 / −2 |  |
| 2024-09-04 | `5242d84d7a` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-09-16 | `6759bf781b` | Other | Bumped Ibexa LTS version to v4.6.11 | 1 | +1 / −1 |  |
| 2024-09-16 | `b9f1582dec` | Other | Aligned PHPStan baseline and updated Symfony deprecations helper setting (#426) | 3 | +2 / −17 |  |
| 2024-09-16 | `7d860e11ec` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-09-17 | `96f656c977` | Other | Improved strictness of URLChecker HTTPHandler and URL Value Object (#427) | 3 | +40 / −28 |  |
| 2024-09-24 | `05a3bd15d2` | Added | Added basic Pool implementation (#428) | 4 | +229 / −0 |  |
| 2024-09-24 | `cf8ac329c0` | Merged | Merge remote-tracking branch 'origin/4.6' into main | 0 | +0 / −0 |  |
| 2024-09-28 | `1a90955eaa` | Other | [Tests] Migrated deprecated PHPUnit configuration (#429) | 2 | +62 / −80 |  |

## 2024-10 (29 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-10-02 | `3d91a751e8` | Other | Regenerated PHPStan baseline (#431) | 1 | +9 / −4 |  |
| 2024-10-02 | `9328655ccc` | Other | IBX-8811: Rebranded SiteAccess session prefix (#420) | 7 | +59 / −149 |  |
| 2024-10-04 | `0123be1537` | Other | Bumped Ibexa LTS version to v4.6.12 | 1 | +1 / −1 |  |
| 2024-10-04 | `9da20d1df4` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-10-04 | `6652a62ab5` | Other | [PHPStan] Fixed errors after PHPStan update (#432) | 5 | +4 / −10 |  |
| 2024-10-04 | `9bf029b92a` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-10-08 | `f6dde521c1` | Updated | Fixed issues found by by PHPStan (#433) | 3 | +1 / −11 |  |
| 2024-10-09 | `f85390d8d7` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-10-09 | `c338dc5ebb` | Other | [PHPDoc] Prefixed template mentioning annotation with phpstan- (#434) | 2 | +7 / −7 |  |
| 2024-10-09 | `89bf6e001f` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-10-10 | `d1479983aa` | Other | [Composer] Dropped ibexa/ci-scripts from dev requirements (#436) | 1 | +0 / −1 |  |
| 2024-10-22 | `b19be8dbef` | Other | IBX-8562: Command to remove duplicated entries after faulty IBX-5388 fix | 3 | +268 / −2 |  |
| 2024-10-22 | `4bde7410fc` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.6 | 0 | +0 / −0 |  |
| 2024-10-22 | `2fbf8ca9ad` | Other | IBX-9103: Fixed cache tag name not including relation type (#437) | 3 | +23 / −8 |  |
| 2024-10-22 | `cdaaf2530a` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-10-22 | `8c50b0e9ad` | Other | [PHPStan] Aligned baseline with PHPStan update | 3 | +19 / −9 |  |
| 2024-10-22 | `8ab433a033` | Merged | Merge branch '1.3' of ezsystems/ezplatform-kernel into 4.6 (#443) | 0 | +0 / −0 |  |
| 2024-10-22 | `fef43ed5fb` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-10-22 | `08ee22ce16` | Other | Bumped Ibexa LTS version to v4.6.13 | 1 | +1 / −1 |  |
| 2024-10-22 | `bcdfe3703c` | Other | [PHPStan] Fix phpstan-baseline.neon | 1 | +0 / −5 |  |
| 2024-10-22 | `1a433a4a02` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-10-23 | `fed1a91cd0` | Other | IBX-9103: Added RelationType filtering to fetch relations methods (#440) | 7 | +210 / −51 |  |
| 2024-10-25 | `ac4e57c913` | Other | IBX-8957: Fixed deserializing SiteAccess Matchers for ESI (#430) | 20 | +802 / −431 |  |
| 2024-10-28 | `dac8de32b9` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-10-30 | `61a0914aa4` | Other | IBX-8534: Added cache invalidation for source content when adding relation (#446) | 2 | +5 / −1 |  |
| 2024-10-30 | `e5df9d4b4c` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-10-31 | `623bbc3325` | Other | Optimised content thumbnail resolving (#441) | 1 | +9 / −6 |  |
| 2024-10-31 | `b10e48cc63` | Other | Reduced row extraction time complexity in persistence content type mapper  (#442) | 1 | +13 / −5 |  |
| 2024-10-31 | `2ba88571cb` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |

## 2024-11 (19 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-11-04 | `0c5f86c672` | Other | IBX-8534: Improved iteration over relation list (#444) | 6 | +211 / −1 |  |
| 2024-11-05 | `9e313d2e1a` | Other | [PHPDoc] Improved ContentService doc for API reference (#379) | 4 | +168 / −258 |  |
| 2024-11-07 | `78236ffc31` | Other | IBX-8805: Dropped deprecated Twig Functions&Filters (#450) | 31 | +2 / −992 |  |
| 2024-11-08 | `aba61e9098` | Other | IBX-8534: Dropped core deprecations (#435) | 124 | +462 / −3948 |  |
| 2024-11-08 | `02200e652f` | Other | IBX-8418: Fixed removing orphaned drafts when trashing or deleting its ancestors (#439) | 16 | +284 / −15 |  |
| 2024-11-13 | `ae12de3612` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-11-19 | `9fda0ecd1c` | Other | Regenerated PHPStan (#453) | 1 | +2 / −2 |  |
| 2024-11-19 | `2e22cebee1` | Updated | Fixed issues related to twig/twig 3.15 in tests | 36 | +719 / −217 |  |
| 2024-11-19 | `130a381da6` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-11-20 | `c38bd010b1` | Other | Introduced StructValidator to unpack validation errors for structs | 9 | +536 / −0 |  |
| 2024-11-20 | `4c9295f179` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-11-25 | `eed0ceab7e` | Other | IBX-8566: Fixed postgres language limit (#454) | 13 | +246 / −41 |  |
| 2024-11-25 | `701ed90789` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2024-11-27 | `397e20983d` | Other | IBX-9169: Added `ContentName` criterion handler to trash handler (#448) | 2 | +2 / −0 |  |
| 2024-11-27 | `7d2a63c0c6` | Merged | Merge branch '4.6' into main | 0 | +0 / −0 |  |
| 2024-11-28 | `dad432ebed` | Other | Bumped Ibexa LTS version to v4.6.14 | 1 | +1 / −1 |  |
| 2024-11-28 | `6ca16e9e03` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-11-28 | `312f7ea498` | Other | IBX-6312: Fixed ParentContentType View Matcher for not available parent (#438) | 4 | +110 / −13 |  |
| 2024-11-28 | `8bdad66728` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |

## 2024-12 (12 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-12-06 | `c45fb35c83` | Other | [PHPStan] Aligned baseline after PHPStan release (#459) | 2 | +0 / −10 |  |
| 2024-12-06 | `54014140a5` | Other | [PHPDoc] Enhanced `@deprecated` usage (#458) | 4 | +6 / −6 |  |
| 2024-12-09 | `ddc2855015` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-12-10 | `625f7b740d` | Other | IBX-8534: Cleaned up deprecations (#456) | 180 | +578 / −2108 |  |
| 2024-12-10 | `e8dc468dc9` | Other | [CI] Skipped MaxLanguagesContentServiceTest::testCreateContent on non-LSE (#460) | 1 | +4 / −0 |  |
| 2024-12-10 | `760bfefa75` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2024-12-13 | `46ec5e9864` | Other | Bumped Ibexa LTS version to v4.6.15 | 1 | +1 / −1 |  |
| 2024-12-13 | `3c496e82a2` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-12-19 | `806270b727` | Other | [PHPStan] Updated baseline after PHPStan release (#465) | 2 | +4 / −24 |  |
| 2024-12-20 | `d5df651592` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2024-12-20 | `90f0c81e83` | Other | IBX-9316: Fixed CPU count for Ibexa Cloud (#461) | 1 | +13 / −2 |  |
| 2024-12-20 | `773c7d05b1` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |

## 2025-01 (9 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-01-09 | `cd57d587f0` | Other | IBX-9337: Fixed failure to serialize field data post symfony/serializer 5.4.40 | 2 | +60 / −0 |  |
| 2025-01-09 | `e8a4b95346` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-01-16 | `81cb9bcfc5` | Other | Bumped Ibexa LTS version to v4.6.16 | 1 | +1 / −1 |  |
| 2025-01-16 | `ca0da00e1b` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-01-22 | `7a5d9eb7ca` | Updated | Updated copyright year to 2025 | 3 | +3 / −3 |  |
| 2025-01-22 | `8481e63cb1` | Updated | Updated copyright year to 2025 | 3 | +3 / −3 |  |
| 2025-01-22 | `920f5a8cc3` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-01-30 | `3a7b4062b0` | Other | [PHPStan] Regenerated baseline (#472) | 2 | +5 / −5 |  |
| 2025-01-31 | `07b82cc61d` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |

## 2025-02 (36 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-02-04 | `815b00db79` | Other | IBX-8470: Upgraded codebase to Symfony 6 (#447) | 148 | +3045 / −4494 |  |
| 2025-02-05 | `aec2544a1f` | Other | IBX-9421: Fixed global `@serializer` service overwriting Ibexa serializer variant | 1 | +1 / −2 |  |
| 2025-02-05 | `2af18593c8` | Other | IBX-9326: Fixed missing class attribute for image field Twig block (#471) | 1 | +1 / −0 |  |
| 2025-02-10 | `2a6f21efb0` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-02-11 | `553e0dd0c7` | Other | IBX-9455: Upgraded Twig to ^3.19.0 (#411) | 1 | +1 / −1 |  |
| 2025-02-11 | `10fcc63a25` | Other | Merged branch '1.3' of ezsystems/ezplatform-kernel into 4.6 | 0 | +0 / −0 |  |
| 2025-02-11 | `6b2d7a3003` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-02-11 | `382f9a61e0` | Other | IBX-9513: Fixed obsolete `ibexa_*` controller references (#474) | 4 | +8 / −8 |  |
| 2025-02-12 | `b43570078a` | Other | IBX-9415: Added support for ContentAwareInterface in ibexa_* Twig functions (#467) | 21 | +558 / −38 |  |
| 2025-02-12 | `7c30793299` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-02-13 | `242b25ab7f` | Other | Enhanced contracts' PHPDoc (#463) | 16 | +262 / −296 |  |
| 2025-02-14 | `1efa3ef33f` | Other | IBX-9302: Improved default image variations to auto rotate (#464) | 1 | +1 / −0 |  |
| 2025-02-15 | `3738528c52` | Other | [PHPUnit] Bumped number of expected direct deprecations (#482) | 1 | +1 / −1 |  |
| 2025-02-17 | `0627a2b7c3` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-02-17 | `23637ba1e4` | Other | [PHPDoc] Fixed typo in SearchService::findLocations contract doc | 1 | +1 / −1 |  |
| 2025-02-18 | `f3eeda34e1` | Other | IBX-9415: Added union types to ContentExtension, FieldRenderingExtension, and RenderContentExtension (#478) | 4 | +34 / −108 |  |
| 2025-02-16 | `aa55a0a217` | Other | [Rector] Added rector configuration | 2 | +22 / −0 |  |
| 2025-02-16 | `a739d49fbb` | Other | [Validator] Migrated annotation to attributes | 1 | +1 / −3 |  |
| 2025-02-16 | `f36ad29786` | Other | [Serializer] Migrated annotation to attributes | 1 | +2 / −4 |  |
| 2025-02-16 | `933f374a97` | Other | [Rector] Enabled Symfony 6.1 rule set | 1 | +1 / −0 |  |
| 2025-02-16 | `f448a98b51` | Other | [PHP] Updated method reference syntax | 9 | +23 / −23 |  |
| 2025-02-16 | `dd928f5793` | Other | [CLI] Replaced deprecated Command::{$defaultName, $defaultDescription} with the AsCommand attribute | 16 | +72 / −57 |  |
| 2025-02-16 | `b4d33e044a` | Other | [Serializer] Replaced usage of deprecated Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface | 2 | +8 / −4 |  |
| 2025-02-16 | `b87e5795a0` | Other | [Rector] Enabled Symfony 6.2 rule set | 1 | +7 / −0 |  |
| 2025-02-16 | `271b60767c` | Other | [HTTP] Migrated from ArgumentValueResolvedInterface to ValueResolverInterfaceInterface | 3 | +27 / −35 |  |
| 2025-02-16 | `f1c3a3d3ec` | Other | [Rector] Enabled Symfony 6.3 and Symfony 6.4 rule sets | 1 | +2 / −0 |  |
| 2025-02-16 | `b16d986813` | Other | [HTTP] Replaced usage of deprecated Symfony\Component\HttpKernel\UriSigner | 2 | +2 / −2 |  |
| 2025-02-16 | `f0a4d838b0` | Other | [Rector] Enabled rector on CI | 1 | +28 / −3 |  |
| 2025-02-20 | `9571e93045` | Other | IBX-8532: Removed deprecated Facets API (#484) | 186 | +6 / −1701 |  |
| 2025-02-23 | `9fe7e20811` | Other | Mark ContentName as also being a Trash Criterion (#486) | 1 | +2 / −1 |  |
| 2025-02-23 | `cbd646fd00` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-02-24 | `438946dcdc` | Other | Refactored DateMapper for better date conversion handling and code readability (#487) | 1 | +22 / −10 |  |
| 2025-02-24 | `3365458f16` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-02-24 | `7048240e60` | Removed | Removed deprecated SearchResult::$count property (#489) | 3 | +3 / −57 |  |
| 2025-02-24 | `1dd257acab` | Removed | Removed deprecated SearchResult::$spellSuggestion property (#488) | 150 | +149 / −159 |  |
| 2025-02-25 | `cdfba458bb` | Other | [Twig] Removed usage of deprecated spaceless filter (#490) | 1 | +1 / −54 |  |

## 2025-03 (28 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-03-01 | `e4d79cf580` | Removed | Removed deprecated ContentType::isContainer property (#491) | 5 | +5 / −5 |  |
| 2025-03-02 | `fdeeb4fd60` | Released | Bump phpstan/phpstan to ^2.0 (#493) | 11 | +26797 / −12541 |  |
| 2025-03-02 | `7dd9de3225` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-03-04 | `818ec9313e` | Other | Bumped Ibexa LTS version to v4.6.17 | 1 | +1 / −1 |  |
| 2025-03-04 | `638209e1d4` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-03-05 | `2ae80f9b35` | Removed | Removed deprecated MaskGenerator::generateLanguageMask method  (#503) | 3 | +13 / −100 |  |
| 2025-03-05 | `e33b2ff045` | Removed | Removed deprecated Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\*Converter::create method (#500) | 21 | +0 / −292 |  |
| 2025-03-06 | `663ee7a741` | Other | Bumped Ibexa LTS version to v4.6.18 | 1 | +1 / −1 |  |
| 2025-03-06 | `5ff418621a` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-03-06 | `edc08ed68b` | Other | [PHPStan] Regenerated baseline after PHPStan release (#505) | 2 | +222 / −128 |  |
| 2025-03-06 | `0344633609` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-03-07 | `a09169caa0` | Other | IBX-9447: Added a missing condition when verifying the Asset field (#495) | 1 | +1 / −1 |  |
| 2025-03-07 | `a874f98106` | Removed | Removed deprecated PermissionSubtree::createFromQueryBuilder method (#506) | 2 | +0 / −33 |  |
| 2025-03-07 | `4dfa29f4a8` | Other | Replaced deprecated FieldNameResolver::getFieldNames method with FieldNameResolver::getFieldTypes (#507) | 3 | +19 / −46 |  |
| 2025-03-07 | `b3a926f2b8` | Removed | Removed deprecation BaseTest::isVersion4 and SearchServiceTest::testDeprecatedCriteriaProperty methods (#508) | 3 | +0 / −40 |  |
| 2025-03-10 | `dde5b765fb` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2025-03-15 | `0ca333f716` | Other | [PHPStan] Regenerated PHPStan baseline (#512) | 1 | +11 / −11 |  |
| 2025-03-15 | `78d85caaad` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-03-15 | `e04b45d7a7` | Other | [PHPStan] Regenerated PHPStan baseline for PHP < 8.3 (#513) | 2 | +70 / −10 |  |
| 2025-03-15 | `7d0c7c81b2` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-03-15 | `ed27a78f95` | Removed | Removed deprecated buildSPIFieldDefinitionUpdate and buildSPIFieldDefinitionCreate methodd from ContentTypeDomainMapper (#504) | 2 | +2 / −155 |  |
| 2025-03-15 | `049e9c386d` | Removed | Removed deprecated timestamp property from DataAndTimeConverter and DateConverter (#509) | 4 | +4 / −14 |  |
| 2025-03-25 | `7365e09ce1` | Other | [PHPStan] Regenerated PHPStan baseline (#518) | 1 | +7 / −1 |  |
| 2025-03-25 | `e2f284bcec` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-03-25 | `0b608b7d98` | Added | Added support for ContentAwareInterface in ibexa_render function (#511) | 6 | +143 / −10 |  |
| 2025-03-26 | `c6a7851c7a` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-03-28 | `375c949f60` | Other | IBX-9697: Added priority attribute to ibexa data collector (#517) | 3 | +56 / −21 |  |
| 2025-03-28 | `bb02ea1ec6` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |

## 2025-04 (16 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-04-03 | `793ee0e07a` | Other | IBX-9060: Added mark as unread functionality for notifications (#510) | 11 | +241 / −20 |  |
| 2025-04-03 | `0cbc4ee29a` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-04-07 | `d177f134e0` | Other | IBX-9379: Added Grace Period for archived versions (#515) | 23 | +325 / −23 |  |
| 2025-04-07 | `e95c9da6ae` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2025-04-07 | `00f133945c` | Other | [CI] Fixed phpstan issues (#522) | 1 | +0 / −6 |  |
| 2025-04-08 | `b8f0ca2710` | Other | Bumped Ibexa LTS version to v4.6.19 | 1 | +1 / −1 |  |
| 2025-04-08 | `04e47dd1c6` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-04-23 | `42a1259c7c` | Other | IBX-9810: Fixed inner `validateProperty*()` calls of StructWrapperValidator (#521) | 3 | +40 / −7 |  |
| 2025-04-23 | `3930bff620` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-04-24 | `f6e3294b3f` | Removed | Remove usage of deprecated ContainerAwareTrait (#514) | 31 | +177 / −258 |  |
| 2025-04-24 | `222f62c5d7` | Other | IBX-9262: Fixed Relation::Asset not being cleaned up on content deletion (#523) | 2 | +131 / −1 |  |
| 2025-04-24 | `e6ad1d2c16` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2025-04-29 | `f4925b3367` | Other | [CI] Fixed phpstan issues (#526) | 2 | +3 / −3 |  |
| 2025-04-29 | `6e3f288383` | Other | IBX-9898: Added mandatory admin user password altering on `ibexa:install` (#525) | 2 | +55 / −1 |  |
| 2025-04-29 | `244f7b24c4` | Other | IBX-9103: Replaced relation constants with RelationType enum (#524) | 15 | +150 / −148 |  |
| 2025-04-30 | `0f089583d8` | Other | [CI] Removed fixed issues from baseline (#527) | 1 | +0 / −12 |  |

## 2025-05 (52 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-05-03 | `9734ff4d68` | Other | [Rector] Enabled DoctrineSetList::DOCTRINE_DBAL_211 set | 1 | +2 / −0 |  |
| 2025-05-03 | `29b993e791` | Other | [Rector] Executed DoctrineSetList::DOCTRINE_DBAL_211 set | 108 | +702 / −718 |  |
| 2025-05-03 | `8305c46641` | Other | [doctrine/dbal] Refactor parameter binding to remove colons in setParameter calls | 24 | +200 / −200 |  |
| 2025-05-03 | `50191dcbce` | Other | [doctrine/dbal] Replaced usage of deprecated fetchColumn method with fetchOne | 17 | +33 / −33 |  |
| 2025-05-03 | `a1959a6733` | Other | [PHPStan] Regenerated phpstan baseline | 1 | +24 / −24 |  |
| 2025-05-12 | `2b4177ed75` | Other | IBX-8471: Upgraded codebase to Symfony 7 (#530) | 129 | +1705 / −2056 |  |
| 2025-05-12 | `fec1745900` | Other | Adjusted ibexa:install command to SF 7.x / DBAL 3.x (#531) | 2 | +8 / −45 |  |
| 2025-05-13 | `eeb58999dc` | Other | ENG-140: Added auto-assign reviewers GH workflow | 1 | +15 / −0 |  |
| 2025-05-13 | `fd9ad6ac45` | Updated | Updated ubunut and version for CI | 1 | +1 / −1 |  |
| 2025-05-14 | `6fefc46fa9` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-05-15 | `86c9c165fb` | Other | Resolved PHPStan issues (#537) | 1 | +8 / −0 |  |
| 2025-05-15 | `39243e0c2f` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-05-16 | `8d5c8beeb5` | Updated | Fixed collection subtypes PHPStan declarations (#538) | 8 | +40 / −20 |  |
| 2025-05-16 | `b282ff8207` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-05-17 | `4cb7e02bed` | Other | [CS] Resolved code style issues | 2 | +2 / −0 |  |
| 2025-05-16 | `668ed9fedb` | Other | [Rector] Added Symfony 7.0..7.2 sets to rector configuration | 1 | +3 / −0 |  |
| 2025-05-16 | `7de6a4dbda` | Other | [symfony/http-foundation] Use constructor to initialize RequestStack | 7 | +17 / −35 |  |
| 2025-05-16 | `c19c3200f7` | Other | [symfony/http-kernel] Replaced usage of internal Symfony\Component\HttpKernel\DependencyInjection\Extension class | 6 | +7 / −7 |  |
| 2025-05-16 | `969de8188b` | Other | [symfony/dependency-injection] Replaced deprecated !tagged YAML tag to !tagged_iterator | 6 | +6 / −6 |  |
| 2025-05-21 | `548b060c6c` | Other | IBX-8543: Included newer DBMS versions on CI & adjusted setup (#470) | 2 | +58 / −5 |  |
| 2025-05-21 | `cff4ccadca` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-05-21 | `e11bc2d5da` | Other | IBX-8543: Fixed PHP versions for MySQL 8.0 | 1 | +1 / −3 |  |
| 2025-05-22 | `0d9a5fd281` | Added | Added `phpstan-` to param using TValue | 2 | +3 / −3 |  |
| 2025-05-22 | `37b7b357b7` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-05-22 | `1af6f9d982` | Other | IBX-8226: Removed deprecated PHP deprecations handler (#542) | 3 | +0 / −77 |  |
| 2025-05-26 | `9edc9f795c` | Removed | Removed deprecated getName method from GatewayFactory (#544) | 4 | +8 / −10 |  |
| 2025-05-27 | `1a30260682` | Other | Bumped Ibexa LTS version to v4.6.20 | 1 | +1 / −1 |  |
| 2025-05-28 | `6526a15c24` | Added | Added return type declarations (#546) | 15 | +16 / −16 |  |
| 2025-05-28 | `aed11eab8e` | Removed | Removed deprecated mergeGlobals method (#545) | 1 | +2 / −2 |  |
| 2025-05-28 | `e92da4f51e` | Added | Added missing `void` return types to CompilerPass implementations (#548) | 3 | +6 / −25 |  |
| 2025-05-28 | `680ab25098` | Added | Added missing `void` return types and minor refactoring (#547) | 28 | +72 / −297 |  |
| 2025-05-28 | `634423f5d3` | Added | Added missing type hints to command configure method (#549) | 11 | +14 / −14 |  |
| 2025-05-29 | `cad65d1fb9` | Added | Add return type declaration for current() method in BatchIterator (#550) | 1 | +1 / −1 |  |
| 2025-05-29 | `55463f8fa9` | Other | IBX-9941: Renamed core database schema (#541) | 172 | +2485 / −2210 |  |
| 2025-05-29 | `6d3bd2719c` | Other | [Doc] Dropped obsolete doc about upgrade scripts location (#551) | 1 | +0 / −4 |  |
| 2025-05-30 | `d4dfdd4d32` | Added | Added missing `void` return types to 'process' (#553) | 2 | +3 / −3 |  |
| 2025-05-30 | `ef6b1ba4e7` | Added | Added return and parameter types to IORepositoryResolver methods and improve test typing (#554) | 3 | +24 / −159 |  |
| 2025-05-29 | `33ab291e63` | Other | [Tests] Renamed `ezbinaryfile.yaml` to `ibexa_binary_file.yaml` | 2 | +1 / −1 |  |
| 2025-05-29 | `1c35a8aec1` | Other | [Tests] Fixed `@covers` value for Bookmark\Gateway\DoctrineDatabaseTest | 1 | +1 / −1 |  |
| 2025-05-29 | `01fbbf3abb` | Other | [Tests] Optimized Bookmark\Gateway\DoctrineDatabaseTest::loadBookmark::loadBookmark query | 1 | +1 / −1 |  |
| 2025-05-29 | `9ca07f1756` | Other | [Tests] Fixed `@covers` value for Content\Gateway\DoctrineDatabaseTest | 1 | +1 / −1 |  |
| 2025-05-29 | `0c2f1b9989` | Other | [Tests] Fixed Notification GW DoctrineDatabaseTest after Doctrine update | 1 | +10 / −3 |  |
| 2025-05-29 | `fd77c00c22` | Other | Unwrapped unnecessary curly braces when referencing table name variables | 3 | +21 / −21 |  |
| 2025-05-29 | `09e7d15fbc` | Other | [PHPStan] Removed resolved issue from the baseline | 1 | +0 / −6 |  |
| 2025-05-29 | `2983b31543` | Updated | Fixed ObjectState GW `DoctrineDatabase::insertObjectState` method | 1 | +9 / −3 |  |
| 2025-05-30 | `dcc5216526` | Other | IBX-9727: Added missing type hints to collections framework  (#555) | 7 | +14 / −14 |  |
| 2025-05-30 | `7844072313` | Other | IBX-9727: Added missing type hints to Ibexa\Contracts\Core\Repository\Iterator\BatchIterator (#556) | 3 | +11 / −19 |  |
| 2025-05-30 | `132c789a56` | Other | IBX-9727: Add missing type hints to PAPI events (#557) | 213 | +819 / −2092 |  |
| 2025-05-30 | `48a779f5fa` | Other | IBX-9727: Add missing type hints to PAPI decorators (#558) | 19 | +19 / −50 |  |
| 2025-05-30 | `4b6414e2fd` | Other | IBX-8471: [Tests][DI] Replaced deprecated `!tagged` with `!tagged_iterator` (#559) | 2 | +2 / −2 |  |
| 2025-05-30 | `01d5a98ca8` | Other | [Tests] Fixed precision in `ParameterProviderTest::testGetViewParameters` (#560) | 1 | +3 / −5 |  |
| 2025-05-31 | `e49d16a1f5` | Other | IBX-9727: Add missing type hints to PAPI exceptions (#561) | 14 | +32 / −111 |  |

## 2025-06 (39 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-06-01 | `25c9abc01d` | Other | IBX-9727: Add missing type hints to options contracts (#563) | 4 | +23 / −41 |  |
| 2025-06-02 | `3bef031658` | Other | [Tests] Added `Case` suffix to abstract unit test cases (#566) | 150 | +555 / −563 |  |
| 2025-06-02 | `6b08178df7` | Other | IBX-9941: Renamed contentclass_id into content_type_id (#568) | 37 | +13604 / −13610 |  |
| 2025-06-02 | `375ca1c638` | Other | IBX-9727: Add missing type hints to image variants contracts (#564) | 19 | +63 / −152 |  |
| 2025-06-02 | `8358297d80` | Other | [Tests] Added `Case` suffix to abstract integration test cases (#567) | 168 | +1505 / −1505 |  |
| 2025-06-03 | `ec04f6ec3c` | Other | IBX-9727: Add missing type hints to limitation contracts (#562) | 25 | +313 / −413 |  |
| 2025-06-03 | `3304eaee65` | Other | Corrected PHPDoc for removeFieldDefinition() param (#573) | 2 | +0 / −14 |  |
| 2025-06-03 | `752e91892f` | Other | Replaced deprecated DBAL count expression and other minor deprecations (#571) | 22 | +278 / −860 |  |
| 2025-06-03 | `13b64dc4c9` | Removed | Removed deprecated getName method from RandomSortClauseHandlerFactory (#570) | 7 | +53 / −80 |  |
| 2025-06-03 | `6b4760be0a` | Other | IBX-9727: Add missing type hints to search contracts  (#565) | 95 | +555 / −740 |  |
| 2025-06-05 | `3ddf5385db` | Other | IBX-9941: Renamed content type version column to status (#574) | 15 | +584 / −573 |  |
| 2025-06-05 | `12abb87c81` | Other | IBX-9947: Rebranded field type identifiers (#543) | 199 | +3036 / −2879 |  |
| 2025-06-06 | `902584df80` | Other | IBX-8125: Added *link* index to ezurlalias_ml | 1 | +1 / −0 |  |
| 2025-06-06 | `cd53e7f043` | Other | IBX-9727: [Tests] Fixed strict types for core field type tests (#576) | 31 | +2866 / −9153 |  |
| 2025-06-06 | `a2473ef9b2` | Other | IBX-10129: Replaced ValueObject with `object` for PermissionResolver (#580) | 26 | +69 / −118 |  |
| 2025-06-06 | `e6b12591de` | Other | IBX-9727: Add missing type hints to content types related VO (#575) | 23 | +317 / −727 |  |
| 2025-06-06 | `25d8a0e15d` | Updated | Fixed "Cannot assign null to property ContentType::$urlAliasSchema of type string" (#583) | 4 | +4 / −4 |  |
| 2025-06-09 | `de7d259d2d` | Other | IBX-9941: Renamed ibexa_preferences into ibexa_user_preference (#578) | 3 | +5 / −5 |  |
| 2025-06-09 | `690baad458` | Other | IBX-9941: Fixed references to ibexa_preferences table clean data (#586) | 2 | +3 / −3 |  |
| 2025-06-09 | `68b2e3b5d5` | Other | Bumped Ibexa LTS version to v4.6.21 | 1 | +1 / −1 |  |
| 2025-06-09 | `5402ddd254` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-06-09 | `99f0f71bd8` | Other | [Tests] Refactored Content and Field Rendering Twig Integration tests (#584) | 4 | +152 / −273 |  |
| 2025-06-09 | `79b62afbec` | Other | IBX-10063: Fixed incorrect key used to determinate sub-matcher class for denormalization (#588) | 9 | +13 / −3 |  |
| 2025-06-16 | `05d353ce00` | Updated | Fixed case sensitivity for URLWildcard references (#597) | 5 | +11 / −55 |  |
| 2025-06-16 | `718bbb0f44` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-06-16 | `d314255aac` | Other | Use Doctrine\DBAL\Connection instead of Doctrine\DBAL\Driver\Connection in NormalizeImagesPathsCommand (#591) | 1 | +2 / −3 |  |
| 2025-06-17 | `0963e11b12` | Other | IBX-10137: Moved slug transformation rules to production Resources (#589) | 32 | +5016 / −5644 |  |
| 2025-06-17 | `450c4a9711` | Other | IBX-9328: Added the `users_group_root_subtree_path` config parameter (#594) | 2 | +6 / −0 |  |
| 2025-06-17 | `be546a919c` | Other | IBX-9727: Fixed strict types of translatable Exceptions and Values (#590) | 53 | +512 / −1461 |  |
| 2025-06-18 | `898dfa8912` | Other | IBX-9727: Fixed strict types of `MultiLanguageName` contracts (#599) | 5 | +20 / −57 |  |
| 2025-06-20 | `3771d73456` | Other | IBX-9941: Renamed contentclassattribute_id to content_type_field_definition_id (#582) | 30 | +14857 / −14857 |  |
| 2025-06-24 | `61e440bd8b` | Other | IBX-9727: Fixed strict types for Encore ConfigurationDumper class (#601) | 3 | +32 / −57 |  |
| 2025-06-24 | `07187f8a79` | Other | IBX-9060: Added API to filter notifications (#520) | 28 | +737 / −365 |  |
| 2025-06-24 | `6a7d2be9a2` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-06-24 | `1a24ebd80a` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-06-24 | `f649896bd5` | Merged | Merge remote-tracking branch 'origin/main' | 0 | +0 / −0 |  |
| 2025-06-25 | `25804db32f` | Other | IBX-9727: [Contracts][Tests] Fixed strict types of Test contracts (#602) | 10 | +119 / −303 |  |
| 2025-06-30 | `e8e73c770a` | Other | IBX-8697: Added validation for Keyword field type values in content and tests (#592) | 2 | +116 / −0 |  |
| 2025-06-30 | `75978a7adf` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |

## 2025-07 (37 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-07-01 | `167d710798` | Other | IBX-10167: Fixed translations export in abstract field type classes (#603) | 7 | +35 / −33 |  |
| 2025-07-02 | `3aff6dad8f` | Other | IBX-9980: Fixed displaying search engine in `ibexa:reindex` command (#607) | 2 | +35 / −2 |  |
| 2025-07-02 | `35b60242e4` | Other | IBX-10246: Made `FieldDefinitionCreateStruct` not translatable by default (#608) | 5 | +6 / −2 |  |
| 2025-07-03 | `792307c68b` | Other | IBX-9845: Added test skipping for Solr doesn't support shard URL (#593) | 1 | +28 / −10 |  |
| 2025-07-07 | `b49ff5935d` | Other | IBX-10229: Made old field type alias fallback to new one in searchable field map (#606) | 4 | +28 / −4 |  |
| 2025-07-07 | `6b7ba98e9f` | Other | IBX-9727: Added missing type hints to content related VO (#569) | 78 | +866 / −1047 |  |
| 2025-07-07 | `7ea74c5bd7` | Other | IBX-7801: Added constraint for new content to only be created inside content with container content type (#598) | 19 | +275 / −18 |  |
| 2025-07-07 | `d6bcd99f3f` | Other | IBX-7801: Skipped container validation for root location (#609) | 1 | +5 / −0 |  |
| 2025-07-08 | `998ee1559c` | Other | IBX-9727: Fixed strict types of ProxyCacheWarmer (#611) | 1 | +3 / −4 |  |
| 2025-07-08 | `bf8f7593fa` | Other | [CI] Moved Stub namespace to autoload from dev section (#612) | 1 | +2 / −2 |  |
| 2025-07-10 | `cc599a75b5` | Other | IBX-10253: Fixed condition checks for sending a file with BinaryStreamResponse (#614) | 3 | +87 / −6 |  |
| 2025-07-11 | `76bd7f550e` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-07-12 | `45b0c919b7` | Other | IBX-10254: Renamed ez_lock object state group to ibexa_lock (#615) | 16 | +51 / −51 |  |
| 2025-07-13 | `5b8100ec1b` | Other | IBX-10228: Bump symfony/* requirement to ^7.3 (#605) | 1 | +21 / −21 |  |
| 2025-07-15 | `d9b72df6b2` | Other | IBX-4470: Rebranded Flysystem filesystem service ID (#616) | 3 | +2 / −8 |  |
| 2025-07-16 | `0c57041b68` | Other | IBX-10283: Added urlAlias $id validation (#617) | 1 | +11 / −0 |  |
| 2025-07-17 | `91b213b38b` | Updated | Changed pattern to vhost compatible (#618) | 2 | +15 / −2 |  |
| 2025-07-17 | `8724fdad99` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2025-07-17 | `ad74bb4e7a` | Other | IBX-10334: Fallbacked to a new field type alias in the `DoctrineGatewayDataMapper` (#620) | 1 | +8 / −2 |  |
| 2025-07-20 | `2618149202` | Added | Added a condition to check whether script_filename is not empty (#621) | 3 | +20 / −10 |  |
| 2025-07-20 | `86eb31fcde` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-07-20 | `9107058813` | Other | [PHPStan] Regenerated phpstan baseline | 1 | +12 / −6 |  |
| 2025-07-21 | `656732c3e5` | Other | [Translations] Added missing translations (#622) | 3 | +371 / −11 |  |
| 2025-07-24 | `e45fcea17d` | Other | [CI] Fixed newly reported phpstan errors (#624) | 5 | +20 / −9 |  |
| 2025-07-24 | `17993a561c` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2025-07-24 | `658177d372` | Other | [CI] Adjusted baseline after merge up from 4.6 | 1 | +0 / −12 |  |
| 2025-07-24 | `0e1aa71f22` | Added | Added conflict with ezsystems/ezplatform-kernel (#625) | 1 | +1 / −3 |  |
| 2025-07-26 | `bdce6c0b4b` | Other | IBX-9060: Added TypedNotificationRenderer interface for notification type labeling (#610) | 3 | +99 / −0 |  |
| 2025-07-26 | `f40196b578` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-07-26 | `d24854c6c4` | Other | IBX-10159: Corrected generate dfs_database_url parameter for Ibexa Cloud (#595) | 3 | +48 / −2 |  |
| 2025-07-26 | `d3b8c8a71e` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-07-28 | `ffdbf94866` | Other | [CS] Resolved code style issues | 1 | +1 / −1 |  |
| 2025-07-28 | `7bbaf6972c` | Other | IBX-10116: Fixed `ibexa_render()` not using decorated fragment renders (#579) | 2 | +24 / −1 |  |
| 2025-07-29 | `0e3372e5d4` | Other | IBX-10263: Allowed hyphens in HostText and UriText siteaccess matchers (#623) | 3 | +8 / −2 |  |
| 2025-07-29 | `e483a90621` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2025-07-30 | `1c05b96cf7` | Other | IBX-9060: Updated StatusCriterionHandler to use `in` expression for multi-status support (#627) | 1 | +2 / −2 |  |
| 2025-07-30 | `946ff2f5f3` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |

## 2025-08 (29 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-08-01 | `00d2adee58` | Other | IBX-8811: Rebranded SiteAccess session prefix (#629) | 1 | +1 / −1 |  |
| 2025-08-05 | `566e5eeab6` | Other | Bumped Ibexa LTS version to v4.6.22 | 1 | +1 / −1 |  |
| 2025-08-05 | `986273005d` | Other | [PHPStan] Updated baseline after PHPStan release | 1 | +1 / −1 |  |
| 2025-08-06 | `d95c3cd931` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-08-06 | `c2c835f43f` | Other | Bumped Ibexa LTS version to v5.0.1 | 1 | +1 / −1 |  |
| 2025-08-06 | `8f1f61e394` | Other | IBX-9727: Fixed strict types of IO layer (#613) | 83 | +1132 / −2608 |  |
| 2025-08-11 | `5de840c7df` | Other | IBX-9060: Added bulk mark-as-read for user notifications (#630) | 15 | +326 / −18 |  |
| 2025-08-11 | `4e473cec62` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-08-11 | `d7bd6037c0` | Other | Refactored NotificationService and DoctrineDatabase for type safety (#632) | 4 | +22 / −31 |  |
| 2025-08-14 | `691bfdaf76` | Other | IBX-10428: Fixed invokable commands failing to compile Container (#634) | 2 | +20 / −1 |  |
| 2025-08-19 | `196e30c00d` | Other | Bumped Ibexa LTS version to v4.6.23 | 1 | +1 / −1 |  |
| 2025-08-19 | `78e6c7f668` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-08-20 | `8608e1e833` | Other | IBX-10507: Updated method signatures to use nullable type hints for improved code readability and consistency. (#636) | 196 | +341 / −447 |  |
| 2025-08-21 | `e132ce78a8` | Removed | Removed trailing comma in BinaryFileLister constructor (#637) | 1 | +6 / −15 |  |
| 2025-08-21 | `7e33dc6593` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-08-22 | `810f9cd8c9` | Updated | Refactor: Remove unnecessary `tearDown` methods and enhance type consistency across interfaces, constructors, and method definitions. Adjust tests accordingly. | 14 | +36 / −215 |  |
| 2025-08-22 | `58cf66162a` | Removed | Removed redundant `@throws` annotations and dead catch blocks, introduce cleanup in tearDown methods for improved test reliability. | 20 | +103 / −54 |  |
| 2025-08-22 | `6542fbfb13` | Other | Reverted removal of `tearDown` method | 1 | +12 / −0 |  |
| 2025-08-25 | `3905aa17c7` | Added | Added Throwable alias imports and update dependencies | 5 | +6 / −4 |  |
| 2025-08-25 | `e2c552fa96` | Removed | Removed `AbstractPropertyWhitelistNormalizer` empty file. | 1 | +0 / −0 |  |
| 2025-08-25 | `b25948bf76` | Removed | Removed empty files left after merging. | 2 | +0 / −0 |  |
| 2025-08-25 | `5eb3e83655` | Other | IBX-10428: Added `siteaccess` option to application definition, instead of per command (#635) | 6 | +53 / −98 |  |
| 2025-08-22 | `b111c4801e` | Other | Enhanced type consistency acrreoss intefaces, constructors, and method definitions | 33 | +27 / −165 |  |
| 2025-08-26 | `a6dea29c6b` | Other | Merged branch 'merg-4.6-to-main' (#640) | 0 | +0 / −0 |  |
| 2025-08-26 | `68a645842a` | Other | Merged branch 'merg-4.6-to-main-v2' (#640) | 0 | +0 / −0 |  |
| 2025-08-28 | `a09c0a5eed` | Other | IBX-10534: Ensured CriteriaConverter instances are lazy to prevent early connection initialization | 3 | +3 / −2 |  |
| 2025-08-28 | `200d5c2402` | Merged | Merge branch 'refs/heads/4.6' | 0 | +0 / −0 |  |
| 2025-08-28 | `25907f7d9a` | Other | Made CriteriaConverter handlers priority-aware | 3 | +83 / −96 |  |
| 2025-08-28 | `91c7a294b3` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |

## 2025-09 (16 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-09-03 | `76e77fe57b` | Added | Added `UniqueIdentifier` abstract constraint to reduce code duplication | 3 | +280 / −0 |  |
| 2025-09-03 | `d206650bf6` | Other | [PHPStan] Added missing throw doc (#645) | 1 | +1 / −0 |  |
| 2025-09-03 | `dc2fcd70c8` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-09-08 | `284c70c5fa` | Other | Bumped Ibexa LTS version to v4.6.24 | 1 | +1 / −1 |  |
| 2025-09-08 | `5e477c5079` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-09-08 | `c83571e0c8` | Other | Bumped Ibexa LTS version to v5.0.2 | 1 | +1 / −1 |  |
| 2025-09-11 | `79649c3d75` | Updated | Fixed issues uncovered by PHPStan v2.1.23 (#649) | 14 | +247 / −459 |  |
| 2025-09-12 | `db39fd7746` | Updated | Fixed the incorrect type hint for `CreateStruct::$mainLocationId` (#648) | 6 | +6 / −24 |  |
| 2025-09-12 | `ea58659e54` | Other | IBX-9446: Fixed updating new non-translatable field value (#533) | 2 | +150 / −1 |  |
| 2025-09-12 | `f1a4c26ff7` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-09-12 | `a56a5391b5` | Other | [i18n] Inlined PHPStan `TIOHandlersMap` to fix translation extraction (#647) | 1 | +2 / −4 |  |
| 2025-09-19 | `024b97df5c` | Other | IBX-10657: Fixed invalid IO services definitions (#651) | 1 | +13 / −17 |  |
| 2025-09-22 | `ffe3397406` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2025-09-23 | `2ba7fdfea8` | Other | [CI] Fixed PHPStan issues (#654) | 4 | +105 / −7 |  |
| 2025-09-23 | `c4b3f76f54` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-09-24 | `f2f0db0718` | Other | IBX-10669: Fixed accessing `isContainer` property for CT drafts (#653) | 4 | +39 / −2 |  |

## 2025-10 (27 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-10-03 | `31835d7bae` | Other | IBX-10597: Fixed accessing uninitialized image variation properties when switching to the original variation (#652) | 9 | +48 / −200 |  |
| 2025-10-03 | `62929022f0` | Other | IBX-10458: Implemented new Content Type search PHP API (#633) | 44 | +1581 / −9 |  |
| 2025-10-05 | `7e8c8ec420` | Merged | Merge branch '4.6' into main | 0 | +0 / −0 |  |
| 2025-10-06 | `a800188e2b` | Merged | Merge branch '4.6' into main | 0 | +0 / −0 |  |
| 2025-10-06 | `fca1340d33` | Other | [CI] Bumped actions' versions + regenerated PHPStan baseline (#657) | 2 | +22 / −22 |  |
| 2025-10-06 | `1870a585af` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-10-14 | `2a440c3387` | Other | [PHPStan] Regenerated PHPStan baseline (#658) | 10 | +101 / −273 |  |
| 2025-10-14 | `201cda6db5` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-10-14 | `327d1e1b58` | Removed | Removed accidentally commited file | 1 | +0 / −13 |  |
| 2025-10-14 | `b9759a8a8b` | Updated | Fixed code style | 1 | +0 / −1 |  |
| 2025-10-15 | `91fa7b02a1` | Other | Bumped Ibexa LTS version to v4.6.25 | 1 | +1 / −1 |  |
| 2025-10-15 | `14616b75bf` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-10-15 | `38bd5add2c` | Other | Bumped Ibexa LTS version to v5.0.3 | 1 | +1 / −1 |  |
| 2025-10-15 | `127929d168` | Updated | Updated validator translation file with new identifier resource definition (#659) | 1 | +5 / −5 |  |
| 2025-10-15 | `2e5e0d7a85` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-10-15 | `7a8ac5f617` | Other | [GHA][Browser tests] Switched to inherited secrets (#660) | 1 | +1 / −5 |  |
| 2025-10-15 | `d5827d048b` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-10-24 | `f84460a934` | Other | [CS] Bumped Ibexa Code Style to v2.1.0 (#665) | 6 | +8 / −8 |  |
| 2025-10-28 | `b7cf5f0235` | Other | [Composer] Added conflict with doctrine/orm:2.20.7 due to broken Enti… (#669) | 1 | +1 / −0 |  |
| 2025-10-28 | `06c873c2b1` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-10-22 | `f681b7a5ee` | Other | IBX-10840: Fixed missing logger in ImageExtension | 2 | +6 / −2 |  |
| 2025-10-22 | `fd2c23a2d7` | Other | Enforced PHPStan rule against dynamic properties | 21 | +54 / −45 |  |
| 2025-10-22 | `9a0206bc13` | Updated | Fixed `empty` usage against `AuthorCollection` object | 2 | +1 / −7 |  |
| 2025-10-30 | `ee99bd415d` | Other | [Tests][PHPStan] Added missing property.notFound exclusions | 10 | +14 / −4 |  |
| 2025-10-30 | `cb05032a42` | Other | [PHPStan] Removed resolved issues from the baseline | 2 | +12 / −138 |  |
| 2025-10-30 | `25cdd62c37` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-10-30 | `4b60242876` | Other | [PHPStan] Added extra validation of `filesize` result in BinaryBase\Type | 1 | +11 / −1 |  |

## 2025-11 (10 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-11-04 | `9de4817ace` | Other | IBX-10627: Fixed transformation ForbiddenException to AccessDeniedHttpException (#673) | 3 | +129 / −133 |  |
| 2025-11-04 | `6d945e20e7` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2025-11-05 | `163b111e04` | Other | IBX-10865 set correct type for Content-Length header (#671) | 2 | +1 / −7 |  |
| 2025-11-07 | `d1bfbf7bb5` | Other | IBX-10937: Add the `ContentTypeGroupName` criterion for `findContentTypes` PAPI (#675) | 3 | +113 / −0 |  |
| 2025-11-07 | `97383b02f4` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-11-19 | `fb60a60d64` | Other | IBX-10936: Made `ProxyDomainMapperFactory` lazy (#676) | 15 | +68 / −147 |  |
| 2025-11-19 | `af51f17c50` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-11-19 | `c874e94837` | Other | [CI] Fixed merge-up of IBX-10936 (#680) | 1 | +1 / −1 |  |
| 2025-11-20 | `40159cb3d7` | Other | [CI] Made `Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapper` lazy (#681) | 1 | +1 / −0 |  |
| 2025-11-28 | `3d351949b4` | Other | Temporarily disable symfony/validator 7.4 | 1 | +2 / −1 |  |

## 2025-12 (20 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-12-02 | `d156ccc0da` | Other | [Doctrine ORM] Changed entity mappings type to attribute (#685) | 1 | +1 / −1 |  |
| 2025-12-03 | `0589421bd9` | Other | [DI] Dropped unknown NameSchema services (#686) | 1 | +0 / −4 |  |
| 2025-12-01 | `62b0741194` | Other | [PHPDoc] Fixed site access system config reference after rebranding | 4 | +5 / −10 |  |
| 2025-12-01 | `638d38b638` | Other | [PHPDoc] Dropped redundant `ParserInterface::addSemanticConfig` doc blocks | 7 | +0 / −35 |  |
| 2025-12-02 | `c6385a6972` | Other | Defined TParent of Symfony semantic configuration builders | 30 | +194 / −162 |  |
| 2025-12-02 | `315ebae997` | Other | [PHPStan] Aligned baseline with Symfony config TParent alignment | 1 | +7 / −295 |  |
| 2025-12-03 | `b864345e34` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-12-04 | `6e92721b7a` | Other | IBX-9846: Added search using embeddings (#536) | 29 | +1413 / −1 |  |
| 2025-12-04 | `4d75abcf87` | Other | Merged branch '4.6' into main | 0 | +0 / −0 |  |
| 2025-12-09 | `70c356dd86` | Other | Bumped Ibexa LTS version to v4.6.26 | 1 | +1 / −1 |  |
| 2025-12-09 | `aa19cd52c6` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |
| 2025-12-09 | `d9fd9e5ef1` | Other | Bumped Ibexa LTS version to v5.0.4 | 1 | +1 / −1 |  |
| 2025-12-11 | `bc809dce8e` | Other | IBX-10233: Fixed iterating over a batch of files in `ibexa:io:migrate-files` command (#661) | 1 | +4 / −2 |  |
| 2025-12-11 | `61322fb015` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2025-12-17 | `8732ba9e55` | Other | IBX-10841: Fixed sorting in content filtering (#655) | 23 | +633 / −174 |  |
| 2025-12-17 | `393f7d9b9c` | Merged | Merge branch '4.6' | 23 | +637 / −172 |  |
| 2025-12-15 | `86a4b8900a` | Other | [Rector] Replaced andX with and in query builders | 2 | +2 / −2 |  |
| 2025-12-17 | `077fc6d783` | Added | Added missing type hints to constants | 1 | +2 / −2 |  |
| 2025-12-17 | `c8beb8271c` | Other | [Composer] Dropped unused guzzle dependencies (#691) | 1 | +0 / −2 |  |
| 2025-12-17 | `237c5b027d` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |

## 2026-01 (17 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-01-07 | `0bb102f762` | Other | IBX-10708: Introduced SerializerFactory for SiteAccess matchers (#689) | 2 | +88 / −33 |  |
| 2026-01-09 | `f1d4c2b4a6` | Other | IBX-11148: Moved autoconfigure setting from routing.yml to serializers.yml for CompoundMatcherNormalizer (#698) | 2 | +1 / −3 |  |
| 2026-01-15 | `0c7784d080` | Other | Bumped Ibexa LTS version to v5.0.5 | 1 | +1 / −1 |  |
| 2026-01-15 | `335e714baf` | Other | Bumped cleandata release marker to v5.0 | 2 | +2 / −2 |  |
| 2026-01-19 | `e1c241502e` | Other | IBX-10964: Fixed PHPDoc annotation in AggregationResultCollection (#678) | 2 | +1 / −7 |  |
| 2026-01-19 | `2968aa27a4` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2026-01-19 | `868367d6fa` | Other | Bumped Ibexa LTS version to v5.0.6 | 1 | +1 / −1 |  |
| 2026-01-21 | `0650d1120e` | Other | IBX-8897: Fixed incorrect extrapolation of parentheses in the name schema pattern (#449) | 2 | +86 / −41 |  |
| 2026-01-21 | `28df8edf66` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2026-01-22 | `ecd91f9307` | Other | IBX-11131: Fixed streaming files for apache + php-fpm (#695) | 2 | +11 / −15 |  |
| 2026-01-22 | `a2f6335c4b` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2026-01-26 | `8931fd055b` | Other | IBX-11116: Added EmbeddingProviderException and an interface for embedding providers' error handling (#697) | 3 | +38 / −0 |  |
| 2026-01-26 | `05ef6f97b5` | Merged | Merge remote-tracking branch 'origin/4.6' into main | 0 | +0 / −0 |  |
| 2026-01-26 | `3194ce7093` | Other | [Composer] Added conflict with symfony/finder 7.3.10 \|\| 7.4.4 (#705) | 1 | +2 / −1 |  |
| 2026-01-29 | `98253e9f02` | Other | IBX-11130: Fixed proxy initializer returning `null` (#693) | 3 | +74 / −11 |  |
| 2026-01-29 | `32778cb27b` | Other | Bumped Ibexa LTS version to v4.6.27 | 1 | +1 / −1 |  |
| 2026-01-29 | `6ccb8b0a5a` | Other | Merged branch '4.6' | 0 | +0 / −0 |  |

## 2026-02 (14 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-02-09 | `42ff60ae65` | Other | IBX-10186: Added limits to Repository Filtering count and subtree queries (#696) | 33 | +336 / −69 |  |
| 2026-02-16 | `d172474467` | Other | IBX-11146: Test for solr search with many (50+) languages (#703) | 1 | +61 / −0 |  |
| 2026-02-16 | `24aa8868ae` | Merged | Merge remote-tracking branch 'origin/4.6' | 0 | +0 / −0 |  |
| 2026-02-16 | `8be1cbc5f5` | Other | IBX-9275: Added locale Igbo (Nigeria) (#711) | 1 | +2 / −0 |  |
| 2026-02-16 | `4a0683c97c` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2026-02-16 | `80a93b2448` | Other | [CI] Fixed wrong(old) base case test class (#712) | 1 | +1 / −1 |  |
| 2026-02-25 | `8383842972` | Updated | Fixed UrlAlias class name case in URLAliasService and clean phpstan baseline (#715) | 2 | +1 / −7 |  |
| 2026-02-25 | `b99a8a24ae` | Other | IBX-11179: Resolved PHP 8.4 lazy proxy incompatibility (#707) | 3 | +319 / −3 |  |
| 2026-02-25 | `b5b7c6b5ae` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2026-02-25 | `003421fe72` | Other | IBX-11397: Added resolving embedding provider by model identifier for taxonomy embeddings (#714) | 3 | +51 / −0 |  |
| 2026-02-25 | `0b8f946cd6` | Merged | Merge remote-tracking branch 'origin/4.6' into main | 0 | +0 / −0 |  |
| 2026-02-25 | `235575c0e2` | Removed | Removed incorrect case reference for URLAlias in PHPStan baseline | 1 | +0 / −6 |  |
| 2026-02-25 | `3f3569951c` | Other | Corrected case of URLAlias in UrlAliasRouter and updated phpstan baseline (#716) | 1 | +1 / −1 |  |
| 2026-02-26 | `fac75f3881` | Other | IBX-11179: Updated type hints in SiteAccessAwareEntityManager (#717) | 1 | +24 / −6 |  |

## 2026-03 (19 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `d5db4fd5d2` | Other | IBX-10764: Removed dependencies to Upsun (#672) | 5 | +7 / −309 |  |
| 2026-03-03 | `f1e65715d9` | Other | Provided cross-compatibility between doctrine/persistence v2 and v3 (#720) | 6 | +69 / −30 |  |
| 2026-03-03 | `667ed21f9f` | Other | Bumped Ibexa LTS version to v4.6.28 | 1 | +1 / −1 |  |
| 2026-03-03 | `94271b4469` | Other | Merged branch '4.6' into main | 0 | +0 / −0 |  |
| 2026-03-05 | `33ca7696af` | Other | IBX-11272: Fixed anonymous access to SA without `user`/`login` policy | 6 | +413 / −2 |  |
| 2026-03-05 | `d3e03942de` | Other | Merged branch 'main' of ibexa/core-f51653cc | 0 | +0 / −0 |  |
| 2026-03-06 | `beece8cb31` | Other | IBX-11259: Fixed download file by id by additionally handling queries version and language (#718) | 1 | +8 / −1 |  |
| 2026-03-06 | `db093b323d` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2026-03-06 | `2bb8931028` | Other | IBX-3035: [Twig] Allowed passing parameters to `ibexa render` function (#674) | 7 | +89 / −56 |  |
| 2026-03-06 | `1bf2fda92b` | Other | Merged branch '4.6' into main | 0 | +0 / −0 |  |
| 2026-03-10 | `d01b203204` | Other | IBX-11401: Fixed embedding default model inheritance and added field name fallback prefixes (#719) | 6 | +120 / −37 |  |
| 2026-03-11 | `06eb4ae9d1` | Other | IBX-11179: Updated PHP versions in CI configuration with 8.3 and 8.4 (#724) | 20 | +313 / −228 |  |
| 2026-03-11 | `1f6aba2ff0` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |
| 2026-03-12 | `355d885409` | Other | Adjust phpunit and phpstan setup after merge to main | 15 | +69 / −146 |  |
| 2026-03-12 | `37941fbad3` | Merged | Merge up/IBX-11179 php 8.4 certification (#727) | 0 | +0 / −0 |  |
| 2026-03-17 | `5e9cbcc5fb` | Other | IBX-11247: Bumped `symfony/*` to 7.4 LTS (#709) | 2 | +35 / −36 |  |
| 2026-03-17 | `be75e34a17` | Other | [Composer] Fixed `jms-translation-bundle` dependency (#729) | 1 | +1 / −1 |  |
| 2026-03-18 | `9275d10b14` | Other | IBX-11383: Hash image file in a deterministic way during storing to avoid pilling up duplicates (#725) | 5 | +132 / −6 |  |
| 2026-03-18 | `2f4870d7d7` | Merged | Merge branch '4.6' | 0 | +0 / −0 |  |

## 2026-04 (4 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-29 | `d8c0db034a` | Updated | Updated: skip doctrine:database:create for SQLite in checkCreateDatabase() | 1 | +21 / −0 |  |
| 2026-04-29 | `0b979991e1` | Updated | Updated: use instanceof to resolve DBMS platform name in getKernelSQLFileForDBMS() | 1 | +7 / −1 |  |
| 2026-04-29 | `5f861df99e` | Updated | Updated: substitute SqliteDbPlatform in importSchema() to fix composite-PK AUTOINCREMENT on SQLite | 1 | +10 / −0 |  |
| 2026-04-29 | `93f175106a` | Added | Added: data/sqlite/cleandata.sql — SQLite-compatible seed data for ibexa:install ibexa-oss | 1 | +322 / −0 |  |
