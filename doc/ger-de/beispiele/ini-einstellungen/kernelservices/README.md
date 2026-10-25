---
title: kernelservices.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von kernelservices.ini.
keywords: Beispiele, Referenz, kernelservices.ini, examples, reference
type: generated
generated: exp:help:examples
---
# kernelservices.ini

Die ausgelieferten Einstellungen von `settings/kernelservices.ini`: 15 Blöcke, 148 Einstellungen.

> The kernel service container: shared objects of the kernel by name, made on  
> first use, in a scope, and the compiled boot of the web kernel.  
> See doc/eng-us/bc/6.0/kernel-services.md.  
>
> Extensions add or replace services in  
> extension/<name>/settings/kernelservices.ini.append.php (a later block of the  
> same name wins, as everywhere in the settings). After a change:  
> ./console exp:services:compile (the deploy and ezcache.php --clear-tag=ini do  
> it too), ./console exp:services:check to check.  

| Block | Einstellungen |
|---|---|
| [ContainerSettings](ContainerSettings.md) | 4 |
| [Service_db](Service_db.md) | 5 |
| [Service_http](Service_http.md) | 5 |
| [Service_ini.site](Service_ini.site.md) | 6 |
| [Service_ini.i18n](Service_ini.i18n.md) | 6 |
| [Service_extensions.overrides](Service_extensions.overrides.md) | 4 |
| [Tags](Tags.md) | 1 |
| [Service_transfer.registry](Service_transfer.registry.md) | 5 |
| [Service_transfer.package](Service_transfer.package.md) | 17 |
| [Service_transfer.rss_import](Service_transfer.rss_import.md) | 16 |
| [Service_transfer.rss_export](Service_transfer.rss_export.md) | 16 |
| [Service_transfer.infocollector](Service_transfer.infocollector.md) | 15 |
| [Service_transfer.audit](Service_transfer.audit.md) | 15 |
| [Service_transfer.datatable](Service_transfer.datatable.md) | 18 |
| [Service_transfer.pdf](Service_transfer.pdf.md) | 15 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
