---
title: "Im Inneren des Kerns: Berechtigungen und persistente Objekte"
audience: developer
category: books
order: 500
summary: Wie ein Modul über den Zugriff entscheidet (hasAccessTo, die Liste der Einschränkungen, Fehlercodes) und wie Kernel-Klassen sich über eZPersistentObject selbst speichern, mit den heutigen statischen Aufrufen und Links auf die Entwicklerseiten.
keywords: Berechtigungen, Richtlinie, Einschränkung, Fehlercodes, persistente Objekte, Buch Learning, permissions, policy, limitation, hasAccessTo, eZUser, error codes, eZPersistentObject, definition, fields, function_attributes, fetchObject, fetchObjectList, store, Learning book
type: help
---
# Im Inneren des Kerns: Berechtigungen und persistente Objekte

Kapitel 4 des Buchs blickt in die Kernel-Klassen (`kernel/classes`), damit Sie Erweiterungen schreiben können. Zwei
Gedanken tragen das Kapitel. Beide sind in ihrer Form unverändert; das PHP um sie herum ist heute modern.

## Berechtigungen im Code

Eine Rolle enthält Richtlinien; eine Richtlinie nennt ein Modul und eine Funktion (oder alle) mit Einschränkungen
([Rollen und Richtlinien](rollen-und-richtlinien-schritt-fuer-schritt.md)). Ein Modul erklärt in seiner Definitionsdatei, welche
Einschränkungen jede seiner Funktionen versteht:

```php
$FunctionList['read'] = array( 'Class' => $ClassID, 'Section' => $SectionID, 'Node' => $Node, 'Subtree' => $Subtree );
```

(die echte `kernel/content/module.php` führt mehr auf: Besitzer, Status, Sprache.) Die Prüfung, die das Hauptskript
ausführt:

```php
$user = eZUser::currentUser();
$access = $user->hasAccessTo( $module->attribute( 'name' ), $functionName );
```

`hasAccessTo()` liefert ein Array, dessen `accessWord` `yes`, `no` oder `limited` ist. Bei `yes` hat der Benutzer volle
Rechte; bei `no` wird der Zugriff verweigert; bei `limited` kommen die Richtlinien als Liste von Einschränkungen zurück,
und die Klassen, die Inhalte abrufen (etwa `eZContentObjectTreeNode`), wandeln sie in SQL um, sodass nur die erlaubten
Objekte abgerufen werden. Das Beispiel des Buchs, in dem `subTreeCount()` für eine Klasseneinschränkung
`contentclass_id in (...)` aufbaut, zeigt immer noch, wie es gemacht wird. Beachten Sie das `&` und `include_once` des
Buchs: Referenzen und Includes sind verschwunden (das Autoloading findet die Klasse).

Häufige Fehlercodes sind heute Konstanten von `eZError`: `KERNEL_ACCESS_DENIED` (1), `KERNEL_NOT_FOUND` (2),
`KERNEL_NOT_AVAILABLE` (3), `KERNEL_MODULE_NOT_FOUND` (20), `KERNEL_MODULE_VIEW_NOT_FOUND` (21) und weitere.

Lesen Sie als Nächstes: [Richtlinienfunktionen und Zugriff](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md).

## Persistente Objekte

`eZPersistentObject` ist die dünne Schicht zwischen dem Code und der Datenbank. Eine Klasse, die davon erbt, beschreibt
ihre Tabelle in einer Funktion `definition()` und erhält `store()`, `sync()`, `fetchObject()`, `fetchObjectList()` und
`removeObject()` umsonst. Das Array, das `definition()` liefert, enthält:

| Schlüssel | Bedeutung |
|---|---|
| `fields` | Datenbankfeld => das Objektattribut: sein Name, Typ (`integer`, `string`...), Standardwert und ob es Pflicht ist |
| `keys` | die Felder des Primärschlüssels |
| `function_attributes` | Template-Attribute, die Methoden aufrufen (so ruft `$object.owner` die Methode `owner()` auf) |
| `increment_key` | das Feld mit automatischer Hochzählung |
| `class_name`, `name` | die PHP-Klasse und die Tabelle |
| `sort`, `grouping`, `set_functions` | Standardreihenfolge, Gruppierung, Setter |

Ein Objekt wird über seine Attribute verwendet: `$object->attribute( 'name' )`, `setAttribute( 'name', 'x' )`, `store()`.
Dieselben Namen funktionieren in Templates; deshalb fühlen sich die Template-Sprache und die PHP-API ähnlich an.
Abrufen:

```php
$group = eZPersistentObject::fetchObject( eZContentClassGroup::definition(), null, array( 'name' => $name ) );
```

Lesen Sie als Nächstes: [Persistente Objekte und Tabellen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/persistente-objekte-und-tabellen.md)
und [Datenbankschema und DBA-Dateien](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md).
Wenn Sie SQL von Hand schreiben, maskieren Sie Zeichenketten mit `escapeString()` des Datenbankobjekts und wandeln Sie
Zahlen ausdrücklich um.

Weiter: [Im Inneren des Kerns: Klassen, Workflows, Benachrichtigungen, Suche](im-inneren-des-kerns-klassen-und-dienste.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 4, „A Glimpse Inside the Core“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: gekürzt; PHP-4-Code durch aktuelle Aufrufe ersetzt; Klassennamen, Einstellungen und Konstanten gegen Exponential 6 geprüft.
