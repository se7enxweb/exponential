---
title: eZContentObjectTreeNode
audience: developer
category: extension-development
order: 70
summary: Die Knotenmethoden, die Erweiterungen verwenden - fetch(), fetchByURLPath(), subTreeByNodeID() und seine Parameter, subTreeCountByNodeID(), object(), Pfad, URL-Alias, Zugriff - mit einem getesteten Beispiel.
keywords: Knoten, Inhaltsbaum, Teilbaum, Listen, Filter, Sortierung, Seitenaufteilung, eZContentObjectTreeNode, node, fetch, subTreeByNodeID, subTreeCountByNodeID, ClassFilterType, ClassFilterArray, AttributeFilter, SortBy, Depth, Limit, url_alias, pathArray, children, canRead
type: help
---
# eZContentObjectTreeNode

Teil 8, Referenz 7. Ein Knoten (`kernel/classes/ezcontentobjecttreenode.php`) ist ein Standort eines Inhaltsobjekts
im Inhaltsbaum. Listen, Menüs und Pfade bestehen aus Knoten. Der Knoten mit der ID 2 ist üblicherweise die Wurzel der
Inhalte der Website.

## Einen Knoten abrufen

| Methode (statisch) | Gibt zurück |
|---|---|
| `fetch( $nodeID = false, $lang = false, $asObject = true, $conditions = false )` | den Knoten oder `null` |
| `fetchByURLPath( $pathString, $asObject = true )` | den Knoten unter einem URL-Pfad |
| `fetchByContentObjectID( $contentObjectID, $asObject = true, $contentObjectVersion = false )` | alle Knoten eines Objekts |
| `fetchNode( $contentObjectID, $parentNodeID )` | den Knoten eines Objekts unter einem bestimmten Elternknoten |

## Listen abrufen: subTreeByNodeID()

```php
static function subTreeByNodeID( $params = false, $nodeID = 0 )
static function subTreeCountByNodeID( $params, $nodeID )
```

Die Parameter, in einem Array:

| Schlüssel | Bedeutung |
|---|---|
| `Depth`, `DepthOperator` | wie tief: `1` mit `eq` für direkte Kinder; `lt`, `le` und andere für Bereiche |
| `ClassFilterType`, `ClassFilterArray` | `include` oder `exclude`, mit Klassenbezeichnern |
| `AttributeFilter` | Bedingungen an Attribute, zum Beispiel `array( array( 'published', '>', $since ) )` |
| `SortBy` | `array( 'published', false )` (false = absteigend) oder eine Liste solcher Paare |
| `Offset`, `Limit` | Seitenaufteilung |
| `Language`, `OnlyTranslated` | Übersetzungen |
| `IgnoreVisibility` | verborgene Knoten einschließen |
| `MainNodeOnly` | nur Hauptstandorte |
| `LoadDataMap` | die Attribute sofort laden |
| `Limitation` | `array()`, um die Zugriffsprüfung zu überspringen (nur, wenn Sie den Zugriff selbst prüfen) |
| `AsObject` | `false` für einfache Arrays |

Zugriffsrichtlinien und Sichtbarkeit gelten, solange Sie sie nicht abschalten; das ist für alles, was Benutzern
angezeigt wird, genau richtig. Verwenden Sie für die Seitenaufteilung immer das passende `subTreeCountByNodeID()` mit
denselben Filtern.

```php
<?php
$params = array( 'Depth' => 1, 'DepthOperator' => 'eq',
                 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'article', 'folder' ),
                 'SortBy' => array( 'published', false ),
                 'Offset' => 0, 'Limit' => 10 );
$children = eZContentObjectTreeNode::subTreeByNodeID( $params, 2 );
$total = eZContentObjectTreeNode::subTreeCountByNodeID( $params, 2 );
foreach ( $children as $child )
{
    echo $child->attribute( 'node_id' ), ' ', $child->getName(), ' /', $child->attribute( 'url_alias' ), "\n";
}
```

## Einen Knoten lesen

| Methode oder Attribut | Gibt zurück |
|---|---|
| `object()` | das Inhaltsobjekt |
| `dataMap()` | die Attribute des Objekts |
| `getName( $language = false )` | den Namen |
| `attribute( 'url_alias' )`, `urlAlias()` | den URL-Pfad ohne führenden Schrägstrich |
| `fetchParent()` | den Elternknoten |
| `pathArray()` | die Knoten-IDs von der Wurzel bis zu diesem Knoten |
| `children()`, `childrenCount( $checkPolicies = true )` | direkte Kinder |
| `isMain()` | ob dies der Hauptknoten des Objekts ist |
| `canRead()`, `canEdit()`, `canCreate()`, `canRemove()` | Zugriff des aktuellen Benutzers |

## In Templates

Dieselben Listen liefert die Fetch-Funktion `fetch( 'content', 'list', hash( ... ) )` mit denselben Parametern in
Kleinschreibung (`parent_node_id`, `class_filter_type`, `sort_by`, `limit`); siehe
[Eingebaute Fetch-Funktionen](referenz-der-fetch-funktionen.md).

## Verwandte Seiten

- Zurück: [eZContentObject](ezcontentobject.md)
- Weiter: [eZContentClass und ihre Attribute](ezcontentclass.md)
- [Fetch für Builder](../teil-2-builder/fetch-fuer-builder.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
