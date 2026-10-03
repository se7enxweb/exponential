# Platform package forks, command renames and version rules

Read this page before you update a project built on the Symfony based Exponential Platform (3.x, 4.6, v5), a Nexus
distribution or the legacy bridge from a version that still used the upstream package names, or before you merge a
branch that switches to the se7enxweb packages. The steps are in the order you should do them. Installations of the
legacy kernel alone are affected only by the last step.

Overview: [Ecosystem](../../history/ecosystem.md). Reference: [package map](../../specifications/6.0/platform-package-map.md),
[command names](../../specifications/6.0/platform-console-commands.md).

## In short

| | |
|---|---|
| What changed | About fifty platform packages install from the `se7enxweb` vendor; console commands got the `exponential:` prefix; branding and license wording changed. |
| Who is affected | Platform, Nexus and legacy bridge projects. Legacy kernel installations: only the database update in step 9. |
| How to check | `composer why ibexa/admin-ui` and `composer show \| grep -E "^(ibexa\|ezsystems\|netgen)/"` |
| How to fix | Follow steps 1 to 9 below. Most need no change; the old names keep working. |

## Step 1: packages install from the se7enxweb vendor

**What changed.** Between September 2025 and April 2026 the platform packages were renamed to the vendor
`se7enxweb`: `ibexa/admin-ui` became `se7enxweb/admin-ui`, `ezsystems/ezplatform-kernel` became
`se7enxweb/ezplatform-kernel`, `netgen/site-bundle` became `se7enxweb/site-bundle`, `netgen/layouts-core` became
`se7enxweb/layouts-core`, `twig/twig` became `se7enxweb/twig`, and so on for about fifty packages
([table](../../specifications/6.0/platform-package-map.md)).

**Why your `composer.json` does not break.** Every fork declares the upstream name under `replace` (since 2026-04-12
with the wildcard `*`, not `self.version`, so the fork satisfies any version constraint another package puts on the
upstream name). Composer installs only the fork. Third-party packages that require `ibexa/admin-ui` or
`ezsystems/ezplatform-kernel` keep working.

**How to check.**

```bash
composer why ibexa/admin-ui               # which package provides it; expect se7enxweb/admin-ui
composer why ezsystems/ezplatform-kernel
composer show | grep -E "^(ibexa|ezsystems|netgen)/"   # upstream packages still installed next to forks
```

If an upstream package is still listed, something in your project requires a version the fork does not replace, or
the lock file is older than the fork. Update the dependency tree on a copy of the project first and read the plan
Composer prints.

**How to fix.** Only if your own `composer.json` requires an upstream name directly and you want the project to be
explicit: require the se7enxweb name instead. For Platform v5 the single entry is the metapackage:

```json
{
    "require": {
        "se7enxweb/exponential-platform-dxp": "*"
    }
}
```

**Never install both.** A project must not have the upstream and the forked package installed together. The fork's
classes and the upstream's classes share namespaces, and the autoloader would take one of them at random. The
`site-bundle` fork also declares `netgen/site-bundle:*` as replaced to prevent exactly this (release `3.0.6`,
2026-04-19).

## Step 2: use tags, not branch aliases

On 2026-03-26 branch aliases such as `1.3-se7enx` and `2.3-se7enx` were added to several forks and removed again the
same day, "to prevent auto-resolution by external projects". Do not depend on a dev branch alias. Require a tagged
release, or `dev-main` knowingly.

## Step 3: check PHP and Node versions

| Line | PHP | Node.js (asset builds) |
|---|---|---|
| Platform v5 (`exponential-platform-dxp`, skeleton) | 8.3 or newer; the Nexus v5 starter 8.4 or newer | 20 (DXP skeleton guide), 22 (Nexus v5 starter) |
| Platform 4.6.x skeleton | 7.4 to 8.5 | 20 |
| Platform 3.x forks (`ezplatform-*`) | 7.3 to 8.5 (constraint extended to `^8.5` on 2026-03-25) | per project |
| Nexus 1.x / 2.5 | 7.1 to 8.5 | 20 (the `cjw` Nexus moved from 14 to 20 on 2026-02-12) |

- On PHP 8.4 or 8.5 an older stack needs the [framework forks](../../features/6.0/platform-php85-framework-forks.md).
- On PHP 8.4 the Layouts 2.0 stack needs the [layouts-core fork](../../features/6.0/platform-layouts-core-fork.md).
- For the 1.x line only, Composer skips the platform check with `--ignore-platform-reqs` (the Nexus 1.x install guide
  says so). Do not use that flag on other lines.

## Step 4: console command names

**What changed.** On 2026-04-07 the commands got the prefix `exponential:`. The old names are deprecated aliases and
keep working.

