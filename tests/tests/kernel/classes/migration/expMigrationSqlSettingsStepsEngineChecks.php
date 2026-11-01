<?php
/**
 * The checks of SQL migrations, INI migrations and the step types schema, data, sql and settings that run on every
 * engine the same way: SQLite in process (expMigrationSqlSettingsStepsSqliteTest), MySQL/MariaDB and PostgreSQL in the
 * database job (expMigrationSqlSettingsStepsDatabaseTest), MongoDB in the live MongoDB suite
 * (expMigrationSqlSettingsStepsMongoDBTest). Each gives a connection with the kernel schema.
 *
 *  QE-1 a YAML migration makes a table with an index, adds a column and an index (the kernel's schema handlers), a
 *       second one inserts, ensures and updates rows and runs the engine's own statements; a dry run changes nothing,
 *       a second run nothing; the rollback runs the down: of the second and undoes the first step by step, the last
 *       first, and the table is gone
 *  QE-2 an SQL migration of a portable file, the SQLite file and the MongoDB file: the engine's own file runs, its
 *       "-- down" part undoes it
 *  QE-3 schema changes and transactions: a CREATE TABLE before a failing statement is undone with the migration on
 *       PostgreSQL and SQLite, stays on MySQL/MariaDB (and the run says so), and stays on MongoDB (no transactions)
 *  QE-4 an INI migration through the runner writes settings/override of a settings tree of the test, with a backup;
 *       its dry run writes nothing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';

trait expMigrationSqlSettingsStepsEngineChecks
{
    use expMigrationRunnerEngineChecks;

    /** The table of the checks. */
    protected static $wp11Table = 'exp_test_wp11_rating';

    /**
     * The migrations of files written to a folder of runnerDir().
     *
     * @param string $folder
     * @param array $files name => text
     * @return expMigration[]
     */
    protected function wp11Migrations( $folder, array $files )
    {
        $dir = $this->runnerDir() . '/' . $folder;
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        foreach ( $files as $name => $text )
            file_put_contents( "$dir/$name", $text );
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $dir ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        return $migrations;
    }

    /**
     * @param array $report
     * @return string
     */
    protected static function wp11Why( array $report )
    {
        return implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) );
    }

    /**
     * @param eZDBInterface $db
     */
    protected function wp11Clean( $db )
    {
        $this->dropRunnerTable( $db, self::$wp11Table );
        $this->dropRunnerTable( $db, 'exp_test_wp11_ddl' );
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261107%wp11%'" );
    }

    /** @return string[] the files of QE-1 */
    protected static function wp11YamlFiles()
    {
        return array(
            '20261107100000_wp11_schema.yml' => <<<'YAML'
# description: A table with an index, a column and an index
steps:
    - type: schema
      mode: create_table
      table: exp_test_wp11_rating
      columns:
          id: { type: auto_increment }
          object_id: { type: int, not_null: true, default: 0 }
          rating: { type: int, not_null: true, default: 0 }
          comment: { type: varchar, length: 100, default: "" }
      indexes:
          exp_test_wp11_rating_object: { columns: [ object_id ] }
    - type: schema
      mode: add_column
      table: exp_test_wp11_rating
      column: created
      definition: { type: int, not_null: true, default: 0 }
    - type: schema
      mode: add_index
      table: exp_test_wp11_rating
      index: exp_test_wp11_rating_created
      columns: [ created ]
YAML
            ,
            '20261107100100_wp11_data.yml' => <<<'YAML'
# description: Rows of the table
steps:
    - type: data
      mode: insert
      table: exp_test_wp11_rating
      rows:
          - { object_id: 1, rating: 5, comment: "it's good" }
          - { object_id: 2, rating: 3, comment: "" }
      id_column: id
      references: { last_rating: id }
    - type: data
      mode: ensure
      table: exp_test_wp11_rating
      key: [ object_id ]
      rows:
          - { object_id: 2, rating: 4 }
          - { object_id: 3, rating: 1 }
    - type: data
      mode: update
      table: exp_test_wp11_rating
      where: { object_id: [ 1, 3 ] }
      values: { created: 1700000000 }
    - type: sql
      sql: "UPDATE exp_test_wp11_rating SET rating = rating + 10 WHERE object_id = 3;"
      mongodb:
          - { update: exp_test_wp11_rating, filter: { object_id: 3 }, set: { rating: 11 } }
down:
    - type: data
      mode: delete
      table: exp_test_wp11_rating
      all: true
YAML
        );
    }

    /**
     * QE-1, QE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkSchemaDataAndSqlFiles( $db )
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        $engine = expKernelTableSchema::type( $db );
        $table = self::$wp11Table;
        $this->wp11Clean( $db );
        $migrations = $this->wp11Migrations( 'yaml', self::wp11YamlFiles() );

        $dry = ( new expMigrationRunner( $this->runnerContext( $db, false ) ) )->run( $migrations );
        $this->assertSame( 0, $dry['exit'], self::wp11Why( $dry ) );
        $this->assertStringContainsString( "create the table $table", implode( "\n", $dry['migrations'][0]['plan'] ) );
        expKernelTableSchema::reset();
        $this->assertNotTrue( expKernelTableSchema::exists( $table, $db, true ), 'the dry run created nothing' );

        $context = $this->runnerContext( $db );
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], self::wp11Why( $report ) );
        $this->assertSame( array( 'applied', 'applied' ), array_column( $report['migrations'], 'action' ) );
        expKernelTableSchema::reset();
        $this->assertTrue( expKernelTableSchema::exists( $table, $db, true ) );
        if ( $engine !== 'mongodb' )
        {
            $this->assertTrue( expUpgradeCatalog::hasColumn( $db, $table, 'created' ) );
            $this->assertTrue( expUpgradeCatalog::hasIndex( $db, $table, array( 'created' ) ) );
            $this->assertTrue( expUpgradeCatalog::hasIndex( $db, $table, array( 'object_id' ) ) );
        }
        $this->assertSame( 3, self::runnerCount( $db, $table ) );
        $this->assertSame( 1, self::runnerCount( $db, $table, 'object_id = 1 AND rating = 5' ) );
        $this->assertSame( 1, self::runnerCount( $db, $table, "comment = 'it''s good'" ), 'a quote in a value' );
        $this->assertSame( 1, self::runnerCount( $db, $table, 'object_id = 2 AND rating = 4' ), 'ensure updated the row there' );
        $this->assertSame( 1, self::runnerCount( $db, $table, 'object_id = 3 AND rating = 11' ), "the statements of $engine ran" );
        $this->assertSame( 2, self::runnerCount( $db, $table, 'created = 1700000000' ) );
        $this->assertNotNull( $context->reference( 'last_rating' ), 'the id of the row inserted last' );
        $messages = implode( "\n", $report['migrations'][0]['messages'] );
        $this->assertStringContainsString( "create the table $table: created", $messages );

        $again = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $this->wp11Migrations( 'yaml', array() ) );
        $this->assertSame( 0, $again['summary']['applied'] );

        // QE-2: the engine's own file, else the portable one
        $files = array(
            '20261107110000_wp11_files.sql' => "-- description: A row by SQL\nINSERT INTO $table ( object_id, rating, comment, created ) VALUES ( 10, 1, 'portable', 0 );\n-- down\nDELETE FROM $table WHERE object_id = 10;\n",
            '20261107110000_wp11_files.sqlite.sql' => "INSERT INTO $table ( object_id, rating, comment, created ) VALUES ( 10, 1, 'sqlite', 0 );\n-- down\nDELETE FROM $table WHERE object_id = 10;\n",
            '20261107110000_wp11_files.mongodb.json' => json_encode( array( 'up' => array( array( 'insert' => $table, 'documents' => array( array( 'object_id' => 10, 'rating' => 1, 'comment' => 'mongodb', 'created' => 0 ) ) ) ),
                                                                            'down' => array( array( 'delete' => $table, 'filter' => array( 'object_id' => 10 ) ) ) ) ),
        );
        $sqlFiles = $this->wp11Migrations( 'files', $files );
        $this->assertTrue( $sqlFiles[0]->definition()->supportsEngine( $engine ) );
        $fileRun = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $sqlFiles );
        $this->assertSame( 0, $fileRun['exit'], self::wp11Why( $fileRun ) );
        $expected = in_array( $engine, array( 'sqlite', 'mongodb' ), true ) ? $engine : 'portable';
        $this->assertSame( 1, self::runnerCount( $db, $table, "object_id = 10 AND comment = '$expected'" ), "$engine ran its own file" );
        $row = expMigrationRecord::get( $db, 'site', '20261107110000_wp11_files' );
        $this->assertSame( 'done', $row['state'] );
        $fileBack = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $sqlFiles );
        $this->assertSame( 0, $fileBack['exit'], self::wp11Why( $fileBack ) );
        $this->assertSame( 0, self::runnerCount( $db, $table, 'object_id = 10' ), 'its down part ran' );

        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $this->wp11Migrations( 'yaml', array() ) );
        $this->assertSame( 0, $rollback['exit'], self::wp11Why( $rollback ) );
        $this->assertSame( array( 'undone', 'undone' ), array_column( $rollback['migrations'], 'action' ) );
        expKernelTableSchema::reset();
        $this->assertNotTrue( expKernelTableSchema::exists( $table, $db, true ), 'the schema steps undid themselves' );
        $this->wp11Clean( $db );
    }

    /**
     * QE-3
     *
     * @param eZDBInterface $db
     */
    protected function checkSchemaChangeTransactions( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        $this->wp11Clean( $db );
        $migrations = $this->wp11Migrations( 'ddl', array(
            '20261107120000_wp11_ddl.sql' => "-- description: A table, then a statement that fails\nCREATE TABLE exp_test_wp11_ddl ( id INT NOT NULL );\n"
                                           . "INSERT INTO exp_test_wp11_no_such_table ( id ) VALUES ( 1 );\n",
            '20261107120000_wp11_ddl.mongodb.json' => '[ { "create_collection": "exp_test_wp11_ddl" }, { "delete": "exp_test_wp11_ddl", "filter": { "$nonsense": 1 } } ]',
        ) );
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $this->assertSame( 1, $report['exit'] );
        $entry = $report['migrations'][0];
        $this->assertSame( 'failed', $entry['action'] );
        $mode = expMigrationContext::transactionModeOf( $engine );
        $this->assertSame( $mode, $entry['transaction'] );
        expKernelTableSchema::reset();
        $exists = expKernelTableSchema::exists( 'exp_test_wp11_ddl', $db, true );
        if ( $mode === expMigrationContext::TRANSACTION_FULL )
        {
            $this->assertNotTrue( $exists, "$engine undoes the CREATE TABLE with the migration" );
        }
        else
        {
            $this->assertTrue( $exists, "$engine keeps the CREATE TABLE made before the failure" );
            if ( $mode === expMigrationContext::TRANSACTION_DATA )
                $this->assertContains( "rolled back what could be: $engine keeps schema changes made before the failure", $entry['messages'] );
        }
        $this->assertSame( 0, $db->transactionCounter(), 'no transaction left open' );
        $this->wp11Clean( $db );
    }

    /**
     * QE-4
     *
     * @param eZDBInterface $db
     */
    protected function checkIniMigration( $db )
    {
        $root = $this->runnerDir() . '/root/';
        mkdir( $root . 'settings/override', 0775, true );
        mkdir( $root . 'settings/siteaccess/admin', 0775, true );
        file_put_contents( $root . 'settings/override/site.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=Old\n*/ ?>\n" );
        expIniEditor::setRoot( $root, array(), array( 'admin' ) );
        try
        {
            $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261107%wp11%'" );
            $migrations = $this->wp11Migrations( 'ini', array(
                '20261107130000_wp11_settings.ini' => "#?ini charset=\"utf-8\"?\n# description: The site's name and the admin's start page\n"
                                                    . "[site.ini/SiteSettings]\nSiteName=WP11\n[siteaccess:admin site.ini/SiteSettings]\nDefaultPage=content/dashboard\n",
            ) );
            $dry = ( new expMigrationRunner( $this->runnerContext( $db, false ) ) )->run( $migrations );
            $this->assertSame( 0, $dry['exit'], self::wp11Why( $dry ) );
            $this->assertStringContainsString( 'SiteName=Old', file_get_contents( $root . 'settings/override/site.ini.append.php' ), 'the dry run wrote nothing' );
            $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
            $this->assertSame( 0, $report['exit'], self::wp11Why( $report ) );
            $this->assertStringContainsString( 'SiteName=WP11', file_get_contents( $root . 'settings/override/site.ini.append.php' ) );
            $this->assertStringContainsString( 'DefaultPage=content/dashboard', file_get_contents( $root . 'settings/siteaccess/admin/site.ini.append.php' ) );
            $this->assertCount( 1, glob( $root . 'var/backup/ini/*/settings/override/site.ini.append.php' ) );
            $this->assertSame( 'done', expMigrationRecord::get( $db, 'site', '20261107130000_wp11_settings' )['state'] );
            $back = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $migrations );
            $this->assertSame( 1, $back['exit'], 'an INI migration is undone by its caption only' );
        }
        finally
        {
            expIniEditor::setRoot( null );
            $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261107%wp11%'" );
        }
    }
}
