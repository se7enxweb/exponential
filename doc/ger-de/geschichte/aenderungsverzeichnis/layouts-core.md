# Änderungsverzeichnis: layouts-core

Jede Änderung an `layouts-core` seit Beginn der se7enxweb-Ära, die älteste zuerst: 450 Änderungen an 6363 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Sonstiges | 221 |
| Aktualisiert | 93 |
| Entfernt | 63 |
| Hinzugefügt | 36 |
| Umbenannt | 17 |
| Release | 15 |
| Zusammengeführt | 5 |

## 2024-09 (9 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-09-06 | `03acf1f33` | Aktualisiert | Fix CS | 41 | +65 / −69 |  |
| 2024-09-06 | `4edf0f383` | Aktualisiert | Fix PHPStan in tests | 2 | +5 / −3 |  |
| 2024-09-06 | `7984d66df` | Aktualisiert | Update API tests | 6 | +6 / −6 |  |
| 2024-09-06 | `aeeb092cc` | Aktualisiert | Update Psalm config | 1 | +8 / −0 |  |
| 2024-09-06 | `0c9f786c1` | Aktualisiert | Fix issue in PHPStan | 1 | +5 / −0 |  |
| 2024-09-06 | `40987764e` | Aktualisiert | Fix deprecation in Twig 3.12 | 7 | +27 / −12 |  |
| 2024-09-06 | `e22c56d82` | Zusammengeführt | Merge pull request #33 from netgen-layouts/twig39 | 0 | +0 / −0 | 1.4.10 |
| 2024-09-06 | `d4224578b` | Aktualisiert | Update prepare behat test script | 1 | +2 / −2 |  |
| 2024-09-06 | `fd4c359ac` | Aktualisiert | Update prepare behat test script | 1 | +1 / −1 |  |

## 2024-05 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-05-21 | `79b509c4a` | Sonstiges | Support yield rendering strategy in Twig 3.9+ | 10 | +106 / −18 |  |

## 2025-02 (4 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-02-10 | `0206f2477` | Sonstiges | Expose block definition plugin classes | 7 | +127 / −0 |  |
| 2025-02-10 | `21ea622d7` | Hinzugefügt | Add a DynamicParameter class to declare which dynamic parameters a handler has | 1 | +18 / −0 |  |
| 2025-02-10 | `925caf9b5` | Sonstiges | Enable iterating over dynamic parameters | 1 | +10 / −1 |  |
| 2025-02-10 | `2794571cd` | Entfernt | Remove obsolete phpstan rules | 1 | +0 / −1 | 1.4.11 |

## 2025-07 (19 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-07-10 | `476395363` | Sonstiges | tweak ajax-paging.js to fix accessibility focus issues | 1 | +13 / −2 |  |
| 2025-07-10 | `9af6b762b` | Sonstiges | tweak ajax-paging.js to remove optional chaining | 1 | +9 / −7 |  |
| 2025-07-10 | `8f63cb75c` | Sonstiges | Force minimatch to version 9 to fix frontend build | 1 | +3 / −0 |  |
| 2025-07-10 | `3fa4c576f` | Zusammengeführt | Merge pull request #34 from netgen-layouts/ajax-paging-accessibility | 0 | +0 / −0 |  |
| 2025-07-10 | `2a7966804` | Sonstiges | Frontend build for 1.4.12 | 1 | +0 / −0 | 1.4.12 |
| 2025-07-11 | `4d6cfd0bb` | Entfernt | Remove Psalm | 4 | +5 / −242 |  |
| 2025-07-11 | `5b914364c` | Aktualisiert | Fix tests and deprecations with newer Twig versions | 8 | +71 / −17 |  |
| 2025-07-11 | `575e8943d` | Hinzugefügt | Add Twig\Environment::VERSION_ID to dynamic constants for tests phpstan | 1 | +1 / −0 |  |
| 2025-07-11 | `88497c45f` | Aktualisiert | Fix PHPStan issue | 2 | +9 / −0 |  |
| 2025-07-11 | `e38e78a52` | Aktualisiert | Fix tests on PHP 7.4 | 8 | +24 / −17 |  |
| 2025-07-11 | `8791b0f93` | Sonstiges | Try to improve MySQL performance in tests | 1 | +6 / −0 |  |
| 2025-07-11 | `5c90162ed` | Sonstiges | Downgrade minimatch to version 3 | 1 | +1 / −1 |  |
| 2025-07-14 | `2320946b9` | Release | Bump tests to PHP 8.4 | 5 | +8 / −10 |  |
| 2025-07-15 | `bb12b1e1f` | Umbenannt | Move SYMFONY_DEPRECATIONS_HELPER variable to Composer config | 3 | +0 / −6 |  |
| 2025-07-15 | `58f8187d7` | Sonstiges | Use pre-command-run Composer script to set env variable | 1 | +1 / −0 |  |
| 2025-07-15 | `4305fd3ab` | Sonstiges | Make CI use Composer scripts to run tests | 2 | +13 / −12 |  |
| 2025-07-15 | `3c95d79a2` | Entfernt | Remove support for Symfony 6.2 and 6.3 | 1 | +29 / −29 |  |
| 2025-07-16 | `3c525cd18` | Sonstiges | Make LinkValue internal properties only accept strings | 11 | +86 / −81 |  |
| 2025-07-16 | `14f39b6ea` | Sonstiges | Upgrade PHPStan to 2.x | 26 | +82 / −77 |  |

## 2025-09 (2 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-09-08 | `d89ff1fd2` | Sonstiges | Use include Twig function instead of tag | 4 | +16 / −16 |  |
| 2025-09-08 | `5a02c8595` | Entfernt | Remove obsolete @var tag | 1 | +0 / −1 |  |

## 2025-10 (25 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-10-28 | `3ef20e9de` | Release | Bump PHP to 8.4 | 3 | +2 / −9 |  |
| 2025-10-24 | `fcd9d72f3` | Sonstiges | Convert persistence STATUS_* constants to Status enum | 68 | +1206 / −1174 |  |
| 2025-10-24 | `0cb883ef2` | Sonstiges | Convert API STATUS_* constants to Status enum | 32 | +200 / −199 |  |
| 2025-10-28 | `493bc5ea5` | Hinzugefügt | Add missing parameter and return typehints | 333 | +758 / −1838 |  |
| 2025-10-28 | `3c7a774cc` | Sonstiges | Use nullsafe operator | 13 | +25 / −43 |  |
| 2025-10-28 | `08e55d125` | Sonstiges | Various little code optimizations | 10 | +19 / −17 |  |
| 2025-10-28 | `7b9c8a2a1` | Hinzugefügt | Add types to constants | 51 | +98 / −98 |  |
| 2025-10-28 | `f4c11f862` | Sonstiges | Use ::class on objects | 5 | +5 / −12 |  |
| 2025-10-28 | `a4e12221c` | Hinzugefügt | Add trailing commas to method properties | 52 | +63 / −63 |  |
| 2025-10-28 | `4a0824a52` | Hinzugefügt | Add constructor property promotion | 278 | +993 / −2451 |  |
| 2025-10-28 | `716d575e7` | Sonstiges | Use match expression where possible | 2 | +13 / −18 |  |
| 2025-10-28 | `4186a7324` | Sonstiges | Use first class callable syntax | 2 | +2 / −2 |  |
| 2025-10-28 | `a9d8f0b31` | Hinzugefügt | Add never return typehint where possible | 14 | +27 / −27 |  |
| 2025-10-28 | `3f72b8f8e` | Sonstiges | Finalize constants where needed | 10 | +20 / −20 |  |
| 2025-10-28 | `852fd48a6` | Entfernt | Remove parenthesis around new calls | 29 | +62 / −62 |  |
| 2025-10-28 | `24928f380` | Sonstiges | Use array_any & array_all where possible | 4 | +20 / −28 |  |
| 2025-10-28 | `2ca1ef3fb` | Sonstiges | Use mb_trim instead of trim | 12 | +40 / −40 |  |
| 2025-10-28 | `db8f65261` | Aktualisiert | Fix PHPStan errors in tests | 12 | +4 / −86 |  |
| 2025-10-31 | `fd2afdbba` | Sonstiges | Use non-capturing catches | 29 | +63 / −63 |  |
| 2025-10-31 | `24e3919c3` | Sonstiges | Call Closure properties directly | 3 | +4 / −9 |  |
| 2025-10-31 | `07653310a` | Sonstiges | Use string key array unpacking where possible | 40 | +94 / −112 |  |
| 2025-10-31 | `6eb55a3c6` | Sonstiges | Use enum for import mode | 6 | +35 / −55 |  |
| 2025-10-31 | `65b0824ae` | Sonstiges | Use enum for collection type | 13 | +75 / −149 |  |
| 2025-10-31 | `23113d56c` | Sonstiges | Use enum for visibility voter result | 5 | +49 / −40 |  |
| 2025-10-31 | `8b8f91e4e` | Sonstiges | Use enum for url generator url type | 10 | +41 / −54 |  |

