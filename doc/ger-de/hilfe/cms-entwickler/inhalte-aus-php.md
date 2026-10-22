---
title: Inhalte aus PHP
audience: cms-developer
category: cms-development
order: 26
summary: Für CMS-Entwickler, die Inhalte in PHP-Skripten, Importern und Integrationen mit der echten Kernel-API lesen und schreiben. Die Seite behandelt das Ausführen eines Skripts mit geladenem Kernel, das Abrufen von Knoten und Listen, das Lesen von Attributen, das Anlegen und Aktualisieren von Objekten, sodass Workflows, Caches und Suche Schritt halten, und den Benutzer, unter dem ein Skript läuft.
keywords: PHP, Kernel-API, eZContentObjectTreeNode, eZContentObject, subTreeByNodeID, dataMap, fetchByRemoteID, eZContentFunctions, createAndPublishObject, updateAndPublishObject, ezexec, eZScript, eZCLI, Remote-ID, Importskript, aktueller Benutzer, kernel API, remote id, import script, current user
type: help
---
# Inhalte aus PHP

Diese Seite richtet sich an Entwickler, die Inhalte in PHP brauchen: einen Importer, einen nächtlichen Abgleich mit
einem anderen System, einen Bericht, ein eigenes Modul. Der Kernel hat für jedes davon einen unterstützten Weg, und ihn
zu verwenden hält Versionen, Workflows, Suche und Caches korrekt. Schreiben Sie nie direkt in die Inhaltstabellen.

## Inhalt

