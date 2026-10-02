<?php
/**
 * The code of kernel/class/classlist.php, moved into a class (#207 stage 1). The file kernel/class/classlist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/class/classlist.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Class
{

class Classlist extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $GroupID = false;
        if ( isset( $Params["GroupID"] ) )
            $GroupID = $Params["GroupID"];

        $http = \eZHTTPTool::instance();
        $http->setSessionVariable( 'FromGroupID', $GroupID );
        if ( $http->hasPostVariable( "RemoveButton" ) )
        {
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
                if ( $deleteIDArray !== null )
                {
                    $http->setSessionVariable( 'DeleteClassIDArray', $deleteIDArray );
                    $Module->redirectTo( $Module->functionURI( 'removeclass' ) . '/'  . $GroupID . '/' );
                }
            }
        }

        if ( $http->hasPostVariable( "NewButton" ) )
        {
            if ( $http->hasPostVariable( "CurrentGroupID" ) )
                $GroupID = $http->postVariable( "CurrentGroupID" );
            if ( $http->hasPostVariable( "CurrentGroupName" ) )
                $GroupName = $http->postVariable( "CurrentGroupName" );
            if ( $http->hasPostVariable( "ClassLanguageCode" ) )
                $LanguageCode = $http->postVariable( "ClassLanguageCode" );
            $params = array( null, $GroupID, $GroupName, $LanguageCode );
            $unorderedParams = array( 'Language' => $LanguageCode );
            $Module->run( 'edit', $params, $unorderedParams );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( !isset( $TemplateData ) or !is_array( $TemplateData ) )
        {
            $TemplateData = array( array( "name" => "groupclasses",
                                          "http_base" => "ContentClass",
                                          "data" => array( "command" => "groupclass_list",
                                                           "type" => "class" ) ) );
        }

        $Module->setTitle( \ezpI18n::tr( 'kernel/class', 'Class list of group' ) . ' ' . $GroupID );
        $tpl = \eZTemplate::factory();

        $user = \eZUser::currentUser();
        foreach( $TemplateData as $tpldata )
        {
            $tplname = $tpldata["name"];

            $groupInfo =  \eZContentClassGroup::fetch( $GroupID );

            if( !$groupInfo )
            {
               return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
            }

            $list = \eZContentClassClassGroup::fetchClassList( 0, $GroupID, $asObject = true );
            $groupModifier = \eZContentObject::fetch( $groupInfo->attribute( 'modifier_id') );

            // Paged. A group holding every class of a large installation drew every one
            // of them, each with its language list, on one screen.
            $classCount  = count( $list );
            $classLimit  = \expAdminPagination::limit( 'class/classlist' );
            $classOffset = \expAdminPagination::offset( $Params );

            $tpl->setVariable( $tplname, \expAdminPagination::page( $list, $classOffset, $classLimit ) );
            $tpl->setVariable( "class_count", $classCount );
            $tpl->setVariable( "limit", $classLimit );
            $tpl->setVariable( "view_parameters", array( 'offset' => $classOffset ) );
            $tpl->setVariable( "GroupID", $GroupID );
            $tpl->setVariable( "group", $groupInfo );
            $tpl->setVariable( "group_modifier", $groupModifier );
        }

        $group = \eZContentClassGroup::fetch( $GroupID );
        $groupName = $group->attribute( 'name' );


        $tpl->setVariable( "module", $Module );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:class/classlist.tpl" );
        $Result['path'] = array( array( 'url' => '/class/grouplist/',
                                        'text' => \ezpI18n::tr( 'kernel/class', 'Class groups' ) ),
                                 array( 'url' => false,
                                        'text' => $groupName ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
