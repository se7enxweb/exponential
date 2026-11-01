---
title: "Migrationen in PHP schreiben"
audience: php-developer, developer
category: migrations
order: 510
summary: Eine praktische Seite für PHP-Entwickler - die Migrationsklasse und ihr Namensraum, die Kontext-API, Referenzen zwischen Migrationen, Ausnahmen und Transaktionen je Engine, die Kernel-APIs einer Migration, ein vollständiges Beispiel, das einen Ordner veröffentlicht und eine Rolle anlegt, und ein PHPUnit-Test auf SQLite.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Auslieferung, PHP-Entwickler, Kontext, Referenz, Rolle, Richtlinie, migration, migrations, schema change, database change, deploy, exp:migration:migrate, PHP developer, expMigrationPhp, expMigrationContext, plan, up, down, detect, setReference, eZContentFunctions, eZRole, eZContentClass, eZINI, expKernelTableSchema, PHPUnit, SQLite
featured: true
type: help
---
# Migrationen in PHP schreiben

Diese Seite ist für PHP-Entwickler, die die Migrationen einer Site oder einer Erweiterung schreiben. In Exponential
6.0.15 ist eine Migration eine PHP-Klasse: Datenbank, Benutzer und Umgebung des Laufs bekommen Sie von einem
Kontextobjekt, die Änderung machen Sie mit den APIs des Kernels, und Sie geben zurück, was Sie getan haben. Alles
unten läuft auf PHP 7.4 und jedem neueren PHP.

## Die Klasse

```bash
./console exp:migration:generate --skeleton press_releases --description="The folder Press releases" --apply
```

schreibt `migrations/20261101100000_press_releases.php` mit einem Kopf, dem Namensraum `Site\Migrations` und einer
Klasse `PressReleases20261101100000`, die `\expMigrationPhp` erweitert. Das Gerüst einer Erweiterung
(`--source=extension:<name>`) bekommt den Namensraum `Extension\<Name>\Migrations`. Die Regeln:

- eine nicht abstrakte Klasse je Datei, die `\expMigrationPhp` erweitert, mit einem Namen, den keine andere Datei
  deklariert;
- kein Code außerhalb der Klasse: der Runner lädt die Datei mit `require`;
- der führende Backslash vor Kernel-Klassen (`\eZRole`, `\expMigration`), weil die Datei einen Namensraum hat;
- PHP-7.4-Syntax: kein `match`, keine Constructor Property Promotion, keine benannten Argumente, kein `readonly`.

| Methode | Aufgerufen von | Gibt zurück |
|---|---|---|
| `plan( $context )` | dem Probelauf, der Statusseite | was `up()` tun würde, ein String je Zeile; darf nicht schreiben |
| `up( $context )` | einem Lauf mit `--apply`, der Simulation | was es getan hat; wirft bei einem Fehler |
| `down( $context )` | `exp:migration:rollback <id>` | was es zurückgenommen hat; weglassen, wenn sich die Änderung nicht selbst rückgängig machen lässt |
| `detect( $context )` | jeder Abfrage des Zustands | `\expMigration::DONE`, `PENDING`, `NOT_NEEDED` oder `null` (die Aufzeichnung entscheidet) |
| `transactional()` | dem Runner | `false` für eine Migration, die selbst committet |

## Der Kontext

| Aufruf | Liefert |
|---|---|
| `$context->db()` | die Verbindung des Laufs (`eZDBInterface`), in der Transaktion des Laufs |
| `$context->engine()` | `mysql`, `postgresql`, `sqlite`, `oracle` oder `mongodb` |
| `$context->isApply()` | `true` in einem Lauf, der schreibt, `false` in einem Probelauf |
| `$context->environment()` | `development`, `test`, `staging`, `production` oder was `--environment` sagt |
| `$context->runId()`, `$context->user()`, `$context->userLogin()` | ID und Benutzer des Laufs |
| `$context->setReference( $name, $value )`, `reference( $name, $default )` | Werte für spätere Migrationen desselben Laufs |
| `$context->log( $level, $text )` | eine Zeile im Laufprotokoll und in `var/log/migration.log` |
| `$context->option( $name )` | eine Option des Laufs (`force`, `reason`, `only` ...) |

