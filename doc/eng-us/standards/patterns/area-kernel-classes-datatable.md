---
title: Patterns in kernel/classes/datatable
audience: developer
category: standards
order: 495
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/datatable with its design patterns.
keywords: kernel/classes/datatable, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/datatable

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/datatable` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (19)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expDataConnection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/datatable/expdataconnection.php:19` | A named database connection made in the administration (Setup › Data tables › Connections): a row of exp_data_connection. |
| `expDataConnections` | class | [Utility (static helper)](utility.md) | `kernel/classes/datatable/expdataconnections.php:35` | The named database connections of the installation (doc/eng-us/features/6.0/data-tables.md, "Connections"): |
| `expDataSync` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/datatable/sync/expdatasync.php:39` | The synchronisation between content and data tables (doc/eng-us/features/6.0/data-tables.md, "Synchronisation"): |
| `expDataSyncConflict` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/datatable/sync/expdatasyncdefinition.php:140` | A conflict of a two-way synchronisation with the rule "ask" (table exp_data_sync_conflict): both sides changed since the last synchronisation; the va… |
| `expDataSyncDefinition` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/datatable/sync/expdatasyncdefinition.php:19` | A synchronisation between content and a data table (table exp_data_sync; doc/eng-us/features/6.0/data-tables.md, "Synchronisation"): the persistent c… |
| `expDataSyncLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/datatable/sync/expdatasyncdefinition.php:104` | The pairing of a row and a content object of a synchronisation (table exp_data_sync_link): the row's key, the object, and the hashes and times of bot… |
| `expDataSyncListener` | class | [Observer / listener / event](observer.md), [Utility (static helper)](utility.md) | `kernel/classes/datatable/sync/expdatasynclistener.php:20` | The live mirror of the data table synchronisations (doc/eng-us/features/6.0/data-tables.md, "Live mirror"): the kernel notifies content/publish, cont… |
| `expDataSyncTask` | class implements expKernelTransferTask | – | `kernel/classes/datatable/sync/expdatasynctask.php:17` | One run of a data table synchronisation, as the transfer runner calls it (expKernelTransferTask): parameters sync (the definition's id) and, for the… |
| `expDataTableCatalogue` | class | [Singleton / instance()](singleton.md) | `kernel/classes/datatable/expdatatablecatalogue.php:32` | The catalogue of data tables (doc/eng-us/features/6.0/data-tables.md, "The catalogue"): every persistent class of the installation (an eZPersistentOb… |
| `expDataTableClassFieldSource` | class implements expDataTableFieldSource | – | `kernel/classes/datatable/expdatatablefieldsources.php:186` | The fields of a content class: one column per attribute, typed by its datatype, and the columns that tie a row to its object (contentobject_id, uniqu… |
| `expDataTableDescriber` | class | [Utility (static helper)](utility.md) | `kernel/classes/datatable/expdatatabledescriber.php:25` | A data table as its database has it (doc/eng-us/features/6.0/data-tables.md, "Describe"): whether the table exists, its columns from the engine's own… |
| `expDataTableFieldSource` | interface | [Interface (contract)](interface.md) | `kernel/classes/datatable/expdatatablefieldsource.php:33` | Something that can hand the fields of a new data table to the module table wizard (doc/eng-us/features/6.0/ data-tables.md, "The module table wizard"… |
| `expDataTableFieldSources` | class | [Utility (static helper)](utility.md) | `kernel/classes/datatable/expdatatablefieldsources.php:17` | The field sources of the module table wizard: the kernel's (a content class, an existing table on a connection) and the kernel services tagged datata… |
| `expDataTableFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `kernel/classes/datatable/expdatatablefunctioncollection.php:28` | The template fetch functions of the datatable module (kernel/datatable/function_definition.php, doc/eng-us/features/6.0/data-tables.md, "In templates… |
| `expDataTableRelation` | class | [Utility (static helper)](utility.md) | `kernel/classes/datatable/expdatatablerelation.php:25` | Relations between data tables and content (doc/eng-us/features/6.0/data-tables.md, "Relations"): what a relation column of a row points at (a content… |
| `expDataTableSchema` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/datatable/expdatatableschema.php:30` | The schema of data tables on any connection (doc/eng-us/features/6.0/data-tables.md, "Schema"): a .dba schema, or one made from a persistent class's… |
| `expDataTableTableFieldSource` | class implements expDataTableFieldSource | – | `kernel/classes/datatable/expdatatablefieldsources.php:254` | The fields of an existing table on a connection (the columns as the database describes them). |
| `expDataTableWriter` | class | – | `kernel/classes/datatable/expdatatablewriter.php:33` | Writes rows into a persistent class's table, the one way every import, sync and tool puts rows into a data table (doc/eng-us/features/6.0/data-tables… |
| `expKernelTableSchema` | class | [Utility (static helper)](utility.md) | `kernel/classes/datatable/expkerneltableschema.php:25` | Tables of a .dba schema on any engine: whether a table exists (asked of the engine's own catalogue, never inferred from a query result: MongoDB answe… |
