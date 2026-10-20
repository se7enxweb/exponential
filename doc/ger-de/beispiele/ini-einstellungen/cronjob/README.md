---
title: cronjob.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von cronjob.ini.
keywords: Beispiele, Referenz, cronjob.ini, examples, reference
type: generated
generated: exp:help:examples
---
# cronjob.ini

Die ausgelieferten Einstellungen von `settings/cronjob.ini`: 19 Blöcke, 37 Einstellungen.

> Exponential configuration file for cronjobs.  
>
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/cronjob.ini.append or  
>       settings/override/cronjob.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  

| Block | Einstellungen |
|---|---|
| [CronjobSettings](CronjobSettings.md) | 4 |
| [AdminSettings](AdminSettings.md) | 4 |
| [PauseSettings](PauseSettings.md) | 2 |
| [ScriptDocumentation](ScriptDocumentation.md) | 2 |
| [CronjobPart-infrequent](CronjobPart-infrequent.md) | 1 |
| [CronjobPart-frequent](CronjobPart-frequent.md) | 1 |
| [CronjobPart-contentcheck](CronjobPart-contentcheck.md) | 1 |
| [CronjobPart-contentjobs](CronjobPart-contentjobs.md) | 1 |
| [CronjobPart-audit](CronjobPart-audit.md) | 1 |
| [CronjobPart-logs](CronjobPart-logs.md) | 1 |
| [CronjobPart-notification](CronjobPart-notification.md) | 1 |
| [CronjobPart-mailpreferences](CronjobPart-mailpreferences.md) | 1 |
| [CronjobPart-mailbounces](CronjobPart-mailbounces.md) | 1 |
| [linkCheckSettings](linkCheckSettings.md) | 9 |
| [CronjobPart-unlock](CronjobPart-unlock.md) | 1 |
| [CronjobPart-cluster_maintenance](CronjobPart-cluster_maintenance.md) | 1 |
| [CronjobPart-cleanuprss](CronjobPart-cleanuprss.md) | 1 |
| [CronjobPart-cache_cleanup](CronjobPart-cache_cleanup.md) | 1 |
| [CacheCleanupSettings](CacheCleanupSettings.md) | 3 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