Referenzen leben einen Lauf lang. Eine Migration, die in einem späteren Lauf läuft, findet, was sie braucht, wieder
über Remote-ID oder Bezeichner, nie über eine Referenz.

## Ausnahmen und Transaktionen

Werfen Sie eine beliebige Ausnahme aus `up()`, damit die Migration scheitert. Der Runner rollt ihre Transaktion
zurück, zeichnet sie als `failed` auf, hält den Lauf an, bewahrt den gescheiterten Zustand in einem Sicherungspunkt
und stellt den Sicherungspunkt von vor dem Lauf wieder her.

| Engine | Was die Transaktion zurücknimmt |
|---|---|
| PostgreSQL, SQLite | alles: Schema und Daten |
| MySQL/MariaDB, Oracle | nur Daten; `CREATE`, `ALTER` und `DROP` werden sofort committet |
| MongoDB | nichts |

Legen Sie deshalb auf MySQL/MariaDB eine Schemaänderung und die Daten, die davon abhängen, in getrennte Migrationen,
und schreiben Sie `up()` so, dass es einen halb erledigten Zustand verträgt.

## Die Kernel-APIs, die Sie nutzen

| Änderung | API |
|---|---|
| eine eigene Tabelle | `\expKernelTableSchema::install( array( $table ), $context->db(), $schema, $messages )`, `::exists()` |
| eine Spalte oder ein Index | `\expUpgradeCatalog::hasColumn()`, `::hasIndex()`, `::execute()` mit SQL vom Schema-Handler der Engine |
| Inhalte | `\eZContentFunctions::createAndPublishObject()`, `::updateAndPublishObject()`, `\eZContentObject::fetchByRemoteID()` |
| eine Inhaltsklasse | `\eZContentClass::create()`, `\eZContentClassAttribute::create()`, `\eZContentClassClassGroup::create()` |
| Rollen und Richtlinien | `\eZRole::fetchByName()`, `::create()`, `appendPolicy()`, `assignToUser()`, `\eZRole::expireCache()` |
| Einstellungen | `\eZINI::instance( '<datei>.ini.append.php', 'settings/override', null, false, null, true )`, `setVariable()`, `save()` |

Die [Rezepte](../migrationen/rezepte/README.md) zeigen jede davon in einer vollständigen Migration.

## Ein vollständiges Beispiel

Der Ordner Press releases unter der Inhaltswurzel, auf jeder Installation über seine Remote-ID gefunden, und eine
Rolle, die ihn lesen darf:

```php
<?php
// description: The folder Press releases and the role Press readers that may read it
// author: Site team
namespace Site\Migrations;

final class PressReleases20261101100000 extends \expMigrationPhp
{
    const REMOTE_ID = 'site-press-releases-folder';
    const ROLE = 'Press readers';

    public function plan( $context )
    {
        return array( 'publish the folder Press releases below node 2 (remote id ' . self::REMOTE_ID . ')',
                      'create the role ' . self::ROLE . ' with content/read on that folder' );
    }

    public function detect( $context )
    {
        $done = \eZContentObject::fetchByRemoteID( self::REMOTE_ID ) instanceof \eZContentObject
             && \eZRole::fetchByName( self::ROLE ) instanceof \eZRole;
        return $done ? \expMigration::DONE : null;
    }

    public function up( $context )
    {
        $done = array();
        $object = \eZContentObject::fetchByRemoteID( self::REMOTE_ID );
        if ( !$object instanceof \eZContentObject )
        {
            $root = \eZContentObjectTreeNode::fetch( 2 );
            if ( !$root instanceof \eZContentObjectTreeNode )
                throw new \RuntimeException( 'there is no content root (node 2)' );
            $object = \eZContentFunctions::createAndPublishObject( array(
                'parent_node_id' => 2,
                'language' => $root->attribute( 'object' )->attribute( 'initial_language_code' ),
                'class_identifier' => 'folder',
                'creator_id' => \eZUser::currentUserID(),
                'remote_id' => self::REMOTE_ID,
                'attributes' => array( 'name' => 'Press releases' ),
            ), false );
            if ( !$object instanceof \eZContentObject )
                throw new \RuntimeException( 'the folder Press releases could not be published' );
            $done[] = 'published the folder Press releases (object ' . $object->attribute( 'id' ) . ')';
        }
        $node = $object->mainNode();
        $context->setReference( 'press_releases_node', (int)$node->attribute( 'node_id' ) );

        if ( !\eZRole::fetchByName( self::ROLE ) instanceof \eZRole )
        {
            $role = \eZRole::create( self::ROLE );
            $role->store();
            $role->appendPolicy( 'content', 'read', array( 'Subtree' => array( $node->attribute( 'path_string' ) ) ) );
            \eZRole::expireCache();
            $done[] = 'created the role ' . self::ROLE . ' with content/read on ' . $node->attribute( 'path_string' );
        }
        return $done;
    }

    public function down( $context )
    {
        $done = array();
        $role = \eZRole::fetchByName( self::ROLE );
        if ( $role instanceof \eZRole )
        {
            $role->removeThis();
            \eZRole::expireCache();
            $done[] = 'removed the role ' . self::ROLE;
        }
        $object = \eZContentObject::fetchByRemoteID( self::REMOTE_ID );
        if ( $object instanceof \eZContentObject )
        {
            $object->purge();
            $done[] = 'removed the folder Press releases';
        }
        return $done;
    }
}
```

