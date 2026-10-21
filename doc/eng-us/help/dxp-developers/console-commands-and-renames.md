---
title: Console commands and renames
audience: dxp-developer
category: dxp-development
order: 35
summary: The console commands a DXP developer uses, the exponential prefix and its ibexa aliases, the commands that keep their upstream names, and how to find and migrate old names in scripts and cron files.
keywords: console, bin/console, exponential:, ibexa:, aliases, renamed commands, exponential:install, exponential:reindex, ibexa:cron:run, ibexa:graphql:generate-schema, cleanup-versions, copy-subtree
type: help
---
# Console commands and renames

This page is for developers who script a DXP project. The commands of the content kernel have an `exponential:`
primary name; the earlier `ibexa:` names stay as working aliases. The full reference is
[Platform console command names](../../specifications/6.0/platform-console-commands.md); this page is the short form.

## Contents

1. [The rule](#the-rule)
2. [The commands of the core](#the-commands-of-the-core)
3. [Commands with upstream names](#commands-with-upstream-names)
4. [Symfony and Doctrine commands you need](#symfony-and-doctrine-commands-you-need)
5. [Migrating old names](#migrating-old-names)

## The rule

| Prefix | Status |
|---|---|
| `exponential:*` | Primary name, use it in everything new |
| `ibexa:*` | Deprecated alias, fully functional |
| `ezplatform:*` | Does not exist on Platform v5 |

## The commands of the core

Every one of these was found in the installed `se7enxweb/exponential-platform-dxp-core`:

| Command | Purpose |
|---|---|
| `exponential:install [type]` | Install schema and seed data (`exponential-oss` by default; `--skip-indexing`) |
| `exponential:reindex` | Rebuild the search index ([Reindexing](reindexing.md)) |
| `exponential:check-urls` | Check external links in content |
| `exponential:content:cleanup-versions` | Prune drafts and archived versions (`--keep`, `--status`, `--excluded-content-types`) |
| `exponential:content:remove-duplicate-fields` | Remove duplicate content fields |
| `exponential:content-type-group:set-system` | Mark a content type group as system |
| `exponential:copy-subtree <source> <target>` | Copy a location subtree |
| `exponential:delete-content-translation` | Delete one translation of a content item |
| `exponential:urls:regenerate-aliases` | Rebuild URL aliases |
| `exponential:images:resize-original` | Batch-resize stored original images |
| `exponential:images:normalize-paths` | Fix image field storage paths |
| `exponential:timestamps:to-utc` | Convert date and time values to UTC (dry run first) |
| `exponential:io:migrate-files` | Move binary files between storage handlers |
| `exponential:user:validate-password-hashes` | Check password hash algorithms |
| `exponential:user:expire-password` | Force password expiry |
| `exponential:debug:config-resolver` (short: `exponential:debug:config`) | Show the value a siteaccess resolves |

Commands that change content take `--user`; the user needs the matching policies.

## Commands with upstream names

| Command | Purpose |
|---|---|
| `ibexa:cron:run` | Run the cron jobs ([Cron](cron.md)) |
| `ibexa:graphql:generate-schema` | Generate the GraphQL schema ([GraphQL](graphql-and-graphiql.md)) |
| `ibexa:user:update-user` | Update a user from the command line (from `ibexa/user`) |
| `lexik:jwt:generate-keypair` | JWT keys ([JWT](jwt-authentication.md)) |
| `liip:imagine:cache:remove` | Remove generated image variations |
| `fos:httpcache:invalidate:path`, `fos:httpcache:invalidate:tag` | Purge the HTTP cache |
| `bazinga:js-translation:dump` | Admin JavaScript translations |

## Symfony and Doctrine commands you need

```bash
php bin/console list                                   # everything registered
php bin/console help <command>                         # options of one command
php bin/console cache:clear && php bin/console cache:warmup
php bin/console debug:router
php bin/console doctrine:migration:migrate --allow-no-migration
php bin/console messenger:consume ibexa.messenger.transport
```

## Migrating old names

```bash
grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d /var/spool/cron ./deploy 2>/dev/null
```

Replace each match whose command has an `exponential:` name. Nothing breaks if you wait: the aliases remain.

## Related pages

- [Platform console command names](../../specifications/6.0/platform-console-commands.md)
- [Day-to-day commands](day-to-day-commands.md)
- [Platform package forks, Step 4](../../bc/6.0/platform-package-forks-and-command-renames.md#step-4-console-command-names)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
