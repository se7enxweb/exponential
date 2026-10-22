---
title: Idempotente Importe über die remote_id
audience: integrator, developer
category: integration
order: 280
summary: Importe so gestalten, dass sie beliebig oft laufen dürfen - die remote_id von Objekten und Knoten, ein Benennungsschema für Schlüssel, die Funktion zum Anlegen oder Aktualisieren mit Änderungserkennung, der Umgang mit an der Quelle entfernten Datensätzen und was zu tun ist, wenn Schlüssel kollidieren.
keywords: idempotent, Idempotenz, remote_id, Remote-ID, fetchByRemoteID, eZContentObject, eZContentObjectTreeNode, createAndPublishObject, updateAndPublishObject, Upsert, Änderungserkennung, Duplikat, erneuter Lauf, Synchronisation, Schlüsselschema, idempotency, remote id, change detection, duplicate, re-run, sync, key scheme
type: help
---
# Idempotente Importe über die remote_id

Diese Seite ist für Integrationsentwickler. Ein Import ist **idempotent**, wenn zweimaliges Ausführen dasselbe
Ergebnis liefert wie einmaliges: keine Duplikate, keine leeren Versionen, keine verlorenen Änderungen. Exponential gibt
jedem Inhaltsobjekt (und jedem Knoten) eine **Remote-ID**, eine Zeichenkette, die Sie selbst setzen dürfen, und das ist
der Schlüssel, der Importe idempotent macht.

## Inhalt