## 2025-11 (165 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-11-07 | `a5596a39c` | Sonstiges | Use enum for link type | 14 | +227 / −207 |  |
| 2025-11-07 | `6c87f6d40` | Sonstiges | Make all public and protected properties final | 14 | +52 / −62 |  |
| 2025-11-10 | `3b06316da` | Entfernt | Remove deprecations across the board: symfony, doctrine and so on | 216 | +1308 / −2626 |  |
| 2025-11-11 | `501549e21` | Sonstiges | Replace PHPUnit annotations with attributes | 594 | +1698 / −11198 |  |
| 2025-11-11 | `fe178082e` | Sonstiges | Replace usage of getMockForAbstractClass | 16 | +122 / −51 |  |
| 2025-11-11 | `1d5032714` | Sonstiges | Replace usage of ezyang html purifier with Symfony Html Sanitizer component | 3 | +14 / −17 |  |
| 2025-11-11 | `6698e5294` | Aktualisiert | Fix some DBAL queries | 5 | +14 / −13 |  |
| 2025-11-11 | `f3c9d6625` | Sonstiges | Use DsnParser in Doctrine, fix more deprecations | 5 | +23 / −16 |  |
| 2025-11-11 | `fca68fb6d` | Aktualisiert | Fix RenderingExtensionTwigTest on new PHPUnit | 2 | +73 / −16 |  |
| 2025-11-11 | `e5b99d6bf` | Hinzugefügt | Add intersection types for MockObject, replace properties with local variables | 88 | +169 / −261 |  |
| 2025-11-11 | `29fb2386b` | Aktualisiert | Fix some more deprecations | 4 | +30 / −0 |  |
| 2025-11-11 | `c1ab53e93` | Sonstiges | Use named properties for Symfony constraints | 55 | +175 / −205 |  |
| 2025-11-11 | `c1e44d023` | Sonstiges | Use list/associative_array for Type constraint where possible | 7 | +8 / −8 |  |
| 2025-11-11 | `a04fec820` | Sonstiges | Use stringNode and stringPrototype where possible | 15 | +37 / −57 |  |
| 2025-11-11 | `e5c2a43ab` | Sonstiges | No need to call formView explicitly | 1 | +1 / −1 |  |
| 2025-11-11 | `e2e627db2` | Entfernt | Remove obsolete method | 2 | +0 / −10 |  |
| 2025-11-11 | `345c419fb` | Sonstiges | Use Positive/PositiveOrZero where possible | 5 | +9 / −9 |  |
| 2025-11-11 | `cf94a2d1e` | Sonstiges | Use invokeable redirect controller | 1 | +10 / −10 |  |
| 2025-11-11 | `6838ec29c` | Sonstiges | Use simpler method calls in DI config | 4 | +20 / −20 |  |
| 2025-11-11 | `022964bb7` | Sonstiges | Validate that item value is an int or string | 3 | +3 / −3 |  |
| 2025-11-12 | `83a44a3f7` | Sonstiges | Cleanup GitHub actions | 5 | +7 / −21 |  |
| 2025-11-12 | `57e43bb2e` | Entfernt | Remove obsolete Locale constraint | 12 | +5 / −179 |  |
| 2025-11-12 | `ea6574da1` | Entfernt | Remove wrong deprecation | 1 | +0 / −1 |  |
| 2025-11-12 | `f434330b6` | Hinzugefügt | Add note about 2.0 change | 1 | +2 / −0 |  |
| 2025-11-12 | `a94e295e0` | Sonstiges | Explicitly configure arguments set by compiler passes as abstract | 26 | +50 / −32 |  |
| 2025-11-12 | `8a79f51ee` | Entfernt | Remove duplicate definition in optionsresolver | 1 | +6 / −7 |  |
| 2025-11-12 | `10eb6c2d8` | Sonstiges | Use symfony/clock component | 7 | +30 / −27 |  |
| 2025-11-12 | `861c46f71` | Sonstiges | Use fluent interface in Options Resolver | 28 | +329 / −231 |  |
| 2025-11-12 | `679130062` | Sonstiges | No need for array unpacking for const arrays | 7 | +12 / −14 |  |
| 2025-11-12 | `c83a84dbc` | Aktualisiert | Fix CS | 13 | +90 / −145 |  |
| 2025-11-13 | `d882107d6` | Sonstiges | Use Count constraint instead of NotIdenticalTo | 1 | +1 / −1 |  |
| 2025-11-13 | `cc96bff68` | Aktualisiert | Fix issues with Doctrine DBAL 4 | 8 | +28 / −28 |  |
| 2025-11-13 | `dded3a8b4` | Sonstiges | Allow Doctrine DBAL 4 | 1 | +2 / −2 |  |
| 2025-11-13 | `567b0f037` | Sonstiges | Only use Count constraint in ChoiceType if the value is an array | 1 | +7 / −4 |  |
| 2025-11-13 | `8591fb736` | Sonstiges | Convert constraints to use named arguments | 29 | +224 / −286 |  |
| 2025-11-13 | `c16843755` | Entfernt | Remove redundant @var tags | 12 | +0 / −12 |  |
| 2025-11-13 | `5bc6a252a` | Sonstiges | Use original argument name in normalizers | 17 | +73 / −73 |  |
| 2025-11-13 | `56554a05b` | Hinzugefügt | Add lowest dependencies to tests | 1 | +11 / −0 |  |
| 2025-11-14 | `d47c34e4d` | Hinzugefügt | Add PHP 8.5 polyfill | 8 | +18 / −22 |  |
| 2025-11-14 | `c118898d4` | Sonstiges | Implement EnumType parameter type | 7 | +626 / −0 |  |
| 2025-11-14 | `2eab7e293` | Sonstiges | More OptionsResolver fluent interface usage | 16 | +135 / −108 |  |
| 2025-11-14 | `69cd86f40` | Sonstiges | CS fix | 1 | +1 / −2 |  |
| 2025-11-14 | `fcaeb51fe` | Aktualisiert | Update exception messages in tests | 28 | +31 / −32 |  |
| 2025-11-14 | `c69c99cc8` | Entfernt | Remove obsolete PHP CS Fixer rule | 1 | +0 / −6 |  |
| 2025-11-17 | `0f6a64997` | Entfernt | Remove duplicate import | 1 | +1 / −2 |  |
| 2025-11-17 | `86f769190` | Sonstiges | Subscribe to event class names | 32 | +73 / −78 |  |
| 2025-11-17 | `be48f70c4` | Sonstiges | Use attributes in Behat tests | 9 | +47 / −98 |  |
| 2025-11-17 | `bdb915fe8` | Aktualisiert | Update nglayouts_migration_versions table in DB schema | 1 | +11 / −10 |  |
| 2025-11-17 | `104748f74` | Release | Bump doctrine/doctrine-migrations-bundle to 3.7 | 1 | +1 / −1 |  |
| 2025-11-17 | `203389936` | Sonstiges | Replace lchrusciel/api-test-case with zenstruck/browser | 47 | +1889 / −3275 |  |
| 2025-11-17 | `dd40bd296` | Sonstiges | Downgrade Doctrine DBAL to 3.x | 1 | +2 / −2 |  |
| 2025-11-17 | `5d9d26009` | Aktualisiert | Fix tests | 7 | +84 / −8 |  |
| 2025-11-17 | `e004a96d7` | Sonstiges | Use PHPUnit deprecations reporter | 3 | +14 / −10 |  |
| 2025-11-19 | `594232342` | Sonstiges | Use array type in constriants | 5 | +5 / −5 |  |
| 2025-11-19 | `edf24f7a2` | Aktualisiert | Fix cloning parameter values | 1 | +5 / −1 |  |
| 2025-11-19 | `dd51db849` | Entfernt | Remove obsolete redirects | 3 | +0 / −61 |  |
| 2025-11-19 | `2896e828e` | Aktualisiert | Fix using EnumType form type | 1 | +1 / −1 |  |
| 2025-11-19 | `222572d72` | Sonstiges | Use symfony/mime component in transfer file upload | 2 | +1 / −2 |  |
| 2025-11-19 | `cc3d848bf` | Aktualisiert | Fix class name in Doctrine DBAL 3 | 2 | +6 / −5 |  |
| 2025-11-19 | `f86b7197d` | Sonstiges | Use DateTimeImmutable | 2 | +6 / −7 |  |
| 2025-11-19 | `b628cae37` | Aktualisiert | Fix SQLite and PotgreSQ class names | 5 | +18 / −18 |  |
| 2025-11-19 | `f841e775c` | Entfernt | Remove unused trait from RuleGroup class | 1 | +0 / −2 |  |
| 2025-11-20 | `3ffb9da42` | Hinzugefügt | Add missing typehints | 2 | +2 / −2 |  |
| 2025-11-20 | `49bcbc946` | Entfernt | Remove deprecations | 48 | +125 / −632 |  |
| 2025-11-20 | `47e9c6fc7` | Entfernt | Remove unused properties in tests | 3 | +0 / −15 |  |
| 2025-11-20 | `996c0a502` | Aktualisiert | Fix issues found by PHPStan | 3 | +4 / −5 |  |
| 2025-11-20 | `99e99e835` | Sonstiges | Master is 2.0 | 2 | +5 / −5 |  |
| 2025-11-20 | `1f3aec29b` | Release | Bump netgen/layouts-coding-standard to 3.0 | 1 | +1 / −1 |  |
| 2025-11-20 | `d3f46640f` | Sonstiges | Replace property with local variable | 5 | +15 / −28 |  |
| 2025-11-21 | `e7974b4e1` | Sonstiges | Convert ItemInterface and LocationInterface to property hooks | 11 | +30 / −42 |  |
| 2025-11-21 | `68102c875` | Sonstiges | Convert SearchQuery and SearchResult to property hooks | 1 | +7 / −3 |  |
| 2025-11-21 | `ecf1db9b2` | Aktualisiert | Update GitHub Actions config | 6 | +8 / −8 |  |
| 2025-11-21 | `c1f8d5433` | Entfernt | Remove obsolete require | 1 | +0 / −1 |  |
| 2025-11-21 | `7fdd6e72d` | Sonstiges | Make the configuration object final and non-lazy | 6 | +78 / −31 |  |
| 2025-11-21 | `167a1bd2d` | Sonstiges | Controllers do not have to be public any more | 2 | +78 / −78 |  |
| 2025-11-24 | `89edcfc82` | Entfernt | Remove lazy from service definitions and make classes final | 17 | +10 / −51 |  |
| 2025-11-24 | `87aee2478` | Entfernt | Remove unneeded config from tests | 3 | +0 / −10 |  |
| 2025-11-24 | `6599a4706` | Sonstiges | Make the attribute properties private for writing | 7 | +7 / −7 |  |
| 2025-11-24 | `51893352c` | Sonstiges | Make the design and pagelayout config nodes strings | 3 | +3 / −4 |  |
| 2025-11-24 | `8733b6937` | Entfernt | Remove ::$default* static property support for registering services, add missing attributes | 17 | +145 / −120 |  |
| 2025-11-24 | `439ad94b7` | Sonstiges | Use Uri\Rfc3986\Uri from PHP 8.5 | 7 | +83 / −42 |  |
| 2025-11-24 | `8534c9bb4` | Release | Bump dependencies | 1 | +2 / −2 |  |
| 2025-11-24 | `8c68b80de` | Sonstiges | Use callback in ChoiceType constraint | 1 | +12 / −2 |  |
| 2025-11-24 | `cbde9c8f9` | Sonstiges | Use array unpacking instead of iterator_to_array | 31 | +114 / −173 |  |
| 2025-11-25 | `9a9fc44b1` | Aktualisiert | Fix outputing manual/dynamic collection type | 6 | +16 / −10 |  |
| 2025-11-25 | `c1ca6c736` | Sonstiges | Do not use static event names | 5 | +4 / −27 |  |
| 2025-11-25 | `1ceb713a9` | Sonstiges | View builder and registries do not need to be public any more | 7 | +0 / −10 |  |
| 2025-11-25 | `8f7012fcc` | Sonstiges | Ignore errors caused by PHPStan not supporting Symfony 7.3 | 1 | +3 / −1 |  |
| 2025-11-25 | `bd86ffda1` | Entfernt | Remove unneeded query type mock | 4 | +7 / −35 |  |
| 2025-11-25 | `41205566b` | Umbenannt | Rename app templates folder in tests | 3 | +2 / −2 |  |
| 2025-11-26 | `e88254df7` | Aktualisiert | Update core API values to use property hooks | 258 | +2098 / −2749 |  |
| 2025-11-27 | `c0d57a899` | Hinzugefügt | Add possibility to implement lazy objects in hydrator | 1 | +30 / −2 |  |
| 2025-11-27 | `eea9c37a7` | Sonstiges | Implement lazy layout in Rule value object | 11 | +40 / −46 |  |
| 2025-11-27 | `ff6837f26` | Sonstiges | Implement lazy linked zone in Zone value object | 10 | +50 / −54 |  |
| 2025-11-27 | `30dc0fc92` | Sonstiges | Implement lazy query in Collection value object | 19 | +46 / −48 |  |
| 2025-11-27 | `2c79385a7` | Sonstiges | Implement lazy CMS item in Item value object | 13 | +53 / −137 |  |
| 2025-11-27 | `0aabcf852` | Umbenannt | Move status property to ValueStatusTrait | 16 | +4 / −48 |  |
| 2025-11-27 | `2940b1f5d` | Sonstiges | Convert isDraft/isPublished/isArchived to virtual properties | 27 | +270 / −273 |  |
| 2025-11-27 | `c8b4de372` | Sonstiges | Use property hooks for config values | 93 | +513 / −837 |  |
| 2025-11-27 | `3f1304d44` | Sonstiges | Make Result object use HydratorTrait | 8 | +100 / −62 |  |
| 2025-11-27 | `38f6fbd0f` | Sonstiges | Make definition objects use property hooks | 55 | +229 / −313 |  |
| 2025-11-27 | `f757a16b9` | Sonstiges | Convert various other classes to use property hooks | 77 | +292 / −509 |  |
| 2025-11-27 | `054dfb5c7` | Sonstiges | Convert config to use property hooks | 39 | +77 / −117 |  |
| 2025-11-27 | `53f9211f2` | Sonstiges | Convert parameter based structs to property hooks | 22 | +63 / −106 |  |
| 2025-11-27 | `b4d488539` | Sonstiges | Convert parameter list to use property hooks | 11 | +13 / −19 |  |
| 2025-11-27 | `bd4cc8cca` | Sonstiges | Convert parameters to use property hooks | 43 | +152 / −221 |  |
| 2025-11-27 | `58bfc95d4` | Sonstiges | CS fixes | 4 | +6 / −10 |  |
| 2025-11-27 | `22d3cff88` | Sonstiges | Convert parameter entity to use property hooks | 21 | +110 / −132 |  |
| 2025-11-27 | `9e635db82` | Umbenannt | Rename Layout::shared to Layout::isShared | 35 | +93 / −93 |  |
| 2025-11-27 | `d2b1f5f72` | Umbenannt | Rename alwaysAvailable properties to isAlwaysAvailable | 52 | +257 / −257 |  |
| 2025-11-27 | `ce6442d41` | Umbenannt | Rename rule and rule group enabled property to isEnabled | 35 | +144 / −144 |  |
| 2025-11-27 | `674cc263f` | Aktualisiert | Update boolean property names | 6 | +22 / −22 |  |
| 2025-11-27 | `3970ee18b` | Sonstiges | CS fixes | 17 | +25 / −25 |  |
| 2025-11-27 | `0f6fe20e0` | Sonstiges | Revert usage of Uri polyfill | 7 | +42 / −83 |  |
| 2025-11-27 | `e1cbd3922` | Aktualisiert | Fix Behat tests | 1 | +6 / −6 |  |
| 2025-11-27 | `8b9b5d4bb` | Hinzugefügt | Add missing form.isShared in layout create form | 1 | +1 / −0 |  |
| 2025-11-27 | `14626c68b` | Aktualisiert | Update .gitignore | 1 | +1 / −0 |  |
| 2025-11-27 | `ed2a8c0f9` | Sonstiges | Expose query type handler through query type | 6 | +41 / −5 |  |
| 2025-11-27 | `1b28a18c9` | Sonstiges | Specify that parameter constraints are lists | 3 | +5 / −5 |  |
| 2025-11-27 | `d90546b43` | Aktualisiert | Update PHPStan config | 22 | +97 / −35 |  |
| 2025-11-27 | `5bb672bd5` | Sonstiges | Run GitHub Actions on Symfony 7.4 | 5 | +7 / −7 |  |
| 2025-11-27 | `099061cbd` | Aktualisiert | Update test app console | 1 | +4 / −0 |  |
| 2025-11-27 | `07b8809f4` | Sonstiges | Allow Monolog 4.0 | 1 | +1 / −1 |  |
| 2025-11-27 | `1020fe760` | Aktualisiert | Update PHPStan config | 7 | +15 / −13 |  |
| 2025-11-28 | `691252c1e` | Entfernt | Remove unused method | 4 | +0 / −39 |  |
| 2025-11-28 | `04e5a3ba9` | Sonstiges | Document we return positive integers for counts | 7 | +46 / −9 |  |
| 2025-11-28 | `f8260a233` | Hinzugefügt | Add missing contracts depenencies | 9 | +10 / −8 |  |
| 2025-11-28 | `b913ecac7` | Sonstiges | Use JSON_THROW_ON_ERROR constant | 6 | +31 / −7 |  |
| 2025-11-28 | `8be88e490` | Entfernt | Remove unused properties | 1 | +0 / −8 |  |
| 2025-11-28 | `5164be638` | Sonstiges | CS fix | 1 | +1 / −3 |  |
| 2025-11-28 | `cf9565502` | Aktualisiert | Update PHPStan config | 2 | +7 / −4 |  |
| 2025-11-28 | `3ce4d2ce5` | Entfernt | Remove unneeded usage of expectExceptionMessageMatches | 4 | +4 / −32 |  |
| 2025-11-28 | `0302884ae` | Entfernt | Remove config tests, they don't work any more on Symfony 7.4 | 19 | +0 / −3826 |  |
| 2025-11-28 | `be2af84d0` | Sonstiges | Allow symfony/event-dispatcher-contracts 2.x | 1 | +1 / −1 |  |
| 2025-11-28 | `a937b1be7` | Aktualisiert | Fix PHPStan issues | 1 | +3 / −3 |  |
| 2025-11-28 | `0b7d283f4` | Hinzugefügt | Add missing required notation for struct properties | 3 | +6 / −0 |  |
| 2025-11-28 | `1b0819763` | Entfernt | Remove TranslatableParameterBuilderFactory, merged implementation into main | 22 | +159 / −257 |  |
| 2025-11-28 | `93575b959` | Entfernt | Remove CompoundParameterDefinition, merged implementation into main | 25 | +123 / −136 |  |
| 2025-11-28 | `a3398f104` | Sonstiges | CS fixes | 33 | +48 / −48 |  |
| 2025-11-28 | `2cee6f385` | Sonstiges | Public properties in traits do not need to be final | 4 | +4 / −4 |  |
| 2025-11-28 | `ef097f154` | Hinzugefügt | Add final to methods that are not meant to be overriden | 8 | +38 / −38 |  |
| 2025-11-28 | `edb59271a` | Sonstiges | Use property hooks for view system | 101 | +432 / −518 |  |
| 2025-11-28 | `9d0097692` | Sonstiges | Make method final | 1 | +1 / −1 |  |
| 2025-11-28 | `eedb65fb1` | Sonstiges | Allow Symfony 8 | 2 | +32 / −32 |  |
| 2025-11-28 | `b40ff1682` | Sonstiges | Only run MySQL and PostgreSQL tests once | 1 | +23 / −1 |  |
| 2025-11-28 | `ac9359043` | Aktualisiert | Fix typo in actions | 1 | +6 / −6 |  |
| 2025-11-28 | `471d24707` | Hinzugefügt | Add Symfony 8 method arguments to voters | 1 | +2 / −1 |  |
| 2025-11-29 | `dab388101` | Sonstiges | Use instance method for >expects() calls | 68 | +391 / −390 |  |
| 2025-11-29 | `e4644e746` | Sonstiges | Implement Stringable where needed | 2 | +4 / −2 |  |
| 2025-11-29 | `8a7b8fbab` | Sonstiges | Use ::class on objects | 5 | +5 / −11 |  |
| 2025-11-29 | `3b2b31a96` | Sonstiges | Use static::class in abstract method | 1 | +1 / −1 |  |
| 2025-11-29 | `e5c8a681e` | Sonstiges | Misc fixes | 43 | +93 / −175 |  |
| 2025-11-29 | `76d5912e1` | Umbenannt | Rename Exception listeners to Throwable listeners | 5 | +66 / −66 |  |
| 2025-11-29 | `41b7d6843` | Sonstiges | Replace Generator typehint with iterable | 38 | +107 / −144 |  |
| 2025-11-29 | `0bc4afea2` | Sonstiges | CS fixes in templates | 2 | +2 / −2 |  |
| 2025-11-30 | `beced5843` | Hinzugefügt | Add missing PHPDoc | 1 | +3 / −0 |  |
| 2025-11-30 | `563bd1250` | Aktualisiert | Update dependencies | 1 | +2 / −2 |  |
| 2025-11-30 | `772a0e836` | Entfernt | Remove fixed ignore from PHPStan config files | 2 | +0 / −2 |  |
| 2025-11-30 | `4321e9e69` | Entfernt | Remove unused doctrine/doctrine-bundle require | 1 | +0 / −1 |  |
| 2025-11-30 | `4da024df9` | Sonstiges | Cast and query expression to string and allow Doctrine DBAL 4 | 9 | +35 / −40 |  |
| 2025-11-30 | `8cb93f092` | Sonstiges | Migrate Behat config to PHP | 9 | +178 / −104 |  |
| 2025-11-30 | `72889e727` | Aktualisiert | Fix route name | 1 | +1 / −1 |  |
| 2025-11-30 | `0422cba71` | Sonstiges | CS fixes in templates | 2 | +2 / −2 |  |
| 2025-11-30 | `6ce8b4569` | Aktualisiert | Fix div elements not allowed in span and button | 2 | +3 / −3 |  |
| 2025-11-24 | `14ab0999a` | Sonstiges | Use Uri\Rfc3986\Uri from PHP 8.5 | 7 | +83 / −43 |  |

