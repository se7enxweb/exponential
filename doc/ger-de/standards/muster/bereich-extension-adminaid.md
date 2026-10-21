---
title: Muster in extension/AdminAid
audience: developer
category: standards
order: 400
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/AdminAid mit den Entwurfsmustern.
keywords: extension/AdminAid, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/AdminAid

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/AdminAid` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (1)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezAdminAidInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/AdminAid/ezinfo.php:27` | – |

## Modulansichten (6)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/aid/user_switch` | `user_switch.php` | user_switch | `extension/AdminAid/modules/aid/module.php:18` |
| `/aid/object_list` | `object_list.php` | object_view | `extension/AdminAid/modules/aid/module.php:19` |
| `/aid/object_view` | `object_view.php` | object_view | `extension/AdminAid/modules/aid/module.php:20` |
| `/aid/class_translation` | `class_translation.php` | class_translate | `extension/AdminAid/modules/aid/module.php:21` |
| `/aid/class_attribute_select` | `class_attribute_select.php` | class_translate | `extension/AdminAid/modules/aid/module.php:22` |
| `/aid/search` | `search.php` | search | `extension/AdminAid/modules/aid/module.php:23` |

## Richtlinienfunktionen (4)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `aid/user_switch` | – | `extension/AdminAid/modules/aid/module.php:27` |
| `aid/object_view` | – | `extension/AdminAid/modules/aid/module.php:28` |
| `aid/class_translate` | – | `extension/AdminAid/modules/aid/module.php:29` |
| `aid/search` | – | `extension/AdminAid/modules/aid/module.php:30` |

## INI-Plugin-Punkte (18)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SearchDatabase] ClassList[]` | `eZContentObject` | `extension/AdminAid/settings/aid.ini.append.php:26` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectTreeNode` | `extension/AdminAid/settings/aid.ini.append.php:30` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectAttribute` | `extension/AdminAid/settings/aid.ini.append.php:34` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectVersion` | `extension/AdminAid/settings/aid.ini.append.php:38` |
| `[SearchDatabase] ClassList[]` | `eZContentClass` | `extension/AdminAid/settings/aid.ini.append.php:41` |
| `[SearchDatabase] ClassList[]` | `eZContentClassAttribute` | `extension/AdminAid/settings/aid.ini.append.php:45` |
| `[SearchDatabase] ClassList[]` | `eZUser` | `extension/AdminAid/settings/aid.ini.append.php:49` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectState` | `extension/AdminAid/settings/aid.ini.append.php:53` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateGroup` | `extension/AdminAid/settings/aid.ini.append.php:57` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateLanguage` | `extension/AdminAid/settings/aid.ini.append.php:61` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateGroupLanguage` | `extension/AdminAid/settings/aid.ini.append.php:65` |
| `[SearchDatabase] ClassList[]` | `eZContentLanguage` | `extension/AdminAid/settings/aid.ini.append.php:69` |
| `[SearchDatabase] ClassList[]` | `eZContentBrowseBookmark` | `extension/AdminAid/settings/aid.ini.append.php:73` |
| `[SearchDatabase] ClassList[]` | `eZImageFile` | `extension/AdminAid/settings/aid.ini.append.php:77` |
| `[SearchDatabase] ClassList[]` | `eZInformationCollectionAttribute` | `extension/AdminAid/settings/aid.ini.append.php:81` |
| `[SearchDatabase] ClassList[]` | `eZNodeAssignment` | `extension/AdminAid/settings/aid.ini.append.php:85` |
| `[SearchDatabase] ClassList[]` | `eZURL` | `extension/AdminAid/settings/aid.ini.append.php:88` |
| `[SearchDatabase] ClassList[]` | `eZWorkflow` | `extension/AdminAid/settings/aid.ini.append.php:96` |
