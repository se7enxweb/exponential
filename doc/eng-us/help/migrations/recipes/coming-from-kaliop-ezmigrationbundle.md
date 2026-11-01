---
title: "Coming from the Kaliop migration bundle"
audience: developer, dxp-developer, cms-developer, software-engineer
category: migrations
order: 24
summary: For developers who used the Kaliop eZ Migration Bundle (ezmigrationbundle) on eZ Platform and Ibexa projects - its commands, files and table mapped to Exponential 6, and a YAML content, class and role step rewritten by hand as a PHP migration.
keywords: migration, migrations, schema change, database change, Kaliop, kaliop, ezmigrationbundle, eZ Migration Bundle, kaliop:migration:migrate, kaliop:migration:generate, kaliop:migration:status, kaliop_migrations, YAML, Ibexa, eZ Platform, concept map
featured: true
type: help
---
# Coming from the Kaliop migration bundle

Many eZ Platform and Ibexa projects kept their content model and content in migrations of the Kaliop eZ Migration
Bundle (`kaliop/ezmigrationbundle`): YAML files with steps such as `type: content_type, mode: create`, PHP and SQL
migrations, the commands `kaliop:migration:*` and the table `kaliop_migrations`. Exponential 6 has migrations in its
kernel, with the same idea of files run once in order. This page maps the two and rewrites typical steps by hand.

## What you'll have at the end

- the Exponential counterpart of each Kaliop command and file type;
- a content type, a content item and a role step rewritten as one PHP migration;
- a clear picture of what 6.0.15 does not offer yet.

## Before you start

- Work through [the tutorial](../../../guides/migrations-tutorial.md) once.
- Keep your Kaliop YAML files at hand; they are the specification of what to write.

## Concept map

| Kaliop eZ Migration Bundle | Exponential 6.0.15 |
|---|---|
| `kaliop:migration:generate` | `./console exp:migration:generate --skeleton <name> --apply` (PHP only) |
| `kaliop:migration:status` | `./console exp:migration:status` |
| `kaliop:migration:migrate` | `./console exp:migration:migrate --apply` (without `--apply` a dry run) |
| `kaliop:migration:migrate --path=...` | `./console exp:migration:migrate --path=<file or folder> --apply` |
| table `kaliop_migrations` | tables `exp_migration` and `exp_migration_run` |
| statuses such as todo, done, failed, skipped | states `pending`, `done`, `failed`, `not-needed`, `unknown` |
| bundle folders `MigrationVersions/` | the site's `migrations/`, `extension/<name>/migrations/`, a package's `migrations/` |
| YAML steps (`content`, `content_type`, `role`, `sql`, ...) | **not run in 6.0.15**: write the step as PHP |
| PHP migrations | PHP migrations extending `\expMigrationPhp` |
| SQL migrations per database | **not run in 6.0.15**: SQL inside a PHP migration, per engine where needed |
| references (`[reference:...]`) | `$context->setReference()` / `$context->reference()` within one run; between runs find things again by remote id or identifier |
| conditions on a step | `detect()` and plain PHP `if` |

Files of the types `yml`, `sql`, `<engine>.sql` and `ini` are found, ordered and checked, but the runner refuses
them in 6.0.15. There is no converter of Kaliop YAML yet. YAML migrations will have their own pages when the YAML
engine lands.

## Steps

### 1. Read the Kaliop step

```yaml
-
    type: content_type
    mode: create
    content_type_group: Content
    identifier: news_article
    name: News article
    name_pattern: <title>
    attributes:
        - { type: ezstring, identifier: title, name: Title, required: true }
-
    type: content
    mode: create
    content_type: news_article
    parent_location: 2
    remote_id: news-welcome
    attributes:
        title: Welcome
-
    type: role
    mode: update
    name: Anonymous
    add_policies:
        - { module: content, function: read }
```

### 2. Write it as a PHP migration

