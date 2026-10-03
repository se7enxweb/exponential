# Platform package forks, command renames and version rules

**Applies to:** projects built on the Symfony based Exponential Platform (3.x, 4.6, v5) and the Nexus distributions, and the legacy bridge. Installations of the legacy kernel alone are not affected, except for the short kernel section at the end.
**Overview:** [Ecosystem](../../history/ecosystem.md) · **Reference:** [package map](../../specifications/6.0/platform-package-map.md), [command names](../../specifications/6.0/platform-console-commands.md).

Read this before you update a platform project from a version that still used upstream package names, or before you merge a branch that switches to the se7enxweb packages. The steps are in the order you should do them.

## 1. Packages now install from the se7enxweb vendor

**What changed.** Between September 2025 and April 2026 the platform packages were renamed to the vendor `se7enxweb`: `ibexa/admin-ui` became `se7enxweb/admin-ui`, `ezsystems/ezplatform-kernel` became `se7enxweb/ezplatform-kernel`, `netgen/site-bundle` became `se7enxweb/site-bundle`, `netgen/layouts-core` became `se7enxweb/layouts-core`, `twig/twig` became `se7enxweb/twig`, and so on for about fifty packages ([table](../../specifications/6.0/platform-package-map.md)).

**Why it does not break your `composer.json`.** Every fork declares the upstream name under `replace` (since 2026-04-12 with the wildcard `*`, not `self.version`, so the fork satisfies any version constraint another package puts on the upstream name). Composer installs only the fork. Third-party packages that require `ibexa/admin-ui` or `ezsystems/ezplatform-kernel` keep working.

**What you must check.**

```bash
composer why ibexa/admin-ui               # which package provides it; expect se7enxweb/admin-ui
composer why ezsystems/ezplatform-kernel
composer show | grep -E "^(ibexa|ezsystems|netgen)/"   # upstream packages still installed next to forks
```

If an upstream package is still listed, something in your project requires a version the fork does not replace, or the lock file is older than the fork. Update the dependency tree on a copy of the project first and read the plan Composer prints.

**What you must change.** Only if your own `composer.json` requires an upstream name directly and you want the project to be explicit: require the se7enxweb name instead. For Platform v5 the single entry is the metapackage:

```json
{
    "require": {
        "se7enxweb/exponential-platform-dxp": "*"
    }
}
```

**Never run both.** A project must not have the upstream and the forked package installed together: the fork's classes and the upstream's classes share namespaces, and the autoloader would take one of them at random. The `site-bundle` fork additionally declares `netgen/site-bundle:*` as replaced to stop exactly this (release `3.0.6`, 2026-04-19).

## 2. No branch aliases, use tags

On 2026-03-26 branch aliases such as `1.3-se7enx` and `2.3-se7enx` were added to several forks and removed again the same day "to prevent auto-resolution by external projects". Do not depend on a dev branch alias. Require a tagged release, or `dev-main` knowingly.

## 3. PHP and Node versions

| Line | PHP | Node.js (asset builds) |
|---|---|---|
| Platform v5 (`exponential-platform-dxp`, skeleton) | 8.3 or newer; the Nexus v5 starter 8.4 or newer | 20 (DXP skeleton guide), 22 (Nexus v5 starter) |
| Platform 4.6.x skeleton | 7.4 to 8.5 | 20 |
| Platform 3.x forks (`ezplatform-*`) | 7.3 to 8.5 (constraint extended to `^8.5` on 2026-03-25) | per project |
| Nexus 1.x / 2.5 | 7.1 to 8.5 | 20 (the `cjw` Nexus moved from 14 to 20 on 2026-02-12) |

On PHP 8.4 or 8.5 an older stack needs the [framework forks](../../features/6.0/platform-php85-framework-forks.md); on PHP 8.4 the Layouts 2.0 stack needs the [layouts-core fork](../../features/6.0/platform-layouts-core-fork.md). Composer ignores the platform check for the 1.x line only with `--ignore-platform-reqs` (the Nexus 1.x install guide says so); do not use that flag on other lines.

## 4. Console command names

**What changed.** On 2026-04-07 the commands got the prefix `exponential:`. The old names are deprecated aliases and continue to work.

**What you must check.** Scripts and cron entries that parse command names or `list` output:

```bash
grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d ~/bin ./deploy 2>/dev/null
```

**What you must change.** Nothing, until a future release removes the aliases. New scripts should use the primary names ([list](../../specifications/6.0/platform-console-commands.md)). Two legacy bridge commands changed spelling beyond the prefix: `install_extensions` is now `install-extensions`, `assets_install` is now `assets-install`.

