---
title: Cronjob scripts and parts
audience: developer, admin
category: examples
order: 0
summary: Every cronjob script and cronjob part, with how to run and schedule them.
keywords: Cronjob scripts and parts, examples, reference
type: generated
generated: exp:help:examples
---
# Cronjob scripts and parts

Every cronjob script of the kernel and the bundled extensions and the parts of the shipped `settings/cronjob.ini` that run them.

## Parts

| Part | Scripts | Run it |
|---|---|---|
| (default) | [unpublish.php](unpublish.md), [rssimport.php](rssimport.md), [indexcontent.php](indexcontent.md), [hide.php](hide.md), [internal_drafts_cleanup.php](internal_drafts_cleanup.md) | `php runcronjobs.php` |
| `infrequent` | [basket_cleanup.php](basket_cleanup.md), [linkcheck.php](linkcheck.md), [mailpreferences.php](mailpreferences.md), [usersessions.php](usersessions.md), [exphelp.php](exphelp.md) | `php runcronjobs.php infrequent` |
| `frequent` | [notification.php](notification.md), [workflow.php](workflow.md), [contentjobs.php](contentjobs.md), [datatablesync.php](datatablesync.md), [audit.php](audit.md), [mailbounces.php](mailbounces.md) | `php runcronjobs.php frequent` |
| `contentcheck` | [contentcheck.php](contentcheck.md) | `php runcronjobs.php contentcheck` |
| `contentjobs` | [contentjobs.php](contentjobs.md) | `php runcronjobs.php contentjobs` |
| `datatable` | [datatablesync.php](datatablesync.md) | `php runcronjobs.php datatable` |
| `audit` | [audit.php](audit.md) | `php runcronjobs.php audit` |
| `logs` | [explogrotate.php](explogrotate.md) | `php runcronjobs.php logs` |
| `notification` | [notification.php](notification.md) | `php runcronjobs.php notification` |
| `mailpreferences` | [mailpreferences.php](mailpreferences.md) | `php runcronjobs.php mailpreferences` |
| `mailbounces` | [mailbounces.php](mailbounces.md) | `php runcronjobs.php mailbounces` |
| `usersessions` | [usersessions.php](usersessions.md) | `php runcronjobs.php usersessions` |
| `exphelp` | [exphelp.php](exphelp.md) | `php runcronjobs.php exphelp` |
| `unlock` | [unlock.php](unlock.md) | `php runcronjobs.php unlock` |
| `cluster_maintenance` | [clusterpurge.php](clusterpurge.md) | `php runcronjobs.php cluster_maintenance` |
| `cleanuprss` | [cleanuprss.php](cleanuprss.md) | `php runcronjobs.php cleanuprss` |
| `cache_cleanup` | [cachecleanup.php](cachecleanup.md), [httpcache_cleanup.php](httpcache_cleanup.md), [querycache_cleanup.php](querycache_cleanup.md) | `php runcronjobs.php cache_cleanup` |

## Scripts

| Script | What it does | Parts |
|---|---|---|
| [audit.php](audit.md) | Audit: sink spools, alert rules, rotation by day, archives, retention and checkpoints | frequent, audit |
| [basket_cleanup.php](basket_cleanup.md) | Remove abandoned shopping baskets older than the configured threshold | infrequent |
| [cachecleanup.php](cachecleanup.md) | Remove expired and old view cache and cache-block files from disk | cache_cleanup |
| [cleanuprss.php](cleanuprss.md) | Trim the content the RSS import has created, keeping the newest items of each feed | cleanuprss |
| [clusterpurge.php](clusterpurge.md) | Purge expired cluster storage entries from the database backend | cluster_maintenance |
| [contentcheck.php](contentcheck.md) | Build the reference index of the content checks (unused assets), for installations where a page may not build it (contentcheck.ini BuildOnRequest=false) | contentcheck |
| [contentjobs.php](contentjobs.md) | Resume content jobs whose worker died and start queued jobs nobody started | frequent, contentjobs |
| [datatablesync.php](datatablesync.md) | Start the data table synchronisations that are due (scheduled and live, by their interval) | frequent, datatable |
| [exphelp.php](exphelp.md) | Help maintained as content: removes old answers to "Was this helpful?" and records the DITA export of the help in git when exphelp.ini [ContentSettings] DitaHistoryDirectory is set | infrequent, exphelp |
| [explogrotate.php](explogrotate.md) | Logs: rotate the logs over their MaxSize, prune and compress their archives (off unless a part runs it) | logs |
| [hide.php](hide.md) | Process scheduled hide and unhide actions on content objects | (default) |
| [httpcache_cleanup.php](httpcache_cleanup.md) | Remove expired, purged and orphaned entries from the role-aware HTTP cache | cache_cleanup |
| [indexcontent.php](indexcontent.md) | Index pending content modifications in the search engine | (default) |
| [internal_drafts_cleanup.php](internal_drafts_cleanup.md) | Remove internal drafts that exceed the configured age limit | (default) |
| [linkcheck.php](linkcheck.md) | Check all internal and external links in published content for broken URLs | infrequent |
| [mailbounces.php](mailbounces.md) | The bounce mailbox: hard bounces and spam complaints go on the suppression list (mailpreferences.ini [BounceSettings]) | frequent, mailbounces |
| [mailpreferences.php](mailpreferences.md) | E-mail preferences retention: consent log rows of people who are gone, expired confirmations, old suppression entries | infrequent, mailpreferences |
| [notification.php](notification.md) | Process and dispatch pending notification events to subscribers | frequent, notification |
| [old_drafts_cleanup.php](old_drafts_cleanup.md) | Remove user drafts that exceed the configured age limit |  |
| [querycache_cleanup.php](querycache_cleanup.md) | Remove the result files of the SQL query cache that are older than MaxAge or from before the last clear | cache_cleanup |
| [rssimport.php](rssimport.md) | Fetch and import configured RSS feeds into the content tree | (default) |
| [session_gc.php](session_gc.md) | Garbage-collect expired user sessions from the session store |  |
| [staticcache_cleanup.php](staticcache_cleanup.md) | Remove expired and stale entries from the static page cache |  |
| [subtreeexpirycleanup.php](subtreeexpirycleanup.md) | Clean up subtree expiry entries from the URL cache tables |  |
| [trashpurge.php](trashpurge.md) | Permanently delete objects in the trash (all, or as old as [TrashSettings] KeepItemsForDays says) |  |
| [unlock.php](unlock.md) | Release content objects stuck in a locked editing state | unlock |
| [unpublish.php](unpublish.md) | Process scheduled unpublish actions on content objects | (default) |
| [updateviewcount.php](updateviewcount.md) | Flush the in-memory view count buffer to the database |  |
| [usersessions.php](usersessions.md) | Signed-in sessions ("Your devices"): ends the sessions not used for [Session] SessionTimeout, removes ended ones after [UserSessionSettings] RetentionDays | infrequent, usersessions |
| [workflow.php](workflow.md) | Process pending workflow events and advance stalled workflows | frequent |

- [All examples](../README.md)

---

Generated by `./console exp:help:examples` from `settings/cronjob.ini`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
