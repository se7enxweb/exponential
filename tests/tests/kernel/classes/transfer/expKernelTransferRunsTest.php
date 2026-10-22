<?php
/**
 * The run history and the runner of the import and export tools (expKernelTransferRun, expKernelTransferHistory,
 * expKernelTransferRunner, expKernelTransferContext, expContentJobTransfer; doc/eng-us/features/6.0/transfer-runs.md).
 * No installation: the table is checked in its schema files and in a SQLite database built in var/tmp, the rest
 * through the pure functions, a registry built here and tasks defined here.
 *
 *  TX-01 - The table is the same in share/db_schema.dba, the persistent object, the kernel schemas of MySQL,
 *          PostgreSQL and SQLite (sequence, primary key, setval) and the 6.0.0 to 6.0.15 update files (guarded)
 *  TX-02 - The SQLite update runs twice and keeps the rows; the indexes exist
 *  TX-03 - normalize(): numbers as int and never negative, texts cut, unknown state/kind/direction/engine left out,
 *          a detail_url that is not a module URL and a file_ref that is a path refused, detail and errors as JSON
 *  TX-04 - The outcome and the statistics row: the keys and values expImportStatistics::normalizeRun() reads
 *  TX-05 - conditions(): one tool or a list, an empty list matches nothing, kinds and states from their lists,
 *          from/to as a range on started
 *  TX-06 - The engine: auto takes expqueue, contentjob, process in that order; a named engine when available, else
 *          the same fallback; process always
 *  TX-07 - The payload check: tool id, task class, a class that is not a task, parameters
 *  TX-08 - execute(): the task's result, progress kept by the context, a cancel and a failure passed on
 *  TX-09 - The registry keys Engine=, QueueJobType=, HistoryAllPermission=; mayReadAllRuns(); visibleFilter() gives
 *          only the tools the user may use, everybody's runs only with HistoryAllPermission
 *  TX-10 - The settings: transfer.ini, the content job type transfer, the cronjob part purges the history
 *  TX-11 - The content job type refuses a payload without a task and describes a run by its tool and title
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expKernelTransferRunsTestTask implements expKernelTransferTask
{
    public static $mode = 'ok';

    public function run( array $params, expKernelTransferContext $context )
    {
        $context->progress( 5, 10, 'half' );
        if ( self::$mode === 'cancel' )
            throw new expKernelTransferCancelledException( 'stopped' );
        if ( self::$mode === 'fail' )
            throw new RuntimeException( 'the file could not be written' );
        $context->progress( 10, 10, 'all' );
        return array( 'rows_done' => 10, 'bytes' => 1234, 'file_ref' => 'export.csv', 'echo' => $params );
    }
}

class expKernelTransferRunsTestNotATask
{
}

class expKernelTransferRunsTest extends PHPUnit\Framework\TestCase
{
    private static $columns = array( 'bytes', 'created', 'detail', 'detail_url', 'direction', 'duration', 'engine', 'error',
                                     'file_ref', 'finished', 'format', 'id', 'job_ref', 'kind', 'modified', 'rows_done',
                                     'rows_failed', 'rows_total', 'scope', 'scope_ref', 'siteaccess', 'started', 'state',
                                     'title', 'tool', 'tool_ref', 'user_id' );

    private static $indexes = array( 'exp_transfer_run_started' => array( 'started' ), 'exp_transfer_run_tool' => array( 'tool', 'started' ),
                                     'exp_transfer_run_toolref' => array( 'tool', 'tool_ref' ), 'exp_transfer_run_user' => array( 'user_id' ) );

    protected function tearDown(): void
    {
        expKernelTransferRunner::setProbes( null );
        expKernelTransferRunsTestTask::$mode = 'ok';
    }

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $file )
    {
        return file_get_contents( self::root() . '/' . $file );
    }

    /** TX-01 */
    public function testTheTableIsTheSameEverywhere()
    {
        $schema = null;
        include self::root() . '/share/db_schema.dba';
        $this->assertArrayHasKey( 'exp_transfer_run', $schema );
        $table = $schema['exp_transfer_run'];
        $this->assertSame( self::$columns, array_keys( $table['fields'] ) );
        $this->assertSame( 'auto_increment', $table['fields']['id']['type'] );
        $this->assertSame( 'bigint', $table['fields']['bytes']['type'] );
        $this->assertSame( 'longtext', $table['fields']['detail']['type'] );
        $this->assertSame( array( 'id' ), $table['indexes']['PRIMARY']['fields'] );
        foreach ( self::$indexes as $name => $fields )
            $this->assertSame( $fields, $table['indexes'][$name]['fields'], $name );
        foreach ( $table['fields'] as $name => $field )
            if ( isset( $field['length'] ) && $field['type'] === 'varchar' )
                $this->assertSame( expKernelTransferRun::LENGTHS[$name], $field['length'], "length of $name" );

        $definition = expKernelTransferRun::definition();
        $persistent = array_keys( $definition['fields'] );
        sort( $persistent );
        $this->assertSame( self::$columns, $persistent );
        $this->assertSame( 'exp_transfer_run', $definition['name'] );

        $files = array( 'mysql' => 'kernel/sql/mysql/kernel_schema.sql', 'postgresql' => 'kernel/sql/postgresql/kernel_schema.sql',
                        'sqlite' => 'kernel/sql/sqlite/schema.sql', 'mysql-update' => 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql',
                        'postgresql-update' => 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql',
                        'sqlite-update' => 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
        foreach ( $files as $engine => $file )
        {
            $sql = self::read( $file );
            $this->assertMatchesRegularExpression( '/CREATE TABLE (?:IF NOT EXISTS )?`?exp_transfer_run`? \((.*?)\n\s*\)/s', $sql, $engine );
            preg_match( '/CREATE TABLE (?:IF NOT EXISTS )?`?exp_transfer_run`? \((.*?)\n\s*\)/s', $sql, $m );
            preg_match_all( '/^[\s,]*`?([a-z_]+)`? (?:int|integer|bigint|varchar|character|longtext|text)/m', $m[1], $cols );
            $this->assertSame( self::$columns, $cols[1], $engine );
            foreach ( array_keys( self::$indexes ) as $name )
                $this->assertMatchesRegularExpression( '/(?:KEY|INDEX(?: IF NOT EXISTS)?) ' . $name . ' /', $sql, "$engine $name" );
        }
        $pg = self::read( 'kernel/sql/postgresql/kernel_schema.sql' );
        $this->assertStringContainsString( 'CREATE SEQUENCE exp_transfer_run_id_seq', $pg );
        $this->assertStringContainsString( 'ADD CONSTRAINT exp_transfer_run_pkey PRIMARY KEY (id)', $pg );
        $this->assertStringContainsString( "setval('exp_transfer_run_id_seq'", self::read( 'kernel/sql/postgresql/setval.sql' ) );
        $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS exp_transfer_run', self::read( 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' ) );
        $pgUpdate = self::read( 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertMatchesRegularExpression( "/IF NOT EXISTS \( SELECT 1 FROM information_schema.tables WHERE table_schema = current_schema\(\) AND table_name = 'exp_transfer_run' \)/", $pgUpdate );
        // the MySQL update file stops at its last statement on a second run: the table comes before it
        $mysql = self::read( 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertLessThan( strpos( $mysql, 'ALTER TABLE ezprest_authcode' ), strpos( $mysql, 'CREATE TABLE IF NOT EXISTS exp_transfer_run' ) );
        // Oracle: no reserved word as a column, every identifier within 30 characters
        foreach ( array_merge( self::$columns, array_keys( self::$indexes ), array( 'exp_transfer_run', 'exp_transfer_run_id_seq' ) ) as $name )
            $this->assertLessThanOrEqual( 30, strlen( $name ), $name );
        $this->assertEmpty( array_intersect( self::$columns, array( 'file', 'size', 'level', 'mode', 'comment', 'number', 'session', 'user', 'uid', 'access', 'date' ) ) );
        $this->assertFileExists( self::root() . '/update/common/scripts/6.0/createtransferruntable.php' );
    }

    /** TX-02 */
    public function testSqliteUpdateRunsTwice()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::root() . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/transfer-run-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $apply = function () use ( $pdo ) {
            $sql = self::read( 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
            $start = strpos( $sql, '-- The run history of the import and export tools' );
            $section = substr( $sql, $start, strpos( $sql, "\n--\n-- ezuser.login", $start ) - $start );
            $body = preg_replace( '/^--.*$/m', '', $section );
            foreach ( preg_split( '/;\s*$/m', $body ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        $apply();
        $cols = array_column( $pdo->query( 'PRAGMA table_info(exp_transfer_run)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
        $this->assertSame( self::$columns, $cols );
        $pdo->exec( "INSERT INTO exp_transfer_run (tool, direction, kind, state, started) VALUES ('transfer.bccie', 'export', 'export', 'done', 100)" );
        $apply();
        $this->assertSame( '1', (string)$pdo->query( 'SELECT COUNT(*) FROM exp_transfer_run' )->fetchColumn(), 'the second run kept the row' );
        $names = array_column( $pdo->query( "SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = 'exp_transfer_run'" )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
        sort( $names );
        $this->assertSame( array_keys( self::$indexes ), $names );
        $pdo = null;
        @unlink( $file );
    }

    /** TX-03 */
    public function testNormalize()
    {
        $v = expKernelTransferRun::normalize( array(
            'rows_done' => '12', 'rows_failed' => -3, 'bytes' => 9000000000, 'user_id' => 'x',
            'title' => str_repeat( 'ä', 300 ), 'tool' => "transfer.xrowextract\n", 'state' => 'exploded', 'kind' => 'dry_run',
            'direction' => 'sideways', 'engine' => 'contentjob', 'detail_url' => 'https://evil.example/', 'file_ref' => '/var/site/x/../export.csv',
            'detail' => array( 'encoding' => 'UTF-8' ), 'errors' => array( 'first', '', array( 'no' ), 'second' ), 'unknown' => 1 ) );
        $this->assertSame( 12, $v['rows_done'] );
        $this->assertSame( 0, $v['rows_failed'] );
        $this->assertSame( 9000000000, $v['bytes'] );
        $this->assertArrayNotHasKey( 'user_id', $v );
        $this->assertSame( 255, mb_strlen( $v['title'] ) );
        $this->assertSame( 'transfer.xrowextract', $v['tool'] );
        $this->assertArrayNotHasKey( 'state', $v );
        $this->assertSame( 'dry_run', $v['kind'] );
        $this->assertArrayNotHasKey( 'direction', $v );
        $this->assertSame( 'contentjob', $v['engine'] );
        $this->assertArrayNotHasKey( 'detail_url', $v );
        $this->assertSame( 'export.csv', $v['file_ref'] );
        $this->assertSame( array( 'encoding' => 'UTF-8', 'errors' => array( 'first', 'second' ) ), json_decode( $v['detail'], true ) );
        $this->assertArrayNotHasKey( 'unknown', $v );

        $this->assertTrue( expKernelTransferRun::isRelativeUrl( 'xrowextract/jobs/(job)/abc' ) );
        $this->assertTrue( expKernelTransferRun::isRelativeUrl( '/bccie/job/20261022-1' ) );
        $this->assertFalse( expKernelTransferRun::isRelativeUrl( '//evil.example/x' ) );
        $this->assertFalse( expKernelTransferRun::isRelativeUrl( 'javascript:alert(1)' ) );
        $this->assertSame( array(), expKernelTransferRun::normalize( array() ) );
        $this->assertSame( '', expKernelTransferRun::normalize( array( 'detail' => array() ) )['detail'] );
    }

    /** TX-04 */
    public function testStatisticsRow()
    {
        $this->assertSame( 'completed', expKernelTransferRun::outcomeOf( 'done', 0 ) );
        $this->assertSame( 'completed_with_errors', expKernelTransferRun::outcomeOf( 'done', 2 ) );
        $this->assertSame( 'failed', expKernelTransferRun::outcomeOf( 'failed', 0 ) );
        $this->assertSame( 'cancelled', expKernelTransferRun::outcomeOf( 'cancelled', 0 ) );
        $this->assertSame( 'running', expKernelTransferRun::outcomeOf( 'queued', 0 ) );
        $this->assertSame( 'running', expKernelTransferRun::outcomeOf( 'running', 0 ) );

        $row = expKernelTransferHistory::toStatisticsRow( array( 'id' => '7', 'tool' => 'transfer.bccie', 'user_id' => 14, 'kind' => 'export',
            'state' => 'done', 'started' => 1000, 'finished' => 1060, 'rows_done' => 120, 'rows_failed' => 20, 'title' => 'Form',
            'format' => 'csv', 'bytes' => 99, 'engine' => 'expqueue', 'detail_url' => 'bccie/job/1' ) );
        $this->assertSame( array( 'id', 'import_id', 'tool', 'user_id', 'run_kind', 'status', 'started_at', 'finished_at',
                                  'created_count', 'updated_count', 'unchanged_count', 'skipped_count', 'failed_count',
                                  'processed_count', 'undone_count', 'title', 'format', 'bytes', 'engine', 'detail_url' ), array_keys( $row ) );
        $this->assertSame( 7, $row['id'] );
        $this->assertSame( 0, $row['import_id'] );
        $this->assertSame( 'export', $row['run_kind'] );
        $this->assertSame( 'completed_with_errors', $row['status'] );
        $this->assertSame( 1000, $row['started_at'] );
        $this->assertSame( 1060, $row['finished_at'] );
        $this->assertSame( 20, $row['failed_count'] );
        $this->assertSame( 100, $row['processed_count'], 'processed + failed = the rows done' );

        $running = expKernelTransferHistory::toStatisticsRow( array( 'STATE' => 'running', 'CREATED' => 50, 'STARTED' => 0, 'FINISHED' => 70 ) );
        $this->assertSame( 'running', $running['status'] );
        $this->assertSame( 50, $running['started_at'], 'a queued run counts from its creation' );
        $this->assertSame( 0, $running['finished_at'], 'a run that has not ended has no end' );
        $this->assertSame( 'export', $running['run_kind'] );
    }

    /** TX-05 */
    public function testConditions()
    {
        $this->assertSame( array(), expKernelTransferHistory::conditions( array() ) );
        $this->assertSame( array( 'tool' => 'transfer.bccie' ), expKernelTransferHistory::conditions( array( 'tool' => 'bccie' ) ) );
        $this->assertSame( array( 'tool' => array( array( 'transfer.bccie', 'transfer.xrowextract' ) ) ),
                           expKernelTransferHistory::conditions( array( 'tool' => array( 'transfer.bccie', 'xrowextract', '' ) ) ) );
        $this->assertSame( array( 'tool' => array( array( '' ) ) ), expKernelTransferHistory::conditions( array( 'tool' => array() ) ), 'no tool, no run' );
        $c = expKernelTransferHistory::conditions( array( 'kind' => array( 'export', 'nonsense' ), 'state' => 'done', 'user_id' => '14',
                                                          'direction' => 'export', 'from' => 100, 'to' => 200 ) );
        $this->assertSame( 'export', $c['kind'] );
        $this->assertSame( 'done', $c['state'] );
        $this->assertSame( 14, $c['user_id'] );
        $this->assertSame( 'export', $c['direction'] );
        $this->assertSame( array( false, array( 100, 199 ) ), $c['started'] );
        $this->assertSame( array( '>=', 100 ), expKernelTransferHistory::conditions( array( 'from' => 100 ) )['started'] );
        $this->assertSame( array( '<', 200 ), expKernelTransferHistory::conditions( array( 'to' => 200 ) )['started'] );
        $this->assertSame( array( array( '' ) ), expKernelTransferHistory::conditions( array( 'state' => array( 'x' ) ) )['state'] );
        $this->assertSame( 'transfer.package', expKernelTransferHistory::toolID( 'package' ) );
        $this->assertSame( 'transfer.package', expKernelTransferHistory::toolID( 'transfer.package' ) );
        $this->assertSame( 'import', expKernelTransferHistory::directionOf( 'transfer.x', 'dry_run' ) );
        $this->assertSame( 'export', expKernelTransferHistory::directionOf( 'transfer.x', 'export' ) );
    }

    /** TX-06 */
    public function testEngineChoice()
    {
        $all = array( 'expqueue' => true, 'contentjob' => true );
        $this->assertSame( 'expqueue', expKernelTransferRunner::choose( 'auto', $all ) );
        $this->assertSame( 'contentjob', expKernelTransferRunner::choose( 'auto', array( 'expqueue' => false, 'contentjob' => true ) ) );
        $this->assertSame( 'process', expKernelTransferRunner::choose( 'auto', array( 'expqueue' => false, 'contentjob' => false ) ) );
        $this->assertSame( 'contentjob', expKernelTransferRunner::choose( 'contentjob', $all ) );
        $this->assertSame( 'process', expKernelTransferRunner::choose( 'process', $all ) );
        $this->assertSame( 'contentjob', expKernelTransferRunner::choose( 'expqueue', array( 'expqueue' => false, 'contentjob' => true ) ), 'a named engine that is not there falls back' );
        $this->assertSame( 'process', expKernelTransferRunner::choose( 'process', array() ) );

        expKernelTransferRunner::setProbes( array( 'expqueue' => false, 'contentjob' => true ) );
        $engines = expKernelTransferRunner::engines( 'transfer.nothere' );
        $this->assertFalse( $engines['expqueue']['available'] );
        $this->assertNotSame( '', $engines['expqueue']['reason'] );
        $this->assertTrue( $engines['contentjob']['available'] );
        $this->assertTrue( $engines['process']['available'] );
    }

    /** TX-07 */
    public function testPayloadCheck()
    {
        $ok = array( 'tool' => 'transfer.bccie', 'task' => 'expKernelTransferRunsTestTask', 'params' => array( 'job' => 'a' ) );
        $this->assertSame( '', expKernelTransferRunner::payloadProblem( $ok ) );
        $this->assertNotSame( '', expKernelTransferRunner::payloadProblem( array( 'task' => 'expKernelTransferRunsTestTask' ) ) );
        $this->assertNotSame( '', expKernelTransferRunner::payloadProblem( array( 'tool' => 'bccie' ) + $ok ), 'the registry id, not the short one' );
        $this->assertNotSame( '', expKernelTransferRunner::payloadProblem( array( 'task' => 'expKernelTransferRunsTestNotATask' ) + $ok ) );
        $this->assertNotSame( '', expKernelTransferRunner::payloadProblem( array( 'task' => 'eZUser; rm' ) + $ok ) );
        $this->assertNotSame( '', expKernelTransferRunner::payloadProblem( array( 'params' => 'x' ) + $ok ) );
    }

    /** TX-08 */
    public function testExecute()
    {
        $seen = array();
        $context = new expKernelTransferContext( 'inline', 'transfer.bccie', 0, 'job-1', array(
            'progress' => function ( $done, $total, $message ) use ( &$seen ) { $seen[] = "$done/$total $message"; } ) );
        $result = expKernelTransferRunner::execute( 'expKernelTransferRunsTestTask', array( 'job' => 'a' ), $context );
        $this->assertSame( 10, $result['rows_done'] );
        $this->assertSame( array( 'job' => 'a' ), $result['echo'] );
        $this->assertSame( array( '5/10 half', '10/10 all' ), $seen );
        $this->assertSame( 10, $context->lastProgress()['done'] );
        $this->assertSame( 'inline', $context->engine() );
        $this->assertSame( 'job-1', $context->jobRef() );

        expKernelTransferRunsTestTask::$mode = 'cancel';
        try
        {
            expKernelTransferRunner::execute( 'expKernelTransferRunsTestTask', array(), new expKernelTransferContext( 'inline', 'transfer.bccie' ) );
            $this->fail( 'a cancel is passed on' );
        }
        catch ( expKernelTransferCancelledException $e )
        {
            $this->assertSame( 'stopped', $e->getMessage() );
        }
        expKernelTransferRunsTestTask::$mode = 'fail';
        try
        {
            expKernelTransferRunner::runPayload( array( 'tool' => 'transfer.bccie', 'task' => 'expKernelTransferRunsTestTask' ), 'inline', '' );
            $this->fail( 'a failure is passed on' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertSame( 'the file could not be written', $e->getMessage() );
        }
        $this->expectException( RuntimeException::class );
        expKernelTransferRunner::runPayload( array( 'tool' => 'transfer.bccie', 'task' => 'expKernelTransferRunsTestNotATask' ), 'inline', '' );
    }

    /** TX-09 */
    public function testRegistryKeysAndVisibility()
    {
        $export = expKernelTransferTool::fromBlock( 'transfer.export', array( 'Name' => 'Export', 'EntryURL' => 'xrowextract/csv',
            'Permission' => 'xrowextract/csv', 'Engine' => 'ContentJob', 'QueueJobType' => 'xrowextract.run',
            'HistoryAllPermission' => 'xrowextract/all_jobs' ) );
        $this->assertSame( 'contentjob', $export->get( 'engine' ) );
        $this->assertSame( 'xrowextract.run', $export->get( 'queue_job_type' ) );
        $this->assertSame( array( array( 'xrowextract', 'all_jobs' ) ), $export->get( 'history_all_permissions' ) );
        $cie = expKernelTransferTool::fromBlock( 'transfer.cie', array( 'Name' => 'CIE', 'EntryURL' => 'bccie/overview',
            'Permission' => 'bccie/read', 'Engine' => 'rocket', 'QueueJobType' => 'bad type!' ) );
        $this->assertSame( 'auto', $cie->get( 'engine' ) );
        $this->assertSame( '', $cie->get( 'queue_job_type' ) );
        $this->assertSame( array(), $cie->get( 'history_all_permissions' ) );
        $hidden = expKernelTransferTool::fromBlock( 'transfer.hidden', array( 'Name' => 'Hidden', 'EntryURL' => 'hidden/list', 'Permission' => 'hidden/use' ) );

        $granted = array( 'xrowextract/csv', 'bccie/read', 'xrowextract/all_jobs' );
        $access = function ( $module, $function ) use ( &$granted ) { return in_array( "$module/$function", $granted, true ); };
        $registry = new expKernelTransferRegistry( array( $export, $cie, $hidden ), function () { return true; } );
        $this->assertTrue( $export->mayReadAllRuns( $access ) );
        $this->assertFalse( $cie->mayReadAllRuns( $access ), 'without HistoryAllPermission only the own runs' );

        $visible = expKernelTransferHistory::visibleFilter( null, $access, 14, $registry );
        // in the registry's order (weight, then name)
        $this->assertSame( array( array( 'tool' => 'transfer.cie', 'user_id' => 14 ), array( 'tool' => 'transfer.export', 'user_id' => null ) ), $visible );
        $this->assertSame( array( array( 'tool' => 'transfer.cie', 'user_id' => 14 ) ),
                           expKernelTransferHistory::visibleFilter( array( 'cie' ), $access, 14, $registry ) );
        $granted = array( 'bccie/read' );
        $this->assertSame( array( array( 'tool' => 'transfer.cie', 'user_id' => 14 ) ), expKernelTransferHistory::visibleFilter( null, $access, 14, $registry ) );
    }

    /** TX-10 */
    public function testSettings()
    {
        $ini = self::read( 'settings/transfer.ini' );
        $this->assertMatchesRegularExpression( '/^\[TransferSettings\]$/m', $ini );
        $this->assertMatchesRegularExpression( '/^Engine=auto$/m', $ini );
        $this->assertMatchesRegularExpression( '/^ToolEngine\[\]$/m', $ini );
        $this->assertMatchesRegularExpression( '/^QueueNeedsWorker=enabled$/m', $ini );
        $this->assertMatchesRegularExpression( '/^KeepDays=365$/m', $ini );
        $this->assertMatchesRegularExpression( '/^JobTypes\[transfer\]=expContentJobTransfer$/m', self::read( 'settings/content.ini' ) );
        $this->assertTrue( class_exists( 'expContentJobTransfer' ) );
        $this->assertTrue( in_array( 'expContentJobType', class_implements( 'expContentJobTransfer' ), true ) );
        $this->assertStringContainsString( 'expKernelTransferHistory::purge()', self::read( 'kernel/private/classes/cronjobs/contentjobs.php' ) );
        // without expqueue the queue handler file defines nothing (and loads without an error)
        if ( !interface_exists( 'expQueueHandler' ) )
        {
            require_once self::root() . '/kernel/classes/transfer/expkerneltransferqueuehandler.php';
            $this->assertFalse( class_exists( 'expKernelTransferQueueHandler', false ) );
        }
    }

    /** TX-11 */
    public function testContentJobType()
    {
        $type = new expContentJobTransfer();
        $user = $this->createStub( 'eZUser' );
        try
        {
            $type->validate( array( 'tool' => 'transfer.bccie' ), $user );
            $this->fail( 'a payload without a task is refused' );
        }
        catch ( expContentJobException $e )
        {
            $this->assertStringContainsString( 'task', $e->getMessage() );
        }
        $this->assertSame( array(), $type->locks( array() ) );
        $this->assertSame( 42, $type->countNodes( array( 'params' => array( 'rows_total' => 42 ) ) ) );
        $this->assertStringContainsString( 'CIE', $type->describe( array( 'tool' => 'transfer.bccie', 'tool_name' => 'CIE', 'title' => 'Form 42' ) ) );
        $this->assertStringContainsString( 'Form 42', $type->describe( array( 'tool' => 'transfer.bccie', 'tool_name' => 'CIE', 'title' => 'Form 42' ) ) );
        $this->assertStringContainsString( 'transfer.x', $type->describe( array( 'tool' => 'transfer.x', 'title' => '' ) ) );
    }
}
