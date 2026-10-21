---
title: Muster in kernel/private/classes/cronjobs
audience: developer
category: standards
order: 518
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private/classes/cronjobs mit den Entwurfsmustern.
keywords: kernel/private/classes/cronjobs, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private/classes/cronjobs

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private/classes/cronjobs` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (27)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\Cronjob\Kernel\Audit` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/audit.php:17` | Der Cronjob-Teil audit (cronjobs/audit.php; [CronjobPart-audit] und die Gruppe frequent in cronjob.ini): die geplanten Arbeiten des Audits, expAuditMaintenan… |
| `Exponential\Cronjob\Kernel\BasketCleanup` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/basket_cleanup.php:25` | Der Code von cronjobs/basket_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Cachecleanup` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/cachecleanup.php:59` | Der Code von cronjobs/cachecleanup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Cleanuprss` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/cleanuprss.php:29` | Der Code von cronjobs/cleanuprss.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Clusterpurge` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/clusterpurge.php:25` | Der Code von cronjobs/clusterpurge.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Contentcheck` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/contentcheck.php:18` | Der Code von cronjobs/contentcheck.php: baut den Referenzindex der Inhaltsprüfungen auf und hält ihn im Cache (expContentCheckReferences). |
| `Exponential\Cronjob\Kernel\Contentjobs` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/contentjobs.php:20` | Der Cronjob-Teil contentjobs (cronjobs/contentjobs.php, Gruppe frequent): startet die Inhaltsjobs, die einen Worker brauchen und keinen haben — ein laufender Job, des… |
| `Exponential\Cronjob\Kernel\Explogrotate` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/explogrotate.php:19` | Der Code von cronjobs/explogrotate.php: der Cronjob-Teil für die Log-Rotation ([CronjobPart-logs], in keinem Standardteil, er läuft also nur, wenn eine Site ihn hinzufügt zu… |
| `Exponential\Cronjob\Kernel\Hide` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/hide.php:25` | Der Code von cronjobs/hide.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\HttpcacheCleanup` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/httpcache_cleanup.php:23` | Der Code von cronjobs/httpcache_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Indexcontent` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/indexcontent.php:25` | Der Code von cronjobs/indexcontent.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\InternalDraftsCleanup` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/internal_drafts_cleanup.php:25` | Der Code von cronjobs/internal_drafts_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Linkcheck` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/linkcheck.php:26` | Der Code von cronjobs/linkcheck.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Mailbounces` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/mailbounces.php:15` | – |
| `Exponential\Cronjob\Kernel\Mailpreferences` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/mailpreferences.php:15` | – |
| `Exponential\Cronjob\Kernel\Notification` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/notification.php:25` | Der Code von cronjobs/notification.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\OldDraftsCleanup` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/old_drafts_cleanup.php:25` | Der Code von cronjobs/old_drafts_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\QuerycacheCleanup` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/querycache_cleanup.php:18` | Der Code von cronjobs/querycache_cleanup.php. |
| `Exponential\Cronjob\Kernel\Rssimport` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/rssimport.php:407` | Der Code von cronjobs/rssimport.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\SessionGc` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/session_gc.php:25` | Der Code von cronjobs/session_gc.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\StaticcacheCleanup` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/staticcache_cleanup.php:25` | Der Code von cronjobs/staticcache_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Subtreeexpirycleanup` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/subtreeexpirycleanup.php:25` | Der Code von cronjobs/subtreeexpirycleanup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Trashpurge` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/trashpurge.php:25` | Der Code von cronjobs/trashpurge.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Unlock` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/unlock.php:93` | Der Code von cronjobs/unlock.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Unpublish` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/unpublish.php:25` | Der Code von cronjobs/unpublish.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Updateviewcount` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/updateviewcount.php:25` | Der Code von cronjobs/updateviewcount.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Kernel\Workflow` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `kernel/private/classes/cronjobs/workflow.php:25` | Der Code von cronjobs/workflow.php, in eine Klasse verschoben (#207 Stufe 1). |
