---
title: "Migrationen in einer Erweiterung oder einem Paket"
audience: developer, software-engineer, php-developer, integrator
category: migrations
order: 11
summary: Migrationen in einer Erweiterung (extension/<name>/migrations/) oder einem installierten Paket ausliefern, sie mit --source=extension:<name> erzeugen und die Migrationen einer Site mit requires auf sie warten lassen.
keywords: Migration, Migrationen, Erweiterung, Paket, Quelle, Namensraum, Schemaänderung, Datenbankänderung, Auslieferung, migration, migrations, extension, package, extension:<name>, package:<name>, ExtensionDirectory, requires, source, namespace, schema change, database change, deploy, exp:migration:generate, --source
type: help
---
# Migrationen in einer Erweiterung oder einem Paket

Eine Erweiterung, die auf den Sites, die sie nutzen, eine Tabelle, eine Klasse oder eine Rolle braucht, kann die
Änderung als Migrationen mitbringen. Sie werden gefunden, solange die Erweiterung aktiv ist, und laufen in ihrer
Reihenfolge zusammen mit den Migrationen der Site, unter derselben Sperre, mit denselben Sicherungspunkten und
derselben Aufzeichnung. Dieses Rezept fügt der Erweiterung `expnews` eine Migration hinzu, lässt eine Migration der
Site auf sie warten und zeigt, wohin die Migrationen eines Pakets gehören.

## Was Sie am Ende haben

- `extension/expnews/migrations/20261101100000_news_source_table.php`, gelistet mit der Quelle `extension:expnews`;
- eine Migration der Site, die sie in `requires:` nennt und darum immer nach ihr läuft;
- die Regeln für Pakete, deren Migrationen gefunden werden, sobald das Paket installiert ist.

## Bevor Sie beginnen

- Die Erweiterung ist aktiv (`site.ini [ExtensionSettings] ActiveExtensions[]`). Die Migrationen einer inaktiven
  Erweiterung werden nicht gefunden, und eine Anforderung `extension:<name>` scheitert.
- Entscheiden Sie, ob die Änderung überhaupt eine Migration ist: Was jede Installation der Erweiterung bei jedem
  Upgrade braucht, ist ein [Upgrade-Schritt](../../../anleitungen/upgrade-schritte-fuer-erweiterungen.md); siehe
  [Upgrade-Schritt oder Migration?](upgrade-schritt-oder-migration.md).

## Schritte

### 1. Die Migration in der Erweiterung erzeugen

```bash
./console exp:migration:generate --skeleton news_source_table --source=extension:expnews --description="expnews: the table of news sources" --apply
```

```text
Exponential migrations: generate
Wrote extension/expnews/migrations/20261101100000_news_source_table.php
Migration 20261101100000_news_source_table, class \Extension\Expnews\Migrations\NewsSourceTable20261101100000
Next: fill in plan() and up(), then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

Der Ordner ist `extension/<name>/` plus `migration.ini [MigrationSettings] ExtensionDirectory` (`migrations`). Der
Namensraum `Extension\Expnews\Migrations` hält die Klasse von den `Site\Migrations` jeder Site getrennt.

### 2. Ausfüllen

```php
<?php
// description: expnews: the table of news sources
// requires: 6.0.15-migration-tables
// author: expnews maintainers
namespace Extension\Expnews\Migrations;

final class NewsSourceTable20261101100000 extends \expMigrationPhp
{
    const TABLE = 'expnews_source';

