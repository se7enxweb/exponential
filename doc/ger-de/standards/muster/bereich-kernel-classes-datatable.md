---
title: Muster in kernel/classes/datatable
audience: developer
category: standards
order: 496
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/datatable mit den Entwurfsmustern.
keywords: kernel/classes/datatable, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/datatable

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/datatable` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (19)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expDataConnection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/datatable/expdataconnection.php:19` | Eine benannte Datenbankverbindung, in der Administration angelegt (Setup › Datentabellen › Verbindungen): eine Zeile von exp_data_connection. |
| `expDataConnections` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatable/expdataconnections.php:35` | Die benannten Datenbankverbindungen der Installation (doc/eng-us/features/6.0/data-tables.md, „Connections“): |
| `expDataSync` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatable/sync/expdatasync.php:39` | Die Synchronisation zwischen Inhalt und Datentabellen (doc/eng-us/features/6.0/data-tables.md, „Synchronisation“): |
| `expDataSyncConflict` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/datatable/sync/expdatasyncdefinition.php:140` | Ein Konflikt einer Zwei-Wege-Synchronisation mit der Regel „ask“ (Tabelle exp_data_sync_conflict): Beide Seiten haben sich seit der letzten Synchronisation geändert; der We… |
| `expDataSyncDefinition` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/datatable/sync/expdatasyncdefinition.php:19` | Eine Synchronisation zwischen Inhalt und einer Datentabelle (Tabelle exp_data_sync; doc/eng-us/features/6.0/data-tables.md, „Synchronisation“): die persistente… |
| `expDataSyncLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/datatable/sync/expdatasyncdefinition.php:104` | Die Zuordnung einer Zeile und eines Inhaltsobjekts einer Synchronisation (Tabelle exp_data_sync_link): der Schlüssel der Zeile, das Objekt und die Hashes und Zeiten beider… |
| `expDataSyncListener` | class | [Beobachter / Listener / Ereignis](beobachter.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatable/sync/expdatasynclistener.php:20` | Der Live-Spiegel der Datentabellen-Synchronisationen (doc/eng-us/features/6.0/data-tables.md, „Live mirror“): der Kernel benachrichtigt content/publish, cont… |
| `expDataSyncTask` | class implements expKernelTransferTask | – | `kernel/classes/datatable/sync/expdatasynctask.php:17` | Ein Lauf einer Datentabellen-Synchronisation, wie der Transfer-Runner ihn aufruft (expKernelTransferTask): Parameter sync (die ID der Definition) und, für die… |
| `expDataTableCatalogue` | class | [Singleton / instance()](singleton.md) | `kernel/classes/datatable/expdatatablecatalogue.php:32` | Der Katalog der Datentabellen (doc/eng-us/features/6.0/data-tables.md, „The catalogue“): jede persistente Klasse der Installation (ein eZPersistentOb… |
| `expDataTableClassFieldSource` | class implements expDataTableFieldSource | – | `kernel/classes/datatable/expdatatablefieldsources.php:186` | Die Felder einer Content-Klasse: eine Spalte je Attribut, typisiert durch ihren Datentyp, und die Spalten, die eine Zeile an ihr Objekt binden (contentobject_id, uniqu… |
| `expDataTableDescriber` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatable/expdatatabledescriber.php:25` | Eine Datentabelle, wie ihre Datenbank sie kennt (doc/eng-us/features/6.0/data-tables.md, „Describe“): ob die Tabelle existiert, ihre Spalten aus der eigenen Auskunft der Engine… |
| `expDataTableFieldSource` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/datatable/expdatatablefieldsource.php:33` | Etwas, das die Felder einer neuen Datentabelle an den Modul-Tabellen-Assistenten übergeben kann (doc/eng-us/features/6.0/ data-tables.md, „The module table wizard“… |
| `expDataTableFieldSources` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatable/expdatatablefieldsources.php:17` | Die Feldquellen des Modultabellen-Assistenten: die des Kernels (eine Content-Klasse, eine bestehende Tabelle auf einer Verbindung) und die Kernel-Services mit dem Tag datata… |
| `expDataTableFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatable/expdatatablefunctioncollection.php:28` | Die Template-Fetch-Funktionen des Moduls datatable (kernel/datatable/function_definition.php, doc/eng-us/features/6.0/data-tables.md, „In templates… |
| `expDataTableRelation` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatable/expdatatablerelation.php:25` | Beziehungen zwischen Datentabellen und Inhalt (doc/eng-us/features/6.0/data-tables.md, „Relations“): worauf eine Beziehungsspalte einer Zeile zeigt (ein Inhal… |
| `expDataTableSchema` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatable/expdatatableschema.php:30` | Das Schema der Datentabellen auf jeder Verbindung (doc/eng-us/features/6.0/data-tables.md, „Schema“): ein .dba-Schema oder eines, das aus der einer persistenten Klasse… |
| `expDataTableTableFieldSource` | class implements expDataTableFieldSource | – | `kernel/classes/datatable/expdatatablefieldsources.php:254` | Die Felder einer bestehenden Tabelle auf einer Verbindung (die Spalten, wie die Datenbank sie beschreibt). |
| `expDataTableWriter` | class | – | `kernel/classes/datatable/expdatatablewriter.php:33` | Schreibt Zeilen in die Tabelle einer persistenten Klasse, der einzige Weg, auf dem jeder Import, jede Synchronisation und jedes Werkzeug Zeilen in eine Datentabelle schreibt (doc/eng-us/features/6.0/data-tables… |
| `expKernelTableSchema` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatable/expkerneltableschema.php:25` | Tabellen eines .dba-Schemas auf jeder Engine: ob eine Tabelle existiert (beim Katalog der Engine selbst erfragt, nie aus einem Abfrageergebnis geschlossen: MongoDB antwo… |
