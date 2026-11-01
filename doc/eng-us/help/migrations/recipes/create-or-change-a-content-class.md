---
title: "Create or change a content class"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 3
summary: "A migration that creates the content class news_article with its attributes and class group, the same way on development, staging and production, and the pattern for changing a class (its name, its name pattern) in a later migration."
keywords: migration, migrations, content class, create class, change class, class attributes, class group, eZContentClass, eZContentClassAttribute, eZContentClassClassGroup, content model, news_article, exp:migration:migrate, Inhaltsklasse
featured: true
type: help
---
# Create or change a content class

This recipe is for developers who change the content model of a site in code instead of clicking it together in
Setup > Classes on every installation. A class created by a migration has the same identifier, attributes and
remote id everywhere, and a site's later migrations (content, roles) can rely on it with `requires:`. The example
creates the class `news_article` (Title, Intro, Body, Source) in the class group Content.

## What you'll have at the end

- the migration `20261101110000_news_article_class.php`;
- the class **News article** (`news_article`, remote id `site-class-news-article`) with four attributes, in the
  class group Content, on every installation the migration runs on;
- a `detect()` that recognizes an installation where the class exists already, and a `down()` that removes the
  class while it has no objects.

## Before you start

- Exponential 6.0.15 with `6.0.15-migration-tables` done.
- A development installation and a git checkout; run as the web server user (as root, add `--allow-root-user`).
- The class identifier is free: Setup > Classes has no `news_article`.

## Steps

### 1. Generate the skeleton

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

### 2. Fill it in

Write the class definition as data (`attributes()`) and the methods below. The attributes are created with the
kernel's own classes in the order of the list; `initializeClassAttribute()` gives each datatype its defaults.

### 3. Dry run

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

### 4. Run

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

The class id (48 here) differs per installation. Refer to the class by its identifier or remote id in later
migrations, never by its id.

## The migration

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

- The class is created as the run's user (`eZUser::currentUserID()`): the signed-in user, or
  `migration.ini [RunSettings] RunAsUser` on the console.
- `eZContentClassOperations::remove()` removes a class with its objects; `down()` refuses while objects exist, so a
  rollback never deletes content by surprise. Take content and class back together with the run's caption.

## Changing a class later

Never edit the migration that created a class once it has run: the checksum guard refuses the changed file. Write a
new migration for every change. For example, a new name and name pattern:

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

A new attribute is its own recipe: [Add a class attribute](add-a-class-attribute.md). Changing the name pattern does
not rename existing objects; they get the new name when they are published again
([Change content in bulk](change-content-in-bulk.md)).

## Check it

- Setup > Classes > Content shows **News article** with four attributes.
- `./console exp:migration:status` lists the migration as `done`; on an installation where the class was created by
  hand before, `detect()` makes it `done` without a run.

## Roll it back

```bash
./console exp:migration:rollback 20261101110000_news_article_class --apply
```

```text
  undone      20261101110000_news_article_class            site               The content class News article (news_article) in the class group Content
              removed the class news_article
```

With objects of the class, `down()` fails with "has 3 objects"; restore the pre caption of the run instead:
`./console exp:migration:rollback 20261101-110522-e41f07 --apply`.

## Next step

Add a field to the class: [Add a class attribute](add-a-class-attribute.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Seed content for a new site](seed-content-for-a-new-site.md), [Add roles and policies](add-roles-and-policies.md), [Content model and editing](../../../guides/content-model-and-editing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
