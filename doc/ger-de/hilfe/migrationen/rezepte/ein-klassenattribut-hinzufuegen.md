---
title: "Ein Klassenattribut hinzufügen"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 4
summary: "Eine Migration, die der Klasse news_article das Attribut subtitle an der richtigen Stelle hinzufügt, jedem bestehenden Objekt das neue Feld gibt und Attribut und Werte in down() wieder entfernt."
keywords: Migration, Migrationen, Klassenattribut, Attribut hinzufügen, neues Feld, Inhaltsklasse, bestehende Objekte, migration, migrations, class attribute, add attribute, eZContentClassAttribute, initializeObjectAttributes, placement, ezstring, exp:migration:migrate
type: help
---
# Ein Klassenattribut hinzufügen

Dieses Rezept richtet sich an Entwickler, die einer Inhaltsklasse mit vorhandenen Inhalten ein Feld hinzufügen. Das
Attribut nur an der Klasse anzulegen reicht nicht: Jedes vorhandene Objekt braucht das neue Feld in jeder seiner
Versionen und Sprachen, sonst fehlt es beim Bearbeiten und in den Templates. Die Migration erledigt beides. Das
Beispiel fügt `news_article` direkt nach Title das Attribut **Subtitle** (`subtitle`, eine Textzeile mit höchstens
150 Zeichen) hinzu.

## Was Sie am Ende haben

- die Migration `20261101120000_news_article_subtitle.php`, mit `requires:` nach der Migration der Klasse geordnet;
- das Attribut `subtitle` an Position 2 von `news_article`, die Attribute danach um eins nach hinten gerückt;
- jeden bestehenden News-Artikel mit einem leeren Feld Subtitle, das Redakteure oder eine Migration füllen können
  ([Inhalte in großen Mengen ändern](inhalte-in-grossen-mengen-aendern.md));
- ein `down()`, das das Attribut und seine gespeicherten Werte entfernt.

## Bevor Sie beginnen

- Exponential 6.0.15 mit erledigtem `6.0.15-migration-tables`.
- Die Klasse `news_article` existiert ([Eine Inhaltsklasse anlegen oder ändern](eine-inhaltsklasse-anlegen-oder-aendern.md)).
- Eine Entwicklungsinstallation mit einigen News-Artikeln, damit Sie sehen, wie bestehende Objekte erweitert werden;
  ein Git-Checkout; ausführen als Benutzer des Webservers (als root mit `--allow-root-user`).

## Schritte

### 1. Erzeugen und füllen

```bash
./console exp:migration:generate --skeleton news_article_subtitle --description="The attribute Subtitle (subtitle, ezstring) of the class news_article, after Title" --apply
```

```text
Exponential migrations: generate
Wrote migrations/20261101120000_news_article_subtitle.php
Migration 20261101120000_news_article_subtitle, class \Site\Migrations\NewsArticleSubtitle20261101120000
Next: fill in plan() and up(), then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

Ergänzen Sie den Kopf um `// requires: 20261101110000_news_article_class` und fügen Sie die Methoden unten ein.

### 2. Probelauf

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-120118-5ea3b4, environment development, database sqlite (transactions: full)

  done        20261101110000_news_article_class            site               The content class News article (news_article) in the class group Content
  would-apply 20261101120000_news_article_subtitle         site               The attribute Subtitle (subtitle, ezstring) of the class news_article, after Title
              would add the attribute subtitle (ezstring, 150 characters) to news_article after title
              would give each of the 3 existing objects an empty subtitle

Captions: pre would-take, post would-take
Summary: 2 migration(s): 1 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

### 3. Ausführen

```bash
./console exp:migration:migrate --apply
```

```text
  applied     20261101120000_news_article_subtitle         site               The attribute Subtitle (subtitle, ezstring) of the class news_article, after Title
              added the attribute subtitle to news_article at position 2
              gave the 3 existing objects the attribute
```

### 4. Ausliefern

Die Klassendefinition ist zwischengespeichert. Leeren Sie nach dem Lauf die Caches wie bei jeder Auslieferung, zum
Beispiel mit `./console exp:velocity deploy`, damit PHP-FPM und die Velocity-Worker das neue Feld im Editor zeigen.

## Die Migration

