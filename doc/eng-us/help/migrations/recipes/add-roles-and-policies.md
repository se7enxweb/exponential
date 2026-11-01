---
title: "Add roles and policies"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 8
summary: "A migration that creates the role News editor with its policies, limits two of them to the class news_article, assigns it to the user group Editors found by remote id, and removes role, policies and assignment in down()."
keywords: migration, migrations, roles, policies, permissions, access control, eZRole, appendPolicy, assignToUser, limitation, class limitation, user group, remote id, expireCache, exp:migration:migrate, Rollen, Richtlinien
type: help
---
# Add roles and policies

This recipe is for developers and administrators who change who may do what. Roles set up by hand in Setup > Roles
and policies drift apart between installations: a policy added on staging is forgotten on production. Written as a
migration, the role is created the same way everywhere, reviewed like code, and every run is in the audit log. The
example creates the role **News editor** for the class of
[Create or change a content class](create-or-change-a-content-class.md) and gives it to the group Editors.

## What you'll have at the end

- the migration `20261101160000_news_editor_role.php`;
- the role News editor with the policies `content/read`, `content/create` and `content/edit` (both limited to the
  class `news_article`) and `content/versionread`;
- the role assigned to the user group Editors on every installation, found by its remote id;
- a `down()` that removes the role with its policies and assignment.

## Before you start

- Exponential 6.0.15 with `6.0.15-migration-tables` done; the class `news_article` exists.
- The user group you assign to has a known remote id. The group Editors of a new installation has
  `3c160cca19fb135f83bd02d911f04db2`; for a group of your own, look it up once (Setup > Users, the group's
  details) and use it in the migration. Never use the object id: it differs between installations.
- Run as the web server user (as root, add `--allow-root-user`).

## Steps

### 1. Generate and fill in

```bash
./console exp:migration:generate --skeleton news_editor_role --description="The role News editor: read everything, create and edit news articles, assigned to the group Editors" --apply
```

Add `// requires: 20261101110000_news_article_class` and the methods below.

### 2. Dry run

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-160233-4f0c8a, environment development, database sqlite (transactions: full)

  would-apply 20261101160000_news_editor_role              site               The role News editor: read everything, create and edit news articles, assigned to the group Editors
              would create the role "News editor"
              would add the policy content/read
              would add the policy content/create (classes: news_article)
              would add the policy content/edit (classes: news_article)
              would add the policy content/versionread
              would assign the role to the user group with the remote id 3c160cca19fb135f83bd02d911f04db2

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

### 3. Run

```bash
./console exp:migration:migrate --apply
```

```text
  applied     20261101160000_news_editor_role              site               The role News editor: read everything, create and edit news articles, assigned to the group Editors
              created the role "News editor"
              added content/read
              added content/create
              added content/edit
              added content/versionread
              assigned it to the group Editors
```

## The migration

