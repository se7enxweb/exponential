---
title: datatable.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von datatable.ini.
keywords: Beispiele, Referenz, datatable.ini, examples, reference
type: generated
generated: exp:help:examples
---
# datatable.ini

Die ausgelieferten Einstellungen von `settings/datatable.ini`: 3 Blöcke, 10 Einstellungen.

> Data tables: the tables of the installation's persistent classes (eZPersistentObject), the named database  
> connections they and the tools can use, their schema, their relations to content, and the synchronisation between  
> tables and content. See doc/eng-us/features/6.0/data-tables.md.  
>
> Connections made in the administration (Setup > Data tables > Connections) are rows of the table  
> exp\_data\_connection and need nothing here. This file is for what an extension ships: a connection it declares, the  
> connection one of its classes uses, a relation its definition() does not state.  

| Block | Einstellungen |
|---|---|
| [DataTableSettings](DataTableSettings.md) | 5 |
| [ConnectionSettings](ConnectionSettings.md) | 4 |
| [ClassSettings](ClassSettings.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datatable.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