    /** @return array the table in the format of share/db_schema.dba */
    public static function schema()
    {
        return array( self::TABLE => array(
            'name' => self::TABLE,
            'fields' => array(
                'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'identifier' => array( 'length' => 64, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                'url' => array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
            ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
    }

    public function plan( $context )
    {
        return array( 'create the table ' . self::TABLE );
    }

    public function up( $context )
    {
        $messages = array();
        if ( !\expKernelTableSchema::install( array( self::TABLE ), $context->db(), self::schema(), $messages ) )
            throw new \RuntimeException( 'the table could not be created: ' . implode( '; ', $messages ) );
        return array( 'created ' . self::TABLE );
    }

    public function down( $context )
    {
        $context->db()->query( 'DROP TABLE ' . self::TABLE );
        \expKernelTableSchema::reset();
        return array( 'dropped ' . self::TABLE );
    }
}
```

`requires: 6.0.15-migration-tables` nennt einen Upgrade-Schritt des Kernels: Kernel-Schritte sind Migrationen der
Quelle `kernel`, also kann die Migration einer Erweiterung auf einen warten. Benennen Sie ihre Tabellen mit dem
Präfix der Erweiterung (`expnews_`).

### 3. Eine Migration der Site auf sie warten lassen

Eine Migration der Site, die in `expnews_source` schreibt, nennt die Migration der Erweiterung bei ihrer ID oder die
ganze Erweiterung:

```php
// requires: 20261101100000_news_source_table
// requires: extension:expnews
```

Die erste Form ordnet die beiden Migrationen; die zweite prüft, dass `expnews` aktiv ist, und lässt den Lauf sonst
scheitern.

### 4. Den Stand ansehen

```text
Exponential migrations: status
Run 20261101-100512-7be210, environment development, database sqlite (transactions: full)

  done        6.0.15-migration-tables                      kernel             The migration record (tables exp_migration and exp_migration_run), with the rows of expupgrade_step copied over
  pending     20261101100000_news_source_table             extension:expnews  expnews: the table of news sources
  pending     20261101101000_news_default_sources          site               The default news sources

Summary: 3 migration(s): 1 done, 2 pending, 0 not needed, 0 failed, 0 unknown; 2 would-apply
PASS
```

## Pakete

Die Migrationen eines installierten Pakets liegen in
`var/<storage>/packages/<repository>/<paket>/migrations/` und haben die Quelle `package:<name>`.
`exp:migration:generate` schreibt nur für `site` und `extension:<name>`: Schreiben Sie die Datei in der Site oder
der Erweiterung und kopieren Sie sie vor dem Export des Pakets in dessen Ordner `migrations/`. Der Installer von
6.0.15 führt die Migrationen eines Pakets nicht von sich aus aus; nach der Installation eines Pakets führt sie das
nächste `./console exp:migration:migrate --apply` aus.

## IDs sind über alle Quellen eindeutig

Eine ID darf es einmal geben. Wird dieselbe ID in der Site, einer Erweiterung und einem Paket gefunden, zählt die
zuerst gefundene (Site, dann Erweiterungen, dann Pakete), und das Duplikat ist ein Fehler, der jeden Lauf anhält,
bis eine Datei umbenannt ist. Erzeugte IDs tragen die Zeit auf die Sekunde, ein Zusammenstoß ist also selten;
benennen Sie nur eine Datei um, die noch nirgends gelaufen ist.

## Prüfen

```bash
./console exp:migration:check --path=extension/expnews/migrations
```

Liefern Sie die Migrationen im Repository der Erweiterung und ebenso in ihrem `-ezpackage`-Repository aus, damit
beide Arten von Installation sie bekommen.

## Zurücknehmen

`./console exp:migration:rollback 20261101100000_news_source_table --apply` führt `down()` der Migration der
Erweiterung aus. Haben zwei Quellen dieselbe ID, geben Sie `--source=extension:expnews` an. Eine Migration der Site,
die sie verlangt, muss zuerst zurückgenommen werden.

## Nächster Schritt

Machen Sie jedes `down()` verlässlich: [Ein umkehrbares down() schreiben](ein-umkehrbares-down-schreiben.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Upgrade-Schritte für Erweiterungen](../../../anleitungen/upgrade-schritte-fuer-erweiterungen.md), [Pakete](../../../anleitungen/pakete.md),
  [migration.ini \[MigrationSettings\]](../../../beispiele/ini-einstellungen/migration/MigrationSettings.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
