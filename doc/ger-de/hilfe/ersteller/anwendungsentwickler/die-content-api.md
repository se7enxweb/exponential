---
title: Die Content-API
audience: app-creator, developer
category: app-creation
order: 190
summary: Inhalte aus PHP lesen - eZContentObject, eZContentObjectTreeNode und eZContentObjectAttribute, Abruf nach ID, Remote-ID und URL-Alias, Teilbaumabfragen mit Filtern, Data Maps, Versionen und Sprachen sowie Lesen unter Beachtung der Rechte.
keywords: Content-API, Inhaltsobjekt, Knoten, Attribut, Data Map, Versionen, Sprachen, Anwendungsentwickler, content API, eZContentObject, eZContentObjectTreeNode, eZContentObjectAttribute, data map, subTreeByNodeID, fetchByRemoteID, fetchByURLPath, versions, languages, PHP, app creator
type: help
---
# Die Content-API

Diese Seite richtet sich an Anwendungsentwickler, die Inhalte in PHP lesen: in Ansichten, Diensten, Befehlen und
Cronjobs. Die Klassen sind `eZContentObject`, `eZContentObjectTreeNode` und `eZContentObjectAttribute` des Kernels in
`kernel/classes/`.

## Inhalt

1. [Objekte, Knoten und Attribute](#objekte-knoten-und-attribute)
2. [Ein einzelnes Element abrufen](#ein-einzelnes-element-abrufen)
3. [Listen abrufen](#listen-abrufen)
4. [Attribute lesen](#attribute-lesen)
5. [Versionen und Sprachen](#versionen-und-sprachen)
6. [Rechte und Kontext](#rechte-und-kontext)

## Objekte, Knoten und Attribute

| Klasse | Ist | Wichtige Attribute |
|---|---|---|
| `eZContentObject` | ein Inhalt mit allen seinen Versionen und Übersetzungen | `id`, `remote_id`, `name`, `current_version`, `main_node_id`, `class_identifier`, `section_id` |
| `eZContentObjectTreeNode` | eine Platzierung eines Objekts | `node_id`, `parent_node_id`, `path_string`, `url_alias`, `depth`, `object` |
| `eZContentObjectAttribute` | ein Feld einer Version in einer Sprache | `data_type_string`, `content`, `has_content`, `contentclass_attribute_identifier` |

## Ein einzelnes Element abrufen

```php
$object = eZContentObject::fetch( 57 );
$object = eZContentObject::fetchByRemoteID( 'expdemo-settings-page' );
$node   = eZContentObjectTreeNode::fetch( 2 );
$node   = eZContentObjectTreeNode::fetchByURLPath( 'about_us' );
```

Verwenden Sie für Inhalte, von denen Ihre Anwendung abhängt, bevorzugt **Remote-IDs**: Sie sind über Installationen
hinweg stabil, IDs nicht.

## Listen abrufen

```php
$nodes = eZContentObjectTreeNode::subTreeByNodeID( array(
    'ClassFilterType'  => 'include',
    'ClassFilterArray' => array( 'event' ),
    'AttributeFilter'  => array( array( 'event/start_date', '>=', time() ) ),
    'SortBy'           => array( array( 'attribute', true, 'event/start_date' ) ),
    'Depth'            => 3,
    'Limit'            => 20,
    'Offset'           => 0 ), $parentNodeID );

$count = eZContentObjectTreeNode::subTreeCountByNodeID( array(
    'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'event' ) ), $parentNodeID );
```

Das ist die PHP-Seite der Template-Fetches ([Fetch-Funktionen: Daten in Templates lesen](fetch-funktionen.md)). Setzen
Sie immer ein `Limit`.

## Attribute lesen

```php
$dataMap = $object->dataMap();                     // identifier => eZContentObjectAttribute
if ( isset( $dataMap['title'] ) && $dataMap['title']->hasContent() )
    $title = $dataMap['title']->content();         // a string for ezstring
$image = $dataMap['image']->content();             // an eZImageAliasHandler for ezimage
$alias = $image->attribute( 'medium' );            // url, width, height, alternative_text
$text  = $dataMap['title']->toString();            // a plain string form for every datatype
```

`content()` liefert, was der Datentyp speichert (eine Zeichenkette, eine Zahl, ein Objekt); `toString()` eine
Zeichenkette, die Sie speichern und später an `fromString()` übergeben können
([Lebenszyklus eines Datentyps](lebenszyklus-eines-datentyps.md)).

## Versionen und Sprachen

```php
$version = $object->version( $object->attribute( 'current_version' ) );
$germanMap = $object->fetchDataMap( false, 'ger-DE' );
$languages = $object->availableLanguages();
```

Die aktuelle Version ist die veröffentlichte; Entwürfe sind andere Versionen mit eigenem Status. Welche Sprache eine
Seite zeigt, folgt der `SiteLanguageList` des Siteaccess ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)).

## Rechte und Kontext

- `subTreeByNodeID()` wendet `content/read` des aktuellen Benutzers an, sofern Sie nicht `'Limitation' => array()`
  übergeben; übergeben Sie das niemals in Code, der Benutzern Ergebnisse zeigt.
- `$node->canRead()` und `$object->canEdit()` prüfen ein einzelnes Element
  ([Richtlinienfunktionen und Einschränkungen](richtlinienfunktionen-und-einschraenkungen.md)).
- In Kommandozeilenskripten ist der aktuelle Benutzer anonym, bis Sie einen anmelden; Skripte, die mehr brauchen,
  müssen einen eigens dafür vorgesehenen Benutzer anmelden (`eZUser::setCurrentlyLoggedInUser()`), nicht den
  Administrator.
- Große Listen: Rufen Sie seitenweise ab und leeren Sie dazwischen die Caches im Speicher
  (`eZContentObject::clearCache()`), damit einem langen Skript nicht der Speicher ausgeht.

## Verwandte Seiten

- [Inhalte im Code anlegen](inhalte-im-code-anlegen.md)
- [Persistente Objekte und eigene Tabellen](persistente-objekte-und-eigene-tabellen.md)
- [Active-Record-Muster](../../../../eng-us/standards/patterns/active-record.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
