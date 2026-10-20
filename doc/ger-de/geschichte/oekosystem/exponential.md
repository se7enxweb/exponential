# exponential: Geschichte des Plattform-Repositorys

Die Geschichte von `exponential`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Kernel). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 423 Änderungen von 2023-12-11 bis 2026-04-29: 420 vom se7enxweb-Team und 3 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Der Legacy-Kernel von Exponential 6 (Composer-Paket se7enxweb/exponential). Seine composer.json erlaubt PHP 8.1 bis 8.8 (`"php": "^8.1 || ... || ^8.8"`); diese Installation läuft mit PHP 8.5. Prüfen Sie es mit `grep '"php"' composer.json`.

## Wie es mit Exponential zusammenhängt

Dies ist dieselbe Geschichte wie die des Repositorys der Hauptinstallation: Jeder hier aufgeführte Commit trägt in der Hauptchronik denselben Hash, die Erzählung steht also auf den Monatsseiten der Hauptgeschichte. Diese Seite ordnet sie nur ein.

## Was Anwender davon haben

Unterstützung für PHP 8.1 bis 8.5, ein SQLite-Datenbanktreiber und Installationsweg, das responsive Design admin3, neue Template-Operatoren, INI-Verarbeitung für mehrere Websites und die Releases v6.0.x.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/exponential
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 109 |
| Fehlerbehebungen | 80 |
| Verhaltens- und Upgrade-Änderungen | 7 |
| Dokumentation | 64 |
| Werkzeuge | 56 |
| Releases | 42 |
| Ohne Nutzen für Anwender | 65 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
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

## Änderungen des se7enxweb-Teams, nach Thema

### Dokumentation (64)

- 2023-12-14 `9abb8c685` Dokumentation: Update README.md to focus on current core Exponential Informations
- 2023-12-14 `63dd1bcc0` Dokumentation: Update README.md to use https links over http links
- 2023-12-14 `02add8937` Dokumentation: Changing license type to match with composer requirements
- 2023-12-23 `000a410f9` Dokumentation: Update README.md Removed Outdated Build information
- 2023-12-23 `f9c9d6547` Dokumentation: Update README.md Rewrite to reflect changes in ecosystem and state of Exponential
- 2023-12-23 `3c8b3d6a4` Dokumentation: Update README.md fixed link format
- 2023-12-23 `21c521abc` Dokumentation: Update README.md Added section on PHP version support
- 2023-12-23 `48133a5fb` Dokumentation: Update README.md link syntax bugfix
- 2023-12-23 `6eb28d43e` Dokumentation: Update README.md made link relative
- 2023-12-24 `035eac80f` Dokumentation: Update README.md add note on PHP support available
- 2023-12-25 `caa7906c2` Dokumentation: Update README.md Shorten Links
- 2023-12-25 `3f7eae1db` Dokumentation: Update README.md shortened link
- 2023-12-25 `3944d4934` Dokumentation: Update README.md Added a word on combining with ibexa OSS
- 2023-12-25 `204660113` Dokumentation: Update README.md shortened forums link
- 2023-12-26 `0a726a38e` Dokumentation: Update README.md added minor refinements
- 2023-12-26 `4427a8b55` Dokumentation: Update README.md refine link formatting and added telegram channel link
- 2023-12-26 `a15cb1dd2` Dokumentation: Moved version bc doc into bc folder. typo bugfix
- 2023-12-30 `2a37bcbe2` Dokumentation: Updated admin and standard design templates to reflect 7x and Share Exponential! Links and Copyright meta data
- 2023-12-30 `7286590ce` Dokumentation: Bugfix for documentation links in setup wizard
- 2023-12-30 `ad4a5e6cb` Dokumentation: Create SECURITY.md
- 2023-12-30 `bc3318e93` Dokumentation: Create CONTRIBUTING.md
- 2024-01-01 `c9e5e3009` Dokumentation: Update INSTALL to point to mugo doc for Exponential
- 2024-01-03 `f9b7ba1a0` Dokumentation: Update README.md clarified free software license for ibexa oss
- 2024-01-06 `72db74b6e` Dokumentation: Update README.md added new product logo image
- 2024-01-07 `79cb7664a` Dokumentation: Update README.md update ezplatform website information link to point to internet archive copy of now droped website
- ... und 39 weitere zu diesem Thema; jede steht im [Änderungsverzeichnis](../aenderungsverzeichnis/exponential.md).

### Composer-Abhängigkeiten (46)

