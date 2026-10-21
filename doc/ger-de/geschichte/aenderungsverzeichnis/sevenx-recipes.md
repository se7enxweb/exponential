# Änderungsverzeichnis: sevenx-recipes

Jede Änderung an `sevenx-recipes` seit Beginn der se7enxweb-Ära, die älteste zuerst: 74 Änderungen an 2407 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 41 |
| Hinzugefügt | 25 |
| Sonstiges | 7 |
| Entfernt | 1 |

## 2026-03 (9 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-12 | `c60b6b7` | Hinzugefügt | Add Flex recipe for se7enxweb/exponential-platform-dxp | 64 | +120337 / −0 |  |
| 2026-03-12 | `2ad788f` | Aktualisiert | Fix index.json: add branch, recipe-conflicts, versions, is_contrib keys required by symfony/flex Downloader | 1 | +4 / −0 |  |
| 2026-03-17 | `4411912` | Hinzugefügt | Added: Added recipes support for forks of netgen packages: site-bundle and tagsbundle. Enhancements. | 7 | +79 / −1 |  |
| 2026-03-21 | `966f53f` | Aktualisiert | Updated: Replaced Ibexa Product Name in user facing templates with logos / trademarks. Rebranding. | 2 | +4 / −4 |  |
| 2026-03-22 | `16a3e6f` | Hinzugefügt | Added: Added recipe files for se7enxweb/exponential-platform-dxp-skeleton package. Bugfix. | 64 | +2738 / −0 |  |
| 2026-03-22 | `0b5b79c` | Hinzugefügt | Added: Added recipe files for se7enxweb/exponential-platform-dxp-skeleton package. Bugfix. | 1 | +112008 / −112008 |  |
| 2026-03-25 | `f735ef7` | Aktualisiert | Updated: Rebranding default installation welcome message usage of product name / vendor / doc links / text translations. Rebranding. | 2 | +20 / −20 |  |
| 2026-03-28 | `573851a` | Hinzugefügt | Added: Flex recipe for se7enxweb/exponential-platform-nexus 1.1 — installs Exponential settings (override, ngadminui, legacy_admin siteaccesses) | 6 | +363 / −1 |  |
| 2026-03-28 | `be0008d` | Entfernt | remove: nexus 1.1 recipe — replaced by src/install/ + bin/create_install_symlinks.php approach | 6 | +0 / −363 |  |