Worauf Sie achten sollten: `up()` prüft vor dem Anlegen, damit ein zweiter Lauf nach einem Teilfehler die Änderung
vervollständigt; `detect()` lässt eine Installation, die beide Teile schon hat, die Migration überspringen; der
Ordner wird als Benutzer des Laufs angelegt (`\eZUser::currentUserID()`); eine spätere Migration desselben Laufs
liest die Knoten-ID mit `$context->reference( 'press_releases_node' )`.

```bash
./console exp:migration:check
./console exp:migration:dry-run --simulate --diff     # PostgreSQL und SQLite
./console exp:migration:migrate --apply
./console exp:migration:rollback 20261101100000_press_releases --apply
```

## Testen mit PHPUnit auf SQLite

Laden Sie die Datei mit den Klassen des Kernels und führen Sie sie auf einer Wegwerf-SQLite-Datenbank aus, wie es
`tests/tests/kernel/classes/migration/expMigrationRunnerSqliteTest.php` tut:

```php
$locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => __DIR__ . '/migrations' ) ) );
$errors = array();
$migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
$context = new expMigrationContext( $db, true, array( 'environment' => 'testing', 'no-mirror' => true,
                                                      'user' => eZUser::fetchByName( 'admin' ) ) );
$report = ( new expMigrationRunner( $context ) )->run( $migrations );
$this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
```

Führen Sie in einem Test `up()`, dann `down()`, dann wieder `up()` aus: das beweist, dass sich die Migration
zurücknehmen und erneut anwenden lässt. Testen Sie `plan()` ohne Datenbank, indem Sie es aus reinen Funktionen bauen.

## Wie es weitergeht

- [PHP-Migrationen](../../anleitungen/migrationen-php.md): die vollständige Referenz der Klasse und des Kontexts
- [Ein umkehrbares down() schreiben](../migrationen/rezepte/ein-umkehrbares-down-schreiben.md)
- [Eine Inhaltsklasse anlegen oder ändern](../migrationen/rezepte/eine-inhaltsklasse-anlegen-oder-aendern.md) und
  [Rollen und Richtlinien hinzufügen](../migrationen/rezepte/rollen-und-richtlinien-hinzufuegen.md)
- [Migrationen testen](../migrationen/rezepte/migrationen-testen.md)
- Jetzt beginnen: [das Tutorial](../../anleitungen/migrationen-tutorial.md)

## Verwandte Seiten

- [Migrationen: der Einstieg](../migrationen/README.md)
- [Migrationen: die Anleitung](../../anleitungen/migrationen.md)
- [Migrationen: das Tutorial](../../anleitungen/migrationen-tutorial.md)
- [PHP-Migrationen](../../anleitungen/migrationen-php.md)
- [Migrationen in der Administration](../migrationen/in-der-administration.md)
- [Die Migrationsbefehle](../migrationen/befehle.md)
- [Die Migrationseinstellungen](../migrationen/einstellungen.md)
- [Das Migrationsdateiformat](../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../migrationen/wenn-eine-migration-scheitert.md)
- [Fehlerbehebung bei Migrationen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen/migrationen-oder-umzug-einer-site.md)
- [PHP-Entwickler: hier beginnen](php-entwickler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
