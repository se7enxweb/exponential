# Change ledger: site-bundle

Every change made to `site-bundle` since the se7enxweb era began, oldest first: 105 changes touching 139 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Other | 82 |
| Updated | 14 |
| Merged | 8 |
| Released | 1 |

## 2023-12 (13 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2023-12-11 | `2828c52` | Other | NGSTACK-813 created configuration and logic for choosing between inline representation and automatic download | 4 | +23 / −5 |  |
| 2023-12-11 | `5b96fa1` | Other | NGSTACK-813 php-cs-fixed | 1 | +5 / −3 |  |
| 2023-12-11 | `4f271db` | Other | NGSTACK-813 option for default behaviour occurs on null instead of -1 | 2 | +4 / −4 |  |
| 2023-12-13 | `5936d90` | Other | Skip invisible items when building menu | 1 | +14 / −0 |  |
| 2023-12-13 | `ab08fc6` | Merged | Merge pull request #47 from netgen/check-invisible-items | 0 | +0 / −0 | 3.1.17 |
| 2023-12-15 | `fe3ac0d` | Other | NGSTACK-813 Download Controller PHPDoc modified | 1 | +1 / −1 |  |
| 2023-12-15 | `2c3b92f` | Other | NGSTACK-813 Download Controller PHPDoc modified | 1 | +1 / −1 |  |
| 2023-12-15 | `64d55c3` | Other | NGSTACK-813 added PHPDoc to specify array type | 1 | +3 / −0 |  |
| 2023-12-15 | `f877841` | Other | NGSTACK-813 unintended indentation fixed | 1 | +1 / −1 |  |
| 2023-12-15 | `597abf3` | Other | NGSTACK-813 comment moved above parameter instead of beaing under | 1 | +2 / −2 |  |
| 2023-12-15 | `4fb0b70` | Merged | Merge pull request #46 from netgen/NGSTACK-813-add-configuration-for-specifing-inline-vs-download-for-mime-type | 0 | +0 / −0 | 3.1.18 |
| 2023-12-29 | `825e7db` | Other | NGSTACK-822 sorting for standard netgen site search implemented | 2 | +63 / −12 |  |
| 2023-12-29 | `7f13742` | Other | NGSTACK-822 SearchQueryType fixed with php-cs-fixer | 1 | +45 / −37 |  |

## 2024-01 (6 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-26 | `30ed45d` | Updated | Update PHP CS Fixer rules | 1 | +1 / −0 |  |
| 2024-01-25 | `4121347` | Other | IOTA-384 simplify and rename compiler pass | 3 | +25 / −37 |  |
| 2024-01-25 | `b31893d` | Other | IOTA-384 rename parameter | 2 | +3 / −4 |  |
| 2024-01-26 | `7569199` | Other | IOTA-384 delete unused class | 1 | +0 / −149 |  |
| 2024-01-26 | `089eac0` | Merged | Merge pull request #45 from netgen/IOTA-384-direct-download-richtext-link | 0 | +0 / −0 |  |
| 2024-01-26 | `89988ca` | Other | Master is 3.2 | 1 | +1 / −1 | 3.2.0 |

## 2023-10 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2023-10-26 | `55c0024` | Other | IOTA-384 add support for direct download in richtext links | 6 | +196 / −10 |  |
| 2023-10-26 | `cceee4b` | Other | IOTA-384 make class final | 1 | +1 / −1 |  |

## 2024-02 (6 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-02-08 | `b98525e` | Other | NGSTACK-673: update for breaking change in Site API | 3 | +3 / −3 |  |
| 2024-02-08 | `f7224cf` | Merged | Merge pull request #49 from netgen/NGSTACK-673_path_array | 0 | +0 / −0 | 3.2.1 |
| 2024-02-07 | `cf8fd66` | Other | NGSTACK-822 type hint to array parameter of function added | 1 | +4 / −0 |  |
| 2024-02-07 | `4b4245b` | Other | NGSTACK-822 static function made non static where this is needed | 1 | +2 / −1 |  |
| 2024-02-07 | `dfdd3af` | Other | NGSTACK-822 switched functons order to avoid big git diff | 1 | +25 / −25 |  |
| 2024-02-08 | `a70fcb9` | Other | NGSTACK-822 switch from string mapped to sort clause to FQN | 2 | +4 / −25 |  |

