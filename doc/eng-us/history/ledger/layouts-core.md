# Change ledger: layouts-core

Every change made to `layouts-core` since the se7enxweb era began, oldest first: 450 changes touching 6363 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Other | 221 |
| Updated | 93 |
| Removed | 63 |
| Added | 36 |
| Renamed | 17 |
| Released | 15 |
| Merged | 5 |

## 2024-09 (9 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-09-06 | `03acf1f33` | Updated | Fix CS | 41 | +65 / −69 |  |
| 2024-09-06 | `4edf0f383` | Updated | Fix PHPStan in tests | 2 | +5 / −3 |  |
| 2024-09-06 | `7984d66df` | Updated | Update API tests | 6 | +6 / −6 |  |
| 2024-09-06 | `aeeb092cc` | Updated | Update Psalm config | 1 | +8 / −0 |  |
| 2024-09-06 | `0c9f786c1` | Updated | Fix issue in PHPStan | 1 | +5 / −0 |  |
| 2024-09-06 | `40987764e` | Updated | Fix deprecation in Twig 3.12 | 7 | +27 / −12 |  |
| 2024-09-06 | `e22c56d82` | Merged | Merge pull request #33 from netgen-layouts/twig39 | 0 | +0 / −0 | 1.4.10 |
| 2024-09-06 | `d4224578b` | Updated | Update prepare behat test script | 1 | +2 / −2 |  |
| 2024-09-06 | `fd4c359ac` | Updated | Update prepare behat test script | 1 | +1 / −1 |  |

## 2024-05 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-05-21 | `79b509c4a` | Other | Support yield rendering strategy in Twig 3.9+ | 10 | +106 / −18 |  |

## 2025-02 (4 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-02-10 | `0206f2477` | Other | Expose block definition plugin classes | 7 | +127 / −0 |  |
| 2025-02-10 | `21ea622d7` | Added | Add a DynamicParameter class to declare which dynamic parameters a handler has | 1 | +18 / −0 |  |
| 2025-02-10 | `925caf9b5` | Other | Enable iterating over dynamic parameters | 1 | +10 / −1 |  |
| 2025-02-10 | `2794571cd` | Removed | Remove obsolete phpstan rules | 1 | +0 / −1 | 1.4.11 |

## 2025-07 (19 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-07-10 | `476395363` | Other | tweak ajax-paging.js to fix accessibility focus issues | 1 | +13 / −2 |  |
| 2025-07-10 | `9af6b762b` | Other | tweak ajax-paging.js to remove optional chaining | 1 | +9 / −7 |  |
| 2025-07-10 | `8f63cb75c` | Other | Force minimatch to version 9 to fix frontend build | 1 | +3 / −0 |  |
| 2025-07-10 | `3fa4c576f` | Merged | Merge pull request #34 from netgen-layouts/ajax-paging-accessibility | 0 | +0 / −0 |  |
| 2025-07-10 | `2a7966804` | Other | Frontend build for 1.4.12 | 1 | +0 / −0 | 1.4.12 |
| 2025-07-11 | `4d6cfd0bb` | Removed | Remove Psalm | 4 | +5 / −242 |  |
| 2025-07-11 | `5b914364c` | Updated | Fix tests and deprecations with newer Twig versions | 8 | +71 / −17 |  |
| 2025-07-11 | `575e8943d` | Added | Add Twig\Environment::VERSION_ID to dynamic constants for tests phpstan | 1 | +1 / −0 |  |
| 2025-07-11 | `88497c45f` | Updated | Fix PHPStan issue | 2 | +9 / −0 |  |
| 2025-07-11 | `e38e78a52` | Updated | Fix tests on PHP 7.4 | 8 | +24 / −17 |  |
| 2025-07-11 | `8791b0f93` | Other | Try to improve MySQL performance in tests | 1 | +6 / −0 |  |
| 2025-07-11 | `5c90162ed` | Other | Downgrade minimatch to version 3 | 1 | +1 / −1 |  |
| 2025-07-14 | `2320946b9` | Released | Bump tests to PHP 8.4 | 5 | +8 / −10 |  |
| 2025-07-15 | `bb12b1e1f` | Renamed | Move SYMFONY_DEPRECATIONS_HELPER variable to Composer config | 3 | +0 / −6 |  |
| 2025-07-15 | `58f8187d7` | Other | Use pre-command-run Composer script to set env variable | 1 | +1 / −0 |  |
| 2025-07-15 | `4305fd3ab` | Other | Make CI use Composer scripts to run tests | 2 | +13 / −12 |  |
| 2025-07-15 | `3c95d79a2` | Removed | Remove support for Symfony 6.2 and 6.3 | 1 | +29 / −29 |  |
| 2025-07-16 | `3c525cd18` | Other | Make LinkValue internal properties only accept strings | 11 | +86 / −81 |  |
| 2025-07-16 | `14f39b6ea` | Other | Upgrade PHPStan to 2.x | 26 | +82 / −77 |  |

## 2025-09 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-09-08 | `d89ff1fd2` | Other | Use include Twig function instead of tag | 4 | +16 / −16 |  |
| 2025-09-08 | `5a02c8595` | Removed | Remove obsolete @var tag | 1 | +0 / −1 |  |

## 2025-10 (25 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-10-28 | `3ef20e9de` | Released | Bump PHP to 8.4 | 3 | +2 / −9 |  |
| 2025-10-24 | `fcd9d72f3` | Other | Convert persistence STATUS_* constants to Status enum | 68 | +1206 / −1174 |  |
| 2025-10-24 | `0cb883ef2` | Other | Convert API STATUS_* constants to Status enum | 32 | +200 / −199 |  |
| 2025-10-28 | `493bc5ea5` | Added | Add missing parameter and return typehints | 333 | +758 / −1838 |  |
| 2025-10-28 | `3c7a774cc` | Other | Use nullsafe operator | 13 | +25 / −43 |  |
| 2025-10-28 | `08e55d125` | Other | Various little code optimizations | 10 | +19 / −17 |  |
| 2025-10-28 | `7b9c8a2a1` | Added | Add types to constants | 51 | +98 / −98 |  |
| 2025-10-28 | `f4c11f862` | Other | Use ::class on objects | 5 | +5 / −12 |  |
| 2025-10-28 | `a4e12221c` | Added | Add trailing commas to method properties | 52 | +63 / −63 |  |
| 2025-10-28 | `4a0824a52` | Added | Add constructor property promotion | 278 | +993 / −2451 |  |
| 2025-10-28 | `716d575e7` | Other | Use match expression where possible | 2 | +13 / −18 |  |
| 2025-10-28 | `4186a7324` | Other | Use first class callable syntax | 2 | +2 / −2 |  |
| 2025-10-28 | `a9d8f0b31` | Added | Add never return typehint where possible | 14 | +27 / −27 |  |
| 2025-10-28 | `3f72b8f8e` | Other | Finalize constants where needed | 10 | +20 / −20 |  |
| 2025-10-28 | `852fd48a6` | Removed | Remove parenthesis around new calls | 29 | +62 / −62 |  |
| 2025-10-28 | `24928f380` | Other | Use array_any & array_all where possible | 4 | +20 / −28 |  |
| 2025-10-28 | `2ca1ef3fb` | Other | Use mb_trim instead of trim | 12 | +40 / −40 |  |
| 2025-10-28 | `db8f65261` | Updated | Fix PHPStan errors in tests | 12 | +4 / −86 |  |
| 2025-10-31 | `fd2afdbba` | Other | Use non-capturing catches | 29 | +63 / −63 |  |
| 2025-10-31 | `24e3919c3` | Other | Call Closure properties directly | 3 | +4 / −9 |  |
| 2025-10-31 | `07653310a` | Other | Use string key array unpacking where possible | 40 | +94 / −112 |  |
| 2025-10-31 | `6eb55a3c6` | Other | Use enum for import mode | 6 | +35 / −55 |  |
| 2025-10-31 | `65b0824ae` | Other | Use enum for collection type | 13 | +75 / −149 |  |
| 2025-10-31 | `23113d56c` | Other | Use enum for visibility voter result | 5 | +49 / −40 |  |
| 2025-10-31 | `8b8f91e4e` | Other | Use enum for url generator url type | 10 | +41 / −54 |  |

