# Legacy bridge: run Exponential 6 inside the Symfony platform

This page is for teams that move an Exponential 6 site to the Symfony platform, or that want Twig on the public site
while editors keep the legacy admin. Reference: [Legacy bridge bundle specification](../../specifications/6.0/legacy-bridge-bundle.md).

**Repositories:** `se7enxweb/legacy-bridge` (Composer package, repository `legacyBridge`), `se7enxweb/ibexa-legacy-bridge`
(Platform 4 port), `se7enxweb/site-legacy-bundle`, `se7enxweb/ngsymfonytools`, `se7enxweb/exponential-legacy-installer`.

## What it is

The legacy bridge is a Symfony bundle that loads the Exponential 6 kernel (the system you are reading about) inside a Symfony platform installation. One web application then serves both worlds:

- the **legacy admin and legacy templates** (your `.tpl` files, your extensions, your content classes), through the siteaccess named `legacy_admin`;
- the **new stack** (Twig templates, Symfony routes, REST, GraphQL, the Platform admin interface).

Both read the same database. Content that editors create in the legacy admin shows up on the Twig front end, and the other way around.

## Why you would use it

- You have an Exponential 6 site and want to move to the Symfony platform without freezing editors out. Run both until the migration is done.
- You want Twig, Symfony routing or the Netgen Layouts page builder for the public site, but your editors keep the familiar legacy admin.
- You keep legacy extensions (for example `ezjscore`, `eztags`, `ezwebin`) available in a platform project.

## Which version for which platform

The package has one branch per platform generation. Tags are listed in the [release history](../../history/ecosystem/legacyBridge.md).

| Branch / tags | Platform | Symfony | PHP | Requires |
|---|---|---|---|---|
| `2.1.x` (`v2.1.10`, `v2.1.11`, 2025-08 to 2026-02) | eZ Platform 2.5 LTS | 3.4 | 8.x with fixes | the Exponential legacy kernel |
| `3.x` (`3.0.0.1` to `v3.0.0.28`, 2026-03-25 to 03-27) | eZ Platform 3.3 | 5.4 | `^8.0` | `se7enxweb/exponential ^6.0.12`, `ezsystems/ezplatform-kernel ~1.3` |
| `4.x` (`v4.0.0.0` to `v4.0.0.3`, 2026-04-06 to 04-17) | Ibexa 4.6 compatible installs | 5.4 | `^8.0` | `se7enxweb/exponential dev-main` |
| `5.x` (`v5.0.0.0`, `v5.0.1` to `v5.0.9`) | Platform v5 | 7.4 | `^8.4` | adds `se7enxweb/site-bundle`, `se7enxweb/site-legacy-bundle`, `se7enxweb/ngsymfonytools` |

## Install (Platform 2.5 style projects)

```bash
composer require --update-with-all-dependencies "se7enxweb/legacy-bridge"
```

1. Enable the bundle in `app/AppKernel.php`: add `new eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle( $this ),` at the end of the `$bundles` array. The `$this` argument is required.
2. Prepare the project:

```bash
php bin/console exponential:legacy:init
```

The command prints what to do next: move your legacy files (extensions, settings, optionally designs) into the project, then add the `legacy_admin` siteaccess to the siteaccess `list` and `site_group` it points out. The legacy backoffice needs `legacy_mode: true` for that siteaccess, which the init step writes at the end of your platform configuration.

3. Run `composer symfony-scripts` once or twice so the assets are generated and symlinked.
4. Optional: allow the legacy setup wizard in `app/config/security.yml`:

```yaml
ezpublish_setup:
    pattern: ^/ezsetup
    security: false
```

5. Add the legacy rewrite rules to your Apache virtual host so legacy assets are served as files (storage images, `design`, `share/icons`, extension designs, `packages/styles`, `var/storage/packages`); an Nginx equivalent is in the bridge's `INSTALL.md`.
6. Fix permissions as for any platform project.

Missing legacy extensions that older kernels bundled (`ezfind`, `eztags`, the script monitor, the system info extension) are added to the project's `composer.json` with `composer require`.

### Cache behaviour

By default the bridge also clears the platform's persistence (SPI) cache when the legacy content view cache is cleared, and `bin/console cache:clear` clears it too. Switch each off in the platform configuration:

| File | Key | Default | Effect |
|---|---|---|---|
| platform config (for example `app/config/ezplatform.yml`) | `ez_publish_legacy.clear_all_spi_cache_from_legacy` | `true` | clear SPI cache when the legacy view cache is cleared |
| same | `ez_publish_legacy.clear_all_spi_cache_on_symfony_clear_cache` | `true` | clear SPI cache on `cache:clear` |
| same | `ez_publish_legacy.enabled` | `true` | bridge on or off |
| same | `ez_publish_legacy.root_dir` | none | path of the legacy root; must exist |
| same | `ez_publish_legacy.legacy_aware_routes` | empty list | routes (or route prefixes) allowed while `legacy_mode` is on |
| siteaccess scope | `ez_publish_legacy.<scope>.legacy_mode` | unset | let the legacy kernel handle URL aliases |
| siteaccess scope | `ez_publish_legacy.<scope>.templating.view_layout` | unset | Twig pagelayout used when a content view is rendered in legacy |
| siteaccess scope | `ez_publish_legacy.<scope>.templating.module_layout` | unset | Twig pagelayout for legacy modules (legacy pagelayout otherwise) |

## Console commands

The bridge's six commands were renamed in April 2026. The old names stay as deprecated aliases, so scripts keep working.

| Command | Old name (alias) | Does |
|---|---|---|
| `exponential:legacy:init` | `ezpublish:legacy:init` | prepares the platform installation for legacy use |
| `exponential:legacy:configure` | `ezpublish:configure` | creates platform configuration from an existing legacy directory |
| `exponential:legacy:install-extensions` | `ezpublish:legacybundles:install_extensions` | installs legacy extensions that Symfony bundles define (symlink by default) |
| `exponential:legacy:symlink` | `ezpublish:legacy:symlink` | installs legacy settings and design files from `src` into `ezpublish_legacy/` |
| `exponential:legacy:assets-install` | `ezpublish:legacy:assets_install` | installs legacy assets and front controller wrappers (for example `index_cluster.php`) |
| `exponential:legacy:script` | `ezpublish:legacy:script` | runs a legacy CLI script in the bridged kernel |

Example: run a legacy script with the platform's database settings. The script path is relative to the legacy root; every further option is passed on to the legacy script, and `--legacy-help` shows the script's own help:

```bash
php bin/console exponential:legacy:script bin/php/ezcache.php --legacy-help
php bin/console exponential:legacy:script bin/php/ezcache.php --clear-id=content --siteaccess=legacy_admin
```

## SQLite

Since `v4.0.0.1` (2026-04-07) a platform installation on SQLite can run the legacy kernel too. The bridge maps the Doctrine driver `pdo_sqlite` to the legacy `DatabaseImplementation=sqlite3` setting and injects the database file path into the legacy `site.ini` `[DatabaseSettings] Database`. Before that fix the bridge stopped with "Could not map database driver". See [SQLite for the platform](platform-sqlite-install.md).

## What changed over time

- 2025-08: first Exponential releases (`v2.1.10`): the wrapper install command accepts a quoted web directory when installed through Composer, vendor names switched to se7enxweb.
- 2026-02: admin design `admin3` replaces `admin2` as the default design of the `legacy_admin` siteaccess (`v2.1.11`), see [the responsive admin](admin3-responsive-admin.md).
- 2026-03: branch `3.x`: ported to Symfony 5.4 and PHP 8: new `TreeBuilder` API, Symfony contracts events, `RequestEvent` / `ResponseEvent`, typed config resolver, Twig 3, DI-registered commands with constructor injection (no `ContainerAwareCommand`), permission resolver API of platform 3.
- 2026-04: branch `4.x`: Ibexa 4.6 service aliases, session handling for Symfony 5.3+ (factory based sessions), SQLite, command rename, extra GUI chrome for `legacy_admin` through the `ngsite` extension.

## Limits

- The upstream project stated that the bridge is not supported on eZ Platform 3.x because the schema would diverge from the legacy one. The se7enxweb branches keep the legacy schema in place for the platform kernels listed above; use the branch that matches your platform.
- The ledger of the repository records the checked-out branch (`4.x`); branches `3.x` and `5.x` carry their own tags.

## Related pages

- Platform features: [site bundles and Twig tools](platform-site-bundles.md), [Nexus starter](platform-nexus-starter.md), [SQLite for Exponential Platform](platform-sqlite-install.md), [platform administration interface](platform-admin-ui-fork.md), [DXP skeleton](platform-dxp-skeleton.md), [Layouts on the platform](platform-layouts-core-fork.md), [PHP 8.5 framework forks](platform-php85-framework-forks.md), [AdminNeo database manager](adminneo-database-manager.md)
- Specifications: [legacy bridge bundle](../../specifications/6.0/legacy-bridge-bundle.md), [platform console command names](../../specifications/6.0/platform-console-commands.md), [platform package map](../../specifications/6.0/platform-package-map.md), [platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md)
- Upgrade notes: [package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [legacyBridge](../../history/ecosystem/legacyBridge.md) (49 changes, 34 releases), [ibexa-legacy-bridge---7x](../../history/ecosystem/ibexa-legacy-bridge---7x.md), [site-legacy-bundle](../../history/ecosystem/site-legacy-bundle.md), [exponential-legacy-installer](../../history/ecosystem/exponential-legacy-installer.md), [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md)