## 2024-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-03-20 | `61bdf0b` | Other | Cast the location ID to int | 1 | +1 / −1 | 3.2.2 |

## 2024-07 (47 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-07-19 | `d3fcdf3` | Updated | Fix download controller when attempting to download an image, fixes #52 | 1 | +2 / −3 |  |
| 2024-07-19 | `cbaa44c` | Updated | Fix issues with PHPStan | 4 | +4 / −1 |  |
| 2024-07-19 | `ed94ea3` | Updated | Fix CS | 1 | +1 / −1 |  |
| 2024-07-19 | `8d92dc1` | Updated | Update PHPStan rules | 1 | +0 / −1 | 3.2.3 |
| 2024-07-23 | `8515255` | Other | NGSTACK-811 add tag content command | 3 | +198 / −0 |  |
| 2024-07-23 | `723eded` | Other | NGSTACK-811 fix code styling TagContentByTypesCommand.php | 1 | +2 / −3 |  |
| 2024-07-23 | `c2ccf4d` | Other | NGSTACK-811 assing tag to content in batches | 1 | +51 / −37 |  |
| 2024-07-23 | `2516943` | Other | NGSTACK-811 add empty line before return | 1 | +1 / −0 |  |
| 2024-07-23 | `76ae62d` | Other | NGSTACK-811 move field identifier logic out of loop | 1 | +6 / −8 |  |
| 2024-07-23 | `14aef55` | Other | NGSTACK-811 use sudo only for parts that require it | 1 | +24 / −30 |  |
| 2024-07-23 | `efdb0cc` | Other | NGSTACK-811 call getTag method only once | 1 | +3 / −2 |  |
| 2024-07-23 | `872ee9f` | Other | NGSTACK-811 add transaction inside loop | 1 | +35 / −22 |  |
| 2024-07-23 | `c45ef64` | Other | NGSTACK-811 add empty line parameters.yaml | 1 | +1 / −1 |  |
| 2024-07-23 | `8d1482b` | Other | NGSTACK-811 add empty line commands.yaml | 1 | +1 / −1 |  |
| 2024-07-23 | `2887c4c` | Other | NGSTACK-811 allow only one field identifier | 2 | +34 / −45 |  |
| 2024-07-23 | `0ab8f8e` | Other | NGSTACK-811 don't try to access input field-identifier unnecessary | 1 | +3 / −2 |  |
| 2024-07-23 | `ab29c2a` | Other | NGSTACK-811 fix code styling | 1 | +2 / −3 |  |
| 2024-07-24 | `bd3915a` | Other | NGSTACK-811 remove getTag method | 1 | +1 / −6 |  |
| 2024-07-24 | `59a7508` | Other | NGSTACK-811 add empty line between properties | 1 | +1 / −0 |  |
| 2024-07-24 | `19f0e1b` | Other | NGSTACK-811 remove column alignment in constructor | 1 | +2 / −2 |  |
| 2024-07-24 | `3dd233e` | Other | NGSTACK-811 remove input property | 1 | +14 / −22 |  |
| 2024-07-24 | `3200afb` | Other | NGSTACK-811 inject ContentService and SearchService | 2 | +12 / −6 |  |
| 2024-07-24 | `a0d5652` | Other | NGSTACK-811 remove default field identifier | 3 | +3 / −13 |  |
| 2024-07-24 | `5d773e1` | Other | NGSTACK-811 do not stop command if field identifier is not typeof eztags | 1 | +2 / −5 |  |
| 2024-07-24 | `72dc9df` | Other | NGSTACK-811 add static to arrow function | 1 | +1 / −1 |  |
| 2024-07-24 | `ed2b379` | Other | NGSTACK-811 make method getParentLocationPrivate | 1 | +1 / −1 |  |
| 2024-07-24 | `c86eaf6` | Other | NGSTACK-811 make method getTagId | 1 | +1 / −1 |  |
| 2024-07-24 | `7660f8e` | Other | NGSTACK-811 make method getContentTypes | 1 | +1 / −1 |  |
| 2024-07-24 | `e5f99dd` | Other | NGSTACK-811 remove unnecessary hasField method | 1 | +1 / −8 |  |
| 2024-07-24 | `91ba9de` | Other | NGSTACK-811 solve php stan errors | 1 | +8 / −5 |  |
| 2024-07-24 | `b54b2e4` | Other | NGSTACK-811 add return type to getContentTypes method | 1 | +3 / −0 |  |
| 2024-07-24 | `243b184` | Other | NGSTACK-811 fix code styling | 1 | +3 / −4 |  |
| 2024-07-25 | `b696439` | Other | NGSTACK-811 move transaction out of batch level | 1 | +4 / −3 |  |
| 2024-07-25 | `fbca72c` | Other | NGSTACK-811 remove "Input" suffix from variables | 1 | +10 / −10 |  |
| 2024-07-25 | `80319d9` | Other | NGSTACK-811 make argument of parseCommaDelimited method not nullable | 1 | +2 / −2 |  |
| 2024-07-25 | `3a34186` | Other | NGSTACK-811 move transaction to content level | 1 | +4 / −4 |  |
| 2024-07-25 | `b4de0ec` | Other | NGSTACK-811 fix code styling | 1 | +0 / −1 |  |
| 2024-07-25 | `0308d30` | Other | NGSTACK-811 move transaction commit inside try and skip content that already has tag assigned | 1 | +26 / −23 |  |
| 2024-07-25 | `c7b85f0` | Other | NGSTACK-811 fix code styling | 1 | +3 / −2 |  |
| 2024-07-25 | `ce20a57` | Other | NGSTACK-811 add use count | 1 | +1 / −0 |  |
| 2024-07-25 | `a887f31` | Merged | Merge pull request #53 from netgen/NGSTACK-811-taga-content-command | 0 | +0 / −0 |  |
| 2024-07-25 | `21352e5` | Other | Be more clear when skipping content with tag already present | 1 | +15 / −14 |  |
| 2024-07-25 | `ecc5d5b` | Other | CS fixes | 1 | +3 / −3 |  |
| 2024-07-26 | `4c6770a` | Other | Support Twig 3.9 for template start and end markup | 2 | +29 / −32 | 3.3.0 |
| 2024-07-26 | `19c80ca` | Other | CS fixes | 1 | +2 / −4 |  |
| 2024-07-26 | `807c2e8` | Updated | Refactor support for template start and stop markup to use yield | 1 | +30 / −26 | 3.3.1 |
| 2024-07-30 | `c06a504` | Updated | Fix branch alias | 1 | +1 / −1 |  |