## 2025-11 (165 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-11-07 | `a5596a39c` | Other | Use enum for link type | 14 | +227 / −207 |  |
| 2025-11-07 | `6c87f6d40` | Other | Make all public and protected properties final | 14 | +52 / −62 |  |
| 2025-11-10 | `3b06316da` | Removed | Remove deprecations across the board: symfony, doctrine and so on | 216 | +1308 / −2626 |  |
| 2025-11-11 | `501549e21` | Other | Replace PHPUnit annotations with attributes | 594 | +1698 / −11198 |  |
| 2025-11-11 | `fe178082e` | Other | Replace usage of getMockForAbstractClass | 16 | +122 / −51 |  |
| 2025-11-11 | `1d5032714` | Other | Replace usage of ezyang html purifier with Symfony Html Sanitizer component | 3 | +14 / −17 |  |
| 2025-11-11 | `6698e5294` | Updated | Fix some DBAL queries | 5 | +14 / −13 |  |
| 2025-11-11 | `f3c9d6625` | Other | Use DsnParser in Doctrine, fix more deprecations | 5 | +23 / −16 |  |
| 2025-11-11 | `fca68fb6d` | Updated | Fix RenderingExtensionTwigTest on new PHPUnit | 2 | +73 / −16 |  |
| 2025-11-11 | `e5b99d6bf` | Added | Add intersection types for MockObject, replace properties with local variables | 88 | +169 / −261 |  |
| 2025-11-11 | `29fb2386b` | Updated | Fix some more deprecations | 4 | +30 / −0 |  |
| 2025-11-11 | `c1ab53e93` | Other | Use named properties for Symfony constraints | 55 | +175 / −205 |  |
| 2025-11-11 | `c1e44d023` | Other | Use list/associative_array for Type constraint where possible | 7 | +8 / −8 |  |
| 2025-11-11 | `a04fec820` | Other | Use stringNode and stringPrototype where possible | 15 | +37 / −57 |  |
| 2025-11-11 | `e5c2a43ab` | Other | No need to call formView explicitly | 1 | +1 / −1 |  |
| 2025-11-11 | `e2e627db2` | Removed | Remove obsolete method | 2 | +0 / −10 |  |
| 2025-11-11 | `345c419fb` | Other | Use Positive/PositiveOrZero where possible | 5 | +9 / −9 |  |
| 2025-11-11 | `cf94a2d1e` | Other | Use invokeable redirect controller | 1 | +10 / −10 |  |
| 2025-11-11 | `6838ec29c` | Other | Use simpler method calls in DI config | 4 | +20 / −20 |  |
| 2025-11-11 | `022964bb7` | Other | Validate that item value is an int or string | 3 | +3 / −3 |  |
| 2025-11-12 | `83a44a3f7` | Other | Cleanup GitHub actions | 5 | +7 / −21 |  |
| 2025-11-12 | `57e43bb2e` | Removed | Remove obsolete Locale constraint | 12 | +5 / −179 |  |
| 2025-11-12 | `ea6574da1` | Removed | Remove wrong deprecation | 1 | +0 / −1 |  |
| 2025-11-12 | `f434330b6` | Added | Add note about 2.0 change | 1 | +2 / −0 |  |
| 2025-11-12 | `a94e295e0` | Other | Explicitly configure arguments set by compiler passes as abstract | 26 | +50 / −32 |  |
| 2025-11-12 | `8a79f51ee` | Removed | Remove duplicate definition in optionsresolver | 1 | +6 / −7 |  |
| 2025-11-12 | `10eb6c2d8` | Other | Use symfony/clock component | 7 | +30 / −27 |  |
| 2025-11-12 | `861c46f71` | Other | Use fluent interface in Options Resolver | 28 | +329 / −231 |  |
| 2025-11-12 | `679130062` | Other | No need for array unpacking for const arrays | 7 | +12 / −14 |  |
| 2025-11-12 | `c83a84dbc` | Updated | Fix CS | 13 | +90 / −145 |  |
| 2025-11-13 | `d882107d6` | Other | Use Count constraint instead of NotIdenticalTo | 1 | +1 / −1 |  |
| 2025-11-13 | `cc96bff68` | Updated | Fix issues with Doctrine DBAL 4 | 8 | +28 / −28 |  |
| 2025-11-13 | `dded3a8b4` | Other | Allow Doctrine DBAL 4 | 1 | +2 / −2 |  |
| 2025-11-13 | `567b0f037` | Other | Only use Count constraint in ChoiceType if the value is an array | 1 | +7 / −4 |  |
| 2025-11-13 | `8591fb736` | Other | Convert constraints to use named arguments | 29 | +224 / −286 |  |
| 2025-11-13 | `c16843755` | Removed | Remove redundant @var tags | 12 | +0 / −12 |  |
| 2025-11-13 | `5bc6a252a` | Other | Use original argument name in normalizers | 17 | +73 / −73 |  |
| 2025-11-13 | `56554a05b` | Added | Add lowest dependencies to tests | 1 | +11 / −0 |  |
| 2025-11-14 | `d47c34e4d` | Added | Add PHP 8.5 polyfill | 8 | +18 / −22 |  |
| 2025-11-14 | `c118898d4` | Other | Implement EnumType parameter type | 7 | +626 / −0 |  |
| 2025-11-14 | `2eab7e293` | Other | More OptionsResolver fluent interface usage | 16 | +135 / −108 |  |
| 2025-11-14 | `69cd86f40` | Other | CS fix | 1 | +1 / −2 |  |
| 2025-11-14 | `fcaeb51fe` | Updated | Update exception messages in tests | 28 | +31 / −32 |  |
| 2025-11-14 | `c69c99cc8` | Removed | Remove obsolete PHP CS Fixer rule | 1 | +0 / −6 |  |
| 2025-11-17 | `0f6a64997` | Removed | Remove duplicate import | 1 | +1 / −2 |  |
| 2025-11-17 | `86f769190` | Other | Subscribe to event class names | 32 | +73 / −78 |  |
| 2025-11-17 | `be48f70c4` | Other | Use attributes in Behat tests | 9 | +47 / −98 |  |
| 2025-11-17 | `bdb915fe8` | Updated | Update nglayouts_migration_versions table in DB schema | 1 | +11 / −10 |  |
| 2025-11-17 | `104748f74` | Released | Bump doctrine/doctrine-migrations-bundle to 3.7 | 1 | +1 / −1 |  |
| 2025-11-17 | `203389936` | Other | Replace lchrusciel/api-test-case with zenstruck/browser | 47 | +1889 / −3275 |  |
| 2025-11-17 | `dd40bd296` | Other | Downgrade Doctrine DBAL to 3.x | 1 | +2 / −2 |  |
| 2025-11-17 | `5d9d26009` | Updated | Fix tests | 7 | +84 / −8 |  |
| 2025-11-17 | `e004a96d7` | Other | Use PHPUnit deprecations reporter | 3 | +14 / −10 |  |
| 2025-11-19 | `594232342` | Other | Use array type in constriants | 5 | +5 / −5 |  |
| 2025-11-19 | `edf24f7a2` | Updated | Fix cloning parameter values | 1 | +5 / −1 |  |
| 2025-11-19 | `dd51db849` | Removed | Remove obsolete redirects | 3 | +0 / −61 |  |
| 2025-11-19 | `2896e828e` | Updated | Fix using EnumType form type | 1 | +1 / −1 |  |
| 2025-11-19 | `222572d72` | Other | Use symfony/mime component in transfer file upload | 2 | +1 / −2 |  |
| 2025-11-19 | `cc3d848bf` | Updated | Fix class name in Doctrine DBAL 3 | 2 | +6 / −5 |  |
| 2025-11-19 | `f86b7197d` | Other | Use DateTimeImmutable | 2 | +6 / −7 |  |
| 2025-11-19 | `b628cae37` | Updated | Fix SQLite and PotgreSQ class names | 5 | +18 / −18 |  |
| 2025-11-19 | `f841e775c` | Removed | Remove unused trait from RuleGroup class | 1 | +0 / −2 |  |
| 2025-11-20 | `3ffb9da42` | Added | Add missing typehints | 2 | +2 / −2 |  |
| 2025-11-20 | `49bcbc946` | Removed | Remove deprecations | 48 | +125 / −632 |  |
| 2025-11-20 | `47e9c6fc7` | Removed | Remove unused properties in tests | 3 | +0 / −15 |  |
| 2025-11-20 | `996c0a502` | Updated | Fix issues found by PHPStan | 3 | +4 / −5 |  |
| 2025-11-20 | `99e99e835` | Other | Master is 2.0 | 2 | +5 / −5 |  |
| 2025-11-20 | `1f3aec29b` | Released | Bump netgen/layouts-coding-standard to 3.0 | 1 | +1 / −1 |  |
| 2025-11-20 | `d3f46640f` | Other | Replace property with local variable | 5 | +15 / −28 |  |
| 2025-11-21 | `e7974b4e1` | Other | Convert ItemInterface and LocationInterface to property hooks | 11 | +30 / −42 |  |
| 2025-11-21 | `68102c875` | Other | Convert SearchQuery and SearchResult to property hooks | 1 | +7 / −3 |  |
| 2025-11-21 | `ecf1db9b2` | Updated | Update GitHub Actions config | 6 | +8 / −8 |  |
| 2025-11-21 | `c1f8d5433` | Removed | Remove obsolete require | 1 | +0 / −1 |  |
| 2025-11-21 | `7fdd6e72d` | Other | Make the configuration object final and non-lazy | 6 | +78 / −31 |  |
| 2025-11-21 | `167a1bd2d` | Other | Controllers do not have to be public any more | 2 | +78 / −78 |  |
| 2025-11-24 | `89edcfc82` | Removed | Remove lazy from service definitions and make classes final | 17 | +10 / −51 |  |
| 2025-11-24 | `87aee2478` | Removed | Remove unneeded config from tests | 3 | +0 / −10 |  |
| 2025-11-24 | `6599a4706` | Other | Make the attribute properties private for writing | 7 | +7 / −7 |  |
| 2025-11-24 | `51893352c` | Other | Make the design and pagelayout config nodes strings | 3 | +3 / −4 |  |
| 2025-11-24 | `8733b6937` | Removed | Remove ::$default* static property support for registering services, add missing attributes | 17 | +145 / −120 |  |
| 2025-11-24 | `439ad94b7` | Other | Use Uri\Rfc3986\Uri from PHP 8.5 | 7 | +83 / −42 |  |
| 2025-11-24 | `8534c9bb4` | Released | Bump dependencies | 1 | +2 / −2 |  |
| 2025-11-24 | `8c68b80de` | Other | Use callback in ChoiceType constraint | 1 | +12 / −2 |  |
| 2025-11-24 | `cbde9c8f9` | Other | Use array unpacking instead of iterator_to_array | 31 | +114 / −173 |  |
| 2025-11-25 | `9a9fc44b1` | Updated | Fix outputing manual/dynamic collection type | 6 | +16 / −10 |  |
| 2025-11-25 | `c1ca6c736` | Other | Do not use static event names | 5 | +4 / −27 |  |
| 2025-11-25 | `1ceb713a9` | Other | View builder and registries do not need to be public any more | 7 | +0 / −10 |  |
| 2025-11-25 | `8f7012fcc` | Other | Ignore errors caused by PHPStan not supporting Symfony 7.3 | 1 | +3 / −1 |  |
| 2025-11-25 | `bd86ffda1` | Removed | Remove unneeded query type mock | 4 | +7 / −35 |  |
| 2025-11-25 | `41205566b` | Renamed | Rename app templates folder in tests | 3 | +2 / −2 |  |
| 2025-11-26 | `e88254df7` | Updated | Update core API values to use property hooks | 258 | +2098 / −2749 |  |
| 2025-11-27 | `c0d57a899` | Added | Add possibility to implement lazy objects in hydrator | 1 | +30 / −2 |  |
| 2025-11-27 | `eea9c37a7` | Other | Implement lazy layout in Rule value object | 11 | +40 / −46 |  |
| 2025-11-27 | `ff6837f26` | Other | Implement lazy linked zone in Zone value object | 10 | +50 / −54 |  |
| 2025-11-27 | `30dc0fc92` | Other | Implement lazy query in Collection value object | 19 | +46 / −48 |  |
| 2025-11-27 | `2c79385a7` | Other | Implement lazy CMS item in Item value object | 13 | +53 / −137 |  |
| 2025-11-27 | `0aabcf852` | Renamed | Move status property to ValueStatusTrait | 16 | +4 / −48 |  |
| 2025-11-27 | `2940b1f5d` | Other | Convert isDraft/isPublished/isArchived to virtual properties | 27 | +270 / −273 |  |
| 2025-11-27 | `c8b4de372` | Other | Use property hooks for config values | 93 | +513 / −837 |  |
| 2025-11-27 | `3f1304d44` | Other | Make Result object use HydratorTrait | 8 | +100 / −62 |  |
| 2025-11-27 | `38f6fbd0f` | Other | Make definition objects use property hooks | 55 | +229 / −313 |  |
| 2025-11-27 | `f757a16b9` | Other | Convert various other classes to use property hooks | 77 | +292 / −509 |  |
| 2025-11-27 | `054dfb5c7` | Other | Convert config to use property hooks | 39 | +77 / −117 |  |
| 2025-11-27 | `53f9211f2` | Other | Convert parameter based structs to property hooks | 22 | +63 / −106 |  |
| 2025-11-27 | `b4d488539` | Other | Convert parameter list to use property hooks | 11 | +13 / −19 |  |
| 2025-11-27 | `bd4cc8cca` | Other | Convert parameters to use property hooks | 43 | +152 / −221 |  |
| 2025-11-27 | `58bfc95d4` | Other | CS fixes | 4 | +6 / −10 |  |
| 2025-11-27 | `22d3cff88` | Other | Convert parameter entity to use property hooks | 21 | +110 / −132 |  |
| 2025-11-27 | `9e635db82` | Renamed | Rename Layout::shared to Layout::isShared | 35 | +93 / −93 |  |
| 2025-11-27 | `d2b1f5f72` | Renamed | Rename alwaysAvailable properties to isAlwaysAvailable | 52 | +257 / −257 |  |
| 2025-11-27 | `ce6442d41` | Renamed | Rename rule and rule group enabled property to isEnabled | 35 | +144 / −144 |  |
| 2025-11-27 | `674cc263f` | Updated | Update boolean property names | 6 | +22 / −22 |  |
| 2025-11-27 | `3970ee18b` | Other | CS fixes | 17 | +25 / −25 |  |
| 2025-11-27 | `0f6fe20e0` | Other | Revert usage of Uri polyfill | 7 | +42 / −83 |  |
| 2025-11-27 | `e1cbd3922` | Updated | Fix Behat tests | 1 | +6 / −6 |  |
| 2025-11-27 | `8b9b5d4bb` | Added | Add missing form.isShared in layout create form | 1 | +1 / −0 |  |
| 2025-11-27 | `14626c68b` | Updated | Update .gitignore | 1 | +1 / −0 |  |
| 2025-11-27 | `ed2a8c0f9` | Other | Expose query type handler through query type | 6 | +41 / −5 |  |
| 2025-11-27 | `1b28a18c9` | Other | Specify that parameter constraints are lists | 3 | +5 / −5 |  |
| 2025-11-27 | `d90546b43` | Updated | Update PHPStan config | 22 | +97 / −35 |  |
| 2025-11-27 | `5bb672bd5` | Other | Run GitHub Actions on Symfony 7.4 | 5 | +7 / −7 |  |
| 2025-11-27 | `099061cbd` | Updated | Update test app console | 1 | +4 / −0 |  |
| 2025-11-27 | `07b8809f4` | Other | Allow Monolog 4.0 | 1 | +1 / −1 |  |
| 2025-11-27 | `1020fe760` | Updated | Update PHPStan config | 7 | +15 / −13 |  |
| 2025-11-28 | `691252c1e` | Removed | Remove unused method | 4 | +0 / −39 |  |
| 2025-11-28 | `04e5a3ba9` | Other | Document we return positive integers for counts | 7 | +46 / −9 |  |
| 2025-11-28 | `f8260a233` | Added | Add missing contracts depenencies | 9 | +10 / −8 |  |
| 2025-11-28 | `b913ecac7` | Other | Use JSON_THROW_ON_ERROR constant | 6 | +31 / −7 |  |
| 2025-11-28 | `8be88e490` | Removed | Remove unused properties | 1 | +0 / −8 |  |
| 2025-11-28 | `5164be638` | Other | CS fix | 1 | +1 / −3 |  |
| 2025-11-28 | `cf9565502` | Updated | Update PHPStan config | 2 | +7 / −4 |  |
| 2025-11-28 | `3ce4d2ce5` | Removed | Remove unneeded usage of expectExceptionMessageMatches | 4 | +4 / −32 |  |
| 2025-11-28 | `0302884ae` | Removed | Remove config tests, they don't work any more on Symfony 7.4 | 19 | +0 / −3826 |  |
| 2025-11-28 | `be2af84d0` | Other | Allow symfony/event-dispatcher-contracts 2.x | 1 | +1 / −1 |  |
| 2025-11-28 | `a937b1be7` | Updated | Fix PHPStan issues | 1 | +3 / −3 |  |
| 2025-11-28 | `0b7d283f4` | Added | Add missing required notation for struct properties | 3 | +6 / −0 |  |
| 2025-11-28 | `1b0819763` | Removed | Remove TranslatableParameterBuilderFactory, merged implementation into main | 22 | +159 / −257 |  |
| 2025-11-28 | `93575b959` | Removed | Remove CompoundParameterDefinition, merged implementation into main | 25 | +123 / −136 |  |
| 2025-11-28 | `a3398f104` | Other | CS fixes | 33 | +48 / −48 |  |
| 2025-11-28 | `2cee6f385` | Other | Public properties in traits do not need to be final | 4 | +4 / −4 |  |
| 2025-11-28 | `ef097f154` | Added | Add final to methods that are not meant to be overriden | 8 | +38 / −38 |  |
| 2025-11-28 | `edb59271a` | Other | Use property hooks for view system | 101 | +432 / −518 |  |
| 2025-11-28 | `9d0097692` | Other | Make method final | 1 | +1 / −1 |  |
| 2025-11-28 | `eedb65fb1` | Other | Allow Symfony 8 | 2 | +32 / −32 |  |
| 2025-11-28 | `b40ff1682` | Other | Only run MySQL and PostgreSQL tests once | 1 | +23 / −1 |  |
| 2025-11-28 | `ac9359043` | Updated | Fix typo in actions | 1 | +6 / −6 |  |
| 2025-11-28 | `471d24707` | Added | Add Symfony 8 method arguments to voters | 1 | +2 / −1 |  |
| 2025-11-29 | `dab388101` | Other | Use instance method for >expects() calls | 68 | +391 / −390 |  |
| 2025-11-29 | `e4644e746` | Other | Implement Stringable where needed | 2 | +4 / −2 |  |
| 2025-11-29 | `8a7b8fbab` | Other | Use ::class on objects | 5 | +5 / −11 |  |
| 2025-11-29 | `3b2b31a96` | Other | Use static::class in abstract method | 1 | +1 / −1 |  |
| 2025-11-29 | `e5c8a681e` | Other | Misc fixes | 43 | +93 / −175 |  |
| 2025-11-29 | `76d5912e1` | Renamed | Rename Exception listeners to Throwable listeners | 5 | +66 / −66 |  |
| 2025-11-29 | `41b7d6843` | Other | Replace Generator typehint with iterable | 38 | +107 / −144 |  |
| 2025-11-29 | `0bc4afea2` | Other | CS fixes in templates | 2 | +2 / −2 |  |
| 2025-11-30 | `beced5843` | Added | Add missing PHPDoc | 1 | +3 / −0 |  |
| 2025-11-30 | `563bd1250` | Updated | Update dependencies | 1 | +2 / −2 |  |
| 2025-11-30 | `772a0e836` | Removed | Remove fixed ignore from PHPStan config files | 2 | +0 / −2 |  |
| 2025-11-30 | `4321e9e69` | Removed | Remove unused doctrine/doctrine-bundle require | 1 | +0 / −1 |  |
| 2025-11-30 | `4da024df9` | Other | Cast and query expression to string and allow Doctrine DBAL 4 | 9 | +35 / −40 |  |
| 2025-11-30 | `8cb93f092` | Other | Migrate Behat config to PHP | 9 | +178 / −104 |  |
| 2025-11-30 | `72889e727` | Updated | Fix route name | 1 | +1 / −1 |  |
| 2025-11-30 | `0422cba71` | Other | CS fixes in templates | 2 | +2 / −2 |  |
| 2025-11-30 | `6ce8b4569` | Updated | Fix div elements not allowed in span and button | 2 | +3 / −3 |  |
| 2025-11-24 | `14ab0999a` | Other | Use Uri\Rfc3986\Uri from PHP 8.5 | 7 | +83 / −43 |  |