```bash
./console exp:migration:generate --skeleton news_article_from_kaliop --apply
```

```php
<?php
// description: The class news_article, a first article and read access for Anonymous (from the Kaliop YAML)
namespace Site\Migrations;

final class NewsArticleFromKaliop20261101150000 extends \expMigrationPhp
{
    public function plan( $context )
    {
        return array( 'create the class news_article in the group Content', 'publish the article Welcome below node 2',
                      'let Anonymous read content' );
    }

    public function up( $context )
    {
        $done = array();
        $userId = \eZUser::currentUserID();
        $language = \eZContentObject::defaultLanguage();
        if ( !\eZContentClass::fetchByIdentifier( 'news_article' ) )
        {
            $class = \eZContentClass::create( $userId, array(
                'version' => \eZContentClass::VERSION_STATUS_DEFINED, 'identifier' => 'news_article',
                'remote_id' => 'site-class-news-article', 'contentobject_name' => '<title>', 'is_container' => 0,
                'name' => 'News article' ), $language );
            $class->store();
            $attribute = \eZContentClassAttribute::create( (int)$class->attribute( 'id' ), 'ezstring', array(
                'version' => \eZContentClass::VERSION_STATUS_DEFINED, 'identifier' => 'title', 'name' => 'Title',
                'placement' => 1, 'is_required' => 1, 'is_searchable' => 1, 'can_translate' => 1 ), $language );
            $attribute->dataType()->initializeClassAttribute( $attribute );
            $attribute->store();
            $group = \eZContentClassGroup::fetchByName( 'Content' );
            if ( $group )
                \eZContentClassClassGroup::create( $class->attribute( 'id' ), \eZContentClass::VERSION_STATUS_DEFINED,
                                                   $group->attribute( 'id' ), $group->attribute( 'name' ) )->store();
            $done[] = 'created the class news_article';
        }
        if ( !\eZContentObject::fetchByRemoteID( 'news-welcome' ) )
        {
            $object = \eZContentFunctions::createAndPublishObject( array(
                'parent_node_id' => 2, 'class_identifier' => 'news_article', 'creator_id' => $userId,
                'language' => $language, 'remote_id' => 'news-welcome',
                'attributes' => array( 'title' => 'Welcome' ) ), false );
            if ( !$object instanceof \eZContentObject )
                throw new \RuntimeException( 'the article Welcome could not be published' );
            $done[] = 'published the article Welcome';
        }
        $role = \eZRole::fetchByName( 'Anonymous' );
        if ( !$role instanceof \eZRole )
            throw new \RuntimeException( 'there is no role Anonymous' );
        $role->appendPolicy( 'content', 'read' );
        \eZRole::expireCache();
        $done[] = 'Anonymous may read content';
        return $done;
    }
}
```

What changed against the YAML: each step became a block of `up()` that first asks whether its result exists, the
`parent_location` became a node id of this installation (look it up by remote id where it differs), and the
reference between steps became a remote id. Split a long Kaliop file into several migrations, ordered with
`requires:`. More on each part: [Create or change a content class](create-or-change-a-content-class.md),
[Add roles and policies](add-roles-and-policies.md), [Seed content for a new site](seed-content-for-a-new-site.md).

### 3. Check and run

```bash
./console exp:migration:check
./console exp:migration:dry-run --simulate --diff     # PostgreSQL and SQLite
./console exp:migration:migrate --apply
```

## Check it

The class, the article and the policy exist; `./console exp:migration:status` shows the migration `done`. A second
`--apply` does nothing.

## Roll it back

This migration has no `down()`: it publishes content that editors may build on. The way back is the caption taken
before the run: `./console exp:migration:rollback <run-id> --apply`.

## Next step

[Coming from SQL update files](coming-from-sql-update-files.md), or [Testing migrations](testing-migrations.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Coming from Doctrine Migrations](coming-from-doctrine-migrations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
