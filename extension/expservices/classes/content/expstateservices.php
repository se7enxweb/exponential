<?php
/**
 * Object state services: ezjscore/call/expstate::<method>[::arg...]
 *
 * State groups and states, the states of an object, the objects in a state, writes that create, change and remove
 * groups and states, order the states of a group, and assign states to an object or a subtree (subtrees route
 * through content jobs when asked or large).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expStateServices extends expContentServiceBase
{
    public static $services = array(
        'groups' => array( 'summary' => 'The state groups with their states', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of groups' ),
        'groupCount' => array( 'summary' => 'Number of state groups', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => '{count}' ),
        'group' => array( 'summary' => 'A state group by id or identifier, with its states', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array( 'group' => 'string' ), 'returns' => 'group' ),
        'states' => array( 'summary' => 'The states of a group in order', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array( 'group' => 'string' ), 'returns' => 'states' ),
        'state' => array( 'summary' => 'One state by id', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array( 'state_id' => 'int' ), 'returns' => 'state' ),
        'stateByIdentifier' => array( 'summary' => 'One state by group and identifier', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array( 'group' => 'string', 'identifier' => 'string' ), 'returns' => 'state' ),
        'translations' => array( 'summary' => 'The names and descriptions of a state in every language', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array( 'state_id' => 'int' ), 'returns' => 'language => {name, description}' ),
        'groupTranslations' => array( 'summary' => 'The names and descriptions of a group in every language', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array( 'group' => 'string' ), 'returns' => 'language => {name, description}' ),
        'defaultState' => array( 'summary' => 'The first (default) state of a group', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array( 'group' => 'string' ), 'returns' => 'state' ),
        'ofObject' => array( 'summary' => 'The states of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'states' ),
        'allowedForObject' => array( 'summary' => 'The states the current user may assign to an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'states' ),
        'objects' => array( 'summary' => 'Objects (main nodes) in a state, paged', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'state_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'objectCount' => array( 'summary' => 'Number of objects in a state', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array( 'state_id' => 'int' ), 'returns' => '{count}' ),
        'limitations' => array( 'summary' => 'The state limitations policies can use', 'access' => array( 'state', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'limitations' ),
        'createGroup' => array( 'summary' => 'Creates a state group. POST: identifier, name, description, language', 'access' => array( 'state', 'administrate' ), 'write' => true, 'args' => array(), 'returns' => 'group' ),
        'updateGroup' => array( 'summary' => 'Changes a state group. POST: identifier, name, description, language', 'access' => array( 'state', 'administrate' ), 'write' => true, 'args' => array( 'group' => 'string' ), 'returns' => 'group' ),
        'removeGroup' => array( 'summary' => 'Removes a state group with its states', 'access' => array( 'state', 'administrate' ), 'write' => true, 'args' => array( 'group' => 'string' ), 'returns' => '{removed}' ),
        'createState' => array( 'summary' => 'Creates a state in a group. POST: identifier, name, description, language', 'access' => array( 'state', 'administrate' ), 'write' => true, 'args' => array( 'group' => 'string' ), 'returns' => 'state' ),
        'updateState' => array( 'summary' => 'Changes a state. POST: identifier, name, description, language', 'access' => array( 'state', 'administrate' ), 'write' => true, 'args' => array( 'state_id' => 'int' ), 'returns' => 'state' ),
        'removeState' => array( 'summary' => 'Removes a state (its objects get the group default)', 'access' => array( 'state', 'administrate' ), 'write' => true, 'args' => array( 'state_id' => 'int' ), 'returns' => '{removed}' ),
        'reorderStates' => array( 'summary' => 'Orders the states of a group. POST: state_ids (the group\'s state ids in the new order)', 'access' => array( 'state', 'administrate' ), 'write' => true, 'args' => array( 'group' => 'string' ), 'returns' => 'states' ),
        'assign' => array( 'summary' => 'Assigns a state to an object', 'access' => array( 'state', 'assign' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'state_id' => 'int' ), 'returns' => 'states of the object' ),
        'assignSubtree' => array( 'summary' => 'Assigns a state to a node and its subtree. POST: mode', 'access' => array( 'state', 'assign' ), 'write' => true, 'args' => array( 'node_id' => 'int', 'state_id' => 'int' ), 'returns' => '{changed} or job' ),
    );

    protected static function groupOf( $idOrIdentifier, $editable = false )
    {
        $g = is_numeric( $idOrIdentifier ) ? eZContentObjectStateGroup::fetchById( (int)$idOrIdentifier ) : eZContentObjectStateGroup::fetchByIdentifier( (string)$idOrIdentifier );
        if ( !$g )
            throw new expServiceException( "State group $idOrIdentifier does not exist", 404 );
        if ( $editable && $g->isInternal() )
            throw new expServiceException( 'Internal state groups cannot be changed', 403 );
        return $g;
    }

    protected static function stateById( $id )
    {
        $s = eZContentObjectState::fetchById( (int)$id );
        if ( !$s )
            throw new expServiceException( "State $id does not exist", 404 );
        return $s;
    }

    protected static function translationRows( $item )
    {
        $out = array();
        foreach ( (array)$item->allTranslations() as $t )
        {
            if ( !$t->hasData() )
                continue;
            $l = eZContentLanguage::fetch( (int)$t->attribute( 'language_id' ) & ~1 );
            $out[$l ? $l->attribute( 'locale' ) : (string)$t->attribute( 'language_id' )] = array( 'name' => $t->attribute( 'name' ), 'description' => $t->attribute( 'description' ) );
        }
        return (object)$out;
    }

    /** Sets identifier, name and description from the POST fields; the translation is in POST language. */
    protected static function applyFields( $item, $isNew )
    {
        $lang = self::languageCode( self::post( 'language', 'string', '' ) );
        if ( ( $v = trim( self::post( 'identifier', 'string', '' ) ) ) !== '' )
            $item->setAttribute( 'identifier', strtolower( $v ) );
        else if ( $isNew )
            throw new expServiceException( 'The identifier is required', 400 );
        $t = $item->translationByLocale( $lang );
        if ( !$t )
            throw new expServiceException( "Language $lang is not available", 422 );
        if ( empty( $item->DefaultLanguageID ) )
            $item->setAttribute( 'default_language_id', (int)$t->realLanguageID() );
        if ( ( $v = trim( self::post( 'name', 'string', '' ) ) ) !== '' )
            $t->setAttribute( 'name', $v );
        else if ( $isNew )
            throw new expServiceException( 'The name is required', 400 );
        $v = self::post( 'description', 'string', null );
        if ( $v === null && $t->attribute( 'description' ) === null )
            $v = '';
        if ( $v !== null )
            $t->setAttribute( 'description', $v );
    }

    protected static function validated( $item )
    {
        $messages = array();
        if ( !$item->isValid( $messages ) )
            throw new expServiceException( implode( ' ', $messages ), 422 );
    }

    public static function groups( $args )
    {
        static::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $total = count( (array)eZContentObjectStateGroup::fetchByOffset( 1000, 0 ) );
        $items = array();
        foreach ( (array)eZContentObjectStateGroup::fetchByOffset( $limit, $offset ) as $g )
            $items[] = self::exportStateGroup( $g, true );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function groupCount( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => count( (array)eZContentObjectStateGroup::fetchByOffset( 1000, 0 ) ) ) );
    }

    public static function group( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportStateGroup( self::groupOf( self::arg( $args, 0, 'string' ) ), true ) );
    }

    public static function states( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)self::groupOf( self::arg( $args, 0, 'string' ) )->states() as $s )
            $out[] = self::exportState( $s );
        return self::ok( $out );
    }

    public static function state( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportState( self::stateById( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function stateByIdentifier( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::groupOf( self::arg( $args, 0, 'string' ) );
        $s = $g->stateByIdentifier( self::arg( $args, 1, 'string' ) );
        if ( !$s )
            throw new expServiceException( 'No such state in that group', 404 );
        return self::ok( self::exportState( $s ) );
    }

    public static function translations( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::translationRows( self::stateById( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function groupTranslations( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::translationRows( self::groupOf( self::arg( $args, 0, 'string' ) ) ) );
    }

    public static function defaultState( $args )
    {
        static::guard( __FUNCTION__ );
        $states = (array)self::groupOf( self::arg( $args, 0, 'string' ) )->states();
        if ( !$states )
            throw new expServiceException( 'The group has no states', 404 );
        return self::ok( self::exportState( reset( $states ) ) );
    }

    public static function ofObject( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)$o->stateIDArray( true ) as $stateId )
            if ( $s = eZContentObjectState::fetchById( (int)$stateId ) )
                $out[] = array_merge( self::exportState( $s ), array( 'group' => $s->group()->attribute( 'identifier' ) ) );
        return self::ok( $out );
    }

    public static function allowedForObject( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)$o->allowedAssignStateList() as $entry )
            foreach ( (array)$entry['states'] as $s )
                $out[] = array_merge( self::exportState( $s ), array( 'group' => $entry['group']->attribute( 'identifier' ) ) );
        return self::ok( $out );
    }

    public static function objects( $args )
    {
        static::guard( __FUNCTION__ );
        $s = self::stateById( self::arg( $args, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $params = array( 'MainNodeOnly' => true, 'AsObject' => true, 'AttributeFilter' => array( 'and', array( 'state', 'in', array( (int)$s->attribute( 'id' ) ) ) ),
                         'SortBy' => array( array( 'published', false ) ) );
        $count = eZContentObjectTreeNode::subTreeCountByNodeID( $params, 1 );
        $nodes = $count ? eZContentObjectTreeNode::subTreeByNodeID( $params + array( 'Limit' => $limit, 'Offset' => $offset ), 1 ) : array();
        return self::page( self::exportNodes( (array)$nodes ), $count, $offset, $limit );
    }

    public static function objectCount( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => (int)self::stateById( self::arg( $args, 0, 'int' ) )->objectCount() ) );
    }

    public static function limitations( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZContentObjectStateGroup::limitations() as $name => $l )
            $out[] = array( 'name' => $name, 'group_id' => isset( $l['parameter'][0] ) ? (int)$l['parameter'][0] : null );
        return self::ok( $out );
    }

    public static function createGroup( $args )
    {
        static::guard( __FUNCTION__ );
        $g = new eZContentObjectStateGroup();
        self::applyFields( $g, true );
        self::validated( $g );
        $g->store();
        eZContentObjectState::cleanDefaultsCache();
        ezpEvent::getInstance()->notify( 'content/state/group/cache', array( $g->attribute( 'id' ) ) );
        return self::ok( self::exportStateGroup( eZContentObjectStateGroup::fetchById( $g->attribute( 'id' ) ), true ) );
    }

    public static function updateGroup( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::groupOf( self::arg( $args, 0, 'string' ), true );
        self::applyFields( $g, false );
        self::validated( $g );
        $g->store();
        eZContentObjectState::cleanDefaultsCache();
        ezpEvent::getInstance()->notify( 'content/state/group/cache', array( $g->attribute( 'id' ) ) );
        return self::ok( self::exportStateGroup( eZContentObjectStateGroup::fetchById( $g->attribute( 'id' ) ), true ) );
    }

    public static function removeGroup( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::groupOf( self::arg( $args, 0, 'string' ), true );
        $id = (int)$g->attribute( 'id' );
        eZContentObjectStateGroup::removeByID( $id );
        eZContentObjectState::cleanDefaultsCache();
        ezpEvent::getInstance()->notify( 'content/state/group/cache', array( $id ) );
        return self::ok( array( 'removed' => $id ) );
    }

    public static function createState( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::groupOf( self::arg( $args, 0, 'string' ), true );
        $s = $g->newState();
        self::applyFields( $s, true );
        self::validated( $s );
        $s->store();
        eZContentObjectState::cleanDefaultsCache();
        ezpEvent::getInstance()->notify( 'content/state/cache', array( $s->attribute( 'id' ) ) );
        return self::ok( self::exportState( eZContentObjectState::fetchById( $s->attribute( 'id' ) ) ) );
    }

    public static function updateState( $args )
    {
        static::guard( __FUNCTION__ );
        $s = self::stateById( self::arg( $args, 0, 'int' ) );
        if ( $s->group()->isInternal() )
            throw new expServiceException( 'States of internal groups cannot be changed', 403 );
        self::applyFields( $s, false );
        self::validated( $s );
        $s->store();
        eZContentObjectState::cleanDefaultsCache();
        ezpEvent::getInstance()->notify( 'content/state/cache', array( $s->attribute( 'id' ) ) );
        return self::ok( self::exportState( eZContentObjectState::fetchById( $s->attribute( 'id' ) ) ) );
    }

    public static function removeState( $args )
    {
        static::guard( __FUNCTION__ );
        $s = self::stateById( self::arg( $args, 0, 'int' ) );
        $g = $s->group();
        if ( $g->isInternal() )
            throw new expServiceException( 'States of internal groups cannot be removed', 403 );
        $id = (int)$s->attribute( 'id' );
        $g->removeStatesByID( array( $id ) );
        eZContentObjectState::cleanDefaultsCache();
        ezpEvent::getInstance()->notify( 'content/state/cache', array( $id ) );
        return self::ok( array( 'removed' => $id ) );
    }

    public static function reorderStates( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::groupOf( self::arg( $args, 0, 'string' ), true );
        $ids = array_map( 'intval', self::post( 'state_ids', 'list' ) );
        $have = array();
        foreach ( (array)$g->states() as $s )
            $have[] = (int)$s->attribute( 'id' );
        $a = $ids;
        $b = $have;
        sort( $a );
        sort( $b );
        if ( $a !== $b )
            throw new expServiceException( 'state_ids must list every state of the group exactly once', 422 );
        $g->reorderStates( $ids );
        eZContentObjectState::cleanDefaultsCache();
        $out = array();
        foreach ( (array)$g->states( true ) as $s )
            $out[] = self::exportState( $s );
        return self::ok( $out );
    }

    public static function assign( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $s = self::stateById( self::arg( $args, 1, 'int' ) );
        $sid = (int)$s->attribute( 'id' );
        if ( !in_array( $sid, array_map( 'intval', (array)$o->attribute( 'allowed_assign_state_id_list' ) ), true ) )
            throw new expServiceException( 'You cannot assign this state to this object', 403 );
        $oid = (int)$o->attribute( 'id' );
        $o->stateIDArray( true );
        self::operation( 'updateobjectstate', array( 'object_id' => $oid, 'state_id_list' => array( $sid ) ),
                         function () use ( $oid, $sid ) { return eZContentOperationCollection::updateObjectState( $oid, array( $sid ) ); } );
        eZContentObject::clearCache();
        $out = array();
        foreach ( (array)eZContentObject::fetch( $oid )->stateIDArray( true ) as $id )
            if ( $x = eZContentObjectState::fetchById( (int)$id ) )
                $out[] = self::exportState( $x );
        return self::ok( $out );
    }

    public static function assignSubtree( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $s = self::stateById( self::arg( $args, 1, 'int' ) );
        $sid = (int)$s->attribute( 'id' );
        if ( !in_array( $sid, array_map( 'intval', (array)$node->object()->attribute( 'allowed_assign_state_id_list' ) ), true ) )
            throw new expServiceException( 'You cannot assign this state here', 403 );
        $nid = (int)$node->attribute( 'node_id' );
        return self::runOrJob( 'state', array( 'node_id' => $nid, 'state_id' => $sid ), function () use ( $nid, $sid ) {
            self::notLocked( array( $nid ) );
            $changed = 0;
            $list = array( eZContentObjectTreeNode::fetch( $nid ) );
            foreach ( (array)eZContentObjectTreeNode::fetch( $nid )->subTree( array( 'AsObject' => true, 'MainNodeOnly' => true, 'IgnoreVisibility' => true ) ) as $n )
                $list[] = $n;
            foreach ( $list as $n )
            {
                $o = $n->object();
                if ( !$o || !in_array( $sid, array_map( 'intval', (array)$o->attribute( 'allowed_assign_state_id_list' ) ), true ) )
                    continue;
                $oid = (int)$o->attribute( 'id' );
                $o->stateIDArray( true );
                self::operation( 'updateobjectstate', array( 'object_id' => $oid, 'state_id_list' => array( $sid ) ),
                                 function () use ( $oid, $sid ) { return eZContentOperationCollection::updateObjectState( $oid, array( $sid ) ); } );
                $changed++;
            }
            return array( 'changed' => $changed, 'state_id' => $sid );
        } );
    }
}
