---
title: Eine Inhaltsklasse anlegen
audience: developer, admin
category: examples
order: 20
summary: Die Inhaltsklasse Note mit einem Titel und einem Text anlegen, einmal von Hand in der Administration und einmal mit einem wiederholt ausführbaren PHP-Skript, das eZContentClass und eZContentClassAttribute verwendet.
keywords: Inhaltsklasse, Klasse, Attribut, Datentyp, Klassengruppe, Bezeichner, content class, class, attribute, datatype, ezstring, eztext, eZContentClass, eZContentClassAttribute, eZContentClassClassGroup, ezexec, class group, identifier
type: example
---
# Eine Inhaltsklasse anlegen

Eine Inhaltsklasse ist der Typ eines Inhaltsobjekts: seine Felder (Attribute), jedes von einem Datentyp, und wie seine
Objekte benannt werden. Sie legen die Klasse **Note** (Bezeichner `note`) mit einem erforderlichen **Title**
(Textzeile, `ezstring`) und einem **Body** (Textabschnitt, `eztext`) an: zuerst von Hand in der Administration, dann
mit einem Skript, das Sie in einer Erweiterung aufbewahren und auf jeder Installation ausführen können, die die Klasse
braucht.

Voraussetzungen: eine Administratoranmeldung mit den Richtlinien für `class`; für das Skript eine Shell im
Wurzelverzeichnis der Installation.

## 1. Von Hand in der Administration

1. Öffnen Sie **Setup > Klassen** (`/class/grouplist`). Die Seite listet die Klassengruppen auf.
2. Öffnen Sie die Gruppe **Content** und klicken Sie auf **Neue Klasse**.
3. **Name** `Note`, **Bezeichner** `note`, **Objekt Namensschema** `<title>`.
4. Wählen Sie unter **Attribut hinzufügen** *Textzeile* und klicken Sie auf **Hinzufügen**: Name `Title`, Bezeichner
   `title`, setzen Sie das Häkchen bei **Erforderlich**. Fügen Sie einen *Textabschnitt* hinzu: Name `Body`,
   Bezeichner `body`.
5. Klicken Sie auf **OK**. Die Klasse erscheint in der Gruppe; `/class/view/<id>` zeigt sie mit zwei Attributen.

Die Bezeichner sind das, was Templates und Code verwenden (`$node.data_map.title`), wählen Sie sie also einmal und
endgültig: Kleinbuchstaben, Buchstaben, Ziffern und Unterstriche.

## 2. Mit einem Skript

Bewahren Sie das Skript in Ihrer Erweiterung auf, zum Beispiel unter
`extension/expexample/scripts/create_note_class.php`. Es sucht die Klasse zuerst über ihren Bezeichner, sodass ein
zweiter Lauf nichts ändert:

```php
<?php
/**
 * Creates the content class "Note" (identifier note) with a title and a body, in the class group "Content".
 * Run it from the installation root with the kernel loaded:
 *
 *   php bin/php/ezexec.php extension/expexample/scripts/create_note_class.php
 *
 * Running it a second time changes nothing: the class is looked up by its identifier first.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$identifier = 'note';
$language   = 'eng-US';

if ( eZContentClass::fetchByIdentifier( $identifier ) instanceof eZContentClass )
{
    echo "The class $identifier exists already, nothing to do\n";
    return;
}

$group = eZContentClassGroup::fetchByName( 'Content' );
if ( !$group instanceof eZContentClassGroup )
{
    echo "The class group Content does not exist\n";
    return;
}

// The administrator (user id 14 in a new installation) is the creator, as the command line has no signed-in user
$creatorID = 14;

$db = eZDB::instance();
$db->begin();

$class = eZContentClass::create( $creatorID, array(
    'version'            => eZContentClass::VERSION_STATUS_DEFINED,
    'identifier'         => $identifier,
    'remote_id'          => 'expexample-class-note',
    'contentobject_name' => '<title>',
    'is_container'       => 0,
    'always_available'   => 0,
    'name'               => 'Note',
    'description'        => 'A short note with a title and a body',
), $language );
$class->store();

$attributes = array(
    // identifier => array( datatype, name, required, searchable, translatable, extra columns )
    'title' => array( 'ezstring', 'Title', 1, 1, 1, array( 'data_int1' => 255 ) ),   // data_int1: maximum length
    'body'  => array( 'eztext',   'Body',  0, 1, 1, array( 'data_int1' => 10 ) ),    // data_int1: rows of the field
);
$placement = 0;
foreach ( $attributes as $attributeIdentifier => $definition )
{
    list( $dataTypeString, $name, $required, $searchable, $translatable, $extra ) = $definition;
    $attribute = eZContentClassAttribute::create( (int)$class->attribute( 'id' ), $dataTypeString, array_merge( array(
        'version'                  => eZContentClass::VERSION_STATUS_DEFINED,
        'identifier'               => $attributeIdentifier,
        'name'                     => $name,
        'placement'                => ++$placement,
        'is_required'              => $required,
        'is_searchable'            => $searchable,
        'can_translate'            => $translatable,
        'is_information_collector' => 0,
    ), $extra ), $language );
    $attribute->dataType()->initializeClassAttribute( $attribute );
    $attribute->store();
}

eZContentClassClassGroup::create( $class->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED,
                                  $group->attribute( 'id' ), $group->attribute( 'name' ) )->store();
$db->commit();

eZContentClass::expireCache();
eZContentClassAttribute::expireCache();

echo "Created the class $identifier (id " . $class->attribute( 'id' ) . ")\n";
```

