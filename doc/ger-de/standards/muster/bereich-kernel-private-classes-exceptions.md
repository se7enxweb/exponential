---
title: Muster in kernel/private/classes/exceptions
audience: developer
category: standards
order: 532
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private/classes/exceptions mit den Entwurfsmustern.
keywords: kernel/private/classes/exceptions, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private/classes/exceptions

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private/classes/exceptions` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (14)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZClusterHandlerDBNoConnectionException` | class extends eZDBException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/cluster/noconnection.php:19` | Klasse für die Ausnahme „keine Cluster-Verbindung“ |
| `eZClusterHandlerDBNoDatabaseException` | class extends eZDBException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/cluster/nodatabase.php:19` | Klasse für die Ausnahme „keine Datenbank“ |
| `eZDBException` | class extends ezcBaseException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/database/exception.php:26` | Klasse, die die oberste Klasse für alle datenbankbezogenen Ausnahmen darstellt |
| `eZDBNoConnectionException` | class extends eZDBException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/database/noconnection.php:19` | Klasse für die Ausnahme „keine Verbindung“ |
| `eZDFSFileHandlerNFSMountPointNotFoundException` | class extends ezcBaseException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/cluster/mount_point_not_found.php:19` | Klasse für die Ausnahme „Cluster-Mountpunkt nicht gefunden“ |
| `eZDFSFileHandlerNFSMountPointNotWriteableException` | class extends ezcBaseException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/cluster/mount_point_not_writeable.php:19` | Klasse für die Ausnahme „Cluster-Mountpunkt nicht beschreibbar“ |
| `eZDFSFileHandlerTableNotFoundException` | class extends ezcBaseException | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/cluster/table_not_found.php:19` | Klasse für die Ausnahme „Cluster-Tabelle nicht gefunden“ |
| `ezpAccessDenied` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/kernel/accessdenied.php:17` | Ausnahme, die auftritt, wenn der Zugriff verweigert wird. |
| `ezpFormTokenException` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/kernel/formtoken.php:23` | Ein POST wurde abgelehnt, weil sein Formular-Token (CSRF-Token) fehlte oder nicht zur Sitzung gehörte: das Formular wurde in einer anderen Sitzung geladen, bevor e… |
| `ezpLanguageNotFound` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/kernel/languagenotfound.php:17` | Ausnahme, die auftritt, wenn eine unbekannte Sprache verwendet wird. |
| `ezpModuleDisabled` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/kernel/moduledisabled.php:17` | Ausnahme, die auftritt, wenn ein Modul deaktiviert ist. |
| `ezpModuleNotFound` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/kernel/modulenotfound.php:17` | Ausnahme, die auftritt, wenn ein Modul nicht gefunden wird. |
| `ezpModuleViewDisabled` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/kernel/moduleviewdisabled.php:17` | Ausnahme, die auftritt, wenn ein Modul/eine Ansicht deaktiviert ist. |
| `ezpModuleViewNotFound` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `kernel/private/classes/exceptions/kernel/moduleviewnotfound.php:17` | Ausnahme, die auftritt, wenn ein Modul/eine Ansicht nicht gefunden wird. |
