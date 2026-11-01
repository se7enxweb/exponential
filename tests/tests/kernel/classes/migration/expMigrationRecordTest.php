<?php
/**
 * The migration record (expMigrationRecord, tables exp_migration and exp_migration_run) and exp:upgrade's record on
 * it (expUpgradeRecord). No installation: the schema checks read the files; the record runs on a SQLite database in
 * memory, and the SQLite update file on a SQLite file under var/tmp that is removed afterwards.
 *
 *  MR-01 the columns of expMigrationRecord are those of share/db_schema.dba, with the keys; the schema has no
 *        expupgrade_step; every column of the pre-release expupgrade_step has its place (the mapping of the class
 *        comment), and back again
 *  MR-02 the kernel schemas of MySQL, PostgreSQL and SQLite create both tables with the same columns and keys, and
 *        never expupgrade_step
 *  MR-03 the 6.0.0 to 6.0.15 update files create them only when missing, copy expupgrade_step without duplicates and
 *        drop it; the SQLite update runs twice (the second time without expupgrade_step) and copies each row once
 *  MR-04 upgrade.ini lists the migration tables first, then the step that drops expupgrade_step, which names it in
 *        after()
 *  MR-05 normalize(), validRunId(), environment(): the shapes the record accepts
 *  MR-06 SQLite in process: create, copy and drop, re-run, write and read, exp:upgrade's record
 *        (expMigrationRecordEngineChecks)
 *  MR-07 exp:upgrade keeps working before step 0: no table, nothing written; after it the record is exp_migration
 *        and expupgrade_step is never made
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRecordEngineChecks.php';

class expMigrationRecordTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRecordEngineChecks;

    private static $columns = array( 'caption_id', 'checksum', 'duration_ms', 'engine', 'environment', 'file_path', 'finished_at',
                                     'message', 'migration_id', 'revision', 'run_by', 'run_id', 'source', 'stage', 'started_at',
                                     'state', 'version' );

    private static $runColumns = array( 'caption_post', 'caption_pre', 'commit_id', 'environment', 'finished_at', 'log_file',
                                        'message', 'push_state', 'run_by', 'run_id', 'started_at', 'state', 'tag_name' );

    /** @var array|null */
    private $savedGlobal = null;

    /** @var bool */
    private $replacedGlobal = false;

    protected function tearDown(): void
    {
        if ( $this->replacedGlobal )
        {
            if ( $this->savedGlobal === null )
                unset( $GLOBALS['eZDBGlobalInstance'] );
            else
                $GLOBALS['eZDBGlobalInstance'] = $this->savedGlobal[0];
            expKernelTableSchema::reset();
        }
    }

    /** @return eZSQLite3DB a SQLite database in memory, the global database of the test */
    private function sqlite()
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->savedGlobal = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        $this->replacedGlobal = true;
        $db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => ':memory:',
                                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                      'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                      'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                      'use_persistent_connection' => false, 'show_errors' => false ) );
        eZDB::setInstance( $db );
        expKernelTableSchema::reset();
        return $db;
    }

    /** MR-01 */
    public function testColumnsAndMapping()
    {
        $schema = self::migrationSchema();
        $columns = array_keys( expMigrationRecord::COLUMNS );
        sort( $columns );
        $this->assertSame( self::$columns, $columns );
        $this->assertSame( self::$columns, array_keys( $schema['exp_migration']['fields'] ) );
        $this->assertSame( array( 'source', 'migration_id' ), $schema['exp_migration']['indexes']['PRIMARY']['fields'] );
        $this->assertSame( array( 'run_id' ), $schema['exp_migration']['indexes']['exp_migration_run_id']['fields'] );
        $runColumns = array_keys( expMigrationRecord::RUN_COLUMNS );
        sort( $runColumns );
        $this->assertSame( self::$runColumns, $runColumns );
        $this->assertSame( self::$runColumns, array_keys( $schema['exp_migration_run']['fields'] ) );
        $this->assertSame( array( 'run_id' ), $schema['exp_migration_run']['indexes']['PRIMARY']['fields'] );
        foreach ( array( 'exp_migration' => expMigrationRecord::COLUMNS, 'exp_migration_run' => expMigrationRecord::RUN_COLUMNS ) as $table => $defs )
            foreach ( $defs as $name => $type )
            {
                $field = $schema[$table]['fields'][$name];
                $this->assertSame( $type[0] === 'int' ? 'int' : 'varchar', $field['type'], "$table.$name" );
                if ( $type[0] === 'string' )
                    $this->assertSame( $type[1], $field['length'], "$table.$name length" );
            }
        // a new installation has no expupgrade_step; every column of the pre-release table has a place, with the same length
        $this->assertArrayNotHasKey( 'expupgrade_step', $schema );
        $legacy = expMigrationLegacyRecordFixture::schema()['expupgrade_step']['fields'];
        $this->assertEqualsCanonicalizing( expMigrationRecord::LEGACY_COLUMNS, array_keys( $legacy ) );
        $map = array( 'step_id' => 'migration_id', 'applied_at' => 'finished_at', 'applied_by' => 'run_by' );
        foreach ( $legacy as $name => $field )
        {
            $target = isset( $map[$name] ) ? $map[$name] : $name;
            $this->assertArrayHasKey( $target, $schema['exp_migration']['fields'], "expupgrade_step.$name goes to exp_migration.$target" );
            $this->assertGreaterThanOrEqual( (int)$field['length'], (int)$schema['exp_migration']['fields'][$target]['length'], "$name fits $target" );
        }

        $legacyRow = array( 'STEP_ID' => '6.0.15-x', 'stage' => 'database', 'version' => '6.0.15', 'state' => 'failed', 'applied_at' => '1761900000',
                            'duration_ms' => '2499', 'applied_by' => 'cli root', 'engine' => 'oracle', 'checksum' => str_repeat( 'e', 64 ),
                            'message' => 'ORA-00942' );
        $row = expMigrationRecord::fromLegacy( $legacyRow );
        $this->assertSame( array_keys( expMigrationRecord::COLUMNS ), array_keys( $row ) );
        $this->assertSame( array( 'kernel', '6.0.15-x', 'database', '6.0.15', 'failed', 1761899998, 1761900000, 2499, 'cli root', 'oracle', 'ORA-00942', '', '', '', '' ),
                           array( $row['source'], $row['migration_id'], $row['stage'], $row['version'], $row['state'], $row['started_at'],
                                  $row['finished_at'], $row['duration_ms'], $row['run_by'], $row['engine'], $row['message'], $row['file_path'],
                                  $row['environment'], $row['run_id'], $row['caption_id'] ) );
        $back = expMigrationRecord::toLegacy( $row );
        $this->assertSame( expMigrationRecord::LEGACY_COLUMNS, array_keys( $back ) );
        $this->assertSame( array( 'step_id' => '6.0.15-x', 'stage' => 'database', 'version' => '6.0.15', 'state' => 'failed',
                                  'applied_at' => 1761900000, 'duration_ms' => 2499, 'applied_by' => 'cli root', 'engine' => 'oracle',
                                  'checksum' => str_repeat( 'e', 64 ), 'message' => 'ORA-00942' ), $back );
        $caption = expMigrationRecord::fromLegacy( array( 'step_id' => 'caption:abc', 'stage' => 'caption', 'state' => 'pre' ) );
        $this->assertSame( 'abc', $caption['caption_id'] );
        $this->assertSame( 0, $caption['started_at'], 'no time, no start' );
    }

    /** @return string the CREATE TABLE statement of a table in a schema file */
    private static function createTable( $sql, $table )
    {
        preg_match( '/CREATE TABLE (?:IF NOT EXISTS )?`?' . $table . '`? \((.*?)\n\)/s', $sql, $m );
        return isset( $m[1] ) ? $m[1] : '';
    }

    /** @return string[] the column names of a CREATE TABLE body */
    private static function columnsOf( $body )
    {
        preg_match_all( '/^[\s,]*`?"?([a-z_]+)"?`? (?:int|integer|varchar|character)/m', $body, $cols );
        return $cols[1];
    }

    /** MR-02 */
    public function testKernelSchemas()
    {
        $files = array( 'mysql' => 'kernel/sql/mysql/kernel_schema.sql', 'postgresql' => 'kernel/sql/postgresql/kernel_schema.sql',
                        'sqlite' => 'kernel/sql/sqlite/schema.sql' );
        foreach ( $files as $engine => $file )
        {
            $sql = file_get_contents( self::migrationRoot() . '/' . $file );
            $this->assertSame( self::$columns, self::columnsOf( self::createTable( $sql, 'exp_migration' ) ), "$engine exp_migration" );
            $this->assertSame( self::$runColumns, self::columnsOf( self::createTable( $sql, 'exp_migration_run' ) ), "$engine exp_migration_run" );
            $this->assertStringNotContainsString( 'expupgrade_step', $sql, "$engine: a new installation has no expupgrade_step" );
        }
        $mysql = file_get_contents( self::migrationRoot() . '/kernel/sql/mysql/kernel_schema.sql' );
        $this->assertStringContainsString( 'PRIMARY KEY  (source,migration_id)', $mysql );
        $this->assertStringContainsString( 'KEY exp_migration_run_id (run_id)', $mysql );
        $this->assertStringContainsString( 'KEY exp_migration_run_started (started_at)', $mysql );
        $pg = file_get_contents( self::migrationRoot() . '/kernel/sql/postgresql/kernel_schema.sql' );
        $this->assertStringContainsString( 'ADD CONSTRAINT exp_migration_pkey PRIMARY KEY (source, migration_id);', $pg );
        $this->assertStringContainsString( 'ADD CONSTRAINT exp_migration_run_pkey PRIMARY KEY (run_id);', $pg );
        $this->assertStringContainsString( 'CREATE INDEX exp_migration_run_id ON exp_migration USING btree (run_id);', $pg );
        $sqlite = file_get_contents( self::migrationRoot() . '/kernel/sql/sqlite/schema.sql' );
        $this->assertStringContainsString( ',  PRIMARY KEY (`source`,`migration_id`)', $sqlite );
        $this->assertStringContainsString( 'CREATE INDEX exp_migration_run_started ON exp_migration_run ( started_at );', $sqlite );
    }

    /** MR-03 */
    public function testUpdateFiles()
    {
        foreach ( array( 'mysql', 'postgresql', 'sqlite' ) as $engine )
        {
            $sql = file_get_contents( self::migrationRoot() . "/update/database/$engine/6.0/dbupdate-6.0.0-6.0.15.sql" );
            $this->assertSame( self::$columns, self::columnsOf( self::createTable( $sql, 'exp_migration' ) ), $engine );
            $this->assertSame( self::$runColumns, self::columnsOf( self::createTable( $sql, 'exp_migration_run' ) ), $engine );
            $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS exp_migration (', $sql, $engine );
            $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS exp_migration_run (', $sql, $engine );
            $this->assertStringContainsString( "WHERE NOT EXISTS ( SELECT 1 FROM exp_migration m WHERE m.source = 'kernel' AND m.migration_id = s.step_id );", $sql, $engine );
            $this->assertLessThan( strpos( $sql, 'INSERT INTO exp_migration (' ), strpos( $sql, 'CREATE TABLE IF NOT EXISTS expupgrade_step (' ), "$engine: expupgrade_step is made before the copy" );
            $this->assertGreaterThan( strpos( $sql, 'INSERT INTO exp_migration (' ), strpos( $sql, 'DROP TABLE IF EXISTS expupgrade_step;' ), "$engine: expupgrade_step is dropped after the copy" );
        }
    }

    /** MR-03 */
    public function testSqliteUpdateRunsTwiceAndCopiesOnce()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::migrationRoot() . '/var/tmp';
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        $file = $dir . '/migration-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $sql = file_get_contents( self::migrationRoot() . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $section = substr( $sql, strpos( $sql, '-- expupgrade_step:' ) );
        $apply = function () use ( $pdo, $section ) {
            foreach ( preg_split( '/;\s*$/m', preg_replace( '/^--.*$/m', '', $section ) ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        try
        {
            $pdo->exec( 'CREATE TABLE expupgrade_step ( applied_at integer NOT NULL DEFAULT 0, applied_by varchar(150) NOT NULL DEFAULT \'\', checksum varchar(64) NOT NULL DEFAULT \'\', duration_ms integer NOT NULL DEFAULT 0, engine varchar(30) NOT NULL DEFAULT \'\', message varchar(1000) NOT NULL DEFAULT \'\', stage varchar(20) NOT NULL DEFAULT \'\', state varchar(20) NOT NULL DEFAULT \'\', step_id varchar(150) NOT NULL DEFAULT \'\' PRIMARY KEY, version varchar(30) NOT NULL DEFAULT \'\' )' );
            $pdo->exec( "INSERT INTO expupgrade_step ( step_id, stage, version, state, applied_at, duration_ms, applied_by, engine ) VALUES ( '6.0.15-a', 'database', '6.0.15', 'done', 1761900000, 1500, 'cli root', 'sqlite' )" );
            $pdo->exec( "INSERT INTO expupgrade_step ( step_id, stage, state, applied_at ) VALUES ( 'caption:c1', 'caption', 'pre', 0 )" );
            // what the PHP mapping makes of the same rows (read before the update drops the table)
            $php = expMigrationRecord::fromLegacy( $pdo->query( "SELECT * FROM expupgrade_step WHERE step_id = '6.0.15-a'" )->fetch( PDO::FETCH_ASSOC ) );
            $legacyExists = function () use ( $pdo ) {
                return (int)$pdo->query( "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = 'expupgrade_step'" )->fetchColumn() > 0;
            };
            $apply();
            $this->assertFalse( $legacyExists(), 'the update drops expupgrade_step' );
            $rows = $pdo->query( 'SELECT * FROM exp_migration ORDER BY migration_id' )->fetchAll( PDO::FETCH_ASSOC );
            $this->assertCount( 2, $rows );
            $this->assertSame( array( 'kernel', '6.0.15-a', 'done', 1761899998, 1761900000, 1500, 'cli root' ),
                               array( $rows[0]['source'], $rows[0]['migration_id'], $rows[0]['state'], (int)$rows[0]['started_at'],
                                      (int)$rows[0]['finished_at'], (int)$rows[0]['duration_ms'], $rows[0]['run_by'] ) );
            $this->assertSame( 'c1', $rows[1]['caption_id'] );
            $this->assertSame( 0, (int)$rows[1]['started_at'] );
            foreach ( array( 'source', 'migration_id', 'state', 'started_at', 'finished_at', 'duration_ms', 'run_by', 'caption_id' ) as $name )
                $this->assertEquals( $php[$name], $rows[0][$name], "the SQL copy and fromLegacy() agree on $name" );
            $pdo->exec( "UPDATE exp_migration SET state = 'failed' WHERE migration_id = '6.0.15-a'" );
            $apply();
            $this->assertFalse( $legacyExists(), 'the second run (without expupgrade_step) leaves none behind' );
            $this->assertSame( '2', (string)$pdo->query( 'SELECT COUNT(*) FROM exp_migration' )->fetchColumn(), 'the second run copied nothing' );
            $this->assertSame( 'failed', (string)$pdo->query( "SELECT state FROM exp_migration WHERE migration_id = '6.0.15-a'" )->fetchColumn(), 'and kept the row' );
            $cols = array_column( $pdo->query( 'PRAGMA table_info(exp_migration_run)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
            $this->assertSame( self::$runColumns, $cols );
        }
        finally
        {
            $pdo = null;
            if ( is_file( $file ) )
                unlink( $file );
        }
    }

    /** MR-04 */
    public function testStepIsRegisteredAfterTheRecordTable()
    {
        $ini = file_get_contents( self::migrationRoot() . '/settings/upgrade.ini' );
        $this->assertMatchesRegularExpression( "/^StepList\\[\\]=expUpgradeStepMigrationTables\n(#[^\n]*\n)*StepList\\[\\]=expUpgradeStepRecordTable\n/m", $ini );
        $step = new expUpgradeStepMigrationTables();
        $this->assertSame( '6.0.15-migration-tables', $step->id() );
        $this->assertSame( 'database', $step->stage() );
        $this->assertSame( array(), $step->after(), 'step 0' );
        $this->assertSame( array( 'exp_migration', 'exp_migration_run' ), $step->tables() );
        $drop = new expUpgradeStepRecordTable();
        $this->assertSame( '6.0.15-upgrade-record-table', $drop->id() );
        $this->assertSame( 'database', $drop->stage() );
        $this->assertSame( array( '6.0.15-migration-tables' ), $drop->after() );
        $this->assertNotInstanceOf( expUpgradeSchemaStep::class, $drop, 'it makes no table' );
    }

    /** MR-05 */
    public function testShapes()
    {
        $row = expMigrationRecord::normalize( array( 'source' => 'site', 'migration_id' => str_repeat( 'x', 200 ), 'duration_ms' => '12',
                                                     'message' => array( 'no' ), 'other' => 1 ), expMigrationRecord::COLUMNS );
        $this->assertSame( 150, strlen( $row['migration_id'] ) );
        $this->assertSame( 12, $row['duration_ms'] );
        $this->assertSame( '', $row['message'] );
        $this->assertArrayNotHasKey( 'other', $row );
        foreach ( array( '20261031-120000-ab12', 'r_1', 'a.b-c' ) as $id )
            $this->assertTrue( expMigrationRecord::validRunId( $id ), $id );
        foreach ( array( '', '.hidden', '../x', 'a/b', str_repeat( 'a', 65 ), 'a b', 12 ) as $id )
            $this->assertFalse( expMigrationRecord::validRunId( $id ), (string)$id );
        $this->expectException( InvalidArgumentException::class );
        expMigrationRecord::write( null, array( 'source' => 'site' ) );
    }

    /** MR-05 */
    public function testEnvironment()
    {
        $ini = $this->createStub( eZINI::class );
        $ini->method( 'hasVariable' )->willReturn( true );
        $ini->method( 'variable' )->willReturnOnConsecutiveCalls( 'Staging', 'auto', 'auto', 'bad name!' );
        $this->assertSame( 'staging', expMigrationRecord::environment( $ini ) );
        $saved = getenv( 'EXP_ENV' );
        try
        {
            putenv( 'EXP_ENV=Production' );
            $this->assertSame( 'production', expMigrationRecord::environment( $ini ) );
            putenv( 'EXP_ENV' );
            $this->assertSame( '', expMigrationRecord::environment( $ini ) );
            $this->assertSame( '', expMigrationRecord::environment( $ini ), 'a value that is no name is no environment' );
        }
        finally
        {
            putenv( $saved === false ? 'EXP_ENV' : 'EXP_ENV=' . $saved );
        }
    }

    /** MR-06 */
    public function testSqliteInProcess()
    {
        $db = $this->sqlite();
        $this->checkCreateAndCopy( $db );
        $this->checkWriteAndRead( $db );
        $this->checkUpgradeRecord( $db );
    }

    /** MR-07 */
    public function testUpgradeRecordBeforeTheStep()
    {
        $db = $this->sqlite();
        $step = new expUpgradeStepMigrationTables();
        $drop = new expUpgradeStepRecordTable();
        $this->assertNull( expUpgradeRecord::table( $db ) );
        $this->assertFalse( expUpgradeRecord::exists( $db ) );
        $this->assertSame( array(), expUpgradeRecord::all( $db ) );
        $this->assertFalse( expUpgradeRecord::write( $db, $step, 'done', 10 ), 'nothing to write to' );
        $this->assertSame( array(), expMigrationRecord::all( $db ) );
        $this->assertSame( array(), expMigrationRecord::runs( $db ) );
        $this->assertFalse( expMigrationRecord::write( $db, array( 'source' => 'site', 'migration_id' => 'x' ) ) );
        $this->assertFalse( expMigrationRecord::copyLegacy( $db ) );
        $this->assertSame( 0, expMigrationRecord::legacyPending( $db ) );

        $context = new expUpgradeContext( $db, true, array(), self::migrationRoot() . '/share/db_schema.dba' );
        $effect = $drop->detect( $context );
        $this->assertSame( expUpgradeStep::PRESENT, $effect['effect'], 'a new database has no expupgrade_step: nothing to drop' );
        $this->assertSame( array(), $drop->plan( $context ) );
        $step->apply( $context );
        $this->assertSame( expMigrationRecord::TABLE, expUpgradeRecord::table( $db ) );
        $this->assertTrue( expUpgradeRecord::write( $db, $step, 'done', 1200, 'made' ) );
        $this->assertTrue( expUpgradeRecord::writeCaption( $db, 'c2', 'pre', 'before', 'git_manager' ) );
        $all = expUpgradeRecord::all( $db );
        $this->assertSame( array( '6.0.15-migration-tables', 'caption:c2' ), array_keys( $all ) );
        $this->assertSame( 1200, $all['6.0.15-migration-tables']['duration_ms'] );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{64}$/', $all['6.0.15-migration-tables']['checksum'] );
        $this->assertSame( 'before (git_manager)', $all['caption:c2']['message'] );
        $this->assertSame( 0, expMigrationRecord::legacyPending( $db ) );
        $this->assertSame( array(), $drop->apply( $context ) );
        expKernelTableSchema::reset();
        $this->assertFalse( expKernelTableSchema::exists( expMigrationRecord::LEGACY_TABLE, $db, true ), 'expupgrade_step is never made' );
    }
}