## 2024-08 (4 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-08-28 | `98e8d7d` | Other | NGSTACK-822 logic of ordering changes, string parameter replaced by QN | 1 | +2 / −12 |  |
| 2024-08-28 | `a777492` | Other | NGSTACK-822 logic of ordering changes, string parameter replaced by QN | 1 | +1 / −1 |  |
| 2024-08-28 | `b5debe8` | Other | NGSTACK-822 added check if sort key class exists | 1 | +2 / −1 |  |
| 2024-08-28 | `90ae053` | Other | NGSTACK-822 php-cs-fixed | 1 | +4 / −4 |  |

## 2024-09 (16 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-09-02 | `61fa53c` | Updated | Fix compatibility with Twig 3.12 | 1 | +3 / −3 |  |
| 2024-09-02 | `b945c84` | Updated | Fix PHPStan errors | 2 | +4 / −3 | 3.3.2 |
| 2024-09-03 | `f182721` | Other | NGSTACK-822 removed variable that is only used once | 1 | +2 / −2 |  |
| 2024-09-03 | `90259bf` | Other | NGSTACK-822 accidantely deleted newline returned | 1 | +1 / −0 |  |
| 2024-09-03 | `5bf05f1` | Other | NGSTACK-822 unnecessary returns in PHPDoc removed | 1 | +1 / −3 |  |
| 2024-09-03 | `d0a1408` | Other | NGSTACK-822 sort keys nomenclature changed | 1 | +8 / −8 |  |
| 2024-09-04 | `ca7fe2f` | Other | NGSTACK-822: Fix PHPStan issues | 1 | +12 / −2 |  |
| 2024-09-04 | `8f77b8e` | Merged | Merge pull request #48 from netgen/NGSTACK-822-adding-sorting-option-to-netgen-site-search | 0 | +0 / −0 |  |
| 2024-09-06 | `a4b2381` | Other | Restore compatibility with Twig 3.11 | 5 | +125 / −72 | 3.3.3 |
| 2024-09-09 | `c68a928` | Other | Compatibility with Twig 3.13 | 3 | +7 / −2 | 3.3.4 |
| 2024-09-09 | `71ee8b3` | Updated | Update phpstan | 1 | +1 / −5 |  |
| 2024-09-24 | `29ad27d` | Other | NGSTACK-918 adjust ShortcutExtension to work with new link field (ngenhancedlink) | 2 | +28 / −21 |  |
| 2024-09-24 | `6f9ce3e` | Other | NGSTACK-918 add netgen/ibexa-fieldtype-enhanced-link as dependency | 1 | +1 / −0 |  |
| 2024-09-24 | `30170d7` | Other | NGSTACk-918 fix invalid variable name | 1 | +1 / −1 |  |
| 2024-09-24 | `ff3d23d` | Merged | Merge pull request #55 from netgen/NGSTACK-918-menu-shortcut-enhanced-link | 0 | +0 / −0 | 3.3.5 |
| 2024-09-30 | `aa6af7a` | Other | CS fixes | 1 | +21 / −8 |  |

