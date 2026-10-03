<?php
/**
 * expcronjob: the cronjob parts and scripts (setup/managecronjobs), read-only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expCronjobServices extends expServiceBase
{
    public static $services = array(
        'parts' => array( 'summary' => 'The cronjob parts (groups run by runcronjobs.php <part>) with their script count',
            'access' => array( 'setup', 'managecronjobs' ), 'write' => false, 'args' => array(), 'returns' => 'list of part, scripts' ),
        'scripts' => array( 'summary' => 'The scripts of one cronjob part',
            'access' => array( 'setup', 'managecronjobs' ), 'write' => false, 'args' => array( 'part' => 'string' ), 'returns' => 'part, scripts, found (script file exists)' ),
        'all' => array( 'summary' => 'Every part with its scripts',
            'access' => array( 'setup', 'managecronjobs' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of part, scripts' ),
        'settings' => array( 'summary' => 'The cronjob settings: script directories, execution time limit, admin console settings',
            'access' => array( 'setup', 'managecronjobs' ), 'write' => false, 'args' => array(), 'returns' => 'directories, max_script_execution_time' ),
        'status' => array( 'summary' => 'The cronjob log files: whether they exist, their size and last change',
            'access' => array( 'setup', 'managecronjobs' ), 'write' => false, 'args' => array(), 'returns' => 'list of log, exists, bytes, modified' ),
        'deferred' => array( 'summary' => 'The workflow processes deferred to cron: how many wait',
            'access' => array( 'setup', 'managecronjobs' ), 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'runnables' => array( 'summary' => 'The cronjobs implemented as runnables (kernel and extension)',
            'access' => array( 'setup', 'managecronjobs' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of runnables of kind cronjob' ),
    );

    protected static function ini()
    {
        return eZINI::instance( 'cronjob.ini' );
    }

    /** @return array part => list of scripts */
    public static function partMap()
    {
        $ini = self::ini();
        $map = array();
        foreach ( $ini->groups() as $group => $values )
            if ( strpos( $group, 'CronjobPart-' ) === 0 )
                $map[substr( $group, strlen( 'CronjobPart-' ) )] = isset( $values['Scripts'] ) ? array_values( (array)$values['Scripts'] ) : array();
        ksort( $map );
        return $map;
    }

    public static function parts( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( self::partMap() as $part => $scripts )
            $list[] = array( 'part' => $part, 'scripts' => count( $scripts ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function scripts( $args )
    {
        self::guard( __FUNCTION__ );
        $part = self::arg( $args, 0, 'string' );
        $map = self::partMap();
        if ( !isset( $map[$part] ) )
            throw new expServiceException( "No cronjob part '$part'", 404 );
        $dirs = (array)self::ini()->variable( 'CronjobSettings', 'ScriptDirectories' );
        $scripts = array();
        foreach ( $map[$part] as $name )
        {
            $found = false;
            foreach ( $dirs as $dir )
                if ( is_file( $dir . '/' . $name ) )
                    $found = true;
            $scripts[] = array( 'script' => $name, 'found' => $found );
        }
        return self::ok( array( 'part' => $part, 'scripts' => $scripts ) );
    }

    public static function all( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( self::partMap() as $part => $scripts )
            $list[] = array( 'part' => $part, 'scripts' => $scripts );
        return self::pageOf( $list, $args, 0, 1 );
    }

    public static function settings( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = self::ini();
        return self::ok( array(
            'directories' => array_values( (array)$ini->variable( 'CronjobSettings', 'ScriptDirectories' ) ),
            'max_script_execution_time' => (int)$ini->variable( 'CronjobSettings', 'MaxScriptExecutionTime' ),
            'parts' => count( self::partMap() ) ) );
    }

    public static function status( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = self::ini();
        $list = array();
        foreach ( array( 'LogFile', 'ErrorFile' ) as $key )
        {
            if ( !$ini->hasVariable( 'AdminSettings', $key ) )
                continue;
            $path = eZSys::varDirectory() . '/log/' . $ini->variable( 'AdminSettings', $key );
            $list[] = array( 'log' => $ini->variable( 'AdminSettings', $key ), 'exists' => is_file( $path ),
                             'bytes' => is_file( $path ) ? filesize( $path ) : 0, 'modified' => is_file( $path ) ? self::iso( filemtime( $path ) ) : null );
        }
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function deferred( $args )
    {
        self::guard( __FUNCTION__ );
        $list = eZWorkflowProcess::fetchForStatus( eZWorkflow::STATUS_DEFERRED_TO_CRON );
        return self::ok( array( 'count' => is_array( $list ) ? count( $list ) : 0 ) );
    }

    public static function runnables( $args )
    {
        self::guard( __FUNCTION__ );
        $survey = expRADSurvey::runnables();
        $list = expRADSurvey::runnablesOf( $survey['list'], 'kind', 'cronjob' );
        return self::pageOf( $list, $args, 0, 1 );
    }
}
