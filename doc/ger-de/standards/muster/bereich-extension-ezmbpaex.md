---
title: Muster in extension/ezmbpaex
audience: developer
category: standards
order: 448
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezmbpaex mit den Entwurfsmustern.
keywords: extension/ezmbpaex, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezmbpaex

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezmbpaex` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezmbpaexInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezmbpaex/ezinfo.php:7` | – |
| `eZPaEx` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezmbpaex/classes/ezpaex.php:11` | – |
| `ezpaextype` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/ezmbpaex/datatypes/ezpaex/ezpaextype.php:16` | Verarbeitet den Datentyp, der für den Passwortablauf verwendet wird. |
| `eZPaExUser` | class extends eZUser | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezmbpaex/login_handler/ezpaexuser.php:23` | Verarbeitet Anmeldungen von Benutzern unter den neuen Regeln zum Passwortablauf |

## Modulansichten (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/userpaex/password` | `password.php` | password | `extension/ezmbpaex/modules/userpaex/module.php:16` |
| `/userpaex/forgotpassword` | `forgotpassword.php` | password | `extension/ezmbpaex/modules/userpaex/module.php:23` |

## Richtlinienfunktionen (2)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `userpaex/password` | – | `extension/ezmbpaex/modules/userpaex/module.php:37` |
| `userpaex/editpaex` | – | `extension/ezmbpaex/modules/userpaex/module.php:38` |

## Cronjob-Teile (2)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `ezmbpaex_sendexpirynotifications` | `extension/ezmbpaex/cronjobs/ezmbpaex_sendexpirynotifications.php` | – |
| `ezmbpaex_updatechildren` | `extension/ezmbpaex/cronjobs/ezmbpaex_updatechildren.php` | – |