## 2025-05 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-05-22 | `41079d3` | Updated | Update CS fixer rules | 1 | +1 / −0 |  |
| 2025-05-22 | `f41dac6` | Other | Implement command to generate dynamic showcases | 8 | +743 / −1 |  |

## 2025-06 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-06-02 | `2135430` | Other | Conflict with older versions of netgen/layouts-ibexa | 1 | +3 / −0 |  |
| 2025-06-04 | `b8dda23` | Updated | Update master to 3.4 | 1 | +1 / −1 |  |
| 2025-06-04 | `804be60` | Released | Bump PHP to 8.2 | 2 | +2 / −2 | 3.4.0 |

## 2025-10 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-10-01 | `4b60ca2` | Other | NGSTACK-826 change ibexa Visibility criterion to Visible criterion from ibexa-search-extra bundle | 1 | +2 / −1 |  |
| 2025-10-02 | `ae5d21e` | Other | NGSTACK-826 add 'ibexa-search-extra' bundle as dependency in composer.json | 1 | +2 / −1 |  |
| 2025-10-02 | `28cd825` | Merged | Merge pull request #57 from netgen/NGSTACK-826-use-Visible-criterion-for-Location-Query-from-search-extra | 0 | +0 / −0 |  |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-17 | `cf54aee` | Updated | Updated: Replaced vendor infos. Rebranding. | 1 | +8 / −4 | 3.0.5.0 |

## 2026-04 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-19 | `64e6c89` | Updated | fix: add replace netgen/site-bundle:* — prevent dual install with upstream fork | 1 | +3 / −0 | 3.0.6 origin/3.x |
