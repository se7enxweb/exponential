# sevenx-recipes (Symfony Flex recipes): release notes

What each release of `sevenx-recipes` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/sevenx-recipes.md); the story is in the [chronicle](../../history/extensions/sevenx-recipes.md).

## v1.3.23 (2026-04-17)

**Updated**

- fix(1.3): strip legacy service definitions from services.yaml + ibexa.yaml ([`b4dd83a`](https://github.com/se7enxweb/sevenx-recipes/commit/b4dd83a))

## v1.3.22 (2026-04-17)

**Updated**

- fix(1.3): remove legacy bridge + netgen files from pure v5 recipe ([`690678c`](https://github.com/se7enxweb/sevenx-recipes/commit/690678c))

## v1.3.21 (2026-04-17)

**Added**

- feat(4.6.x-LB-dev): add site siteaccess symlink to install-legacy-links ([`e84cda3`](https://github.com/se7enxweb/sevenx-recipes/commit/e84cda3))

**Updated**

- rebrand: update ibexa_welcome_page.en.xlf translations for Exponential Platform DXP ([`995fd38`](https://github.com/se7enxweb/sevenx-recipes/commit/995fd38))
- fix(1.0): use --config-name ibexa in ibexa:build scripts ([`f007c08`](https://github.com/se7enxweb/sevenx-recipes/commit/f007c08))

## v1.3.20 (2026-04-15)

**Updated**

- recipe 1.4: sync site.ini.append.php — add translator extension, fix section order ([`cefca1f`](https://github.com/se7enxweb/sevenx-recipes/commit/cefca1f))

## v1.3.19 (2026-04-14)

**Updated**

- fix(1.4): add/update legacy siteaccess INI settings ([`160c9c5`](https://github.com/se7enxweb/sevenx-recipes/commit/160c9c5))

## v1.3.18 (2026-04-14)

**Updated**

- chore: update 1.4 flex manifest + remove stale manifest JSONs ([`dbf5d4e`](https://github.com/se7enxweb/sevenx-recipes/commit/dbf5d4e))

## v1.3.17 (2026-04-14)

**Updated**

- chore: remove stale unregistered recipe slots (5.x-LB-dev, dev-5.x-LB, dev-master) ([`b83f33e`](https://github.com/se7enxweb/sevenx-recipes/commit/b83f33e))

## v1.3.16 (2026-04-14)

**Updated**

- docs+fix(1.4): update welcome page translations + add MAINTENANCE.md ([`54c166d`](https://github.com/se7enxweb/sevenx-recipes/commit/54c166d))

## v1.3.15 (2026-04-14)

**Updated**

- fix(encore): add richtext webpack config + ibexa-admin-ui alias to recipes 1.3 + 1.4 ([`ccb275e`](https://github.com/se7enxweb/sevenx-recipes/commit/ccb275e))

## v1.3.14 (2026-04-14)

**Updated**

- revert(install): restore ExponentialOssInstaller to simple empty form ([`33b73d0`](https://github.com/se7enxweb/sevenx-recipes/commit/33b73d0))

## v1.3.13 (2026-04-14)

**Updated**

- fix(install): load Netgen Layouts schema (nglayouts_* tables) during exponential:install exponential-oss ([`0667cc6`](https://github.com/se7enxweb/sevenx-recipes/commit/0667cc6))

## v1.3.12 (2026-04-14)

**Updated**

- fix(encore): add libsConfigs for react/reactDOM builds + tsconfig.json + controllers.json to recipes 1.3 + 1.4 ([`2645ec5`](https://github.com/se7enxweb/sevenx-recipes/commit/2645ec5))

## v1.3.11 (2026-04-13)

**Added**

- Add clean 1.x versioned recipe slots for exponential-platform-dxp ([`77cbf14`](https://github.com/se7enxweb/sevenx-recipes/commit/77cbf14))

## v1.3.10 (2026-04-13)

**Updated**

- fix: declare imagemagick.pre/post_parameters as container parameters ([`b6b3dbf`](https://github.com/se7enxweb/sevenx-recipes/commit/b6b3dbf))

## v1.3.9 (2026-04-13)

**Updated**

- fix: routing.yml → routing.yaml for NetgenLayoutsBundle route file ([`60f237f`](https://github.com/se7enxweb/sevenx-recipes/commit/60f237f))

## v1.3.8 (2026-04-13)

**Updated**

- fix: update SiteAccess namespace eZ→Ibexa in LegacyInjectedSettingsSubscriber ([`e60a824`](https://github.com/se7enxweb/sevenx-recipes/commit/e60a824))

## v1.3.7 (2026-04-13)

**Updated**

- fix: reorder recipe versions so Flex version_compare selects 5.0 correctly ([`7b42583`](https://github.com/se7enxweb/sevenx-recipes/commit/7b42583))

## v1.3.6 (2026-04-13)

**Updated**

- fix: add dev-5.x-LB exact recipe version for exponential-platform-dxp ([`ed5d737`](https://github.com/se7enxweb/sevenx-recipes/commit/ed5d737))

## v1.3.5 (2026-04-12)

**Updated**

- fix: remove framework.yaml from 5.0/5.x-LB-dev/4.6.x-LB-dev recipes ([`a04c16a`](https://github.com/se7enxweb/sevenx-recipes/commit/a04c16a))

## v1.3.4 (2026-04-12)

**Updated**

- fix(4.6.x-dev): rebuild manifest — storage_id → storage_factory_id ([`cee81eb`](https://github.com/se7enxweb/sevenx-recipes/commit/cee81eb))

## v1.3.3 (2026-04-12)

**Updated**

- fix: add 5.0 recipe version for exponential-platform-dxp ([`67e9da5`](https://github.com/se7enxweb/sevenx-recipes/commit/67e9da5))

## v1.3.2 (2026-04-12)

**Updated**

- fix(4.6.x-LB-dev): rebuild manifest — storage_id → storage_factory_id in framework.yaml ([`bbf2582`](https://github.com/se7enxweb/sevenx-recipes/commit/bbf2582))

## v1.3.1 (2026-04-12)

**Updated**

- fix(5.x-LB-dev): Symfony 7 / Ibexa 5 config compatibility ([`20e268f`](https://github.com/se7enxweb/sevenx-recipes/commit/20e268f))

## v1.3.0 (2026-04-12)

**Updated**

- fix(dxp-recipe): storage_id → storage_factory_id + add 5.x-LB-dev recipe ([`b405f6b`](https://github.com/se7enxweb/sevenx-recipes/commit/b405f6b))

## v1.2.0 (2026-04-12)

**Added**

- Add Flex recipe for se7enxweb/exponential-platform-dxp ([`c60b6b7`](https://github.com/se7enxweb/sevenx-recipes/commit/c60b6b7))
- Added recipes support for forks of netgen packages: site-bundle and tagsbundle. Enhancements. ([`4411912`](https://github.com/se7enxweb/sevenx-recipes/commit/4411912))
- Added recipe files for se7enxweb/exponential-platform-dxp-skeleton package. Bugfix. ([`16a3e6f`](https://github.com/se7enxweb/sevenx-recipes/commit/16a3e6f))
- Added recipe files for se7enxweb/exponential-platform-dxp-skeleton package. Bugfix. ([`0b5b79c`](https://github.com/se7enxweb/sevenx-recipes/commit/0b5b79c))
- Flex recipe for se7enxweb/exponential-platform-nexus 1.1 — installs ezpublish_legacy settings (override, ngadminui, legacy_admin siteaccesses) ([`573851a`](https://github.com/se7enxweb/sevenx-recipes/commit/573851a))
- Adding support for 4.6.x branch of exponential platform dxp composer installation process. Enhancement. ([`5f04b8c`](https://github.com/se7enxweb/sevenx-recipes/commit/5f04b8c))
- Add public/favicon.ico and favicon.png to recipe; wire public/ in manifest ([`1f62a54`](https://github.com/se7enxweb/sevenx-recipes/commit/1f62a54))
- Add public/.htaccess to recipe ([`f25d8ec`](https://github.com/se7enxweb/sevenx-recipes/commit/f25d8ec))
- Add ibexa webpack build config to webpack.config.js ([`09ec5be`](https://github.com/se7enxweb/sevenx-recipes/commit/09ec5be))
- Add framework.yaml to 4.6.x-dev recipe for legacy bridge session compatibility ([`73c2dd8`](https://github.com/se7enxweb/sevenx-recipes/commit/73c2dd8))
- Add config/services.yaml: alias ezpublish.config.resolver + legacy_mode params ([`40a64ad`](https://github.com/se7enxweb/sevenx-recipes/commit/40a64ad))
- Add missing netgen bundle route imports ([`89c40f5`](https://github.com/se7enxweb/sevenx-recipes/commit/89c40f5))
- Add missing netgen_tags route import ([`905fee7`](https://github.com/se7enxweb/sevenx-recipes/commit/905fee7))
- Add legacy_site siteaccess + LegacyInjectedSettingsSubscriber ([`b6313b3`](https://github.com/se7enxweb/sevenx-recipes/commit/b6313b3))
- Add public/index.php and legacy bridge symlinks (design, share, var) to 4.6.x-dev recipe ([`4f7cca8`](https://github.com/se7enxweb/sevenx-recipes/commit/4f7cca8))
- Add bin/install-legacy-links, src/ezpublish_legacy/app/ and update manifest [4.6.x-dev] ([`4845ea5`](https://github.com/se7enxweb/sevenx-recipes/commit/4845ea5))
- Add public/extension symlink, index_cluster.php and index_rest.php ([`5e6631a`](https://github.com/se7enxweb/sevenx-recipes/commit/5e6631a))
- Add src/EventListener: LegacyRestListener and LegacyRequestListener ([`5e90a9e`](https://github.com/se7enxweb/sevenx-recipes/commit/5e90a9e))
- feat: add LegacySettings/, LegacyRoot/, legacy_site siteaccess, updated install-legacy-links + services.yaml public aliases ([`b7376f6`](https://github.com/se7enxweb/sevenx-recipes/commit/b7376f6))
- feat: add 4.6.x-LB-dev recipe for legacy-bridge variant of exponential-platform-dxp ([`9ba5cc9`](https://github.com/se7enxweb/sevenx-recipes/commit/9ba5cc9))
- feat: add exponential-oss installer type to 4.6.x-LB-dev recipe ([`1d7fe31`](https://github.com/se7enxweb/sevenx-recipes/commit/1d7fe31))
- feat(recipe): add se7enxweb/doctrine-dbal-schema 1.0 recipe ([`9c526ae`](https://github.com/se7enxweb/sevenx-recipes/commit/9c526ae))
- feat(recipe): add se7enxweb/ezplatform-solr-search-engine 3.3 recipe ([`86a3384`](https://github.com/se7enxweb/sevenx-recipes/commit/86a3384))

**Updated**

- Fix index.json: add branch, recipe-conflicts, versions, is_contrib keys required by symfony/flex Downloader ([`2ad788f`](https://github.com/se7enxweb/sevenx-recipes/commit/2ad788f))
- Rebranding default installation welcome message usage of product name / vendor / doc links / text translations. Rebranding. ([`f735ef7`](https://github.com/se7enxweb/sevenx-recipes/commit/f735ef7))
- Fix webpack build for Dart Sass 1.x and missing ibexa modules (4.6.x-dev) ([`8018f27`](https://github.com/se7enxweb/sevenx-recipes/commit/8018f27))
- Update flex manifest: add ibexa.webpack.config.manager.js, public/, bump ref to f25d8ec ([`3dd03f7`](https://github.com/se7enxweb/sevenx-recipes/commit/3dd03f7))
- Fixed: REST API 401, missing icons SVG, and JS translation loading ([`36d8a66`](https://github.com/se7enxweb/sevenx-recipes/commit/36d8a66))
- Fixed: security.yaml — add password hasher for Ibexa\Core\MVC\Symfony\Security\User ([`07d5133`](https://github.com/se7enxweb/sevenx-recipes/commit/07d5133))
- fix(legacy_admin): resolve siteaccess from request attribute in LegacyInjectedSettingsSubscriber ([`c8ba86e`](https://github.com/se7enxweb/sevenx-recipes/commit/c8ba86e))
- fix: sync ibexa.yaml, services.yaml, site.ini.append.php to live ([`de27241`](https://github.com/se7enxweb/sevenx-recipes/commit/de27241))
- Fix: siteaccess service alias, ActiveExtensions, CSSFileList reset for admin siteaccesses ([`9a9bc7f`](https://github.com/se7enxweb/sevenx-recipes/commit/9a9bc7f))
- fix: move legacy_admin symlinks from recipe to install-legacy-links script ([`9a3db78`](https://github.com/se7enxweb/sevenx-recipes/commit/9a3db78))
- revert: restore 4.6.x-dev recipe to pre-legacy-bridge state (4845ea5) ([`496e186`](https://github.com/se7enxweb/sevenx-recipes/commit/496e186))
- fix: legacy_site design.ini - correct CSS/JS filenames (main not main-un) ([`2b52b9b`](https://github.com/se7enxweb/sevenx-recipes/commit/2b52b9b))
- fix: add missing ngsite to ActiveExtensions in LegacySettings/override/site.ini ([`082087e`](https://github.com/se7enxweb/sevenx-recipes/commit/082087e))
- fix: ibexa:build/dev/watch scripts use --config-name ibexa via webpack.config.js ([`be9f100`](https://github.com/se7enxweb/sevenx-recipes/commit/be9f100))
- fix: remove ez_publish_legacy.yaml from 4.6.x-dev recipe; reorder index so LB-dev sorts before 4.6.x-dev ([`1d1ede7`](https://github.com/se7enxweb/sevenx-recipes/commit/1d1ede7))
- fix: update 4.6.x-dev manifest ref to commit that removes ez_publish_legacy.yaml ([`1df2662`](https://github.com/se7enxweb/sevenx-recipes/commit/1df2662))
- fix: regenerate 4.6.x-dev manifest JSON to remove ez_publish_legacy.yaml from embedded files ([`7e0e3a1`](https://github.com/se7enxweb/sevenx-recipes/commit/7e0e3a1))
- fix: remove Legacy Bridge files from 4.6.x-dev recipe (LegacyInjectedSettingsSubscriber, LegacyRequestListener, legacy params in services.yaml) ([`a819fcd`](https://github.com/se7enxweb/sevenx-recipes/commit/a819fcd))
- fix: regenerate 4.6.x-dev manifest JSON after removing LB files ([`a41c930`](https://github.com/se7enxweb/sevenx-recipes/commit/a41c930))
- fix: remove all remaining Legacy Bridge artefacts from 4.6.x-dev recipe (LegacyRestListener, LegacyRoot, LegacySettings, ezpublish_legacy trees; src/ App autowire block) ([`42f4875`](https://github.com/se7enxweb/sevenx-recipes/commit/42f4875))
- fix: regenerate 4.6.x-dev manifest JSON after full LB purge (75 files removed) ([`96fbd56`](https://github.com/se7enxweb/sevenx-recipes/commit/96fbd56))
- fix: remove netgen_* route files from 4.6.x-dev recipe (bundles not required by skeleton) ([`f720558`](https://github.com/se7enxweb/sevenx-recipes/commit/f720558))
- fix: regenerate 4.6.x-dev manifest after removing netgen route files ([`373dd42`](https://github.com/se7enxweb/sevenx-recipes/commit/373dd42))

**Removed**

- remove: nexus 1.1 recipe — replaced by src/install/ + bin/create_install_symlinks.php approach ([`be0008d`](https://github.com/se7enxweb/sevenx-recipes/commit/be0008d)) Upgrade note.

**Maintenance, documentation and packaging**

- Replaced Ibexa Product Name in user facing templates with logos / trademarks. Rebranding. ([`966f53f`](https://github.com/se7enxweb/sevenx-recipes/commit/966f53f))

## Related

* [Feature page](../../features/6.0/extensions/sevenx-recipes.md)
* [Chronicle](../../history/extensions/sevenx-recipes.md)
* [Change ledger](../../history/ledger/sevenx-recipes.md)

## See also

* [behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
* months: [2026-03](../../history/extensions/months/2026-03.md), [2026-04](../../history/extensions/months/2026-04.md)
