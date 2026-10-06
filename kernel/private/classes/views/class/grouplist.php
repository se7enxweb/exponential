<?php
/**
 * The code of kernel/class/grouplist.php, moved into a class (#207 stage 1). The file kernel/class/grouplist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/class/grouplist.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Class
{

class Grouplist extends \Exponential\Runnable\ModuleView
{
    /** How many classes a group card names; the rest are counted, with a link to the group. */
    const CLASSES_SHOWN = 12;

    /**
     * Every link of a defined class to a group. One query.
     *
     * @return array of hash( class_id, group_id )
     */
    public static function links()
    {
        $rows = \eZDB::instance()->arrayQuery( 'SELECT contentclass_id, group_id FROM ezcontentclass_classgroup WHERE contentclass_version = ' . (int)\eZContentClass::VERSION_STATUS_DEFINED );
        $links = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $links[] = array( 'class_id' => (int)$row['contentclass_id'], 'group_id' => (int)$row['group_id'] );
        }
        return $links;
    }

    /**
     * Every defined class with its name, identifier, last change and published objects. Two queries.
     *
     * @return array class id => hash( id, name, identifier, modified, objects )
     */
    public static function classFacts()
    {
        $counts = array();
        $rows = \eZDB::instance()->arrayQuery( 'SELECT contentclass_id, COUNT(*) AS object_count FROM ezcontentobject WHERE status = '
                                              . (int)\eZContentObject::STATUS_PUBLISHED . ' GROUP BY contentclass_id' );
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $counts[(int)$row['contentclass_id']] = (int)$row['object_count'];
        }
        $facts = array();
        foreach ( (array)\eZContentClass::fetchList( \eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
        {
            $id = (int)$class->attribute( 'id' );
            $facts[$id] = array( 'id' => $id, 'name' => (string)$class->attribute( 'name' ),
                                 'identifier' => (string)$class->attribute( 'identifier' ),
                                 'modified' => (int)$class->attribute( 'modified' ),
                                 'objects' => isset( $counts[$id] ) ? $counts[$id] : 0 );
        }
        return $facts;
    }

    /**
     * @param array $groups eZContentClassGroup objects
     * @return array group id => name
     */
    public static function groupNames( array $groups )
    {
        $names = array();
        foreach ( $groups as $group )
            $names[(int)$group->attribute( 'id' )] = (string)$group->attribute( 'name' );
        return $names;
    }

    /**
     * One card per group: its classes (by name, the first CLASSES_SHOWN), their published objects, when the group
     * or one of its classes last changed, and what removing it would remove. class/removegroup removes a class
     * that is in no other group, with all its objects; one that is also in another group only leaves this one.
     *
     * @param array $groups eZContentClassGroup objects (this page)
     * @param array $links links()
     * @param array $classes classFacts()
     * @param array $groupNames group id => name, of every group
     * @return array group id => hash
     */
    public static function overview( array $groups, array $links, array $classes, array $groupNames )
    {
        $classesOf = array();
        $groupsOf = array();
        foreach ( $links as $link )
        {
            if ( !isset( $classes[$link['class_id']] ) )
                continue;
            $classesOf[$link['group_id']][$link['class_id']] = true;
            $groupsOf[$link['class_id']][$link['group_id']] = true;
        }
        $overview = array();
        foreach ( $groups as $group )
        {
            $groupID = (int)$group->attribute( 'id' );
            $list = array();
            $info = array( 'class_count' => 0, 'objects' => 0, 'removes' => 0, 'removes_objects' => 0, 'shared' => 0,
                           'last_modified' => (int)$group->attribute( 'modified' ), 'last_class' => false );
            $search = array( mb_strtolower( (string)$group->attribute( 'name' ) ), (string)$groupID );
            foreach ( array_keys( isset( $classesOf[$groupID] ) ? $classesOf[$groupID] : array() ) as $classID )
            {
                $fact = $classes[$classID];
                $only = count( $groupsOf[$classID] ) === 1;
                $list[] = $fact + array( 'only_here' => $only );
                $info['class_count']++;
                $info['objects'] += $fact['objects'];
                if ( $only )
                {
                    $info['removes']++;
                    $info['removes_objects'] += $fact['objects'];
                }
                else
                {
                    $info['shared']++;
                }
                if ( $fact['modified'] >= $info['last_modified'] )
                {
                    $info['last_modified'] = $fact['modified'];
                    $info['last_class'] = $fact;
                }
                $search[] = mb_strtolower( $fact['name'] . ' ' . $fact['identifier'] );
            }
            usort( $list, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
            $info['classes'] = array_slice( $list, 0, self::CLASSES_SHOWN );
            $info['more'] = max( 0, count( $list ) - self::CLASSES_SHOWN );
            $info['search'] = implode( ' ', $search );
            $overview[$groupID] = $info;
        }
        return $overview;
    }

    /**
     * @param array $groups every group
     * @param array $links links()
     * @param array $classes classFacts()
     * @return array groups, classes, objects, shared (classes in more than one group), ungrouped (in none)
     */
    public static function summary( array $groups, array $links, array $classes )
    {
        $groupsOf = array();
        foreach ( $links as $link )
            if ( isset( $classes[$link['class_id']] ) )
                $groupsOf[$link['class_id']][$link['group_id']] = true;
        $summary = array( 'groups' => count( $groups ), 'classes' => count( $classes ), 'objects' => 0, 'shared' => 0, 'ungrouped' => 0 );
        foreach ( $classes as $id => $fact )
        {
            $summary['objects'] += $fact['objects'];
            if ( !isset( $groupsOf[$id] ) )
                $summary['ungrouped']++;
            else if ( count( $groupsOf[$id] ) > 1 )
                $summary['shared']++;
        }
        return $summary;
    }

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];

        $http = \eZHTTPTool::instance();
        if ( $http->hasPostVariable( "RemoveGroupButton" ) )
        {
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
                if ( $deleteIDArray !== null )
                {
                    $http->setSessionVariable( 'DeleteGroupIDArray', $deleteIDArray );
                    $Module->redirectTo( $Module->functionURI( 'removegroup' ) . '/' );
                }
            }
        }

        if ( $http->hasPostVariable( "EditGroupButton" ) && $http->hasPostVariable( "EditGroupID" ) )
        {
            $Module->redirectTo( $Module->functionURI( "groupedit" ) . "/" . $http->postVariable( "EditGroupID" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "NewGroupButton" ) )
        {
            $params = array();
            $Module->run( "groupedit", $params );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "NewClassButton" ) )
        {
            if ( $http->hasPostVariable( "SelectedGroupID" ) )
            {
                $groupID = $http->postVariable( "SelectedGroupID" );
                $group = \eZContentClassGroup::fetch( $groupID );
                $groupName = $group->attribute( 'name' );

                $params = array( null, $groupID, $groupName );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->run( "edit", $params ) );
            }
        }

        if ( !isset( $TemplateData ) or !is_array( $TemplateData ) )
        {
            $TemplateData = array( array( "name" => "groups",
                                          "http_base" => "ContentClass",
                                          "data" => array( "command" => "group_list",
                                                           "type" => "class" ) ) );
        }

        $Module->setTitle( \ezpI18n::tr( 'kernel/class', 'Class group list' ) );
        $tpl = \eZTemplate::factory();

        $user = \eZUser::currentUser();
        foreach( $TemplateData as $tpldata )
        {
            $tplname = $tpldata["name"];
            $data = $tpldata["data"];
            $asObject = isset( $data["as_object"] ) ? $data["as_object"] : true;
            $base = $tpldata["http_base"];
            unset( $list );
            $list = \eZContentClassGroup::fetchList( false, $asObject );

            $groupCount  = count( $list );
            $groupLimit  = \expAdminPagination::limit( 'class/grouplist' );
            $groupOffset = \expAdminPagination::offset( $Params );

            $tpl->setVariable( $tplname, \expAdminPagination::page( $list, $groupOffset, $groupLimit ) );
            $tpl->setVariable( "group_count", $groupCount );
            $tpl->setVariable( "limit", $groupLimit );
            $tpl->setVariable( "view_parameters", array( 'offset' => $groupOffset ) );
        }

        $tpl->setVariable( "module", $Module );

        // The redesigned page: per group its classes, their objects, the last change and what removing the group
        // would remove (class/removegroup removes the classes that are in no other group, with their objects).
        $links = self::links();
        $classes = self::classFacts();
        $tpl->setVariable( 'group_overview', self::overview( isset( $groupLimit ) ? \expAdminPagination::page( $list, $groupOffset, $groupLimit ) : array(),
                                                             $links, $classes, self::groupNames( $list ) ) );
        $tpl->setVariable( 'group_summary', self::summary( $list, $links, $classes ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:class/grouplist.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/class', 'Class groups' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
