<?php
/**
 * Section services: ezjscore/call/expsection::<method>[::arg...]
 *
 * Content sections: list and look at them, the objects in a section, create, change and remove sections, and
 * assign an object or a whole subtree to a section (subtrees route through content jobs when asked or large).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expSectionServices extends expContentServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'All sections', 'access' => array( 'section', 'view' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of sections' ),
        'count' => array( 'summary' => 'Number of sections', 'access' => array( 'section', 'view' ), 'write' => false, 'args' => array(), 'returns' => '{count}' ),
        'get' => array( 'summary' => 'A section by id', 'access' => array( 'section', 'view' ), 'write' => false, 'args' => array( 'section_id' => 'int' ), 'returns' => 'section' ),
        'getByIdentifier' => array( 'summary' => 'A section by identifier', 'access' => array( 'section', 'view' ), 'write' => false, 'args' => array( 'identifier' => 'string' ), 'returns' => 'section' ),
        'objects' => array( 'summary' => 'Objects (main nodes) in a section, paged', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'section_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'objectCount' => array( 'summary' => 'Number of objects in a section', 'access' => array( 'section', 'view' ), 'write' => false, 'args' => array( 'section_id' => 'int' ), 'returns' => '{count}' ),
        'usage' => array( 'summary' => 'Sections with their object counts', 'access' => array( 'section', 'view' ), 'write' => false, 'args' => array(), 'returns' => 'sections with counts' ),
        'canRemove' => array( 'summary' => 'Whether a section can be removed and what blocks it', 'access' => array( 'section', 'view' ), 'write' => false, 'args' => array( 'section_id' => 'int' ), 'returns' => '{can_remove, objects, limitations, roles}' ),
        'assignable' => array( 'summary' => 'Sections the current user may assign', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'sections' ),
        'navigationParts' => array( 'summary' => 'The navigation parts a section can use', 'access' => array( 'section', 'view' ), 'write' => false, 'args' => array(), 'returns' => 'parts' ),
        'ofObject' => array( 'summary' => 'The section of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'section' ),
        'ofNode' => array( 'summary' => 'The section of a node', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'section' ),
        'create' => array( 'summary' => 'Creates a section. POST: name, identifier, navigation_part', 'access' => array( 'section', 'edit' ), 'write' => true, 'args' => array(), 'returns' => 'section' ),
        'update' => array( 'summary' => 'Changes a section (same POST fields)', 'access' => array( 'section', 'edit' ), 'write' => true, 'args' => array( 'section_id' => 'int' ), 'returns' => 'section' ),
        'remove' => array( 'summary' => 'Removes an unused section', 'access' => array( 'section', 'edit' ), 'write' => true, 'args' => array( 'section_id' => 'int' ), 'returns' => '{removed}' ),
        'assign' => array( 'summary' => 'Assigns an object to a section', 'access' => array( 'section', 'assign' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'section_id' => 'int' ), 'returns' => 'object' ),
        'assignSubtree' => array( 'summary' => 'Assigns a node and its subtree to a section. POST: mode', 'access' => array( 'section', 'assign' ), 'write' => true, 'args' => array( 'node_id' => 'int', 'section_id' => 'int' ), 'returns' => '{changed} or job' ),
    );

    /** Forgets the kernel's per-process section cache, so a read after a write sees the write. */
    protected static function forget( $id )
    {
        unset( $GLOBALS['eZContentSectionObjectCache'][(int)$id] );
    }

    protected static function section( $id )
    {
        $s = eZSection::fetch( (int)$id );
        if ( !$s )
            throw new expServiceException( "Section $id does not exist", 404 );
        return $s;
    }

    public static function list( $args )
    {
        static::guard( __FUNCTION__ );
        $items = array();
        foreach ( (array)eZSection::fetchList() as $s )
            $items[] = self::exportSection( $s );
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function count( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => (int)eZSection::sectionCount() ) );
    }

    public static function get( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportSection( self::section( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function getByIdentifier( $args )
    {
        static::guard( __FUNCTION__ );
        $s = eZSection::fetchByIdentifier( self::arg( $args, 0, 'string' ) );
        if ( !$s )
            throw new expServiceException( 'No section with that identifier', 404 );
        return self::ok( self::exportSection( $s ) );
    }

    public static function objects( $args )
    {
        static::guard( __FUNCTION__ );
        $s = self::section( self::arg( $args, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $params = array( 'MainNodeOnly' => true, 'AsObject' => true, 'AttributeFilter' => array( 'and', array( 'section', '=', (int)$s->attribute( 'id' ) ) ),
                         'SortBy' => array( array( 'published', false ) ) );
        $count = eZContentObjectTreeNode::subTreeCountByNodeID( $params, 1 );
        $nodes = $count ? eZContentObjectTreeNode::subTreeByNodeID( $params + array( 'Limit' => $limit, 'Offset' => $offset ), 1 ) : array();
        return self::page( self::exportNodes( (array)$nodes ), $count, $offset, $limit );
    }

    public static function objectCount( $args )
    {
        static::guard( __FUNCTION__ );
        $s = self::section( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'count' => (int)eZContentObject::fetchListCount( array( 'section_id' => (int)$s->attribute( 'id' ) ) ) ) );
    }

    public static function usage( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZSection::fetchList() as $s )
        {
            $row = self::exportSection( $s );
            $row['objects'] = (int)eZContentObject::fetchListCount( array( 'section_id' => (int)$s->attribute( 'id' ) ) );
            $out[] = $row;
        }
        return self::ok( $out );
    }

    public static function canRemove( $args )
    {
        static::guard( __FUNCTION__ );
        $s = self::section( self::arg( $args, 0, 'int' ) );
        $id = (int)$s->attribute( 'id' );
        $objects = (int)eZContentObject::fetchListCount( array( 'section_id' => $id ) );
        $limitations = count( (array)eZPolicyLimitation::findByType( 'Section', $id, true, false ) );
        $roles = count( (array)eZRole::fetchRolesByLimitation( 'section', $id ) );
        return self::ok( array( 'can_remove' => $objects + $limitations + $roles === 0, 'objects' => $objects, 'limitations' => $limitations, 'roles' => $roles ) );
    }

    public static function assignable( $args )
    {
        static::guard( __FUNCTION__ );
        $user = eZUser::currentUser();
        $out = array();
        foreach ( (array)eZSection::fetchList() as $s )
            if ( $user->canAssignSection( (int)$s->attribute( 'id' ) ) )
                $out[] = self::exportSection( $s );
        return self::ok( $out );
    }

    public static function navigationParts( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZNavigationPart::fetchList() as $p )
            $out[] = array( 'identifier' => $p['identifier'], 'name' => $p['name'] );
        return self::ok( $out );
    }

    public static function ofObject( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        return self::ok( self::exportSection( self::section( $o->attribute( 'section_id' ) ) ) );
    }

    public static function ofNode( $args )
    {
        static::guard( __FUNCTION__ );
        $n = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( self::exportSection( self::section( $n->object()->attribute( 'section_id' ) ) ) );
    }

    protected static function applyFields( eZSection $s, $isNew )
    {
        $name = trim( self::post( 'name', 'string', '' ) );
        if ( $name === '' && $isNew )
            throw new expServiceException( 'The name is required', 400 );
        if ( $name !== '' )
            $s->setAttribute( 'name', $name );
        $identifier = trim( self::post( 'identifier', 'string', '' ) );
        if ( $identifier === '' && $isNew )
            throw new expServiceException( 'The identifier is required', 400 );
        if ( $identifier !== '' )
        {
            if ( preg_match( '/(^[^A-Za-z])|\W/', $identifier ) )
                throw new expServiceException( 'The identifier has letters, digits and _ and starts with a letter', 422 );
            $others = eZSection::fetchFilteredList( array( 'identifier' => $identifier, 'id' => array( '!=', $isNew ? 0 : (int)$s->attribute( 'id' ) ) ) );
            if ( count( (array)$others ) )
                throw new expServiceException( 'The identifier is used by another section', 409 );
            $s->setAttribute( 'identifier', $identifier );
        }
        $part = self::post( 'navigation_part', 'string', '' );
        if ( $part === '' && $isNew )
            $part = 'ezcontentnavigationpart';
        if ( $part !== '' )
        {
            $known = array();
            foreach ( (array)eZNavigationPart::fetchList() as $p )
                $known[] = $p['identifier'];
            if ( !in_array( $part, $known, true ) )
                throw new expServiceException( 'Unknown navigation part, see expsection::navigationParts', 422 );
            $s->setAttribute( 'navigation_part_identifier', $part );
        }
    }

    public static function create( $args )
    {
        static::guard( __FUNCTION__ );
        $s = new eZSection( array() );
        self::applyFields( $s, true );
        $s->store();
        self::forget( $s->attribute( 'id' ) );
        ezpEvent::getInstance()->notify( 'content/section/cache', array( $s->attribute( 'id' ) ) );
        return self::ok( self::exportSection( self::section( $s->attribute( 'id' ) ) ) );
    }

    public static function update( $args )
    {
        static::guard( __FUNCTION__ );
        $s = self::section( self::arg( $args, 0, 'int' ) );
        self::applyFields( $s, false );
        $s->store();
        self::forget( $s->attribute( 'id' ) );
        eZContentCacheManager::clearContentCacheIfNeededBySectionID( $s->attribute( 'id' ) );
        ezpEvent::getInstance()->notify( 'content/section/cache', array( $s->attribute( 'id' ) ) );
        return self::ok( self::exportSection( self::section( $s->attribute( 'id' ) ) ) );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        $s = self::section( self::arg( $args, 0, 'int' ) );
        if ( !$s->canBeRemoved() )
            throw new expServiceException( 'The section is in use (objects, policy limitations or roles)', 409 );
        $id = (int)$s->attribute( 'id' );
        $s->removeThis();
        self::forget( $id );
        eZContentCacheManager::clearAllContentCache();
        return self::ok( array( 'removed' => $id ) );
    }

    public static function assign( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $s = self::section( self::arg( $args, 1, 'int' ) );
        if ( !eZUser::currentUser()->canAssignSectionToObject( (int)$s->attribute( 'id' ), $o ) )
            throw new expServiceException( 'You cannot assign this section to this object', 403 );
        if ( (int)$o->attribute( 'section_id' ) === (int)$s->attribute( 'id' ) )
            throw new expServiceException( 'The object is in that section already', 409 );
        $s->applyTo( $o );
        eZContentObject::clearCache();
        $fresh = eZContentObject::fetch( $o->attribute( 'id' ) );
        if ( (int)$fresh->attribute( 'section_id' ) !== (int)$s->attribute( 'id' ) )
            throw new expServiceException( 'The section could not be assigned', 422 );
        return self::ok( self::exportObject( $fresh ) );
    }

    public static function assignSubtree( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $s = self::section( self::arg( $args, 1, 'int' ) );
        $user = eZUser::currentUser();
        if ( !$user->canAssignSection( (int)$s->attribute( 'id' ) ) || !$user->canAssignSectionToObject( (int)$s->attribute( 'id' ), $node->object() ) )
            throw new expServiceException( 'You cannot assign this section here', 403 );
        $nid = (int)$node->attribute( 'node_id' );
        $sid = (int)$s->attribute( 'id' );
        return self::runOrJob( 'section', array( 'node_id' => $nid, 'section_id' => $sid ), function () use ( $nid, $sid ) {
            self::notLocked( array( $nid ) );
            self::operation( 'updatesection', array( 'node_id' => $nid, 'selected_section_id' => $sid ),
                             function () use ( $nid, $sid ) { return eZContentOperationCollection::updateSection( $nid, $sid, false ); } );
            return array( 'changed' => $nid, 'section_id' => $sid );
        } );
    }
}
