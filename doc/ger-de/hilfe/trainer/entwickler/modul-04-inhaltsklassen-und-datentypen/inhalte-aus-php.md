---
title: "Lektion 4.3: Inhalte aus PHP lesen und schreiben"
audience: trainer, developer
category: training
order: 43
summary: Die Teilnehmenden lesen Objekte, Knoten und Attribute aus PHP, verwenden toString und fromString und legen auf ihrer Schulungsinstallation mit eZContentFunctions ein Inhaltsobjekt an und aktualisieren es. 55 Minuten.
keywords: entwickler, eZContentObject, eZContentObjectTreeNode, dataMap, data_map, toString, fromString, eZContentFunctions, createAndPublishObject, updateAndPublishObject, remote_id, creator_id, importskript, inhalte aus php, developers course, import script, content from PHP
type: help
---
# Lektion 4.3: Inhalte aus PHP lesen und schreiben

Importe, Migrationen und geplante Aufgaben lesen und schreiben Inhalte aus PHP. Der Kernel hat dafür eine kleine,
sichere API: ein Objekt oder einen Knoten holen, seine Attribute nach Bezeichner lesen und ein Objekt über dieselbe
Veröffentlichungsoperation anlegen oder aktualisieren, die auch das Bearbeitungsformular verwendet. In dieser Lektion
schreiben die Teilnehmenden ein Skript, das eine Notiz liest, eine neue anlegt und sie aktualisiert.

> **Hinweis**
> Dieses Skript schreibt Inhalte. Führen Sie es nur auf der Schulungsinstallation aus.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 4: Inhaltsklassen und Datentypen](README.md) |
| Dauer | 55 Minuten (15 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 4.1](klassen-attribute-und-bezeichner.md): die Klasse **Note** |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einen Knoten mit `eZContentObjectTreeNode::fetch()` und sein Objekt mit `object()` holen;
- Attribute mit `dataMap()` und `toString()` nach Bezeichner lesen;
- ein Objekt mit `eZContentFunctions::createAndPublishObject()` anlegen und veröffentlichen;
- ein Objekt mit `eZContentFunctions::updateAndPublishObject()` aktualisieren;
- eine `remote_id` verwenden, damit ein Skript seine eigenen Objekte wiederfindet.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: die Dokumentationskommentare in `kernel/classes/ezcontentfunctions.php`.

1. **Lesen** (3 Minuten). Ein Knoten liefert sein Objekt mit `$node->object()`; `$object->dataMap()` gibt die
   Attribute nach Bezeichner zurück, wie `data_map` in einem Template. `toString()` gibt den Wert eines Datentyps als
   String zurück.
2. **`createAndPublishObject`** (5 Minuten). Seine Parameter sind `parent_node_id`, `class_identifier`, `creator_id`,
   `attributes` (Bezeichner zu Wert) und optional `remote_id`, `section_id` und `storage_dir`. Die Werte sind Strings
   im Format der Methode `fromString()` des jeweiligen Datentyps; lesen Sie diese Methode im Zweifel nach. Die Methode
   gibt das Objekt oder `false` zurück.
3. **`updateAndPublishObject`** (3 Minuten). Nimmt ein Objekt und `attributes` (und optional `language`) an, legt eine
   neue Version an und veröffentlicht sie. Eine fehlende oder leere Attributliste wird abgewiesen, bevor sich etwas
   ändert.
4. **Benachrichtigungen** (2 Minuten). Beide Methoden nehmen ein letztes Argument `$notify` an; `false` veröffentlicht
   ohne Benachrichtigungsereignisse, wie **Ohne Benachrichtigung veröffentlichen** im Bearbeitungsformular.
5. **`remote_id`** (2 Minuten). Ein eindeutiger String, den Sie wählen. `eZContentObject::fetchByRemoteID()` findet
   das Objekt wieder, sodass ein Import aktualisieren kann, statt zu duplizieren.

### Häufige Fragen

**Welcher Benutzer veröffentlicht?** Die angegebene `creator_id` (die Inhaltsobjekt-ID eines Benutzers), sonst der
aktuelle Benutzer. In einem Skript ist der aktuelle Benutzer der anonyme Benutzer; geben Sie die ID eines Redakteurs
oder Administrators an.

**Wie schreibe ich Rich Text?** Als das XML, das der Rich-Text-Datentyp speichert; das Beispiel im Kommentar von
`updateAndPublishObject()` zeigt eine `<section>` mit `<paragraph>`. Einfache Textattribute (`ezstring`, `eztext`)
nehmen einfache Strings an.

**Werden Berechtigungen geprüft?** Die API veröffentlicht, was Sie ihr geben; das Skript ist vertrauenswürdiger Code.
Prüfen Sie Berechtigungen selbst, wenn ein Skript für einen Benutzer handelt.

### Stolperfallen

- **Das Skript zweimal ausführen.** Ohne Prüfung der `remote_id` legt der zweite Lauf ein zweites Objekt an. Holen Sie
  zuerst über die Remote-ID.
