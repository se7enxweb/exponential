---
title: Troubleshooting a DXP project
audience: dxp-developer
category: dxp-development
order: 40
summary: The problems DXP developers meet most, with cause and fix - white screens, class not found, missing assets, admin build errors, stale content, missing image variations, outdated search, permission errors, JWT errors and the two SQLite errors.
keywords: troubleshooting, errors, 500, white screen, class not found, assets 404, Module not found, stale cache, image variations, search outdated, permission denied, JWT, SQLite, readonly database, logs
type: help
---
# Troubleshooting a DXP project

This page is for developers who need a fix now. The cases are the ones in the skeleton guide's section 20, plus the
causes behind them. Start with the logs.

## Contents

1. [First, the logs](#first-the-logs)
2. [Problems and fixes](#problems-and-fixes)
3. [Fork-related problems](#fork-related-problems)
4. [Asking for help](#asking-for-help)

## First, the logs

```bash
tail -f var/log/prod.log                     # or dev.log
tail -f /var/log/nginx/error.log             # or /var/log/apache2/error.log
APP_ENV=dev php bin/console cache:clear      # a development cache, for a stack trace on a test machine
```

Never switch a public production site to `dev`; reproduce on a copy.

## Problems and fixes

| Problem | Cause | Fix |
|---|---|---|
| White screen or error 500 | An exception | Read `var/log/<env>.log` |
| "Class not found" after `composer update` | Stale autoloader or cache | `composer dump-autoload -o`, then `cache:clear` |
| 404 on `/bundles/` or `/assets/` | Public assets not installed or not built | `assets:install --symlink --relative public`, `yarn dev`, `yarn ibexa:build` |
| `yarn ibexa:build` fails with "Module not found" | `var/encore/` is empty | Run `assets:install` first |
| Content or pages stale | Application or HTTP cache | `cache:clear`; purge the HTTP cache ([HTTP cache](http-cache-and-varnish.md)) |
| Image variations missing | Generated files removed or settings changed | `liip:imagine:cache:remove`, `cache:clear`; they come back on the next request |
| Search results outdated | Index not updated | `exponential:reindex` ([Reindexing](reindexing.md)) |
| "Permission denied" in `var/` or `public/var/` | Web server cannot write | Set the ACLs ([File permissions](file-permissions.md)) |
| REST or GraphQL JWT errors | Missing or unreadable keys | `lexik:jwt:generate-keypair --overwrite`, `cache:clear` ([JWT](jwt-authentication.md)) |
| `no such table: ibexa_section` (SQLite) | Database never initialized | Run `exponential:install exponential-oss` |
| `attempt to write a readonly database` (SQLite) | Wrong owner of the database file | `chmod 664`, `chown` to the web server's group |
| REST client gets 404 on `/api/ezp/v2/` | The v5 prefix is different | Check `ibexa.rest.path_prefix` ([The REST API](the-rest-api.md#where-it-lives)) |

## Fork-related problems

| Symptom | Cause | Check |
|---|---|---|
| The admin build reads files from `vendor/ibexa/...` | The upstream package is installed beside the fork | `composer why ibexa/admin-ui-assets` |
| A method "does not exist" on a class that has it | Two packages define the class | `composer show \| grep -E "^(ibexa\|ezsystems\|netgen)/"` |
| "Call to undefined method" in a layouts block template on PHP 8.4 | Upstream `netgen/layouts-core` instead of the fork | `composer why netgen/layouts-core` ([Layouts core](layouts-core-in-dxp.md)) |

See [The se7enxweb forks and `replace`](the-se7enxweb-forks-and-replace.md#the-rule-never-both).

## Asking for help

Collect the PHP version, `composer show "se7enxweb/*"`, the exact error from the log, and the steps to reproduce.
Report bugs in the issue tracker of the repository concerned (the skeleton, the metapackage or the fork); report
security problems privately, as the skeleton's README asks, not in a public issue.

## Related pages

- [Day-to-day commands](day-to-day-commands.md)
- [DXP developer FAQ](dxp-developer-faq.md)
- [When something goes wrong (Exponential 6)](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