- 2023-12-14 `bb9601a94` Werkzeuge: Update composer.json to brand repository as a 7x product.
- 2023-12-22 `f2e0cc863` Werkzeuge: Update composer.json to require se7enxweb repositories
- 2023-12-23 `91c7d6d92` Werkzeuge: Update composer.json changed alias
- 2023-12-23 `5849da74e` Werkzeuge: Update composer.json remove unused section
- 2023-12-23 `c39efa3c6` Werkzeuge: Update composer.json changing ezgmaplocation package version to cure installation problems
- 2023-12-23 `45e5c69d1` Werkzeuge: Update composer.json changed ezstarrating package version
- 2023-12-23 `fb9f17ba4` Werkzeuge: Update composer.json changed ezwt package version
- 2023-12-23 `330b0ef30` Werkzeuge: Update composer.json replaced package vendor
- 2023-12-24 `ccccf47a5` Werkzeuge: Update composer.json update description text
- 2023-12-25 `094c44e17` Werkzeuge: Update composer.json package description (wording)
- 2023-12-25 `c460261ca` Werkzeuge: Update composer.json updated package description
- 2024-01-01 `7b358c1a1` Werkzeuge: Update and rename INSTALL to INSTALL.md, rewrite content to support composer based installation of library requirements
- 2024-01-03 `240c093fa` Werkzeuge: Update composer.json clarify ibexa oss is available under a free software license
- 2024-01-07 `d47662a8b` Werkzeuge: Update composer.json Added installation of latest release of stable bcgooglesitemaps extension to distrobution
- 2024-01-07 `b64f6b98b` Werkzeuge: Update INSTALL.md added link to composer package webpage
- 2024-01-07 `b463db7b3` Werkzeuge: Update composer.json added swark to list of installed packages
- 2024-01-07 `ce4c720cf` Werkzeuge: Update composer.json added owsimpleoperator package to install list
- 2024-01-07 `6d09cca76` Werkzeuge: Update composer.json add support for google analytics by default
- 2024-01-07 `65febffbe` Werkzeuge: Update composer.json maintains existing composer configuration
- 2024-01-23 `aee23d5f3` Werkzeuge: Update composer.json. Added extension  and updated package name for
- 2024-01-28 `6f408858d` Werkzeuge: Update composer.json updated ezodf package name to work without rename composer feature
- 2024-01-28 `9f18aba5f` Werkzeuge: Update composer.json updated ezautosave package name and version
- 2024-01-28 `03229663f` Werkzeuge: Update composer.json updated ezie package name and version
- 2024-01-28 `f36b68aea` Werkzeuge: Update composer.json added ezoracle suggestion package
- 2024-01-28 `1a7d76ebd` Werkzeuge: Update composer.json updated ezflow, ezdemo packages names and versions also added ezwebin as first class citizen in Exponential default installation 
- ... und 21 weitere zu diesem Thema; jede steht im [Änderungsverzeichnis](../aenderungsverzeichnis/exponential.md).

### Versionsnummern (39)

- 2023-12-23 `20fafbd86` Release: Update README.md Changed version number of product offered to match existing data
- 2023-12-23 `db7dca6e5` Release: Update version.php switch from version-less development and distribution to planned versions.
- 2023-12-23 `1d5de8bf3` Release: Update composer.json changed package version number in description
- 2023-12-25 `7e453d53d` Release: Update version.php update to v6.0.0beta1
- 2023-12-31 `f6bf82ab4` Release: Bump Version Number to beta2
- 2024-01-01 `835ea80c8` Release: Version state bump to stable for release of 6.0
- 2024-01-03 `3e2e373d3` Release: Update version.php increase version number
- 2024-01-07 `912d5e9ad` Release: Refactor sqlite server version (same as client version number) for setup wizard use
- 2024-01-07 `ca6c99947` Release: Update composer.json increased version number to gain much needed bugfixes to end users of the multi upload extension
- 2024-01-25 `6d56f332f` Release: Setting release version number in database data
- 2024-01-27 `09b3d533d` Release: Increased ezpublish-version in default installation data
- 2024-01-28 `30805fa1c` Release: Update composer.json updated ezodf package version number to latest release
- 2024-02-02 `2bd0af5a2` Release: Bump version number for next release development
- 2024-02-29 `f58c03154` Release: Increment remote package version url
- 2024-03-06 `7cc2b9c61` Release: Update version.php increment version for development to begin
- 2024-04-02 `f81246905` Release: Increment release package version url setting for release
- 2024-04-04 `f5ad1f879` Release: Bump version number forward for next version development to begin
- 2024-08-22 `59d649cc4` Release: Bump xrowextract extension to stable version 2.4.2
- 2024-08-22 `de204f8f1` Release: Bump xrowextract extension to stable version 2.4.2
- 2024-08-23 `6083bf913` Release: Version number bump to 6.0.4 for package system source location for setup wizard
- 2024-09-14 `7c28653d0` Release: Updated: Bump version number forward for development to begin
- 2024-10-01 `d1dc811bd` Release: Update package.ini package url version bump for 6.0.5 release
- 2024-10-18 `d2d36c2e0` Release: Version bump for development to begin on 6.0.6
- 2024-12-03 `9849b43df` Release: Updated version number of mysql and sqlite example data to 6.0.6
- 2024-12-17 `b265140fc` Release: Version bump for remote package version
- ... und 14 weitere zu diesem Thema; jede steht im [Änderungsverzeichnis](../aenderungsverzeichnis/exponential.md).

