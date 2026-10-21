---
title: Iterator
audience: developer
category: standards
order: 333
summary: Durchläuft eine Sammlung, ohne offenzulegen, wie sie gespeichert ist. 5 Instanzen in Exponential.
keywords: Entwurfsmuster, Iterator, Verhalten, Musterkatalog, design pattern, iterator, pattern catalogue
type: catalogue
---
# Iterator

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Durchläuft eine Sammlung, ohne offenzulegen, wie sie gespeichert ist.

|  |  |
| --- | --- |
| Gruppe | Verhalten |
| Instanzen | 5 |
| Erkannt an | Implementiert Iterator, IteratorAggregate oder ArrayAccess. |

## Klassen nach Bereich

### extension/sevenx_themes_media (1)

[Alle Muster von extension/sevenx_themes_media](bereich-extension-sevenx-themes-media.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxThemesMediaField` | class | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php:3` | – | – |

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
