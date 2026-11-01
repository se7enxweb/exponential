---
title: "Rollen und Richtlinien hinzufügen"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 8
summary: "Eine Migration, die die Rolle News editor mit ihren Richtlinien anlegt, zwei davon auf die Klasse news_article begrenzt, sie der über ihre Remote-ID gefundenen Benutzergruppe Editors zuweist und Rolle, Richtlinien und Zuweisung in down() wieder entfernt."
keywords: Migration, Migrationen, Rollen, Richtlinien, Rechte, Zugriffsrechte, Begrenzung, Benutzergruppe, Remote-ID, migration, migrations, roles, policies, permissions, access control, eZRole, appendPolicy, assignToUser, limitation, class limitation, user group, remote id, expireCache, exp:migration:migrate
type: help
---
# Rollen und Richtlinien hinzufügen

Dieses Rezept richtet sich an Entwickler und Administratoren, die ändern, wer was darf. Von Hand in Setup > Rollen
und Richtlinien eingerichtete Rollen laufen zwischen Installationen auseinander: Eine auf Staging ergänzte Richtlinie
wird auf Produktion vergessen. Als Migration wird die Rolle überall gleich angelegt, wie Code geprüft, und jeder Lauf
steht im Audit-Log. Das Beispiel legt die Rolle **News editor** für die Klasse aus
[Eine Inhaltsklasse anlegen oder ändern](eine-inhaltsklasse-anlegen-oder-aendern.md) an und gibt sie der Gruppe
Editors.

## Was Sie am Ende haben

- die Migration `20261101160000_news_editor_role.php`;
- die Rolle News editor mit den Richtlinien `content/read`, `content/create` und `content/edit` (beide auf die Klasse
  `news_article` begrenzt) und `content/versionread`;
- die Rolle auf jeder Installation der Benutzergruppe Editors zugewiesen, gefunden über deren Remote-ID;
- ein `down()`, das die Rolle mit ihren Richtlinien und ihrer Zuweisung entfernt.

## Bevor Sie beginnen

- Exponential 6.0.15 mit erledigtem `6.0.15-migration-tables`; die Klasse `news_article` existiert.
- Die Benutzergruppe, der Sie zuweisen, hat eine bekannte Remote-ID. Die Gruppe Editors einer neuen Installation hat
  `3c160cca19fb135f83bd02d911f04db2`; für eine eigene Gruppe schlagen Sie sie einmal nach (Setup > Benutzer, die
  Details der Gruppe) und tragen sie in die Migration ein. Nehmen Sie nie die Objekt-ID: Sie unterscheidet sich
  zwischen Installationen.
- Ausführen als Benutzer des Webservers (als root mit `--allow-root-user`).

## Schritte

### 1. Erzeugen und füllen

```bash
./console exp:migration:generate --skeleton news_editor_role --description="The role News editor: read everything, create and edit news articles, assigned to the group Editors" --apply
```

Ergänzen Sie `// requires: 20261101110000_news_article_class` und die Methoden unten.

### 2. Probelauf

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

### 3. Ausführen

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

## Die Migration

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

- **Begrenzungen über Bezeichner, zur Laufzeit aufgelöst.** Die Klassenbegrenzung speichert Klassen-IDs, die je
  Installation anders sind; `resolve()` macht aus `news_article` die ID dieser Installation. Machen Sie es bei
  Sektionen (über den Bezeichner), Teilbäumen (über die Remote-ID des Objekts des Knotens) und Zuständen genauso.
- **`eZRole::expireCache()` und `eZUser::cleanupCache()`** sorgen dafür, dass angemeldete Benutzer die neuen Rechte
  mit der nächsten Anfrage bekommen und nicht erst, wenn der Cache abläuft.
- **`detect()`** zählt die Migration als erledigt, wo es eine Rolle dieses Namens gibt; eine Installation, auf der ein
  Administrator sie von Hand angelegt hat, bleibt unberührt. Vergleichen Sie die Richtlinien einer solchen Rolle mit
  denen der Migration, bevor Sie sich darauf verlassen.
- **Um eine bestehende Rolle zu ändern**, schreiben Sie eine neue Migration, die sie mit `eZRole::fetchByName()` holt
  und `appendPolicy()` oder `removePolicy( 'content', 'edit' )` aufruft; prüfen Sie vorher die Richtlinien der Rolle,
  damit ein zweiter Lauf nichts doppelt anlegt (die Richtlinien-Schritte des Kernels machen genau das, siehe
  [Upgrade-Schritte für Erweiterungen](../../../anleitungen/upgrade-schritte-fuer-erweiterungen.md)).
- Änderungen an Rollen werden auditiert (Ereignisse `access.role.*`) wie Änderungen in der Administration, als
  Benutzer des Laufs.

## Prüfen

- Setup > Rollen und Richtlinien zeigt **News editor** mit vier Richtlinien und der Zuweisung an Editors.
- Melden Sie sich als Mitglied von Editors an: Einen News-Artikel anlegen geht, einen Ordner bearbeiten nicht.
- `./console exp:migration:status`: Die Migration ist `done`.

## Zurücknehmen

```bash
./console exp:migration:rollback 20261101160000_news_editor_role --apply
```

```text
  undone      20261101160000_news_editor_role              site               The role News editor: read everything, create and edit news articles, assigned to the group Editors
              removed the role "News editor", its policies and its assignments
```

Richtlinien wirken sofort; eine Rücknahme entzieht die Rechte auch angemeldeten Benutzern.

## Nächster Schritt

Geben Sie einer neuen Site ihre Startinhalte: [Inhalte für eine neue Site anlegen](inhalte-fuer-eine-neue-site-anlegen.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Richtlinien, Ansichten und Ereignisse der Migrationen](../richtlinien-ansichten-und-ereignisse.md), [Die Audit-Spur der Läufe](die-audit-spur-der-laeufe.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