### Kompatibilität mit PHP 8.x (34)

- 2023-12-24 `06eca7418` Fehlerbehebung: Minor patch for php 8 compatablity durring setup wizard
- 2023-12-25 `d6d783723` Fehlerbehebung: Update eztemplateforfunction.php bugfix for php 8
- 2023-12-25 `fde5a6900` Fehlerbehebung: Create php8.md and added base documentation document structure and links
- 2023-12-25 `3d9406a68` Fehlerbehebung: Update eztemplateforfunction.php bugfix for php 8
- 2023-12-25 `99ca162e8` Fehlerbehebung: Added documentation outline on PHP 8 Support
- 2023-12-25 `bd03edbbe` Fehlerbehebung: PHP 8.3 bugfix
- 2023-12-30 `634f00704` Fehlerbehebung: Refactor autoloads code used durring installation to prevent installation error text from displaying. php8 fix
- 2023-12-31 `1558406cb` Fehlerbehebung: Refactor autoloads code used durring installation to prevent installation error text from displaying. php8 fix
- 2024-01-03 `30f04b4b1` Fehlerbehebung: Refactor autoloads code used durring installation to prevent error text from displaying. php8 fix
- 2024-01-07 `6f7f898de` Fehlerbehebung: Minor php8 bugfix. test if input is countable before counting
- 2024-01-07 `378bfc8d5` Fehlerbehebung: Minor php8 bugfix. test if input is countable before counting
- 2024-01-09 `8964f26df` Fehlerbehebung: Added is_countable before count to prevent error
- 2024-01-27 `1b98ec98b` Fehlerbehebung: Added is_countable check before use of count to fix fatal error
- 2024-08-22 `937c31419` Fehlerbehebung: PHP8 Bugfix required to prevent warnings in debug output. Dynamic Properties of Persistant Object Class.
- 2024-08-22 `509cf5091` Fehlerbehebung: PHP8 Bugfix required to prevent warnings in debug output. Dynamic Properties of Persistant Object Class.
- 2024-12-03 `21d2c6b4f` Fehlerbehebung: Minor php8 bugfix
- 2025-01-09 `7a210b0ba` Fehlerbehebung: Bugfix for php8 based warning upon use of for loop tpl logic
- 2025-01-09 `eb9079bcb` Fehlerbehebung: Bugfix for php8 / 9 compatability of usage of ezstarrating extension with eZ Publlish. This prevents many usage warnings
- 2025-04-06 `3bcf09d88` Fehlerbehebung: Removed reference to php84 removed E_STRICT constant preventing usage without error
- 2025-04-15 `86abc1190` Fehlerbehebung: Added inital support for php 8.4 into setup wizard, core kernel
- 2025-04-17 `bdbcb503a` Fehlerbehebung: Updated Composer PHP Requirements to php8.1 or greater
- 2025-05-08 `0a418638c` Fehlerbehebung: README.md; Changed support for PHP to clarify php7 support is deprecated and PHP8.4 is supported. Also remarked invalidation of offers by ez.no for no
- 2025-09-07 `b13f305ac` Fehlerbehebung: Added additional checking for type to address php8 specific isssues. From mugo.ca; https://github.com/mugoweb/ezpublish-legacy/pull/235/files Feature 
- 2025-09-07 `7e0a68ee9` Fehlerbehebung: Bugfix for php8 support to silence warnings related to missing public property. Bugfix.
- 2025-09-08 `a075afe4d` Fehlerbehebung: kernel(api): add #[ReturnTypeWillChange] on SPL interface methods for PHP 8.1 compatibility (no behavior change)
- ... und 9 weitere zu diesem Thema; jede steht im [Änderungsverzeichnis](../aenderungsverzeichnis/exponential.md).

### Unterstützung für SQLite (30)

