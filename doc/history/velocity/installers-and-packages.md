# Installer and package repositories: history

*Part of the [Exponential Velocity history](README.md). This page covers the two small repositories that carry the legacy installers: `ezwebin-ezpackage` (the site packages the setup wizard installs) and `exponential-legacy-installer` (the Composer installer that puts the legacy kernel and its extensions in place). Release notes of both are also in [ezwebin-ezpackage](../../changelogs/extensions/ezwebin-ezpackage.md) and [exponential-legacy-installer](../../changelogs/extensions/exponential-legacy-installer.md); the package map is in [platform package map](../../specifications/6.0/platform-package-map.md).*

## In one paragraph

Seventeen changes in two and a half years, and each one matters when it is hit. Between December 2023 and March 2024 the `ezwebin` site packages were made to install through the setup wizard on current PHP, MySQL 8 and SQLite. In July 2026 their templates were converted to HTML5 markup. The installer for the legacy kernel was forked under a new package name in August 2025 and repaired for Composer 2.10 in June 2026.

## ezwebin-ezpackage

The repository holds the packages the setup wizard installs: the site packages `ezwebin_site` and `ezwebin_site_clean`, their demo content, designs (`ezwebin_design_blue`, `ezwebin_design_cleangray`, `ezwebin_design_gray`), banners, classes and the `ezwebin_extension` package.

### December 2023 to January 2024: installing on current systems

| Change | Commit | What it fixes for you |
|---|---|---|
| Fatal error in the installer settings during the setup wizard | `52ee7a6` | The installer computed the next siteaccess port as `setting + 1`; the setting is now cast to an integer first (`(int)$this->setting( 'access_type_value' ) + 1`), which prevents the fatal error the commit names. Applies to `ezwebin_site` and `ezwebin_site_clean`. |
| SQLite support in the package installer | `5ce4b25` | When the database is SQLite, the installer reads an extension's schema from `ezextension/<extension>/sql/sqlite/sqlite.sql`, so the site package's extensions can be installed into a SQLite database (see [SQLite database](../../features/6.0/sqlite-database.md)). A misspelt error message ("shema") was corrected in the same change. |
| `eZArchive` and MySQL 8 | `fc49287` | The archive class uses a PHP 5 style constructor and the extension's SQL runs on MySQL 8. |
| Blog templates on MySQL 8 | `76897e9` | Blog content templates display correctly; Composer configuration updated. |
| Composer package name | `afa7b90`, `0646383` | Vendor and package name switched to the se7enxweb package; the licence field set to a valid value. |
| Funding metadata | `f3a569a`, `d720ca8`, `f4fcdfa` | No user-visible effect. |

### March 2024: updated site packages

`ba5f05a`, `d339474` and `51aebec` update the internals of `ezwebin_site` and `ezwebin_site_clean` (their `package.xml` and `settings/ezwebininstaller.php`, about 100 lines each) for release testing, and fix a bug in the installer script of the clean package. These are the packages the setup wizard reads when you choose a site type.

### July 2026: HTML5 markup in the templates

`98c5c51` removes XHTML self-closing slashes and obsolete `type` attributes from 56 template files (about 2,670 lines changed). The page output changes from `<input ... />` to `<input ...>` and from `<script type="text/javascript">` to `<script>`.

