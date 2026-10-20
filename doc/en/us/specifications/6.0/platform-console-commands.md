# Specification: Exponential Platform console command names

This page lists the console command names of the Symfony based Exponential Platform (`bin/console`): the
`exponential:` primary names, the old names that remain as aliases, and the options of the commands you use
most. Read it when you write cron entries, deployment scripts or runbooks for a platform project. It does not
cover the legacy kernel's `./console` and `bin/php/*` scripts; those are described in
[Console](../../bc/6.0/console.md).

Introduced on 2026-04-07 (kernel, legacy bridge) and documented in the skeleton on 2026-04-10.

## The rule

The primary name of every migrated command starts with `exponential:`. The earlier names stay registered as
deprecated aliases, so scripts, cron entries and runbooks that use them keep working. Use the `exponential:`
name in everything new.

| Prefix | Status |
|---|---|
| `exponential:*` | Primary name |
| `ibexa:*` | Deprecated alias, fully functional |
| `ezplatform:*` | Deprecated alias on the 3.x / 4.6 lines; does not exist on Platform v5 |
| `ezpublish:*` | Deprecated alias of the legacy bridge commands |

Commands that were not migrated keep their upstream name, for example `ibexa:cron:run` and
`ibexa:graphql:generate-schema`. To see what your project registers and which options a command takes:

```bash
php bin/console list
php bin/console help <command>
```

## Platform kernel commands

Commit `4b90bbf4d` (2026-04-07) in the 3.x / 4.6 kernel fork `ezplatform-kernel`. Each command gets its new name
with `setName()` and keeps the old one through the deprecated alias mechanism.

On Platform v5 the same names come from the kernel package `se7enxweb/exponential-platform-dxp-core`, the version
the Nexus starter and the DXP skeleton install. That package also adds `exponential:check-urls`,
`exponential:content-type-group:set-system` and the install type `exponential-media`
([Nexus starter](../../features/6.0/platform-nexus-starter.md)). Its repository is not part of the change ledger,
so the two rows marked "v5" are taken from the skeleton guide and the installed package.

| Primary name | Old name | Purpose |
|---|---|---|
| `exponential:install` | `ibexa:install` | Install schema and seed data into an empty database |
| `exponential:reindex` | `ibexa:reindex` | Rebuild the search index |
| `exponential:check-urls` (v5) | `ibexa:check-urls` | Check external links in content |
| `exponential:content:cleanup-versions` | `ibexa:content:cleanup-versions` | Prune old content versions |
| `exponential:content:remove-duplicate-fields` | `ibexa:content:remove-duplicate-fields` | Remove duplicate content fields |
| `exponential:content-type-group:set-system` (v5) | `ibexa:content-type-group:set-system` | Mark a content type group as system |
| `exponential:copy-subtree` | `ibexa:copy-subtree` | Copy a location subtree |
| `exponential:delete-content-translation` | `ibexa:delete-content-translation` | Delete one translation of a content item |
| `exponential:urls:regenerate-aliases` | `ibexa:urls:regenerate-aliases` | Rebuild URL aliases |
| `exponential:images:resize-original` | `ibexa:images:resize-original` | Batch-resize stored original images |
| `exponential:images:normalize-paths` | `ibexa:images:normalize-paths` | Fix image field storage paths |
| `exponential:timestamps:to-utc` | `ibexa:timestamps:to-utc` | Convert date and datetime values to UTC |
| `exponential:io:migrate-files` | `ibexa:io:migrate-files` | Move binary files between IO handlers |
| `exponential:user:validate-password-hashes` | `ibexa:user:validate-password-hashes` | Check password hash algorithms |
| `exponential:user:expire-password` | `ibexa:user:expire-password` | Force password expiry |
| `exponential:debug:config-resolver` | `ibexa:debug:config-resolver` | Show siteaccess configuration values |
| `exponential:debug:config` | (short alias of the previous command) | Same as above |

### Options of the commands you use most

Options and defaults as documented in the skeleton guide:

