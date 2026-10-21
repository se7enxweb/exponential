---
title: Inhalte im Code anlegen und aktualisieren
audience: app-creator, developer
category: app-creation
order: 200
summary: Inhalte aus PHP anlegen, aktualisieren, verschieben und entfernen - eZContentFunctions::createAndPublishObject und updateAndPublishObject, Werte im fromString-Format, Rich Text, Bilder, Remote-IDs für idempotente Importe, Workflows und Transaktionen sowie große Jobs.
keywords: Inhalte anlegen, Inhalte aktualisieren, Import, Remote-ID, veröffentlichen, Inhalts-Jobs, Anwendungsentwickler, create content, update content, eZContentFunctions, createAndPublishObject, updateAndPublishObject, fromString, import, remote_id, publish, content jobs, PHP, app creator
type: help
---
# Inhalte im Code anlegen und aktualisieren

Diese Seite ist für Anwendungsentwickler, die Inhalte aus Code schreiben: Importe, Synchronisierungen, Formulare, die
Objekte anlegen, Migrationen. `eZContentFunctions` des Kernels (`kernel/classes/ezcontentfunctions.php`) legt in einem
Aufruf an und veröffentlicht, mit derselben Operation (und denselben Triggern) wie der Editor.

## Inhalt

1. [Anlegen und veröffentlichen](#anlegen-und-veröffentlichen)
2. [Aktualisieren](#aktualisieren)
3. [Werte für jeden Datentyp](#werte-für-jeden-datentyp)
4. [Idempotente Importe mit Remote-IDs](#idempotente-importe-mit-remote-ids)
5. [Verschieben, verbergen, entfernen](#verschieben-verbergen-entfernen)
6. [Große Operationen](#große-operationen)

## Anlegen und veröffentlichen

```php
$object = eZContentFunctions::createAndPublishObject( array(
    'creator_id'       => $userID,
    'class_identifier' => 'article',
    'parent_node_id'   => $parentNodeID,
    'remote_id'        => 'feed-item-' . $externalID,
    'attributes'       => array(
        'title' => 'A new article',
        'intro' => $introXml,
        'body'  => $bodyXml ) ) );

if ( !$object instanceof eZContentObject )
    throw new RuntimeException( 'Could not create the article' );
```

Attributwerte werden in ihrer `fromString()`-Form angegeben. Das Veröffentlichen führt die Operation
`content/publish` aus, also laufen auch die Workflows an ihren Triggern, und Caches sowie der Suchindex werden
aktualisiert.

## Aktualisieren

```php
eZContentFunctions::updateAndPublishObject( $object, array(
    'attributes' => array( 'title' => 'A better title' ) ) );
```

Eine neue Version wird angelegt und veröffentlicht; die vorherige wird archiviert, wie bei einer Bearbeitung durch die
Redaktion.

## Werte für jeden Datentyp

| Datentyp | Zeichenkettenform |
|---|---|
| `ezstring`, `eztext` | der Text |
| `ezinteger`, `ezfloat`, `ezboolean` | `42`, `3.5`, `1` |
| `ezdate`, `ezdatetime` | ein Unix-Zeitstempel |
| `ezxmltext` | das interne XML des Rich Text (das `<section>`-Dokument) |
| `ezobjectrelationlist` | Objekt-IDs, getrennt durch `-` |
| `ezselection` | die IDs der gewählten Optionen |
| `ezimage`, `ezbinaryfile` | ein Dateipfad (bei Bildern nach einem Trennzeichen ein Alternativtext) |

Das genaue Format steht jeweils in `fromString()` der Datentyp-Klasse in `kernel/classes/datatypes/<name>/`; lesen Sie
es, bevor Sie einen Datentyp zum ersten Mal importieren.

## Idempotente Importe mit Remote-IDs

Geben Sie importierten Objekten eine Remote-ID, die aus der ID der Quelle gebildet wird. Aktualisieren Sie beim nächsten
Lauf, statt neu anzulegen:

```php
$remoteID = 'feed-item-' . $externalID;
$existing = eZContentObject::fetchByRemoteID( $remoteID );
if ( $existing )
    eZContentFunctions::updateAndPublishObject( $existing, array( 'attributes' => $attributes ) );
else
    eZContentFunctions::createAndPublishObject( array( 'remote_id' => $remoteID ) + $params );
```

Ein zweimal ausgeführter Import darf keine Duplikate anlegen.

## Verschieben, verbergen, entfernen

Verwenden Sie die Operationen des Kernels statt SQL: `eZContentObjectTreeNodeOperations::move()`,
`eZContentOperationCollection` zum Verbergen und Einblenden, `eZContentObjectTreeNode::removeSubtrees()` zum Entfernen
(in den Papierkorb oder nicht). Sie halten URL-Aliasse, Caches und den Suchindex korrekt.

## Große Operationen

Tausende Objekte gehören in einen Hintergrundprozess: einen Befehl, der aus Cron läuft, einen Job der Warteschlange
([Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md)) oder die Inhalts-Jobs des Kernels für das Entfernen, Kopieren
und Verschieben großer Teilbäume ([Inhalts-Jobs](inhalts-jobs.md)). Committen Sie in Stapeln, leeren Sie zwischen den
Stapeln mit `eZContentObject::clearCache()` und protokollieren Sie den Fortschritt.

## Verwandte Seiten

- [Die Content-API in PHP](die-content-api.md)
- [CLI-Skripte mit eZScript](cli-skripte.md)
- [Workflows, Trigger und Ereignisse](workflows-und-ereignisse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
