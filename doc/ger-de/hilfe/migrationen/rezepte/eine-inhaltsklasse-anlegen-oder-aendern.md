---
title: "Eine Inhaltsklasse anlegen oder ändern"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 3
summary: "Eine Migration, die die Inhaltsklasse news_article mit ihren Attributen und ihrer Klassengruppe anlegt, auf Entwicklung, Staging und Produktion gleich, und das Muster, mit dem eine spätere Migration eine Klasse ändert (Name, Namensmuster)."
keywords: Migration, Migrationen, Inhaltsklasse, Klasse anlegen, Klasse ändern, Klassenattribute, Klassengruppe, Inhaltsmodell, migration, migrations, content class, create class, change class, eZContentClass, eZContentClassAttribute, eZContentClassClassGroup, news_article, exp:migration:migrate
featured: true
type: help
---
# Eine Inhaltsklasse anlegen oder ändern

Dieses Rezept richtet sich an Entwickler, die das Inhaltsmodell einer Site im Code ändern, statt es auf jeder
Installation in Setup > Klassen zusammenzuklicken. Eine von einer Migration angelegte Klasse hat überall denselben
Bezeichner, dieselben Attribute und dieselbe Remote-ID, und spätere Migrationen der Site (Inhalte, Rollen) können
sich mit `requires:` auf sie verlassen. Das Beispiel legt die Klasse `news_article` (Title, Intro, Body, Source) in
der Klassengruppe Content an.

## Was Sie am Ende haben

- die Migration `20261101110000_news_article_class.php`;
- die Klasse **News article** (`news_article`, Remote-ID `site-class-news-article`) mit vier Attributen in der
  Klassengruppe Content, auf jeder Installation, auf der die Migration läuft;
- ein `detect()`, das eine Installation erkennt, auf der es die Klasse schon gibt, und ein `down()`, das die Klasse
  entfernt, solange sie keine Objekte hat.

## Bevor Sie beginnen

- Exponential 6.0.15 mit erledigtem `6.0.15-migration-tables`.
- Eine Entwicklungsinstallation und ein Git-Checkout; ausführen als Benutzer des Webservers (als root mit
  `--allow-root-user`).
- Der Klassenbezeichner ist frei: Setup > Klassen hat kein `news_article`.

## Schritte

### 1. Das Gerüst erzeugen

```bash
./console exp:migration:generate --skeleton news_article_class --description="The content class News article (news_article) in the class group Content" --apply
```

```text
Exponential migrations: generate
Wrote migrations/20261101110000_news_article_class.php
Migration 20261101110000_news_article_class, class \Site\Migrations\NewsArticleClass20261101110000
Next: fill in plan() and up(), then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

### 2. Füllen

Schreiben Sie die Klassendefinition als Daten (`attributes()`) und die Methoden unten. Die Attribute werden mit den
Klassen des Kernels in der Reihenfolge der Liste angelegt; `initializeClassAttribute()` gibt jedem Datentyp seine
Vorgaben.

### 3. Probelauf

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-110405-0c1d9e, environment development, database mysql (transactions: data)

  would-apply 20261101110000_news_article_class            site               The content class News article (news_article) in the class group Content
              would create the class news_article with 4 attributes (title, intro, body, source)
              would add it to the class group Content

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

### 4. Ausführen

```bash
./console exp:migration:migrate --apply
```

```text
Exponential migrations: run
Run 20261101-110522-e41f07, environment development, database mysql (transactions: data), as admin

  applied     20261101110000_news_article_class            site               The content class News article (news_article) in the class group Content
              created the class news_article (id 48) with 4 attributes
              added it to the class group Content

Captions: pre 20261101-110522, post 20261101-110524
Repository: commit 6b1f0c2d9a7e, tag migrations/development/20261101-110522-e41f07, push pushed
Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 1 applied
PASS
```

Die Klassen-ID (hier 48) ist auf jeder Installation eine andere. Verweisen Sie in späteren Migrationen über
Bezeichner oder Remote-ID auf die Klasse, nie über ihre ID.

## Die Migration

```php
<?php
// description: The content class News article (news_article) in the class group Content
// author: Site team
namespace Site\Migrations;

final class NewsArticleClass20261101110000 extends \expMigrationPhp
{
    const IDENTIFIER = 'news_article';
    const REMOTE_ID = 'site-class-news-article';
    const LANGUAGE = 'eng-US';

    /** @return array identifier => (datatype, name, required, searchable) */
    public static function attributes()
    {
        return array(
            'title' => array( 'ezstring', 'Title', true, true ),
            'intro' => array( 'eztext', 'Intro', false, true ),
            'body' => array( 'eztext', 'Body', false, true ),
            'source' => array( 'ezstring', 'Source', false, false ),
        );
    }

    public function plan( $context )
    {
        if ( \eZContentClass::fetchByIdentifier( self::IDENTIFIER ) instanceof \eZContentClass )
            return array( 'nothing: the class ' . self::IDENTIFIER . ' exists' );
        return array( 'create the class ' . self::IDENTIFIER . ' with ' . count( self::attributes() ) . ' attributes (' . implode( ', ', array_keys( self::attributes() ) ) . ')',
                      'add it to the class group Content' );
    }

    public function detect( $context )
    {
        return \eZContentClass::fetchByIdentifier( self::IDENTIFIER ) instanceof \eZContentClass ? \expMigration::DONE : null;
    }

