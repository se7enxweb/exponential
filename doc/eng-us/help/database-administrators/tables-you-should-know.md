---
title: The tables a database administrator should know
audience: dba
category: database-administration
order: 34
summary: A map of the Exponential kernel's tables for database administrators, grouped by purpose (content, classes, users and permissions, URL aliases, search, sessions, queues, site data, files, shop, audit, mail, API), with the tables that grow fastest, the ones safe to inspect, and the tools to use instead of editing them by hand.
keywords: tables, schema map, ezcontentobject, ezcontentobject_attribute, ezcontentobject_tree, ezcontentobject_version, ezurlalias_ml, ezsearch_word, ezsearch_object_word_link, ezsession, ezpending_actions, ezsite_data, ezuser, ezuservisit, ezpolicy, expaudit, expmail, expapikey, ezdfsfile, growth, table size
type: help
---
# The tables a database administrator should know

This page is for database administrators who need to find their way in an Exponential database: to size it, to read
it for a report, or to understand which table a problem lives in. It groups the kernel's tables (from
`kernel/sql/mysql/kernel_schema.sql`, 129 tables) by purpose. Extensions add their own.

> **Warning**
> Read these tables; do not edit them by hand. Content, versions, the node tree, URL aliases and the search index
> depend on each other, and the caches do not see changes made outside Exponential. Use the administration, the
> scripts named below, or the content API. After any write outside Exponential, clear the caches.

## Contents