## 2025-12 (197 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-12-01 | `1754863a5` | Other | Properly inject container into controllers | 3 | +1 / −28 |  |
| 2025-12-01 | `f0fa0d2f4` | Updated | Update license copyright year | 1 | +1 / −1 |  |
| 2025-12-01 | `1a8f4034b` | Updated | Fix PHPStan issue | 2 | +8 / −4 |  |
| 2025-12-01 | `a209e644f` | Updated | Update install instructions | 1 | +11 / −11 |  |
| 2025-12-01 | `6f58b05ac` | Other | Replace nglayouts_format_datetime with format_datetime from Twig Intl extension | 10 | +8 / −75 |  |
| 2025-12-01 | `e4b38176a` | Updated | Update PHPUnit configuration | 2 | +6 / −4 |  |
| 2025-12-01 | `9d4ea5ea1` | Other | Reorganize JSON schema for import/export for better readability | 18 | +95 / −74 |  |
| 2025-12-01 | `48199b5a0` | Other | Replace swaggest/json-schema with more maintained justinrainbow/json-schema | 5 | +15 / −30 |  |
| 2025-12-02 | `fef8f4a16` | Added | Add generics notation to ViewProvideInterface | 14 | +43 / −2 |  |
| 2025-12-02 | `124603b23` | Added | Add helper templates to easily include preview plugins | 2 | +2 / −0 |  |
| 2025-12-02 | `1262639ca` | Released | Bump justinrainbow/json-schema to 6.6.3 to fix issues with php-enum warnings on obsolete versions | 1 | +1 / −1 |  |
| 2025-12-02 | `d0a5b605a` | Updated | Update github actions config | 1 | +10 / −10 |  |
| 2025-12-02 | `e6bcc5519` | Other | Enable Twig Extra bundle in test app | 1 | +1 / −0 |  |
| 2025-12-02 | `2bd62fcad` | Other | Use asset package for Netgen Layouts logo in debug toolbar | 1 | +2 / −2 |  |
| 2025-12-02 | `6ff089e13` | Updated | Update composer.json | 1 | +0 / −1 |  |
| 2025-12-03 | `0fac880f4` | Other | Split CollectViewParametersEvent into RenderViewEvent and BuildViewEvent | 24 | +374 / −377 |  |
| 2025-12-03 | `cef094de7` | Renamed | Rename ConfigureMenuEvent to BuildAdminMenuEvent | 4 | +14 / −8 |  |
| 2025-12-03 | `ebf6dae4e` | Updated | Refactor views not to return properties from internal parameters | 13 | +54 / −100 |  |
| 2025-12-03 | `1bfea6fdd` | Removed | Remove usage of deprecated DI Extension class | 3 | +3 / −3 |  |
| 2025-12-03 | `d9c697487` | Renamed | Move twig.yaml template out of framework subfolder | 4 | +4 / −4 |  |
| 2025-12-03 | `964db5a50` | Removed | Remove unused dependencies | 3 | +9 / −9 |  |
| 2025-12-03 | `d83cfd977` | Renamed | Rename ContainerConfiguration class and remove netgen_layouts.configuration.container service | 6 | +26 / −78 |  |
| 2025-12-03 | `cf1d9569d` | Other | Re-add symfony/translation dependency | 1 | +1 / −0 |  |
| 2025-12-03 | `efef71240` | Added | Add missing final keywords | 50 | +568 / −579 |  |
| 2025-12-03 | `662486f21` | Updated | Fix CS | 12 | +85 / −161 |  |
| 2025-12-03 | `71224a7c7` | Other | Convert abstract query handler to trait | 7 | +52 / −100 |  |
| 2025-12-03 | `b72a2015d` | Other | CS fix | 6 | +32 / −31 |  |
| 2025-12-04 | `1dfede980` | Other | Switch mocks to stubs in tests | 123 | +1121 / −1661 |  |
| 2025-12-04 | `c5bde346e` | Added | Add back mocks where needed | 13 | +232 / −60 |  |
| 2025-12-04 | `891ef4433` | Removed | Remove obsolete code | 1 | +0 / −8 |  |
| 2025-12-04 | `ce44c33ec` | Released | Bump PHPUnit to 12.5 | 1 | +1 / −1 |  |
| 2025-12-04 | `42eeaf30f` | Other | Make it possible for ParameterStruct::$parameterValues to be private(set) | 16 | +276 / −142 |  |
| 2025-12-04 | `3cc1a2ffd` | Added | Add missing final in traits | 2 | +2 / −2 |  |
| 2025-12-04 | `6d8ab516c` | Updated | Fix CS | 1 | +1 / −1 |  |
| 2025-12-04 | `80ce2ebea` | Updated | Update script for google maps | 1 | +3 / −1 |  |
| 2025-12-04 | `032b3f182` | Updated | Update PHPStan config | 2 | +2 / −2 |  |
| 2025-12-05 | `c7c0461eb` | Other | Cleanup test app | 11 | +11 / −135 |  |
| 2025-12-05 | `81a7bf499` | Renamed | Rename test entities | 80 | +429 / −429 |  |
| 2025-12-05 | `992f87ba8` | Updated | Update test app kernel | 2 | +3 / −3 |  |
| 2025-12-05 | `f0d3bfe38` | Other | CS fixes | 2 | +16 / −16 |  |
| 2025-12-05 | `1c73cd56f` | Removed | Remove unused abstract class and method | 6 | +7 / −131 |  |
| 2025-12-05 | `f4ba9b622` | Added | Add missing final keywords | 1 | +5 / −5 |  |
| 2025-12-06 | `ed8ae0ee7` | Other | No need for null coallesce assignment operator | 1 | +13 / −13 |  |
| 2025-12-06 | `f09c4b6b4` | Renamed | Move core constraints from enterprise | 15 | +570 / −1 |  |
| 2025-12-06 | `63ced2626` | Updated | Update tests | 37 | +556 / −530 |  |
| 2025-12-06 | `14230218e` | Updated | Update BlockDefinitionFactory | 5 | +31 / −31 |  |
| 2025-12-06 | `483e5d4b5` | Updated | Fix PHPStan issue | 2 | +4 / −7 |  |
| 2025-12-06 | `c10ff614e` | Updated | Refactor DatabaseTrait a bit | 1 | +18 / −14 |  |
| 2025-12-06 | `fb22f4b8e` | Removed | Remove duplicate tearDown methods | 38 | +8 / −207 |  |
| 2025-12-06 | `78c9982ca` | Removed | Remove unused method | 1 | +1 / −16 |  |
| 2025-12-06 | `8d8572d0a` | Other | Build stub handler parameters with parameter builder | 14 | +226 / −453 |  |
| 2025-12-06 | `1bdeccdac` | Added | Add isTranslatable to parameter definition | 15 | +56 / −45 |  |
| 2025-12-06 | `04ebf63d6` | Other | Cleanup test fixtures | 3 | +17 / −87 |  |
| 2025-12-06 | `25ba2e864` | Other | Make parameter builder support translatable parameters by default | 12 | +22 / −21 |  |
| 2025-12-06 | `0fc255a3b` | Other | Reorder methods | 2 | +10 / −10 |  |
| 2025-12-06 | `610856022` | Other | CS fix | 2 | +2 / −2 |  |
| 2025-12-06 | `f5e2d6c09` | Other | Make sure building parameters fails when using translatable option on untranslatable parameters | 1 | +0 / −4 |  |
| 2025-12-06 | `1b5ae6af6` | Other | There is no need for default values in ParameterBuilder | 1 | +5 / −5 |  |
| 2025-12-06 | `671c1fe10` | Updated | Fix API tests | 4 | +8 / −8 |  |
| 2025-12-07 | `0783bfe71` | Updated | Fix phpstan issue | 3 | +5 / −3 |  |
| 2025-12-07 | `c34671b3e` | Other | CS fixes | 2 | +10 / −2 |  |
| 2025-12-07 | `eed7cd2cd` | Other | CS fixes | 55 | +236 / −212 |  |
| 2025-12-07 | `55d13d022` | Updated | Fix postgresql test | 1 | +1 / −1 |  |
| 2025-12-08 | `31348ec9d` | Other | Replace eligible compiler passes with !tagged_locator | 25 | +44 / −933 |  |
| 2025-12-08 | `7ac3ac4ea` | Updated | Fix PHPDoc | 5 | +11 / −11 |  |
| 2025-12-08 | `6245919b0` | Other | Implement a php object for zone mappings when changing layout type | 9 | +91 / −90 |  |
| 2025-12-08 | `332ed1d05` | Updated | Update PHPStan config | 1 | +2 / −5 |  |
| 2025-12-08 | `c6b18ba1d` | Removed | Remove service aliases | 2 | +1 / −7 |  |
| 2025-12-09 | `cdafb792f` | Other | Template resolver needs to be lazy | 1 | +1 / −0 |  |
| 2025-12-09 | `a0c0736e2` | Updated | Fix PHPDoc | 1 | +0 / −4 |  |
| 2025-12-09 | `8b83c64d9` | Other | Account for items in exported data being null (unknown) | 1 | +12 / −4 |  |
| 2025-12-10 | `a4d204665` | Removed | Remove reference to obsolete target type names | 2 | +2 / −6 |  |
| 2025-12-10 | `35c147053` | Other | Use CoversTrait where needed | 12 | +24 / −22 |  |
| 2025-12-10 | `bc4f0d671` | Other | Relax coverage strictness | 2 | +0 / −2 |  |
| 2025-12-11 | `095e0db2e` | Removed | Remove obsolete migration scripts | 3 | +0 / −471 |  |
| 2025-12-11 | `11650d4f3` | Other | Do not allow nulls in string configuration | 4 | +9 / −9 |  |
| 2025-12-11 | `95fec6720` | Updated | Update PHPDoc | 19 | +19 / −19 |  |
| 2025-12-11 | `d73f9e2ea` | Released | Bump behat/behat version | 1 | +1 / −1 |  |
| 2025-12-11 | `ae7c1878e` | Other | Allow doctrine/doctrine-migrations-bundle v4 | 1 | +1 / −1 |  |
| 2025-12-11 | `6461b3d1f` | Updated | Update GH actions to use ramsey/composer-install | 5 | +25 / −16 |  |
| 2025-12-12 | `5b22dfc5a` | Removed | Remove obsolete @throws tags | 19 | +0 / −77 |  |
| 2025-12-12 | `4223bc518` | Removed | Remove obsolete @param tags | 1 | +0 / −3 |  |
| 2025-12-12 | `65a5a93ad` | Removed | Remove obsolete PHPDoc | 43 | +29 / −158 |  |
| 2025-12-12 | `f028ee713` | Other | Use more PHP 8 style code | 32 | +60 / −66 |  |
| 2025-12-12 | `a8c63ff78` | Other | Code style fixes | 24 | +77 / −88 |  |
| 2025-12-12 | `fe9ab2cd4` | Removed | Remove obsolete inline @var annotations | 6 | +8 / −18 |  |
| 2025-12-13 | `e6230d90c` | Other | Do not use UuidGeneratorTrait in service tests | 3 | +62 / −170 |  |
| 2025-12-13 | `02befe412` | Other | Switch uuids to Symfony Uid component | 210 | +1026 / −969 |  |
| 2025-12-13 | `5785a32ca` | Other | Use Symfony VarExporter hydrator instead of custom variant | 15 | +49 / −189 |  |
| 2025-12-13 | `0561c3b60` | Other | Use specific ParameterBag methods where possible | 19 | +29 / −28 |  |
| 2025-12-13 | `2f6a8be3f` | Other | protected(set) properties can be private(set) | 6 | +16 / −13 |  |
| 2025-12-13 | `58bd1988c` | Other | Use Request::getPayload | 19 | +189 / −175 |  |
| 2025-12-13 | `db5d940d1` | Other | CS fix | 1 | +1 / −3 |  |
| 2025-12-13 | `97e8a1c30` | Removed | Remove obsolete validation | 1 | +3 / −22 |  |
| 2025-12-13 | `f5c0ab5f2` | Other | Do not use strict option in Choice constraint | 2 | +0 / −3 |  |
| 2025-12-13 | `2585038ff` | Other | Validate new_type in change collection type API endpoint | 2 | +31 / −2 |  |
| 2025-12-13 | `58798083d` | Updated | Improve zone mappings validation | 2 | +109 / −0 |  |
| 2025-12-14 | `3ebfa6f72` | Other | Use InputBag::all | 2 | +4 / −6 |  |
| 2025-12-14 | `b91c9d857` | Other | Use array_key_exists | 3 | +8 / −5 |  |
| 2025-12-14 | `5da8ff575` | Removed | Remove obsolete consts | 3 | +3 / −9 |  |
| 2025-12-14 | `341beeeec` | Other | Use Timezone constraint | 3 | +2 / −19 |  |
| 2025-12-14 | `3134b17ed` | Other | Use yield in getExtendedTypes | 2 | +2 / −2 |  |
| 2025-12-15 | `34b550d03` | Updated | Fix CS | 1 | +2 / −2 |  |
| 2025-12-15 | `88df21a24` | Other | Revert "Make it possible for ParameterStruct::$parameterValues to be private(set)" | 16 | +142 / −276 |  |
| 2025-12-15 | `2638d8242` | Other | Use setter option in ParametersType form to make it possible parameterValues is readonly | 10 | +301 / −121 |  |
| 2025-12-15 | `9fe12ce9f` | Removed | Remove obsolete todos | 2 | +0 / −5 |  |
| 2025-12-15 | `57b8b1849` | Updated | Fix PHPDoc | 4 | +3 / −10 |  |
| 2025-12-15 | `9378ca0b5` | Other | Use range constraint in NumberType | 1 | +2 / −6 |  |
| 2025-12-15 | `c69d1117e` | Renamed | Rename mapper classes | 33 | +110 / −110 |  |
| 2025-12-15 | `7ab4113b8` | Removed | Remove obsolete extended_type DI tag attribute | 1 | +1 / −1 |  |
| 2025-12-15 | `276824324` | Other | Use self:: to access constant on interface | 1 | +2 / −2 |  |
| 2025-12-15 | `e549a0d25` | Removed | Remove Descriptor class | 4 | +8 / −20 |  |
| 2025-12-15 | `ff1b0319f` | Other | Get rid of data provider closure binding in tests | 19 | +155 / −51 |  |
| 2025-12-15 | `c4feb180b` | Other | CS fixes | 14 | +28 / −28 |  |
| 2025-12-15 | `68838eee6` | Removed | Remove ArrayAccess, Countable and IteratorAggregate from Layout | 4 | +3 / −89 |  |
| 2025-12-15 | `f510108f9` | Other | Use .blocks in placeholder.html.twig | 1 | +1 / −1 |  |
| 2025-12-15 | `a41264684` | Removed | Remove ArrayAccess, Countable and IteratorAggregate from Placeholder | 4 | +8 / −84 |  |
| 2025-12-15 | `519c45d9e` | Other | Use DoesNotPerformAssertions | 12 | +59 / −143 |  |
| 2025-12-15 | `81efb2066` | Other | Use symfony/emoji component instead of custom flag generator | 7 | +2 / −135 |  |
| 2025-12-15 | `e5cdcd61e` | Removed | Remove custom wrapper around Symfonys HtmlSanitizer | 7 | +21 / −68 |  |
| 2025-12-15 | `121715949` | Other | Use lowercase identifiers for statuses in import schema | 19 | +37 / −37 |  |
| 2025-12-15 | `9bd9659c4` | Removed | Remove constant | 1 | +1 / −3 |  |
| 2025-12-15 | `c6f3894ec` | Other | Replace ValueResolver constants with Status enum | 28 | +152 / −113 |  |
| 2025-12-15 | `2a4fa99df` | Other | CS fix | 4 | +0 / −9 |  |
| 2025-12-15 | `b9cb4785a` | Other | Switch API enum to strings | 11 | +63 / −32 |  |
| 2025-12-15 | `60aa37bad` | Removed | Remove duplicated Status enum | 34 | +41 / −135 |  |
| 2025-12-15 | `12dab770d` | Other | Switch VisibilityVoterResult enum to non-backed enum | 1 | +4 / −4 |  |
| 2025-12-16 | `29d4b975b` | Other | Switch UrlType enum to non-backed enum | 5 | +94 / −18 |  |
| 2025-12-16 | `d0046ad9a` | Other | Use sprintf in choice_label for enums | 2 | +12 / −12 |  |
| 2025-12-16 | `43094d2be` | Updated | Refactor ItemRuntime, make UrlType required in URL generator | 4 | +42 / −28 |  |
| 2025-12-16 | `f6b560b78` | Updated | Update test | 1 | +1 / −1 |  |
| 2025-12-16 | `5f3fba205` | Other | Allow enum type to define option labels | 4 | +52 / −1 |  |
| 2025-12-16 | `0a16ab3a2` | Other | Use strings for collection type | 19 | +31 / −39 |  |
| 2025-12-16 | `5d77787d5` | Other | Make sure throwable uses our normalizer only in API requests | 4 | +31 / −17 |  |
| 2025-12-16 | `4c183a938` | Other | Include app routes in error output | 1 | +1 / −1 |  |
| 2025-12-16 | `1ef9c1519` | Renamed | Rename SetIsApiRequestListener to SetIsAppRequestListener | 12 | +43 / −43 |  |
| 2025-12-16 | `932903874` | Renamed | Rename ApiCsrfValidationListener to AppCsrfValidationListener | 4 | +11 / −11 |  |
| 2025-12-16 | `17a293aef` | Updated | Fix error message | 1 | +1 / −1 |  |
| 2025-12-16 | `e1cfdcb87` | Other | Document that parameter constraint closures need to return constraints | 5 | +6 / −6 |  |
| 2025-12-16 | `ab2c4cda0` | Added | Add narrower types | 10 | +16 / −61 |  |
| 2025-12-16 | `81b09291d` | Updated | Fix some PHPStan issues | 2 | +6 / −14 |  |
| 2025-12-16 | `5fe5ad3a5` | Other | Reconfigure registry services in DI configuration | 35 | +160 / −215 |  |
| 2025-12-16 | `2dac20ab5` | Updated | Fix PHPStan issues | 7 | +4 / −64 |  |
| 2025-12-16 | `510ff4cfb` | Updated | Update PHPStan | 2 | +2 / −5 |  |
| 2025-12-16 | `a342e2bd5` | Other | View system can use objects instead of mixed | 28 | +41 / −86 |  |
| 2025-12-16 | `3ea39b4e2` | Updated | Fix return typehint | 1 | +2 / −1 |  |
| 2025-12-16 | `aab956c69` | Other | Use 0o octal notation | 1 | +12 / −12 |  |
| 2025-12-17 | `fbcfc3c04` | Added | Add nofollow noopener noreferrer to ajax paging URLs | 1 | +4 / −4 | 1.4.13 origin/1.4 |
| 2025-12-17 | `1a35168f3` | Other | Do not run CSRF validation on app forms | 2 | +13 / −6 |  |
| 2025-12-17 | `6b17980aa` | Added | Add nofollow noopener noreferrer to ajax paging URLs | 1 | +4 / −4 |  |
| 2025-12-17 | `a7027337f` | Updated | Update tests | 6 | +34 / −20 |  |
| 2025-12-17 | `4f1be80de` | Other | Cleanup Composer scripts | 3 | +19 / −9 |  |
| 2025-12-17 | `04e3b659f` | Other | Exclude tests/application/config from PHP CS Fixer | 1 | +1 / −1 |  |
| 2025-12-17 | `e2037baa5` | Other | Restore usage of API flags in request body listener and admin auth listener | 6 | +21 / −21 |  |
| 2025-12-17 | `a90e72cad` | Added | New build | 2 | +0 / −0 |  |
| 2025-12-17 | `0afbc94e1` | Added | New build | 1 | +0 / −0 |  |
| 2025-12-17 | `32de30cb7` | Other | Use PHPStan extension installer | 3 | +2 / −12 |  |
| 2025-12-17 | `33df406b9` | Updated | Update MySQL schema | 1 | +19 / −19 |  |
| 2025-12-17 | `d58e5e32e` | Other | Do not use schema object to create DB tables in migrations | 5 | +241 / −212 |  |
| 2025-12-17 | `0dc6eeb16` | Updated | Update test schema, remove unused mssql schema | 2 | +69 / −359 |  |
| 2025-12-17 | `7e4956c66` | Removed | Remove unused fixtures dump | 1 | +0 / −600 |  |
| 2025-12-17 | `8ca4f1bcf` | Other | Disable Composer cache in GH actions | 5 | +5 / −0 |  |
| 2025-12-17 | `5fc054059` | Updated | Update autload config | 1 | +0 / −2 |  |
| 2025-12-17 | `3782d86fb` | Updated | Update test | 1 | +2 / −2 |  |
| 2025-12-17 | `d7b04962b` | Other | Introduce more specific types where possible | 22 | +62 / −40 |  |
| 2025-12-18 | `179080620` | Removed | Remove phpstan/phpstan-symfony | 3 | +0 / −13 |  |
| 2025-12-18 | `0e4b4758c` | Other | Reorder some types | 4 | +10 / −10 |  |
| 2025-12-18 | `cc88714aa` | Other | Replace remaining scalar nodes with string nodes | 3 | +18 / −22 |  |
| 2025-12-18 | `a8b917978` | Updated | Fix tests | 1 | +8 / −2 |  |
| 2025-12-18 | `c9e841939` | Other | CS fixes | 5 | +16 / −35 |  |
| 2025-12-18 | `dfef4f9c3` | Updated | Update core test case to fix using undefined property | 6 | +263 / −273 |  |
| 2025-12-18 | `5a144c4a1` | Other | Centralize building validator in tests | 31 | +177 / −220 |  |
| 2025-12-18 | `e234c63df` | Other | Use only symfony/event-dispatcher-contracts 3.x | 1 | +1 / −1 |  |
| 2025-12-18 | `8ddcf295b` | Merged | Merge branch 'master' into 2.0 | 0 | +0 / −0 |  |
| 2025-12-18 | `2ca30ba41` | Removed | Remove unused class | 1 | +0 / −35 |  |
| 2025-12-19 | `d736a9399` | Removed | Remove unused parameter | 3 | +1 / −8 |  |
| 2025-12-27 | `58599cd24` | Updated | Update dependencies | 1 | +1 / −1 |  |
| 2025-12-29 | `a77e76f79` | Added | Add PHP 8.5 to test matrix | 1 | +3 / −3 |  |
| 2025-12-29 | `46149d88c` | Updated | Update PHPDoc | 2 | +1 / −4 |  |
| 2025-12-29 | `99a43533b` | Other | There is no need for some abstract methods to be public | 40 | +878 / −589 |  |
| 2025-12-29 | `f2584e5c5` | Updated | Fix data provider method name | 3 | +6 / −9 |  |
| 2025-12-29 | `b6c5aa9d5` | Updated | Fix class not being final | 1 | +1 / −1 |  |
| 2025-12-29 | `49239ef1e` | Updated | Update PHPDoc for data providers | 119 | +608 / −24 |  |
| 2025-12-29 | `f72807cc4` | Updated | Fix typehints | 4 | +7 / −4 |  |
| 2025-12-29 | `f3a279eac` | Other | CS fix | 1 | +2 / −2 |  |
| 2025-12-30 | `c91c92071` | Other | Debug Behat action | 1 | +9 / −1 |  |
| 2025-12-30 | `e82f8cdf6` | Updated | Update GH Actions to use PHP 8.5 | 4 | +4 / −4 |  |
| 2025-12-30 | `55ed72da4` | Other | Cast offset to string in DynamicParameters | 1 | +1 / −1 |  |
| 2025-12-30 | `4953a08f8` | Other | Run Behat GH action via PHP built in server | 2 | +12 / −7 |  |
| 2025-12-30 | `8e3ba0e33` | Other | Use InstalledVersions from Composer instead of jean85/pretty-package-versions | 3 | +5 / −5 |  |
| 2025-12-30 | `5e21e6144` | Other | Use emodric/twig-package-versions 2.0 | 1 | +1 / −1 |  |
| 2025-12-30 | `7929340e7` | Removed | Remove unneeded behat dependencies | 2 | +1 / −8 |  |
| 2025-12-31 | `31e28ef7a` | Other | Use GH action to setup Chrome Web Driver | 2 | +7 / −13 |  |
| 2025-12-31 | `4c4271f13` | Updated | Fix issues with PHPStan | 3 | +6 / −0 |  |
| 2025-12-31 | `500cda504` | Other | Use Uuid v7 in tests | 78 | +287 / −287 |  |
| 2025-12-31 | `f505309e5` | Other | Use Uuid v7 by default | 1 | +1 / −1 |  |
| 2025-12-31 | `206c29284` | Added | Add log output for chromedriver | 1 | +7 / −1 |  |