    public function up( $context )
    {
        $class = \eZContentClass::create( \eZUser::currentUserID(), array(
            'version' => \eZContentClass::VERSION_STATUS_DEFINED,
            'identifier' => self::IDENTIFIER,
            'remote_id' => self::REMOTE_ID,
            'contentobject_name' => '<title>',
            'is_container' => 0,
            'always_available' => 0,
            'name' => 'News article',
            'description' => 'An article of the News section',
        ), self::LANGUAGE );
        $class->store();
        $classId = (int)$class->attribute( 'id' );
        $placement = 0;
        foreach ( self::attributes() as $identifier => $definition )
        {
            list( $type, $name, $required, $searchable ) = $definition;
            $attribute = \eZContentClassAttribute::create( $classId, $type, array(
                'version' => \eZContentClass::VERSION_STATUS_DEFINED,
                'identifier' => $identifier,
                'name' => $name,
                'placement' => ++$placement,
                'is_required' => $required ? 1 : 0,
                'is_searchable' => $searchable ? 1 : 0,
                'can_translate' => 1,
                'is_information_collector' => 0,
            ), self::LANGUAGE );
            $attribute->dataType()->initializeClassAttribute( $attribute );
            $attribute->store();
        }
        $group = \eZContentClassGroup::fetchByName( 'Content' );
        if ( !$group instanceof \eZContentClassGroup )
            throw new \RuntimeException( 'there is no class group Content' );
        \eZContentClassClassGroup::create( $classId, \eZContentClass::VERSION_STATUS_DEFINED,
                                           $group->attribute( 'id' ), $group->attribute( 'name' ) )->store();
        return array( "created the class news_article (id $classId) with $placement attributes", 'added it to the class group Content' );
    }

    public function down( $context )
    {
        $class = \eZContentClass::fetchByIdentifier( self::IDENTIFIER );
        if ( !$class instanceof \eZContentClass )
            return array( 'the class ' . self::IDENTIFIER . ' is gone already' );
        if ( $class->objectCount() > 0 )
            throw new \RuntimeException( 'the class ' . self::IDENTIFIER . ' has ' . $class->objectCount() . ' objects: remove them first, or restore the caption taken before the run' );
        \eZContentClassOperations::remove( (int)$class->attribute( 'id' ) );
        return array( 'removed the class ' . self::IDENTIFIER );
    }
}
```

- Die Klasse wird als Benutzer des Laufs angelegt (`eZUser::currentUserID()`): der angemeldete Benutzer oder auf
  der Konsole `migration.ini [RunSettings] RunAsUser`.
- `eZContentClassOperations::remove()` entfernt eine Klasse mit ihren Objekten; `down()` verweigert das, solange
  Objekte existieren, damit eine Rücknahme nie überraschend Inhalte löscht. Inhalte und Klasse nehmen Sie gemeinsam
  mit dem Sicherungspunkt des Laufs zurück.

## Eine Klasse später ändern

Ändern Sie die Migration, die eine Klasse angelegt hat, nie, nachdem sie gelaufen ist: Die Prüfsummen-Sperre lehnt
die geänderte Datei ab. Schreiben Sie für jede Änderung eine neue Migration, zum Beispiel für einen neuen Namen und
ein neues Namensmuster:

```php
public function up( $context )
{
    $class = \eZContentClass::fetchByIdentifier( 'news_article' );
    if ( !$class instanceof \eZContentClass )
        throw new \RuntimeException( 'there is no class news_article' );
    $class->setName( 'News story', 'eng-US' );
    $class->setAttribute( 'contentobject_name', '<title> (<source>)' );
    $class->store();
    return array( 'renamed news_article to "News story", name pattern <title> (<source>)' );
}
```

Ein neues Attribut ist ein eigenes Rezept: [Ein Klassenattribut hinzufügen](ein-klassenattribut-hinzufuegen.md).
Ein geändertes Namensmuster benennt bestehende Objekte nicht um; sie bekommen den neuen Namen, wenn sie erneut
veröffentlicht werden ([Inhalte in großen Mengen ändern](inhalte-in-grossen-mengen-aendern.md)).

## Prüfen

- Setup > Klassen > Content zeigt **News article** mit vier Attributen.
- `./console exp:migration:status` zeigt die Migration als `done`; auf einer Installation, auf der die Klasse vorher
  von Hand angelegt wurde, macht `detect()` sie ohne Lauf zu `done`.

## Zurücknehmen

```bash
./console exp:migration:rollback 20261101110000_news_article_class --apply
```

```text
  undone      20261101110000_news_article_class            site               The content class News article (news_article) in the class group Content
              removed the class news_article
```

Gibt es Objekte der Klasse, scheitert `down()` mit „has 3 objects“; stellen Sie dann den Sicherungspunkt des Laufs
wieder her: `./console exp:migration:rollback 20261101-110522-e41f07 --apply`.

## Nächster Schritt

Fügen Sie der Klasse ein Feld hinzu: [Ein Klassenattribut hinzufügen](ein-klassenattribut-hinzufuegen.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Inhalte für eine neue Site anlegen](inhalte-fuer-eine-neue-site-anlegen.md), [Rollen und Richtlinien hinzufügen](rollen-und-richtlinien-hinzufuegen.md), [Inhaltsmodell und Bearbeitung](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