1. [Die Remote-ID](#die-remote-id)
2. [Ein Schlüsselschema](#ein-schlüsselschema)
3. [Anlegen oder aktualisieren, mit Änderungserkennung](#anlegen-oder-aktualisieren-mit-änderungserkennung)
4. [An der Quelle entfernte Datensätze](#an-der-quelle-entfernte-datensätze)
5. [Kollisionen und Reparaturen](#kollisionen-und-reparaturen)
6. [Regeln](#regeln)

## Die Remote-ID

| | Objekt | Knoten (Platzierung) |
|---|---|---|
| Spalte | `ezcontentobject.remote_id`, bis zu 100 Zeichen | `ezcontentobject_tree.remote_id`, bis zu 100 Zeichen |
| Eindeutig | Ja (eindeutiger Index auf MySQL und PostgreSQL) | Jeder Knoten bekommt seine eigene; verwenden Sie für Importe die Remote-ID des Objekts als Schlüssel |
| Gesetzt, wenn | Erzeugt, wenn leer (`eZRemoteIdUtility::generate()`), oder von Ihnen vorgegeben | Erzeugt, wenn der Knoten angelegt wird |
| Darüber finden | `eZContentObject::fetchByRemoteID( $remoteID )`, gibt das Objekt oder `null` zurück | `eZContentObjectTreeNode::fetchByRemoteID( $remoteID )` |
| Setzen | `createAndPublishObject( array( 'remote_id' => ... ) )`, `updateAndPublishObject( $object, array( 'remote_id' => ... ) )`, `expobject::setRemoteId` | |

Die Objekt-ID ist eine Zahl, die sich zwischen Installationen unterscheidet; die Remote-ID reist mit: Pakete tragen
sie, und ein woanders wiederhergestelltes oder installiertes Objekt behält sie.

## Ein Schlüsselschema

Wählen Sie ein Schema pro Integration und schreiben Sie es in die Schnittstellenspezifikation:

```text
<system>-<record type>-<id at the source>
erp-product-10442
pim-category-shoes
crm-event-2027-0312
```

- Stellen Sie das Quellsystem als Präfix voran, damit zwei Integrationen nie kollidieren können (der RSS-Import
  verwendet `RSSImport_<import id>_...`).
- Verwenden Sie die **stabile** ID der Quelle, nie einen Namen oder Titel, der sich ändern kann.
- Bleiben Sie bei Buchstaben, Ziffern und `_ . : -` und innerhalb von 100 Zeichen (der `expservices`-Dienst
  `expobject::setRemoteId` nimmt genau dieses Alphabet an).
- Bei Sprachen behalten Sie ein Objekt pro Datensatz und importieren jede Sprache als Übersetzung, nicht ein Objekt pro
  Sprache.

## Anlegen oder aktualisieren, mit Änderungserkennung

```php
<?php
/**
 * Creates or updates the object for one source record. Returns 'created', 'updated', 'unchanged' or 'failed'.
 * $attributes: attribute identifier => value in the datatype's fromString() format.
 */
function myIntegrationUpsert( $remoteID, $parentNodeID, $classIdentifier, array $attributes, $language = false, $dryRun = false )
{
    $object = eZContentObject::fetchByRemoteID( $remoteID );

    if ( !$object instanceof eZContentObject )
    {
        if ( $dryRun )
            return 'created';
        $params = array( 'class_identifier' => $classIdentifier,
                         'parent_node_id' => $parentNodeID,
                         'remote_id' => $remoteID,
                         'attributes' => $attributes );
        if ( $language )
            $params['language'] = $language;
        $object = eZContentFunctions::createAndPublishObject( $params, false );   // no notifications
        return $object instanceof eZContentObject ? 'created' : 'failed';
    }

    // only the attributes whose value differs, so an unchanged record makes no new version
    $changed = array();
    $dataMap = $object->dataMap();
    foreach ( $attributes as $identifier => $value )
    {
        if ( !isset( $dataMap[$identifier] ) || $dataMap[$identifier]->toString() !== (string)$value )
            $changed[$identifier] = $value;
    }
    if ( !$changed )
        return 'unchanged';
    if ( $dryRun )
        return 'updated';

    $params = array( 'attributes' => $changed );
    if ( $language )
        $params['language'] = $language;
    return eZContentFunctions::updateAndPublishObject( $object, $params, false ) ? 'updated' : 'failed';
}
```

Hinweise:

- `toString()` und `fromString()` sind das jeweils eigene Textformat eines Datentyps; vergleichen Sie in diesem
  Format. Bei Rich Text (`ezxmltext`), Bildern und Dateien wird die gespeicherte Darstellung verglichen; vergleichen
  Sie eine Prüfsumme des Quellwerts, die Sie in einem versteckten Attribut oder Ihrer eigenen Tabelle speichern, wenn
  die gespeicherte Form von dem abweicht, was Sie senden.
- `createAndPublishObject()` verwaltet seine eigene Transaktion und durchläuft den Veröffentlichungsvorgang, sodass
  Workflows, Caches, der Suchindex und das Audit-Protokoll im Gleichschritt bleiben. Schreiben Sie nie direkt in
  Inhaltstabellen.
- Bei einem Probelauf meldet die Funktion, was sie tun würde; zählen Sie die Ergebnisse und geben Sie sie am Ende aus.

## An der Quelle entfernte Datensätze

Entscheiden Sie pro Klasse, gemeinsam mit dem Kunden:

| Regel | Wie | Folge |
|---|---|---|
| In den Papierkorb entfernen | `eZContentObjectTreeNode::removeSubtrees( array( $nodeID ), true )` | Eine Weile wiederherstellbar; URLs antworten nicht mehr |
| Verbergen | `eZContentObjectTreeNode::hideSubTree( $node )` | Die URL bleibt für Redakteure; Besucher sehen sie nicht |
| Mit einem Objektzustand kennzeichnen | Einen Zustand wie „zurückgezogen" zuweisen | Für Templates und Filter sichtbar; nichts verschwindet |
| Behalten | Nichts tun | Die Website zeigt veraltete Datensätze |

Finden Sie entfernte Datensätze in einem vollständigen Lauf: jedes Objekt mit Ihrem Präfix, dessen Quell-ID nicht in
der Datei stand. Entfernen Sie nie etwas, wenn die Quelldatei leer oder viel kleiner als üblich ist; halten Sie
stattdessen an und alarmieren Sie.

## Kollisionen und Reparaturen

- **Zwei Objekte für einen Datensatz** (angelegt, bevor Remote-IDs verwendet wurden): Wählen Sie das zu behaltende aus,
  geben Sie ihm die Remote-ID mit `expobject::setRemoteId` oder
  `updateAndPublishObject( $object, array( 'remote_id' => ..., 'attributes' => ... ) )` und entfernen Sie das andere.
- **Eine Remote-ID, die bereits ein anderes Objekt verwendet**: Das Anlegen scheitert auf MySQL und PostgreSQL, und
  `expobject::create` antwortet mit `409`. Protokollieren Sie es mit beiden IDs und halten Sie für diesen Datensatz
  an; überschreiben Sie nie das andere Objekt.
- **Das Schlüsselschema ändern**: Schreiben Sie eine einmalige Migration, die vor dem ersten Import mit dem neuen
  Schema für jedes vorhandene Objekt die neue Remote-ID setzt, mit Probelauf.

## Regeln

- [ ] Jedes importierte Objekt trägt eine Remote-ID nach dem Schema.
- [ ] Importe suchen, bevor sie anlegen.
- [ ] Unveränderte Datensätze erzeugen keine neue Version.
- [ ] Importe veröffentlichen ohne Benachrichtigung, sofern Benachrichtigungen nicht gewünscht sind.
- [ ] Löschregel pro Klasse entschieden; ein Sicherheitsstopp für verdächtig kleine Quellen.
- [ ] Ein Probelauf existiert und wird vor dem ersten echten Lauf in jeder Umgebung genutzt.

## Verwandte Seiten

- [Inhalte aus PHP anlegen, veröffentlichen und ändern](../erweiterungsentwicklung/teil-8-referenz/inhaltsfunktionen-aus-php.md)
- [Ein Importskript schreiben](ein-importskript-schreiben.md)
- [Inhaltsklassen externen Daten zuordnen](inhaltsklassen-auf-externe-daten-abbilden.md)
- [eZContentObject](../erweiterungsentwicklung/teil-8-referenz/ezcontentobject.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
