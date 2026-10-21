---
title: Der Lebenszyklus eines Datentyps
audience: app-creator, developer
category: app-creation
order: 250
summary: Wann der Kernel welche Methode von eZDataType aufruft - Bearbeiten des Klassenattributs, Anlegen von Objekten, Bearbeiten und Validieren, Speichern und Veröffentlichen, Kopieren und Übersetzen, Papierkorb und Entfernen, Suche, Sortierung, Serialisierung als Zeichenkette und in Paketen, Informationssammlung.
keywords: Lebenszyklus eines Datentyps, Methoden von eZDataType, Validierung, Veröffentlichen, Serialisierung, Anwendungsentwickler, datatype life cycle, eZDataType methods, initializeObjectAttribute, validateObjectAttributeHTTPInput, fetchObjectAttributeHTTPInput, storeObjectAttribute, onPublish, deleteStoredObjectAttribute, toString, fromString, metaData, sortKey, serialize, app creator
type: help
---
# Der Lebenszyklus eines Datentyps

Diese Seite ist für Anwendungsentwickler, die einen Datentyp schreiben oder pflegen. Sie führt die Methoden von
`eZDataType` (`kernel/classes/ezdatatype.php`) nach dem Zeitpunkt auf, zu dem der Kernel sie aufruft, damit Sie wissen,
welche Ihr Datentyp implementieren muss ([Einen eigenen Datentyp schreiben](eigene-datentypen.md)).

## Inhalt

