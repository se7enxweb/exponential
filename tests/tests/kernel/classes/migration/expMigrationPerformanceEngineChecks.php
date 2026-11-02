<?php
/**
 * The performance checks of the migration engine that run on every engine the same way: SQLite in process
 * (expMigrationPerformanceSqliteTest), MySQL/MariaDB and PostgreSQL in the database job
 * (expMigrationPerformanceDatabaseTest), MongoDB in the live MongoDB suite (expMigrationPerformanceMongoDBTest).
 * Each check asserts what the run did, keeps a generous upper bound on its time, and writes its measurements (seconds,
 * memory) as one JSON line to the file EXP_MIGRATION_PERF_REPORT when that is set.
 *
 *  PF-1 1,000 YAML migrations of one step each: discovery, ordering and checksums; loading; the run that applies all
 *       of them (a record row and a run log entry each); a second run that has nothing to do; the status of 1,000
 *  PF-2 a large data step: one migration inserts 5,000 rows, a second updates all of them, a third deletes them
 *  PF-3 a long run with checkpoints: a PHP migration handles 5,000 rows in batches of 250 with a checkpoint after each,
 *       is stopped by a failure at row 2,600 and goes on from the last checkpoint in a second run
 *
 * EXP_MIGRATION_PERF_COUNT changes the number of migrations of PF-1 (default 1000), EXP_MIGRATION_PERF_ROWS the rows
 * of PF-2 and PF-3 (default 5000).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';

trait expMigrationPerformanceEngineChecks
{
    use expMigrationRunnerEngineChecks;

    /** The table of the checks. */
    protected static $perfTable = 'exp_test_wp17_perf';

    /** @return int a positive number from the environment, else the default */
    protected static function perfNumber( $name, $default )
    {
        $value = (int)getenv( $name );
        return $value > 0 ? $value : $default;
    }

    /** Starts a measurement: the peak of memory from here on where PHP can reset it. */
    protected static function perfStart()
    {
        if ( function_exists( 'memory_reset_peak_usage' ) )
            memory_reset_peak_usage();
        return array( microtime( true ), memory_get_usage() );
    }

    /** @return array seconds and the memory in MiB the step took from perfStart() */
    protected static function perfStop( array $start )
    {
        return array( 'seconds' => round( microtime( true ) - $start[0], 4 ),
                      'memory_mib' => round( max( 0, memory_get_peak_usage() - $start[1] ) / 1048576, 2 ) );
    }

    /**
     * Appends a line to the report file of EXP_MIGRATION_PERF_REPORT.
     *
     * @param eZDBInterface $db
     * @param string $check
     * @param array $values
     */
    protected function perfReport( $db, $check, array $values )
    {
        $file = trim( (string)getenv( 'EXP_MIGRATION_PERF_REPORT' ) );
        if ( $file === '' )
            return;
        $line = array( 'engine' => expKernelTableSchema::type( $db ), 'check' => $check, 'php' => PHP_VERSION ) + $values;
        file_put_contents( $file, json_encode( $line ) . "\n", FILE_APPEND | LOCK_EX );
    }

    /**
     * The table of the checks with an id and a value, empty.
     *
     * @param eZDBInterface $db
     */
    protected function perfTable( $db )
    {
        $this->dropRunnerTable( $db, self::$perfTable );
        $schema = array( self::$perfTable => array(
            'name' => self::$perfTable,
            'fields' => array( 'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                               'value' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
        $messages = array();
        $this->assertTrue( expKernelTableSchema::install( array( self::$perfTable ), $db, $schema, $messages ), implode( '; ', $messages ) );
        expKernelTableSchema::reset();
    }

    /**
     * Removes the table and the records of the checks.
     *
     * @param eZDBInterface $db
     */
    protected function perfClean( $db )
    {
        $this->dropRunnerTable( $db, self::$perfTable );
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '%_wp17_%'" );
        $db->query( 'DELETE FROM ' . expMigrationRecord::RUN_TABLE );
    }

    /**
     * @param array $report
     * @return string
     */
    protected static function perfWhy( array $report )
    {
        return implode( '; ', $report['errors'] ) . ' ' . json_encode( array_slice( array_column( $report['migrations'], 'messages' ), 0, 5 ) );
    }

    /**
     * PF-1
     *
     * @param eZDBInterface $db
     */
    protected function checkThousandMigrations( $db )
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        $count = self::perfNumber( 'EXP_MIGRATION_PERF_COUNT', 1000 );
        $engine = expKernelTableSchema::type( $db );
        $this->perfClean( $db );
        $this->perfTable( $db );
        $dir = $this->runnerDir() . '/thousand';
        mkdir( $dir, 0775, true );
        $table = self::$perfTable;
        $start = strtotime( '2026-11-02 00:00:00 UTC' );
        for ( $i = 0; $i < $count; $i++ )
        {
            $id = gmdate( 'YmdHis', $start + $i ) . '_wp17_noop_' . $i;
            $requires = $i > 0 && $i % 10 === 0 ? '# requires: ' . gmdate( 'YmdHis', $start + $i - 1 ) . '_wp17_noop_' . ( $i - 1 ) . "\n" : '';
            file_put_contents( "$dir/$id.yml", "# description: Migration $i of $count\n$requires" .
                "- type: data\n  mode: update\n  table: $table\n  where: { id: -1 }\n  values: { value: $i }\n" );
        }

        $discover = self::perfStart();
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $dir ) ) );
        $graph = new expMigrationGraph( $locator->find() );
        $sums = array();
        foreach ( $graph->migrations() as $definition )
            $sums[] = $definition->checksumFor( $engine );
        $discovered = self::perfStop( $discover );
        $this->assertSame( array(), $locator->errors() );
        $this->assertSame( array(), $graph->errors() );
        $this->assertCount( $count, array_filter( $sums ) );

        $load = self::perfStart();
        $errors = array();
        $migrations = expMigrationRunner::load( $graph->migrations(), $errors );
        $loaded = self::perfStop( $load );
        $this->assertSame( array(), $errors );
        $this->assertCount( $count, $migrations );

        $run = self::perfStart();
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $ran = self::perfStop( $run );
        $this->assertSame( 0, $report['exit'], self::perfWhy( $report ) );
        $this->assertSame( $count, $report['summary']['applied'], self::perfWhy( $report ) );
        $this->assertSame( $count, self::runnerCount( $db, expMigrationRecord::TABLE, "migration_id LIKE '%_wp17_noop_%' AND state = 'done'" ) );

        $again = self::perfStart();
        $second = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $rerun = self::perfStop( $again );
        $this->assertSame( 0, $second['exit'], self::perfWhy( $second ) );
        $this->assertSame( 0, $second['summary']['applied'], 'nothing to do the second time' );

        $status = self::perfStart();
        $done = 0;
        foreach ( $migrations as $migration )
        {
            $row = expMigrationRecord::get( $db, 'site', $migration->id() );
            if ( $row && $row['state'] === 'done' )
                $done++;
        }
        $statused = self::perfStop( $status );
        $this->assertSame( $count, $done );

        $values = array( 'migrations' => $count,
                         'discover_seconds' => $discovered['seconds'], 'load_seconds' => $loaded['seconds'],
                         'run_seconds' => $ran['seconds'], 'run_ms_per_migration' => round( $ran['seconds'] * 1000 / $count, 2 ),
                         'rerun_seconds' => $rerun['seconds'], 'status_seconds' => $statused['seconds'],
                         'memory_mib' => max( $discovered['memory_mib'], $loaded['memory_mib'], $ran['memory_mib'], $rerun['memory_mib'] ) );
        $this->perfReport( $db, 'PF-1', $values );
        // R3.29: discovery, ordering and checksums under 1 s (3 s with the margin of a shared or CI host)
        $this->assertLessThan( getenv( 'CI' ) ? 3.0 : 1.5, $discovered['seconds'] * 1000 / $count, json_encode( $values ) );
        // a generous bound only: a migration that does nothing must not cost a quarter of a second
        $this->assertLessThan( 250.0, $values['run_ms_per_migration'], json_encode( $values ) );
        $this->perfClean( $db );
    }

    /**
     * PF-2
     *
     * @param eZDBInterface $db
     */
    protected function checkLargeDataStep( $db )
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        $rows = self::perfNumber( 'EXP_MIGRATION_PERF_ROWS', 5000 );
        $this->perfClean( $db );
        $this->perfTable( $db );
        $table = self::$perfTable;
        $dir = $this->runnerDir() . '/large';
        mkdir( $dir, 0775, true );
        $lines = array();
        for ( $id = 1; $id <= $rows; $id++ )
            $lines[] = "      - { id: $id, value: " . ( $id % 97 ) . ' }';
        file_put_contents( "$dir/20261102110000_wp17_insert.yml", "# description: $rows rows\n# transactional: no\n" .
            "- type: data\n  mode: insert\n  table: $table\n  rows:\n" . implode( "\n", $lines ) . "\n" );
        file_put_contents( "$dir/20261102110100_wp17_update.yml", "- type: data\n  mode: update\n  table: $table\n  all: true\n  values: { value: 1 }\n" );
        file_put_contents( "$dir/20261102110200_wp17_delete.yml", "- type: data\n  mode: delete\n  table: $table\n  all: true\n" );
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $dir ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        $this->assertCount( 3, $migrations );

        $measured = array();
        foreach ( array( 'insert' => $rows, 'update' => $rows, 'delete' => 0 ) as $step => $left )
        {
            $migration = array_shift( $migrations );
            $start = self::perfStart();
            $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( array( $migration ) );
            $measured[$step] = self::perfStop( $start );
            $this->assertSame( 0, $report['exit'], self::perfWhy( $report ) );
            $this->assertSame( 1, $report['summary']['applied'], self::perfWhy( $report ) );
            $this->assertSame( $left, self::runnerCount( $db, $table ), "$step: the rows left" );
            if ( $step === 'update' )
                $this->assertSame( $rows, self::runnerCount( $db, $table, 'value = 1' ), 'every row updated' );
        }
        $values = array( 'rows' => $rows );
        foreach ( $measured as $step => $m )
        {
            $values[$step . '_seconds'] = $m['seconds'];
            $values[$step . '_rows_per_second'] = $m['seconds'] > 0 ? (int)round( $rows / $m['seconds'] ) : null;
            $values[$step . '_memory_mib'] = $m['memory_mib'];
        }
        $this->perfReport( $db, 'PF-2', $values );
        $this->assertLessThan( 600.0, $measured['insert']['seconds'], json_encode( $values ) );
        $this->perfClean( $db );
    }

    /**
     * PF-3
     *
     * @param eZDBInterface $db
     */
    protected function checkLongRunWithCheckpoints( $db )
    {
        $rows = self::perfNumber( 'EXP_MIGRATION_PERF_ROWS', 5000 );
        $engine = expKernelTableSchema::type( $db );
        $this->perfClean( $db );
        $this->perfTable( $db );
        $table = self::$perfTable;
        $db->begin();
        for ( $first = 1; $first <= $rows; $first += 500 )
        {
            $values = array();
            for ( $id = $first; $id < $first + 500 && $id <= $rows; $id++ )
                $values[] = "( $id, 0 )";
            if ( $db instanceof expMongoDB || expKernelTableSchema::type( $db ) === 'oracle' )
                foreach ( $values as $value )
                    $db->query( "INSERT INTO $table ( id, value ) VALUES $value" );
            else
                $db->query( "INSERT INTO $table ( id, value ) VALUES " . implode( ', ', $values ) );
        }
        $db->commit();
        $this->assertSame( $rows, self::runnerCount( $db, $table ) );

        $checkpoints = $this->runnerDir() . '/checkpoints';
        $options = array( 'lock-file' => $this->runnerDir() . '/upgrade.lock', 'repository' => $this->runnerDir() . '/migrations',
                          'environment' => 'testing', 'root' => self::runnerRoot(), 'user' => eZUser::fetchByName( 'admin' ),
                          'checkpoint-directory' => $checkpoints, 'throttle' => 0 );
        $migration = new expMigrationPerformanceBatchMigration();
        $migration->setDefinition( new expMigrationDefinition( '20261102120000_wp17_long', 'site', 'php' ) );
        $migration->failAt = (int)( $rows * 0.52 );
        $stopAt = $migration->failAt;

        $first = self::perfStart();
        $report = ( new expMigrationRunner( new expMigrationContext( $db, true, $options ) ) )->run( array( $migration ) );
        $interrupted = self::perfStop( $first );
        $this->assertSame( 'failed', $report['migrations'][0]['action'] );
        $offset = ( new expMigrationCheckpoint( '20261102120000_wp17_long', $checkpoints ) )->get( 'offset' );
        $this->assertSame( (int)( floor( ( $stopAt - 1 ) / 250 ) * 250 ), $offset, "$engine: the batches before the failure are kept" );
        $this->assertSame( 0, $db->transactionCounter(), 'no transaction left open' );

        $migration->failAt = null;
        $second = self::perfStart();
        $report = ( new expMigrationRunner( new expMigrationContext( $db, true, $options ) ) )->run( array( $migration ) );
        $resumed = self::perfStop( $second );
        $this->assertSame( 'applied', $report['migrations'][0]['action'], self::perfWhy( $report ) );
        $this->assertSame( $rows - $offset, $migration->handledCount, "$engine: the second run goes on after the checkpoint" );
        $this->assertSame( $rows, self::runnerCount( $db, $table, 'value = 1' ) );
        $this->assertFileDoesNotExist( $checkpoints . '/20261102120000_wp17_long.json', 'the checkpoint is cleared when done' );

        $values = array( 'rows' => $rows, 'batch' => 250, 'checkpoint_offset' => $offset,
                         'interrupted_seconds' => $interrupted['seconds'], 'resumed_seconds' => $resumed['seconds'],
                         'rows_per_second' => $resumed['seconds'] > 0 ? (int)round( ( $rows - $offset ) / $resumed['seconds'] ) : null,
                         'memory_mib' => max( $interrupted['memory_mib'], $resumed['memory_mib'] ) );
        $this->perfReport( $db, 'PF-3', $values );
        $this->assertLessThan( 600.0, $interrupted['seconds'] + $resumed['seconds'], json_encode( $values ) );
        $this->perfClean( $db );
    }
}

/** A migration that sets value = 1 on the rows of exp_test_wp17_perf 250 at a time and can fail at one (PF-3). */
class expMigrationPerformanceBatchMigration extends expMigrationPhp
{
    public $failAt = null;
    public $handledCount = 0;

    public function transactional()
    {
        return false;
    }

    public function up( $context )
    {
        $db = $context->db();
        $this->handledCount = 0;
        $self = $this;
        $count = $this->batches( $context,
            function ( $offset, $limit ) use ( $db ) {
                $rows = $db->arrayQuery( 'SELECT id FROM exp_test_wp17_perf ORDER BY id', array( 'offset' => $offset, 'limit' => $limit ) );
                return array_map( 'intval', array_column( is_array( $rows ) ? $rows : array(), 'id' ) );
            },
            function ( $id ) use ( $db, $self ) {
                if ( $self->failAt === $id )
                    throw new RuntimeException( "stopped on purpose at $id" );
                $db->query( "UPDATE exp_test_wp17_perf SET value = 1 WHERE id = $id" );
                $self->handledCount++;
            }, 250 );
        return array( "$count rows" );
    }
}