## 2025-12 (197 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-12-01 | `1754863a5` | Sonstiges | Properly inject container into controllers | 3 | +1 / −28 |  |
| 2025-12-01 | `f0fa0d2f4` | Aktualisiert | Update license copyright year | 1 | +1 / −1 |  |
| 2025-12-01 | `1a8f4034b` | Aktualisiert | Fix PHPStan issue | 2 | +8 / −4 |  |
| 2025-12-01 | `a209e644f` | Aktualisiert | Update install instructions | 1 | +11 / −11 |  |
| 2025-12-01 | `6f58b05ac` | Sonstiges | Replace nglayouts_format_datetime with format_datetime from Twig Intl extension | 10 | +8 / −75 |  |
| 2025-12-01 | `e4b38176a` | Aktualisiert | Update PHPUnit configuration | 2 | +6 / −4 |  |
| 2025-12-01 | `9d4ea5ea1` | Sonstiges | Reorganize JSON schema for import/export for better readability | 18 | +95 / −74 |  |
| 2025-12-01 | `48199b5a0` | Sonstiges | Replace swaggest/json-schema with more maintained justinrainbow/json-schema | 5 | +15 / −30 |  |
| 2025-12-02 | `fef8f4a16` | Hinzugefügt | Add generics notation to ViewProvideInterface | 14 | +43 / −2 |  |
| 2025-12-02 | `124603b23` | Hinzugefügt | Add helper templates to easily include preview plugins | 2 | +2 / −0 |  |
| 2025-12-02 | `1262639ca` | Release | Bump justinrainbow/json-schema to 6.6.3 to fix issues with php-enum warnings on obsolete versions | 1 | +1 / −1 |  |
| 2025-12-02 | `d0a5b605a` | Aktualisiert | Update github actions config | 1 | +10 / −10 |  |
| 2025-12-02 | `e6bcc5519` | Sonstiges | Enable Twig Extra bundle in test app | 1 | +1 / −0 |  |
| 2025-12-02 | `2bd62fcad` | Sonstiges | Use asset package for Netgen Layouts logo in debug toolbar | 1 | +2 / −2 |  |
| 2025-12-02 | `6ff089e13` | Aktualisiert | Update composer.json | 1 | +0 / −1 |  |
| 2025-12-03 | `0fac880f4` | Sonstiges | Split CollectViewParametersEvent into RenderViewEvent and BuildViewEvent | 24 | +374 / −377 |  |
| 2025-12-03 | `cef094de7` | Umbenannt | Rename ConfigureMenuEvent to BuildAdminMenuEvent | 4 | +14 / −8 |  |
| 2025-12-03 | `ebf6dae4e` | Aktualisiert | Refactor views not to return properties from internal parameters | 13 | +54 / −100 |  |
| 2025-12-03 | `1bfea6fdd` | Entfernt | Remove usage of deprecated DI Extension class | 3 | +3 / −3 |  |
| 2025-12-03 | `d9c697487` | Umbenannt | Move twig.yaml template out of framework subfolder | 4 | +4 / −4 |  |
| 2025-12-03 | `964db5a50` | Entfernt | Remove unused dependencies | 3 | +9 / −9 |  |
| 2025-12-03 | `d83cfd977` | Umbenannt | Rename ContainerConfiguration class and remove netgen_layouts.configuration.container service | 6 | +26 / −78 |  |
| 2025-12-03 | `cf1d9569d` | Sonstiges | Re-add symfony/translation dependency | 1 | +1 / −0 |  |
| 2025-12-03 | `efef71240` | Hinzugefügt | Add missing final keywords | 50 | +568 / −579 |  |
| 2025-12-03 | `662486f21` | Aktualisiert | Fix CS | 12 | +85 / −161 |  |
| 2025-12-03 | `71224a7c7` | Sonstiges | Convert abstract query handler to trait | 7 | +52 / −100 |  |
| 2025-12-03 | `b72a2015d` | Sonstiges | CS fix | 6 | +32 / −31 |  |
| 2025-12-04 | `1dfede980` | Sonstiges | Switch mocks to stubs in tests | 123 | +1121 / −1661 |  |
| 2025-12-04 | `c5bde346e` | Hinzugefügt | Add back mocks where needed | 13 | +232 / −60 |  |
| 2025-12-04 | `891ef4433` | Entfernt | Remove obsolete code | 1 | +0 / −8 |  |
| 2025-12-04 | `ce44c33ec` | Release | Bump PHPUnit to 12.5 | 1 | +1 / −1 |  |
| 2025-12-04 | `42eeaf30f` | Sonstiges | Make it possible for ParameterStruct::$parameterValues to be private(set) | 16 | +276 / −142 |  |
| 2025-12-04 | `3cc1a2ffd` | Hinzugefügt | Add missing final in traits | 2 | +2 / −2 |  |
| 2025-12-04 | `6d8ab516c` | Aktualisiert | Fix CS | 1 | +1 / −1 |  |
| 2025-12-04 | `80ce2ebea` | Aktualisiert | Update script for google maps | 1 | +3 / −1 |  |
| 2025-12-04 | `032b3f182` | Aktualisiert | Update PHPStan config | 2 | +2 / −2 |  |
| 2025-12-05 | `c7c0461eb` | Sonstiges | Cleanup test app | 11 | +11 / −135 |  |
| 2025-12-05 | `81a7bf499` | Umbenannt | Rename test entities | 80 | +429 / −429 |  |
| 2025-12-05 | `992f87ba8` | Aktualisiert | Update test app kernel | 2 | +3 / −3 |  |
| 2025-12-05 | `f0d3bfe38` | Sonstiges | CS fixes | 2 | +16 / −16 |  |
| 2025-12-05 | `1c73cd56f` | Entfernt | Remove unused abstract class and method | 6 | +7 / −131 |  |
| 2025-12-05 | `f4ba9b622` | Hinzugefügt | Add missing final keywords | 1 | +5 / −5 |  |
| 2025-12-06 | `ed8ae0ee7` | Sonstiges | No need for null coallesce assignment operator | 1 | +13 / −13 |  |
| 2025-12-06 | `f09c4b6b4` | Umbenannt | Move core constraints from enterprise | 15 | +570 / −1 |  |
| 2025-12-06 | `63ced2626` | Aktualisiert | Update tests | 37 | +556 / −530 |  |
| 2025-12-06 | `14230218e` | Aktualisiert | Update BlockDefinitionFactory | 5 | +31 / −31 |  |
| 2025-12-06 | `483e5d4b5` | Aktualisiert | Fix PHPStan issue | 2 | +4 / −7 |  |
| 2025-12-06 | `c10ff614e` | Aktualisiert | Refactor DatabaseTrait a bit | 1 | +18 / −14 |  |
| 2025-12-06 | `fb22f4b8e` | Entfernt | Remove duplicate tearDown methods | 38 | +8 / −207 |  |
| 2025-12-06 | `78c9982ca` | Entfernt | Remove unused method | 1 | +1 / −16 |  |
| 2025-12-06 | `8d8572d0a` | Sonstiges | Build stub handler parameters with parameter builder | 14 | +226 / −453 |  |
| 2025-12-06 | `1bdeccdac` | Hinzugefügt | Add isTranslatable to parameter definition | 15 | +56 / −45 |  |
| 2025-12-06 | `04ebf63d6` | Sonstiges | Cleanup test fixtures | 3 | +17 / −87 |  |
| 2025-12-06 | `25ba2e864` | Sonstiges | Make parameter builder support translatable parameters by default | 12 | +22 / −21 |  |
| 2025-12-06 | `0fc255a3b` | Sonstiges | Reorder methods | 2 | +10 / −10 |  |
| 2025-12-06 | `610856022` | Sonstiges | CS fix | 2 | +2 / −2 |  |
| 2025-12-06 | `f5e2d6c09` | Sonstiges | Make sure building parameters fails when using translatable option on untranslatable parameters | 1 | +0 / −4 |  |
| 2025-12-06 | `1b5ae6af6` | Sonstiges | There is no need for default values in ParameterBuilder | 1 | +5 / −5 |  |
| 2025-12-06 | `671c1fe10` | Aktualisiert | Fix API tests | 4 | +8 / −8 |  |
| 2025-12-07 | `0783bfe71` | Aktualisiert | Fix phpstan issue | 3 | +5 / −3 |  |
| 2025-12-07 | `c34671b3e` | Sonstiges | CS fixes | 2 | +10 / −2 |  |
| 2025-12-07 | `eed7cd2cd` | Sonstiges | CS fixes | 55 | +236 / −212 |  |
| 2025-12-07 | `55d13d022` | Aktualisiert | Fix postgresql test | 1 | +1 / −1 |  |
| 2025-12-08 | `31348ec9d` | Sonstiges | Replace eligible compiler passes with !tagged_locator | 25 | +44 / −933 |  |
| 2025-12-08 | `7ac3ac4ea` | Aktualisiert | Fix PHPDoc | 5 | +11 / −11 |  |
| 2025-12-08 | `6245919b0` | Sonstiges | Implement a php object for zone mappings when changing layout type | 9 | +91 / −90 |  |
| 2025-12-08 | `332ed1d05` | Aktualisiert | Update PHPStan config | 1 | +2 / −5 |  |
| 2025-12-08 | `c6b18ba1d` | Entfernt | Remove service aliases | 2 | +1 / −7 |  |
| 2025-12-09 | `cdafb792f` | Sonstiges | Template resolver needs to be lazy | 1 | +1 / −0 |  |
| 2025-12-09 | `a0c0736e2` | Aktualisiert | Fix PHPDoc | 1 | +0 / −4 |  |
| 2025-12-09 | `8b83c64d9` | Sonstiges | Account for items in exported data being null (unknown) | 1 | +12 / −4 |  |
| 2025-12-10 | `a4d204665` | Entfernt | Remove reference to obsolete target type names | 2 | +2 / −6 |  |
| 2025-12-10 | `35c147053` | Sonstiges | Use CoversTrait where needed | 12 | +24 / −22 |  |
| 2025-12-10 | `bc4f0d671` | Sonstiges | Relax coverage strictness | 2 | +0 / −2 |  |
| 2025-12-11 | `095e0db2e` | Entfernt | Remove obsolete migration scripts | 3 | +0 / −471 |  |
| 2025-12-11 | `11650d4f3` | Sonstiges | Do not allow nulls in string configuration | 4 | +9 / −9 |  |
| 2025-12-11 | `95fec6720` | Aktualisiert | Update PHPDoc | 19 | +19 / −19 |  |
| 2025-12-11 | `d73f9e2ea` | Release | Bump behat/behat version | 1 | +1 / −1 |  |
| 2025-12-11 | `ae7c1878e` | Sonstiges | Allow doctrine/doctrine-migrations-bundle v4 | 1 | +1 / −1 |  |
| 2025-12-11 | `6461b3d1f` | Aktualisiert | Update GH actions to use ramsey/composer-install | 5 | +25 / −16 |  |
| 2025-12-12 | `5b22dfc5a` | Entfernt | Remove obsolete @throws tags | 19 | +0 / −77 |  |
| 2025-12-12 | `4223bc518` | Entfernt | Remove obsolete @param tags | 1 | +0 / −3 |  |
| 2025-12-12 | `65a5a93ad` | Entfernt | Remove obsolete PHPDoc | 43 | +29 / −158 |  |
| 2025-12-12 | `f028ee713` | Sonstiges | Use more PHP 8 style code | 32 | +60 / −66 |  |
| 2025-12-12 | `a8c63ff78` | Sonstiges | Code style fixes | 24 | +77 / −88 |  |
| 2025-12-12 | `fe9ab2cd4` | Entfernt | Remove obsolete inline @var annotations | 6 | +8 / −18 |  |
| 2025-12-13 | `e6230d90c` | Sonstiges | Do not use UuidGeneratorTrait in service tests | 3 | +62 / −170 |  |
| 2025-12-13 | `02befe412` | Sonstiges | Switch uuids to Symfony Uid component | 210 | +1026 / −969 |  |
| 2025-12-13 | `5785a32ca` | Sonstiges | Use Symfony VarExporter hydrator instead of custom variant | 15 | +49 / −189 |  |
| 2025-12-13 | `0561c3b60` | Sonstiges | Use specific ParameterBag methods where possible | 19 | +29 / −28 |  |
| 2025-12-13 | `2f6a8be3f` | Sonstiges | protected(set) properties can be private(set) | 6 | +16 / −13 |  |
| 2025-12-13 | `58bd1988c` | Sonstiges | Use Request::getPayload | 19 | +189 / −175 |  |
| 2025-12-13 | `db5d940d1` | Sonstiges | CS fix | 1 | +1 / −3 |  |
| 2025-12-13 | `97e8a1c30` | Entfernt | Remove obsolete validation | 1 | +3 / −22 |  |
| 2025-12-13 | `f5c0ab5f2` | Sonstiges | Do not use strict option in Choice constraint | 2 | +0 / −3 |  |
| 2025-12-13 | `2585038ff` | Sonstiges | Validate new_type in change collection type API endpoint | 2 | +31 / −2 |  |
| 2025-12-13 | `58798083d` | Aktualisiert | Improve zone mappings validation | 2 | +109 / −0 |  |
| 2025-12-14 | `3ebfa6f72` | Sonstiges | Use InputBag::all | 2 | +4 / −6 |  |
| 2025-12-14 | `b91c9d857` | Sonstiges | Use array_key_exists | 3 | +8 / −5 |  |
| 2025-12-14 | `5da8ff575` | Entfernt | Remove obsolete consts | 3 | +3 / −9 |  |
| 2025-12-14 | `341beeeec` | Sonstiges | Use Timezone constraint | 3 | +2 / −19 |  |
| 2025-12-14 | `3134b17ed` | Sonstiges | Use yield in getExtendedTypes | 2 | +2 / −2 |  |
| 2025-12-15 | `34b550d03` | Aktualisiert | Fix CS | 1 | +2 / −2 |  |
| 2025-12-15 | `88df21a24` | Sonstiges | Revert "Make it possible for ParameterStruct::$parameterValues to be private(set)" | 16 | +142 / −276 |  |
| 2025-12-15 | `2638d8242` | Sonstiges | Use setter option in ParametersType form to make it possible parameterValues is readonly | 10 | +301 / −121 |  |
| 2025-12-15 | `9fe12ce9f` | Entfernt | Remove obsolete todos | 2 | +0 / −5 |  |
| 2025-12-15 | `57b8b1849` | Aktualisiert | Fix PHPDoc | 4 | +3 / −10 |  |
| 2025-12-15 | `9378ca0b5` | Sonstiges | Use range constraint in NumberType | 1 | +2 / −6 |  |
| 2025-12-15 | `c69d1117e` | Umbenannt | Rename mapper classes | 33 | +110 / −110 |  |
| 2025-12-15 | `7ab4113b8` | Entfernt | Remove obsolete extended_type DI tag attribute | 1 | +1 / −1 |  |
| 2025-12-15 | `276824324` | Sonstiges | Use self:: to access constant on interface | 1 | +2 / −2 |  |
| 2025-12-15 | `e549a0d25` | Entfernt | Remove Descriptor class | 4 | +8 / −20 |  |
| 2025-12-15 | `ff1b0319f` | Sonstiges | Get rid of data provider closure binding in tests | 19 | +155 / −51 |  |
| 2025-12-15 | `c4feb180b` | Sonstiges | CS fixes | 14 | +28 / −28 |  |
| 2025-12-15 | `68838eee6` | Entfernt | Remove ArrayAccess, Countable and IteratorAggregate from Layout | 4 | +3 / −89 |  |
| 2025-12-15 | `f510108f9` | Sonstiges | Use .blocks in placeholder.html.twig | 1 | +1 / −1 |  |
| 2025-12-15 | `a41264684` | Entfernt | Remove ArrayAccess, Countable and IteratorAggregate from Placeholder | 4 | +8 / −84 |  |
| 2025-12-15 | `519c45d9e` | Sonstiges | Use DoesNotPerformAssertions | 12 | +59 / −143 |  |
| 2025-12-15 | `81efb2066` | Sonstiges | Use symfony/emoji component instead of custom flag generator | 7 | +2 / −135 |  |
| 2025-12-15 | `e5cdcd61e` | Entfernt | Remove custom wrapper around Symfonys HtmlSanitizer | 7 | +21 / −68 |  |
| 2025-12-15 | `121715949` | Sonstiges | Use lowercase identifiers for statuses in import schema | 19 | +37 / −37 |  |
| 2025-12-15 | `9bd9659c4` | Entfernt | Remove constant | 1 | +1 / −3 |  |
| 2025-12-15 | `c6f3894ec` | Sonstiges | Replace ValueResolver constants with Status enum | 28 | +152 / −113 |  |
| 2025-12-15 | `2a4fa99df` | Sonstiges | CS fix | 4 | +0 / −9 |  |
| 2025-12-15 | `b9cb4785a` | Sonstiges | Switch API enum to strings | 11 | +63 / −32 |  |
| 2025-12-15 | `60aa37bad` | Entfernt | Remove duplicated Status enum | 34 | +41 / −135 |  |
| 2025-12-15 | `12dab770d` | Sonstiges | Switch VisibilityVoterResult enum to non-backed enum | 1 | +4 / −4 |  |
| 2025-12-16 | `29d4b975b` | Sonstiges | Switch UrlType enum to non-backed enum | 5 | +94 / −18 |  |
| 2025-12-16 | `d0046ad9a` | Sonstiges | Use sprintf in choice_label for enums | 2 | +12 / −12 |  |
| 2025-12-16 | `43094d2be` | Aktualisiert | Refactor ItemRuntime, make UrlType required in URL generator | 4 | +42 / −28 |  |
| 2025-12-16 | `f6b560b78` | Aktualisiert | Update test | 1 | +1 / −1 |  |
| 2025-12-16 | `5f3fba205` | Sonstiges | Allow enum type to define option labels | 4 | +52 / −1 |  |
| 2025-12-16 | `0a16ab3a2` | Sonstiges | Use strings for collection type | 19 | +31 / −39 |  |
| 2025-12-16 | `5d77787d5` | Sonstiges | Make sure throwable uses our normalizer only in API requests | 4 | +31 / −17 |  |
| 2025-12-16 | `4c183a938` | Sonstiges | Include app routes in error output | 1 | +1 / −1 |  |
| 2025-12-16 | `1ef9c1519` | Umbenannt | Rename SetIsApiRequestListener to SetIsAppRequestListener | 12 | +43 / −43 |  |
| 2025-12-16 | `932903874` | Umbenannt | Rename ApiCsrfValidationListener to AppCsrfValidationListener | 4 | +11 / −11 |  |
| 2025-12-16 | `17a293aef` | Aktualisiert | Fix error message | 1 | +1 / −1 |  |
| 2025-12-16 | `e1cfdcb87` | Sonstiges | Document that parameter constraint closures need to return constraints | 5 | +6 / −6 |  |
| 2025-12-16 | `ab2c4cda0` | Hinzugefügt | Add narrower types | 10 | +16 / −61 |  |
| 2025-12-16 | `81b09291d` | Aktualisiert | Fix some PHPStan issues | 2 | +6 / −14 |  |
| 2025-12-16 | `5fe5ad3a5` | Sonstiges | Reconfigure registry services in DI configuration | 35 | +160 / −215 |  |
| 2025-12-16 | `2dac20ab5` | Aktualisiert | Fix PHPStan issues | 7 | +4 / −64 |  |
| 2025-12-16 | `510ff4cfb` | Aktualisiert | Update PHPStan | 2 | +2 / −5 |  |
| 2025-12-16 | `a342e2bd5` | Sonstiges | View system can use objects instead of mixed | 28 | +41 / −86 |  |
| 2025-12-16 | `3ea39b4e2` | Aktualisiert | Fix return typehint | 1 | +2 / −1 |  |
| 2025-12-16 | `aab956c69` | Sonstiges | Use 0o octal notation | 1 | +12 / −12 |  |
| 2025-12-17 | `fbcfc3c04` | Hinzugefügt | Add nofollow noopener noreferrer to ajax paging URLs | 1 | +4 / −4 | 1.4.13 origin/1.4 |
| 2025-12-17 | `1a35168f3` | Sonstiges | Do not run CSRF validation on app forms | 2 | +13 / −6 |  |
| 2025-12-17 | `6b17980aa` | Hinzugefügt | Add nofollow noopener noreferrer to ajax paging URLs | 1 | +4 / −4 |  |
| 2025-12-17 | `a7027337f` | Aktualisiert | Update tests | 6 | +34 / −20 |  |
| 2025-12-17 | `4f1be80de` | Sonstiges | Cleanup Composer scripts | 3 | +19 / −9 |  |
| 2025-12-17 | `04e3b659f` | Sonstiges | Exclude tests/application/config from PHP CS Fixer | 1 | +1 / −1 |  |
| 2025-12-17 | `e2037baa5` | Sonstiges | Restore usage of API flags in request body listener and admin auth listener | 6 | +21 / −21 |  |
| 2025-12-17 | `a90e72cad` | Hinzugefügt | New build | 2 | +0 / −0 |  |
| 2025-12-17 | `0afbc94e1` | Hinzugefügt | New build | 1 | +0 / −0 |  |
| 2025-12-17 | `32de30cb7` | Sonstiges | Use PHPStan extension installer | 3 | +2 / −12 |  |
| 2025-12-17 | `33df406b9` | Aktualisiert | Update MySQL schema | 1 | +19 / −19 |  |
| 2025-12-17 | `d58e5e32e` | Sonstiges | Do not use schema object to create DB tables in migrations | 5 | +241 / −212 |  |
| 2025-12-17 | `0dc6eeb16` | Aktualisiert | Update test schema, remove unused mssql schema | 2 | +69 / −359 |  |
| 2025-12-17 | `7e4956c66` | Entfernt | Remove unused fixtures dump | 1 | +0 / −600 |  |
| 2025-12-17 | `8ca4f1bcf` | Sonstiges | Disable Composer cache in GH actions | 5 | +5 / −0 |  |
| 2025-12-17 | `5fc054059` | Aktualisiert | Update autload config | 1 | +0 / −2 |  |
| 2025-12-17 | `3782d86fb` | Aktualisiert | Update test | 1 | +2 / −2 |  |
| 2025-12-17 | `d7b04962b` | Sonstiges | Introduce more specific types where possible | 22 | +62 / −40 |  |
| 2025-12-18 | `179080620` | Entfernt | Remove phpstan/phpstan-symfony | 3 | +0 / −13 |  |
| 2025-12-18 | `0e4b4758c` | Sonstiges | Reorder some types | 4 | +10 / −10 |  |
| 2025-12-18 | `cc88714aa` | Sonstiges | Replace remaining scalar nodes with string nodes | 3 | +18 / −22 |  |
| 2025-12-18 | `a8b917978` | Aktualisiert | Fix tests | 1 | +8 / −2 |  |
| 2025-12-18 | `c9e841939` | Sonstiges | CS fixes | 5 | +16 / −35 |  |
| 2025-12-18 | `dfef4f9c3` | Aktualisiert | Update core test case to fix using undefined property | 6 | +263 / −273 |  |
| 2025-12-18 | `5a144c4a1` | Sonstiges | Centralize building validator in tests | 31 | +177 / −220 |  |
| 2025-12-18 | `e234c63df` | Sonstiges | Use only symfony/event-dispatcher-contracts 3.x | 1 | +1 / −1 |  |
| 2025-12-18 | `8ddcf295b` | Zusammengeführt | Merge branch 'master' into 2.0 | 0 | +0 / −0 |  |
| 2025-12-18 | `2ca30ba41` | Entfernt | Remove unused class | 1 | +0 / −35 |  |
| 2025-12-19 | `d736a9399` | Entfernt | Remove unused parameter | 3 | +1 / −8 |  |
| 2025-12-27 | `58599cd24` | Aktualisiert | Update dependencies | 1 | +1 / −1 |  |
| 2025-12-29 | `a77e76f79` | Hinzugefügt | Add PHP 8.5 to test matrix | 1 | +3 / −3 |  |
| 2025-12-29 | `46149d88c` | Aktualisiert | Update PHPDoc | 2 | +1 / −4 |  |
| 2025-12-29 | `99a43533b` | Sonstiges | There is no need for some abstract methods to be public | 40 | +878 / −589 |  |
| 2025-12-29 | `f2584e5c5` | Aktualisiert | Fix data provider method name | 3 | +6 / −9 |  |
| 2025-12-29 | `b6c5aa9d5` | Aktualisiert | Fix class not being final | 1 | +1 / −1 |  |
| 2025-12-29 | `49239ef1e` | Aktualisiert | Update PHPDoc for data providers | 119 | +608 / −24 |  |
| 2025-12-29 | `f72807cc4` | Aktualisiert | Fix typehints | 4 | +7 / −4 |  |
| 2025-12-29 | `f3a279eac` | Sonstiges | CS fix | 1 | +2 / −2 |  |
| 2025-12-30 | `c91c92071` | Sonstiges | Debug Behat action | 1 | +9 / −1 |  |
| 2025-12-30 | `e82f8cdf6` | Aktualisiert | Update GH Actions to use PHP 8.5 | 4 | +4 / −4 |  |
| 2025-12-30 | `55ed72da4` | Sonstiges | Cast offset to string in DynamicParameters | 1 | +1 / −1 |  |
| 2025-12-30 | `4953a08f8` | Sonstiges | Run Behat GH action via PHP built in server | 2 | +12 / −7 |  |
| 2025-12-30 | `8e3ba0e33` | Sonstiges | Use InstalledVersions from Composer instead of jean85/pretty-package-versions | 3 | +5 / −5 |  |
| 2025-12-30 | `5e21e6144` | Sonstiges | Use emodric/twig-package-versions 2.0 | 1 | +1 / −1 |  |
| 2025-12-30 | `7929340e7` | Entfernt | Remove unneeded behat dependencies | 2 | +1 / −8 |  |
| 2025-12-31 | `31e28ef7a` | Sonstiges | Use GH action to setup Chrome Web Driver | 2 | +7 / −13 |  |
| 2025-12-31 | `4c4271f13` | Aktualisiert | Fix issues with PHPStan | 3 | +6 / −0 |  |
| 2025-12-31 | `500cda504` | Sonstiges | Use Uuid v7 in tests | 78 | +287 / −287 |  |
| 2025-12-31 | `f505309e5` | Sonstiges | Use Uuid v7 by default | 1 | +1 / −1 |  |
| 2025-12-31 | `206c29284` | Hinzugefügt | Add log output for chromedriver | 1 | +7 / −1 |  |