- 2024-01-07 `c73eed6cb` Funktion: Bugfix for missing parameter passing to function. Found testing under sqlite
- 2024-01-07 `6ad5d5708` Funktion: Refactor sql to support sqlite use case
- 2024-01-07 `2a86e3c35` Funktion: Refactor sql to support sqlite use case
- 2024-01-07 `d1aa934a6` Funktion: Refactor sql to support sqlite use case
- 2024-01-07 `80532e07d` Funktion: Refactor sql to support sqlite use case
- 2024-01-07 `d74e31a30` Funktion: Refactor sql to support sqlite use case
- 2024-01-07 `a078ae172` Funktion: Refactor sql to support sqlite use case
- 2024-01-07 `6a2e04a54` Funktion: Added SQLite database driver and schema into kernel
- 2024-01-07 `5d11b34ef` Funktion: Added SQLite database driver and schema into settings
- 2024-01-07 `430a12c81` Funktion: Added sqlite ImplementationAlias to default settings
- 2024-01-07 `7b4703bda` Funktion: Added sqlite database type support
- 2024-01-07 `04e4080f2` Funktion: Refactor database choise logic to support sqlite databases by default
- 2024-01-07 `605f1fd02` Funktion: Added sqlite database type support
- 2024-01-07 `527e9a933` Funktion: Added expanded database sqlite support among others
- 2024-01-07 `452193edf` Funktion: Refactor Class Method createTemporaryCopy exception requirements to use looser rules when db is sqlite
- 2024-01-07 `70b815b0d` Funktion: Added example sqlite database schema, working data and example pre-built ezwebin demo db for study
- 2024-01-07 `30ddf8276` Funktion: Refactor getting started with sqlite database data and example db
- 2024-01-08 `e4d54e58d` Funktion: Added SQLite db info template with written descriptions from home page and wikipedia.org as part of the setup process
- 2024-01-08 `3bb983581` Funktion: Added support for testing for database driver spport for sqlite and displaying info if missing
- 2024-01-09 `e451fcb2e` Funktion: Added SQLite Database Support. Required By Default eZ Webin Demo Data Site Package Installation Process
- 2024-01-13 `6223672ee` Funktion: Bugfix for SQLite Database Installer Support
- 2024-01-24 `deed3f5f0` Funktion: Minor improvements to underlying sqlite driver implementation
- 2024-01-27 `d76ffac25` Funktion: Refactoring of sqlite default installation implementation to prefer mysql first and db data now avoids quote marks which break sqlite installation pro
- 2024-01-27 `a493e96b0` Funktion: Refactored sqlite creation of database upon default installation to fix fatal error
- 2024-01-27 `72bd907a7` Funktion: Minor bugfixes to sqlite implentation, which is not enabled by default. Restoring existing paterns
- ... und 5 weitere zu diesem Thema; jede steht im [Änderungsverzeichnis](../aenderungsverzeichnis/exponential.md).

### Fehlerbehebungen (27)

- 2023-12-25 `4233c84a0` Fehlerbehebung: Bugfix for default installation process to allow user downloading our packages to advance beyond the 50% step (site_types). Bad logic conditional
- 2023-12-25 `3378ca617` Fehlerbehebung: Update registration.tpl bugfix for typo in link syntax
- 2023-12-25 `a756066ed` Fehlerbehebung: Bugfix for default package installation process durring setup wizard steps
- 2024-01-24 `be3140277` Fehlerbehebung: Minor description typo bugfix
- 2024-01-25 `b2884debe` Fehlerbehebung: Refactor logic for non eng-GB installation support. Fatal error bugfix.
- 2024-01-27 `2e40143ee` Fehlerbehebung: Bugfix for existing downloaded  package installation process
- 2024-08-22 `c672ed9e8` Fehlerbehebung: Bugfix required by xrowextract content export use of content/browse features. Can not wash an array.
- 2024-08-23 `1a008400a` Fehlerbehebung: Bugfix for default menu state and how it is passed succesfully to javascript. Big improvement
- 2024-08-23 `fff6613bf` Fehlerbehebung: Bugfix for default menu state and how it is used to set the default display. Big improvement
- 2024-09-30 `b81069a49` Fehlerbehebung: Bugfix for fatal error of installation of 6.0.4+ database data.
- 2024-11-24 `9fb63105e` Fehlerbehebung: Minor bugfix for popup menu display order priority / zindex to ensure menus are usable
- 2025-01-04 `98e1d61eb` Fehlerbehebung: Object Relations Modal Uploadg Initial Displayed Position. Bugfix.
- 2025-01-09 `480643259` Fehlerbehebung: Bugfix. Removed unneeded comment
- 2025-09-07 `ff023c8e5` Fehlerbehebung: Added code to detect empty string input and return instead of passing null to mysqli_real_escape_string which triggers a deprecated warning. Bugfix.
- 2025-09-07 `569cea280` Fehlerbehebung: Added code to detect empty string input and set an empty string instead of passing null to preg_match which triggers a deprecated warning. Bugfix.
- 2025-09-07 `96ee5aa25` Fehlerbehebung: Bugfixes for class properties not defined causing warnings. Bugfix.
- 2025-09-07 `dc2440c8d` Fehlerbehebung: Bugfix for  not defined when editing content causing a chain of warnings from the kernel/lib. Bugfix.
- 2025-09-13 `11dfa2e17` Fehlerbehebung: Bugfix for email notifications which refuse to send using Bcc method, switching to To. Bugfix.
- 2025-09-13 `d806722a8` Fehlerbehebung: Bugfix for ezsoap library response class payload internals to comment out a dynamic variable causing warnings. Bugfix.
- 2025-09-13 `1ba06942c` Fehlerbehebung: Bugfix for collaboration module view using this class to prevent fatal error regarding parameters previously not being passed when required to be pass
- 2025-09-18 `86e0d4988` Fehlerbehebung: Removed admin dashboard logo width restriction as blocking product name on dashboard. Bugfix.
- 2026-01-08 `ccd8b3857` Fehlerbehebung: Bugfix for cronjobs/updateviewcount.php to fix uri detection and prevent stats from being stored to just one node. Bugfix.
- 2026-02-10 `3bbe53dd2` Fehlerbehebung: Expanded ezpKernelWeb class internals which run under platform to specifically not start session for requests to /login route. This is required to pre
- 2026-02-21 `221caccf3` Fehlerbehebung: SEC [SEC-01..06]: Fix SQL injection and OS shell injection — 4 files, 6 attack surfaces closed
- 2026-02-21 `dbb64b169` Fehlerbehebung: FIX [UND-01..03, LOG-01..02, NUL-01..13, PRG-01]: Null/undefined guards and logic corrections — 12 files
- ... und 2 weitere zu diesem Thema; jede steht im [Änderungsverzeichnis](../aenderungsverzeichnis/exponential.md).