1. [Content](#content)
2. [Classes and structure](#classes-and-structure)
3. [Users and permissions](#users-and-permissions)
4. [URL aliases](#url-aliases)
5. [Search](#search)
6. [Sessions, queues and housekeeping](#sessions-queues-and-housekeeping)
7. [Files, site data and other areas](#files-site-data-and-other-areas)
8. [Which tables grow](#which-tables-grow)
9. [Tools instead of hand edits](#tools-instead-of-hand-edits)

## Content

| Table | Holds |
|---|---|
| `ezcontentobject` | one row per content object: class, owner, section, current version, `remote_id` |
| `ezcontentobject_version` | every version of every object (drafts, published, archived) |
| `ezcontentobject_attribute` | the field values, per object, version and language: usually the largest table |
| `ezcontentobject_name` | object names per version and language |
| `ezcontentobject_tree` | the node tree: `node_id`, `parent_node_id`, `path_string`, `depth`, `main_node_id` |
| `eznode_assignment` | where a version will be placed when published |
| `ezcontentobject_link` | relations between objects |
| `ezcontentobject_trash` | trashed nodes (`trashed`, `trashed_by`, `trashed_via` since 6.0.15) |
| `ezcobj_state*` | object states and their groups |

## Classes and structure

`ezcontentclass`, `ezcontentclass_attribute`, `ezcontentclass_name`, `ezcontentclassgroup`,
`ezcontentclass_classgroup`, `ezsection`, `ezcontent_language`.

## Users and permissions

| Table | Holds |
|---|---|
| `ezuser` | logins, e-mail addresses, password hashes (`password_hash` widened to 255) |
| `ezuser_setting` | whether an account is enabled |
| `ezuservisit` | last and current visit, sign-in count, failed sign-in attempts |
| `ezrole`, `ezpolicy`, `ezpolicy_limitation`, `ezpolicy_limitation_value`, `ezuser_role` | roles, policies and their assignments |
| `ezforgot_password`, `ezuser_accountkey` | password reset and activation keys |
| `ezpreferences` | per-user preferences of the administration |

These tables hold personal data. Limit who can read them and keep them out of test copies you hand to others.

## URL aliases

`ezurlalias_ml` (the multilingual URL aliases), `ezurlalias_ml_incr`, `ezurlwildcard`, and the older `ezurlalias`.
`exp:verify_aliases` checks `ezurlalias_ml` for consistency (`--fix` repairs safe issues, `--sql` shows the queries);
`bin/php/updateniceurls.php` rebuilds the aliases.

## Search

`ezsearch_word` (one row per word, with `object_count`), `ezsearch_object_word_link` (one row per word occurrence in an
object, with neighbors and `integer_value`), `ezsearch_search_phrase` (search statistics, only with `LogSearchStats`).
See [The built-in search index tables](search-index-tables.md).

## Sessions, queues and housekeeping

| Table | Holds | See |
|---|---|---|
| `ezsession` | sessions, when `ezpSessionHandlerDB` is used | [Sessions in the database](sessions-in-the-database.md) |
| `ezpending_actions` | queued work: delayed indexing (`index_object`, `index_moved_node`) and other actions | [Housekeeping](housekeeping-cronjobs-and-queues.md) |
| `ezpublishingqueueprocesses` | the asynchronous publisher's processes | |
| `ezworkflow_process`, `ezoperation_memento`, `ezwaituntildatevalue` | running and waiting workflows | |
| `ezscheduled_script`, `ezmodule_run` | scheduled scripts and module runs | |
| `ezbasket`, `ezproductcollection*` | shop baskets, cleaned by `basket_cleanup.php` | |

## Files, site data and other areas

| Table(s) | Holds |
|---|---|
| `ezsite_data` | name and value pairs, among them `ezpublish-version` and `ezpublish-release` |
| `ezimagefile`, `ezbinaryfile`, `ezmedia` | metadata of files whose content is in `var/storage` |
| `ezurl`, `ezurl_object_link` | external links and where they are used (checked by `linkcheck.php`) |
| `ezorder*`, `ezvat*`, `ezdiscount*`, `ezcurrencydata`, `ezmultipricedata`, `ezwishlist`, `ezpaymentobject` | the shop |
| `expaudit_cursor`, `expaudit_event`, `expaudit_file` | the audit index (6.0.15) |
| `expmail_*` | e-mail preferences, consent log, suppression list (6.0.15) |
| `expbookmark_folder` | bookmark folders (6.0.15) |
| `expbookmark_view` | the addresses of bookmarks of system pages (6.0.15) |
| `expapikey`, `ezprest_*` | personal API keys, OAuth clients and tokens: credentials, treat as secrets |
| `ezrss_*`, `ezpdf_export`, `ezpackage`, `ezinfocollection*`, `ezcollab_*`, `eznotification*` | feeds, exports, packages, form submissions, collaboration, notifications |

The DFS cluster's `ezdfsfile` and `ezdfsfile_cache` live in the cluster's own database
([Clustering](clustering-dfs-and-the-database.md)).

## Which tables grow

Measure on your own site; **Setup > System information > Measure sizes** shows the database size, and your engine's
catalog the size per table. Typically largest and growing: `ezcontentobject_attribute` (every version and language),
`ezsearch_object_word_link`, `ezcontentobject_version`, `ezurlalias_ml`, and `ezsession` when sessions are in the
database and expired rows are not removed. Old versions are pruned with `bin/php/cleanupversions.php` within the
`VersionManagement` limits.

## Tools instead of hand edits

| Need | Use |
|---|---|
| Remove old versions | `php bin/php/cleanupversions.php --apply` |
| Rebuild URL aliases | `php bin/php/updateniceurls.php`, `exp:verify_aliases` |
| Rebuild the search index | `php bin/php/updatesearchindex.php --clean` |
| Remove expired sessions | `php bin/php/ezsessiongc.php` |
| Duplicate logins | `exp:userlogincheck` (`--fix-index` adds a unique index on `ezuser.login` once there are none) |
| Schema differences | the upgrade check, `ezsqldiff.php` |

## Related pages

- [The .dba schema format](the-dba-schema-format.md)
- [Housekeeping: cronjobs and queue tables](housekeeping-cronjobs-and-queues.md)
- [Content model and editing](../../guides/content-model-and-editing.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