**Check before upgrading a customised site:** a stylesheet or test that matches the exact old markup (for example one that looks for `<script type="text/javascript">`) needs a look; browsers are unaffected. This change is also listed in [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md#installer-templates-html5).

## exponential-legacy-installer

A Composer plugin that installs the legacy kernel and its extensions into the right directories.

| Date | Commit | Release | Change |
|---|---|---|---|
| 2025-08-25 | `6e8cf01` | 2.2.1 | The package is renamed `se7enxweb/exponential-legacy-installer` (it was `netgen/ezpublish-legacy-installer`) and the PHP constraint extended to include 8.2. The fork exists so that changes can be made without waiting for the upstream package. |
| 2026-03-02 | `068ac1c` | | Funding metadata; no user-visible effect. |
| 2026-04-11 | `d80adb2` | 2.2.2 | `composer.json` now declares `replace` for both `ezsystems/ezpublish-legacy-installer` and `se7enxweb/ezpublish-legacy-installer`, so Composer's resolver never installs two legacy installers side by side (the cause of classmap ambiguity warnings in `composer dump-autoload`). |
| 2026-06-19 | `bddac7c` | 2.2.3 | **Composer 2.10 no longer fails in soft updates of the legacy kernel.** With the legacy root set to `.`, Composer 2.10 normalises `.` to an empty string inside `Filesystem::normalizePath()`; the installer then tried to copy files back to a directory with no name. The installer now resolves dot or empty paths to the absolute working directory before calling `copyThenRemove()`. Explicit directories pass through untouched, and older Composer versions work because absolute paths are valid there too. |

If `composer update` fails while it copies the legacy kernel back into a project whose legacy root is `.`, upgrade this package to 2.2.3 or later.

## Every change of the period

Generated from the ledger: 17 changes. "Class" is the classification used for the coverage check (feature, fix, bc, security, performance, docs, tooling, release, no-user-benefit). Merge commits and archive rebuilds are listed so that nothing is hidden; the change they carry is classified at its own row.

| Date | Commit | Class | Change | Release |
|---|---|---|---|---|
| 2023-12-24 | [`f3a569a`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/f3a569a) | no-user-benefit | Create FUNDING.yml |  |
| 2023-12-31 | [`52ee7a6`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/52ee7a6) | fix | Updated: Updated Package Installer Settings PHP to Prevent Fatal Error on Installation via Setup Wizard |  |
| 2024-01-08 | [`5ce4b25`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/5ce4b25) | feature | Updated: Added SQLite Database Support |  |
| 2024-01-27 | [`d720ca8`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/d720ca8) | no-user-benefit | Updated: Updated funding informations |  |
| 2024-01-27 | [`fc49287`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/fc49287) | fix | Updated: Updated eZArchive class to PHP5 Constructor and MySQL 8 Compatability To SQL |  |
| 2024-01-28 | [`afa7b90`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/afa7b90) | tooling | Update composer.json switched vendor name and package name and suggested and required vendors |  |
| 2024-01-28 | [`0646383`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/0646383) | tooling | Update composer.json updated composer license field to valid value |  |
| 2024-01-28 | [`76897e9`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/76897e9) | fix | Updated: Bugfix for Mysql 8 compatability to blog content related template code displays. Updated composer configuration |  |
| 2024-03-05 | [`ba5f05a`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/ba5f05a) | feature | Updated: Updated ezwebin package internals for updated release testing |  |
| 2024-03-05 | [`d339474`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/d339474) | feature | Updated: Updated ezwebin_site_clean package internals for updated release testing |  |
| 2024-03-05 | [`51aebec`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/51aebec) | fix | Updated: Bugfix for installer php for ezwebin clean |  |
| 2026-03-02 | [`f4fcdfa`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/f4fcdfa) | no-user-benefit | chore: add GitHub Sponsors funding metadata |  |
| 2026-07-19 | [`98c5c51`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/98c5c51) | bc | Remove XHTML self-closing slashes and obsolete type attributes from ezwebin templates |  |
| 2025-08-25 | [`6e8cf01`](https://github.com/se7enxweb/exponential-legacy-installer/commit/6e8cf01) | tooling | Update composer.json replaced package vendor and package name. Forking for changes. | 2.2.1 |
| 2026-03-02 | [`068ac1c`](https://github.com/se7enxweb/exponential-legacy-installer/commit/068ac1c) | no-user-benefit | chore: add GitHub Sponsors funding metadata |  |
| 2026-04-11 | [`d80adb2`](https://github.com/se7enxweb/exponential-legacy-installer/commit/d80adb2) | fix | fix: add replace shim for ezsystems counterpart package | 2.2.2 |
| 2026-06-19 | [`bddac7c`](https://github.com/se7enxweb/exponential-legacy-installer/commit/bddac7c) | fix | Fix Composer 2.10 path normalization failure in legacy kernel installer | 2.2.3 |
