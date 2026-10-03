# Platform console command names

**Applies to:** the Symfony based Exponential Platform (`bin/console`), not to the legacy kernel's `./console` and `bin/php/*` scripts, which are described in [console](../../bc/6.0/console.md).
**Introduced:** 2026-04-07 (kernel, legacy bridge) and 2026-04-10 (skeleton documentation).
**History:** [ezplatform-kernel](../../history/ecosystem/ezplatform-kernel.md), [legacyBridge](../../history/ecosystem/legacyBridge.md), [ibexa-legacy-bridge---7x](../../history/ecosystem/ibexa-legacy-bridge---7x.md), [exponential-platform-dxp-skeleton](../../history/ecosystem/exponential-platform-dxp-skeleton.md).

## Rule

The primary name of every migrated command starts with `exponential:`. The earlier names stay registered as deprecated aliases, so scripts, cron entries and runbooks that use them keep working. Use the `exponential:` name in everything new.

| Prefix | Status |
|---|---|
| `exponential:*` | canonical |
| `ibexa:*` | deprecated alias, fully functional |
| `ezplatform:*` | deprecated alias on the 3.x / 4.6 lines; does not exist on Platform v5 |
| `ezpublish:*` | deprecated alias of the legacy bridge commands |

Commands that were not migrated keep their upstream name, for example `ibexa:cron:run` and `ibexa:graphql:generate-schema`. `php bin/console list` shows what your installation registers; `php bin/console help <command>` shows options.

## Platform kernel commands (commit `4b90bbf4d`, 2026-04-07)

Done in each command with `setName()` plus the deprecated alias mechanism. The commit is in the 3.x / 4.6 kernel fork `ezplatform-kernel`. On Platform v5 the same names are carried by the kernel package `se7enxweb/exponential-platform-dxp-core` (the version installed by the Nexus starter and the DXP skeleton); that package also adds the commands `exponential:check-urls` and `exponential:content-type-group:set-system` and the install type `exponential-media` ([Nexus starter](../../features/6.0/platform-nexus-starter.md)). Its repository is not part of the change ledger this documentation is built from, so the two rows below that are marked "v5" are taken from the skeleton guide and the installed package.

| New primary name | Old name | Purpose |
|---|---|---|
| `exponential:install` | `ibexa:install` | install schema and seed data into an empty database |
| `exponential:reindex` | `ibexa:reindex` | rebuild the search index |
| `exponential:check-urls` (v5) | `ibexa:check-urls` | audit external links in content |
| `exponential:content:cleanup-versions` | `ibexa:content:cleanup-versions` | prune old content versions |
| `exponential:content:remove-duplicate-fields` | `ibexa:content:remove-duplicate-fields` | remove duplicate content fields |
| `exponential:content-type-group:set-system` (v5) | `ibexa:content-type-group:set-system` | mark a content type group as system |
| `exponential:copy-subtree` | `ibexa:copy-subtree` | copy a location subtree |
| `exponential:delete-content-translation` | `ibexa:delete-content-translation` | delete one translation of a content item |
| `exponential:urls:regenerate-aliases` | `ibexa:urls:regenerate-aliases` | rebuild URL aliases |
| `exponential:images:resize-original` | `ibexa:images:resize-original` | batch-resize stored original images |
| `exponential:images:normalize-paths` | `ibexa:images:normalize-paths` | fix image field storage paths |
| `exponential:timestamps:to-utc` | `ibexa:timestamps:to-utc` | convert date and datetime values to UTC |
| `exponential:io:migrate-files` | `ibexa:io:migrate-files` | migrate binary files between IO handlers |
| `exponential:user:validate-password-hashes` | `ibexa:user:validate-password-hashes` | audit password hash algorithms |
| `exponential:user:expire-password` | `ibexa:user:expire-password` | force password expiry |
| `exponential:debug:config-resolver` | `ibexa:debug:config-resolver` | inspect siteaccess configuration values |
| `exponential:debug:config` | (extra short alias of the previous command) | same |

Frequently used commands in more detail (options and defaults as documented in the skeleton guide):

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

| Command | Option | Default |
|---|---|---|
| `exponential:install` | argument `type` | `exponential-oss` |
| `exponential:install` | `--skip-indexing` | off |
| `exponential:check-urls` | `-c, --iteration-count` | `50` |
| `exponential:check-urls` | `-u, --user` | `admin` (needs content read and versionread) |
| `exponential:images:resize-original` | `-f, --filter` | required |
| `exponential:images:resize-original` | `-i, --iteration-count` | `25` |
| `exponential:images:resize-original` | `-u, --user` | `admin` |

## Legacy bridge commands (commit `8cea9e8`, 2026-04-07; also `dc19470` in the Platform 4 bridge)

| New primary name | Old name | Purpose |
|---|---|---|
| `exponential:legacy:init` | `ezpublish:legacy:init` | prepare the platform for legacy use |
| `exponential:legacy:configure` | `ezpublish:configure` | write platform configuration from a legacy directory |
| `exponential:legacy:install-extensions` | `ezpublish:legacybundles:install_extensions` | install legacy extensions defined by bundles |
| `exponential:legacy:symlink` | `ezpublish:legacy:symlink` | install legacy settings and designs from `src` |
| `exponential:legacy:assets-install` | `ezpublish:legacy:assets_install` | install legacy assets and front controller wrappers |
| `exponential:legacy:script` | `ezpublish:legacy:script` | run a legacy script inside the bridged kernel |

Note the spelling change in two names: `install_extensions` became `install-extensions` and `assets_install` became `assets-install`. The old underscore spellings are the aliases.

## How to migrate your scripts

1. Find old names in cron files and scripts:

```bash
grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d /var/spool/cron ~/bin ./deploy 2>/dev/null
```

2. Replace them with the primary names from the tables above. Nothing breaks if you do not; the aliases are kept for backward compatibility.
3. Where a tool parses command output or `list`, expect the new names in the listing.

## Related

[Package forks and command renames (upgrade note)](../../bc/6.0/platform-package-forks-and-command-renames.md) · [Legacy bridge](../../features/6.0/legacy-bridge.md) · [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md)
