# exponential-platform-legacy: release notes

Read this page before you install or update `exponential-platform-legacy`, or to find out which release brought a change.

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../history/ecosystem/exponential-platform-legacy.md).

## v2.5.0.3 (2026-04-21)

**Added**

- Add project app SCSS/webpack build, brand image, and asset pipeline (`b417ef6`)

## v2.5.0.2 (2026-04-10)

**Added**

- Add SQLite installer support for local dev and testing (`7021d7e`)
- Six new theme directories ported from 3.x to provide the full design chain required for the site siteaccess to render correctly with notices, develope (`9e922cc`)
- README.md and doc/INSTALL.md to document full SQLite and Oracle database support (`2194e96`)

**Updated**

- Rewrite README and INSTALL.md for Exponential Platform Legacy 2.5.0.x (`8e94fb2`)
- Upgrade PHPUnit to ^11.5 and modernize phpunit.xml.dist (`b231844`)
- Set PHPUnit to ^10.5 (max compatible with Symfony 3.4 stack) (`c168757`)
- Version Bump for exponential package requirements to latest release in composer.json. Upgrade, (`31d165d`)

## v2.5.0.1 (2025-09-14)

**Added**

- Added software example .htaccess mod_rewrite configuration to web dir. Feature improvement. (`a17b406`)
- Added software example .htaccess mod_rewrite configuration to doc/apache2 dir. Feature improvement. (`987808a`)
- Added software example robots.txt configuration to web dir. Feature improvement. (`d143c44`)

**Updated**

- Update composer.json minor change to support replacement installer. testing (`544d935`)
- Update composer.json version bump for ezplatform-admin-ui-assets which just got a manual missing files merge bugfix. Testing. (`ae60784`)
- Updated documentation for package. Clarifictaions only. Added md documentation. Moved default readme to doc folder for safe keeping. (`5e5af1c`)
- Updated documentation for package. Clarifictaions only. Replaced text of new README.md. Documentation. (`0627f49`)
- Updated README.md documentation for package. Removed Logo Images. Documentation. (`370b0ed`)
- Updated installation documentation for package. Clarifictaions only. Documentation. (`b3f0465`)
- Updated installation documentation for package. Clarifictaions only. Documentation. (`531199a`)
- Version bump to 1.5.33 to include rebranded logo in ezplatform-admin-ui composer package. Rebranding (`8344b2b`)
- Required changes to boot admin after rebranding and further test results. Bugfix. (`f71c97e`)
- Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix. (`eb660eb`)

## v2.5.0.0 (2025-08-25)

**Updated**

- Update composer.json replaced package vendor name (`1e0297e`)
- Update composer.json increased package php support (`f5538cb`)
- Update composer.json switch package dep vendor for Exponential legacy and version (`6eedef3`)
- Update composer.json change from gplv2 only to gplv2 or later (`5d53cb6`)
- Update composer.json homepage url vendor name change (`c01377f`)
- Update composer.json replaced package dependencies vendor name. Removed older behat bundle as not supported at this time. (`0beb934`)
- Updating composer.json configuration for basis of a php8.2+ possible installation. This change set allows composer to install all the packages success (`333d353`)
- Changes required to install and boot ezplatform 2.5 gpl based site (`61e235b`)
- Minor path bugfix for composer bagsed autoloads workaround patch. Bugfix. (`fef26da`)
- Path to default pagelayout example fix. Bugfix. (`c6bf309`)
- Updated composer.json to fork further required composer packages for ezplatform 2.5 gpl to run with php 8.2+. Bugfix. (`dbceda7`)
- Update config.yml updated configuration paths to support error free installation via composer package post install scripts. Bugfix. (`23adafb`)
- Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes. (`40cf39f`)
- Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes. (`294783f`)
- Update composer.json change package name. Forking for changes. (`528da80`)
- Update composer.json testing pulling latest changes from child package. (`2287268`)
- Update composer.json testing changegs to allow dev-main to exponential 6 install. Testing. (`559dddf`)
- Update composer.json replaced Exponential package name with exponential. Testing. (`7a6abda`)
- Update composer.json testing with release version instead of dev-main. Testing. (`9b35c25`)

## After the last tag

**Added**

- Added .gitattributes file to update GitHub listed language usage to match other repositories in account. Repo Maintinence (`8479bb7`)

## Related pages

- [Package map and upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [package map](../../specifications/6.0/platform-package-map.md)
- [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