### Marke Exponential (17)

- 2025-01-16 `96cf44296` Funktion: Initial addition of new section explaining ibexa dxp oss to ezplatform / Exponential
- 2025-08-13 `837c7b835` Funktion: Renamed text of Exponential to Exponential in bin, doc, design and share/translations. Rebranding project product.
- 2025-08-13 `0f325632c` Funktion: Bugfix and further rebranding.
- 2025-08-13 `195d0cef1` Funktion: Bugfix and further rebranding.
- 2025-08-13 `5d3f294be` Funktion: Rebranding in settings files and package server url change. Rebranding.
- 2025-08-13 `277531549` Funktion: Further rebranding of default templates and urls
- 2025-08-15 `e3367a739` Funktion: Updated logo sprite to use new logo image. Rebranding.
- 2025-08-15 `30e033d7a` Funktion: Updated logo for admin login. Rebranding.
- 2025-08-15 `c5d87c411` Funktion: Updated logo image and positioning styles. Updated copyright to match documentation.
- 2025-08-16 `c26d609a3` Funktion: Replacing product name in setup wizard. Rebranding.
- 2025-08-16 `6bfd74572` Funktion: Product name update clarification in LICENSE file documentation. Rebranding.
- 2025-08-16 `ac05d8d52` Funktion: Update README.md updated project banners to refect product name change. Rebranding.
- 2025-08-17 `4e2891e4f` Funktion: Replaced mugo doc links in templates. Rebranding.
- 2025-12-29 `8febb1e2b` Funktion: Updated README.md to fix broken support resource links from rebranding. Bugfix
- 2026-01-01 `7a53c70a1` Funktion: Bugfixes for doc website resource links post rebranding. Doc
- 2026-02-21 `afcfa973e` Funktion: Rebranding; Within the example .htaccess_root configuration file comments. Rebranding.
- 2026-04-28 `d8fbef61b` Funktion: Rebranding the standard design logo usage to Exponential logo png. Rebranding.

### Responsives Admin-Design (admin3) (14)

- 2024-08-22 `fd5b0b772` Funktion: Refactor debug output for responsive design needs
- 2024-08-22 `335262827` Funktion: Added admin3 design providing responsive administration design based on admin2. Tested and Refined for General Use
- 2024-10-18 `3005038b3` Funktion: Added updated implementation for actually responsive admin3 design based on existing admin design. well tested.
- 2024-10-18 `da51ca71d` Funktion: Added updated implementation for actually responsive admin3 design based on existing admin design. well tested
- 2024-11-21 `4a408e672` Funktion: Minor sidebar layout default position via css bugfix for default admin design pagelayout
- 2024-11-21 `fac956c02` Funktion: Minor sidebar layout default position via css bugfix for default admin design pagelayout
- 2024-11-21 `e3c7fad09` Funktion: Minor sidebar layout default position via css bugfix for default admin design pagelayout
- 2024-11-22 `06afed1d7` Funktion: Refactoring admin3 responsive sidebar implmenetation to be friendly to look at and easier to use on all devices based on feedback and testing
- 2024-11-22 `d719150b2` Funktion: Added admin3 design template overides to use to redesign the sidebar to become responsive and modern
- 2024-11-23 `50ca7535a` Funktion: Enable inclusion of responsive javascript for sidebar(s) by default
- 2024-11-24 `9f5ae59c1` Funktion: Refactor sidebar to fix ios mobile browser scroll down behavior to not hide sidebars via javascript
- 2024-11-26 `306878766` Funktion: Minor Refactoring of Sidebar Menu z-index usage to ensure all menu items and submenu items are usable
- 2025-01-16 `a87ec544a` Funktion: Fixed bug with admin where ordering, roles, policies tabs where non-functional by default admin design templates in admin3 design
- 2025-01-16 `4e488afa3` Funktion: Fixed bug with admin where ordering, roles, policies tabs where non-functional by default admin design templates now fixed

