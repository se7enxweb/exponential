---
title: Inhalte aus PHP anlegen, veröffentlichen und ändern
audience: developer
category: extension-development
order: 220
summary: Inhalte auf dem unterstützten Weg anlegen, aktualisieren, verschieben, verbergen und entfernen - createAndPublishObject(), updateAndPublishObject() und die Knotenoperationen -, damit Workflows, Caches und die Suche im Gleichschritt bleiben.
keywords: Inhalte anlegen, veröffentlichen, Import, Knoten verschieben, verbergen, entfernen, Inhalte aus PHP, eZContentFunctions, createAndPublishObject, updateAndPublishObject, fromString, publish, move, removeSubtrees, hideSubTree, eZContentObjectTreeNodeOperations, eZContentOperationCollection, import, content from PHP
type: help
---
# Inhalte aus PHP anlegen, veröffentlichen und ändern

Teil 8, Referenz 18. Importer, Integrationen und manche Cronjobs legen Inhalte an oder ändern sie. Tun Sie das immer
über die Funktionen des Kernels: Sie erzeugen Versionen, führen die Veröffentlichungsoperation aus (mit ihren
Workflows und Triggern), leeren die View-Caches und aktualisieren den Suchindex. Direktes Schreiben in die
Inhaltstabellen tut nichts davon und hinterlässt die Website in einem inkonsistenten Zustand.

## Anlegen: eZContentFunctions::createAndPublishObject()

```php
static function createAndPublishObject( $params, $notify = true )
```

`kernel/classes/ezcontentfunctions.php`. Die Parameter laut ihrer Dokumentation:

| Schlüssel | Bedeutung |
|---|---|
| `parent_node_id` | wohin das Objekt kommt |
| `class_identifier` | die Klasse, über ihren Bezeichner (die ID wird nicht akzeptiert) |
| `creator_id` | die Benutzerobjekt-ID des Erstellers; der aktuelle Benutzer, wenn nicht gesetzt |
| `attributes` | Attributbezeichner => Wert, im Format von `fromString()` des Datentyps |
| `remote_id` | optional: Ihre eigene, stabile ID für das Objekt |
| `section_id` | optional |
| `storage_dir` | optional: ein Verzeichnis für Dateiattribute, die per Dateiname angegeben werden |

`$notify = false` veröffentlicht ohne Benachrichtigungsereignisse. Die Funktion gibt das veröffentlichte
`eZContentObject` zurück oder `false`.

```php
<?php
$params = array(
    'class_identifier' => 'folder',
    'parent_node_id' => 2,
    'remote_id' => 'myext-import-folder-42',
    'attributes' => array(
        'name' => 'Imported items',
        'short_name' => 'Imported',
    ),
);
$object = eZContentFunctions::createAndPublishObject( $params );
if ( !$object instanceof eZContentObject )
{
    eZDebug::writeError( 'Could not create the folder', __METHOD__ );
}
```

Werte sind Zeichenketten im `fromString()`-Format des jeweiligen Datentyps: einfacher Text für `ezstring`, `1`/`0`
für `ezboolean`, ein Zeitstempel für Datumsangaben, ein Dateipfad für Bilder und Dateien und das gespeicherte XML für
`ezxmltext`. Lesen Sie im Zweifel `fromString()` des Datentyps ([Die eZDataType-API](ezdatatype-api.md)).

Verwenden Sie `remote_id` für alles, was Sie importieren: Der nächste Lauf findet das Objekt mit
`eZContentObject::fetchByRemoteID()` und aktualisiert es, statt ein Duplikat anzulegen.

## Aktualisieren: updateAndPublishObject()

```php
public static function updateAndPublishObject( eZContentObject $object, array $params, $notify = true )
```

`$params` nimmt `attributes` (nur die zu ändernden) und optional `remote_id`, `section_id` und `language` auf. Die
Funktion erzeugt eine neue Version, setzt die Attribute und veröffentlicht sie. Sie gibt `true` oder `false` zurück.

```php
<?php
$object = eZContentObject::fetchByRemoteID( 'myext-import-folder-42' );
if ( $object instanceof eZContentObject )
{
    eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => array( 'short_name' => 'Imports' ) ) );
}
```

## Verschieben, verbergen, entfernen

| Aufgabe | Aufruf |
|---|---|
| Einen Knoten verschieben | `eZContentObjectTreeNodeOperations::move( $nodeID, $newParentNodeID )` |
| Einen Teilbaum verbergen oder anzeigen | `eZContentObjectTreeNode::hideSubTree( $node )`, `unhideSubTree( $node )` |
| Knoten entfernen (standardmäßig in den Papierkorb) | `eZContentObjectTreeNode::removeSubtrees( $deleteIDArray, $moveToTrash = true, $infoOnly = false )` |
| Dasselbe über die Operationsschicht (mit Triggern) | `eZContentOperationCollection::moveNode()`, `removeNodes()`, `deleteObject()`, `changeHideStatus()`, `updateSection()` |

`removeSubtrees( ..., $infoOnly = true )` meldet nur, was entfernt würde; verwenden Sie es für einen Probelauf.

## Als welcher Benutzer

Inhalte werden als der aktuelle Benutzer angelegt. In einem CLI-Skript ist das der anonyme Benutzer, sofern Sie
keinen anderen setzen; der anonyme Benutzer darf meist keine Inhalte anlegen. Führen Sie Importe als eigenen
Benutzer aus, der genau die Rechte hat, die der Import braucht, und machen Sie diesen Benutzer im Skript zum
aktuellen Benutzer, statt einen Administrator zu verwenden:

```php
<?php
$user = eZUser::fetchByName( 'importer' );
if ( $user instanceof eZUser )
{
    eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
}
```

## In Transaktionen und Schleifen

`createAndPublishObject()` verwaltet seine eigene Transaktion. In einer Schleife über viele Elemente:

- leeren Sie ab und zu den Objekt-Cache im Speicher (`eZContentObject::clearCache()`), sonst wächst der
  Speicherverbrauch mit jedem Objekt;
- geben Sie den Fortschritt mit `eZScript::iterate()` aus ([eZScript und eZCLI](ezscript-und-ezcli.md));
- bieten Sie ein `--dry-run` an, das meldet, was angelegt würde.

## Verwandte Seiten

- Zurück: [Die eZWorkflowType-API](ezworkflowtype-api.md)
- Weiter: [Referenz der ezjscore-Serverfunktionen](ezjscore-serverfunktionen-referenz.md)
- [eZContentObject](ezcontentobject.md), [eZContentObjectTreeNode](ezcontentobjecttreenode.md)
- [Inhaltsaufträge](../../../bc/6.0/inhaltsjobs.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