| Command | Option | Default |
|---|---|---|
| `exponential:install` | argument `type` | `exponential-oss` |
| `exponential:install` | `--skip-indexing` | off |
| `exponential:check-urls` | `-c, --iteration-count` | `50` |
| `exponential:check-urls` | `-u, --user` | `admin` (needs content `read` and `versionread`) |
| `exponential:images:resize-original` | `-f, --filter` | required |
| `exponential:images:resize-original` | `-i, --iteration-count` | `25` |
| `exponential:images:resize-original` | `-u, --user` | `admin` |

Examples:

```bash
php bin/console exponential:install exponential-oss                      # type: exponential-oss (default) or ibexa-oss
php bin/console exponential:install exponential-oss --skip-indexing      # no automatic reindex
php bin/console exponential:reindex --iteration-count=100                # batch size
php bin/console exponential:reindex --content-ids=2,34,68                # only these content ids
php bin/console exponential:reindex --content-type=article
php bin/console exponential:reindex --subtree=45
php bin/console exponential:check-urls --iteration-count=100 --user=editor
php bin/console exponential:copy-subtree 42 2 --user=admin
php bin/console exponential:images:resize-original image banner --filter=large --iteration-count=10
```

## Legacy bridge commands

Commit `8cea9e8` (2026-04-07); also `dc19470` in the Platform 4 bridge.

| Primary name | Old name | Purpose |
|---|---|---|
| `exponential:legacy:init` | `ezpublish:legacy:init` | Prepare the platform for legacy use |
| `exponential:legacy:configure` | `ezpublish:configure` | Write platform configuration from a legacy directory |
| `exponential:legacy:install-extensions` | `ezpublish:legacybundles:install_extensions` | Install legacy extensions defined by bundles |
| `exponential:legacy:symlink` | `ezpublish:legacy:symlink` | Install legacy settings and designs from `src` |
| `exponential:legacy:assets-install` | `ezpublish:legacy:assets_install` | Install legacy assets and front controller wrappers |
| `exponential:legacy:script` | `ezpublish:legacy:script` | Run a legacy script inside the bridged kernel |

Two names changed spelling: `install_extensions` became `install-extensions`, and `assets_install` became
`assets-install`. The old underscore spellings are the aliases.

## Migrate your scripts

1. Find old names in cron files and scripts:

   ```bash
   grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d /var/spool/cron ~/bin ./deploy 2>/dev/null
   ```

   Each output line is `file:line:text` with an old name. No output means nothing to change.
2. Replace them with the primary names from the tables above. Nothing breaks if you do not; the aliases stay.
3. If a tool parses `list` or command output, expect the new names in the listing.

## Related pages

- Upgrade notes: [Package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- Specifications: [Platform package map](platform-package-map.md), [Platform SQLite installer](platform-sqlite-installer.md), [Legacy bridge bundle](legacy-bridge-bundle.md)
- Features: [Legacy bridge](../../features/6.0/legacy-bridge.md), [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md), [Nexus starter](../../features/6.0/platform-nexus-starter.md), [Platform administration interface](../../features/6.0/platform-admin-ui-fork.md), [Layouts on the platform](../../features/6.0/platform-layouts-core-fork.md), [PHP 8.5 framework forks](../../features/6.0/platform-php85-framework-forks.md), [Site bundles](../../features/6.0/platform-site-bundles.md), [SQLite for Exponential Platform](../../features/6.0/platform-sqlite-install.md), [AdminNeo database manager](../../features/6.0/adminneo-database-manager.md)
- Changelog: [Platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md); repositories [ezplatform-kernel](../../history/ecosystem/ezplatform-kernel.md), [legacyBridge](../../history/ecosystem/legacyBridge.md), [ibexa-legacy-bridge---7x](../../history/ecosystem/ibexa-legacy-bridge---7x.md), [exponential-platform-dxp-skeleton](../../history/ecosystem/exponential-platform-dxp-skeleton.md)