### Mit der Distribution gelieferte Erweiterungen (13)

- 2024-01-24 `434905886` Funktion: Increase extension owsimpleoperator version to incluse key extension autoload bugfixes
- 2024-01-24 `c6b90d723` Funktion: Update ezmodule.php refactor module order to allow it to be overriden by extension
- 2024-03-06 `6be1b30a6` Funktion: Added enhancedezbinaryfile datatype extension
- 2024-03-06 `ec2cfbb53` Funktion: Added enhancedselection2 datatype extension
- 2024-03-07 `087b31986` Funktion: Added birthday datatype extension
- 2024-03-12 `838d1908c` Funktion: Added jquery-3.7.1 to ezjscore extension
- 2024-03-13 `d32beecb8` Funktion: Added additional suggested extension package se7enxweb/bcurlaliaswithdash
- 2024-03-22 `3fef68a90` Funktion: Added xrowmetadata extension
- 2024-03-22 `c03e12648` Funktion: Set xrowmetadata extension version
- 2024-09-30 `210102f73` Funktion: Update site.ini added default kernel level autoload setting so installer can install content with this extension datatype by default.
- 2024-10-01 `884f21e96` Funktion: Update ezmodule.php add fatal error prevention caused by lesser module extensions like ezownerchange. Still usefull to others.
- 2024-10-18 `9a9ef0c71` Funktion: extension eZ OwnerChange module required changes to module kernel layer
- 2025-01-09 `811ace1fa` Funktion: Bugfix for warning that file does not exist while unlinking already removed file. This is caused mostly by the xrowmetadata extension usage of the ker

### Weitere Änderungen am Fork (12)

- 2023-12-30 `ab10c96a7` Fehlerbehebung: Refactor Edition Text and Version Alias for 6.0 release
- 2023-12-31 `fcf96edc7` Fehlerbehebung: Replaced eZ Systems with 7x as maintainer of Exponential in dashboard/maintenance
- 2024-01-07 `c678ac757` Fehlerbehebung: Refactor call to parent constructor
- 2024-01-08 `9246221a9` Fehlerbehebung: Refactor written text to read correctly.
- 2024-01-24 `411fbcd8c` Fehlerbehebung: Rewrote conditional logic to provided for more flexible sql backend descriptions per each potential use case
- 2024-01-24 `1dbbd95df` Fehlerbehebung: Changing file permissions
- 2024-01-24 `474780bd9` Fehlerbehebung: Replace deprected ez.no help links
- 2024-01-25 `80766180f` Fehlerbehebung: Commented out setting creation which prevents Exponential from using php for session management
- 2024-01-27 `d4201c89e` Fehlerbehebung: Refactor database type text name
- 2024-02-27 `6095ea402` Fehlerbehebung: Update menu.ini unhide shop admin menu item
- 2026-03-09 `7d3a175fa` Fehlerbehebung: Update distribution count and add Nexus version details
- 2026-03-11 `0a36dcd27` Fehlerbehebung: Revise issue tracker links and eZ Platform description

### Design und Templates (9)

- 2023-12-25 `c25d9a438` Funktion: Refactor Setup Registration Template to replace ez.no links
- 2023-12-25 `9a8c9c400` Funktion: Refactor Setup Registration Template to replace ez.no links
- 2024-02-02 `5799206ed` Funktion: Added new site description field in default site ini settings as required by newer design templates by default
- 2024-04-02 `914d528a8` Funktion: Minor bugfix for the extension usage of content browse view template that requires this change to prevent fatal
- 2024-12-03 `ae8ca998a` Funktion: Re-enabled /design menu item for general use under new designed menu space.
- 2024-12-03 `f3a1d31c3` Funktion: Added default 500 server error template with example user descriptions
- 2024-12-11 `9e83074b6` Funktion: eZOE / TinyMCE / JQuery eZXML Field Editor Bugfixes for new design implementation
- 2025-08-16 `fbf356e91` Funktion: Refactoring template internals of page_footer.tpl to support easier hiding of Exponential Powered By Informations. Feature improvement.
- 2026-02-10 `51816a093` Funktion: RSS Feed Feature bugfix for invalid tpl syntax for template node/view/full. Bugfix from Nexus Project.

