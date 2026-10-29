---
title: Iterator
audience: developer
category: standards
order: 333
summary: Durchläuft eine Sammlung, ohne offenzulegen, wie sie gespeichert ist. 16 Instanzen in Exponential.
keywords: Entwurfsmuster, Iterator, Verhalten, Musterkatalog, design pattern, iterator, pattern catalogue
type: catalogue
---
# Iterator

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Durchläuft eine Sammlung, ohne offenzulegen, wie sie gespeichert ist.

| | |
| --- | --- |
| Gruppe | Verhalten |
| Instanzen | 16 |
| Erkannt an | Implementiert Iterator, IteratorAggregate oder ArrayAccess. |

## Klassen nach Bereich

### extension/sevenx_themes_media (1)

[Alle Muster von extension/sevenx_themes_media](bereich-extension-sevenx-themes-media.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxThemesMediaField` | class | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php:3` | – | – |

### extension/sqliimport (11)

[Alle Muster von extension/sqliimport](area-extension-sqliimport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `SQLIContentFieldset` | class | `extension/sqliimport/classes/content/sqlicontentfiedset.php:12` | SQLIContentFieldset | – |
| `SQLIContentFieldsetHolder` | class | `extension/sqliimport/classes/content/sqlicontentfieldsetholder.php:12` | SQLIFieldsetHolder | – |
| `SQLIContentOptions` | class extends SQLIImportOptions | `extension/sqliimport/classes/content/options/sqlicontentoptions.php:16` | Klasse mit grundlegenden Optionen für SQLIContent | – |
| `SQLIContentPublishOptions` | class extends SQLIImportOptions | `extension/sqliimport/classes/content/options/sqlicontentpublishoptions.php:12` | – | – |
| `SQLICSVOptions` | class extends SQLIImportOptions | `extension/sqliimport/classes/sourcehandlers/csv/sqlicsvoptions.php:13` | – | – |
| `SQLICSVRow` | class | `extension/sqliimport/classes/sourcehandlers/csv/sqlicsvrow.php:13` | – | – |
| `SQLICSVRowSet` | class | `extension/sqliimport/classes/sourcehandlers/csv/sqlicsvrowset.php:13` | – | – |
| `SQLIImportHandlerOptions` | class extends SQLIImportOptions | `extension/sqliimport/classes/sourcehandlers/options/sqliimporthandleroptions.php:16` | Optionsklasse für Import-Handler. | – |
| `SQLIImportOptions` | abstract class extends ezcBaseOptions | `extension/sqliimport/classes/options/sqliimportoptions.php:18` | Basis aller Optionsklassen für SQLIImport. Einfache Überschreibung von ezcBaseOptions aus eZComponents, die das Interface Iterator implementiert | – |
| `SQLILocationSet` | class | `extension/sqliimport/classes/location/sqlilocationset.php:12` | – | – |
| `SQLIXMLOptions` | class extends SQLIImportOptions | `extension/sqliimport/classes/sourcehandlers/xml/sqlixmloptions.php:13` | – | – |

### extension/xrowmetadata (1)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZClusterDirectoryIterator` | class | `extension/xrowmetadata/classes/ezclusterdirectoryiterator.php:3` | – | – |

### kernel/private (3)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpContentCriteriaSet` | class | `kernel/private/api/content/criteria_set.php:16` | Diese Klasse verwaltet eine Liste von Kriterien für Inhaltsabfragen | – |
| `ezpContentFieldSet` | class | `kernel/private/api/content/field_set.php:18` | Ermöglicht das Lesen und Schreiben von Inhaltsfeldern (Attributen) über eine Array-ähnliche Schnittstelle. | – |
| `ezpContentList` | class | `kernel/private/api/content/list.php:18` | Mit dieser Klasse können Sie eine zuvor abgerufene Inhaltsliste durchblättern, zum Beispiel mit ezpContentRepository::query(). Sie ist mehr oder weniger ein ResultSet aus einem… | – |
