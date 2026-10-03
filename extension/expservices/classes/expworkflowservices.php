<?php
/**
 * expworkflow: workflows, their events, triggers and processes, read-only (setup/administrate).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expWorkflowServices extends expServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The workflows (published versions)', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of id, name, enabled, type' ),
        'view' => array( 'summary' => 'One workflow with its events', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'workflow with events' ),
        'events' => array( 'summary' => 'The events of a workflow in order', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'list of event id, type, description, placement' ),
        'eventtypes' => array( 'summary' => 'The registered workflow event types', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'list of type string, group, name' ),
        'groups' => array( 'summary' => 'The workflow groups', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'list of id, name' ),
        'triggers' => array( 'summary' => 'The triggers: module, function, connect type and the workflow', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int', 'module' => 'string' ), 'returns' => 'paged list of triggers' ),
        'trigger' => array( 'summary' => 'One trigger', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'trigger' ),
        'processes' => array( 'summary' => 'The running workflow processes', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of processes' ),
        'process' => array( 'summary' => 'One workflow process', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'process' ),
        'statuses' => array( 'summary' => 'The workflow status codes and names', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'list of status, name' ),
    );

    public static function exportWorkflow( eZWorkflow $w )
    {
        return array( 'id' => (int)$w->attribute( 'id' ), 'name' => $w->attribute( 'name' ), 'enabled' => (bool)$w->attribute( 'is_enabled' ),
                      'type' => $w->attribute( 'workflow_type_string' ), 'version' => (int)$w->attribute( 'version' ),
                      'modified' => self::iso( $w->attribute( 'modified' ) ) );
    }

    public static function exportEvent( eZWorkflowEvent $e )
    {
        return array( 'id' => (int)$e->attribute( 'id' ), 'type' => $e->attribute( 'workflow_type_string' ),
                      'description' => $e->attribute( 'description' ), 'placement' => (int)$e->attribute( 'placement' ) );
    }

    protected static function exportTrigger( eZTrigger $t )
    {
        return array( 'id' => (int)$t->attribute( 'id' ), 'name' => $t->attribute( 'name' ), 'module' => $t->attribute( 'module_name' ),
                      'function' => $t->attribute( 'function_name' ), 'connect_type' => $t->attribute( 'connect_type' ), 'workflow_id' => (int)$t->attribute( 'workflow_id' ) );
    }

    protected static function exportProcess( eZWorkflowProcess $p )
    {
        return array( 'id' => (int)$p->attribute( 'id' ), 'process_key' => $p->attribute( 'process_key' ), 'workflow_id' => (int)$p->attribute( 'workflow_id' ),
                      'user_id' => (int)$p->attribute( 'user_id' ), 'status' => (int)$p->attribute( 'status' ),
                      'status_name' => eZWorkflow::statusName( $p->attribute( 'status' ) ), 'created' => self::iso( $p->attribute( 'created' ) ),
                      'modified' => self::iso( $p->attribute( 'modified' ) ) );
    }

    protected static function workflow( $id )
    {
        $w = eZWorkflow::fetch( (int)$id );
        if ( !$w instanceof eZWorkflow )
            throw new expServiceException( "No workflow $id", 404 );
        return $w;
    }

    public static function list( $args )
    {
        self::guard( __FUNCTION__ );
        $items = array();
        foreach ( (array)eZPersistentObject::fetchObjectList( eZWorkflow::definition(), null, array( 'version' => 0 ), array( 'name' => 'asc' ) ) as $w )
            $items[] = self::exportWorkflow( $w );
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function view( $args )
    {
        self::guard( __FUNCTION__ );
        $w = self::workflow( self::arg( $args, 0, 'int' ) );
        $events = array();
        foreach ( (array)eZWorkflow::fetchEventsByWorkflowID( $w->attribute( 'id' ) ) as $e )
            $events[] = self::exportEvent( $e );
        return self::ok( self::exportWorkflow( $w ) + array( 'events' => $events ) );
    }

    public static function events( $args )
    {
        self::guard( __FUNCTION__ );
        $w = self::workflow( self::arg( $args, 0, 'int' ) );
        $events = array();
        foreach ( (array)eZWorkflow::fetchEventsByWorkflowID( $w->attribute( 'id' ) ) as $e )
            $events[] = self::exportEvent( $e );
        return self::ok( $events, array( 'total' => count( $events ) ) );
    }

    public static function eventtypes( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( (array)eZWorkflowType::fetchRegisteredTypes() as $string => $type )
            $list[] = array( 'type' => $string, 'name' => is_object( $type ) && method_exists( $type, 'getName' ) ? $type->getName() : $string );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function groups( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( (array)eZWorkflowGroup::fetchList() as $g )
            $list[] = array( 'id' => (int)$g->attribute( 'id' ), 'name' => $g->attribute( 'name' ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function triggers( $args )
    {
        self::guard( __FUNCTION__ );
        $module = self::arg( $args, 2, 'string', '*' );
        $items = array();
        foreach ( (array)eZTrigger::fetchList( array( 'module' => $module ) ) as $t )
            $items[] = self::exportTrigger( $t );
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function trigger( $args )
    {
        self::guard( __FUNCTION__ );
        $t = eZTrigger::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$t instanceof eZTrigger )
            throw new expServiceException( 'No such trigger', 404 );
        return self::ok( self::exportTrigger( $t ) );
    }

    public static function processes( $args )
    {
        self::guard( __FUNCTION__ );
        $items = array();
        foreach ( (array)eZWorkflowProcess::fetchList() as $p )
            $items[] = self::exportProcess( $p );
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function process( $args )
    {
        self::guard( __FUNCTION__ );
        $p = eZWorkflowProcess::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$p instanceof eZWorkflowProcess )
            throw new expServiceException( 'No such workflow process', 404 );
        return self::ok( self::exportProcess( $p ) );
    }

    public static function statuses( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( eZWorkflow::statusNameMap() as $status => $name )
            $list[] = array( 'status' => (int)$status, 'name' => $name );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }
}
