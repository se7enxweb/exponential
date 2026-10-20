---
title: Cronjob-Skripte und -Teile
audience: developer, admin
category: examples
order: 0
summary: Jedes Cronjob-Skript und jeder Cronjob-Teil, mit Ausführung und Zeitplanung.
keywords: Beispiele, Referenz, Cronjob-Skripte und -Teile, examples, reference
type: generated
generated: exp:help:examples
---
# Cronjob-Skripte und -Teile

Jedes Cronjob-Skript des Kernels und der mitgelieferten Erweiterungen und die Teile der ausgelieferten `settings/cronjob.ini`, die sie ausführen.

## Teile

| Teil | Skripte | Ausführen |
|---|---|---|
| (Standard) | [unpublish.php](unpublish.md), [rssimport.php](rssimport.md), [indexcontent.php](indexcontent.md), [hide.php](hide.md), [internal_drafts_cleanup.php](internal_drafts_cleanup.md) | `php runcronjobs.php` |
| `infrequent` | [basket_cleanup.php](basket_cleanup.md), [linkcheck.php](linkcheck.md), [mailpreferences.php](mailpreferences.md) | `php runcronjobs.php infrequent` |
| `frequent` | [notification.php](notification.md), [workflow.php](workflow.md), [contentjobs.php](contentjobs.md), [audit.php](audit.md), [mailbounces.php](mailbounces.md) | `php runcronjobs.php frequent` |
| `contentcheck` | [contentcheck.php](contentcheck.md) | `php runcronjobs.php contentcheck` |
| `contentjobs` | [contentjobs.php](contentjobs.md) | `php runcronjobs.php contentjobs` |
| `audit` | [audit.php](audit.md) | `php runcronjobs.php audit` |
| `logs` | [explogrotate.php](explogrotate.md) | `php runcronjobs.php logs` |
| `notification` | [notification.php](notification.md) | `php runcronjobs.php notification` |
| `mailpreferences` | [mailpreferences.php](mailpreferences.md) | `php runcronjobs.php mailpreferences` |
| `mailbounces` | [mailbounces.php](mailbounces.md) | `php runcronjobs.php mailbounces` |
| `unlock` | [unlock.php](unlock.md) | `php runcronjobs.php unlock` |
| `cluster_maintenance` | [clusterpurge.php](clusterpurge.md) | `php runcronjobs.php cluster_maintenance` |
| `cleanuprss` | [cleanuprss.php](cleanuprss.md) | `php runcronjobs.php cleanuprss` |
| `cache_cleanup` | [cachecleanup.php](cachecleanup.md), [httpcache_cleanup.php](httpcache_cleanup.md), [querycache_cleanup.php](querycache_cleanup.md) | `php runcronjobs.php cache_cleanup` |

## Skripte

| Skript | Was es tut | Teile |
|---|---|---|
| [audit.php](audit.md) | Audit: sink spools, alert rules, rotation by day, archives, retention and checkpoints | frequent, audit |
| [basket_cleanup.php](basket_cleanup.md) | Remove abandoned shopping baskets older than the configured threshold | infrequent |
| [cachecleanup.php](cachecleanup.md) | Remove expired and old view cache and cache-block files from disk | cache_cleanup |
| [cleanuprss.php](cleanuprss.md) | Trim the content the RSS import has created, keeping the newest items of each feed | cleanuprss |
| [clusterpurge.php](clusterpurge.md) | Purge expired cluster storage entries from the database backend | cluster_maintenance |
| [contentcheck.php](contentcheck.md) | Build the reference index of the content checks (unused assets), for installations where a page may not build it (contentcheck.ini BuildOnRequest=false) | contentcheck |
| [contentjobs.php](contentjobs.md) | Resume content jobs whose worker died and start queued jobs nobody started | frequent, contentjobs |
| [explogrotate.php](explogrotate.md) | Logs: rotate the logs over their MaxSize, prune and compress their archives (off unless a part runs it) | logs |
| [hide.php](hide.md) | Process scheduled hide and unhide actions on content objects | (Standard) |
| [httpcache_cleanup.php](httpcache_cleanup.md) | Remove expired, purged and orphaned entries from the role-aware HTTP cache | cache_cleanup |
| [indexcontent.php](indexcontent.md) | Index pending content modifications in the search engine | (Standard) |
| [internal_drafts_cleanup.php](internal_drafts_cleanup.md) | Remove internal drafts that exceed the configured age limit | (Standard) |
| [linkcheck.php](linkcheck.md) | Check all internal and external links in published content for broken URLs | infrequent |
| [mailbounces.php](mailbounces.md) | The bounce mailbox: hard bounces and spam complaints go on the suppression list (mailpreferences.ini [BounceSettings]) | frequent, mailbounces |
| [mailpreferences.php](mailpreferences.md) | E-mail preferences retention: consent log rows of people who are gone, expired confirmations, old suppression entries | infrequent, mailpreferences |
| [notification.php](notification.md) | Process and dispatch pending notification events to subscribers | frequent, notification |
| [old_drafts_cleanup.php](old_drafts_cleanup.md) | Remove user drafts that exceed the configured age limit |  |
| [querycache_cleanup.php](querycache_cleanup.md) | Remove the result files of the SQL query cache that are older than MaxAge or from before the last clear | cache_cleanup |
| [rssimport.php](rssimport.md) | Fetch and import configured RSS feeds into the content tree | (Standard) |
| [session_gc.php](session_gc.md) | Garbage-collect expired user sessions from the session store |  |
| [staticcache_cleanup.php](staticcache_cleanup.md) | Remove expired and stale entries from the static page cache |  |
| [subtreeexpirycleanup.php](subtreeexpirycleanup.md) | Clean up subtree expiry entries from the URL cache tables |  |
| [trashpurge.php](trashpurge.md) | Permanently delete objects in the trash (all, or as old as [TrashSettings] KeepItemsForDays says) |  |
| [unlock.php](unlock.md) | Release content objects stuck in a locked editing state | unlock |
| [unpublish.php](unpublish.md) | Process scheduled unpublish actions on content objects | (Standard) |
| [updateviewcount.php](updateviewcount.md) | Flush the in-memory view count buffer to the database |  |
| [workflow.php](workflow.md) | Process pending workflow events and advance stalled workflows | frequent |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