**How to check.** Look for scripts and cron entries that parse command names or `list` output:

```bash
grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d ~/bin ./deploy 2>/dev/null
```

**How to fix.** Nothing, until a future release removes the aliases. New scripts should use the primary names
([list](../../specifications/6.0/platform-console-commands.md)). Two legacy bridge commands changed spelling beyond
the prefix: `install_extensions` is now `install-extensions`, and `assets_install` is now `assets-install`.

## Step 5: branding in installed data and templates

Strings that said "Ibexa DXP", "Ibexa Platform" or "eZ Platform" now say "Exponential Platform DXP" or "Exponential
Platform" in:

- the admin UI (page titles, welcome page, password reset mail, logo, favicons);
- the debug layout of the kernel;
- the SQL seed files (`cleandata.sql`) and the installer text;
- the `legacy_admin` copyright template.

Tests or monitoring that look for the old strings must be updated. Content you created is untouched; only seed data
and shipped templates changed.

## Step 6: license wording

The forks' `composer.json` states "GPL 2.0 or later" (for example `se7enxweb/admin-ui`:
`(GPL-2.0-or-later or proprietary)`; Nexus: "from GPLv2 only to GPLv2 or later", 2025-07-01). If you generate license
reports, expect the new value.

## Step 7: legacy bridge, pick the matching branch

| Your platform | Use |
|---|---|
| eZ Platform 2.5 LTS | `2.1.x` tags (`v2.1.10`, `v2.1.11`) |
| eZ Platform 3.3 / Symfony 5.4 | `3.x` tags (`3.0.0.1` to `v3.0.0.28`) |
| Ibexa 4.6 compatible install | `4.x` tags (`v4.0.0.0` to `v4.0.0.3`) |
| Platform v5 | `5.x` tags (`v5.0.0.0`, `v5.0.1` to `v5.0.9`) |

Changes in the `3.x` branch you may notice in your own code:

- the bundle's `TreeBuilder` uses the Symfony 5 API;
- legacy commands are registered as services with constructor injection (no `ContainerAwareCommand`);
- event class names are the Symfony contracts ones (`RequestEvent`, `ResponseEvent`);
- the config resolver and Twig loader classes have type declarations.

Custom classes that extend bridge classes need the same signatures. See [the legacy bridge guide](../../features/6.0/legacy-bridge.md).

## Step 8: Nexus 1.x, where assets and stored files live

The Nexus 1.x install replaces the legacy directory when Composer updates `se7enxweb/exponential`. Content storage and
the `app` extension therefore live in `src/AppBundle/ezpublish_legacy` and are linked in with the symlinks of the
install guide ([Nexus starter](../../features/6.0/platform-nexus-starter.md#install-nexus-1x)). Never keep uploaded
files only inside `ezpublish_legacy/var`.

## Step 9: legacy kernel, database update from 5.4 to 6.0

For the legacy kernel itself, the 5.4 to 6.0 database update is two statements that record the new version. Back up
the database first, then run the file that matches your database:

```bash
mysql -u <user> -p <database> < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
psql -U <user> -d <database> -f update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql
```

They set `ezsite_data` `ezpublish-version` to `6.0.0` and `ezpublish-release` to `1`. Updates from 6.0.0 to 6.0.15
use `update/database/{mysql,postgresql,sqlite}/6.0/dbupdate-6.0.0-6.0.15.sql`. Further behaviour changes of the
legacy kernel are in the other guides of this directory, starting with [PHP 8 support](php8.md).

## Related pages

- [Package map](../../specifications/6.0/platform-package-map.md) and [command names](../../specifications/6.0/platform-console-commands.md)
- [Legacy bridge](../../features/6.0/legacy-bridge.md) and its [bundle specification](../../specifications/6.0/legacy-bridge-bundle.md)
- [PHP 8.5 framework forks](../../features/6.0/platform-php85-framework-forks.md)
- Features: [Platform administration interface](../../features/6.0/platform-admin-ui-fork.md);
  [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md); [Layouts on the platform](../../features/6.0/platform-layouts-core-fork.md);
  [Nexus starter](../../features/6.0/platform-nexus-starter.md); [Site bundles](../../features/6.0/platform-site-bundles.md);
  [SQLite for Exponential Platform](../../features/6.0/platform-sqlite-install.md);
  [AdminNeo database manager](../../features/6.0/adminneo-database-manager.md)
- [Platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md)
- [Platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [ecosystem overview](../../history/ecosystem.md), a page for every month from 2018-11 in
  [ecosystem months](../../history/ecosystem/months/2026-04.md), and the [change ledger](../../history/ledger/README.md)
