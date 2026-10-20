---
title: workflow.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von workflow.ini.
keywords: Beispiele, Referenz, workflow.ini, examples, reference
type: generated
generated: exp:help:examples
---
# workflow.ini

Die ausgelieferten Einstellungen von `settings/workflow.ini`: 3 Blöcke, 6 Einstellungen.

> Exponential configuration file for workflows.  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/workflow.ini.append or  
>       settings/override/workflow.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  

| Block | Einstellungen |
|---|---|
| [EventSettings](EventSettings.md) | 3 |
| [OperationSettings](OperationSettings.md) | 1 |
| [SimpleShippingWorkflow](SimpleShippingWorkflow.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/workflow.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
