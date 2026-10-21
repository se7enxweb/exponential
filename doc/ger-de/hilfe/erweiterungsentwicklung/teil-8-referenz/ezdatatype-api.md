---
title: Die eZDataType-API
audience: developer
category: extension-development
order: 200
summary: Jede eZDataType-Methode, die ein Datentyp überschreibt, nach Zweck gruppiert - Registrierung, Bearbeitung, Speicherung, Klasseneinstellungen, Informationssammlung, Suche und Sortierung, Import, Pakete - mit den Signaturen des Kernels.
keywords: Datentyp, Datentyp-API, Registrierung, Bearbeitung, Speicherung, Validierung, Sortierung, Pakete, eZDataType, datatype API, register, validateObjectAttributeHTTPInput, fetchObjectAttributeHTTPInput, storeObjectAttribute, objectAttributeContent, hasObjectAttributeContent, classAttributeContent, metaData, sortKey, toString, fromString, onPublish
type: help
---
# Die eZDataType-API

Teil 8, Referenz 16. Ein Datentyp ist eine Klasse, die `eZDataType` (`kernel/classes/ezdatatype.php`) erweitert und
die Methoden überschreibt, die sie braucht. Diese Seite führt sie nach Zweck auf, mit den Signaturen der Basisklasse
(übernehmen Sie sie genau: PHP 8 verweigert ein Überschreiben mit abweichender Signatur). Wie Sie einen bauen, steht in
[Datentypen](../teil-3-erweiterungsentwickler/datentypen.md),
[Speicherung, Validierung und Templates von Datentypen](../teil-3-erweiterungsentwickler/datentyp-speicherung-validierung-und-templates.md)
und in den Tutorials [Ein Datentyp, von Hand](../teil-3-erweiterungsentwickler/tutorials/von-hand-datentyp.md) und
[Mit RAD: ein Datentyp](../teil-3-erweiterungsentwickler/tutorials/rad-datentyp.md).

## Registrierung

| Methode | Hinweise |
|---|---|
| `public function __construct( $dataTypeString, $name, $properties = array() )` | rufen Sie ihn aus Ihrem Konstruktor mit Ihrer Datentyp-Zeichenkette und dem übersetzten Namen auf |
| `static function register( $dataTypeString, $className )` | einmal am Ende der Datei aufrufen: `eZDataType::register( 'mydatatype', 'myDataTypeType' );` |

Dazu `content.ini [DataTypeSettings] ExtensionDirectories[]` und `AvailableDataTypes[]`.

## Das Attribut eines Objekts bearbeiten

| Methode | Aufgerufen, wenn |
|---|---|
| `initializeObjectAttribute( $objectAttribute, $currentVersion, $originalContentObjectAttribute )` | eine neue Version angelegt wird: Werte kopieren oder Vorgaben setzen |
| `validateObjectAttributeHTTPInput( $http, $base, $objectAttribute )` | das Bearbeitungsformular abgeschickt wird; geben Sie `eZInputValidator::STATE_ACCEPTED` oder `STATE_INVALID` zurück |
| `fetchObjectAttributeHTTPInput( $http, $base, $objectAttribute )` | nach der Validierung: den abgeschickten Wert in das Attribut übernehmen; geben Sie `true` zurück, wenn etwas gelesen wurde |
| `customObjectAttributeHTTPAction( $http, $action, $objectAttribute, $parameters )` | eine eigene Schaltfläche Ihres Bearbeitungs-Templates gedrückt wurde |

Die Namen der abgeschickten Felder folgen der Konvention des Kernels aus Basis, Datentyp-Zeichenkette, Feld und
Attribut-ID: `ezstring` liest `$base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' )`. Ihr
Bearbeitungs-Template muss genau die Namen abschicken, die Ihre Klasse liest.

## Speicherung und Inhalt

| Methode | Verwendung |
|---|---|
| `storeObjectAttribute( $objectAttribute )` | bevor die Attributzeile gespeichert wird |
| `objectAttributeContent( $objectAttribute )` | was `$attribute.content` in Templates und `attribute( 'content' )` in PHP ist |
| `hasObjectAttributeContent( $contentObjectAttribute )` | ob das Attribut ausgefüllt ist |
| `onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )` | wenn die Version veröffentlicht wird |
| `deleteStoredObjectAttribute( $objectAttribute, $version = null )` | bevor die gespeicherten Daten entfernt werden (löschen Sie Dateien, die Sie gespeichert haben) |

## Klasseneinstellungen

| Methode | Verwendung |
|---|---|
| `validateClassAttributeHTTPInput( $http, $base, $classAttribute )` | das Formular zum Bearbeiten der Klasse wird abgeschickt |
| `fetchClassAttributeHTTPInput( $http, $base, $classAttribute )` | die Einstellungen auf Klassenebene lesen |
| `storeClassAttribute( $classAttribute, $version )` | bevor das Klassenattribut gespeichert wird |
| `classAttributeContent( $classAttribute )` | was `$class_attribute.content` ist |

## Informationssammlung

| Methode | Verwendung |
|---|---|
| `isInformationCollector()` | geben Sie `true` zurück, wenn das Attribut Eingaben von Besuchern sammeln kann |
| `validateCollectionAttributeHTTPInput( $http, $base, $objectAttribute )` | die Eingabe eines Besuchers validieren |
| `fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $objectAttribute )` | sie speichern |

## Suche, Sortierung, Benennung

| Methode | Verwendung |
|---|---|
| `isIndexable()` | ob die Suchmaschine es indiziert |
| `metaData( $contentObjectAttribute )` | der zu indizierende Text |
| `sortKey( $objectAttribute )`, `sortKeyType()` | Wert und Typ (`'int'` oder `'string'`) zum Sortieren nach diesem Attribut |
| `title( $objectAttribute, $name = null )` | der Text, der in Objektnamen verwendet wird |

## Import, Export, Pakete

| Methode | Verwendung |
|---|---|
| `toString( $objectAttribute )`, `fromString( $objectAttribute, $string )` | das Attribut als Zeichenkette, für Importe, Exporte und einige Werkzeuge |
| `serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )`, `unserializeContentClassAttribute( ... )` | die Klasseneinstellungen in einem Paket |

## Templates

Ohne seine Templates, die über die Design-Kette gefunden werden, zeichnet ein Datentyp nichts:
`content/datatype/view/<datatypestring>.tpl` und `content/datatype/edit/<datatypestring>.tpl`, dazu
`class/datatype/view/` und `class/datatype/edit/` für die Klasseneinstellungen. Die Erweiterung braucht
`design.ini [ExtensionSettings] DesignExtensions[]`, damit sie gefunden werden.

## Verwandte Seiten

- Zurück: [eZFile, eZDir und Cluster-Datei-Handler](ezfile-ezdir-und-cluster-dateien.md)
- Weiter: [Die eZWorkflowType-API](ezworkflowtype-api.md)
- [Datentypen](../teil-3-erweiterungsentwickler/datentypen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
