# Ecosystem repository: exponential-platform-legacy

**Group:** Distributions and starters. **Period in the ledger:** 2025-07-01 to 2026-07-08. **Changes:** 46 (46 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

Exponential Platform Legacy 2.5 distribution (platform plus legacy).

## How it relates to Exponential

Long-term-support line that carries the legacy kernel next to the Symfony stack; SQLite and Oracle documented.

## What a user gets

Composer project for the 2.5 LTS line on PHP 8.1 and 8.2.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/exponential-platform-legacy
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 7 |
| Fixes | 6 |
| Documentation | 7 |
| Tooling | 16 |
| Releases | 4 |
| No user benefit | 6 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-08-25 | v2.5.0.0 | `9b35c25` | Update composer.json testing with release version instead of dev-main. Testing. |
| 2025-09-14 | v2.5.0.1 | `eb660eb` | Updated: Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix. |
| 2026-04-10 | v2.5.0.2 | `2194e96` | Updated: README.md and doc/INSTALL.md to document full SQLite and Oracle database support |
| 2026-04-21 | v2.5.0.3 | `b417ef6` | add project app SCSS/webpack build, brand image, and asset pipeline |

## Changes made by the se7enxweb team, by theme

### Composer requirements (16)

- 2025-07-01 `1e0297e` tooling: Update composer.json replaced package vendor name
- 2025-07-01 `f5538cb` tooling: Update composer.json increased package php support
- 2025-07-01 `6eedef3` tooling: Update composer.json switch package dep vendor for Exponential legacy and version
- 2025-07-01 `5d53cb6` tooling: Update composer.json change from gplv2 only to gplv2 or later
- 2025-07-01 `c01377f` tooling: Update composer.json homepage url vendor name change
- 2025-07-01 `0beb934` tooling: Update composer.json replaced package dependencies vendor name. Removed older behat bundle as not supported at this time.
- 2025-08-24 `333d353` tooling: Updated: Updating composer.json configuration for basis of a php8.2+ possible installation. This change set allows composer to install all the package
- 2025-08-24 `fef26da` tooling: Updated: Minor path bugfix for composer bagsed autoloads workaround patch. Bugfix.
- 2025-08-24 `dbceda7` tooling: Updated: Updated composer.json to fork further required composer packages for ezplatform 2.5 gpl to run with php 8.2+. Bugfix.
- 2025-08-24 `23adafb` tooling: Update config.yml updated configuration paths to support error free installation via composer package post install scripts. Bugfix.
- 2025-08-25 `528da80` tooling: Update composer.json change package name. Forking for changes.
- 2025-08-25 `2287268` tooling: Update composer.json testing pulling latest changes from child package.
- 2025-08-25 `559dddf` tooling: Update composer.json testing changegs to allow dev-main to exponential 6 install. Testing.
- 2025-08-25 `7a6abda` tooling: Update composer.json replaced Exponential package name with exponential. Testing.
- 2025-08-25 `9b35c25` release: Update composer.json testing with release version instead of dev-main. Testing.
- 2025-08-25 `544d935` tooling: Update composer.json minor change to support replacement installer. testing

### Documentation (7)

- 2025-08-25 `5e5af1c` docs: Updated: Updated documentation for package. Clarifictaions only. Added md documentation. Moved default readme to doc folder for safe keeping.
- 2025-08-25 `0627f49` docs: Updated: Updated documentation for package. Clarifictaions only. Replaced text of new README.md. Documentation.
- 2025-08-25 `370b0ed` docs: Updated: Updated README.md documentation for package. Removed Logo Images. Documentation.
- 2025-08-25 `b3f0465` docs: Updated: Updated installation documentation for package. Clarifictaions only. Documentation.
- 2025-08-25 `531199a` docs: Updated: Updated installation documentation for package. Clarifictaions only. Documentation.
- 2025-08-29 `987808a` docs: Added: Added software example .htaccess mod_rewrite configuration to doc/apache2 dir. Feature improvement.
- 2026-04-09 `8e94fb2` docs: rewrite README and INSTALL.md for Exponential Platform Legacy 2.5.0.x

### Bug fixes (4)

- 2025-08-24 `c6bf309` fix: Updated: Path to default pagelayout example fix. Bugfix.
- 2025-08-25 `40cf39f` fix: Updated: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes.
- 2025-08-25 `294783f` fix: Updated: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes.
- 2025-09-14 `eb660eb` fix: Updated: Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix.

### Version numbers (3)

- 2025-08-25 `ae60784` release: Update composer.json version bump for ezplatform-admin-ui-assets which just got a manual missing files merge bugfix. Testing.
- 2025-09-14 `8344b2b` release: Updated: Version bump to 1.5.33 to include rebranded logo in ezplatform-admin-ui composer package. Rebranding
- 2026-04-09 `31d165d` release: Updated: Version Bump for exponential package requirements to latest release in composer.json. Upgrade,

### Symfony and platform compatibility (2)

- 2025-08-24 `61e235b` fix: Update: Changes required to install and boot ezplatform 2.5 gpl based site
- 2026-04-09 `c168757` fix: set PHPUnit to ^10.5 (max compatible with Symfony 3.4 stack)

### Ready-made web server configuration (2)

- 2025-08-29 `a17b406` feature: Added: Added software example .htaccess mod_rewrite configuration to web dir. Feature improvement.
- 2025-08-29 `d143c44` feature: Added: Added software example robots.txt configuration to web dir. Feature improvement.

### SQLite support (2)

- 2026-04-09 `7021d7e` feature: add SQLite installer support for local dev and testing
- 2026-04-10 `2194e96` feature: Updated: README.md and doc/INSTALL.md to document full SQLite and Oracle database support

### Exponential branding (1)

- 2025-09-14 `f71c97e` feature: Updated: Required changes to boot admin after rebranding and further test results. Bugfix.

### Test tooling (1)

- 2026-04-09 `b231844` tooling: upgrade PHPUnit to ^11.5 and modernize phpunit.xml.dist

### Design and templates (1)

- 2026-04-10 `9e922cc` feature: Added: Six new theme directories ported from 3.x to provide the full design chain required for the site siteaccess to render correctly with notices, d

### Features (1)

- 2026-04-21 `b417ef6` feature: add project app SCSS/webpack build, brand image, and asset pipeline

### Repository housekeeping (1)

- 2026-07-08 `8479bb7` no-user-benefit: Added: Added .gitattributes file to update GitHub listed language usage to match other repositories in account. Repo Maintinence

Also: 5 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Full record

- Every change with date, kind, size and release tag: [ledger of exponential-platform-legacy](ledger/exponential-platform-legacy.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
