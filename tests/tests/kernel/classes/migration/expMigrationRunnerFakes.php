<?php
/**
 * The fakes of expMigrationRunnerTest: a run context that keeps everything the runner writes in memory, and a
 * migration whose behavior a test sets with closures.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/**
 * A run context without a database. Options besides expMigrationContext's:
 *   engine       the engine it claims (sqlite)
 *   no-user      true: there is no user to run as
 *   no-table     true: the record tables do not exist
 *   maintenance  what maintenanceBegin() answers ('enabled')
 * Public: records (source\nid => row), runs (every run row written), calls (what changed something, in order),
 * audits (name, data), logs (level, text).
 */
class expMigrationRunnerTestContext extends expMigrationContext
{
    public $records = array();
    public $runs = array();
    public $calls = array();
    public $audits = array();
    public $logs = array();

    public function __construct( $apply = true, array $options = array() )
    {
        parent::__construct( new stdClass(), $apply, $options + array( 'environment' => 'staging', 'engine' => 'sqlite', 'maintenance' => 'enabled' ) );
    }

    public function engine()
    {
        return (string)$this->option( 'engine' );
    }

    public function user()
    {
        return $this->option( 'no-user' ) ? null : new stdClass();
    }

    public function userLogin()
    {
        return $this->option( 'no-user' ) ? '' : 'tester';
    }

    public function runBy()
    {
        return 'cli ' . $this->userLogin();
    }

    public function record( $source, $id )
    {
        $key = $source . "\n" . $id;
        return isset( $this->records[$key] ) ? $this->records[$key] : null;
    }

    public function recordReady()
    {
        return !$this->option( 'no-table' );
    }

    public function writeRecord( array $row )
    {
        $this->calls[] = 'write-record';
        $this->records[$row['source'] . "\n" . $row['migration_id']] = expMigrationRecord::normalize( $row, expMigrationRecord::COLUMNS );
        return true;
    }

    public function removeRecord( $source, $id )
    {
        $this->calls[] = 'remove-record';
        unset( $this->records[$source . "\n" . $id] );
        return true;
    }

    public function writeRun( array $run )
    {
        $this->calls[] = 'write-run';
        $this->runs[] = expMigrationRecord::normalize( $run, expMigrationRecord::RUN_COLUMNS );
        return true;
    }

    public function writeStatus()
    {
        $this->calls[] = 'write-status';
        return null;
    }

    public function log( $level, $text )
    {
        $this->logs[] = array( $level, (string)$text );
    }

    public function maintenanceBegin()
    {
        $this->calls[] = 'maintenance-begin';
        return $this->option( 'maintenance' );
    }

    public function maintenanceEnd()
    {
        $this->calls[] = 'maintenance-end';
        return true;
    }

    public function begin()
    {
        $this->calls[] = 'begin';
    }

    public function commit()
    {
        $this->calls[] = 'commit';
    }

    public function rollback()
    {
        $this->calls[] = 'rollback';
    }

    public function audit( $name, array $data )
    {
        $this->audits[] = array( $name, $data );
    }

    public function becomeUser()
    {
        $this->calls[] = 'become-user';
    }

    public function restoreUser()
    {
        $this->calls[] = 'restore-user';
    }
}

/**
 * A migration of the source "site" set up by options: up, down, state (closures taking the context), requires,
 * engines, environments, maintenance, reversible, transactional, checksum (checksum-<id> by default).
 */
class expMigrationRunnerFake implements expMigration
{
    private $id;
    private $options;

    public function __construct( $id, array $options = array() )
    {
        $this->id = $id;
        $this->options = $options + array( 'requires' => array(), 'engines' => array( '*' ), 'environments' => array(), 'maintenance' => false,
                                           'reversible' => false, 'transactional' => true, 'checksum' => 'checksum-' . $id );
    }

    public function id()
    {
        return $this->id;
    }

    public function source()
    {
        return 'site';
    }

    public function description()
    {
        return "fake $this->id";
    }

    public function environments()
    {
        return $this->options['environments'];
    }

    public function requires()
    {
        return $this->options['requires'];
    }

    public function maintenance()
    {
        return $this->options['maintenance'];
    }

    public function engines()
    {
        return $this->options['engines'];
    }

    public function checksum()
    {
        return $this->options['checksum'];
    }

    public function transactional()
    {
        return $this->options['transactional'];
    }

    public function state( $context )
    {
        if ( isset( $this->options['state'] ) )
            return call_user_func( $this->options['state'], $context );
        $record = $context->record( 'site', $this->id );
        return $record ? $record['state'] : expMigration::PENDING;
    }

    public function plan( $context )
    {
        return array( "plan of $this->id" );
    }

    public function up( $context )
    {
        return isset( $this->options['up'] ) ? call_user_func( $this->options['up'], $context ) : array();
    }

    public function reversible()
    {
        return $this->options['reversible'];
    }

    public function down( $context )
    {
        return isset( $this->options['down'] ) ? call_user_func( $this->options['down'], $context ) : array();
    }
}