### Debug-Ausgabe (8)

- 2024-01-27 `d64e9a7a7` Funktion: Removed debug statement
- 2024-08-22 `dc3db1139` Funktion: Bugfix required by xrowextract content export use of content/browse features. Array to string conversion debug warning
- 2025-01-09 `306bb8948` Funktion: Feature enhancement, added file name to error message to aid in debugging upload issues
- 2025-01-09 `ba1df7cd1` Funktion: Expanding debug heading information to include what kind of debug is enabled, now undstandable at a quick glance
- 2025-01-09 `0ae174656` Funktion: Enhancement. Added pgsql library updates to ez implentation to prevent massive debug warnings
- 2025-01-09 `81996fc6d` Funktion: Feature Enhancement. Added wrapper divs around debugoutput with ids so they can be styled and ordered more by css
- 2025-08-13 `85d8697fd` Funktion: Finishing work on updated eZ Debug DebugOutput Improvements. Feature addition.
- 2025-09-07 `881a28015` Funktion: Added enhanced debug log message information. From mugo.ca; https://github.com/mugoweb/ezpublish-legacy/commit/2fd701480cec92e1c372fdd2ca3a11f7ea48a5e

### Test-Werkzeuge (7)

- 2024-01-07 `d3d3b2e15` Werkzeuge: Bugfix for inconsistant testing of data before use
- 2024-01-07 `d374cdd7a` Werkzeuge: Bugfix for inconsistant testing of data before use
- 2024-01-07 `40b13ea95` Werkzeuge: Bugfix for inconsistant testing of data before use
- 2024-01-07 `b2f295129` Werkzeuge: Removed deprecated database support from setup testing to prevent confusion
- 2024-01-27 `23032593a` Werkzeuge: Bugfix for database connection test using mysqli
- 2026-02-21 `a7b858ddd` Werkzeuge: TEST [PHPUnit-10, SEC-01..06]: Add PHPUnit 10 test infrastructure and security hardening suite — 6 new files
- 2026-03-31 `daea5d555` Werkzeuge: Upgraded phpunit test suite support to v13. Upgrade.

### Template-Operatoren (6)

- 2026-01-10 `e51905eb2` Funktion: Feature addition to eZRole Class with the addition of the new hasPolicy method. Great for PHP Developers who need to know the rights of a user from wi
- 2026-01-10 `2d2271b1d` Funktion: Added new template operators for testing if a user is a member of a role. Inspired by brookinsconsulting/bcmemberofrole owsimpletpl operators. Very fl
- 2026-01-10 `a1e4334b5` Funktion: Expanded newly added template operators for testing if a user is a has a role policy like content/edit. Flexible and useful. Great for secure template
- 2026-01-11 `24a2c9911` Funktion: Renamed class name to be simpler to understand it's purpose by everyone. Feature enhancement.
- 2026-01-11 `254297228` Funktion: Renamed member_of operators to has_ named operators to reduce complexity in simpler usage to understand it's purpose by everyone. Feature enhancement.
- 2026-03-31 `4220e2ff9` Funktion: Added rstring, ristring and many other php string operators as template operators. New features.

### Paket auf den Vendor se7enxweb umbenannt (4)

- 2023-12-24 `12ce4a816` BC: Update package.ini to replace ez.no package service URLs and vendor name
- 2023-12-24 `d7ee972f9` BC: Update package description
- 2024-01-03 `9d38333df` BC: Update package.ini replace php8 incompatible package server URLs
- 2026-02-21 `e8509e21d` BC: DOC rename: phpunitvXXXX.md → phpunitv10.md

### Funktionen (4)

- 2024-01-08 `ebf5f8b1b` Funktion: Added default database name of database to connect to durring setup initial connect
- 2024-03-06 `2ff7a4d5b` Funktion: Added feature to allow view of root node and use
- 2024-10-18 `893c18670` Funktion: Added git manager to suggested packages list
- 2025-06-11 `5c3be6397` Funktion: Refactor package description to mention actuall php support version range

### Aufräumen (4)

- 2024-08-28 `e97f035b6` Werkzeuge: Update INSTALL.md update links to point to 6.0 docs
- 2024-11-24 `65f2bad9f` Werkzeuge: Updated settings include name for admin menubar javascript. Removed comment
- 2025-08-13 `a2d7c828c` Werkzeuge: CS
- 2025-08-13 `62b574a80` Werkzeuge: Removed unused settings directory. CS

