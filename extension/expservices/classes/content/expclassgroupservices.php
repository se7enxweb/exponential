<?php
/**
 * Class group services: ezjscore/call/expclassgroup::<method>[::arg...]
 *
 * The groups content classes are filed in: list, members, and create, rename, remove (empty groups only), add or
 * remove a class from a group.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expClassGroupServices extends expContentServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'All class groups with their class counts', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of groups' ),
        'count' => array( 'summary' => 'Number of class groups', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array(), 'returns' => '{count}' ),
        'get' => array( 'summary' => 'A class group', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'group_id' => 'int' ), 'returns' => 'group' ),
        'getByName' => array( 'summary' => 'A class group by name', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'name' => 'string' ), 'returns' => 'group' ),
        'classes' => array( 'summary' => 'The classes in a group', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'group_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of classes' ),
        'classCount' => array( 'summary' => 'Number of classes in a group', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'group_id' => 'int' ), 'returns' => '{count}' ),
        'ofClass' => array( 'summary' => 'The groups a class is in', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'groups' ),
        'isEmpty' => array( 'summary' => 'Whether a group has no classes (and can be removed)', 'access' => array( 'class', '*' ), 'write' => false, 'args' => array( 'group_id' => 'int' ), 'returns' => '{empty}' ),
        'create' => array( 'summary' => 'Creates a class group', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'name' => 'string' ), 'returns' => 'group' ),
        'rename' => array( 'summary' => 'Renames a class group', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'group_id' => 'int', 'name' => 'string' ), 'returns' => 'group' ),
        'remove' => array( 'summary' => 'Removes an empty class group', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'group_id' => 'int' ), 'returns' => '{removed}' ),
        'addClass' => array( 'summary' => 'Files a class in a group', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'group_id' => 'int', 'class' => 'string' ), 'returns' => 'groups of the class' ),
        'removeClass' => array( 'summary' => 'Takes a class out of a group (a class keeps at least one group)', 'access' => array( 'class', '*' ), 'write' => true, 'args' => array( 'group_id' => 'int', 'class' => 'string' ), 'returns' => 'groups of the class' ),
    );

    protected static function group( $id )
    {
        $g = eZContentClassGroup::fetch( (int)$id );
        if ( !$g )
            throw new expServiceException( "Class group $id does not exist", 404 );
        return $g;
    }

    protected static function exportGroupWithCount( eZContentClassGroup $g )
    {
        $row = self::exportClassGroup( $g );
        $row['class_count'] = count( (array)eZContentClassClassGroup::fetchClassList( eZContentClass::VERSION_STATUS_DEFINED, $g->attribute( 'id' ) ) );
        return $row;
    }

    public static function list( $args )
    {
        static::guard( __FUNCTION__ );
        $items = array();
        foreach ( (array)eZContentClassGroup::fetchList( false, true ) as $g )
            $items[] = self::exportGroupWithCount( $g );
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function count( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => count( (array)eZContentClassGroup::fetchList( false, false ) ) ) );
    }

    public static function get( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportGroupWithCount( self::group( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function getByName( $args )
    {
        static::guard( __FUNCTION__ );
        $g = eZContentClassGroup::fetchByName( self::arg( $args, 0, 'string' ) );
        if ( !$g )
            throw new expServiceException( 'No class group with that name', 404 );
        return self::ok( self::exportGroupWithCount( $g ) );
    }

    public static function classes( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::group( self::arg( $args, 0, 'int' ) );
        $items = array();
        foreach ( (array)eZContentClassClassGroup::fetchClassList( eZContentClass::VERSION_STATUS_DEFINED, $g->attribute( 'id' ) ) as $c )
            $items[] = self::exportClass( $c );
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function classCount( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::group( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'count' => count( (array)eZContentClassClassGroup::fetchClassList( eZContentClass::VERSION_STATUS_DEFINED, $g->attribute( 'id' ) ) ) ) );
    }

    public static function ofClass( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::groupsOf( self::contentClass( self::arg( $args, 0, 'string' ) ) ) );
    }

    protected static function groupsOf( eZContentClass $c )
    {
        $out = array();
        foreach ( (array)eZContentClassClassGroup::fetchGroupList( $c->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED ) as $l )
            $out[] = array( 'id' => (int)$l->attribute( 'group_id' ), 'name' => $l->attribute( 'group_name' ) );
        return $out;
    }

    public static function isEmpty( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::group( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'empty' => !count( (array)eZContentClassClassGroup::fetchClassList( null, $g->attribute( 'id' ) ) ) ) );
    }

    public static function create( $args )
    {
        static::guard( __FUNCTION__ );
        $name = trim( self::arg( $args, 0, 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The name cannot be empty', 422 );
        if ( eZContentClassGroup::fetchByName( $name ) )
            throw new expServiceException( 'A class group with that name exists', 409 );
        $g = eZContentClassGroup::create( (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        $g->setAttribute( 'name', $name );
        $g->store();
        return self::ok( self::exportGroupWithCount( $g ) );
    }

    public static function rename( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::group( self::arg( $args, 0, 'int' ) );
        $name = trim( self::arg( $args, 1, 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The name cannot be empty', 422 );
        $other = eZContentClassGroup::fetchByName( $name );
        if ( $other && (int)$other->attribute( 'id' ) !== (int)$g->attribute( 'id' ) )
            throw new expServiceException( 'A class group with that name exists', 409 );
        $g->setAttribute( 'name', $name );
        $g->setAttribute( 'modified', time() );
        $g->setAttribute( 'modifier_id', (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        $g->store();
        // the class member rows carry the group name too
        eZDB::instance()->query( 'UPDATE ezcontentclass_classgroup SET group_name=\'' . eZDB::instance()->escapeString( $name ) . '\' WHERE group_id=' . (int)$g->attribute( 'id' ) );
        eZContentClassClassGroup::clearGroupListCache();
        return self::ok( self::exportGroupWithCount( self::group( $g->attribute( 'id' ) ) ) );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::group( self::arg( $args, 0, 'int' ) );
        if ( count( (array)eZContentClassClassGroup::fetchClassList( null, $g->attribute( 'id' ) ) ) )
            throw new expServiceException( 'The group still has classes; move them to another group first', 409 );
        $id = (int)$g->attribute( 'id' );
        eZContentClassGroup::removeSelected( $id );
        eZContentClassClassGroup::removeGroupMembers( $id );
        return self::ok( array( 'removed' => $id ) );
    }

    public static function addClass( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::group( self::arg( $args, 0, 'int' ) );
        $c = self::contentClass( self::arg( $args, 1, 'string' ) );
        if ( eZContentClassClassGroup::classInGroup( $c->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED, $g->attribute( 'id' ) ) )
            throw new expServiceException( 'The class is in that group already', 409 );
        eZContentClassClassGroup::create( $c->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED, $g->attribute( 'id' ), $g->attribute( 'name' ) )->store();
        return self::ok( self::groupsOf( $c ) );
    }

    public static function removeClass( $args )
    {
        static::guard( __FUNCTION__ );
        $g = self::group( self::arg( $args, 0, 'int' ) );
        $c = self::contentClass( self::arg( $args, 1, 'string' ) );
        if ( !eZContentClassClassGroup::classInGroup( $c->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED, $g->attribute( 'id' ) ) )
            throw new expServiceException( 'The class is not in that group', 404 );
        if ( count( self::groupsOf( $c ) ) < 2 )
            throw new expServiceException( 'A class must stay in at least one group', 409 );
        eZContentClassClassGroup::removeGroup( $c->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED, $g->attribute( 'id' ) );
        return self::ok( self::groupsOf( $c ) );
    }
}
