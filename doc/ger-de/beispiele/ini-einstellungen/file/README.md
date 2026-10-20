---
title: file.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von file.ini.
keywords: Beispiele, Referenz, file.ini, examples, reference
type: generated
generated: exp:help:examples
---
# file.ini

Die ausgelieferten Einstellungen von `settings/file.ini`: 6 Blöcke, 23 Einstellungen.

> Exponential configuration file for file handling  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/file.ini.append or  
>       settings/override/file.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  

| Block | Einstellungen |
|---|---|
| [BinaryFileSettings](BinaryFileSettings.md) | 1 |
| [FileSettings](FileSettings.md) | 4 |
| [ClusteringSettings](ClusteringSettings.md) | 2 |
| [eZDFSClusteringSettings](eZDFSClusteringSettings.md) | 12 |
| [PassThroughSettings](PassThroughSettings.md) | 2 |
| [ClusterEventsSettings](ClusterEventsSettings.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/file.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