### Datenbank-Upgrade-Scripts (2)

- 2023-12-26 `e7d696b36` BC: Create dbupdate-5.4.0-6.0.0.sql
- 2023-12-26 `bd547fd2c` BC: Create dbupdate-5.4-to-6.0.sql

### Dashboard (2)

- 2023-12-31 `0c9d42ba5` Fehlerbehebung: Comment out community_activity dashboard block as deprecated and non-functional
- 2024-01-03 `210feca41` Fehlerbehebung: Update dashboard.ini disable with comment the community_activity Admin Dashboard Block As Non-Function

### Fertige Webserver-Konfiguration (2)

- 2025-09-17 `f93362f6b` Funktion: Added robots.txt default configuration file with nifty ascii art to help new users get results faster.
- 2025-09-18 `185ddce84` Funktion: To Make the default robots.txt file configuration truely useful we add now disallow block configuration settings to exclude content most commonly want

### INI-Verarbeitung für mehrere Websites (2)

- 2026-01-29 `124186d09` Funktion: Improve multi-site INI override and cache handling
- 2026-01-29 `fc1a2f0a9` Funktion: Enhance multi-site INI override logic

### Manifest der Dateiliste (2)

- 2026-04-28 `bd11387b6` Werkzeuge: Add generatefilelist.sh and generate share/filelist.md5
- 2026-04-28 `365c121e7` Werkzeuge: generatefilelist.sh: add --include=<key> option for custom path inclusion

### Pflege des Repositorys (1)

- 2024-01-01 `6310558dc` ohne Nutzen für Anwender: Added github language override to try to display PHP as language instead of JavaScript

### Online-Editor (1)

- 2024-02-29 `a5068500a` Funktion: Added default support to ezoe link dialog for telephone links

### Paketserver (1)

- 2025-01-07 `5fb85b97f` BC: Replacing legacy package server URL for new brand domain URL for package server resources. Old url supports transparent redirection to keep the old na

### Teilbaum kopieren bei den Unterelementen (1)

- 2025-01-31 `b492af16a` Funktion: Feature Addition: Sub items display action menu option copy subtree(s) (to existing node via copy operation). This provides the entire feature at a ke

### PostgreSQL (1)

- 2025-05-08 `cfe35ff47` Fehlerbehebung: ezpgsqlschema.php Features Enhancement. Prevent warnings and installation errors when using postgresql database

### Inhalte durchsuchen (1)

- 2025-06-18 `5c82f7a45` Funktion: TPL Override. Feature: Quick select current node durring content/browse

### Cronjobs (1)

- 2025-09-09 `8bffd7b08` Fehlerbehebung: Refactor php cronjob part cronjobs/updateviewcount.php internals to support default installations and still improve multi-site installations statistic

### Kompatibilität mit Symfony und der Plattform (1)

- 2026-02-21 `fc8ec2028` Fehlerbehebung: DOC: Document PHP version compatibility — patch set does not raise minimum PHP version

### Curl-Client (1)

- 2026-03-09 `0b7ccae3b` Fehlerbehebung: Patched a fatal flaw in the client calling api for curl requests. Tested as working. Bugfix.

Außerdem: 63 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `b2af30cd2` Bugfix: Incorrect date/time attributes after export within the ezpkg (e.g. contentobject.xml) |
| 2024-12 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `f433a08cf` PHP 8.0: Preventing Fatal error in PHP 8 for ezmatrix datatype |

## Verwandte Seiten

- [Funktionen des Kernels von Exponential 6: SQLite](../../../eng-us/features/6.0/sqlite-database.md)
- [Responsive Administration](../../../eng-us/features/6.0/admin3-responsive-admin.md)
- [Unterstützung für PHP 8](../../../eng-us/bc/6.0/php8.md)
- [Umbenennung](../../../eng-us/features/6.0/rebranding-to-exponential.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/exponential.md)
- [Spezifikation des SQLite-Installers](../../spezifikationen/6.0/platform-sqlite-installer.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2023-12](monate/2023-12.md), [2024-01](monate/2024-01.md), [2024-02](monate/2024-02.md), [2024-03](monate/2024-03.md), [2024-04](monate/2024-04.md), [2024-06](monate/2024-06.md), [2024-08](monate/2024-08.md), [2024-09](monate/2024-09.md), [2024-10](monate/2024-10.md), [2024-11](monate/2024-11.md), [2024-12](monate/2024-12.md), [2025-01](monate/2025-01.md), [2025-02](monate/2025-02.md), [2025-04](monate/2025-04.md), [2025-05](monate/2025-05.md), [2025-06](monate/2025-06.md), [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-09](monate/2025-09.md), [2025-12](monate/2025-12.md), [2026-01](monate/2026-01.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