```php
<?php
// description: The role News editor: read everything, create and edit news articles, assigned to the group Editors
// requires: 20261101110000_news_article_class
// author: Site team
namespace Site\Migrations;

final class NewsEditorRole20261101160000 extends \expMigrationPhp
{
    const ROLE = 'News editor';
    /** the user group Editors of a new installation, found by remote id: object ids differ between installations */
    const GROUP_REMOTE_ID = '3c160cca19fb135f83bd02d911f04db2';

    /** @return array[] module, function, limitations; class limitations by identifier, resolved in up() */
    public static function policies()
    {
        return array(
            array( 'content', 'read', array() ),
            array( 'content', 'create', array( 'Class' => array( 'news_article' ) ) ),
            array( 'content', 'edit', array( 'Class' => array( 'news_article' ) ) ),
            array( 'content', 'versionread', array() ),
        );
    }

    private static function group()
    {
        $group = \eZContentObject::fetchByRemoteID( self::GROUP_REMOTE_ID );
        if ( !$group instanceof \eZContentObject )
            throw new \RuntimeException( 'there is no user group with the remote id ' . self::GROUP_REMOTE_ID );
        return $group;
    }

    /** class identifiers to class ids, the way the role editor stores them */
    private static function resolve( array $limitations )
    {
        if ( isset( $limitations['Class'] ) )
        {
            $ids = array();
            foreach ( $limitations['Class'] as $identifier )
            {
                $class = \eZContentClass::fetchByIdentifier( $identifier );
                if ( !$class instanceof \eZContentClass )
                    throw new \RuntimeException( "there is no class $identifier" );
                $ids[] = (int)$class->attribute( 'id' );
            }
            $limitations['Class'] = $ids;
        }
        return $limitations;
    }

    public function plan( $context )
    {
        $lines = array( 'create the role "' . self::ROLE . '"' );
        foreach ( self::policies() as $policy )
            $lines[] = "add the policy {$policy[0]}/{$policy[1]}" . ( $policy[2] ? ' (classes: ' . implode( ', ', $policy[2]['Class'] ) . ')' : '' );
        $lines[] = 'assign the role to the user group with the remote id ' . self::GROUP_REMOTE_ID;
        return $lines;
    }

    public function detect( $context )
    {
        return \eZRole::fetchByName( self::ROLE ) instanceof \eZRole ? \expMigration::DONE : null;
    }

    public function up( $context )
    {
        $group = self::group();
        $role = \eZRole::create( self::ROLE );
        $role->store();
        $done = array( 'created the role "' . self::ROLE . '"' );
        foreach ( self::policies() as $policy )
        {
            $role->appendPolicy( $policy[0], $policy[1], self::resolve( $policy[2] ) );
            $done[] = "added {$policy[0]}/{$policy[1]}";
        }
        $role->assignToUser( (int)$group->attribute( 'id' ) );
        $done[] = 'assigned it to the group ' . $group->attribute( 'name' );
        \eZRole::expireCache();
        \eZUser::cleanupCache();
        return $done;
    }

    public function down( $context )
    {
        $role = \eZRole::fetchByName( self::ROLE );
        if ( !$role instanceof \eZRole )
            return array( 'the role "' . self::ROLE . '" is gone already' );
        $role->removeThis();
        \eZRole::expireCache();
        \eZUser::cleanupCache();
        return array( 'removed the role "' . self::ROLE . '", its policies and its assignments' );
    }
}
```

- **Limitations by identifier, resolved at run time.** The class limitation stores class ids, which differ per
  installation; `resolve()` turns `news_article` into this installation's id. Do the same for sections (by
  identifier), subtrees (by the remote id of the node's object) and states.
- **`eZRole::expireCache()` and `eZUser::cleanupCache()`** make signed-in users get the new rights on their next
  request instead of after the cache expires.
- **`detect()`** counts the migration as done where a role of that name exists, so an installation where an
  administrator created it by hand is left alone. Compare the policies of such a role with the migration's before
  you rely on it.
- **To change an existing role**, write a new migration that fetches it with `eZRole::fetchByName()` and calls
  `appendPolicy()` or `removePolicy( 'content', 'edit' )`; check the role's policies first so a second run adds
  nothing twice (the kernel's own policy steps do exactly this, see
  [Upgrade steps for extensions](../../../guides/upgrade-steps-for-extensions.md)).
- Role changes are audited (`access.role.*` events) like changes in the administration, as the run's user.

## Check it

- Setup > Roles and policies lists **News editor** with four policies and the assignment to Editors.
- Sign in as a member of Editors: creating a news article works, editing a folder does not.
- `./console exp:migration:status`: the migration is `done`.

## Roll it back

```bash
./console exp:migration:rollback 20261101160000_news_editor_role --apply
```

```text
  undone      20261101160000_news_editor_role              site               The role News editor: read everything, create and edit news articles, assigned to the group Editors
              removed the role "News editor", its policies and its assignments
```

Policies take effect at once; a rollback takes the rights away from users who are signed in, too.

## Next step

Give a new site its starting content: [Seed content for a new site](seed-content-for-a-new-site.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Policies, views and events of migrations](../policies-views-and-events.md), [The audit trail of runs](the-audit-trail-of-runs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
