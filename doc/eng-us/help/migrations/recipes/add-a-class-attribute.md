---
title: "Add a class attribute"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 4
summary: "A migration that adds the attribute subtitle to the class news_article at the right position, gives every existing object the new field, and removes attribute and values again in down()."
keywords: migration, migrations, class attribute, add attribute, new field, content class, eZContentClassAttribute, initializeObjectAttributes, placement, ezstring, existing objects, exp:migration:migrate, Klassenattribut
type: help
---
# Add a class attribute

This recipe is for developers who add a field to a content class that already has content. Adding the attribute to
the class is not enough: every object that exists needs the new field in each of its versions and languages, or
editing and templates find it missing. The migration does both. The example adds **Subtitle** (`subtitle`, a text
line of at most 150 characters) to `news_article`, right after Title.

## What you'll have at the end

- the migration `20261101120000_news_article_subtitle.php`, ordered after the class's migration with `requires:`;
- the attribute `subtitle` at position 2 of `news_article`, the attributes after it moved down by one;
- every existing news article with an empty Subtitle field, ready to be filled by editors or by a migration
  ([Change content in bulk](change-content-in-bulk.md));
- a `down()` that removes the attribute and its stored values.

## Before you start

- Exponential 6.0.15 with `6.0.15-migration-tables` done.
- The class `news_article` exists ([Create or change a content class](create-or-change-a-content-class.md)).
- A development installation with some news articles, so you see the existing objects being extended; a git
  checkout; run as the web server user (as root, add `--allow-root-user`).

## Steps

### 1. Generate and fill in

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

Add `// requires: 20261101110000_news_article_class` to the header and the methods below.

### 2. Dry run

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

### 3. Run

```bash
./console exp:migration:migrate --apply
```

```text
  applied     20261101120000_news_article_subtitle         site               The attribute Subtitle (subtitle, ezstring) of the class news_article, after Title
              added the attribute subtitle to news_article at position 2
              gave the 3 existing objects the attribute
```

### 4. Deploy

The class definition is cached. After the run, clear the caches the way you deploy any change, for example with
`./console exp:velocity deploy`, so PHP-FPM and Velocity workers show the new field in the editor.

## The migration

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

- `initializeObjectAttributes()` writes the new field into every version and language of every object of the
  class. On a class with many thousands of objects this is one long statement series; split such a change as in
  [Large data migrations](large-data-migrations.md).
- `data_int1` is the maximum length of an `ezstring`; other datatypes keep their settings in other fields
  (`eztext` the number of rows in `data_int1`, `ezselection` its options in `data_text5`).
- A required attribute (`is_required => 1`) on a class with content makes every existing object invalid on its next
  edit until the field is filled. Add it as optional, fill it, then make it required in a second migration.

## Check it

- Setup > Classes > News article lists Title, Subtitle, Intro, Body, Source.
- Edit an existing news article: the Subtitle field is there and empty.
- `./console exp:migration:status` shows the migration as `done`.

## Roll it back

```bash
./console exp:migration:rollback 20261101120000_news_article_subtitle --apply
```

```text
  undone      20261101120000_news_article_subtitle         site               The attribute Subtitle (subtitle, ezstring) of the class news_article, after Title
              removed the attribute subtitle and its values
```

The values editors typed into Subtitle are gone with it. If they matter, roll back the run with its pre caption,
or keep the attribute and add a new migration instead. The positions of the attributes after Subtitle stay one
higher; that changes nothing for editors.

## Next step

Change many existing objects in one migration: [Change content in bulk](change-content-in-bulk.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Create or change a content class](create-or-change-a-content-class.md), [Writing a reversible down()](writing-a-reversible-down.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
