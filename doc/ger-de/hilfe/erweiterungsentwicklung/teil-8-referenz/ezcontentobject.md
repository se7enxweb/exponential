---
title: eZContentObject
audience: developer
category: extension-development
order: 60
summary: Die Methoden von eZContentObject, die Erweiterungen verwenden - fetch(), fetchByRemoteID(), dataMap(), mainNode(), assignedNodes(), Versionen, Zugriffsprüfungen - mit Signaturen und einem getesteten Beispiel.
keywords: Inhaltsobjekt, Remote-ID, Versionen, Zugriffsprüfung, Knoten, eZContentObject, fetch, fetchByRemoteID, fetchByNodeID, dataMap, mainNode, mainNodeID, assignedNodes, currentVersion, contentClassIdentifier, canRead, canEdit, checkAccess, remoteID
type: help
---
# eZContentObject

Teil 8, Referenz 6. Ein Inhaltsobjekt (`kernel/classes/ezcontentobject.php`) ist ein einzelner Inhalt: ein Artikel,
ein Bild, ein Benutzer. Es hat Versionen und Übersetzungen; jede veröffentlichte Version hat Attribute, eines je
Attribut seiner Inhaltsklasse. Im Inhaltsbaum erscheint es über einen oder mehrere Knoten
([eZContentObjectTreeNode](ezcontentobjecttreenode.md)). Wie Sie Objekte anlegen und veröffentlichen, steht in
[Inhaltsfunktionen aus PHP](inhaltsfunktionen-aus-php.md).

## Abrufen

| Methode (statisch) | Gibt zurück |
|---|---|
| `fetch( $id, $asObject = true )` | das Objekt oder `null` (und einen Fehler in der Debug-Ausgabe), wenn es keines gibt |
| `fetchByRemoteID( $remoteID, $asObject = true )` | das Objekt mit dieser Remote-ID |
| `fetchByNodeID( $nodeID, $asObject = true )` | das Objekt eines Knotens |
| `fetchIDArray( $idArray, $asObject = true, $lang = false )` | mehrere Objekte auf einmal |
| `fetchList( $asObject = true, $conditions = null, $offset = false, $limit = false )` | Objekte, die Bedingungen erfüllen |

Bevorzugen Sie für Objekte, auf die sich Ihre Erweiterung in Einstellungen oder Code bezieht, die Remote-ID gegenüber
der numerischen ID: Remote-IDs überstehen einen Export und Import in eine andere Installation, numerische IDs nicht.

## Lesen

| Methode oder Attribut | Gibt zurück |
|---|---|
| `attribute( 'name' )`, `name( $version = false, $lang = false )` | den Namen des Objekts |
| `attribute( 'id' )`, `remoteID()` | die IDs |
| `contentClassIdentifier()`, `contentClass()` | den Bezeichner der Klasse oder das Klassenobjekt |
| `dataMap()` | die Attribute der aktuellen Version, nach Attributbezeichner geordnet |
| `fetchDataMap( $version = false, $language = false )` | dasselbe für eine andere Version oder Sprache |
| `mainNodeID()`, `mainNode()` | den Hauptstandort |
| `assignedNodes( $asObject = true, ... )` | alle Standorte |
| `currentVersion( $asObject = true )`, `version( $version, $asObject = true )` | Versionen |
| `relatedObjects( ... )`, `reverseRelatedObjectList( ... )` | Beziehungen |

Jeder Eintrag von `dataMap()` ist ein `eZContentObjectAttribute`: Sein Wert ist `attribute( 'content' )` (was das ist,
entscheidet der Datentyp), `hasContent()` sagt, ob es ausgefüllt ist, `attribute( 'data_type_string' )` nennt den
Datentyp.

## Ein getestetes Beispiel

```php
<?php
$object = eZContentObject::fetchByNodeID( 2 );
if ( $object instanceof eZContentObject && $object->canRead() )
{
    $dataMap = $object->dataMap();
    echo $object->contentClassIdentifier(), ': ', $object->attribute( 'name' ), "\n";
    if ( isset( $dataMap['short_description'] ) && $dataMap['short_description']->hasContent() )
    {
        echo 'has a short description', "\n";
    }
    echo 'main node ', $object->mainNodeID(), "\n";
}
```

Nur lesend mit geladenem Kernel auf einer Demo-Installation ausgeführt, gab dies die Klasse `folder`, den Namen und
den Hauptknoten 2 aus.

## Zugriff

| Methode | Prüft |
|---|---|
| `canRead()` | ob der aktuelle Benutzer es lesen darf |
| `canEdit( ... )` | ob der aktuelle Benutzer es bearbeiten darf |
| `checkAccess( $functionName, ... )` | jede beliebige Funktion der Inhaltsrichtlinien, mit Einschränkungen |

Prüfen Sie den Zugriff in Ihren eigenen Views, bevor Sie ein Objekt anzeigen oder ändern; das Abrufen prüft ihn nicht.
Siehe [eZUser und Zugriffsprüfungen](ezuser-und-zugriff.md).

## In Templates

Dasselbe Objekt ist in Knoten-Templates `$node.object`; seine Attribute sind `$node.data_map.<identifier>` und werden
mit `attribute_view_gui` angezeigt. Siehe [Eingebaute Template-Funktionen](referenz-der-template-funktionen.md).

## Verwandte Seiten

- Zurück: [eZPersistentObject](ezpersistentobject.md)
- Weiter: [eZContentObjectTreeNode](ezcontentobjecttreenode.md)
- [eZContentClass und ihre Attribute](ezcontentclass.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
