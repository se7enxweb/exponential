---
title: "Umstieg vom Kaliop-Migrationsbundle"
audience: developer, dxp-developer, cms-developer, software-engineer
category: migrations
order: 24
summary: Für Entwickler, die das Kaliop eZ Migration Bundle (ezmigrationbundle) in eZ-Platform- und Ibexa-Projekten genutzt haben - seine Befehle, Dateien und Tabelle in Exponential 6, und ein YAML-Schritt für Inhalte, Klasse und Rolle von Hand als PHP-Migration geschrieben.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Umstieg, Begriffe im Vergleich, migration, migrations, schema change, database change, Kaliop, kaliop, ezmigrationbundle, eZ Migration Bundle, kaliop:migration:migrate, kaliop:migration:generate, kaliop:migration:status, kaliop_migrations, YAML, Ibexa, eZ Platform, concept map
featured: true
type: help
---
# Umstieg vom Kaliop-Migrationsbundle

Viele eZ-Platform- und Ibexa-Projekte hielten ihr Inhaltsmodell und ihre Inhalte in Migrationen des Kaliop eZ
Migration Bundle (`kaliop/ezmigrationbundle`): YAML-Dateien mit Schritten wie `type: content_type, mode: create`, PHP-
und SQL-Migrationen, die Befehle `kaliop:migration:*` und die Tabelle `kaliop_migrations`. Exponential 6 hat Migrationen
im Kernel, mit derselben Idee von Dateien, die einmal der Reihe nach laufen. Diese Seite ordnet beides einander zu und
schreibt typische Schritte von Hand um.

## Was Sie am Ende haben

- das Gegenstück in Exponential zu jedem Kaliop-Befehl und Dateityp;
- einen Inhaltstyp, ein Inhaltselement und einen Rollenschritt als eine PHP-Migration;
- ein klares Bild davon, was 6.0.15 noch nicht bietet.

## Bevor Sie beginnen

- Arbeiten Sie [das Tutorial](../../../anleitungen/migrationen-tutorial.md) einmal durch.
- Halten Sie Ihre Kaliop-YAML-Dateien bereit; sie sind die Vorgabe für das, was Sie schreiben.

## Begriffe im Vergleich

| Kaliop eZ Migration Bundle | Exponential 6.0.15 |
|---|---|
| `kaliop:migration:generate` | `./console exp:migration:generate --skeleton <name> --apply` (nur PHP) |
| `kaliop:migration:status` | `./console exp:migration:status` |
| `kaliop:migration:migrate` | `./console exp:migration:migrate --apply` (ohne `--apply` ein Probelauf) |
| `kaliop:migration:migrate --path=...` | `./console exp:migration:migrate --path=<datei oder ordner> --apply` |
| Tabelle `kaliop_migrations` | Tabellen `exp_migration` und `exp_migration_run` |
| Zustände wie todo, done, failed, skipped | Zustände `pending`, `done`, `failed`, `not-needed`, `unknown` |
| Bundle-Ordner `MigrationVersions/` | `migrations/` der Site, `extension/<name>/migrations/`, `migrations/` eines Pakets |
| YAML-Schritte (`content`, `content_type`, `role`, `sql`, ...) | **laufen in 6.0.15 nicht**: schreiben Sie den Schritt in PHP |
| PHP-Migrationen | PHP-Migrationen, die `\expMigrationPhp` erweitern |
| SQL-Migrationen je Datenbank | **laufen in 6.0.15 nicht**: SQL in einer PHP-Migration, je Engine, wo nötig |
| Referenzen (`[reference:...]`) | `$context->setReference()` / `$context->reference()` innerhalb eines Laufs; zwischen Läufen über Remote-ID oder Bezeichner wiederfinden |
| Bedingungen an einem Schritt | `detect()` und ein einfaches PHP-`if` |

Dateien der Typen `yml`, `sql`, `<engine>.sql` und `ini` werden gefunden, geordnet und geprüft, aber vom Runner in
6.0.15 abgelehnt. Einen Konverter für Kaliop-YAML gibt es noch nicht. YAML-Migrationen bekommen eigene Seiten, wenn
die YAML-Engine kommt.

## Schritte

### 1. Den Kaliop-Schritt lesen

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

### 2. Als PHP-Migration schreiben

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

Was sich gegenüber dem YAML geändert hat: jeder Schritt wurde ein Block von `up()`, der zuerst fragt, ob sein Ergebnis
schon existiert, `parent_location` wurde eine Knoten-ID dieser Installation (suchen Sie sie über die Remote-ID, wo sie
abweicht), und die Referenz zwischen den Schritten wurde eine Remote-ID. Teilen Sie eine lange Kaliop-Datei in mehrere
Migrationen, geordnet mit `requires:`. Mehr zu jedem Teil:
[Eine Inhaltsklasse anlegen oder ändern](eine-inhaltsklasse-anlegen-oder-aendern.md),
[Rollen und Richtlinien hinzufügen](rollen-und-richtlinien-hinzufuegen.md),
[Inhalte für eine neue Site anlegen](inhalte-fuer-eine-neue-site-anlegen.md).

### 3. Prüfen und ausführen

```bash
./console exp:migration:check
./console exp:migration:dry-run --simulate --diff     # PostgreSQL und SQLite
./console exp:migration:migrate --apply
```

## Prüfen

Klasse, Artikel und Richtlinie existieren; `./console exp:migration:status` zeigt die Migration als `done`. Ein zweites
`--apply` tut nichts.

## Zurücknehmen

Diese Migration hat kein `down()`: sie veröffentlicht Inhalte, auf denen Redakteure aufbauen können. Der Weg zurück
ist der Sicherungspunkt von vor dem Lauf: `./console exp:migration:rollback <run-id> --apply`.

## Nächster Schritt

[Umstieg von SQL-Update-Dateien](umstieg-von-sql-update-dateien.md) oder [Migrationen testen](migrationen-testen.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und Fehlerbehebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Umstieg von Doctrine Migrations](umstieg-von-doctrine-migrations.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
