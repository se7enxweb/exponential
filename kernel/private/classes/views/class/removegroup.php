<?php
/**
 * The code of kernel/class/removegroup.php, moved into a class (#207 stage 1). The file kernel/class/removegroup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/class/removegroup.php:
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

class Removegroup extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $deleteIDArray = $http->hasSessionVariable( 'DeleteGroupIDArray' ) ? $http->sessionVariable( 'DeleteGroupIDArray' ) : array();
        $groupsInfo = array();
        $deleteResult = array();
        $deleteClassIDList = array();
        foreach ( $deleteIDArray as $deleteID )
        {
            $deletedClassName = '';
            $group = \eZContentClassGroup::fetch( $deleteID );
            if ( $group != null )
            {
                $GroupName = $group->attribute( 'name' );
                $classList = \eZContentClassClassGroup::fetchClassList( null, $deleteID );
                $groupClassesInfo = array();
                foreach ( $classList as $class )
                {
                    $classID = $class->attribute( "id" );
                    $classGroups = \eZContentClassClassGroup::fetchGroupList( $classID, 0);
                    if ( count( $classGroups ) == 1 )
                    {
                        $classObject = \eZContentclass::fetch( $classID );
                        $className = $classObject->attribute( "name" );
                        $deletedClassName .= " '" . $className . "'" ;
                        $deleteClassIDList[] = $classID;
                        $groupClassesInfo[] = array( 'class_name'   => $className,
                                                     'object_count' => $classObject->objectCount() );
                    }
                }
                if ( $deletedClassName == '' )
                    $deletedClassName = \ezpI18n::tr( 'kernel/class', '(no classes)' );
                $deleteResult[] = array( 'groupName'        => $GroupName,
                                         'deletedClassName' => $deletedClassName );
                $groupsInfo[] = array( 'group_name' => $GroupName,
                                       'class_list' => $groupClassesInfo );
            }
        }
        if ( $http->hasPostVariable( "ConfirmButton" ) )
        {
            foreach ( $deleteIDArray as $deleteID )
            {
                \eZContentClassGroup::removeSelected( $deleteID );
                \eZContentClassClassGroup::removeGroupMembers( $deleteID );
                foreach ( $deleteClassIDList as $deleteClassID )
                {
                    $deleteClass = \eZContentClass::fetch( $deleteClassID );
                    if ( $deleteClass )
                        $deleteClass->remove( true );
                    $deleteClass = \eZContentClass::fetch( $deleteClassID, true, \eZContentClass::VERSION_STATUS_TEMPORARY );
                    if ( $deleteClass )
                        $deleteClass->remove( true );
                    \ezpEvent::getInstance()->notify( 'content/class/cache', array( $deleteClassID ) );
                }
                \ezpEvent::getInstance()->notify( 'content/class/group/cache', array( $deleteID ) );
            }

            $Module->redirectTo( '/class/grouplist/' );
        }
        if ( $http->hasPostVariable( "CancelButton" ) )
        {
            $Module->redirectTo( '/class/grouplist/' );
        }
        $Module->setTitle( \ezpI18n::tr( 'kernel/class', 'Remove class groups' ) . ' ' . $GroupName );
        $tpl = \eZTemplate::factory();

        $tpl->setVariable( "DeleteResult", $deleteResult );
        $tpl->setVariable( "module", $Module );
        $tpl->setVariable( "groups_info", $groupsInfo );
        $Result = array();
        $Result['content'] = $tpl->fetch( "design:class/removegroup.tpl" );
        $Result['path'] = array( array( 'url' => '/class/grouplist/',
                                        'text' => \ezpI18n::tr( 'kernel/class', 'Class groups' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/class', 'Remove class groups' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