## 2026-01 (21 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-01-08 | `e17483b3f` | Entfernt | Remove usage of default_index_method, it is deprecated in Symfony 8.1 | 21 | +111 / −112 |  |
| 2026-01-08 | `57de8cd93` | Release | Bump doctrine/collections to 2.5 | 1 | +1 / −1 |  |
| 2026-01-08 | `0de13279b` | Entfernt | Remove usage of friends-of-behat/suite-settings-extension | 36 | +3 / −15 |  |
| 2026-01-08 | `bfeaf706e` | Zusammengeführt | Merge behat into test app | 40 | +124 / −153 |  |
| 2026-01-08 | `9c8f9d849` | Sonstiges | Cleanup behat config | 1 | +1 / −6 |  |
| 2026-01-09 | `f37b2f8ac` | Release | Bump zenstruck/browser to 1.10 | 1 | +1 / −1 |  |
| 2026-01-12 | `e7a7535ec` | Entfernt | Remove Behat from Composer if running tests on Symfony 8 | 3 | +16 / −5 |  |
| 2026-01-13 | `9108fcc86` | Release | Bump friendsofsymfony/http-cache-bundle to 3.3 | 1 | +1 / −1 |  |
| 2026-01-16 | `95d7c2fa8` | Release | Bump doctrine/collections to 2.6 | 1 | +1 / −1 |  |
| 2026-01-16 | `f22a8786e` | Aktualisiert | Update league/uri-polyfill to 7.8 | 1 | +1 / −1 |  |
| 2026-01-20 | `9526b65d6` | Aktualisiert | Fix PHPDoc | 3 | +4 / −4 |  |
| 2026-01-20 | `dd3030811` | Hinzugefügt | Add the rule group root to the postgresql schema | 1 | +337 / −0 |  |
| 2026-01-22 | `c0b2f8bd2` | Zusammengeführt | Merge pull request #35 from wizhippo/pgsql-missing-grp-data | 0 | +0 / −0 |  |
| 2026-01-22 | `0594b79df` | Hinzugefügt | Add indexes to PostgreSQL schema | 1 | +28 / −24 |  |
| 2026-01-22 | `1820d7a4e` | Hinzugefügt | Add missing priority argument to AsTargetType attribute | 2 | +8 / −3 |  |
| 2026-01-22 | `1a1d77bbb` | Umbenannt | Rename attribute in netgen_layouts.parameter_type.form_mapper DI tag | 2 | +19 / −19 |  |
| 2026-01-22 | `041ce6259` | Umbenannt | Rename attribute arguments to match DI tag attributes | 5 | +8 / −8 |  |
| 2026-01-22 | `ff9120f0d` | Release | Bump dependencies | 1 | +2 / −2 |  |
| 2026-01-26 | `576db8f70` | Release | Bump dependencies | 1 | +3 / −3 |  |
| 2026-01-26 | `692f86faf` | Sonstiges | Exclude reference.php from PHPStan checks | 1 | +1 / −0 |  |
| 2026-01-26 | `1219b65fc` | Sonstiges | Use new Twig syntax | 4 | +9 / −12 |  |

## 2026-02 (6 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-02-11 | `e8a47e536` | Entfernt | Remove unneeded with() calls for stubs | 67 | +281 / −752 |  |
| 2026-02-13 | `f65d58795` | Sonstiges | Limit node to v14 | 1 | +3 / −0 |  |
| 2026-02-13 | `bedadcd15` | Sonstiges | Final build for 2.0 | 1 | +0 / −0 |  |
| 2026-02-16 | `0d7e5de03` | Aktualisiert | Update dependencies | 1 | +1 / −1 |  |
| 2026-02-19 | `f694a7394` | Entfernt | Remove unused path from autoload-dev | 1 | +0 / −1 |  |
| 2026-02-25 | `c7a901af0` | Release | Bump PHPUnit to v13 | 1 | +3 / −3 | 2.0.0 |

## 2026-04 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-19 | `3ec0262b2` | Aktualisiert | fix: add Twig/PHP 8.4 private(set) getter shims to Parameter class | 2 | +15 / −2 | 2.0.0-se7enx.1 se7enxweb/master se7enxweb/main se7enxweb/2.0.x |
