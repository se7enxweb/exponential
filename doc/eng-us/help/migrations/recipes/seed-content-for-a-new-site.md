---
title: "Seed content for a new site"
audience: developer, software-engineer, php-developer, website-creator, admin
category: migrations
order: 9
summary: "A migration that publishes the starting content of a site - a folder and its first articles - by remote id, only what is missing, in the language of the content root, so every new installation and every developer's copy starts the same."
keywords: migration, migrations, seed content, starting content, default content, new site, createAndPublishObject, remote id, idempotent, fixtures, demo content, developer setup, exp:migration:migrate, Startinhalte
type: help
---
# Seed content for a new site

This recipe is for developers who set up a new site and want every installation of it, a colleague's development
copy included, to start with the same content: the sections, the first pages, the texts a template needs to show
something. A seed migration publishes them once per installation, by remote id, and the class, role and table
migrations it depends on run before it. The example publishes the folder **News** with three articles.

Copying a whole finished site with all its content to another installation is a different task:
[a site package](../../../guides/packages.md), or see
[Migrations versus migrating a site](../migrations-versus-migrating-a-site.md).

## What you'll have at the end

- the migration `20261101170000_seed_news_content.php`, ordered after the class and its Subtitle attribute;
- the folder News below the content root and three news articles in it, with fixed remote ids
  (`site-seed-news-*`) that later migrations and templates can find;
- a migration that publishes only what is missing, and a `down()` that removes what it published.

## Before you start

- Exponential 6.0.15 with `6.0.15-migration-tables` done.
- The class `news_article` with `subtitle`
  ([Create or change a content class](create-or-change-a-content-class.md),
  [Add a class attribute](add-a-class-attribute.md)).
- Run as the web server user (as root, add `--allow-root-user`); the content is owned by the run's user
  (`[RunSettings] RunAsUser`, `admin` by default, or `--login=<login>`).

## Steps

### 1. Generate and fill in

```bash
./console exp:migration:generate --skeleton seed_news_content --description="The starting content of the site: the folder News with three articles" --apply
```

Add the header line `// requires: 20261101110000_news_article_class, 20261101120000_news_article_subtitle` and
the methods below.

### 2. Run the whole chain on a fresh installation

On a new development installation all the site's migrations are pending, and one run applies them in order. Here the site has four:

```bash
./console exp:migration:migrate --apply --no-commit
```

```text
Exponential migrations: run
Run 20261101-170412-c6d2e9, environment development, database sqlite (transactions: full), as admin

  applied     20261101090000_news_source_table             site               The table exp_news_source: where the news articles come from
              created exp_news_source
              wrote the source newsroom
  applied     20261101110000_news_article_class            site               The content class News article (news_article) in the class group Content
              created the class news_article (id 48) with 4 attributes
              added it to the class group Content
  applied     20261101120000_news_article_subtitle         site               The attribute Subtitle (subtitle, ezstring) of the class news_article, after Title
              added the attribute subtitle to news_article at position 2
              gave the 0 existing objects the attribute
  applied     20261101170000_seed_news_content             site               The starting content of the site: the folder News with three articles
              published folder "News" (object 57)
              published news_article "Welcome to our news" (object 58)
              published news_article "Meet the team" (object 59)
              published news_article "Send us your story" (object 60)

Captions: pre 20261101-170412, post 20261101-170415
Repository: no commit, push disabled
Summary: 4 migration(s): 4 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 4 applied
PASS
```


### 3. Run it on an installation that has some of it

On an installation where an editor created the folder News with the same remote id before, the plan lists only the
articles; `ensure()` reuses what exists and publishes the rest.

## The migration