Was jeder Aufruf tut:

| Aufruf | Bewirkt |
|---|---|
| `eZContentClass::fetchByIdentifier( $identifier )` | die definierte (veröffentlichte) Klasse mit diesem Bezeichner, oder `null` |
| `eZContentClass::create( $userID, $values, $locale )` | ein neues Klassenobjekt, noch nicht gespeichert; `name` und `description` werden für `$locale` gespeichert |
| `eZContentClassAttribute::create( $classID, $datatype, $values, $locale )` | ein neues Attribut; `data_int1` ... `data_text5` sind die eigenen Klasseneinstellungen des Datentyps |
| `$attribute->dataType()->initializeClassAttribute( $attribute )` | lässt den Datentyp seine Standardwerte setzen |
| `eZContentClassClassGroup::create( ... )->store()` | ordnet die Klasse einer Klassengruppe zu |
| `eZContentClass::expireCache()` | die Klassencaches aller Prozesse sehen die neue Klasse |

Wird `version` als `VERSION_STATUS_DEFINED` geschrieben, wird die Klasse direkt als veröffentlicht gespeichert. Eine
Klasse, die bereits Objekte hat, wird über die Klassenbearbeitung geändert (eine temporäre Version, dann *Anwenden*),
niemals, indem man ihre definierte Version unter ihnen hindurch überschreibt: Wer einer solchen Klasse ein Attribut
hinzufügt, braucht außerdem `$attribute->initializeObjectAttributes()`.

Ausführen:

```bash
php -l extension/expexample/scripts/create_note_class.php
php bin/php/ezexec.php extension/expexample/scripts/create_note_class.php --allow-root-user
```

Erwartet: `Created the class note (id <n>)`; ein zweiter Lauf gibt `The class note exists already, nothing to do`
aus.

## 3. Prüfen

- **Setup > Klassen > Content** listet *Note* auf; ihre Seite zeigt die beiden Attribute in der richtigen
  Reihenfolge.
- Das Objekt-Namensschema funktioniert: Legen Sie eine Note mit dem Titel *Einkaufsliste* an; sie heißt
  *Einkaufsliste*.
- In einem Template: `{$node.data_map.title.content|wash}` und `{attribute_view_gui attribute=$node.data_map.body}`.

Für diese Seite geprüft (2026-10-18): `php -l` für das Skript; jede Methode, die es aufruft, existiert mit dieser
Signatur in `kernel/classes/ezcontentclass.php`, `ezcontentclassattribute.php`, `ezcontentclassgroup.php`,
`ezcontentclassclassgroup.php` und `ezdatatype.php`. Das Skript wurde für diese Seite nicht gegen eine Datenbank
ausgeführt.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Die Klasse zeigt in einer anderen Sprache keinen Namen | `name` wurde nur für `$locale` gespeichert; fügen Sie auf der Bearbeitungsseite der Klasse Übersetzungen hinzu |
| `fetchByName( 'Content' )` findet nichts | die Gruppe wurde umbenannt; verwenden Sie ihre ID mit `eZContentClassGroup::fetch( <id> )` |
| Die neue Klasse erscheint nicht im Menü *Hier erstellen* | der Rolle des Benutzers fehlt `content/create` für die Klasse, oder die Klasse des Elternknotens schränkt die darunter erlaubten Klassen ein |
| Ein geändertes Attribut wird nicht sofort sichtbar | die Klassencaches: `eZContentClass::expireCache()`, oder `php bin/php/ezcache.php --clear-id=classid --allow-root-user` |

## Abgeleitet aus

- `kernel/classes/ezcontentclass.php` (`create()`, `fetchByIdentifier()`, `expireCache()`)
- `kernel/classes/ezcontentclassattribute.php` (`create()`, `dataType()`, `initializeObjectAttributes()`)
- `kernel/classes/help/content/exphelpcontentinstaller.php` (`ensureClass()`, `createAttribute()`: die eigene,
  wiederholt ausführbare Klassenerstellung des Kernels, der dieses Skript folgt)
- `doc/eng-us/guides/content-model-and-editing.md`, Abschnitt 2

## Verwandte Seiten

- [Kochbuch](README.md)
- [Inhaltsmodell und Bearbeitung](../../anleitungen/inhaltsmodell-und-bearbeitung.md) und
  [Klassengruppen](../../anleitungen/klassengruppen.md)
- [Datentypen](../datentypen/README.md): jeder Datentyp und seine Template-Attribute
- [Ein eigener Datentyp](eigener-datentyp.md) und [Ein Template-Override](template-override.md)
- Die Programmierregeln und der Musterkatalog: [die Standards](../../standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