1. [Klassenattribut: die Einstellungen des Feldes](#klassenattribut-die-einstellungen-des-feldes)
2. [Objektattribut: anlegen und bearbeiten](#objektattribut-anlegen-und-bearbeiten)
3. [Speichern und veröffentlichen](#speichern-und-veröffentlichen)
4. [Kopien, Versionen und Übersetzungen](#kopien-versionen-und-übersetzungen)
5. [Papierkorb und Entfernen](#papierkorb-und-entfernen)
6. [Suche, Sortierung und Namen](#suche-sortierung-und-namen)
7. [Zeichenketten und Pakete](#zeichenketten-und-pakete)
8. [Informationssammlung und der Shop](#informationssammlung-und-der-shop)

## Klassenattribut: die Einstellungen des Feldes

| Methode | Aufgerufen, wenn |
|---|---|
| `initializeClassAttribute()` | ein Attribut dieses Typs einer Klasse hinzugefügt wird |
| `validateClassAttributeHTTPInput()`, `fetchClassAttributeHTTPInput()` | das Formular zum Bearbeiten der Klasse gesendet wird |
| `customClassAttributeHTTPAction()` | eine eigene Schaltfläche im Formular zum Bearbeiten der Klasse gedrückt wird |
| `preStoreClassAttribute()`, `storeClassAttribute()`, `storeDefinedClassAttribute()` | die Klasse gespeichert und angewendet wird |
| `classAttributeContent()` | `$class_attribute.content` gelesen wird |
| `isClassAttributeRemovable()` | bevor das Attribut aus der Klasse entfernt wird |
| `deleteStoredClassAttribute()` | das Attribut aus der Klasse entfernt wird |

## Objektattribut: anlegen und bearbeiten

| Methode | Aufgerufen, wenn |
|---|---|
| `initializeObjectAttribute()` | ein neues Objekt oder eine neue Version das Attribut erhält (Werte der vorherigen Version hier übernehmen) |
| `postInitializeObjectAttribute()` | nach der Initialisierung |
| `validateObjectAttributeHTTPInput()` | das Bearbeitungsformular gesendet wird: `eZInputValidator::STATE_ACCEPTED` oder `STATE_INVALID` mit `setValidationError()` zurückgeben |
| `fixupObjectAttributeHTTPInput()` | Eingaben vor dem Übernehmen korrigiert werden sollen |
| `fetchObjectAttributeHTTPInput()` | der gesendete Wert in das Attribut übernommen wird; `true` zurückgeben, wenn er sich geändert hat |
| `customObjectAttributeHTTPAction()` | eine eigene Schaltfläche im Bearbeitungsformular gedrückt wird (Hochladen, Bild entfernen ...) |
| `objectAttributeContent()`, `hasObjectAttributeContent()` | Templates `.content`, `.has_content` lesen |

## Speichern und veröffentlichen

| Methode | Aufgerufen, wenn |
|---|---|
| `storeObjectAttribute()` | das Attribut gespeichert wird (bei jedem Speichern des Entwurfs) |
| `postStore()` | nach dem Speichern |
| `onPublish()` | die Version veröffentlicht wird: der Moment für Arbeit, die nur für veröffentlichte Daten geschehen darf |

## Kopien, Versionen und Übersetzungen

`initializeObjectAttribute( $objectAttribute, $currentVersion, $originalContentObjectAttribute )` wird auch für eine neue
Version, eine Kopie und eine neue Übersetzung aufgerufen: Kopieren Sie, was das Original enthält, einschließlich der
Daten in Ihren eigenen Tabellen. `isTranslatable()` (eine Eigenschaft des Datentyps) entscheidet, ob jede Sprache einen
eigenen Wert hat.

## Papierkorb und Entfernen

| Methode | Aufgerufen, wenn |
|---|---|
| `trashStoredObjectAttribute()` | das Objekt in den Papierkorb kommt |
| `restoreTrashedObjectAttribute()` | es wiederhergestellt wird |
| `deleteStoredObjectAttribute( $objectAttribute, $version = null )` | eine Version oder das ganze Objekt entfernt wird: Löschen Sie hier Ihre eigenen Zeilen und Dateien |

## Suche, Sortierung und Namen

| Methode | Zweck |
|---|---|
| `isIndexable()`, `metaData()` | ob und welcher Text in den Suchindex kommt |
| `sortKey()`, `sortKeyType()` | Wert und Typ (`int` oder `string`) für die Sortierung nach dem Attribut |
| `title()` | der Anteil des Attributs am Namensmuster des Objekts |
| `isRelationType()`, `removeRelatedObjectItem()`, `fixRelatedObjectItem()` | Relations-Datentypen |
| `diff()` | die Ansicht zum Vergleich von Versionen |

## Zeichenketten und Pakete

| Methode | Zweck |
|---|---|
| `toString()`, `fromString()` | eine einfache Zeichenkettenform, verwendet von Importen, CSV, REST und `eZContentFunctions` |
| `serializeContentClassAttribute()`, `unserializeContentClassAttribute()` | die Einstellungen des Attributs in Paketen |
| `serializeContentObjectAttribute()`, `unserializeContentObjectAttribute()`, `postUnserializeContentObjectAttribute()` | Werte in Inhaltspaketen |
| `isSimpleStringInsertionSupported()`, `insertSimpleString()`, `insertHTTPFile()`, `insertRegularFile()` | Uploads und WebDAV |

Sorgen Sie dafür, dass `fromString( toString( x ) )` wieder `x` ergibt; testen Sie das.

## Informationssammlung und der Shop

| Methode | Zweck |
|---|---|
| `isInformationCollector()`, `hasInformationCollection()` | das Attribut kann von Besuchern ausgefüllt werden |
| `validateCollectionAttributeHTTPInput()`, `fetchCollectionAttributeHTTPInput()` | der Formularwert des Besuchers |
| `isAddToBasketValidationRequired()`, `validateAddToBasket()`, `productOptionInformation()` | Produktoptionen |

## Verwandte Seiten

- [Einen eigenen Datentyp schreiben](eigene-datentypen.md)
- [Muster Datentyp](../../../../eng-us/standards/patterns/datatype.md)
- [Inhalte im Code anlegen und aktualisieren](inhalte-im-code-anlegen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
