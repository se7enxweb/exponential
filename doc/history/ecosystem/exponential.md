# Ecosystem repository: exponential

**Group:** Kernel. **Period in the ledger:** 2023-12-11 to 2026-04-29. **Changes:** 423 (420 made by the se7enxweb team, 3 upstream history carried by the fork).

## What it is

The Exponential 6 legacy kernel (Composer package se7enxweb/exponential). Its composer.json allows PHP 8.1 up to 8.8 (`"php": "^8.1 || ... || ^8.8"`); this installation runs PHP 8.5. Check with `grep '"php"' composer.json`.

## How it relates to Exponential

This is the same history as the main installation repository: every commit listed here carries the same hash in the main chronicle, so the narrative lives in the month pages of the main history. This page only classifies it.

## What a user gets

PHP 8.1 to 8.5 support, an SQLite database driver and installer path, the responsive admin3 design, new template operators, multi-site INI handling and the v6.0.x releases.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/exponential
```

## Where to read more

- [Exponential 6 kernel features: SQLite](../../features/6.0/sqlite-database.md)
- [Responsive admin](../../features/6.0/admin3-responsive-admin.md)
- [PHP 8 support](../../bc/6.0/php8.md)
- [Rebranding](../../features/6.0/rebranding-to-exponential.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 109 |
| Fixes | 80 |
| Behaviour and upgrade changes | 7 |
| Documentation | 64 |
| Tooling | 56 |
| Releases | 42 |
| No user benefit | 65 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2024-01-01 | v6.0.0 | `835ea80c8` | Version state bump to stable for release of 6.0 |
| 2024-01-29 | v6.0.1 | `2b64d890e` | Update composer.json added suggested package xrowmetadata |
| 2024-02-29 | v6.0.2 | `f58c03154` | Increment remote package version url |
| 2024-04-02 | v6.0.3 | `f81246905` | Increment release package version url setting for release |
| 2024-09-05 | v6.0.4 | `47b1a9dca` | Update composer.json remove redis requiring extension from default installation to suggested package to prevent installation errors |
| 2024-10-01 | v6.0.5 | `d1dc811bd` | Update package.ini package url version bump for 6.0.5 release |
| 2024-12-17 | v6.0.6 | `b265140fc` | Version bump for remote package version |
| 2025-02-01 | v6.0.7 | `c079b52d5` | Version Bump for Remote Package Setting for 6.0.7 |
| 2025-05-02 | v6.0.8 | `ee3752216` | Merge pull request #49 from se7enxweb/6.0.8 |
| 2025-06-10 | v6.0.9 | `f588b7321` | Merge remote-tracking branch 'refs/remotes/origin/main' |
| 2025-08-28 | v6.0.10 | `1887ab8a0` | Bugfix for stand alone installation in composer.json setting. |
| 2025-12-30 | v6.0.11 | `27ed271f3` | Update README.md to document PHP 8.5 Support available now. Doc |
| 2026-02-10 | v6.0.12 | `3bbe53dd2` | Expanded ezpKernelWeb class internals which run under platform to specifically not start session for requests to /login route. This is required to pre |
| 2026-04-18 | v6.0.13 | `fdf232812` | Update distribution count in README.md |

## Changes made by the se7enxweb team, by theme

### Documentation (64)

- 2023-12-14 `9abb8c685` docs: Update README.md to focus on current core Exponential Informations
- 2023-12-14 `63dd1bcc0` docs: Update README.md to use https links over http links
- 2023-12-14 `02add8937` docs: Changing license type to match with composer requirements
- 2023-12-23 `000a410f9` docs: Update README.md Removed Outdated Build information
- 2023-12-23 `f9c9d6547` docs: Update README.md Rewrite to reflect changes in ecosystem and state of Exponential
- 2023-12-23 `3c8b3d6a4` docs: Update README.md fixed link format
- 2023-12-23 `21c521abc` docs: Update README.md Added section on PHP version support
- 2023-12-23 `48133a5fb` docs: Update README.md link syntax bugfix
- 2023-12-23 `6eb28d43e` docs: Update README.md made link relative
- 2023-12-24 `035eac80f` docs: Update README.md add note on PHP support available
- 2023-12-25 `caa7906c2` docs: Update README.md Shorten Links
- 2023-12-25 `3f7eae1db` docs: Update README.md shortened link
- 2023-12-25 `3944d4934` docs: Update README.md Added a word on combining with ibexa OSS
- 2023-12-25 `204660113` docs: Update README.md shortened forums link
- 2023-12-26 `0a726a38e` docs: Update README.md added minor refinements
- 2023-12-26 `4427a8b55` docs: Update README.md refine link formatting and added telegram channel link
- 2023-12-26 `a15cb1dd2` docs: Moved version bc doc into bc folder. typo bugfix
- 2023-12-30 `2a37bcbe2` docs: Updated admin and standard design templates to reflect 7x and Share Exponential! Links and Copyright meta data
- 2023-12-30 `7286590ce` docs: Bugfix for documentation links in setup wizard
- 2023-12-30 `ad4a5e6cb` docs: Create SECURITY.md
- 2023-12-30 `bc3318e93` docs: Create CONTRIBUTING.md
- 2024-01-01 `c9e5e3009` docs: Update INSTALL to point to mugo doc for Exponential
- 2024-01-03 `f9b7ba1a0` docs: Update README.md clarified free software license for ibexa oss
- 2024-01-06 `72db74b6e` docs: Update README.md added new product logo image
- 2024-01-07 `79cb7664a` docs: Update README.md update ezplatform website information link to point to internet archive copy of now droped website
- ... and 39 more of this theme; every one is listed in the [ledger](../ledger/exponential.md).

### Composer requirements (46)

- 2023-12-14 `bb9601a94` tooling: Update composer.json to brand repository as a 7x product.
- 2023-12-22 `f2e0cc863` tooling: Update composer.json to require se7enxweb repositories
- 2023-12-23 `91c7d6d92` tooling: Update composer.json changed alias
- 2023-12-23 `5849da74e` tooling: Update composer.json remove unused section
- 2023-12-23 `c39efa3c6` tooling: Update composer.json changing ezgmaplocation package version to cure installation problems
- 2023-12-23 `45e5c69d1` tooling: Update composer.json changed ezstarrating package version
- 2023-12-23 `fb9f17ba4` tooling: Update composer.json changed ezwt package version
- 2023-12-23 `330b0ef30` tooling: Update composer.json replaced package vendor
- 2023-12-24 `ccccf47a5` tooling: Update composer.json update description text
- 2023-12-25 `094c44e17` tooling: Update composer.json package description (wording)
- 2023-12-25 `c460261ca` tooling: Update composer.json updated package description
- 2024-01-01 `7b358c1a1` tooling: Update and rename INSTALL to INSTALL.md, rewrite content to support composer based installation of library requirements
- 2024-01-03 `240c093fa` tooling: Update composer.json clarify ibexa oss is available under a free software license
- 2024-01-07 `d47662a8b` tooling: Update composer.json Added installation of latest release of stable bcgooglesitemaps extension to distrobution
- 2024-01-07 `b64f6b98b` tooling: Update INSTALL.md added link to composer package webpage
- 2024-01-07 `b463db7b3` tooling: Update composer.json added swark to list of installed packages
- 2024-01-07 `ce4c720cf` tooling: Update composer.json added owsimpleoperator package to install list
- 2024-01-07 `6d09cca76` tooling: Update composer.json add support for google analytics by default
- 2024-01-07 `65febffbe` tooling: Update composer.json maintains existing composer configuration
- 2024-01-23 `aee23d5f3` tooling: Update composer.json. Added extension  and updated package name for
- 2024-01-28 `6f408858d` tooling: Update composer.json updated ezodf package name to work without rename composer feature
- 2024-01-28 `9f18aba5f` tooling: Update composer.json updated ezautosave package name and version
- 2024-01-28 `03229663f` tooling: Update composer.json updated ezie package name and version
- 2024-01-28 `f36b68aea` tooling: Update composer.json added ezoracle suggestion package
- 2024-01-28 `1a7d76ebd` tooling: Update composer.json updated ezflow, ezdemo packages names and versions also added ezwebin as first class citizen in Exponential default installation 
- ... and 21 more of this theme; every one is listed in the [ledger](../ledger/exponential.md).

### Version numbers (39)

- 2023-12-23 `20fafbd86` release: Update README.md Changed version number of product offered to match existing data
- 2023-12-23 `db7dca6e5` release: Update version.php switch from version-less development and distribution to planned versions.
- 2023-12-23 `1d5de8bf3` release: Update composer.json changed package version number in description
- 2023-12-25 `7e453d53d` release: Update version.php update to v6.0.0beta1
- 2023-12-31 `f6bf82ab4` release: Bump Version Number to beta2
- 2024-01-01 `835ea80c8` release: Version state bump to stable for release of 6.0
- 2024-01-03 `3e2e373d3` release: Update version.php increase version number
- 2024-01-07 `912d5e9ad` release: Refactor sqlite server version (same as client version number) for setup wizard use
- 2024-01-07 `ca6c99947` release: Update composer.json increased version number to gain much needed bugfixes to end users of the multi upload extension
- 2024-01-25 `6d56f332f` release: Setting release version number in database data
- 2024-01-27 `09b3d533d` release: Increased ezpublish-version in default installation data
- 2024-01-28 `30805fa1c` release: Update composer.json updated ezodf package version number to latest release
- 2024-02-02 `2bd0af5a2` release: Bump version number for next release development
- 2024-02-29 `f58c03154` release: Increment remote package version url
- 2024-03-06 `7cc2b9c61` release: Update version.php increment version for development to begin
- 2024-04-02 `f81246905` release: Increment release package version url setting for release
- 2024-04-04 `f5ad1f879` release: Bump version number forward for next version development to begin
- 2024-08-22 `59d649cc4` release: Bump xrowextract extension to stable version 2.4.2
- 2024-08-22 `de204f8f1` release: Bump xrowextract extension to stable version 2.4.2
- 2024-08-23 `6083bf913` release: Version number bump to 6.0.4 for package system source location for setup wizard
- 2024-09-14 `7c28653d0` release: Updated: Bump version number forward for development to begin
- 2024-10-01 `d1dc811bd` release: Update package.ini package url version bump for 6.0.5 release
- 2024-10-18 `d2d36c2e0` release: Version bump for development to begin on 6.0.6
- 2024-12-03 `9849b43df` release: Updated version number of mysql and sqlite example data to 6.0.6
- 2024-12-17 `b265140fc` release: Version bump for remote package version
- ... and 14 more of this theme; every one is listed in the [ledger](../ledger/exponential.md).

### PHP 8.x compatibility (34)

- 2023-12-24 `06eca7418` fix: Minor patch for php 8 compatablity durring setup wizard
- 2023-12-25 `d6d783723` fix: Update eztemplateforfunction.php bugfix for php 8
- 2023-12-25 `fde5a6900` fix: Create php8.md and added base documentation document structure and links
- 2023-12-25 `3d9406a68` fix: Update eztemplateforfunction.php bugfix for php 8
- 2023-12-25 `99ca162e8` fix: Added documentation outline on PHP 8 Support
- 2023-12-25 `bd03edbbe` fix: PHP 8.3 bugfix
- 2023-12-30 `634f00704` fix: Refactor autoloads code used durring installation to prevent installation error text from displaying. php8 fix
- 2023-12-31 `1558406cb` fix: Refactor autoloads code used durring installation to prevent installation error text from displaying. php8 fix
- 2024-01-03 `30f04b4b1` fix: Refactor autoloads code used durring installation to prevent error text from displaying. php8 fix
- 2024-01-07 `6f7f898de` fix: Minor php8 bugfix. test if input is countable before counting
- 2024-01-07 `378bfc8d5` fix: Minor php8 bugfix. test if input is countable before counting
- 2024-01-09 `8964f26df` fix: Added is_countable before count to prevent error
- 2024-01-27 `1b98ec98b` fix: Added is_countable check before use of count to fix fatal error
- 2024-08-22 `937c31419` fix: PHP8 Bugfix required to prevent warnings in debug output. Dynamic Properties of Persistant Object Class.
- 2024-08-22 `509cf5091` fix: PHP8 Bugfix required to prevent warnings in debug output. Dynamic Properties of Persistant Object Class.
- 2024-12-03 `21d2c6b4f` fix: Minor php8 bugfix
- 2025-01-09 `7a210b0ba` fix: Bugfix for php8 based warning upon use of for loop tpl logic
- 2025-01-09 `eb9079bcb` fix: Bugfix for php8 / 9 compatability of usage of ezstarrating extension with eZ Publlish. This prevents many usage warnings
- 2025-04-06 `3bcf09d88` fix: Removed reference to php84 removed E_STRICT constant preventing usage without error
- 2025-04-15 `86abc1190` fix: Added inital support for php 8.4 into setup wizard, core kernel
- 2025-04-17 `bdbcb503a` fix: Updated Composer PHP Requirements to php8.1 or greater
- 2025-05-08 `0a418638c` fix: README.md; Changed support for PHP to clarify php7 support is deprecated and PHP8.4 is supported. Also remarked invalidation of offers by ez.no for no
- 2025-09-07 `b13f305ac` fix: Added additional checking for type to address php8 specific isssues. From mugo.ca; https://github.com/mugoweb/ezpublish-legacy/pull/235/files Feature 
- 2025-09-07 `7e0a68ee9` fix: Bugfix for php8 support to silence warnings related to missing public property. Bugfix.
- 2025-09-08 `a075afe4d` fix: kernel(api): add #[ReturnTypeWillChange] on SPL interface methods for PHP 8.1 compatibility (no behavior change)
- ... and 9 more of this theme; every one is listed in the [ledger](../ledger/exponential.md).

### SQLite support (30)

- 2024-01-07 `c73eed6cb` feature: Bugfix for missing parameter passing to function. Found testing under sqlite
- 2024-01-07 `6ad5d5708` feature: Refactor sql to support sqlite use case
- 2024-01-07 `2a86e3c35` feature: Refactor sql to support sqlite use case
- 2024-01-07 `d1aa934a6` feature: Refactor sql to support sqlite use case
- 2024-01-07 `80532e07d` feature: Refactor sql to support sqlite use case
- 2024-01-07 `d74e31a30` feature: Refactor sql to support sqlite use case
- 2024-01-07 `a078ae172` feature: Refactor sql to support sqlite use case
- 2024-01-07 `6a2e04a54` feature: Added SQLite database driver and schema into kernel
- 2024-01-07 `5d11b34ef` feature: Added SQLite database driver and schema into settings
- 2024-01-07 `430a12c81` feature: Added sqlite ImplementationAlias to default settings
- 2024-01-07 `7b4703bda` feature: Added sqlite database type support
- 2024-01-07 `04e4080f2` feature: Refactor database choise logic to support sqlite databases by default
- 2024-01-07 `605f1fd02` feature: Added sqlite database type support
- 2024-01-07 `527e9a933` feature: Added expanded database sqlite support among others
- 2024-01-07 `452193edf` feature: Refactor Class Method createTemporaryCopy exception requirements to use looser rules when db is sqlite
- 2024-01-07 `70b815b0d` feature: Added example sqlite database schema, working data and example pre-built ezwebin demo db for study
- 2024-01-07 `30ddf8276` feature: Refactor getting started with sqlite database data and example db
- 2024-01-08 `e4d54e58d` feature: Added SQLite db info template with written descriptions from home page and wikipedia.org as part of the setup process
- 2024-01-08 `3bb983581` feature: Added support for testing for database driver spport for sqlite and displaying info if missing
- 2024-01-09 `e451fcb2e` feature: Added SQLite Database Support. Required By Default eZ Webin Demo Data Site Package Installation Process
- 2024-01-13 `6223672ee` feature: Bugfix for SQLite Database Installer Support
- 2024-01-24 `deed3f5f0` feature: Minor improvements to underlying sqlite driver implementation
- 2024-01-27 `d76ffac25` feature: Refactoring of sqlite default installation implementation to prefer mysql first and db data now avoids quote marks which break sqlite installation pro
- 2024-01-27 `a493e96b0` feature: Refactored sqlite creation of database upon default installation to fix fatal error
- 2024-01-27 `72bd907a7` feature: Minor bugfixes to sqlite implentation, which is not enabled by default. Restoring existing paterns
- ... and 5 more of this theme; every one is listed in the [ledger](../ledger/exponential.md).

### Bug fixes (27)

- 2023-12-25 `4233c84a0` fix: Bugfix for default installation process to allow user downloading our packages to advance beyond the 50% step (site_types). Bad logic conditional
- 2023-12-25 `3378ca617` fix: Update registration.tpl bugfix for typo in link syntax
- 2023-12-25 `a756066ed` fix: Bugfix for default package installation process durring setup wizard steps
- 2024-01-24 `be3140277` fix: Minor description typo bugfix
- 2024-01-25 `b2884debe` fix: Refactor logic for non eng-GB installation support. Fatal error bugfix.
- 2024-01-27 `2e40143ee` fix: Bugfix for existing downloaded  package installation process
- 2024-08-22 `c672ed9e8` fix: Bugfix required by xrowextract content export use of content/browse features. Can not wash an array.
- 2024-08-23 `1a008400a` fix: Bugfix for default menu state and how it is passed succesfully to javascript. Big improvement
- 2024-08-23 `fff6613bf` fix: Bugfix for default menu state and how it is used to set the default display. Big improvement
- 2024-09-30 `b81069a49` fix: Bugfix for fatal error of installation of 6.0.4+ database data.
- 2024-11-24 `9fb63105e` fix: Minor bugfix for popup menu display order priority / zindex to ensure menus are usable
- 2025-01-04 `98e1d61eb` fix: Object Relations Modal Uploadg Initial Displayed Position. Bugfix.
- 2025-01-09 `480643259` fix: Bugfix. Removed unneeded comment
- 2025-09-07 `ff023c8e5` fix: Added code to detect empty string input and return instead of passing null to mysqli_real_escape_string which triggers a deprecated warning. Bugfix.
- 2025-09-07 `569cea280` fix: Added code to detect empty string input and set an empty string instead of passing null to preg_match which triggers a deprecated warning. Bugfix.
- 2025-09-07 `96ee5aa25` fix: Bugfixes for class properties not defined causing warnings. Bugfix.
- 2025-09-07 `dc2440c8d` fix: Bugfix for  not defined when editing content causing a chain of warnings from the kernel/lib. Bugfix.
- 2025-09-13 `11dfa2e17` fix: Bugfix for email notifications which refuse to send using Bcc method, switching to To. Bugfix.
- 2025-09-13 `d806722a8` fix: Bugfix for ezsoap library response class payload internals to comment out a dynamic variable causing warnings. Bugfix.
- 2025-09-13 `1ba06942c` fix: Bugfix for collaboration module view using this class to prevent fatal error regarding parameters previously not being passed when required to be pass
- 2025-09-18 `86e0d4988` fix: Removed admin dashboard logo width restriction as blocking product name on dashboard. Bugfix.
- 2026-01-08 `ccd8b3857` fix: Bugfix for cronjobs/updateviewcount.php to fix uri detection and prevent stats from being stored to just one node. Bugfix.
- 2026-02-10 `3bbe53dd2` fix: Expanded ezpKernelWeb class internals which run under platform to specifically not start session for requests to /login route. This is required to pre
- 2026-02-21 `221caccf3` fix: SEC [SEC-01..06]: Fix SQL injection and OS shell injection — 4 files, 6 attack surfaces closed
- 2026-02-21 `dbb64b169` fix: FIX [UND-01..03, LOG-01..02, NUL-01..13, PRG-01]: Null/undefined guards and logic corrections — 12 files
- ... and 2 more of this theme; every one is listed in the [ledger](../ledger/exponential.md).

### Exponential branding (17)

- 2025-01-16 `96cf44296` feature: Initial addition of new section explaining ibexa dxp oss to ezplatform / Exponential
- 2025-08-13 `837c7b835` feature: Renamed text of Exponential to Exponential in bin, doc, design and share/translations. Rebranding project product.
- 2025-08-13 `0f325632c` feature: Bugfix and further rebranding.
- 2025-08-13 `195d0cef1` feature: Bugfix and further rebranding.
- 2025-08-13 `5d3f294be` feature: Rebranding in settings files and package server url change. Rebranding.
- 2025-08-13 `277531549` feature: Further rebranding of default templates and urls
- 2025-08-15 `e3367a739` feature: Updated logo sprite to use new logo image. Rebranding.
- 2025-08-15 `30e033d7a` feature: Updated logo for admin login. Rebranding.
- 2025-08-15 `c5d87c411` feature: Updated logo image and positioning styles. Updated copyright to match documentation.
- 2025-08-16 `c26d609a3` feature: Replacing product name in setup wizard. Rebranding.
- 2025-08-16 `6bfd74572` feature: Product name update clarification in LICENSE file documentation. Rebranding.
- 2025-08-16 `ac05d8d52` feature: Update README.md updated project banners to refect product name change. Rebranding.
- 2025-08-17 `4e2891e4f` feature: Replaced mugo doc links in templates. Rebranding.
- 2025-12-29 `8febb1e2b` feature: Updated README.md to fix broken support resource links from rebranding. Bugfix
- 2026-01-01 `7a53c70a1` feature: Bugfixes for doc website resource links post rebranding. Doc
- 2026-02-21 `afcfa973e` feature: Rebranding; Within the example .htaccess_root configuration file comments. Rebranding.
- 2026-04-28 `d8fbef61b` feature: Rebranding the standard design logo usage to Exponential logo png. Rebranding.

### Responsive admin design (admin3) (14)

- 2024-08-22 `fd5b0b772` feature: Refactor debug output for responsive design needs
- 2024-08-22 `335262827` feature: Added admin3 design providing responsive administration design based on admin2. Tested and Refined for General Use
- 2024-10-18 `3005038b3` feature: Added updated implementation for actually responsive admin3 design based on existing admin design. well tested.
- 2024-10-18 `da51ca71d` feature: Added updated implementation for actually responsive admin3 design based on existing admin design. well tested
- 2024-11-21 `4a408e672` feature: Minor sidebar layout default position via css bugfix for default admin design pagelayout
- 2024-11-21 `fac956c02` feature: Minor sidebar layout default position via css bugfix for default admin design pagelayout
- 2024-11-21 `e3c7fad09` feature: Minor sidebar layout default position via css bugfix for default admin design pagelayout
- 2024-11-22 `06afed1d7` feature: Refactoring admin3 responsive sidebar implmenetation to be friendly to look at and easier to use on all devices based on feedback and testing
- 2024-11-22 `d719150b2` feature: Added admin3 design template overides to use to redesign the sidebar to become responsive and modern
- 2024-11-23 `50ca7535a` feature: Enable inclusion of responsive javascript for sidebar(s) by default
- 2024-11-24 `9f5ae59c1` feature: Refactor sidebar to fix ios mobile browser scroll down behavior to not hide sidebars via javascript
- 2024-11-26 `306878766` feature: Minor Refactoring of Sidebar Menu z-index usage to ensure all menu items and submenu items are usable
- 2025-01-16 `a87ec544a` feature: Fixed bug with admin where ordering, roles, policies tabs where non-functional by default admin design templates in admin3 design
- 2025-01-16 `4e488afa3` feature: Fixed bug with admin where ordering, roles, policies tabs where non-functional by default admin design templates now fixed

### Extensions bundled with the distribution (13)

- 2024-01-24 `434905886` feature: Increase extension owsimpleoperator version to incluse key extension autoload bugfixes
- 2024-01-24 `c6b90d723` feature: Update ezmodule.php refactor module order to allow it to be overriden by extension
- 2024-03-06 `6be1b30a6` feature: Added enhancedezbinaryfile datatype extension
- 2024-03-06 `ec2cfbb53` feature: Added enhancedselection2 datatype extension
- 2024-03-07 `087b31986` feature: Added birthday datatype extension
- 2024-03-12 `838d1908c` feature: Added jquery-3.7.1 to ezjscore extension
- 2024-03-13 `d32beecb8` feature: Added additional suggested extension package se7enxweb/bcurlaliaswithdash
- 2024-03-22 `3fef68a90` feature: Added xrowmetadata extension
- 2024-03-22 `c03e12648` feature: Set xrowmetadata extension version
- 2024-09-30 `210102f73` feature: Update site.ini added default kernel level autoload setting so installer can install content with this extension datatype by default.
- 2024-10-01 `884f21e96` feature: Update ezmodule.php add fatal error prevention caused by lesser module extensions like ezownerchange. Still usefull to others.
- 2024-10-18 `9a9ef0c71` feature: extension eZ OwnerChange module required changes to module kernel layer
- 2025-01-09 `811ace1fa` feature: Bugfix for warning that file does not exist while unlinking already removed file. This is caused mostly by the xrowmetadata extension usage of the ker

### Other changes to the fork (12)

- 2023-12-30 `ab10c96a7` fix: Refactor Edition Text and Version Alias for 6.0 release
- 2023-12-31 `fcf96edc7` fix: Replaced eZ Systems with 7x as maintainer of Exponential in dashboard/maintenance
- 2024-01-07 `c678ac757` fix: Refactor call to parent constructor
- 2024-01-08 `9246221a9` fix: Refactor written text to read correctly.
- 2024-01-24 `411fbcd8c` fix: Rewrote conditional logic to provided for more flexible sql backend descriptions per each potential use case
- 2024-01-24 `1dbbd95df` fix: Changing file permissions
- 2024-01-24 `474780bd9` fix: Replace deprected ez.no help links
- 2024-01-25 `80766180f` fix: Commented out setting creation which prevents Exponential from using php for session management
- 2024-01-27 `d4201c89e` fix: Refactor database type text name
- 2024-02-27 `6095ea402` fix: Update menu.ini unhide shop admin menu item
- 2026-03-09 `7d3a175fa` fix: Update distribution count and add Nexus version details
- 2026-03-11 `0a36dcd27` fix: Revise issue tracker links and eZ Platform description

### Design and templates (9)

- 2023-12-25 `c25d9a438` feature: Refactor Setup Registration Template to replace ez.no links
- 2023-12-25 `9a8c9c400` feature: Refactor Setup Registration Template to replace ez.no links
- 2024-02-02 `5799206ed` feature: Added new site description field in default site ini settings as required by newer design templates by default
- 2024-04-02 `914d528a8` feature: Minor bugfix for the extension usage of content browse view template that requires this change to prevent fatal
- 2024-12-03 `ae8ca998a` feature: Re-enabled /design menu item for general use under new designed menu space.
- 2024-12-03 `f3a1d31c3` feature: Added default 500 server error template with example user descriptions
- 2024-12-11 `9e83074b6` feature: eZOE / TinyMCE / JQuery eZXML Field Editor Bugfixes for new design implementation
- 2025-08-16 `fbf356e91` feature: Refactoring template internals of page_footer.tpl to support easier hiding of Exponential Powered By Informations. Feature improvement.
- 2026-02-10 `51816a093` feature: RSS Feed Feature bugfix for invalid tpl syntax for template node/view/full. Bugfix from Nexus Project.

### Debug output (8)

- 2024-01-27 `d64e9a7a7` feature: Removed debug statement
- 2024-08-22 `dc3db1139` feature: Bugfix required by xrowextract content export use of content/browse features. Array to string conversion debug warning
- 2025-01-09 `306bb8948` feature: Feature enhancement, added file name to error message to aid in debugging upload issues
- 2025-01-09 `ba1df7cd1` feature: Expanding debug heading information to include what kind of debug is enabled, now undstandable at a quick glance
- 2025-01-09 `0ae174656` feature: Enhancement. Added pgsql library updates to ez implentation to prevent massive debug warnings
- 2025-01-09 `81996fc6d` feature: Feature Enhancement. Added wrapper divs around debugoutput with ids so they can be styled and ordered more by css
- 2025-08-13 `85d8697fd` feature: Finishing work on updated eZ Debug DebugOutput Improvements. Feature addition.
- 2025-09-07 `881a28015` feature: Added enhanced debug log message information. From mugo.ca; https://github.com/mugoweb/ezpublish-legacy/commit/2fd701480cec92e1c372fdd2ca3a11f7ea48a5e

### Test tooling (7)

- 2024-01-07 `d3d3b2e15` tooling: Bugfix for inconsistant testing of data before use
- 2024-01-07 `d374cdd7a` tooling: Bugfix for inconsistant testing of data before use
- 2024-01-07 `40b13ea95` tooling: Bugfix for inconsistant testing of data before use
- 2024-01-07 `b2f295129` tooling: Removed deprecated database support from setup testing to prevent confusion
- 2024-01-27 `23032593a` tooling: Bugfix for database connection test using mysqli
- 2026-02-21 `a7b858ddd` tooling: TEST [PHPUnit-10, SEC-01..06]: Add PHPUnit 10 test infrastructure and security hardening suite — 6 new files
- 2026-03-31 `daea5d555` tooling: Upgraded phpunit test suite support to v13. Upgrade.

### Template operators (6)

- 2026-01-10 `e51905eb2` feature: Feature addition to eZRole Class with the addition of the new hasPolicy method. Great for PHP Developers who need to know the rights of a user from wi
- 2026-01-10 `2d2271b1d` feature: Added new template operators for testing if a user is a member of a role. Inspired by brookinsconsulting/bcmemberofrole owsimpletpl operators. Very fl
- 2026-01-10 `a1e4334b5` feature: Expanded newly added template operators for testing if a user is a has a role policy like content/edit. Flexible and useful. Great for secure template
- 2026-01-11 `24a2c9911` feature: Renamed class name to be simpler to understand it's purpose by everyone. Feature enhancement.
- 2026-01-11 `254297228` feature: Renamed member_of operators to has_ named operators to reduce complexity in simpler usage to understand it's purpose by everyone. Feature enhancement.
- 2026-03-31 `4220e2ff9` feature: Added rstring, ristring and many other php string operators as template operators. New features.

### Package renamed to the se7enxweb vendor (4)

- 2023-12-24 `12ce4a816` bc: Update package.ini to replace ez.no package service URLs and vendor name
- 2023-12-24 `d7ee972f9` bc: Update package description
- 2024-01-03 `9d38333df` bc: Update package.ini replace php8 incompatible package server URLs
- 2026-02-21 `e8509e21d` bc: DOC rename: phpunitvXXXX.md → phpunitv10.md

### Features (4)

- 2024-01-08 `ebf5f8b1b` feature: Added default database name of database to connect to durring setup initial connect
- 2024-03-06 `2ff7a4d5b` feature: Added feature to allow view of root node and use
- 2024-10-18 `893c18670` feature: Added git manager to suggested packages list
- 2025-06-11 `5c3be6397` feature: Refactor package description to mention actuall php support version range

### Cleanup (4)

- 2024-08-28 `e97f035b6` tooling: Update INSTALL.md update links to point to 6.0 docs
- 2024-11-24 `65f2bad9f` tooling: Updated settings include name for admin menubar javascript. Removed comment
- 2025-08-13 `a2d7c828c` tooling: CS
- 2025-08-13 `62b574a80` tooling: Removed unused settings directory. CS

### Database upgrade scripts (2)

- 2023-12-26 `e7d696b36` bc: Create dbupdate-5.4.0-6.0.0.sql
- 2023-12-26 `bd547fd2c` bc: Create dbupdate-5.4-to-6.0.sql

### Dashboard (2)

- 2023-12-31 `0c9d42ba5` fix: Comment out community_activity dashboard block as deprecated and non-functional
- 2024-01-03 `210feca41` fix: Update dashboard.ini disable with comment the community_activity Admin Dashboard Block As Non-Function

### Ready-made web server configuration (2)

- 2025-09-17 `f93362f6b` feature: Added robots.txt default configuration file with nifty ascii art to help new users get results faster.
- 2025-09-18 `185ddce84` feature: To Make the default robots.txt file configuration truely useful we add now disallow block configuration settings to exclude content most commonly want

### Multi-site INI handling (2)

- 2026-01-29 `124186d09` feature: Improve multi-site INI override and cache handling
- 2026-01-29 `fc1a2f0a9` feature: Enhance multi-site INI override logic

### File list manifest (2)

- 2026-04-28 `bd11387b6` tooling: Add generatefilelist.sh and generate share/filelist.md5
- 2026-04-28 `365c121e7` tooling: generatefilelist.sh: add --include=<key> option for custom path inclusion

### Repository housekeeping (1)

- 2024-01-01 `6310558dc` no-user-benefit: Added github language override to try to display PHP as language instead of JavaScript

### Online editor (1)

- 2024-02-29 `a5068500a` feature: Added default support to ezoe link dialog for telephone links

### Package server (1)

- 2025-01-07 `5fb85b97f` bc: Replacing legacy package server URL for new brand domain URL for package server resources. Old url supports transparent redirection to keep the old na

### Sub-items copy subtree (1)

- 2025-01-31 `b492af16a` feature: Feature Addition: Sub items display action menu option copy subtree(s) (to existing node via copy operation). This provides the entire feature at a ke

### PostgreSQL (1)

- 2025-05-08 `cfe35ff47` fix: ezpgsqlschema.php Features Enhancement. Prevent warnings and installation errors when using postgresql database

### Content browse (1)

- 2025-06-18 `5c82f7a45` feature: TPL Override. Feature: Quick select current node durring content/browse

### Cronjobs (1)

- 2025-09-09 `8bffd7b08` fix: Refactor php cronjob part cronjobs/updateviewcount.php internals to support default installations and still improve multi-site installations statistic

### Symfony and platform compatibility (1)

- 2026-02-21 `fc8ec2028` fix: DOC: Document PHP version compatibility — patch set does not raise minimum PHP version

### Curl client (1)

- 2026-03-09 `0b7ccae3b` fix: Patched a fatal flaw in the client calling api for curl requests. Tested as working. Bugfix.

Also: 63 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `b2af30cd2` Bugfix: Incorrect date/time attributes after export within the ezpkg ( |
| 2024-12 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `f433a08cf` PHP 8.0: Preventing Fatal error in PHP 8 for ezmatrix datatype |

## Full record

- Every change with date, kind, size and release tag: [ledger of exponential](../ledger/exponential.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).

<!-- rev2-see-also:start -->
## See also

- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/exponential.md)
- [SQLite installer specification](../../specifications/6.0/platform-sqlite-installer.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- Platform ecosystem by month: [2023-12](months/2023-12.md), [2024-01](months/2024-01.md), [2024-02](months/2024-02.md), [2024-03](months/2024-03.md), [2024-04](months/2024-04.md), [2024-06](months/2024-06.md), [2024-08](months/2024-08.md), [2024-09](months/2024-09.md), [2024-10](months/2024-10.md), [2024-11](months/2024-11.md), [2024-12](months/2024-12.md), [2025-01](months/2025-01.md), [2025-02](months/2025-02.md), [2025-04](months/2025-04.md), [2025-05](months/2025-05.md), [2025-06](months/2025-06.md), [2025-07](months/2025-07.md), [2025-08](months/2025-08.md), [2025-09](months/2025-09.md), [2025-12](months/2025-12.md), [2026-01](months/2026-01.md), [2026-02](months/2026-02.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)

<!-- rev2-see-also:end -->
