<?php
/**
 * File containing the expMigrationSimulation class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The simulated run of exp:migration:dry-run --simulate: the pending migrations run with up(), one after the other,
 * inside one transaction that is rolled back at the end, so the database is as it was before. It shows whether
 * each migration runs through, the messages it gives and, with diff, what it changed in the database (tables added
 * and dropped, columns added and dropped, rows added, removed or changed per table).
 *
 * Only where it is safe: on engines whose transactions also undo schema changes (PostgreSQL, SQLite). MySQL/MariaDB
 * and Oracle commit a schema change at once and MongoDB has no transactions; there the simulation is not run, the
 * report says so in a warning and lists the plan as a dry run does.
 *
 * What a migration writes outside the database (files in var/storage, caches, the search index, mail) is not undone
 * by the rollback; the report warns about it. No record is written, no caption taken, nothing committed. The run
 * holds the installation lock (shared with exp:upgrade and the migration runs) and runs as the run's user.
 *
 * The report is the runner's dry run (exp-migration/1) with mode "simulate"; each migration that was simulated has
 * the action "simulated" and 'simulation' => array( result (passed, failed, not-run), messages, duration_ms, diff ).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationSimulation
{
    /** The largest table whose rows are compared by content on SQLite (bigger ones by their count only). */
    const SQLITE_HASH_MAX_ROWS = 20000;

    /** @var expMigrationContext a context that writes */
    protected $context;

    /** @var array */
    protected $options;

    /**
     * @param expMigrationContext $context a context that writes (isApply()); give it no-mirror
     * @param array $options diff (bool), ignore-tables (string[], default the caption's ReuseIgnoreTableList),
     *                       dry-context (the context of the dry run before the simulation; one that does not write on
     *                       the same database is made when not given)
     */
    public function __construct( expMigrationContext $context, array $options = array() )
    {
        $this->context = $context;
        $this->options = $options + array( 'diff' => false, 'ignore-tables' => expMigrationCaption::defaults()['ignore-tables'], 'dry-context' => null );
    }

    /**
     * Whether an engine can simulate: its transactions undo schema changes. Pure.
     *
     * @param string $engine
     * @return bool
     */
    public static function supports( $engine )
    {
        return expMigrationContext::transactionModeOf( $engine ) === expMigrationContext::TRANSACTION_FULL;
    }

    /**
     * The dry run, then the simulation of the migrations it would apply.
     *
     * @param expMigration[] $migrations in their order
     * @return array the report
     */
    public function run( array $migrations )
    {
        $context = $this->context;
        $dryContext = $this->options['dry-context'] instanceof expMigrationContext ? $this->options['dry-context']
                    : new expMigrationContext( $context->db(), false, array( 'run-id' => $context->runId(), 'environment' => $context->environment(),
                                                                             'no-mirror' => true, 'root' => $context->root(),
                                                                             'lock-file' => $context->option( 'lock-file' ),
                                                                             'user' => $context->user() ) );
        $report = ( new expMigrationRunner( $dryContext ) )->run( $migrations );
        $report['mode'] = 'simulate';
        $report['simulated'] = false;
        if ( $report['exit'] !== expUpgrade::EXIT_OK )
            return $report;
        $pending = array();
        foreach ( $report['migrations'] as $i => $entry )
            if ( $entry['action'] === 'would-apply' )
                $pending[] = $i;
        if ( !$pending )
            return $report;
        if ( !self::supports( $context->engine() ) )
        {
            $report['warnings'][] = 'no simulation on ' . $context->engine() . ': its transactions do not undo schema changes'
                                  . ( $context->engine() === 'mongodb' ? ' (MongoDB has none)' : ' (it commits them at once)' )
                                  . '; the plan is listed instead. PostgreSQL and SQLite simulate.';
            return $report;
        }
        if ( !$context->user() )
        {
            $report['errors'][] = 'the user to run as does not exist: ' . ( $context->option( 'login' ) ?: '[RunSettings] RunAsUser=' . expMigrationContext::runAsLogin() . ' (migration.ini)' );
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        $byId = array();
        foreach ( $migrations as $migration )
            $byId[(string)$migration->id()] = $migration;

        $lock = $context->lock();
        $heldBefore = $lock->isHeld();
        if ( !$lock->acquire( array( 'purpose' => 'migration simulation ' . $context->runId(), 'user' => $context->userLogin() ) ) )
        {
            $report['errors'][] = 'another run holds the lock: ' . expMigrationLock::describe( $lock->holder() );
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        $report['simulated'] = true;
        $report['user'] = $context->userLogin();
        $report['warnings'][] = 'simulated in a transaction that was rolled back: the database is unchanged, but what the migrations wrote '
                              . 'outside it (files, caches, the search index, mail) is not undone';
        $failed = false;
        $context->becomeUser();
        try
        {
            $context->begin();
            try
            {
                foreach ( $pending as $i )
                {
                    $entry =& $report['migrations'][$i];
                    $entry['action'] = 'simulated';
                    if ( $failed )
                    {
                        $entry['simulation'] = array( 'result' => 'not-run', 'messages' => array(), 'duration_ms' => 0, 'diff' => array() );
                        unset( $entry );
                        continue;
                    }
                    $migration = $byId[$entry['id']];
                    $before = $this->options['diff'] ? $this->snapshot() : null;
                    $start = microtime( true );
                    $result = array( 'result' => 'passed', 'messages' => array(), 'duration_ms' => 0, 'diff' => array() );
                    try
                    {
                        $result['messages'] = array_values( array_map( 'strval', (array)$migration->up( $context ) ) );
                        if ( $migration instanceof expMigrationPhp )
                        {
                            $detected = $migration->detect( $context );
                            if ( $detected !== null && !in_array( $detected, array( expMigration::DONE, expMigration::NOT_NEEDED ), true ) )
                                throw new RuntimeException( "ran, but detection still says $detected" );
                        }
                    }
                    catch ( Throwable $e )
                    {
                        $result['result'] = 'failed';
                        $result['messages'][] = $e->getMessage();
                        $report['errors'][] = $entry['id'] . ' failed in the simulation: ' . $e->getMessage();
                        $failed = true;
                    }
                    $result['duration_ms'] = (int)round( ( microtime( true ) - $start ) * 1000 );
                    if ( $before !== null && !$failed )
                        $result['diff'] = self::diff( $before, $this->snapshot() );
                    $entry['simulation'] = $result;
                    unset( $entry );
                }
            }
            finally
            {
                $context->rollback();
            }
        }
        catch ( Throwable $e )
        {
            $report['errors'][] = 'internal error: ' . $e->getMessage();
            $report['exit'] = expUpgrade::EXIT_INTERNAL;
        }
        finally
        {
            $context->restoreUser();
            expKernelTableSchema::reset();
            if ( class_exists( 'eZContentObject' ) )
                eZContentObject::clearCache();
            if ( !$heldBefore )
                $lock->release();
        }
        $context->audit( 'system.migration.run', array( 'object' => array( 'type' => 'migration_run', 'id' => $context->runId() ), 'verb' => 'simulate',
                                                        'result' => $failed ? 'failure' : 'success',
                                                        'x' => array( 'engine' => $context->engine(), 'environment' => $context->environment(),
                                                                      'migrations' => count( $pending ) ) ) );
        $this->summarize( $report );
        if ( $failed && $report['exit'] === expUpgrade::EXIT_OK )
            $report['exit'] = expUpgrade::EXIT_FAILED;
        return $report;
    }

    /**
     * Recounts the actions of the summary after the simulation.
     *
     * @param array $report
     */
    protected function summarize( array &$report )
    {
        foreach ( array( 'would-apply', 'simulated' ) as $action )
            $report['summary'][$action] = 0;
        foreach ( $report['migrations'] as $entry )
            if ( isset( $report['summary'][$entry['action']] ) )
                $report['summary'][$entry['action']]++;
    }

    /**
     * The tables of the database as the diff compares them: name => array( columns (string[]), rows (int), hash
     * (string, '' when not computed) ).
     *
     * @return array
     */
    public function snapshot()
    {
        $db = $this->context->db();
        $engine = $this->context->engine();
        $ignore = array_map( 'strtolower', (array)$this->options['ignore-tables'] );
        $tables = array();
        if ( $engine === 'sqlite' )
        {
            foreach ( (array)$db->arrayQuery( "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name" ) as $row )
                $tables[] = (string)array_values( (array)$row )[0];
        }
        else
        {
            expKernelTableSchema::reset();
            $tables = array_values( (array)expKernelTableSchema::tables( $db ) );
        }
        sort( $tables, SORT_STRING );
        $out = array();
        foreach ( $tables as $table )
        {
            if ( expMigrationCaption::ignoredTable( $table, $ignore ) )
                continue;
            $quoted = '"' . str_replace( '"', '""', $table ) . '"';
            $columns = array();
            $rows = 0;
            $hash = '';
            if ( $engine === 'sqlite' )
            {
                foreach ( (array)$db->arrayQuery( 'PRAGMA table_info(' . $quoted . ')' ) as $col )
                    $columns[] = (string)$col['name'];
                $count = $db->arrayQuery( 'SELECT count(*) AS n FROM ' . $quoted );
                $rows = (int)( $count ? array_values( (array)$count[0] )[0] : 0 );
                if ( $rows <= self::SQLITE_HASH_MAX_ROWS )
                {
                    $h = hash_init( 'sha256' );
                    foreach ( (array)$db->arrayQuery( 'SELECT * FROM ' . $quoted ) as $r )
                        hash_update( $h, serialize( array_values( (array)$r ) ) );
                    $hash = hash_final( $h );
                }
            }
            else
            {
                foreach ( (array)$db->arrayQuery( "SELECT column_name FROM information_schema.columns WHERE table_schema = current_schema() AND table_name = '"
                                                  . $db->escapeString( $table ) . "' ORDER BY ordinal_position" ) as $col )
                    $columns[] = (string)array_values( (array)$col )[0];
                // a row written in this transaction gets a new row version (xmin)
                $count = $db->arrayQuery( 'SELECT count(*) AS n, coalesce(sum(xmin::text::bigint), 0) AS x FROM ' . $quoted );
                $values = $count ? array_values( (array)$count[0] ) : array( 0, 0 );
                $rows = (int)$values[0];
                $hash = (string)$values[1];
            }
            $out[$table] = array( 'columns' => $columns, 'rows' => $rows, 'hash' => $hash );
        }
        return $out;
    }

    /**
     * What changed between two snapshots, one line per table. Pure.
     *
     *   + table name (3 columns, 1 row)
     *   - table name (12 rows)
     *   ~ table name: columns +a, -b; rows 4 -> 5
     *   ~ table name: rows changed (4)
     *
     * @param array $before snapshot()
     * @param array $after snapshot()
     * @return string[]
     */
    public static function diff( array $before, array $after )
    {
        $lines = array();
        $names = array_unique( array_merge( array_keys( $before ), array_keys( $after ) ) );
        sort( $names, SORT_STRING );
        $rows = function ( $n ) {
            return $n . ' row' . ( $n === 1 ? '' : 's' );
        };
        foreach ( $names as $name )
        {
            if ( !isset( $before[$name] ) )
            {
                $lines[] = "+ table $name (" . count( $after[$name]['columns'] ) . ' columns, ' . $rows( (int)$after[$name]['rows'] ) . ')';
                continue;
            }
            if ( !isset( $after[$name] ) )
            {
                $lines[] = "- table $name (" . $rows( (int)$before[$name]['rows'] ) . ')';
                continue;
            }
            $b = $before[$name];
            $a = $after[$name];
            $parts = array();
            $added = array_values( array_diff( $a['columns'], $b['columns'] ) );
            $dropped = array_values( array_diff( $b['columns'], $a['columns'] ) );
            if ( $added || $dropped )
                $parts[] = 'columns ' . implode( ', ', array_merge( array_map( function ( $c ) { return "+$c"; }, $added ),
                                                                    array_map( function ( $c ) { return "-$c"; }, $dropped ) ) );
            if ( (int)$a['rows'] !== (int)$b['rows'] )
                $parts[] = 'rows ' . (int)$b['rows'] . ' -> ' . (int)$a['rows'];
            else if ( $a['hash'] !== '' && $b['hash'] !== '' && $a['hash'] !== $b['hash'] )
                $parts[] = 'rows changed (' . (int)$a['rows'] . ')';
            if ( $parts )
                $lines[] = "~ table $name: " . implode( '; ', $parts );
        }
        return $lines;
    }
}