```php
<?php
// description: The starting content of the site: the folder News with three articles
// requires: 20261101110000_news_article_class, 20261101120000_news_article_subtitle
// author: Site team
namespace Site\Migrations;

final class SeedNewsContent20261101170000 extends \expMigrationPhp
{
    const FOLDER = 'site-seed-news-folder';

    /** @return array[] remote id => attributes of the articles, in their order */
    public static function articles()
    {
        return array(
            'site-seed-news-welcome' => array( 'title' => 'Welcome to our news', 'subtitle' => 'What you will read here',
                                               'intro' => 'News from the team, every week.', 'source' => 'Newsroom' ),
            'site-seed-news-team' => array( 'title' => 'Meet the team', 'subtitle' => 'Who writes the news',
                                            'intro' => 'Five people, one newsroom.', 'source' => 'Newsroom' ),
            'site-seed-news-contact' => array( 'title' => 'Send us your story', 'subtitle' => 'How to reach the newsroom',
                                               'intro' => 'Tips and stories are welcome.', 'source' => 'Newsroom' ),
        );
    }

    private static function language()
    {
        $root = \eZContentObjectTreeNode::fetch( 2 );
        if ( !$root instanceof \eZContentObjectTreeNode )
            throw new \RuntimeException( 'there is no content root (node 2)' );
        return $root->attribute( 'object' )->attribute( 'initial_language_code' );
    }

    /** @return \eZContentObject the object of a remote id, published now when it is missing */
    private static function ensure( $remoteId, $parentNodeId, $class, array $attributes, $language, array &$done )
    {
        $object = \eZContentObject::fetchByRemoteID( $remoteId );
        if ( $object instanceof \eZContentObject )
            return $object;
        $object = \eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $parentNodeId,
            'class_identifier' => $class,
            'creator_id' => \eZUser::currentUserID(),
            'remote_id' => $remoteId,
            'language' => $language,
            'attributes' => $attributes,
        ), false );
        if ( !$object instanceof \eZContentObject )
            throw new \RuntimeException( "$remoteId could not be published" );
        $done[] = "published $class \"" . reset( $attributes ) . '" (object ' . $object->attribute( 'id' ) . ')';
        return $object;
    }

    public function plan( $context )
    {
        $lines = array();
        if ( !\eZContentObject::fetchByRemoteID( self::FOLDER ) )
            $lines[] = 'publish the folder News below the content root';
        foreach ( self::articles() as $remoteId => $attributes )
            if ( !\eZContentObject::fetchByRemoteID( $remoteId ) )
                $lines[] = 'publish the article "' . $attributes['title'] . '" in News';
        return $lines ? $lines : array( 'nothing: the starting content is there' );
    }

    public function up( $context )
    {
        $language = self::language();
        $done = array();
        $folder = self::ensure( self::FOLDER, 2, 'folder', array( 'name' => 'News' ), $language, $done );
        $folderNode = (int)$folder->attribute( 'main_node_id' );
        foreach ( self::articles() as $remoteId => $attributes )
            self::ensure( $remoteId, $folderNode, 'news_article', $attributes, $language, $done );
        $context->setReference( 'news_folder_node', $folderNode );
        return $done ? $done : array( 'nothing to publish' );
    }

    public function down( $context )
    {
        $done = array();
        foreach ( array_merge( array_keys( self::articles() ), array( self::FOLDER ) ) as $remoteId )
        {
            $object = \eZContentObject::fetchByRemoteID( $remoteId );
            if ( $object instanceof \eZContentObject )
            {
                $object->purge();
                $done[] = "removed $remoteId";
            }
        }
        return $done ? $done : array( 'nothing to remove' );
    }
}
```

- **Remote ids, never node or object ids.** Ids differ between installations; the remote id is chosen by you and
  the same everywhere. Later migrations and templates find the content with
  `eZContentObject::fetchByRemoteID( 'site-seed-news-folder' )`.
- **Only what is missing.** A second run, or a run after an editor made some of it by hand, publishes nothing twice.
- **The language of the content root**, so the seed works on an installation whose main language is not English.
- **`setReference( 'news_folder_node', ... )`** leaves the folder's node id for migrations later in the same run
  (a reference lives for one run only).
- **Publishing without notifications** (`false`): subscribers get no mail about seed content.
- For a seed that must only reach development and test copies (demo users, sample orders), give the header
  `environments: development, test`; see [Per-environment migrations](per-environment-migrations.md).
- Keep personal data out of seed migrations: they are committed to git and pushed.

## Check it

- The content tree shows News with three articles; the articles have a Subtitle.
- `./console exp:migration:migrate` lists every migration as `done` and changes nothing.
- On a second fresh installation the same remote ids exist; the object ids may differ.

## Roll it back

```bash
./console exp:migration:rollback 20261101170000_seed_news_content --apply
```

```text
  undone      20261101170000_seed_news_content             site               The starting content of the site: the folder News with three articles
              removed site-seed-news-welcome
              removed site-seed-news-team
              removed site-seed-news-contact
              removed site-seed-news-folder
```

`purge()` removes the objects for good, without the trash; anything an editor added below News would go with the
folder. On an installation where editors have worked with the content, restore the run's caption instead, or leave
the content alone.

## Next step

Limit migrations to some installations: [Per-environment migrations](per-environment-migrations.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Create or change a content class](create-or-change-a-content-class.md), [Team workflow with git](team-workflow-with-git.md), [Packages](../../../guides/packages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
