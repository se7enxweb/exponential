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
| `infrequent` | [basket_cleanup.php](basket_cleanup.md), [linkcheck.php](linkcheck.md), [mailpreferences.php](mailpreferences.md), [usersessions.php](usersessions.md), [useraudit.php](useraudit.md), [exphelp.php](exphelp.md) | `php runcronjobs.php infrequent` |
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
| `useraudit` | [useraudit.php](useraudit.md) | `php runcronjobs.php useraudit` |
| `exphelp` | [exphelp.php](exphelp.md) | `php runcronjobs.php exphelp` |
| `unlock` | [unlock.php](unlock.md) | `php runcronjobs.php unlock` |
| `cluster_maintenance` | [clusterpurge.php](clusterpurge.md) | `php runcronjobs.php cluster_maintenance` |
| `cleanuprss` | [cleanuprss.php](cleanuprss.md) | `php runcronjobs.php cleanuprss` |
| `cache_cleanup` | [cachecleanup.php](cachecleanup.md), [httpcache_cleanup.php](httpcache_cleanup.md), [querycache_cleanup.php](querycache_cleanup.md) | `php runcronjobs.php cache_cleanup` |

## Skripte

| Skript | Was es tut | Teile |
|---|---|---|
| [audit.php](audit.md) | Audit: Spools der Ziele, Alarmregeln, Rotation nach Tagen, Archive, Aufbewahrung und Prüfpunkte | frequent, audit |
| [basket_cleanup.php](basket_cleanup.md) | Entfernt verlassene Warenkörbe, die älter als die konfigurierte Grenze sind | infrequent |
| [cachecleanup.php](cachecleanup.md) | Entfernt abgelaufene und alte View-Cache- und Cache-Block-Dateien von der Festplatte | cache_cleanup |
| [cleanuprss.php](cleanuprss.md) | Kürzt die vom RSS-Import angelegten Inhalte und behält die neuesten Einträge jedes Feeds | cleanuprss |
| [clusterpurge.php](clusterpurge.md) | Löscht abgelaufene Einträge des Cluster-Speichers aus dem Datenbank-Backend | cluster_maintenance |
| [contentcheck.php](contentcheck.md) | Baut den Referenzindex der Inhaltsprüfungen (ungenutzte Assets) für Installationen, in denen eine Seite ihn nicht bauen darf (contentcheck.ini BuildOnRequest=false) | contentcheck |
| [contentjobs.php](contentjobs.md) | Setzt Inhaltsaufträge fort, deren Worker gestorben ist, und startet wartende Aufträge, die niemand gestartet hat | frequent, contentjobs |
| [datatablesync.php](datatablesync.md) | Startet die fälligen Synchronisationen der Datentabellen (geplante und Live-Synchronisationen, nach ihrem Intervall) | frequent, datatable |
| [exphelp.php](exphelp.md) | Als Inhalt gepflegte Hilfe: entfernt alte Antworten auf "War das hilfreich?" und hält den DITA-Export der Hilfe in Git fest, wenn exphelp.ini [ContentSettings] DitaHistoryDirectory gesetzt ist | infrequent, exphelp |
| [explogrotate.php](explogrotate.md) | Logs: rotiert die Logs über ihrer MaxSize, bereinigt und komprimiert ihre Archive (aus, solange kein Teil ihn ausführt) | logs |
| [hide.php](hide.md) | Führt geplante Aktionen zum Verbergen und Anzeigen von Inhaltsobjekten aus | (Standard) |
| [httpcache_cleanup.php](httpcache_cleanup.md) | Entfernt abgelaufene, gelöschte und verwaiste Einträge aus dem rollenbewussten HTTP-Cache | cache_cleanup |
| [indexcontent.php](indexcontent.md) | Indiziert anstehende Inhaltsänderungen in der Suchmaschine | (Standard) |
| [internal_drafts_cleanup.php](internal_drafts_cleanup.md) | Entfernt interne Entwürfe, die die konfigurierte Altersgrenze überschreiten | (Standard) |
| [linkcheck.php](linkcheck.md) | Prüft alle internen und externen Links in veröffentlichten Inhalten auf defekte URLs | infrequent |
| [mailbounces.php](mailbounces.md) | Das Postfach für Rückläufer: Hard Bounces und Spam-Beschwerden kommen auf die Sperrliste (mailpreferences.ini [BounceSettings]) | frequent, mailbounces |
| [mailpreferences.php](mailpreferences.md) | Aufbewahrung der E-Mail-Einstellungen: Zeilen des Einwilligungsprotokolls von Personen, die nicht mehr da sind, abgelaufene Bestätigungen, alte Einträge der Sperrliste | infrequent, mailpreferences |
| [notification.php](notification.md) | Verarbeitet anstehende Benachrichtigungsereignisse und verschickt sie an die Abonnenten | frequent, notification |
| [old_drafts_cleanup.php](old_drafts_cleanup.md) | Entfernt Entwürfe von Benutzern, die die konfigurierte Altersgrenze überschreiten |  |
| [querycache_cleanup.php](querycache_cleanup.md) | Entfernt die Ergebnisdateien des SQL-Abfrage-Caches, die älter als MaxAge sind oder von vor dem letzten Leeren stammen | cache_cleanup |
| [rssimport.php](rssimport.md) | Holt die konfigurierten RSS-Feeds und importiert sie in den Inhaltsbaum | (Standard) |
| [session_gc.php](session_gc.md) | Räumt abgelaufene Benutzersitzungen aus dem Sitzungsspeicher |  |
| [staticcache_cleanup.php](staticcache_cleanup.md) | Entfernt abgelaufene und veraltete Einträge aus dem statischen Seiten-Cache |  |
| [subtreeexpirycleanup.php](subtreeexpirycleanup.md) | Räumt Einträge zum Ablauf von Teilbäumen aus den URL-Cache-Tabellen |  |
| [trashpurge.php](trashpurge.md) | Löscht Objekte im Papierkorb endgültig (alle oder so alte, wie [TrashSettings] KeepItemsForDays sagt) |  |
| [unlock.php](unlock.md) | Gibt Inhaltsobjekte frei, die in einem gesperrten Bearbeitungszustand hängen | unlock |
| [unpublish.php](unpublish.md) | Führt geplante Aktionen zum Zurückziehen von Inhaltsobjekten aus | (Standard) |
| [updateviewcount.php](updateviewcount.md) | Schreibt den Puffer der Aufrufzähler aus dem Speicher in die Datenbank |  |
| [useraudit.php](useraudit.md) | Benutzer-Audit-Log: entfernt die Zeilen von expuser_audit, die älter sind als audit.ini [UserAuditSettings] RetentionDays | infrequent, useraudit |
| [usersessions.php](usersessions.md) | Angemeldete Sitzungen ("Ihre Geräte"): beendet die Sitzungen, die für [Session] SessionTimeout nicht benutzt wurden, und entfernt beendete nach [UserSessionSettings] RetentionDays | infrequent, usersessions |
| [workflow.php](workflow.md) | Verarbeitet anstehende Workflow-Ereignisse und bringt hängende Workflows weiter | frequent |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