## 5. Branding is visible in installed data and templates

Strings that said "Ibexa DXP", "Ibexa Platform" or "eZ Platform" now say "Exponential Platform DXP" or "Exponential Platform" in: the admin UI (page titles, welcome page, password reset mail, logo, favicons), the debug layout of the kernel, the SQL seed files (`cleandata.sql`), the installer text, and the `legacy_admin` copyright template. Tests or monitoring that look for the old strings must be updated. Content you created is untouched; only seed data and shipped templates changed.

## 6. License wording

`composer.json` of the forks states "GPL 2.0 or later" (for example `se7enxweb/admin-ui`: `(GPL-2.0-or-later or proprietary)`; Nexus: "from GPLv2 only to GPLv2 or later", 2025-07-01). If you generate license reports, expect the new value.

## 7. Legacy bridge: pick the matching branch

| Your platform | Use |
|---|---|
| eZ Platform 2.5 LTS | `2.1.x` tags (`v2.1.10`, `v2.1.11`) |
| eZ Platform 3.3 / Symfony 5.4 | `3.x` tags (`3.0.0.1` to `v3.0.0.28`) |
| Ibexa 4.6 compatible install | `4.x` tags (`v4.0.0.0` to `v4.0.0.3`) |
| Platform v5 | `5.x` tags (`v5.0.0.0`, `v5.0.1` to `v5.0.9`) |

Changes in the `3.x` branch you may notice in your own code: the bundle's `TreeBuilder` uses the Symfony 5 API; legacy commands are registered as services with constructor injection (no `ContainerAwareCommand`); event class names are the Symfony contracts ones (`RequestEvent`, `ResponseEvent`); the config resolver and Twig loader classes have type declarations. Custom classes that extend bridge classes need the same signatures. See [the guide](../../features/6.0/legacy-bridge.md).

## 8. Where Node/Yarn assets and the database file live (Nexus 1.x)

The Nexus 1.x install replaces the legacy directory when Composer updates `se7enxweb/exponential`. Content storage and the `app` extension therefore live in `src/AppBundle/ezpublish_legacy` and are linked in with the symlinks of the install guide ([Nexus starter](../../features/6.0/platform-nexus-starter.md#install-nexus-1x)). Never keep uploaded files only inside `ezpublish_legacy/var`.

## 9. Kernel: upgrading from 5.4 to 6.0 (the legacy kernel)

For the legacy kernel itself the 5.4 to 6.0 database update is two statements that record the new version; run the file that matches your database:

```bash
mysql -u <user> -p <database> < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
psql -U <user> -d <database> -f update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql
```

They set `ezsite_data` `ezpublish-version` to `6.0.0` and `ezpublish-release` to `1`. Updates from 6.0.0 to 6.0.15 use `update/database/{mysql,postgresql,sqlite}/6.0/dbupdate-6.0.0-6.0.15.sql`. Back up the database before running either. Further behaviour changes of the legacy kernel are in the [other guides of this directory](php8.md).

## Related

[Package map](../../specifications/6.0/platform-package-map.md) · [Command names](../../specifications/6.0/platform-console-commands.md) · [Legacy bridge](../../features/6.0/legacy-bridge.md) · [Framework forks](../../features/6.0/platform-php85-framework-forks.md)

## Platform ecosystem pages

- Features: [Platform administration interface](../../features/6.0/platform-admin-ui-fork.md); [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md); [Layouts on the platform](../../features/6.0/platform-layouts-core-fork.md); [Nexus starter](../../features/6.0/platform-nexus-starter.md); [PHP 8.5 framework forks](../../features/6.0/platform-php85-framework-forks.md); [Site bundles](../../features/6.0/platform-site-bundles.md); [SQLite for Exponential Platform](../../features/6.0/platform-sqlite-install.md); [Legacy bridge](../../features/6.0/legacy-bridge.md); [AdminNeo database manager](../../features/6.0/adminneo-database-manager.md).
- Specifications: [Platform console command names](../../specifications/6.0/platform-console-commands.md); [Platform package map](../../specifications/6.0/platform-package-map.md); [Platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md); [Legacy bridge bundle specification](../../specifications/6.0/legacy-bridge-bundle.md).
- Changelog: [Platform changelog](../../changelogs/extensions/exponential-platform.md).
- History: [ecosystem overview](../../history/ecosystem.md), with a page for every month from 2018-11 in [ecosystem months](../../history/ecosystem/months/2026-04.md), and the [change ledger](../../history/ledger/README.md).