1. [Ein Skript mit geladenem Kernel ausführen](#ein-skript-mit-geladenem-kernel-ausführen)
2. [Einen Knoten und sein Objekt lesen](#einen-knoten-und-sein-objekt-lesen)
3. [Listen und Teilbäume](#listen-und-teilbäume)
4. [Attributwerte lesen](#attributwerte-lesen)
5. [Inhalte anlegen](#inhalte-anlegen)
6. [Inhalte aktualisieren](#inhalte-aktualisieren)
7. [Der Benutzer, unter dem ein Skript läuft](#der-benutzer-unter-dem-ein-skript-läuft)
8. [Verschieben, Verbergen und Entfernen](#verschieben-verbergen-und-entfernen)
9. [Große Läufe](#große-läufe)

## Ein Skript mit geladenem Kernel ausführen

Am schnellsten führen Sie eine beliebige PHP-Datei mit erledigtem Bootstrap aus, vom Hauptverzeichnis der Installation:

```bash
php bin/php/ezexec.php extension/exp_mysite/scripts/report.php
```

Für ein Skript, das Sie behalten, mit Optionen, Hilfe und Fortschrittsausgabe, schreiben Sie ein `eZScript`-Skript oder
einen Konsolenbefehl in Ihrer Erweiterung ([eZScript und eZCLI](../erweiterungsentwicklung/teil-8-referenz/ezscript-und-ezcli.md),
[CLI-Skripte und Konsolenbefehle](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md)).
Die Lektion [Ein Skript mit geladenem Kernel](../trainer/entwickler/modul-01-die-entwicklungsinstallation/ein-skript-mit-geladenem-kernel.md)
führt durch ein erstes.

## Einen Knoten und sein Objekt lesen

```php
<?php
$cli = eZCLI::instance();
$node = eZContentObjectTreeNode::fetch( 2 );
if ( !$node instanceof eZContentObjectTreeNode )
{
    $cli->error( 'Node 2 was not found' );
    return;
}
$object = $node->object();
$cli->output( $node->attribute( 'name' ) . ' (' . $node->attribute( 'class_identifier' ) . ')' );
$cli->output( 'Object ' . $object->attribute( 'id' ) . ', main node ' . $object->attribute( 'main_node_id' ) );

$imported = eZContentObject::fetchByRemoteID( 'exp-import-event-42' );
```

`eZContentObjectTreeNode::fetch()` nimmt eine Knoten-ID; `eZContentObjectTreeNode::fetchByRemoteID()`,
`fetchByURLPath()` und `eZContentObject::fetch()` / `fetchByRemoteID()` finden Inhalte über andere Schlüssel
(`kernel/classes/ezcontentobjecttreenode.php`, `kernel/classes/ezcontentobject.php`).

## Listen und Teilbäume

`subTreeByNodeID()` ist die PHP-Seite der Fetches `list` und `tree`, mit den Parametern in CamelCase:

```php
<?php
$params = array( 'Depth' => 1, 'DepthOperator' => 'eq',
                 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'exp_event' ),
                 'SortBy' => array( 'published', false ),
                 'Offset' => 0, 'Limit' => 20, 'LoadDataMap' => true );
$events = eZContentObjectTreeNode::subTreeByNodeID( $params, $eventsFolderNodeID );
$total = eZContentObjectTreeNode::subTreeCountByNodeID( $params, $eventsFolderNodeID );
```

Weitere Parameter: `AttributeFilter`, `ExtendedAttributeFilter`, `MainNodeOnly`, `IgnoreVisibility`, `Language`,
`OnlyTranslated`, `Limitation`, `AsObject`. Zugriffsrichtlinien und Sichtbarkeit gelten wie für den aktuellen Benutzer,
sofern Sie sie nicht abschalten; `'Limitation' => array()` überspringt die Zugriffsprüfung und ist nur für Code gedacht,
der den Zugriff selbst prüft ([eZContentObjectTreeNode](../erweiterungsentwicklung/teil-8-referenz/ezcontentobjecttreenode.md)).

## Attributwerte lesen

```php
<?php
$dataMap = $node->dataMap();                 // attribute identifier => eZContentObjectAttribute
if ( isset( $dataMap['title'] ) && $dataMap['title']->hasContent() )
{
    $title = $dataMap['title']->content();   // the datatype's value
    $asString = $dataMap['title']->toString(); // the same as a string, in the import format
}
```

`content()` gibt zurück, was der Datentyp Templates als `.content` liefert (eine Zeichenkette, ein `eZDateTime`, einen
Bild-Handler, ein Array mit `relation_list` ...). `toString()` gibt das Format zurück, das `fromString()` liest; das
brauchen Sie für Exporte ([Die eZDataType-API](../erweiterungsentwicklung/teil-8-referenz/ezdatatype-api.md)).

## Inhalte anlegen

`eZContentFunctions::createAndPublishObject()` (`kernel/classes/ezcontentfunctions.php`) legt das Objekt, seine erste
Version und seinen Knoten an und führt die Veröffentlichungsoperation aus:

```php
<?php
$object = eZContentFunctions::createAndPublishObject( array(
    'class_identifier' => 'exp_event',
    'parent_node_id'   => $eventsFolderNodeID,
    'remote_id'        => 'exp-import-event-42',
    'attributes'       => array(
        'title'      => 'Opening of the new hall',
        'intro'      => 'Doors open at ten.',
        'start_date' => (string)mktime( 10, 0, 0, 11, 2, 2026 ),
    ),
) );
if ( !$object instanceof eZContentObject )
{
    eZDebug::writeError( 'Could not create the event', __METHOD__ );
}
```

Werte sind Zeichenketten im `fromString()`-Format jedes Datentyps: Text, wie er ist, `1`/`0` für ein
Kontrollkästchen, ein Zeitstempel für Datumsangaben, ein Dateipfad für Bilder und Dateien (mit `storage_dir`), das
gespeicherte XML für Rich Text. Setzen Sie bei importierten Inhalten immer `remote_id`, damit der nächste Lauf
aktualisiert statt zu verdoppeln
([Inhalte aus PHP anlegen, veröffentlichen und ändern](../erweiterungsentwicklung/teil-8-referenz/inhaltsfunktionen-aus-php.md)).

## Inhalte aktualisieren

```php
<?php
$object = eZContentObject::fetchByRemoteID( 'exp-import-event-42' );
if ( $object instanceof eZContentObject )
{
    eZContentFunctions::updateAndPublishObject( $object, array(
        'attributes' => array( 'intro' => 'Doors open at eleven.' ),
    ) );
}
```

Nur die Attribute, die Sie übergeben, ändern sich. `language` fügt eine Übersetzung desselben Objekts hinzu oder
aktualisiert sie ([Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md#übersetzungen-aus-code)). Jeder Aufruf
legt eine neue Version an und veröffentlicht sie, mit ihren Workflows und Benachrichtigungen; übergeben Sie `false` als
drittes Argument, um ohne Benachrichtigungs-Events zu veröffentlichen.

## Der Benutzer, unter dem ein Skript läuft

Ein Kommandozeilenskript läuft als anonymer Benutzer, sofern es keinen setzt, und der anonyme Benutzer darf meist keine
Inhalte anlegen. Verwenden Sie ein eigenes Importkonto mit genau den Rechten, die der Import braucht, keinen
Administrator:

```php
<?php
$user = eZUser::fetchByName( 'exp_importer' );
if ( $user instanceof eZUser )
{
    eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
}
```

Die angelegten Objekte erhalten diesen Benutzer als Eigentümer.

## Verschieben, Verbergen und Entfernen

| Aufgabe | Aufruf |
|---|---|
| Einen Knoten verschieben | `eZContentObjectTreeNodeOperations::move( $nodeID, $newParentNodeID )` |
| Einen Teilbaum verbergen oder anzeigen | `eZContentObjectTreeNode::hideSubTree( $node )`, `unhideSubTree( $node )` |
| Knoten entfernen (standardmäßig in den Papierkorb) | `eZContentObjectTreeNode::removeSubtrees( $nodeIDs, $moveToTrash = true, $infoOnly = false )` |

`removeSubtrees()` mit `$infoOnly = true` meldet, was entfernt würde: Verwenden Sie es für einen Probelauf.

## Große Läufe

- Leeren Sie zwischendurch den Objekt-Cache im Speicher (`eZContentObject::clearCache()`), sonst wächst der Speicher mit
  jedem Objekt.
- Bieten Sie ein `--dry-run` an, das meldet, was angelegt oder geändert würde.
- Führen Sie es zuerst an einer Kopie aus, dann live in einer ruhigen Stunde; große Änderungen können auch als
  Inhaltsjobs im Hintergrund laufen ([Inhaltsjobs](../../bc/6.0/inhaltsjobs.md)).
- Für die vorhandenen Importwerkzeuge lesen Sie [Inhalte importieren](inhalte-importieren.md), bevor Sie Code schreiben.

## Verwandte Seiten

- [Lektion 4.3: Inhalte aus PHP lesen und schreiben](../trainer/entwickler/modul-04-inhaltsklassen-und-datentypen/inhalte-aus-php.md)
- [eZContentObject](../erweiterungsentwicklung/teil-8-referenz/ezcontentobject.md)
- [Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
