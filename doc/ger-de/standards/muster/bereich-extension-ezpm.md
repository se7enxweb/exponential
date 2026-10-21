---
title: Muster in extension/ezpm
audience: developer
category: standards
order: 450
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezpm mit den Entwurfsmustern.
keywords: extension/ezpm, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezpm

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezpm` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZContact` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezpm/classes/ezcontact.php:15` | – |
| `eZPm` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezpm/classes/ezpm.php:15` | – |
| `eZPmBlackList` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezpm/classes/ezpmblacklist.php:15` | – |
| `eZPmFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `extension/ezpm/modules/pm/ezpmfunctioncollection.php:5` | – |
| `ezpmInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezpm/ezinfo.php:25` | – |

## Modulansichten (12)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/pm/message` | `view_message.php` | – | `extension/ezpm/modules/pm/module.php:7` |
| `/pm/list` | `list.php` | – | `extension/ezpm/modules/pm/module.php:11` |
| `/pm/list_inbox` | `list.php` | – | `extension/ezpm/modules/pm/module.php:15` |
| `/pm/list_sent` | `list_sent.php` | – | `extension/ezpm/modules/pm/module.php:19` |
| `/pm/list_drafts` | `list_drafts.php` | – | `extension/ezpm/modules/pm/module.php:23` |
| `/pm/create` | `create.php` | – | `extension/ezpm/modules/pm/module.php:27` |
| `/pm/reply` | `reply.php` | – | `extension/ezpm/modules/pm/module.php:31` |
| `/pm/edit` | `edit.php` | – | `extension/ezpm/modules/pm/module.php:35` |
| `/pm/add` | `add.php` | – | `extension/ezpm/modules/pm/module.php:39` |
| `/pm/action` | `action.php` | – | `extension/ezpm/modules/pm/module.php:46` |
| `/pm/blacklist` | `blacklist.php` | – | `extension/ezpm/modules/pm/module.php:59` |
| `/pm/contacts` | `contact_list.php` | – | `extension/ezpm/modules/pm/module.php:63` |

## Richtlinienfunktionen (6)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `pm/list` | – | `extension/ezpm/modules/pm/module.php:67` |
| `pm/blacklist` | – | `extension/ezpm/modules/pm/module.php:68` |
| `pm/contacts` | – | `extension/ezpm/modules/pm/module.php:69` |
| `pm/send_message` | – | `extension/ezpm/modules/pm/module.php:70` |
| `pm/add_contact` | – | `extension/ezpm/modules/pm/module.php:71` |
| `pm/stats` | – | `extension/ezpm/modules/pm/module.php:72` |

## Fetch-Funktionen (4)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'pm', 'view_message' )` | `eZPm::fetchMessage()` | `extension/ezpm/modules/pm/function_definition.php:11` |
| `fetch( 'pm', 'list' )` | `eZPm::fetchMessageList()` | `extension/ezpm/modules/pm/function_definition.php:19` |
| `fetch( 'pm', 'messages_stats' )` | `eZPm::messagesStats()` | `extension/ezpm/modules/pm/function_definition.php:36` |
| `fetch( 'pm', 'check_contact' )` | `eZContact::checkIsOnContactList()` | `extension/ezpm/modules/pm/function_definition.php:45` |
