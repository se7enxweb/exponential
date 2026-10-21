---
title: "Lektion 4.1: Klassen, Attribute und Bezeichner"
audience: trainer, developer
category: training
order: 41
summary: Die Teilnehmenden lesen das Inhaltsmodell als Entwickler, legen unter Setup > Klassen eine kleine Klasse an und listen mit eZContentClass die Attribute, Datentypen und Pflichtfelder einer Klasse aus PHP auf. 50 Minuten.
keywords: entwickler, inhaltsklasse, klassenbezeichner, attributbezeichner, datentyp, klassengruppe, eZContentClass, fetchByIdentifier, dataMap, objekt namensschema, setup klassen, developers course, content class, class identifier, attribute identifier, datatype, class group, object name pattern, Setup Classes
type: help
---
# Lektion 4.1: Klassen, Attribute und Bezeichner

Templates und Code nennen eine Klasse nie bei ihrem Titel, sondern bei ihrem **Bezeichner**: `article`, `folder`,
`title`, `body`. In dieser Lektion sehen die Teilnehmenden das Inhaltsmodell von der Seite eines Entwicklers: Klassen
und ihre Attribute, den Datentyp jedes Attributs und die Bezeichner, die Templates, Fetches und PHP-Code verwenden.
Sie legen eine eigene kleine Klasse an und lesen sie aus PHP wieder aus.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 4: Inhaltsklassen und Datentypen](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Modul 2](../modul-02-die-template-sprache/README.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Klasse, Klassengruppe, Attribut, Datentyp, Objekt und Knoten jeweils in einem Satz erklären;
- unter **Setup** > **Klassen** eine Klasse mit zwei Attributen anlegen;
- den Bezeichner einer Klasse sowie die Bezeichner und Datentypen ihrer Attribute finden;
- sie mit `eZContentClass::fetchByIdentifier()` und `dataMap()` aus PHP auflisten;
- erklären, warum Bezeichner und nicht Namen oder IDs in Templates und Code gehören.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Das Inhaltsmodell und das Bearbeiten von Inhalten, Teile 1 bis 3](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#1-wie-das-inhaltsmodell-zusammenpasst).

1. **Das Modell** (5 Minuten). Projizieren Sie die Tabelle:

   | Begriff | Bedeutung |
   |---|---|
   | Inhaltsklasse | Eine Art von Inhalt: ein Name, ein Bezeichner und eine Liste von Attributen |
   | Klassengruppe | Ein Ordner von Klassen im Klasseneditor (Content, Users, Media, Setup) |
   | Datentyp | Die Art eines Attributs: `ezstring`, `eztext`, `ezxmltext`, `ezimage`, `ezobjectrelationlist` ... |
   | Inhaltsobjekt | Ein Inhalt, eine Instanz einer Klasse, mit Versionen und Sprachen |
   | Knoten | Ein Platz eines Objekts im Inhaltsbaum; ein Objekt kann mehrere haben |

2. **Bezeichner** (3 Minuten). IDs unterscheiden sich zwischen Installationen; Bezeichner reisen mit Paketen und Code.
   Ein Template schreibt `$node.data_map.title`, ein Override `Match[class_identifier]=article`.
3. **Der Klasseneditor** (4 Minuten). **Setup** > **Klassen** listet die Klassengruppen; öffnen Sie **Inhalt** und
   zeigen Sie **Neue Klasse**, **Bearbeiten**, **Kopieren** und **Ausgewählte entfernen**. Öffnen Sie die Klasse
   **Ordner** und lesen Sie ihre Attribute.
4. **Aus PHP** (3 Minuten). Zeigen Sie das Skript der Übung; `dataMap()` gibt die Klassenattribute nach Bezeichner
   zurück.

### Häufige Fragen

**Kann ich einen Bezeichner später umbenennen?** Das können Sie, aber jedes Template, jeder Override, jeder Fetch und
jedes Skript, das ihn verwendet, muss sich dann auch ändern. Wählen Sie Bezeichner von Anfang an sorgfältig.

**Wie viele Datentypen gibt es?** Die ausgelieferte `settings/content.ini [DataTypeSettings] AvailableDataTypes[]`
listet 35; Erweiterungen fügen eigene hinzu (Lektion 4.4).

**Woher kommen die Klassen eines Seitenpakets?** Aus dem Paket, das es installiert. Der Kurs zu Multisite- und
Seitenpaketen behandelt Pakete ausführlich; [Pakete](../../../../anleitungen/pakete.md) ist die Referenz.

### Stolperfallen

- **Eine Klasse mit vielen Objekten bearbeiten.** Fügen Sie einer Klasse mit Tausenden von Objekten ein Attribut
  hinzu, wird jedes Objekt aktualisiert. Auf einer Schulungsinstallation geht das schnell; auf einer Live-Website
  planen Sie es.
- **Einen Datentyp ändern.** Der Datentyp eines Attributs lässt sich nicht einfach ändern; fügen Sie ein neues
  Attribut hinzu und verschieben Sie die Daten.
- **Teilnehmende, die sich eine Installation teilen.** Alle Teilnehmenden benennen ihre Klasse mit den eigenen
  Initialen, zum Beispiel `note_ab`, damit sich Klassen nicht überschneiden.

## Praktische Übung

Auf der Schulungsinstallation, als Administrator an der Administration angemeldet.

1. Öffnen Sie **Setup** > **Klassen** und die Gruppe **Inhalt**.
2. Öffnen Sie **Ordner** und schreiben Sie die Bezeichner und Datentypen seiner Attribute auf.
3. Klicken Sie zurück in der Gruppe auf **Neue Klasse**. Nennen Sie sie `Note`, Bezeichner `note` (auf einer
   gemeinsamen Installation fügen Sie Ihre Initialen hinzu). Setzen Sie das **Objekt Namensschema** auf `<title>`.
4. Fügen Sie ein Attribut des Datentyps **Textzeile** hinzu, Name `Title`, Bezeichner `title`, und kreuzen Sie
   **Erforderlich** an.
5. Fügen Sie ein Attribut des Datentyps **Textabschnitt** hinzu, Name `Body`, Bezeichner `body`.
6. Klicken Sie auf **OK**. Die Klasse erscheint in der Liste.
7. Legen Sie `extension/mysite_hello/scripts/classinfo.php` an:

   ```php
   <?php
   // List the attributes of a class: php bin/php/ezexec.php extension/mysite_hello/scripts/classinfo.php
   $cli = eZCLI::instance();
   $class = eZContentClass::fetchByIdentifier( 'note' );
   if ( !$class instanceof eZContentClass )
   {
       $cli->error( 'No class with the identifier note' );
       return;
   }
   $cli->output( $class->attribute( 'name' ) . ' (' . $class->attribute( 'identifier' ) . '), ID ' . $class->attribute( 'id' ) );
   foreach ( $class->dataMap() as $identifier => $classAttribute )
   {
       $cli->output( sprintf( '  %-20s %-22s %s',
           $identifier,
           $classAttribute->attribute( 'data_type_string' ),
           $classAttribute->attribute( 'is_required' ) ? 'required' : '' ) );
   }
   $cli->output( 'Objects of this class: ' . $class->objectCount() );
   ```

8. Prüfen Sie es und führen Sie es aus: `php -l` auf der Datei, dann `php bin/php/ezexec.php extension/mysite_hello/scripts/classinfo.php`.
9. Ändern Sie im Skript `note` in `article` und führen Sie es erneut aus.
10. Legen Sie in der Administration in Ihrem Kursordner eine **Note** an und führen Sie das Skript noch einmal aus.
11. Committen Sie das Skript.

## Erwartetes Ergebnis

- Schritt 8 gibt `Note (note)`, seine ID, `title ezstring required` und `body eztext` sowie
  `Objects of this class: 0` aus.
- Schritt 9 listet die Attribute des Artikels auf, zum Beispiel `title`, `short_title`, `author`, `intro`, `body`, mit
  ihren Datentypen.
- Schritt 10: Die Zahl der Objekte ist 1.
- Der Trainer prüft die Klasse in der Liste **Setup** > **Klassen** einer Person.

## Quiz

1. Warum verwenden Templates `class_identifier` und nicht die Klassen-ID?
2. Welche Methode gibt die Attribute einer Klasse nach Bezeichner zurück?
   A. `attribute( 'name' )` B. `dataMap()` C. `objectCount()` D. `fetchByIdentifier()`
3. Was ist der Unterschied zwischen einem Inhaltsobjekt und einem Knoten?
4. Wo steht die Liste der Datentypen, die für neue Attribute gewählt werden können?
5. Richtig oder falsch: Den Bezeichner eines Attributs zu ändern ist harmlos, wenn Templates ihn bereits verwenden.

<details>
<summary>Antworten</summary>

1. IDs unterscheiden sich zwischen Installationen; Bezeichner bleiben in Paketen, Templates und Code gleich.
2. **B.** `eZContentClass::dataMap()`.
3. Das Objekt ist der Inhalt; ein Knoten ist einer seiner Plätze im Baum.
4. In `settings/content.ini`, `[DataTypeSettings] AvailableDataTypes[]`, plus dem, was Erweiterungen hinzufügen.
5. **Falsch.** Jedes Template, jeder Override und jedes Skript, das ihn verwendet, muss sich ebenfalls ändern.

</details>

## Weiterführende Literatur

- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md): Klassen,
  Objekte, Versionen und der Editor.
- [Klassengruppen](../../../../anleitungen/klassengruppen.md): Klassen ordnen.
- [Inhaltsklassenpakete bündeln](../../../../../eng-us/help/designers/extensions/bundling-content-class-packages.md): Klassen mit einer
  Erweiterung ausliefern.

## Verwandte Seiten

- [Modul 4: Inhaltsklassen und Datentypen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