- **Falscher übergeordneter Knoten.** `parent_node_id` ist eine Knoten-ID, keine Objekt-ID.
- **Riesige Schleifen.** Tausende von Objekten in einem Lauf verlangen Sorgfalt beim Speicher (den Objekt-Cache im
  Speicher zwischen den Stapeln leeren) und sind besser als Inhaltsjobs oder in Stapeln aufgehoben; fangen Sie klein
  an.

## Praktische Übung

Auf der Schulungsinstallation, mit der Klasse **Note** und Ihrem Kursordner. `<folder node ID>` ist die Knoten-ID
Ihres Kursordners (in der Administration angezeigt).

1. Legen Sie `extension/mysite_hello/scripts/notes.php` an:

   ```php
   <?php
   // Create or update a note: php bin/php/ezexec.php extension/mysite_hello/scripts/notes.php
   $cli = eZCLI::instance();
   $parentNodeID = 0; // the node ID of your course folder
   $remoteID = 'mysite_hello_note_1';

   $admin = eZUser::fetchByName( 'admin' );
   $creatorID = $admin instanceof eZUser ? $admin->attribute( 'contentobject_id' ) : false;

   $object = eZContentObject::fetchByRemoteID( $remoteID );
   if ( !$object instanceof eZContentObject )
   {
       $object = eZContentFunctions::createAndPublishObject( array(
           'parent_node_id'   => $parentNodeID,
           'class_identifier' => 'note',
           'creator_id'       => $creatorID,
           'remote_id'        => $remoteID,
           'attributes'       => array( 'title' => 'Created by a script',
                                        'body'  => 'First version.' ) ) );
       $cli->output( $object ? 'Created object ' . $object->attribute( 'id' ) : 'Create failed' );
   }
   else
   {
       $ok = eZContentFunctions::updateAndPublishObject( $object, array(
           'attributes' => array( 'body' => 'Updated at ' . date( 'H:i:s' ) ) ) );
       $cli->output( $ok ? 'Updated object ' . $object->attribute( 'id' ) : 'Update failed' );
   }

   $object = eZContentObject::fetchByRemoteID( $remoteID );
   if ( $object instanceof eZContentObject )
   {
       $dataMap = $object->dataMap();
       $cli->output( 'Title: ' . $dataMap['title']->toString() );
       $cli->output( 'Body: ' . $dataMap['body']->toString() );
       $cli->output( 'Version: ' . $object->attribute( 'current_version' ) );
   }
   ```

2. Setzen Sie `$parentNodeID` auf die Knoten-ID Ihres Kursordners.
3. Prüfen Sie es mit `php -l` und führen Sie es dann aus: `php bin/php/ezexec.php extension/mysite_hello/scripts/notes.php`.
4. Suchen Sie die neue Notiz in der Administration in Ihrem Kursordner.
5. Führen Sie das Skript erneut aus.
6. Öffnen Sie die Seite **Versionen verwalten** der Notiz (oder ihre Versionsliste) und zählen Sie die Versionen.
7. Öffnen Sie die Seite der Notiz in der Administration und prüfen Sie **Eigentümer** oder die Versionsliste: Der
   Ersteller ist der Administrator, nicht der anonyme Benutzer.
8. Committen Sie das Skript.

## Erwartetes Ergebnis

- Schritt 3 gibt `Created object <ID>`, den Titel, `First version.` und `Version: 1` aus.
- Schritt 5 gibt `Updated object <ID>`, den neuen Text und `Version: 2` aus.
- Schritt 6: Die Notiz hat zwei Versionen.
- Der Trainer prüft die Notiz einer Person in der Administration.

## Quiz

1. Welche Methode gibt die Attribute eines Objekts nach Bezeichner zurück?
2. Mit welchem Parameter findet ein Skript sein eigenes Objekt wieder?
   A. `section_id` B. `remote_id` C. `storage_dir` D. `creator_id`
3. Was gibt `createAndPublishObject` zurück, wenn es fehlschlägt?
4. Was passiert, wenn `updateAndPublishObject` mit einer leeren Attributliste aufgerufen wird?
5. Ist `parent_node_id` eine Knoten-ID oder eine Objekt-ID?

<details>
<summary>Antworten</summary>

1. `$object->dataMap()`.
2. **B.** `remote_id`, gefunden mit `eZContentObject::fetchByRemoteID()`.
3. `false`.
4. Der Aufruf wird abgewiesen, bevor sich etwas ändert.
5. Eine Knoten-ID.

</details>

## Weiterführende Literatur

- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md): Versionen und
  Entwürfe.
- [Inhaltsjobs](../../../../../eng-us/features/6.0/content-jobs.md): große Operationen in Stapeln.
- [Ohne Benachrichtigung veröffentlichen](../../../../../eng-us/features/6.0/publish-without-notification.md): das Argument
  `$notify`.

## Verwandte Seiten

- [Modul 4: Inhaltsklassen und Datentypen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
