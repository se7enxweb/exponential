<?php
/**
 * exprad: the extension point survey (setup/rad), summary level, read-only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expRadServices extends expServiceBase
{
    public static $services = array(
        'summary' => array( 'summary' => 'The survey counts: ini files, settings, modules, operators, runnables ...', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'counts' ),
        'counts' => array( 'summary' => 'One count of the survey by name', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'name' => 'string' ), 'returns' => 'name, value' ),
        'groups' => array( 'summary' => 'The extension point groups with their counts', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'list of groups' ),
        'runnables' => array( 'summary' => 'The runnables (commands, cronjobs, views) with kind and owner', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int', 'kind' => 'string' ), 'returns' => 'paged list of runnables' ),
        'files' => array( 'summary' => 'The ini files the survey reads', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of ini, origin' ),
        'inicommand' => array( 'summary' => 'The exp:ini actions and scope providers', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'actions, providers' ),
        'contentjobtypes' => array( 'summary' => 'The content job types', 'access' => array( 'setup', 'system_info' ), 'write' => false, 'args' => array(), 'returns' => 'list of content job types' ),
    );

    public static function summary( $args )
    {
        self::guard( __FUNCTION__ );
        $survey = expRADSurvey::survey();
        return self::ok( $survey['counts'], array( 'total' => count( $survey['counts'] ) ) );
    }

    public static function counts( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::arg( $args, 0, 'string' );
        $survey = expRADSurvey::survey();
        if ( !isset( $survey['counts'][$name] ) )
            throw new expServiceException( "No count '$name'", 404 );
        return self::ok( array( 'name' => $name, 'value' => $survey['counts'][$name] ) );
    }

    public static function groups( $args )
    {
        self::guard( __FUNCTION__ );
        $counts = expRADSurvey::groupCounts();
        $list = array();
        foreach ( (array)$counts as $key => $value )
            $list[] = array( 'group' => $key, 'count' => is_array( $value ) ? ( isset( $value['count'] ) ? $value['count'] : count( $value ) ) : $value );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function runnables( $args )
    {
        self::guard( __FUNCTION__ );
        $list = expRADSurvey::runnables();
        $items = $list['list'];
        $kind = self::arg( $args, 2, 'string', null );
        if ( $kind !== null )
            $items = expRADSurvey::runnablesOf( $items, 'kind', $kind );
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function files( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( expRADSurvey::iniFiles() as $f )
            $list[] = array( 'ini' => $f['ini'], 'origin' => $f['origin'] );
        return self::pageOf( $list, $args, 0, 1 );
    }

    public static function inicommand( $args )
    {
        self::guard( __FUNCTION__ );
        $c = expRADSurvey::iniCommand();
        return self::ok( array( 'actions' => array_values( $c['actions'] ), 'providers' => array_values( $c['providers'] ) ) );
    }

    public static function contentjobtypes( $args )
    {
        self::guard( __FUNCTION__ );
        $c = expRADSurvey::contentJobTypes();
        return self::ok( array_values( $c['types'] ), array( 'total' => count( $c['types'] ) ) );
    }
}