## 2026-04 (65 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-04 | `5f04b8c` | Hinzugefügt | Added: Adding support for 4.6.x branch of exponential platform dxp composer installation process. Enhancement. | 64 | +2977 / −1 |  |
| 2026-04-04 | `8018f27` | Aktualisiert | Fix webpack build for Dart Sass 1.x and missing ibexa modules (4.6.x-dev) | 2 | +78 / −1 |  |
| 2026-04-04 | `1f62a54` | Hinzugefügt | Add public/favicon.ico and favicon.png to recipe; wire public/ in manifest | 3 | +1 / −0 |  |
| 2026-04-04 | `f25d8ec` | Hinzugefügt | Add public/.htaccess to recipe | 1 | +98 / −0 |  |
| 2026-04-04 | `3dd03f7` | Aktualisiert | Update flex manifest: add ibexa.webpack.config.manager.js, public/, bump ref to f25d8ec | 1 | +458 / −437 |  |
| 2026-04-04 | `09ec5be` | Hinzugefügt | Add ibexa webpack build config to webpack.config.js | 2 | +21 / −3 |  |
| 2026-04-04 | `36d8a66` | Aktualisiert | Fixed: REST API 401, missing icons SVG, and JS translation loading | 5 | +17 / −7 |  |
| 2026-04-04 | `07d5133` | Aktualisiert | Fixed: security.yaml — add password hasher for Ibexa\Core\MVC\Symfony\Security\User | 5 | +90 / −117428 |  |
| 2026-04-04 | `73c2dd8` | Hinzugefügt | Add framework.yaml to 4.6.x-dev recipe for legacy bridge session compatibility | 1 | +24 / −0 |  |
| 2026-04-04 | `40a64ad` | Hinzugefügt | Add config/services.yaml: alias ezpublish.config.resolver + legacy_mode params | 1 | +35 / −0 |  |
| 2026-04-04 | `89c40f5` | Hinzugefügt | Add missing netgen bundle route imports | 2 | +8 / −0 |  |
| 2026-04-04 | `905fee7` | Hinzugefügt | Add missing netgen_tags route import | 1 | +2 / −0 |  |
| 2026-04-04 | `b6313b3` | Hinzugefügt | Add legacy_site siteaccess + LegacyInjectedSettingsSubscriber | 5 | +145 / −3 |  |
| 2026-04-04 | `4f7cca8` | Hinzugefügt | Add public/index.php and legacy bridge symlinks (design, share, var) to 4.6.x-dev recipe | 4 | +12 / −0 |  |
| 2026-04-04 | `4845ea5` | Hinzugefügt | Add bin/install-legacy-links, src/ezpublish_legacy/app/ and update manifest [4.6.x-dev] | 28 | +763 / −0 |  |
| 2026-04-05 | `c8ba86e` | Aktualisiert | fix(legacy_admin): resolve siteaccess from request attribute in LegacyInjectedSettingsSubscriber | 1 | +9 / −2 |  |
| 2026-04-05 | `de27241` | Aktualisiert | fix: sync ibexa.yaml, services.yaml, site.ini.append.php to live | 3 | +79 / −1 |  |
| 2026-04-06 | `9a9bc7f` | Aktualisiert | Fix: siteaccess service alias, ActiveExtensions, CSSFileList reset for admin siteaccesses | 3 | +41 / −31 |  |
| 2026-04-06 | `5e6631a` | Hinzugefügt | Add public/extension symlink, index_cluster.php and index_rest.php | 4 | +147 / −3 |  |
| 2026-04-06 | `5e90a9e` | Hinzugefügt | Add src/EventListener: LegacyRestListener and LegacyRequestListener | 3 | +144 / −1 |  |
| 2026-04-06 | `b7376f6` | Hinzugefügt | feat: add LegacySettings/, LegacyRoot/, legacy_site siteaccess, updated install-legacy-links + services.yaml public aliases | 67 | +4026 / −14 |  |
| 2026-04-06 | `9a3db78` | Aktualisiert | fix: move legacy_admin symlinks from recipe to install-legacy-links script | 9 | +38 / −9 |  |
| 2026-04-06 | `9ba5cc9` | Hinzugefügt | feat: add 4.6.x-LB-dev recipe for legacy-bridge variant of exponential-platform-dxp | 168 | +8615 / −4 |  |
| 2026-04-06 | `496e186` | Sonstiges | revert: restore 4.6.x-dev recipe to pre-legacy-bridge state (4845ea5) | 15 | +52 / −202 |  |
| 2026-04-06 | `2b52b9b` | Aktualisiert | fix: legacy_site design.ini - correct CSS/JS filenames (main not main-un) | 2 | +5 / −5 |  |
| 2026-04-06 | `082087e` | Aktualisiert | fix: add missing ngsite to ActiveExtensions in LegacySettings/override/site.ini | 2 | +3 / −2 |  |
| 2026-04-07 | `be9f100` | Aktualisiert | fix: ibexa:build/dev/watch scripts use --config-name ibexa via webpack.config.js | 2 | +5 / −5 |  |
| 2026-04-07 | `1d7fe31` | Hinzugefügt | feat: add exponential-oss installer type to 4.6.x-LB-dev recipe | 2 | +55 / −0 |  |
| 2026-04-10 | `1d1ede7` | Aktualisiert | fix: remove ez_publish_legacy.yaml from 4.6.x-dev recipe; reorder index so LB-dev sorts before 4.6.x-dev | 2 | +2 / −53 |  |
| 2026-04-10 | `1df2662` | Aktualisiert | fix: update 4.6.x-dev manifest ref to commit that removes ez_publish_legacy.yaml | 1 | +2 / −2 |  |
| 2026-04-10 | `7e0e3a1` | Aktualisiert | fix: regenerate 4.6.x-dev manifest JSON to remove ez_publish_legacy.yaml from embedded files | 1 | +2 / −6 |  |
| 2026-04-10 | `a819fcd` | Aktualisiert | fix: remove Legacy Bridge files from 4.6.x-dev recipe (LegacyInjectedSettingsSubscriber, LegacyRequestListener, legacy params in services.yaml) | 3 | +0 / −175 |  |
| 2026-04-10 | `a41c930` | Aktualisiert | fix: regenerate 4.6.x-dev manifest JSON after removing LB files | 1 | +2 / −10 |  |
| 2026-04-10 | `42f4875` | Aktualisiert | fix: remove all remaining Legacy Bridge artefacts from 4.6.x-dev recipe (LegacyRestListener, LegacyRoot, LegacySettings, Exponential trees; src/ App autowire block) | 91 | +0 / −4507 |  |
| 2026-04-10 | `96fbd56` | Aktualisiert | fix: regenerate 4.6.x-dev manifest JSON after full LB purge (75 files removed) | 1 | +2 / −302 |  |
| 2026-04-10 | `f720558` | Aktualisiert | fix: remove netgen_* route files from 4.6.x-dev recipe (bundles not required by skeleton) | 3 | +0 / −10 |  |
| 2026-04-10 | `373dd42` | Aktualisiert | fix: regenerate 4.6.x-dev manifest after removing netgen route files | 1 | +1 / −13 |  |
| 2026-04-12 | `9c526ae` | Hinzugefügt | feat(recipe): add se7enxweb/doctrine-dbal-schema 1.0 recipe | 2 | +18 / −0 |  |
| 2026-04-12 | `86a3384` | Hinzugefügt | feat(recipe): add se7enxweb/ezplatform-solr-search-engine 3.3 recipe | 2 | +50 / −0 | v1.2.0 |
| 2026-04-12 | `b405f6b` | Aktualisiert | fix(dxp-recipe): storage_id → storage_factory_id + add 5.x-LB-dev recipe | 169 | +7886 / −37 | v1.3.0 |
| 2026-04-12 | `20e268f` | Aktualisiert | fix(5.x-LB-dev): Symfony 7 / Ibexa 5 config compatibility | 3 | +816 / −2 | v1.3.1 |
| 2026-04-12 | `bbf2582` | Aktualisiert | fix(4.6.x-LB-dev): rebuild manifest — storage_id → storage_factory_id in framework.yaml | 1 | +7 / −3 | v1.3.2 |
| 2026-04-12 | `67e9da5` | Aktualisiert | fix: add 5.0 recipe version for exponential-platform-dxp | 168 | +8698 / −35 | v1.3.3 |
| 2026-04-12 | `cee81eb` | Aktualisiert | fix(4.6.x-dev): rebuild manifest — storage_id → storage_factory_id | 1 | +2 / −2 | v1.3.4 |
| 2026-04-12 | `a04c16a` | Aktualisiert | fix: remove framework.yaml from 5.0/5.x-LB-dev/4.6.x-LB-dev recipes | 6 | +3 / −87 | v1.3.5 |
| 2026-04-13 | `ed5d737` | Aktualisiert | fix: add dev-5.x-LB exact recipe version for exponential-platform-dxp | 167 | +8635 / −0 | v1.3.6 |
| 2026-04-13 | `7b42583` | Aktualisiert | fix: reorder recipe versions so Flex version_compare selects 5.0 correctly | 1 | +2 / −6 | v1.3.7 |
| 2026-04-13 | `e60a824` | Aktualisiert | fix: update SiteAccess namespace eZ→Ibexa in LegacyInjectedSettingsSubscriber | 3 | +4 / −4 | v1.3.8 |
| 2026-04-13 | `60f237f` | Aktualisiert | fix: routing.yml → routing.yaml for NetgenLayoutsBundle route file | 3 | +4 / −4 | v1.3.9 |
| 2026-04-13 | `b6b3dbf` | Aktualisiert | fix: declare imagemagick.pre/post_parameters as container parameters | 3 | +16 / −2 | v1.3.10 |
| 2026-04-13 | `77cbf14` | Hinzugefügt | Add clean 1.x versioned recipe slots for exponential-platform-dxp | 576 | +29307 / −0 | v1.3.11 |
| 2026-04-14 | `2645ec5` | Aktualisiert | fix(encore): add libsConfigs for react/reactDOM builds + tsconfig.json + controllers.json to recipes 1.3 + 1.4 | 12 | +58 / −14 | v1.3.12 |
| 2026-04-14 | `0667cc6` | Aktualisiert | fix(install): load Netgen Layouts schema (nglayouts_* tables) during exponential:install exponential-oss | 4 | +94 / −18 | v1.3.13 |
| 2026-04-14 | `33b73d0` | Sonstiges | revert(install): restore ExponentialOssInstaller to simple empty form | 4 | +18 / −94 | v1.3.14 |
| 2026-04-14 | `ccb275e` | Aktualisiert | fix(encore): add richtext webpack config + ibexa-admin-ui alias to recipes 1.3 + 1.4 | 6 | +24 / −8 | v1.3.15 |
| 2026-04-14 | `54c166d` | Sonstiges | docs+fix(1.4): update welcome page translations + add MAINTENANCE.md | 2 | +107 / −14 | v1.3.16 |
| 2026-04-14 | `b83f33e` | Sonstiges | chore: remove stale unregistered recipe slots (5.x-LB-dev, dev-5.x-LB, dev-master) | 392 | +0 / −18196 | v1.3.17 |
| 2026-04-14 | `dbf5d4e` | Sonstiges | chore: update 1.4 flex manifest + remove stale manifest JSONs | 4 | +10 / −2077 | v1.3.18 |
| 2026-04-14 | `160c9c5` | Aktualisiert | fix(1.4): add/update legacy siteaccess INI settings | 25 | +1268 / −45 | v1.3.19 |
| 2026-04-15 | `cefca1f` | Sonstiges | recipe 1.4: sync site.ini.append.php — add translator extension, fix section order | 2 | +28 / −20 | v1.3.20 |
| 2026-04-17 | `e84cda3` | Hinzugefügt | feat(4.6.x-LB-dev): add site siteaccess symlink to install-legacy-links | 2 | +6 / −2 |  |
| 2026-04-17 | `995fd38` | Sonstiges | rebrand: update ibexa_welcome_page.en.xlf translations for Exponential Platform DXP | 4 | +32 / −32 |  |
| 2026-04-17 | `f007c08` | Aktualisiert | fix(1.0): use --config-name ibexa in ibexa:build scripts | 88 | +5039 / −5 | v1.3.21 |
| 2026-04-17 | `690678c` | Aktualisiert | fix(1.3): remove legacy bridge + netgen files from pure v5 recipe | 91 | +9 / −5207 | v1.3.22 |
| 2026-04-17 | `b4dd83a` | Aktualisiert | fix(1.3): strip legacy service definitions from services.yaml + ibexa.yaml | 3 | +3 / −95 | v1.3.23 origin/master |