```php
<?php
// description: The attribute Subtitle (subtitle, ezstring) of the class news_article, after Title
// requires: 20261101110000_news_article_class
// author: Site team
namespace Site\Migrations;

final class NewsArticleSubtitle20261101120000 extends \expMigrationPhp
{
    const CLASS_IDENTIFIER = 'news_article';
    const ATTRIBUTE = 'subtitle';
    const LANGUAGE = 'eng-US';

    private static function newsClass()
    {
        $class = \eZContentClass::fetchByIdentifier( self::CLASS_IDENTIFIER );
        if ( !$class instanceof \eZContentClass )
            throw new \RuntimeException( 'there is no class ' . self::CLASS_IDENTIFIER );
        return $class;
    }

    public function plan( $context )
    {
        $class = \eZContentClass::fetchByIdentifier( self::CLASS_IDENTIFIER );
        $objects = $class instanceof \eZContentClass ? $class->objectCount() : 0;
        return array( 'add the attribute ' . self::ATTRIBUTE . ' (ezstring, 150 characters) to ' . self::CLASS_IDENTIFIER . ' after title',
                      "give each of the $objects existing objects an empty subtitle" );
    }

    public function detect( $context )
    {
        $class = \eZContentClass::fetchByIdentifier( self::CLASS_IDENTIFIER );
        return $class instanceof \eZContentClass && $class->fetchAttributeByIdentifier( self::ATTRIBUTE )
             ? \expMigration::DONE : null;
    }

    public function up( $context )
    {
        $class = self::newsClass();
        $classId = (int)$class->attribute( 'id' );
        // make room after title: every attribute behind it moves one down
        $title = $class->fetchAttributeByIdentifier( 'title' );
        $placement = $title ? (int)$title->attribute( 'placement' ) + 1 : count( $class->fetchAttributes() ) + 1;
        foreach ( $class->fetchAttributes() as $existing )
        {
            if ( (int)$existing->attribute( 'placement' ) >= $placement )
            {
                $existing->setAttribute( 'placement', (int)$existing->attribute( 'placement' ) + 1 );
                $existing->store();
            }
        }
        $attribute = \eZContentClassAttribute::create( $classId, 'ezstring', array(
            'version' => \eZContentClass::VERSION_STATUS_DEFINED,
            'identifier' => self::ATTRIBUTE,
            'name' => 'Subtitle',
            'placement' => $placement,
            'is_required' => 0,
            'is_searchable' => 1,
            'can_translate' => 1,
            'is_information_collector' => 0,
            'data_int1' => 150,
        ), self::LANGUAGE );
        $attribute->dataType()->initializeClassAttribute( $attribute );
        $attribute->store();
        // the objects that exist get the new attribute in every version and language
        $attribute->initializeObjectAttributes();
        \eZContentClassAttribute::expireCache();
        return array( 'added the attribute subtitle to news_article at position ' . $placement,
                      'gave the ' . $class->objectCount() . ' existing objects the attribute' );
    }

    public function down( $context )
    {
        $class = self::newsClass();
        $attribute = $class->fetchAttributeByIdentifier( self::ATTRIBUTE );
        if ( !$attribute )
            return array( 'the attribute ' . self::ATTRIBUTE . ' is gone already' );
        $id = (int)$attribute->attribute( 'id' );
        if ( !$attribute->removeThis( true ) )
            throw new \RuntimeException( 'the attribute ' . self::ATTRIBUTE . ' cannot be removed' );
        // the values the objects kept for it
        $context->db()->query( "DELETE FROM ezcontentobject_attribute WHERE contentclassattribute_id = $id" );
        \eZContentClassAttribute::expireCache();
        return array( 'removed the attribute subtitle and its values' );
    }
}
```

- `initializeObjectAttributes()` schreibt das neue Feld in jede Version und Sprache jedes Objekts der Klasse. Bei
  einer Klasse mit vielen tausend Objekten ist das eine lange Folge von Anweisungen; teilen Sie solch eine Änderung
  wie in [Große Datenmigrationen](grosse-datenmigrationen.md).
- `data_int1` ist die Höchstlänge eines `ezstring`; andere Datentypen halten ihre Einstellungen in anderen Feldern
  (`eztext` die Zeilenzahl in `data_int1`, `ezselection` die Optionen in `data_text5`).
- Ein Pflichtattribut (`is_required => 1`) an einer Klasse mit Inhalten macht jedes bestehende Objekt beim nächsten
  Bearbeiten ungültig, bis das Feld gefüllt ist. Legen Sie es optional an, füllen Sie es und machen Sie es in einer
  zweiten Migration zur Pflicht.

## Prüfen

- Setup > Klassen > News article zeigt Title, Subtitle, Intro, Body, Source.
- Bearbeiten Sie einen bestehenden News-Artikel: Das Feld Subtitle ist da und leer.
- `./console exp:migration:status` zeigt die Migration als `done`.

## Zurücknehmen

```bash
./console exp:migration:rollback 20261101120000_news_article_subtitle --apply
```

```text
  undone      20261101120000_news_article_subtitle         site               The attribute Subtitle (subtitle, ezstring) of the class news_article, after Title
              removed the attribute subtitle and its values
```

Was Redakteure in Subtitle eingetragen haben, ist damit weg. Wenn es zählt, nehmen Sie den Lauf mit seinem
Sicherungspunkt zurück oder behalten das Attribut und schreiben eine neue Migration. Die Positionen der Attribute
nach Subtitle bleiben um eins höher; für Redakteure ändert das nichts.

## Nächster Schritt

Ändern Sie viele bestehende Objekte in einer Migration:
[Inhalte in großen Mengen ändern](inhalte-in-grossen-mengen-aendern.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Eine Inhaltsklasse anlegen oder ändern](eine-inhaltsklasse-anlegen-oder-aendern.md), [Ein umkehrbares down() schreiben](ein-umkehrbares-down-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
