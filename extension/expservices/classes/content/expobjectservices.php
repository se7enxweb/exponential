<?php
/**
 * Object services: ezjscore/call/expobject::<method>[::arg...]
 *
 * Content objects by id and remote id: reads (data map, names, owner, class, section, states, nodes, languages,
 * rights, lists by class and owner) and writes (create, update with a new published version, rename, remote id,
 * owner, languages, copy, remove, cache).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expObjectServices extends expContentServiceBase
{
    public static $services = array(
        'get' => array( 'summary' => 'An object with its languages and node ids', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'object' ),
        'getByRemoteId' => array( 'summary' => 'An object by its remote id', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'remote_id' => 'string' ), 'returns' => 'object' ),
        'exists' => array( 'summary' => 'Whether an object exists and is readable', 'access' => 'user', 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{exists}' ),
        'list' => array( 'summary' => 'Objects (main nodes) paged, sorted and filtered', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'sort' => 'string', 'order' => 'string', 'limit' => 'int', 'offset' => 'int', 'filter' => 'json' ), 'returns' => 'page of nodes' ),
        'count' => array( 'summary' => 'Number of objects matching a filter', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'filter' => 'json' ), 'returns' => '{count}' ),
        'byClass' => array( 'summary' => 'Objects of a class', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'class' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'byOwner' => array( 'summary' => 'Objects owned by an object (user) id', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'owner_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'recent' => array( 'summary' => 'Most recently published objects', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'limit' => 'int', 'classes' => 'list' ), 'returns' => 'nodes' ),
        'recentlyModified' => array( 'summary' => 'Most recently modified objects', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'limit' => 'int', 'classes' => 'list' ), 'returns' => 'nodes' ),
        'dataMap' => array( 'summary' => 'The attributes of an object version with their values', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int', 'language' => 'string' ), 'returns' => 'identifier => attribute' ),
        'attributes' => array( 'summary' => 'The attributes of an object without values', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'language' => 'string' ), 'returns' => 'attributes' ),
        'attribute' => array( 'summary' => 'One attribute of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'identifier' => 'string', 'language' => 'string' ), 'returns' => 'attribute' ),
        'name' => array( 'summary' => 'The name of an object in a language', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'language' => 'string' ), 'returns' => '{name}' ),
        'names' => array( 'summary' => 'The name of an object in every language', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'language => name' ),
        'owner' => array( 'summary' => 'The owner object of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'object' ),
        'class' => array( 'summary' => 'The class of an object with its attributes', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'class' ),
        'section' => array( 'summary' => 'The section of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'section' ),
        'states' => array( 'summary' => 'The states of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'states' ),
        'nodes' => array( 'summary' => 'All nodes (locations) of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'nodes' ),
        'mainNode' => array( 'summary' => 'The main node of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'node' ),
        'languages' => array( 'summary' => 'Languages the object exists in, initial and always available', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{languages, initial, always_available}' ),
        'versionCount' => array( 'summary' => 'Number of versions', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{count}' ),
        'rights' => array( 'summary' => 'What the current user can do with the object', 'access' => 'user', 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'right => bool' ),
        'url' => array( 'summary' => 'URL aliases of the main node of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{url_alias, system_url}' ),
        'summary' => array( 'summary' => 'Light card of an object: id, name, class, main node, modified', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'card' ),
        'create' => array( 'summary' => 'Creates and publishes an object. POST: parent_node_id, class, attributes (json), language, remote_id', 'access' => array( 'content', 'create' ), 'write' => true, 'args' => array(), 'returns' => 'object' ),
        'update' => array( 'summary' => 'Changes attributes and publishes a new version. POST: attributes (json), language', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => 'object' ),
        'rename' => array( 'summary' => 'Renames an object. POST: name', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => 'object' ),
        'setRemoteId' => array( 'summary' => 'Sets the remote id of an object', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'remote_id' => 'string' ), 'returns' => 'object' ),
        'setOwner' => array( 'summary' => 'Changes the owner of an object (needs unrestricted edit access)', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'owner_id' => 'int' ), 'returns' => 'object' ),
        'setInitialLanguage' => array( 'summary' => 'Changes the initial language of an object', 'access' => array( 'content', 'translate' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'language' => 'string' ), 'returns' => 'object' ),
        'setAlwaysAvailable' => array( 'summary' => 'Sets whether the object is shown in languages it has no translation for', 'access' => array( 'content', 'translate' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'value' => 'bool' ), 'returns' => 'object' ),
        'copy' => array( 'summary' => 'Copies the object of a main node below a node', 'access' => array( 'content', 'create' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'new_parent_node_id' => 'int' ), 'returns' => 'new node' ),
        'remove' => array( 'summary' => 'Removes an object with all its nodes. POST: move_to_trash (default 1), mode', 'access' => array( 'content', 'remove' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => '{removed}' ),
        'expireCache' => array( 'summary' => 'Clears the view caches of an object', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => '{cleared}' ),
        'cleanupDrafts' => array( 'summary' => 'Removes the current user\'s internal drafts of an object', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => '{cleaned}' ),
    );

    public static function get( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportObject( self::object( self::arg( $args, 0, 'int' ) ), true ) );
    }

    public static function getByRemoteId( $args )
    {
        static::guard( __FUNCTION__ );
        $o = eZContentObject::fetchByRemoteID( self::arg( $args, 0, 'string' ) );
        if ( !$o )
            throw new expServiceException( 'No object with that remote id', 404 );
        return self::ok( self::exportObject( self::object( $o->attribute( 'id' ) ), true ) );
    }

    public static function exists( $args )
    {
        static::guard( __FUNCTION__ );
        $o = eZContentObject::fetch( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'exists' => $o instanceof eZContentObject && $o->canRead() ) );
    }

    /** The object list parameters: main nodes of the whole tree, policies applied. */
    protected static function listParams( array $args, $first )
    {
        $sort = self::arg( $args, $first, 'string', 'published' );
        $order = self::arg( $args, $first + 1, 'string', 'desc' );
        list( $limit, $offset ) = self::paging( $args, $first + 2, $first + 3 );
        $filter = self::arg( $args, $first + 4, 'json', array() );
        $params = array_merge( array( 'MainNodeOnly' => true, 'AsObject' => true, 'Limit' => $limit, 'Offset' => $offset,
                                      'SortBy' => self::sortBy( $sort, $order ) ), self::filterParams( $filter ) );
        return array( $params, $limit, $offset );
    }

    public static function list( $args )
    {
        static::guard( __FUNCTION__ );
        list( $params, $limit, $offset ) = self::listParams( $args, 0 );
        $count = eZContentObjectTreeNode::subTreeCountByNodeID( array_diff_key( $params, array( 'Limit' => 1, 'Offset' => 1, 'SortBy' => 1 ) ), 1 );
        $nodes = $count ? eZContentObjectTreeNode::subTreeByNodeID( $params, 1 ) : array();
        return self::page( self::exportNodes( (array)$nodes ), $count, $offset, $limit );
    }

    public static function count( $args )
    {
        static::guard( __FUNCTION__ );
        $params = array_merge( array( 'MainNodeOnly' => true ), self::filterParams( self::arg( $args, 0, 'json', array() ) ) );
        return self::ok( array( 'count' => (int)eZContentObjectTreeNode::subTreeCountByNodeID( $params, 1 ) ) );
    }

    public static function byClass( $args )
    {
        static::guard( __FUNCTION__ );
        $class = self::contentClass( self::arg( $args, 0, 'string' ) );
        list( $params, $limit, $offset ) = self::listParams( array( 'published', 'desc', isset( $args[1] ) ? $args[1] : null, isset( $args[2] ) ? $args[2] : null,
                                                                    array( 'class' => array( $class->attribute( 'identifier' ) ) ) ), 0 );
        $count = eZContentObjectTreeNode::subTreeCountByNodeID( array_diff_key( $params, array( 'Limit' => 1, 'Offset' => 1, 'SortBy' => 1 ) ), 1 );
        $nodes = $count ? eZContentObjectTreeNode::subTreeByNodeID( $params, 1 ) : array();
        return self::page( self::exportNodes( (array)$nodes ), $count, $offset, $limit );
    }

    public static function byOwner( $args )
    {
        static::guard( __FUNCTION__ );
        $owner = self::arg( $args, 0, 'int' );
        list( $params, $limit, $offset ) = self::listParams( array( 'published', 'desc', isset( $args[1] ) ? $args[1] : null, isset( $args[2] ) ? $args[2] : null,
                                                                    array( 'owner' => $owner ) ), 0 );
        $count = eZContentObjectTreeNode::subTreeCountByNodeID( array_diff_key( $params, array( 'Limit' => 1, 'Offset' => 1, 'SortBy' => 1 ) ), 1 );
        $nodes = $count ? eZContentObjectTreeNode::subTreeByNodeID( $params, 1 ) : array();
        return self::page( self::exportNodes( (array)$nodes ), $count, $offset, $limit );
    }

    protected static function recentBy( $args, $sort )
    {
        $limit = min( 100, max( 1, self::arg( $args, 0, 'int', 10 ) ) );
        $params = array( 'MainNodeOnly' => true, 'AsObject' => true, 'Limit' => $limit, 'SortBy' => array( array( $sort, false ) ) );
        $classes = self::arg( $args, 1, 'list', array() );
        if ( $classes )
        {
            $params['ClassFilterType'] = 'include';
            $params['ClassFilterArray'] = $classes;
        }
        return self::ok( self::exportNodes( (array)eZContentObjectTreeNode::subTreeByNodeID( $params, 1 ) ) );
    }

    public static function recent( $args )
    {
        static::guard( __FUNCTION__ );
        return self::recentBy( $args, 'published' );
    }

    public static function recentlyModified( $args )
    {
        static::guard( __FUNCTION__ );
        return self::recentBy( $args, 'modified' );
    }

    public static function dataMap( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $v = self::arg( $args, 1, 'int', 0 );
        if ( $v && $v !== (int)$o->attribute( 'current_version' ) )
            self::version( $o, $v );
        return self::ok( self::exportDataMap( $o, $v, self::arg( $args, 2, 'string', false ) ) );
    }

    public static function attributes( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        return self::ok( self::exportDataMap( $o, false, self::arg( $args, 1, 'string', false ), false ) );
    }

    public static function attribute( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $id = self::arg( $args, 1, 'string' );
        $map = $o->fetchDataMap( false, self::arg( $args, 2, 'string', false ) ?: false );
        if ( !isset( $map[$id] ) )
            throw new expServiceException( "The object has no attribute '$id'", 404 );
        return self::ok( self::exportAttribute( $map[$id] ) );
    }

    public static function name( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $lang = self::arg( $args, 1, 'string', false );
        return self::ok( array( 'name' => $o->name( false, $lang ?: false ) ) );
    }

    public static function names( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( (object)self::object( self::arg( $args, 0, 'int' ) )->names() );
    }

    public static function owner( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $owner = $o->owner();
        if ( !$owner )
            throw new expServiceException( 'The object has no owner', 404 );
        return self::ok( self::exportObject( $owner ) );
    }

    public static function class( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportClass( self::object( self::arg( $args, 0, 'int' ) )->contentClass(), true ) );
    }

    public static function section( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $s = eZSection::fetch( (int)$o->attribute( 'section_id' ) );
        if ( !$s )
            throw new expServiceException( 'The section no longer exists', 404 );
        return self::ok( self::exportSection( $s ) );
    }

    public static function states( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)$o->stateIDArray() as $groupId => $stateId )
        {
            $state = eZContentObjectState::fetchById( (int)$stateId );
            if ( $state )
                $out[] = self::exportState( $state );
        }
        return self::ok( $out );
    }

    public static function nodes( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportNodes( (array)self::object( self::arg( $args, 0, 'int' ) )->assignedNodes() ) );
    }

    public static function mainNode( $args )
    {
        static::guard( __FUNCTION__ );
        $n = self::object( self::arg( $args, 0, 'int' ) )->attribute( 'main_node' );
        if ( !$n )
            throw new expServiceException( 'The object has no node', 404 );
        return self::ok( self::exportNode( $n ) );
    }

    public static function languages( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'languages' => array_values( (array)$o->availableLanguages() ), 'initial' => $o->attribute( 'initial_language_code' ),
                                'always_available' => (bool)$o->attribute( 'always_available' ) ) );
    }

    public static function versionCount( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => (int)self::object( self::arg( $args, 0, 'int' ) )->getVersionCount() ) );
    }

    public static function rights( $args )
    {
        static::guard( __FUNCTION__ );
        $o = eZContentObject::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$o )
            throw new expServiceException( 'Object does not exist', 404 );
        $out = array();
        foreach ( array( 'read', 'edit', 'create', 'remove', 'translate', 'diff', 'pdf', 'moveFrom' ) as $r )
            $out[$r] = (bool)$o->{'can' . ucfirst( $r )}();
        return self::ok( $out );
    }

    public static function url( $args )
    {
        static::guard( __FUNCTION__ );
        $n = self::object( self::arg( $args, 0, 'int' ) )->attribute( 'main_node' );
        if ( !$n )
            throw new expServiceException( 'The object has no node', 404 );
        return self::ok( array( 'url_alias' => $n->urlAlias(), 'system_url' => 'content/view/full/' . (int)$n->attribute( 'node_id' ) ) );
    }

    public static function summary( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'id' => (int)$o->attribute( 'id' ), 'name' => $o->attribute( 'name' ), 'class' => $o->attribute( 'class_identifier' ),
                                'main_node_id' => (int)$o->attribute( 'main_node_id' ), 'modified' => self::iso( $o->attribute( 'modified' ) ) ) );
    }

    // ------------------------------------------------------------------ writes

    public static function create( $args )
    {
        static::guard( __FUNCTION__ );
        $parent = self::post( 'parent_node_id', 'int' );
        $r = expNodeServices::create( array( $parent ) );
        $node = eZContentObjectTreeNode::fetch( $r['data']['node_id'] );
        return self::ok( self::exportObject( $node->object(), true ) );
    }

    public static function update( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $o = self::updateObject( $o, (array)self::post( 'attributes', 'json' ), self::post( 'language', 'string', false ) );
        return self::ok( self::exportObject( $o, true ) );
    }

    public static function rename( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $name = trim( self::post( 'name', 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The name cannot be empty', 422 );
        $o->rename( $name );
        return self::ok( self::exportObject( eZContentObject::fetch( $o->attribute( 'id' ) ) ) );
    }

    public static function setRemoteId( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $remote = trim( self::arg( $args, 1, 'string' ) );
        if ( !preg_match( '/^[A-Za-z0-9_.:-]{1,100}$/', $remote ) )
            throw new expServiceException( 'The remote id may have letters, digits and _ . : - (up to 100)', 422 );
        $other = eZContentObject::fetchByRemoteID( $remote );
        if ( $other && (int)$other->attribute( 'id' ) !== (int)$o->attribute( 'id' ) )
            throw new expServiceException( 'That remote id is in use', 409 );
        $o->setAttribute( 'remote_id', $remote );
        $o->store();
        return self::ok( self::exportObject( eZContentObject::fetch( $o->attribute( 'id' ) ) ) );
    }

    public static function setOwner( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $access = eZUser::currentUser()->hasAccessTo( 'content', 'edit' );
        if ( $access['accessWord'] !== 'yes' )
            throw new expServiceException( 'Changing the owner needs unrestricted edit access', 403 );
        $owner = eZContentObject::fetch( self::arg( $args, 1, 'int' ) );
        if ( !$owner )
            throw new expServiceException( 'The new owner does not exist', 404 );
        $o->setAttribute( 'owner_id', (int)$owner->attribute( 'id' ) );
        $o->store();
        return self::ok( self::exportObject( eZContentObject::fetch( $o->attribute( 'id' ) ) ) );
    }

    public static function setInitialLanguage( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'translate' );
        $code = self::languageCode( self::arg( $args, 1, 'string' ), $o );
        if ( !in_array( $code, (array)$o->availableLanguages(), true ) )
            throw new expServiceException( 'The object has no translation in ' . $code, 422 );
        $id = (int)$o->attribute( 'id' );
        $langId = (int)eZContentLanguage::idByLocale( $code );
        self::operation( 'updateinitiallanguage', array( 'object_id' => $id, 'new_initial_language_id' => $langId ),
                         function () use ( $id, $langId ) { return eZContentOperationCollection::updateInitialLanguage( $id, $langId ); } );
        return self::ok( self::exportObject( eZContentObject::fetch( $id ), true ) );
    }

    public static function setAlwaysAvailable( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'translate' );
        $id = (int)$o->attribute( 'id' );
        $value = self::arg( $args, 1, 'bool' ) ? 1 : 0;
        self::operation( 'updatealwaysavailable', array( 'object_id' => $id, 'new_always_available' => $value ),
                         function () use ( $id, $value ) { return eZContentOperationCollection::updateAlwaysAvailable( $id, $value ); } );
        return self::ok( self::exportObject( eZContentObject::fetch( $id ), true ) );
    }

    public static function copy( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $node = $o->attribute( 'main_node' );
        if ( !$node )
            throw new expServiceException( 'The object has no node to copy', 404 );
        return expNodeServices::copy( array( $node->attribute( 'node_id' ), self::arg( $args, 1, 'int' ) ) );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'remove' );
        $ids = array();
        foreach ( (array)$o->assignedNodes() as $n )
        {
            if ( !$n->canRemove() )
                throw new expServiceException( 'No remove access to node ' . $n->attribute( 'node_id' ), 403 );
            $ids[] = (int)$n->attribute( 'node_id' );
        }
        if ( !$ids )
            throw new expServiceException( 'The object has no nodes', 404 );
        $r = expNodeServices::removeNodes( array_map( function ( $id ) { return eZContentObjectTreeNode::fetch( $id ); }, $ids ), self::post( 'move_to_trash', 'bool', true ) );
        return $r;
    }

    public static function expireCache( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        eZContentCacheManager::clearContentCache( (int)$o->attribute( 'id' ) );
        return self::ok( array( 'cleared' => (int)$o->attribute( 'id' ) ) );
    }

    public static function cleanupDrafts( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $o->cleanupInternalDrafts( (int)eZUser::currentUser()->attribute( 'contentobject_id' ), 0 );
        return self::ok( array( 'cleaned' => (int)$o->attribute( 'id' ) ) );
    }
}