## 2026-01 (21 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-01-08 | `e17483b3f` | Removed | Remove usage of default_index_method, it is deprecated in Symfony 8.1 | 21 | +111 / −112 |  |
| 2026-01-08 | `57de8cd93` | Released | Bump doctrine/collections to 2.5 | 1 | +1 / −1 |  |
| 2026-01-08 | `0de13279b` | Removed | Remove usage of friends-of-behat/suite-settings-extension | 36 | +3 / −15 |  |
| 2026-01-08 | `bfeaf706e` | Merged | Merge behat into test app | 40 | +124 / −153 |  |
| 2026-01-08 | `9c8f9d849` | Other | Cleanup behat config | 1 | +1 / −6 |  |
| 2026-01-09 | `f37b2f8ac` | Released | Bump zenstruck/browser to 1.10 | 1 | +1 / −1 |  |
| 2026-01-12 | `e7a7535ec` | Removed | Remove Behat from Composer if running tests on Symfony 8 | 3 | +16 / −5 |  |
| 2026-01-13 | `9108fcc86` | Released | Bump friendsofsymfony/http-cache-bundle to 3.3 | 1 | +1 / −1 |  |
| 2026-01-16 | `95d7c2fa8` | Released | Bump doctrine/collections to 2.6 | 1 | +1 / −1 |  |
| 2026-01-16 | `f22a8786e` | Updated | Update league/uri-polyfill to 7.8 | 1 | +1 / −1 |  |
| 2026-01-20 | `9526b65d6` | Updated | Fix PHPDoc | 3 | +4 / −4 |  |
| 2026-01-20 | `dd3030811` | Added | Add the rule group root to the postgresql schema | 1 | +337 / −0 |  |
| 2026-01-22 | `c0b2f8bd2` | Merged | Merge pull request #35 from wizhippo/pgsql-missing-grp-data | 0 | +0 / −0 |  |
| 2026-01-22 | `0594b79df` | Added | Add indexes to PostgreSQL schema | 1 | +28 / −24 |  |
| 2026-01-22 | `1820d7a4e` | Added | Add missing priority argument to AsTargetType attribute | 2 | +8 / −3 |  |
| 2026-01-22 | `1a1d77bbb` | Renamed | Rename attribute in netgen_layouts.parameter_type.form_mapper DI tag | 2 | +19 / −19 |  |
| 2026-01-22 | `041ce6259` | Renamed | Rename attribute arguments to match DI tag attributes | 5 | +8 / −8 |  |
| 2026-01-22 | `ff9120f0d` | Released | Bump dependencies | 1 | +2 / −2 |  |
| 2026-01-26 | `576db8f70` | Released | Bump dependencies | 1 | +3 / −3 |  |
| 2026-01-26 | `692f86faf` | Other | Exclude reference.php from PHPStan checks | 1 | +1 / −0 |  |
| 2026-01-26 | `1219b65fc` | Other | Use new Twig syntax | 4 | +9 / −12 |  |

## 2026-02 (6 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-02-11 | `e8a47e536` | Removed | Remove unneeded with() calls for stubs | 67 | +281 / −752 |  |
| 2026-02-13 | `f65d58795` | Other | Limit node to v14 | 1 | +3 / −0 |  |
| 2026-02-13 | `bedadcd15` | Other | Final build for 2.0 | 1 | +0 / −0 |  |
| 2026-02-16 | `0d7e5de03` | Updated | Update dependencies | 1 | +1 / −1 |  |
| 2026-02-19 | `f694a7394` | Removed | Remove unused path from autoload-dev | 1 | +0 / −1 |  |
| 2026-02-25 | `c7a901af0` | Released | Bump PHPUnit to v13 | 1 | +3 / −3 | 2.0.0 |

## 2026-04 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-19 | `3ec0262b2` | Updated | fix: add Twig/PHP 8.4 private(set) getter shims to Parameter class | 2 | +15 / −2 | 2.0.0-se7enx.1 se7enxweb/master se7enxweb/main se7enxweb/2.0.x |
